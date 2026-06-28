<?php
session_start();
include('config.php');

// No strict login required for respondents (public access)
$respondent_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$respondent_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';

// Fetch All Active Forms
$stmt = $conn->prepare("SELECT id, title, description, created_at 
                        FROM forms 
                        WHERE status = 'active' 
                        ORDER BY created_at DESC");
$stmt->execute();
$forms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Forms - AccessForm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="./accessibility.js"></script>
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Available Accessible Forms</h1>
        <p class="lead text-muted">Choose any form below to participate. Your responses help improve services.</p>
    </div>

    <?php if (empty($forms)): ?>
        <div class="alert alert-info text-center py-5">
            <h4>No active forms available at the moment.</h4>
            <p>Please check back later.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($forms as $form): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($form['title']) ?></h5>
                            <p class="card-text text-muted flex-grow-1">
                                <?= htmlspecialchars(substr($form['description'] ?? 'No description available.', 0, 120)) ?>...
                            </p>
                            
                            <div class="mt-auto">
                                <a href="view_form.php?id=<?= $form['id'] ?>" 
                                   class="btn btn-primary w-100">
                                    Fill This Form
                                </a>
                            </div>
                        </div>
                        <div class="card-footer text-muted small">
                            Created: <?= date('M d, Y', strtotime($form['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>