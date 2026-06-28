<?php
session_start();
include('config.php');

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== 'creator') {
    header("Location: login.php");
    exit();
}

$creator_id = $_SESSION["user_id"];
$creator_name = $_SESSION["user_name"] ?? 'Creator';

// ====================== ANALYTICS QUERIES ======================

// Total Forms
$q1 = $conn->prepare("SELECT COUNT(id) as total_forms FROM forms WHERE user_id = ?");
$q1->bind_param("i", $creator_id);
$q1->execute();
$q1->bind_result($total_forms);
$q1->fetch();
$q1->close();

// Total Responses
$q2 = $conn->prepare("SELECT COUNT(DISTINCT fr.id) as total_responses 
                      FROM form_responses fr 
                      JOIN forms f ON fr.form_id = f.id 
                      WHERE f.user_id = ?");
$q2->bind_param("i", $creator_id);
$q2->execute();
$q2->bind_result($total_responses);
$q2->fetch();
$q2->close();

// Accessibility Logs Count (Dynamic connection to accessibility_logs schema)
$q_acc = $conn->prepare("SELECT COUNT(al.id) as total_logs 
                        FROM accessibility_logs al
                        LEFT JOIN forms f ON al.form_id = f.id
                        WHERE f.user_id = ? OR al.form_id IS NULL"); // Includes general logs if form_id is null
$q_acc->bind_param("i", $creator_id);
$q_acc->execute();
$q_acc->bind_result($total_accessibility_logs);
$q_acc->fetch();
$q_acc->close();

// Dynamic Response Trend Data (Last 30 Days)
$chart_labels = [];
$chart_data = [];
$q_trend = $conn->prepare("SELECT DATE(fr.submitted_at) as response_date, COUNT(fr.id) as daily_count 
                           FROM form_responses fr
                           JOIN forms f ON fr.form_id = f.id
                           WHERE f.user_id = ? AND fr.submitted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                           GROUP BY DATE(fr.submitted_at)
                           ORDER BY response_date ASC");
$q_trend->bind_param("i", $creator_id);
$q_trend->execute();
$trend_result = $q_trend->get_result();
while ($trend_row = $trend_result->fetch_assoc()) {
    $chart_labels[] = date('M d', strtotime($trend_row['response_date']));
    $chart_data[] = (int)$trend_row['daily_count'];
}
$q_trend->close();

// Fallback data structure if no trend exists yet
if (empty($chart_labels)) {
    $chart_labels = ['No Data Available'];
    $chart_data = [0];
}

// Forms List with Response Count
$forms_list = [];
$q3 = $conn->prepare("SELECT f.id, f.title, f.description, f.status, f.created_at,
                             COUNT(DISTINCT fr.id) as response_count
                      FROM forms f 
                      LEFT JOIN form_responses fr ON f.id = fr.form_id
                      WHERE f.user_id = ? 
                      GROUP BY f.id 
                      ORDER BY f.id DESC");
$q3->bind_param("i", $creator_id);
$q3->execute();
$result = $q3->get_result();
while ($row = $result->fetch_assoc()) {
    $forms_list[] = $row;
}
$q3->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creator Dashboard - AccessForm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/accessibility.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<div class="container py-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 mb-1">Welcome Back, <?= htmlspecialchars($creator_name) ?>!</h1>
            <p class="text-muted">Form Creator Analytics & Reporting Dashboard</p>
        </div>
        <a href="create_form.php" class="btn btn-primary px-4 py-2">
            Create New Form
        </a>
    </div>

    <h3 class="h5 text-uppercase text-muted fw-bold mb-3">Overview Analytics</h3>
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="display-5 fw-bold text-primary"><?= $total_forms ?></h2>
                    <p class="text-muted mb-0">Total Forms</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="display-5 fw-bold text-success"><?= $total_responses ?></h2>
                    <p class="text-muted mb-0">Total Responses</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <h2 class="display-5 fw-bold text-warning"><?= $total_accessibility_logs ?></h2>
                    <p class="text-muted mb-0">Accessibility Interactions</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Response Trend (Real-time Timeline)</h5>
        </div>
        <div class="card-body">
            <canvas id="responseChart" height="100" aria-label="Response trend chart" role="img"></canvas>
        </div>
    </div>

    <h3 class="h5 text-uppercase text-muted fw-bold mb-3">Your Forms & Analytics</h3>

    <?php if (empty($forms_list)): ?>
        <div class="card p-5 text-center border-0 shadow-sm">
            <p class="text-muted mb-0">You haven't created any forms yet.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white" id="formsTable" aria-label="Forms analytics table">
                <thead class="table-light">
                    <tr>
                        <th>Form Title</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th class="text-center">Responses</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($forms_list as $form): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($form['title']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars(substr($form['description'] ?? '', 0, 80)) ?>...</small>
                            </td>
                            <td><?= date('M d, Y', strtotime($form['created_at'])) ?></td>
                            <td>
                                <span class="badge <?= $form['status']=='active' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($form['status']) ?>
                                </span>
                            </td>
                            <td class="text-center fw-bold"><?= $form['response_count'] ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="view_responses.php?id=<?= $form['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        View Responses
                                    </a>
                                    <a href="view_form.php?id=<?= $form['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        Preview
                                    </a>
                                    <button onclick="exportFormData(<?= $form['id'] ?>)" class="btn btn-outline-success btn-sm">
                                        Export
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('responseChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Responses Timeline',
                data: <?= json_encode($chart_data) ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});

// Export Function
function exportFormData(formId) {
    if (confirm("Export responses for this form in CSV format?")) {
        window.location.href = `export_responses.php?id=${formId}&format=csv`;
    }
}
</script>

</body>
</html>