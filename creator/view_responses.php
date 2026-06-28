<?php
session_start();
include('config.php');

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== 'creator') {
    header("Location: login.php");
    exit();
}

$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($form_id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid Form ID</div>");
}

// Verify form belongs to this creator
$stmt = $conn->prepare("SELECT title FROM forms WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $form_id, $_SESSION['user_id']);
$stmt->execute();
$form = $stmt->get_result()->fetch_assoc();

if (!$form) {
    die("<div class='alert alert-danger text-center mt-5'>Form not found or access denied.</div>");
}

// Fetch Responses with Username (Improved Query)
$responses = [];
$r_stmt = $conn->prepare("
    SELECT 
        fr.id, 
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
    <title>Responses - <?= htmlspecialchars($form['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/accessibility.js"></script>
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Responses for: <span class="text-primary"><?= htmlspecialchars($form['title']) ?></span></h1>
        <a href="export_responses.php?id=<?= $form_id ?>&format=csv" class="btn btn-success">
             Export CSV
        </a>
    </div>

    <p class="text-muted mb-4"><?= count($responses) ?> response(s) received</p>

    <?php if (empty($responses)): ?>
        <div class="alert alert-info text-center py-5">
            <h4>No responses yet for this form.</h4>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Respondent Name</th>
                        <th>Submission Date</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($responses as $i => $resp): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($resp['respondent_name']) ?></strong>
                                <?php if ($resp['respondent_id']): ?>
                                    <small class="text-success">(Registered User)</small>
                                <?php else: ?>
                                    <small class="text-muted">(Guest)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y h:i A', strtotime($resp['submitted_at'])) ?></td>
                            <td><?= htmlspecialchars($resp['respondent_ip']) ?></td>
                            <td class="text-end">
                                <a href="view_single_response.php?response_id=<?= $resp['id'] ?>&form_id=<?= $form_id ?>" class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </td>
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