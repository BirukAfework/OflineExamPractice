<?php
require_once 'db_connect.php';

function getStudent($admission_number) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE admission_number = ?");
    $stmt->execute([$admission_number]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getSubjects() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM subjects");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExamYears($subject_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM exam_years WHERE subject_id = ?");
    $stmt->execute([$subject_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuestions($exam_year_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT q.*, o.option_value, o.option_text 
        FROM questions q 
        LEFT JOIN options o ON q.id = o.question_id 
        WHERE q.exam_year_id = ?
    ");
    $stmt->execute([$exam_year_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $questions = [];
    foreach ($results as $row) {
        if (!isset($questions[$row['id']])) {
            $questions[$row['id']] = [
                'id' => $row['id'],
                'text' => $row['question_text'],
                'correctAnswer' => $row['correct_answer'],
                'explanation' => $row['explanation'],
                'options' => []
            ];
        }
        if ($row['option_value']) {
            $questions[$row['id']]['options'][] = [
                'value' => $row['option_value'],
                'text' => $row['option_text']
            ];
        }
    }
    
    $stmt = $pdo->prepare("SELECT duration_minutes FROM exam_years WHERE id = ?");
    $stmt->execute([$exam_year_id]);
    $duration = $stmt->fetchColumn();
    
    return [
        'questions' => array_values($questions),
        'duration' => $duration !== false ? (int)$duration : 120
    ];
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    switch ($_GET['action']) {
        case 'get_subjects':
            echo json_encode(getSubjects());
            break;
        case 'get_years':
            echo json_encode(getExamYears($_GET['subject_id']));
            break;
        case 'get_questions':
            echo json_encode(getQuestions($_GET['exam_year_id']));
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
    exit;
}
?>