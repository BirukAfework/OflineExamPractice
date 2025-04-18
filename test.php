<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';
require_once 'fetch_data.php';

$student = getStudent('921231410');
if (!$student) {
    die("Error: Student not found for admission number '921231410'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade 12 Online National Exam</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f5f7fa; color: #2d3748; line-height: 1.6; }
        .container { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background-color: #ffffff; border-right: 1px solid #e2e8f0; padding: 24px; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar .logo { font-size: 20px; font-weight: 700; color: #1a73e8; margin-bottom: 32px; display: flex; align-items: center; gap: 8px; }
        .sidebar .dropdowns { margin-bottom: 16px; }
        .sidebar .dropdowns select { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 15px; font-weight: 500; color: #2d3748; background: #ffffff; cursor: pointer; margin-bottom: 12px; transition: border-color 0.2s ease, background-color 0.2s ease; }
        .sidebar .dropdowns select:focus { outline: none; border-color: #1a73e8; background: #f1f5f9; }
        .main-content { flex-grow: 1; margin-left: 240px; padding: 24px; background: #f5f7fa; }
        .header { background: #ffffff; padding: 16px 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .header .basic-info { display: flex; justify-content: space-between; flex: 1; }
        .header .basic-info div { flex: 1; }
        .header .basic-info div p { margin: 4px 0; font-size: 14px; color: #4a5568; }
        .header .basic-info div p strong { color: #2d3748; font-weight: 600; }
        .timer { background: #1a73e8; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; transition: background-color 0.2s ease; }
        .timer.warning { background: #e53e3e; }
        .exam-section { display: flex; gap: 24px; }
        .question-area { flex: 1; background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .question-area h2 { font-size: 18px; font-weight: 600; color: #2d3748; margin-bottom: 16px; }
        .question { border: 1px solid #e2e8f0; padding: 16px; border-radius: 6px; background: #fafafa; }
        .question p { margin: 4px 0; font-size: 14px; color: #4a5568; }
        .question .question-text { font-size: 16px; font-weight: 500; color: #2d3748; margin: 8px 0; }
        .question .options { margin: 12px 0; }
        .question .options .option-btn { display: block; width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; font-size: 14px; color: #2d3748; text-align: left; cursor: pointer; transition: background-color 0.2s ease, border-color 0.2s ease; }
        .question .options .option-btn:hover { background: #f1f5f9; border-color: #1a73e8; }
        .question .options .option-btn.selected { background: #e3f2fd; border-color: #1a73e8; color: #1a73e8; font-weight: 500; }
        .question .skip-btn { padding: 8px 16px; border: 1px solid #e53e3e; border-radius: 6px; background: #ffffff; font-size: 14px; color: #e53e3e; cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease; margin-right: 8px; }
        .question .skip-btn:hover { background: #fef2f2; color: #c53030; }
        .question .skip-btn.skipped { background: #e53e3e; color: #ffffff; border-color: #e53e3e; }
        .question .show-answer-btn { padding: 8px 16px; border: 1px solid #2d3748; border-radius: 6px; background: #ffffff; font-size: 14px; color: #2d3748; cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease; }
        .question .show-answer-btn:hover { background: #f1f5f9; color: #1a73e8; }
        .question .answer-explanation { margin-top: 16px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; display: none; }
        .question .answer-explanation.show { display: block; }
        .navigation { display: flex; justify-content: space-between; margin-top: 24px; }
        .navigation button { padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s ease, transform 0.1s ease; }
        .navigation .prev, .navigation .next, .navigation .show-results { background: #edf2f7; color: #4a5568; }
        .navigation .prev:hover, .navigation .next:hover, .navigation .show-results:hover { background: #e2e8f0; transform: translateY(-1px); }
        .exam-overview { width: 220px; background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .exam-overview h3 { font-size: 16px; font-weight: 600; color: #2d3748; margin-bottom: 16px; }
        .exam-overview .grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .exam-overview .grid div { padding: 8px; background: #edf2f7; border-radius: 4px; text-align: center; font-size: 14px; color: #4a5568; cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease; }
        .exam-overview .grid div:hover { background: #e3f2fd; color: #1a73e8; }
        .exam-overview .grid div.current { background: #1a73e8; color: #ffffff; }
        .exam-overview .grid div.skipped { background: #e53e3e; color: #ffffff; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal#results-modal { background: rgba(0, 0, 0, 0.7); animation: fadeIn 0.3s ease-in-out; }
        .modal-content { background: #ffffff; padding: 32px; border-radius: 8px; width: 90%; max-width: 600px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); max-height: 80vh; overflow-y: auto; }
        .modal-content.results-modal-content { background: linear-gradient(135deg, #ffffff, #e3f2fd); padding: 40px; border-radius: 12px; max-width: 700px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); transform: scale(0.9); animation: popUp 0.4s ease-out forwards; }
        .modal-content h2 { font-size: 24px; font-weight: 600; color: #2d3748; margin-bottom: 16px; }
        .modal-content.results-modal-content h2 { font-size: 28px; color: #1a73e8; text-align: center; text-transform: uppercase; letter-spacing: 1px; }
        .modal-content ul { list-style-type: disc; padding-left: 20px; margin-bottom: 24px; }
        .modal-content ul li { font-size: 14px; color: #4a5568; margin: 8px 0; }
        .modal-content .start-btn { background: #1a73e8; color: #ffffff; padding: 12px 24px; border: none; border-radius: 6px; font-size: 16px; font-weight: 500; cursor: pointer; width: 100%; transition: background-color 0.2s ease; margin-top: 10px; }
        .modal-content .start-btn:hover { background: #1557b0; }
        .modal-content .close-btn { background: #e53e3e; color: #ffffff; padding: 12px 24px; border: none; border-radius: 6px; font-size: 16px; font-weight: 500; cursor: pointer; width: 100%; transition: background-color 0.2s ease, transform 0.1s ease; margin-top: 20px; }
        .modal-content .close-btn:hover { background: #c53030; transform: translateY(-2px); }
        .results-content p { font-size: 16px; margin: 10px 0; padding: 8px; border-radius: 4px; }
        .results-content .correct { color: #2ecc71; background: rgba(46, 204, 113, 0.1); }
        .results-content .incorrect { color: #e74c3c; background: rgba(231, 76, 60, 0.1); }
        .results-content .score { font-size: 24px; font-weight: 600; color: #2d3748; text-align: center; margin-top: 20px; padding: 10px; background: #f1f5f9; border-radius: 8px; }
        .toggle-container { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .toggle-label { font-size: 15px; font-weight: 500; color: #2d3748; }
        .toggle-switch { position: relative; display: inline-block; width: 60px; height: 34px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #1a73e8; }
        input:checked + .slider:before { transform: translateX(26px); }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes popUp { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        @media (max-width: 1024px) { .sidebar { width: 200px; } .main-content { margin-left: 200px; } .exam-overview { width: 180px; } }
        @media (max-width: 768px) { .container { flex-direction: column; } .sidebar { width: 100%; height: auto; position: relative; padding: 16px; } .main-content { margin-left: 0; padding: 16px; } .exam-section { flex-direction: column; } .exam-overview { width: 100%; margin-top: 16px; } .header .basic-info { flex-direction: column; gap: 12px; } .timer { position: static; margin-top: 12px; } }
        @media (max-width: 480px) { .exam-overview .grid { grid-template-columns: repeat(5, 1fr); } .navigation { flex-direction: column; gap: 12px; } .navigation button { width: 100%; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L3 9V21H9V15H15V21H21V9L12 2Z" fill="#1a73e8"/>
                </svg>
                National Exam
            </div>
            <div class="dropdowns">
                <div class="toggle-container">
                    <span class="toggle-label">Practice</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="mode-toggle">
                        <span class="slider"></span>
                    </label>
                    <span class="toggle-label">Exam</span>
                </div>
                <select id="subject-select">
                    <?php foreach (getSubjects() as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="year-select"></select>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="basic-info">
                    <div>
                        <p><strong>FULL NAME:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                        <p><strong>Is Blind / Is Deaf:</strong> <?php echo $student['is_blind'] ? 'Yes' : 'No'; ?> / <?php echo $student['is_deaf'] ? 'Yes' : 'No'; ?></p>
                        <p><strong>EXAM CENTER:</strong> <?php echo htmlspecialchars($student['exam_center']); ?></p>
                    </div>
                    <div>
                        <p><strong>School:</strong> <?php echo htmlspecialchars($student['school']); ?></p>
                        <p><strong>ADMISSION NUMBER:</strong> <?php echo htmlspecialchars($student['admission_number']); ?></p>
                        <p><strong>ENROLLMENT TYPE:</strong> <?php echo htmlspecialchars($student['enrollment_type']); ?></p>
                    </div>
                </div>
                <div class="timer" id="timer">Time left 0:00</div>
            </div>

            <div class="exam-section">
                <div class="question-area">
                    <h2 id="exam-title"></h2>
                    <div class="question" id="question-container"></div>
                    <div class="navigation">
                        <button class="prev" id="prev-btn">Prev</button>
                        <button class="next" id="next-btn">Next</button>
                    </div>
                </div>

                <div class="exam-overview">
                    <h3>EXAM OVERVIEW</h3>
                    <div class="grid" id="exam-grid"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="exam-modal">
        <div class="modal-content">
            <h2>Exam Mode Rules</h2>
            <ul>
                <li>No external resources or assistance allowed</li>
                <li>Time limit: Set by exam duration</li>
                <li>Questions cannot be revisited once submitted</li>
                <li>Internet connection must remain stable</li>
                <li>Answers will be auto-submitted when time expires</li>
                <li>No copying or sharing of questions permitted</li>
            </ul>
            <button class="start-btn" id="start-exam-btn">Start Exam</button>
        </div>
    </div>

    <div class="modal" id="results-modal">
        <div class="modal-content results-modal-content">
            <h2>Your Exam Results</h2>
            <div class="results-content" id="results-content"></div>
            <button class="close-btn" id="close-results-btn">Close</button>
        </div>
    </div>

    <script>
        let currentQuestionIndex = 0;
        let questions = [];
        const skippedQuestions = new Set();
        const selectedAnswers = [];
        let timeLeft;
        let timerInterval;

        const questionContainer = document.getElementById('question-container');
        const prevButton = document.getElementById('prev-btn');
        const nextButton = document.getElementById('next-btn');
        const gridItemsContainer = document.getElementById('exam-grid');
        const modeToggle = document.getElementById('mode-toggle');
        const timer = document.getElementById('timer');
        const subjectSelect = document.getElementById('subject-select');
        const yearSelect = document.getElementById('year-select');
        const examTitle = document.getElementById('exam-title');
        const examModal = document.getElementById('exam-modal');
        const startExamBtn = document.getElementById('start-exam-btn');
        const resultsModal = document.getElementById('results-modal');
        const resultsContent = document.getElementById('results-content');
        const closeResultsBtn = document.getElementById('close-results-btn');

        function startTimer(durationMinutes) {
            timeLeft = durationMinutes * 60; // Convert minutes to seconds
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timer.textContent = `Time left ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                if (timeLeft <= 300) timer.classList.add('warning'); // 5-minute warning
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timer.textContent = "Time's up!";
                    showResults();
                }
                timeLeft--;
            }, 1000);
        }

        function resetExam() {
            currentQuestionIndex = 0;
            questions = [];
            skippedQuestions.clear();
            selectedAnswers.length = 0;
            resultsModal.style.display = 'none';
            clearInterval(timerInterval);
            timer.classList.remove('warning');
            timer.textContent = 'Time left 0:00';
            fetchYears(subjectSelect.value);
        }

        function renderQuestion() {
            if (!questions.length) {
                questionContainer.innerHTML = '<p>No questions available.</p>';
                return;
            }

            const question = questions[currentQuestionIndex];
            const isSkipped = skippedQuestions.has(question.id);
            const selectedAnswer = selectedAnswers[currentQuestionIndex];
            const isExamMode = modeToggle.checked;
            const isLastQuestion = currentQuestionIndex === questions.length - 1;

            questionContainer.innerHTML = `
                <p><strong>Question ${question.id}</strong></p>
                <p class="question-text">${question.text}</p>
                <div class="options">
                    ${question.options.map(option => `
                        <button class="option-btn ${selectedAnswer === option.value ? 'selected' : ''}" data-value="${option.value}">
                            ${option.text}
                        </button>
                    `).join('')}
                </div>
                ${isExamMode ? `
                    <button class="skip-btn ${isSkipped ? 'skipped' : ''}">Skip Question</button>
                ` : `
                    <button class="show-answer-btn">Show Answer with Explanation</button>
                    <div class="answer-explanation">
                        <p><strong>Correct Answer:</strong> ${question.options.find(opt => opt.value === question.correctAnswer).text}</p>
                        <p><strong>Explanation:</strong> ${question.explanation}</p>
                    </div>
                `}
            `;

            const gridItems = gridItemsContainer.querySelectorAll('div');
            gridItems.forEach((item, index) => {
                item.classList.toggle('current', index === currentQuestionIndex);
            });

            const optionButtons = questionContainer.querySelectorAll('.option-btn');
            optionButtons.forEach(button => {
                button.addEventListener('click', () => {
                    optionButtons.forEach(btn => btn.classList.remove('selected'));
                    button.classList.add('selected');
                    selectedAnswers[currentQuestionIndex] = button.dataset.value;
                });
            });

            if (isExamMode) {
                const skipButton = questionContainer.querySelector('.skip-btn');
                if (skipButton) {
                    skipButton.addEventListener('click', () => {
                        skipButton.classList.add('skipped');
                        skippedQuestions.add(question.id);
                        gridItems[currentQuestionIndex].classList.add('skipped');
                        if (currentQuestionIndex < questions.length - 1) {
                            currentQuestionIndex++;
                            renderQuestion();
                        }
                    });
                }
            } else {
                const showAnswerButton = questionContainer.querySelector('.show-answer-btn');
                const answerExplanation = questionContainer.querySelector('.answer-explanation');
                if (showAnswerButton) {
                    showAnswerButton.addEventListener('click', () => {
                        answerExplanation.classList.toggle('show');
                    });
                }
            }

            prevButton.disabled = currentQuestionIndex === 0;
            if (isExamMode && isLastQuestion) {
                nextButton.textContent = 'Show Results';
                nextButton.classList.remove('next');
                nextButton.classList.add('show-results');
            } else {
                nextButton.textContent = 'Next';
                nextButton.classList.remove('show-results');
                nextButton.classList.add('next');
                nextButton.disabled = isLastQuestion && !isExamMode;
            }
        }

        function showResults() {
            if (!modeToggle.checked) return;

            let correct = 0;
            let total = questions.length;
            let resultsHTML = '';

            questions.forEach((question, index) => {
                const userAnswer = selectedAnswers[index];
                const isCorrect = userAnswer === question.correctAnswer;
                if (isCorrect) correct++;
                resultsHTML += `
                    <p>Question ${question.id}: ${question.text}</p>
                    <p>Your Answer: ${userAnswer ? question.options.find(opt => opt.value === userAnswer).text : 'Skipped'}</p>
                    <p class="${isCorrect ? 'correct' : 'incorrect'}">Correct Answer: ${question.options.find(opt => opt.value === question.correctAnswer).text}</p>
                    <p>Explanation: ${question.explanation}</p><br>
                `;
            });

            resultsHTML += `<p class="score"><strong>Score: ${correct} / ${total} (${((correct / total) * 100).toFixed(2)}%)</strong></p>`;
            resultsContent.innerHTML = resultsHTML;
            resultsModal.style.display = 'flex';
            questionContainer.style.display = 'none';
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
            clearInterval(timerInterval);
        }

        function updateExamGrid() {
            gridItemsContainer.innerHTML = questions.map((q, index) => `
                <div class="${index === 0 ? 'current' : ''}">${q.id}</div>
            `).join('');
            const gridItems = gridItemsContainer.querySelectorAll('div');
            gridItems.forEach((item, index) => {
                item.addEventListener('click', () => {
                    currentQuestionIndex = index;
                    renderQuestion();
                });
            });
        }

        function fetchYears(subjectId) {
            fetch(`fetch_data.php?action=get_years&subject_id=${subjectId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(years => {
                    console.log('Fetched years:', years);
                    if (!years.length) {
                        yearSelect.innerHTML = '<option>No years available</option>';
                        return;
                    }
                    yearSelect.innerHTML = years.map(year => 
                        `<option value="${year.id}">${year.year}</option>`
                    ).join('');
                    updateExamTitle();
                    fetchQuestions(yearSelect.value);
                })
                .catch(error => console.error('Error fetching years:', error));
        }

        function fetchQuestions(examYearId) {
            fetch(`fetch_data.php?action=get_questions&exam_year_id=${examYearId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched questions data:', data);
                    questions = data.questions || [];
                    const duration = data.duration || 120; // Default to 120 if not found
                    selectedAnswers.length = 0;
                    selectedAnswers.push(...new Array(questions.length).fill(null));
                    currentQuestionIndex = 0;
                    updateExamGrid();
                    renderQuestion();
                    questionContainer.style.display = 'block';
                    prevButton.style.display = 'block';
                    nextButton.style.display = 'block';
                    resultsModal.style.display = 'none';
                    if (modeToggle.checked) {
                        startTimer(duration); // Start timer with DB duration
                        timer.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error fetching questions:', error));
        }

        function updateExamTitle() {
            const subject = subjectSelect.options[subjectSelect.selectedIndex].text;
            const year = yearSelect.options[yearSelect.selectedIndex]?.text || '';
            examTitle.textContent = `${subject}/${year}`;
        }

        prevButton.addEventListener('click', () => {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                renderQuestion();
            }
        });

        nextButton.addEventListener('click', () => {
            if (modeToggle.checked && currentQuestionIndex === questions.length - 1) {
                showResults();
            } else if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                renderQuestion();
            }
        });

        modeToggle.addEventListener('change', () => {
            resetExam();
            if (modeToggle.checked) {
                examModal.style.display = 'flex';
                timer.style.display = 'block';
            } else {
                examModal.style.display = 'none';
                timer.style.display = 'none';
                clearInterval(timerInterval);
            }
        });

        startExamBtn.addEventListener('click', () => {
            examModal.style.display = 'none';
            fetchQuestions(yearSelect.value);
        });

        closeResultsBtn.addEventListener('click', () => {
            resultsModal.style.display = 'none';
            resetExam();
        });

        subjectSelect.addEventListener('change', () => {
            fetchYears(subjectSelect.value);
        });

        yearSelect.addEventListener('change', () => {
            updateExamTitle();
            fetchQuestions(yearSelect.value);
        });

        timer.style.display = 'none';
        fetchYears(subjectSelect.value);
    </script>
</body>
</html>