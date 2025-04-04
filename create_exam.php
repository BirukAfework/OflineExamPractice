<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session for authentication
session_start();

// Include database connection
require_once 'db_connect.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Fetch user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        } else {
            $login_error = "Invalid username or password";
        }
    }

    // If not authenticated, show login form
    if (!isset($_SESSION['user_id'])) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login - National Exam System</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
                body { background-color: #f5f7fa; color: #2d3748; display: flex; justify-content: center; align-items: center; height: 100vh; }
                .login-container { background: #ffffff; padding: 32px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; }
                h1 { font-size: 24px; font-weight: 600; color: #1a73e8; margin-bottom: 24px; text-align: center; }
                .form-group { margin-bottom: 16px; }
                label { display: block; font-size: 15px; font-weight: 500; color: #2d3748; margin-bottom: 8px; }
                input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; }
                input:focus { outline: none; border-color: #1a73e8; background: #f1f5f9; }
                button { width: 100%; padding: 10px; background: #1a73e8; color: #ffffff; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s ease; }
                button:hover { background: #1557b0; }
                .error { color: #721c24; background: #f8d7da; padding: 12px; border-radius: 6px; margin-bottom: 16px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h1>Admin Login</h1>
                <?php if (isset($login_error)): ?>
                    <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required placeholder="Enter password">
                    </div>
                    <button type="submit" name="login">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// If authenticated, proceed with exam creation logic

// Fetch subjects for the dropdown
function getSubjects() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM subjects");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$subjects = getSubjects();

// Handle form submission (manual entry)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_exam'])) {
    try {
        $pdo->beginTransaction();

        // Insert exam year
        $subject_id = $_POST['subject_id'];
        $year = $_POST['year'];
        $duration_minutes = $_POST['duration_minutes'];

        $stmt = $pdo->prepare("INSERT INTO exam_years (year, subject_id, duration_minutes) VALUES (?, ?, ?)");
        $stmt->execute([$year, $subject_id, $duration_minutes]);
        $exam_year_id = $pdo->lastInsertId();

        // Handle questions
        foreach ($_POST['questions'] as $index => $question_data) {
            $question_text = $question_data['text'];
            $correct_answer = $question_data['correct_answer'];
            $explanation = $question_data['explanation'];

            // Handle image upload (optional)
            $image_path = null;
            if (isset($_FILES['questions']['name'][$index]['image']) && $_FILES['questions']['error'][$index]['image'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $image_name = uniqid() . '_' . basename($_FILES['questions']['name'][$index]['image']);
                $image_path = $upload_dir . $image_name;
                move_uploaded_file($_FILES['questions']['tmp_name'][$index]['image'], $image_path);
            }

            $stmt = $pdo->prepare("INSERT INTO questions (exam_year_id, question_text, correct_answer, explanation, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$exam_year_id, $question_text, $correct_answer, $explanation, $image_path]);
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

// Handle SQL file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    try {
        $sql_file = $_FILES['sql_file']['tmp_name'];
        if ($_FILES['sql_file']['error'] === UPLOAD_ERR_OK && is_uploaded_file($sql_file)) {
            $sql_content = file_get_contents($sql_file);
            $pdo->exec($sql_content);
            $success_message = "Questions imported successfully from SQL file!";
        } else {
            $error_message = "Error uploading SQL file.";
        }
    } catch (Exception $e) {
        $error_message = "Error importing SQL: " . $e->getMessage();
    }
}

// Update schema to include image_path if not already present
$pdo->exec("ALTER TABLE questions ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) NULL");
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
        select, input[type="number"], input[type="file"], textarea { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; color: #2d3748; background: #ffffff; }
        select:focus, input:focus, textarea:focus { outline: none; border-color: #1a73e8; background: #f1f5f9; }
        textarea { resize: vertical; min-height: 80px; }
        .question-block { border: 1px solid #e2e8f0; padding: 16px; border-radius: 6px; background: #fafafa; margin-bottom: 16px; }
        .option-group { margin-top: 12px; display: flex; gap: 8px; align-items: center; }
        .option-group input[type="text"] { flex: 1; }
        button { padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s ease; }
        #generate-questions { background: #1a73e8; color: #ffffff; margin-top: 16px; }
        #generate-questions:hover { background: #1557b0; }
        #submit-exam { background: #2ecc71; color: #ffffff; margin-top: 24px; }
        #submit-exam:hover { background: #27ae60; }
        #submit-sql { background: #f39c12; color: #ffffff; margin-top: 16px; }
        #submit-sql:hover { background: #e67e22; }
        .message { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .sql-upload { margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 16px; }
        .logout { text-align: right; margin-bottom: 16px; }
        .logout a { color: #1a73e8; text-decoration: none; font-size: 14px; }
        .logout a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <h1>Create New Exam</h1>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="exam-form">
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

            <div class="form-group">
                <label for="num_questions">Number of Questions</label>
                <input type="number" id="num_questions" min="1" required placeholder="e.g., 5">
                <button type="button" id="generate-questions">Generate Questions</button>
            </div>

            <div id="questions-container"></div>

            <button type="submit" name="submit_exam" id="submit-exam">Create Exam</button>
        </form>

        <div class="sql-upload">
            <h2>Import Questions from SQL File</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="sql_file">Upload SQL File</label>
                    <input type="file" name="sql_file" id="sql_file" accept=".sql" required>
                </div>
                <button type="submit" id="submit-sql">Import SQL</button>
            </form>
        </div>
    </div>

    <script>
        const questionsContainer = document.getElementById('questions-container');

        function addQuestion(index) {
            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block';
            questionBlock.innerHTML = `
                <h3>Question ${index + 1}</h3>
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea name="questions[${index}][text]" required placeholder="Enter question text"></textarea>
                </div>
                <div class="form-group">
                    <label>Image (Optional)</label>
                    <input type="file" name="questions[${index}][image]" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Options</label>
                    <div class="option-group">
                        <input type="text" name="questions[${index}][options][0][value]" value="a" readonly style="width: 40px;">
                        <input type="text" name="questions[${index}][options][0][text]" required placeholder="Option A">
                    </div>
                    <div class="option-group">
                        <input type="text" name="questions[${index}][options][1][value]" value="b" readonly style="width: 40px;">
                        <input type="text" name="questions[${index}][options][1][text]" required placeholder="Option B">
                    </div>
                    <div class="option-group">
                        <input type="text" name="questions[${index}][options][2][value]" value="c" readonly style="width: 40px;">
                        <input type="text" name="questions[${index}][options][2][text]" required placeholder="Option C">
                    </div>
                    <div class="option-group">
                        <input type="text" name="questions[${index}][options][3][value]" value="d" readonly style="width: 40px;">
                        <input type="text" name="questions[${index}][options][3][text]" required placeholder="Option D">
                    </div>
                </div>
                <div class="form-group">
                    <label>Correct Answer</label>
                    <select name="questions[${index}][correct_answer]" required>
                        <option value="">Select correct answer</option>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Explanation</label>
                    <textarea name="questions[${index}][explanation]" placeholder="Enter explanation (optional)"></textarea>
                </div>
            `;
            questionsContainer.appendChild(questionBlock);
        }

        document.getElementById('generate-questions').addEventListener('click', () => {
            const numQuestions = parseInt(document.getElementById('num_questions').value);
            if (isNaN(numQuestions) || numQuestions < 1) {
                alert("Please enter a valid number of questions.");
                return;
            }
            questionsContainer.innerHTML = ''; // Clear existing questions
            for (let i = 0; i < numQuestions; i++) {
                addQuestion(i);
            }
        });
    </script>
</body>
</html>