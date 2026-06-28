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
    <script src="./accessibility.js"></script>
    
    <style>
        .star { 
            font-size: 2.2rem; 
            cursor: pointer; 
            transition: color 0.2s; 
        }
        .star:hover, .star.active { 
            color: #ffc107; 
        }
        .voice-btn { 
            background: #0d6efd; 
            color: white; 
        }
        .voice-btn.listening {
            background: #dc3545;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<main class="container py-5" role="main" aria-labelledby="form-title">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h1 id="form-title"><?= htmlspecialchars($form['title']) ?></h1>
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
                            <div class="mb-5 p-4 border rounded question-block" role="group" aria-labelledby="q-<?= $q['id'] ?>">
                                
                                <label id="q-<?= $q['id'] ?>" class="form-label fw-bold fs-5 mb-3">
                                    <?= htmlspecialchars($q['question_text']) ?>
                                    <?php if ($q['is_required']): ?>
                                        <span class="text-danger" aria-hidden="true">*</span>
                                    <?php endif; ?>
                                </label>

                                <!-- Voice Input Button -->
                                <button type="button" class="btn btn-sm voice-btn mb-3" 
                                        onclick="startVoiceInput(<?= $q['id'] ?>)">
                                    🎤 Speak Answer
                                </button>

                                <?php if ($q['question_type'] === 'text'): ?>
                                    <input type="text" name="answers[<?= $q['id'] ?>]" id="ans-<?= $q['id'] ?>" 
                                           class="form-control" <?= $q['is_required'] ? 'required' : '' ?>>

                                <?php elseif ($q['question_type'] === 'textarea'): ?>
                                    <textarea name="answers[<?= $q['id'] ?>]" id="ans-<?= $q['id'] ?>" 
                                              class="form-control" rows="4" <?= $q['is_required'] ? 'required' : '' ?>></textarea>

                                <?php elseif ($q['question_type'] === 'radio' && !empty($options)): ?>
                                    <?php foreach ($options as $opt): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" 
                                                   name="answers[<?= $q['id'] ?>]" 
                                                   value="<?= htmlspecialchars($opt) ?>" 
                                                   id="r-<?= $q['id'] ?>-<?= md5($opt) ?>"
                                                   <?= $q['is_required'] ? 'required' : '' ?>>
                                            <label class="form-check-label" for="r-<?= $q['id'] ?>-<?= md5($opt) ?>">
                                                <?= htmlspecialchars($opt) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                <?php elseif ($q['question_type'] === 'checkbox' && !empty($options)): ?>
                                    <?php foreach ($options as $opt): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="answers[<?= $q['id'] ?>][]" 
                                                   value="<?= htmlspecialchars($opt) ?>" 
                                                   id="c-<?= $q['id'] ?>-<?= md5($opt) ?>">
                                            <label class="form-check-label" for="c-<?= $q['id'] ?>-<?= md5($opt) ?>">
                                                <?= htmlspecialchars($opt) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                <?php elseif ($q['question_type'] === 'dropdown' && !empty($options)): ?>
                                    <select name="answers[<?= $q['id'] ?>]" class="form-select" <?= $q['is_required'] ? 'required' : '' ?>>
                                        <option value="">-- Select Option --</option>
                                        <?php foreach ($options as $opt): ?>
                                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif ($q['question_type'] === 'rating'): ?>
                                    <div class="d-flex gap-2" id="rating-<?= $q['id'] ?>">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star text-warning" data-value="<?= $i ?>" 
                                                  onclick="setRating(<?= $q['id'] ?>, <?= $i ?>)">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="answers[<?= $q['id'] ?>]" id="rating-input-<?= $q['id'] ?>" <?= $q['is_required'] ? 'required' : '' ?>>

                                <?php elseif ($q['question_type'] === 'file'): ?>
                                    <input type="file" name="answers[<?= $q['id'] ?>]" class="form-control" 
                                           <?= $q['is_required'] ? 'required' : '' ?>>
                                    <small class="text-muted">Max 5MB (PDF, JPG, PNG, DOCX)</small>
                                <?php endif; ?>

                                <!-- Accessibility Media -->
                                <?php if (!empty($meta['alt_text'])): ?>
                                    <p class="small text-muted mt-2"><?= htmlspecialchars($meta['alt_text']) ?></p>
                                <?php endif; ?>

                                <?php if (!empty($meta['video_url'])): ?>
                                    <div class="mt-3">
                                        <a href="<?= htmlspecialchars($meta['video_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            📹 Watch Sign Language Video
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">Submit Response</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SMS Info -->
            <div class="mt-4 text-center text-muted small">
                <strong>No Internet?</strong> Send SMS to <strong>+92-333-33333</strong><br>
                Format: <code>FORM <?= $form_id ?> Q1: Answer here Q2: Answer here</code>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ==================== STAR RATING ====================
function setRating(questionId, value) {
    const stars = document.querySelectorAll(`#rating-${questionId} .star`);
    const input = document.getElementById(`rating-input-${questionId}`);
    input.value = value;

    stars.forEach(star => {
        if (parseInt(star.dataset.value) <= value) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

// ==================== VOICE RECOGNITION ====================
let recognition = null;

function startVoiceInput(questionId) {
    const textarea = document.getElementById(`ans-${questionId}`);
    const button = event.currentTarget;

    if (!('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
        alert("❌ Voice recognition not supported in this browser.\nPlease use Google Chrome.");
        return;
    }

    recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'en-US';        // Change to 'ur-PK' for Urdu
    recognition.interimResults = false;

    button.classList.add('listening');
    button.innerHTML = '🎤 Listening...';

    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript;
        if (textarea) {
            textarea.value = transcript;
            textarea.focus();
        }
        resetButton(button);
    };

    recognition.onerror = function(event) {
        let msg = "Voice recognition error. Please try again.";
        if (event.error === 'no-speech') msg = "No speech detected. Please speak clearly.";
        if (event.error === 'not-allowed') msg = "Microphone access denied. Please allow permission.";
        if (event.error === 'audio-capture') msg = "No microphone found.";
        
        alert("❌ " + msg);
        resetButton(button);
    };

    recognition.onend = function() {
        resetButton(button);
    };

    try {
        recognition.start();
    } catch (e) {
        alert("Failed to start voice input.");
        resetButton(button);
    }
}

function resetButton(btn) {
    btn.classList.remove('listening');
    btn.innerHTML = '🎤 Speak Answer';
}
</script>

</body>
</html>