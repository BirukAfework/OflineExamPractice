<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';

// Fetch subjects for the dropdown
function getSubjects() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM subjects");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$subjects = getSubjects();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Insert exam year
        $subject_id = $_POST['subject_id'];
        $year = $_POST['year'];
        $duration_minutes = $_POST['duration_minutes'];

        $stmt = $pdo->prepare("INSERT INTO exam_years (year, subject_id, duration_minutes) VALUES (?, ?, ?)");
        $stmt->execute([$year, $subject_id, $duration_minutes]);
        $exam_year_id = $pdo->lastInsertId();

        // Insert questions and options
        foreach ($_POST['questions'] as $index => $question_data) {
            $question_text = $question_data['text'];
            $correct_answer = $question_data['correct_answer'];
            $explanation = $question_data['explanation'];

            $stmt = $pdo->prepare("INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation) VALUES (?, ?, ?, ?)");
            $stmt->execute([$exam_year_id, $question_text, $correct_answer, $explanation]);
            $question_id = $pdo->lastInsertId();

            foreach ($question_data['options'] as $option) {
                $option_value = $option['value'];
                $option_text = $option['text'];

                $stmt = $pdo->prepare("INSERT INTO options (question_id, option_value, option_text) VALUES (?, ?, ?)");
                $stmt->execute([$question_id, $option_value, $option_text]);
            }
        }

        $pdo->commit();
        $success_message = "Exam created successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error creating exam: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam - National Exam</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f5f7fa; color: #2d3748; line-height: 1.6; padding: 24px; }
        .container { max-width: 800px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        h1 { font-size: 24px; font-weight: 600; color: #1a73e8; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 15px; font-weight: 500; color: #2d3748; margin-bottom: 8px; }
        select, input[type="number"], textarea { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; color: #2d3748; background: #ffffff; }
        select:focus, input:focus, textarea:focus { outline: none; border-color: #1a73e8; background: #f1f5f9; }
        textarea { resize: vertical; min-height: 80px; }
        .question-block { border: 1px solid #e2e8f0; padding: 16px; border-radius: 6px; background: #fafafa; margin-bottom: 16px; }
        .option-group { margin-top: 12px; display: flex; gap: 8px; align-items: center; }
        .option-group input[type="text"] { flex: 1; }
        .option-group input[type="radio"] { margin-right: 8px; }
        button { padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s ease; }
        #add-question { background: #1a73e8; color: #ffffff; margin-top: 16px; }
        #add-question:hover { background: #1557b0; }
        #submit-exam { background: #2ecc71; color: #ffffff; margin-top: 24px; }
        #submit-exam:hover { background: #27ae60; }
        .message { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Exam</h1>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" id="exam-form">
            <div class="form-group">
                <label for="subject_id">Subject</label>
                <select name="subject_id" id="subject_id" required>
                    <option value="">Select a subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="year">Exam Year</label>
                <input type="number" name="year" id="year" min="2000" max="2100" required placeholder="e.g., 2025">
            </div>

            <div class="form-group">
                <label for="duration_minutes">Duration (minutes)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" min="1" required placeholder="e.g., 120">
            </div>

            <div id="questions-container">
                <!-- Questions will be added here dynamically -->
            </div>

            <button type="button" id="add-question">Add Question</button>
            <button type="submit" id="submit-exam">Create Exam</button>
        </form>
    </div>

    <script>
        const questionsContainer = document.getElementById('questions-container');
        let questionCount = 0;

        function addQuestion() {
            questionCount++;
            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block';
            questionBlock.innerHTML = `
                <h3>Question ${questionCount}</h3>
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea name="questions[${questionCount - 1}][text]" required placeholder="Enter question text"></textarea>
                </div>
                <div class="form-group">
                    <label>Options</label>
                    <div class="option-group">
                        <input type="radio" name="questions[${questionCount - 1}][correct_answer]" value="a" required>
                        <input type="text" name="questions[${questionCount - 1}][options][0][value]" value="a" readonly style="width: 40px;">
                        <input type="text" name="questions[${questionCount - 1}][options][0][text]" required placeholder="Option A">
                    </div>
                    <div class="option-group">
                        <input type="radio" name="questions[${questionCount - 1}][correct_answer]" value="b">
                        <input type="text" name="questions[${questionCount - 1}][options][1][value]" value="b" readonly style="width: 40px;">
                        <input type="text" name="questions[${questionCount - 1}][options][1][text]" required placeholder="Option B">
                    </div>
                    <div class="option-group">
                        <input type="radio" name="questions[${questionCount - 1}][correct_answer]" value="c">
                        <input type="text" name="questions[${questionCount - 1}][options][2][value]" value="c" readonly style="width: 40px;">
                        <input type="text" name="questions[${questionCount - 1}][options][2][text]" required placeholder="Option C">
                    </div>
                    <div class="option-group">
                        <input type="radio" name="questions[${questionCount - 1}][correct_answer]" value="d">
                        <input type="text" name="questions[${questionCount - 1}][options][3][value]" value="d" readonly style="width: 40px;">
                        <input type="text" name="questions[${questionCount - 1}][options][3][text]" required placeholder="Option D">
                    </div>
                </div>
                <div class="form-group">
                    <label>Explanation</label>
                    <textarea name="questions[${questionCount - 1}][explanation]" placeholder="Enter explanation (optional)"></textarea>
                </div>
            `;
            questionsContainer.appendChild(questionBlock);
        }

        document.getElementById('add-question').addEventListener('click', addQuestion);

        // Add one question by default
        addQuestion();
    </script>
</body>
</html>
