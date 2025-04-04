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

        if ($user) {
            // Check if password is hashed (starts with $2y$ indicates bcrypt)
            if (password_verify($password, $user['password'])) {
                // Hashed password match
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
            } elseif ($user['password'] === $password) {
                // Plain text password match (for 'test' user)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
            } else {
                $login_error = "Invalid username or password";
            }
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
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Roboto', sans-serif; }
                body {
                    background: #f5f5f5;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    color: #1f1f1f;
                }
                .login-container {
                    background: #fff;
                    padding: 40px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    width: 100%;
                    max-width: 400px;
                }
                h1 {
                    font-size: 24px;
                    font-weight: 700;
                    color: #1c2526;
                    margin-bottom: 20px;
                    text-align: center;
                }
                .form-group { margin-bottom: 20px; }
                label {
                    font-size: 14px;
                    font-weight: 500;
                    color: #1f1f1f;
                    margin-bottom: 8px;
                    display: block;
                }
                input[type="text"], input[type="password"] {
                    width: 100%;
                    padding: 12px;
                    border: 1px solid #d1d1d1;
                    border-radius: 4px;
                    font-size: 16px;
                    transition: border-color 0.2s ease;
                }
                input:focus {
                    outline: none;
                    border-color: #5028c6;
                }
                button {
                    width: 100%;
                    padding: 12px;
                    background: #5028c6;
                    color: #fff;
                    border: none;
                    border-radius: 4px;
                    font-size: 16px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: background 0.2s ease;
                }
                button:hover { background: #3f1f9b; }
                .error {
                    color: #721c24;
                    background: #f8d7da;
                    padding: 10px;
                    border-radius: 4px;
                    margin-bottom: 20px;
                    text-align: center;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h1>Login to National Exam</h1>
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>National Exam Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Roboto', sans-serif; }
        body {
            background: #f5f5f5;
            color: #1f1f1f;
            line-height: 1.6;
        }
        header {
            background: #1c2526;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        header .logo {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        header .logo i { color: #5028c6; }
        header .logout a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            background: #5028c6;
            border-radius: 4px;
            transition: background 0.2s ease;
        }
        header .logout a:hover { background: #3f1f9b; }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .welcome {
            margin-bottom: 40px;
            text-align: center;
        }
        .welcome h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1c2526;
            margin-bottom: 10px;
        }
        .welcome p {
            font-size: 16px;
            color: #6a6a6a;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 20px;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            cursor: pointer;
        }
        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        .card i {
            font-size: 40px;
            color: #5028c6;
            margin-bottom: 15px;
        }
        .card h2 {
            font-size: 20px;
            font-weight: 500;
            color: #1c2526;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 14px;
            color: #6a6a6a;
            line-height: 1.5;
        }
        .features {
            padding: 40px 0;
        }
        .features .row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 40px;
            align-items: center;
        }
        .features .col-md-5 {
            flex: 0 0 41.66667%;
            max-width: 41.66667%;
            padding: 15px;
        }
        .features .col-md-7 {
            flex: 0 0 58.33333%;
            max-width: 58.33333%;
            padding: 15px;
        }
        .features img {
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }
        .features h3 {
            font-weight: 700;
            color: #28a745;
            font-size: 45px;
            margin-bottom: 20px;
        }
        .features p {
            font-size: 14px;
            color: #6a6a6a;
            line-height: 1.8;
        }
        .features .btn-get-started {
            display: inline-block;
            padding: 10px 20px;
            background: #5028c6;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s ease;
        }
        .features .btn-get-started:hover { background: #3f1f9b; }
        .order-1 { order: 1; }
        .order-2 { order: 2; }
        .order-md-1 { order: 1; }
        .order-md-2 { order: 2; }
        @media (max-width: 768px) {
            .features .col-md-5, .features .col-md-7 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .features h3 { font-size: 32px; text-align: center; }
            .order-md-1, .order-md-2 { order: 0; }
        }
        footer {
            background: #1c2526;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        footer a {
            color: #a1a1a1;
            text-decoration: none;
            margin: 0 10px;
        }
        footer a:hover { color: #fff; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            National Exam
        </div>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="container">
        <div class="welcome">
            <h1>Welcome to Your Learning Dashboard</h1>
            <p>Explore resources to excel in your Grade 12 National Exams</p>
        </div>

        <div class="cards">
            <div class="card">
                <i class="fas fa-book-open"></i>
                <h2>Exam Practice</h2>
                <p>Access past papers, mock exams, and practice questions to boost your skills.</p>
            </div>
            <div class="card">
                <i class="fas fa-sticky-note"></i>
                <h2>Notes</h2>
                <p>Explore concise, well-organized study notes for all subjects.</p>
            </div>
            <div class="card">
                <i class="fas fa-folder-open"></i>
                <h2>Library</h2>
                <p>Browse a vast collection of educational resources and references.</p>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section class="features">
      <div class="container"><br><br>
        <div class="row">
          <div class="col-md-5">
            <img src="assets/img/hero.jpg" class="img-fluid">
          </div>
          <div class="col-md-7 pt-4">
            <h3>Exam Practice</h3>
            <p>
              Enhance your skills with access to past papers, mock exams, and a variety of practice questions designed to prepare you for the Grade 12 National Exams. Practice regularly to build confidence and improve performance.
            </p>
            <a href="exam_practice.php" class="btn-get-started">Read More</a>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 order-1 order-md-2">
            <img src="assets/img/culture.JPG" class="img-fluid">
          </div>
          <div class="col-md-7 pt-5 order-2 order-md-1">
            <h3>Short Notes</h3>
            <p>
              Get concise, well-organized study notes covering all subjects in the curriculum. These notes are designed for quick revision and effective learning, tailored to the needs of Grade 12 students.
            </p>
            <a href="short_notes.php" class="btn-get-started">Read More</a>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5">
            <img src="assets/img/land (2).jpg" class="img-fluid">
          </div>
          <div class="col-md-7 pt-5">
            <h3>Library</h3>
            <p>
              Explore a vast digital library filled with educational resources, including books, articles, and study guides. Access materials anytime to support your exam preparation and deepen your understanding.
            </p>
            <a href="library.php" class="btn-get-started">Read More</a>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 order-1 order-md-2">
            <img src="assets/img/culture.JPG" class="img-fluid">
          </div>
          <div class="col-md-7 pt-5 order-2 order-md-1">
            <h3>Culture</h3>
            <p>
              The Gede'o have their own culture of drinking and food. There are dominant native woody species associated with Enset-coffea crops which play a great role in the food security and ecological sustainability.
            </p>
            <a href="culture.html" class="btn-get-started">Read More</a>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5">
            <img src="assets/img/abba.JPG" class="img-fluid">
          </div>
          <div class="col-md-7 pt-5">
            <h3>Materials</h3>
            <p>
              Access a wide range of supplementary materials to aid your studies, including worksheets, revision guides, and additional resources tailored for Grade 12 National Exam preparation.
            </p>
            <a href="materials.php" class="btn-get-started">Read More</a>
          </div>
        </div>
      </div>
    </section><!-- End Features Section -->

    <footer>
        <p>© 2025 National Exam System. All rights reserved.</p>
        <p><a href="#">Privacy</a> | <a href="#">Terms</a> | <a href="#">Contact</a></p>
    </footer>
</body>
</html>