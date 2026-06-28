<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: ../admin_login.php");
    exit;
}

// Handle Status Change
if (isset($_GET['action']) && isset($_GET['id'])) {
    $form_id = (int)$_GET['id'];
    $new_status = $_GET['action'] === 'activate' ? 'active' : 
                 ($_GET['action'] === 'close' ? 'closed' : 'draft');

    $stmt = $conn->prepare("UPDATE forms SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $new_status, $form_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_monitor_surveys.php");
    exit;
}

// Handle Delete Form
if (isset($_GET['delete_id'])) {
    $form_id = (int)$_GET['delete_id'];

    // Delete related data first
    $conn->prepare("DELETE FROM form_answers WHERE response_id IN (SELECT id FROM form_responses WHERE form_id = ?)")->execute([$form_id]);
    $conn->prepare("DELETE FROM form_responses WHERE form_id = ?")->execute([$form_id]);
    $conn->prepare("DELETE FROM questions WHERE form_id = ?")->execute([$form_id]);
    
    $stmt = $conn->prepare("DELETE FROM forms WHERE id = ?");
    $stmt->bind_param("i", $form_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_monitor_surveys.php");
    exit;
}

// Fetch All Forms with Creator Name & Response Count
$forms = [];
$query = "
    SELECT 
        f.id, 
        f.title, 
        f.description, 
        f.status, 
        f.created_at,
        u.name as creator_name,
        COUNT(DISTINCT fr.id) as total_responses
    FROM forms f
    LEFT JOIN users u ON f.user_id = u.id
    LEFT JOIN form_responses fr ON f.id = fr.form_id
    GROUP BY f.id
    ORDER BY f.created_at DESC
";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $forms[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Surveys - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Survey Monitoring Dashboard</h2>
            <p class="text-muted">Monitor all forms created by users</p>
        </div>
        <a href="manage_users.php" class="btn btn-outline-primary">Manage Users</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Form Title</th>
                            <th>Creator</th>
                            <th>Responses</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($forms) > 0): ?>
                            <?php foreach ($forms as $i => $form): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($form['title']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars(substr($form['description'] ?? '', 0, 80)) ?>...</small>
                                    </td>
                                    <td><?= htmlspecialchars($form['creator_name'] ?? 'Unknown') ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= $form['total_responses'] ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = $form['status'] === 'active' ? 'bg-success' : 
                                                      ($form['status'] === 'closed' ? 'bg-danger' : 'bg-warning');
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= ucfirst($form['status']) ?></span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($form['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="view_form.php?id=<?= $form['id'] ?>" target="_blank" 
                                               class="btn btn-outline-primary">View</a>
                                            
                                            <?php if ($form['status'] !== 'active'): ?>
                                                <a href="?action=activate&id=<?= $form['id'] ?>" 
                                                   class="btn btn-outline-success">Activate</a>
                                            <?php endif; ?>

                                            <?php if ($form['status'] !== 'closed'): ?>
                                                <a href="?action=close&id=<?= $form['id'] ?>" 
                                                   class="btn btn-outline-danger">Close</a>
                                            <?php endif; ?>

                                            <a href="admin_view_responses.php?form_id=<?= $form['id'] ?>" 
                                               class="btn btn-outline-info">Responses</a>

                                            <a href="?delete_id=<?= $form['id'] ?>" 
                                               class="btn btn-outline-danger"
                                               onclick="return confirm('Delete this survey and all its responses?')">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    No surveys found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>