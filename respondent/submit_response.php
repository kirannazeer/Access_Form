<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_id = (int)$_POST['form_id'];
    $respondent_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $respondent_ip = $_SERVER['REMOTE_ADDR'];

    // Create tables if not exist
    $conn->query("CREATE TABLE IF NOT EXISTS form_responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        form_id INT NOT NULL,
        respondent_id INT NULL,
        respondent_ip VARCHAR(50),
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (form_id) REFERENCES forms(id)
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS form_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        response_id INT NOT NULL,
        question_id INT NOT NULL,
        answer TEXT,
        FOREIGN KEY (response_id) REFERENCES form_responses(id)
    )");

    // Insert Response
    $stmt = $conn->prepare("INSERT INTO form_responses (form_id, respondent_id, respondent_ip) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $form_id, $respondent_id, $respondent_ip);
    $stmt->execute();
    $response_id = $conn->insert_id;

    // Insert Answers
    foreach ($_POST['answers'] ?? [] as $qid => $answer) {
        if (is_array($answer)) $answer = implode(", ", $answer);
        $answer = trim($answer);
        if (!empty($answer)) {
            $q_stmt = $conn->prepare("INSERT INTO form_answers (response_id, question_id, answer) VALUES (?, ?, ?)");
            $q_stmt->bind_param("iis", $response_id, $qid, $answer);
            $q_stmt->execute();
        }
    }

    // File Uploads
    if (isset($_FILES['answers'])) {
        foreach ($_FILES['answers']['name'] as $qid => $name) {
            if ($_FILES['answers']['error'][$qid] == 0) {
                $upload_dir = "uploads/responses/";
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

                $file_name = $response_id . "_" . time() . "_" . basename($name);
                $tmp_name = $_FILES['answers']['tmp_name'][$qid];
                
                if (move_uploaded_file($tmp_name, $upload_dir . $file_name)) {
                    $file_answer = "FILE:" . $file_name;
                    $q_stmt = $conn->prepare("INSERT INTO form_answers (response_id, question_id, answer) VALUES (?, ?, ?)");
                    $q_stmt->bind_param("iis", $response_id, $qid, $file_answer);
                    $q_stmt->execute();
                }
            }
        }
    }

    echo "<div class='alert alert-success text-center mt-5 py-5'>
            <h2>Thank You!</h2>
            <p>Your response has been recorded successfully.</p>
            <a href='respondent_dashboard.php' class='btn btn-primary'>Back to Forms</a>
          </div>";
} else {
    header("Location: respondent_dashboard.php");
}
?>