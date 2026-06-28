<?php
// log_accessibility.php
session_start();
include('config.php');

$data = json_decode(file_get_contents('php://input'), true);

$action   = trim($data['action'] ?? '');
$form_id  = isset($data['form_id']) ? (int)$data['form_id'] : null;
$user_id  = $_SESSION['user_id'] ?? null;
$ip       = $_SERVER['REMOTE_ADDR'];

if (!empty($action)) {
    $stmt = $conn->prepare("
        INSERT INTO accessibility_logs (form_id, user_id, action, ip_address) 
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->bind_param("iiss", $form_id, $user_id, $action, $ip);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Logged']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB Error']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action provided']);
}
?>