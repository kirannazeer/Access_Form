<?php
session_start();
include('config.php');

// Guard: Only creators allowed
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== 'creator') {
    header("Location: login.php");
    exit();
}

$response_id = isset($_GET['response_id']) ? (int)$_GET['response_id'] : 0;
$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;

if ($response_id <= 0 || $form_id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid Request Parameters</div>");
}

// Security Check: Verify form belongs to current active creator and response belongs to the form
$stmt = $conn->prepare("
    SELECT fr.id, fr.submitted_at, fr.respondent_ip, COALESCE(u.name, 'Guest') as respondent_name, f.title 
    FROM form_responses fr
    JOIN forms f ON fr.form_id = f.id
    LEFT JOIN users u ON fr.respondent_id = u.id
    WHERE fr.id = ? AND fr.form_id = ? AND f.user_id = ?
");
$stmt->bind_param("iii", $response_id, $form_id, $_SESSION['user_id']);
$stmt->execute();
$meta_details = $stmt->get_result()->fetch_assoc();

if (!$meta_details) {
    die("<div class='alert alert-danger text-center mt-5'>Response record not found or access denied.</div>");
}
$stmt->close();

// Fetch form questions along with submitted answers using a Left Join matching question mappings
$q_stmt = $conn->prepare("
    SELECT q.id as question_id, q.question_text, q.question_type, fa.answer 
    FROM questions q
    LEFT JOIN form_answers fa ON q.id = fa.question_id AND fa.response_id = ?
    WHERE q.form_id = ?
    ORDER BY q.sort_order ASC, q.id ASC
");
$q_stmt->bind_param("ii", $response_id, $form_id);
$q_stmt->execute();
$questions_result = $q_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Submission - <?= htmlspecialchars($meta_details['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<div class="container py-5">
    <div class="max-width-md mx-auto" style="max-width: 850px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="view_responses.php?id=<?= $form_id ?>" class="btn btn-sm btn-outline-secondary">
                ← Back to Responses
            </a>
            <span class="text-muted small">Response ID: #<?= $response_id ?></span>
        </div>

        <div class="card p-4 border-0 shadow-sm mb-4 bg-white">
            <h1 class="h3 text-dark mb-1"><?= htmlspecialchars($meta_details['title']) ?></h1>
            <p class="text-muted small mb-0">Detailed individual feedback breakdown</p>
            <hr class="my-3">
            
            <div class="row g-3 text-start">
                <div class="col-sm-4">
                    <span class="text-muted d-block small">Submitted By</span>
                    <strong class="text-secondary"><?= htmlspecialchars($meta_details['respondent_name']) ?></strong>
                </div>
                <div class="col-sm-4">
                    <span class="text-muted d-block small">Submission Time</span>
                    <strong class="text-secondary"><?= date('M d, Y h:i A', strtotime($meta_details['submitted_at'])) ?></strong>
                </div>
                <div class="col-sm-4">
                    <span class="text-muted d-block small">IP Metadata Reference</span>
                    <strong class="text-secondary"><?= htmlspecialchars($meta_details['respondent_ip']) ?></strong>
                </div>
            </div>
        </div>

        <h2 class="h5 mb-3 text-secondary">Question Responses</h2>
        
        <?php if ($questions_result->num_rows === 0): ?>
            <div class="alert alert-warning">No configuration criteria loaded for evaluation fields.</div>
        <?php else: ?>
            <?php $counter = 1; ?>
            <?php while ($item = $questions_result->fetch_assoc()): ?>
                <div class="card p-4 border-0 shadow-sm mb-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="fw-bold text-dark">
                            Q<?= $counter++ ?>. <?= htmlspecialchars($item['question_text']) ?>
                        </span>
                        <span class="badge bg-light text-muted border text-capitalize"><?= htmlspecialchars($item['question_type']) ?></span>
                    </div>
                    
                    <div class="p-3 bg-light rounded border-start border-primary border-3 mt-2">
                        <?php if ($item['answer'] !== null && $item['answer'] !== ''): ?>
                            <p class="mb-0 text-dark font-monospace" style="white-space: pre-line;"><?= htmlspecialchars($item['answer']) ?></p>
                        <?php else: ?>
                            <em class="text-muted small">No answer provided / Left empty</em>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
$q_stmt->close();
$conn->close();
?>