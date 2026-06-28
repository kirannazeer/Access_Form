<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: ../admin_login.php");
    exit;
}

// Fetch Overall Accessibility Stats
$stats = [];
$queries = [
    'high_contrast' => "SELECT COUNT(*) FROM accessibility_logs WHERE action = 'high-contrast'",
    'dyslexia'      => "SELECT COUNT(*) FROM accessibility_logs WHERE action = 'dyslexia'",
    'voice_reader'  => "SELECT COUNT(*) FROM accessibility_logs WHERE action = 'voice-reader'",
    'large_text'    => "SELECT COUNT(*) FROM accessibility_logs WHERE action = 'large-text'",
    'total_forms'   => "SELECT COUNT(*) FROM forms",
    'total_responses'=> "SELECT COUNT(*) FROM form_responses"
];

foreach ($queries as $key => $sql) {
    $result = $conn->query($sql);
    $stats[$key] = $result->fetch_row()[0];
}

// Top Forms by Accessibility Usage
$top_forms = $conn->query("
    SELECT f.title, 
           COUNT(DISTINCT al.id) as accessibility_uses,
           COUNT(DISTINCT fr.id) as total_responses
    FROM forms f
    LEFT JOIN accessibility_logs al ON f.id = al.form_id
    LEFT JOIN form_responses fr ON f.id = fr.form_id
    GROUP BY f.id
    ORDER BY accessibility_uses DESC 
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility Compliance - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container py-5">
    <h2 class="mb-4">Accessibility Compliance Monitor</h2>

    <!-- Summary Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="text-warning"><?= $stats['high_contrast'] ?></h3>
                    <p class="text-muted">High Contrast Uses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="text-info"><?= $stats['dyslexia'] ?></h3>
                    <p class="text-muted">Dyslexia Mode</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="text-success"><?= $stats['voice_reader'] ?></h3>
                    <p class="text-muted">Voice Reader</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="text-primary"><?= $stats['large_text'] ?></h3>
                    <p class="text-muted">Large Text</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Forms Table -->
    <h4 class="mb-3">Forms with Highest Accessibility Usage</h4>
    <table class="table table-hover">
        <thead class="table-dark">
            <tr>
                <th>Form Title</th>
                <th>Accessibility Uses</th>
                <th>Total Responses</th>
                <th>Compliance Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $top_forms->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><strong><?= $row['accessibility_uses'] ?></strong></td>
                    <td><?= $row['total_responses'] ?></td>
                    <td>
                        <?php 
                        $rate = $row['total_responses'] > 0 ? 
                            round(($row['accessibility_uses'] / $row['total_responses']) * 100) : 0;
                        ?>
                        <span class="badge bg-<?= $rate > 70 ? 'success' : ($rate > 40 ? 'warning' : 'danger') ?>">
                            <?= $rate ?>%
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>