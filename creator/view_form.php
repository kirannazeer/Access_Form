<?php
session_start();
include('config.php');

$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($form_id <= 0) die("Invalid Form ID");

// Allow creator or public (but show creator notice)
$stmt = $conn->prepare("SELECT * FROM forms WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $form_id);
$stmt->execute();
$form = $stmt->get_result()->fetch_assoc();

if (!$form) die("Form not found");

$q_stmt = $conn->prepare("SELECT * FROM questions WHERE form_id = ? ORDER BY sort_order ASC");
$q_stmt->bind_param("i", $form_id);
$q_stmt->execute();
$questions = $q_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?= htmlspecialchars($form['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <script src="../js/accessibility.js"></script>
    <style>
        .creator-notice { background: #fff3cd; border-left: 5px solid #ffc107; }
        .star { font-size: 2.2rem; cursor: default; color: #ffc107; }
    </style>
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<div class="container py-5">
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'creator'): ?>
        <div class="alert creator-notice mb-4">
            <strong>Creator Preview Mode</strong> - This is how users will see your form.
        </div>
    <?php endif; ?>

    <h1><?= htmlspecialchars($form['title']) ?></h1>
    <p class="text-muted"><?= nl2br(htmlspecialchars($form['description'])) ?></p>

    <form class="mt-4">
        <?php foreach ($questions as $q): 
            $meta = json_decode($q['options'], true) ?? [];
            $options = $meta['options'] ?? [];
        ?>
            <div class="mb-5 p-4 border rounded">
                <label class="form-label fw-bold fs-5">
                    <?= htmlspecialchars($q['question_text']) ?>
                    <?php if ($q['is_required']): ?><span class="text-danger">*</span><?php endif; ?>
                </label>

                <?php if ($q['question_type'] === 'text'): ?>
                    <input type="text" class="form-control" disabled>

                <?php elseif ($q['question_type'] === 'textarea'): ?>
                    <textarea class="form-control" rows="4" disabled></textarea>

                <?php elseif ($q['question_type'] === 'radio' && !empty($options)): ?>
                    <?php foreach ($options as $opt): ?>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" disabled>
                            <label><?= htmlspecialchars($opt) ?></label>
                        </div>
                    <?php endforeach; ?>

                <?php elseif ($q['question_type'] === 'checkbox' && !empty($options)): ?>
                    <?php foreach ($options as $opt): ?>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" disabled>
                            <label><?= htmlspecialchars($opt) ?></label>
                        </div>
                    <?php endforeach; ?>

                <?php elseif ($q['question_type'] === 'dropdown' && !empty($options)): ?>
                    <select class="form-select" disabled>
                        <option>Select...</option>
                        <?php foreach ($options as $opt): ?>
                            <option><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>

                <?php elseif ($q['question_type'] === 'rating'): ?>
                    <div class="d-flex gap-2">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <span class="star">★</span>
                        <?php endfor; ?>
                    </div>

                <?php elseif ($q['question_type'] === 'file'): ?>
                    <input type="file" class="form-control" disabled>
                <?php endif; ?>

                <?php if (!empty($meta['video_url'])): ?>
                    <a href="<?= htmlspecialchars($meta['video_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-3">
                        📹 Sign Language Video
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>