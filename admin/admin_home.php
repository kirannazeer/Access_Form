<?php
session_start();
include('../config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: ../admin_login.php");
    exit;
}

// ==================== STATISTICS ====================

// Total Forms
$total_forms = $conn->query("SELECT COUNT(*) FROM forms")->fetch_row()[0];

// Total Responses
$total_responses = $conn->query("SELECT COUNT(*) FROM form_responses")->fetch_row()[0];

// Total Creators & Respondents
$users = $conn->query("SELECT 
    SUM(CASE WHEN role = 'creator' THEN 1 ELSE 0 END) as creators,
    SUM(CASE WHEN role = 'Respondent' THEN 1 ELSE 0 END) as respondents 
    FROM users")->fetch_assoc();

// Accessibility Usage
$acc_stats = $conn->query("SELECT 
    SUM(CASE WHEN action = 'high-contrast' THEN 1 ELSE 0 END) as high_contrast,
    SUM(CASE WHEN action = 'dyslexia' THEN 1 ELSE 0 END) as dyslexia,
    SUM(CASE WHEN action = 'voice-reader' THEN 1 ELSE 0 END) as voice_reader,
    SUM(CASE WHEN action = 'large-text' THEN 1 ELSE 0 END) as large_text
    FROM accessibility_logs")->fetch_assoc();

// Recent Forms
$recent_forms = $conn->query("
    SELECT f.id, f.title, u.name as creator_name, f.status, f.created_at,
           COUNT(fr.id) as responses
    FROM forms f
    LEFT JOIN users u ON f.user_id = u.id
    LEFT JOIN form_responses fr ON f.id = fr.form_id
    GROUP BY f.id
    ORDER BY f.created_at DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AccessForm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        :root {
            --primary: #2563eb;
        }
        .admin-hero {
            background: linear-gradient(135deg, #1e2937, #2563eb);
            color: white;
            padding: 60px 0;
            border-radius: 0 0 30px 30px;
        }
        .stat-card {
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="admin-hero">
    <div class="container">
        <h1 class="display-5 fw-bold text-white">Admin Control Center</h1>
        <p class="lead opacity-90">AccessForm System Overview & Accessibility Monitoring</p>
    </div>
</div>

<div class="container py-5">

    <!-- Key Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6">
            <div class="stat-card card h-100 border-0 shadow-sm text-center p-4">
                <i class="fas fa-file-alt fa-2x text-primary mb-3"></i>
                <h3 class="fw-bold"><?= number_format($total_forms) ?></h3>
                <p class="text-muted mb-0">Total Forms</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card card h-100 border-0 shadow-sm text-center p-4">
                <i class="fas fa-reply fa-2x text-success mb-3"></i>
                <h3 class="fw-bold"><?= number_format($total_responses) ?></h3>
                <p class="text-muted mb-0">Total Responses</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card card h-100 border-0 shadow-sm text-center p-4">
                <i class="fas fa-user-tie fa-2x text-info mb-3"></i>
                <h3 class="fw-bold"><?= number_format($users['creators']) ?></h3>
                <p class="text-muted mb-0">Creators</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card card h-100 border-0 shadow-sm text-center p-4">
                <i class="fas fa-users fa-2x text-warning mb-3"></i>
                <h3 class="fw-bold"><?= number_format($users['respondents']) ?></h3>
                <p class="text-muted mb-0">Respondents</p>
            </div>
        </div>
    </div>

    <!-- Accessibility Usage -->
    <h4 class="mb-3">Accessibility Feature Usage</h4>
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card shadow-sm p-4 text-center">
                <h5>High Contrast</h5>
                <h3 class="text-warning"><?= number_format($acc_stats['high_contrast']) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-4 text-center">
                <h5>Dyslexia Mode</h5>
                <h3 class="text-info"><?= number_format($acc_stats['dyslexia']) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-4 text-center">
                <h5>Voice Reader</h5>
                <h3 class="text-success"><?= number_format($acc_stats['voice_reader']) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-4 text-center">
                <h5>Larger Text</h5>
                <h3 class="text-primary"><?= number_format($acc_stats['large_text']) ?></h3>
            </div>
        </div>
    </div>

    <!-- Recent Forms -->
    <h4 class="mb-3">Recent Forms</h4>
    <div class="table-responsive">
        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Form Title</th>
                    <th>Creator</th>
                    <th>Responses</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($form = $recent_forms->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($form['title']) ?></td>
                    <td><?= htmlspecialchars($form['creator_name'] ?? 'Unknown') ?></td>
                    <td><strong><?= $form['responses'] ?></strong></td>
                    <td>
                        <span class="badge <?= $form['status']=='active' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($form['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($form['created_at'])) ?></td>
                    <td>
                        <a href="admin_view_responses.php?form_id=<?= $form['id'] ?>" class="btn btn-sm btn-primary">View Responses</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>