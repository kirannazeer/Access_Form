<?php
session_start();
include('config.php');

$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($form_id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid Form ID</div>");
}

// Fetch Form
$stmt = $conn->prepare("SELECT * FROM forms WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $form_id);
$stmt->execute();
$form = $stmt->get_result()->fetch_assoc();

if (!$form) {
    die("<div class='alert alert-danger text-center mt-5'>Form not found or inactive.</div>");
}

// Fetch Questions
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
    <title><?= htmlspecialchars($form['title']) ?> - AccessForm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/accessibility.js"></script>
    
    <style>
        .star { font-size: 2.2rem; cursor: pointer; transition: color 0.2s; }
        .star:hover, .star.active { color: #ffc107; }
        .voice-btn { background: #0d6efd; color: white; }
        .voice-btn.listening { background: #dc3545; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } }
    </style>
</head>
<body class="bg-light">

<?php include('admin_navbar.php'); ?>   

<main class="container py-5" role="main">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h1><?= htmlspecialchars($form['title']) ?></h1>
                    <?php if (!empty($form['description'])): ?>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($form['description'])) ?></p>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <form id="response-form" method="POST" action="submit_response.php" enctype="multipart/form-data">
                        <input type="hidden" name="form_id" value="<?= $form_id ?>">

                        <?php foreach ($questions as $q): 
                            $meta = json_decode($q['options'], true) ?? [];
                            $options = $meta['options'] ?? [];
                        ?>
                            <div class="mb-5 p-4 border rounded question-block">
                                <label class="form-label fw-bold fs-5 mb-3">
                                    <?= htmlspecialchars($q['question_text']) ?>
                                    <?php if ($q['is_required']): ?><span class="text-danger">*</span><?php endif; ?>
                                </label>

                                <button type="button" class="btn btn-sm voice-btn mb-3" onclick="startVoiceInput(<?= $q['id'] ?>)">
                                    🎤 Speak Answer
                                </button>

                                <?php if ($q['question_type'] === 'text'): ?>
                                    <input type="text" name="answers[<?= $q['id'] ?>]" id="ans-<?= $q['id'] ?>" class="form-control" <?= $q['is_required'] ? 'required' : '' ?>>

                                <?php elseif ($q['question_type'] === 'textarea'): ?>
                                    <textarea name="answers[<?= $q['id'] ?>]" id="ans-<?= $q['id'] ?>" class="form-control" rows="4" <?= $q['is_required'] ? 'required' : '' ?>></textarea>

                                <?php elseif (in_array($q['question_type'], ['radio','checkbox','dropdown'])): ?>
                                    <!-- Radio, Checkbox, Dropdown code (same as previous version) -->
                                    <?php /* ... keep your existing code for these types ... */ ?>

                                <?php elseif ($q['question_type'] === 'rating'): ?>
                                    <div class="d-flex gap-2" id="rating-<?= $q['id'] ?>">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star text-warning" data-value="<?= $i ?>" onclick="setRating(<?= $q['id'] ?>, <?= $i ?>)">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="answers[<?= $q['id'] ?>]" id="rating-input-<?= $q['id'] ?>" <?= $q['is_required'] ? 'required' : '' ?>>

                                <?php elseif ($q['question_type'] === 'file'): ?>
                                    <input type="file" name="answers[<?= $q['id'] ?>]" class="form-control" <?= $q['is_required'] ? 'required' : '' ?>>
                                <?php endif; ?>

                                <?php if (!empty($meta['video_url'])): ?>
                                    <a href="<?= htmlspecialchars($meta['video_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        📹 Watch Sign Language Video
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Star Rating & Voice Input functions (same as before)
function setRating(qId, value) { /* ... */ }
function startVoiceInput(qId) { /* ... */ }
</script>

</body>
</html>