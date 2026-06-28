<?php
session_start();
include('config.php');

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== 'creator') {
    exit("Access Denied");
}

$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$format = $_GET['format'] ?? 'csv';

if ($form_id <= 0) {
    exit("Invalid Form");
}

// Verify ownership
$stmt = $conn->prepare("SELECT title FROM forms WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $form_id, $_SESSION['user_id']);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    exit("Access Denied");
}

// Fetch Questions
$q_stmt = $conn->prepare("SELECT id, question_text FROM questions WHERE form_id = ? ORDER BY sort_order");
$q_stmt->bind_param("i", $form_id);
$q_stmt->execute();
$questions = $q_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch All Responses
$r_stmt = $conn->prepare("SELECT fr.id as response_id, fr.submitted_at, fa.question_id, fa.answer 
                          FROM form_responses fr 
                          LEFT JOIN form_answers fa ON fr.id = fa.response_id 
                          WHERE fr.form_id = ? 
                          ORDER BY fr.submitted_at DESC, fa.question_id");
$r_stmt->bind_param("i", $form_id);
$r_stmt->execute();
$all_data = $r_stmt->get_result();

$data = [];
while ($row = $all_data->fetch_assoc()) {
    $rid = $row['response_id'];
    if (!isset($data[$rid])) {
        $data[$rid] = ['submitted_at' => $row['submitted_at']];
    }
    $data[$rid][$row['question_id']] = $row['answer'];
}

// CSV Export
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="responses_form_'.$form_id.'.csv"');

    $output = fopen('php://output', 'w');

    // Header
    $headers = ['Submission Date'];
    foreach ($questions as $q) {
        $headers[] = $q['question_text'];
    }
    fputcsv($output, $headers);

    // Data
    foreach ($data as $row) {
        $line = [$row['submitted_at']];
        foreach ($questions as $q) {
            $line[] = $row[$q['id']] ?? '';
        }
        fputcsv($output, $line);
    }
    fclose($output);
    exit;
}

// Future: Add Excel & PDF support using libraries (PhpSpreadsheet, TCPDF, etc.)
else {
    echo "Only CSV supported currently. Coming soon: Excel & PDF";
}