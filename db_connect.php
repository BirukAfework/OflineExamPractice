<?php
$host = 'localhost';
$dbname = 'national_exam';
$username = 'root'; // Change to your MySQL username
$password = '';     // Change to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<?php
// db_connect.php or a setup script
require_once 'db_connect.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Insert a default user (username: School, password: teacher)
    $default_username = 'School';
    $default_password = password_hash('teacher', PASSWORD_DEFAULT); // Hash the password
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$default_username, $default_password]);

    // echo "Users table created and default user added.";
} catch (PDOException $e) {
    echo "Error creating users table: " . $e->getMessage();
}
?>