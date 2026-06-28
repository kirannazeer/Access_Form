<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: ../admin_login.php");
    exit;
}

$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
if ($form_id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid Form ID</div>");
}

// Get Form Details
$stmt = $conn->prepare("SELECT title FROM forms WHERE id = ?");
$stmt->bind_param("i", $form_id);
$stmt->execute();
$form = $stmt->get_result()->fetch_assoc();

if (!$form) {
    die("<div class='alert alert-danger text-center mt-5'>Form not found.</div>");
}

// Get All Responses with Respondent Info
$responses = [];
$r_stmt = $conn->prepare("
    SELECT 
        fr.id as response_id,
        fr.respondent_id,
        fr.respondent_ip,
        fr.submitted_at,
        COALESCE(u.name, 'Guest') as respondent_name
    FROM form_responses fr
    LEFT JOIN users u ON fr.respondent_id = u.id
    WHERE fr.form_id = ?
    ORDER BY fr.submitted_at DESC
");
$r_stmt->bind_param("i", $form_id);
$r_stmt->execute();
$result = $r_stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $responses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responses - <?= htmlspecialchars($form['title']) ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Responses for: <span class="text-primary"><?= htmlspecialchars($form['title']) ?></span></h2>
        <a href="admin_monitor_surveys.php" class="btn btn-secondary">← Back to All Surveys</a>
    </div>

    <p class="text-muted"><?= count($responses) ?> response(s) received</p>

    <?php if (empty($responses)): ?>
        <div class="alert alert-info text-center py-5">
            <h5>No responses yet for this form.</h5>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Respondent Name</th>
                        <th>Submitted At</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($responses as $i => $resp): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($resp['respondent_name']) ?></strong>
                                <?php if ($resp['respondent_id']): ?>
                                    <small class="text-success">(Registered)</small>
                                <?php else: ?>
                                    <small class="text-muted">(Guest)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y h:i A', strtotime($resp['submitted_at'])) ?></td>
                            <td><?= htmlspecialchars($resp['respondent_ip']) ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>