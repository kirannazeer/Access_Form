<?php
session_start();
include('config.php');

// Guard: Only creators allowed
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== 'creator') {
    header("Location: login.php");
    exit();
}

$creator_id = $_SESSION["user_id"];
$message = "";

// Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title) && isset($_POST['questions'])) {
        $stmt = $conn->prepare("INSERT INTO forms (user_id, title, description, status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("iss", $creator_id, $title, $description);
        
        if ($stmt->execute()) {
            $form_id = $conn->insert_id;
            
            foreach ($_POST['questions'] as $index => $q) {
                $q_text = trim($q['text']);
                $q_type = $q['type'];
                $is_req = isset($q['required']) ? 1 : 0;
                
                $meta = [
                    'options'      => !empty($q['options']) ? explode(',', trim($q['options'])) : [],
                    'alt_text'     => trim($q['alt_text'] ?? ''),
                    'video_url'    => trim($q['video_url'] ?? ''),
                    'video_caption'=> trim($q['video_caption'] ?? '')
                ];
                
                $options_json = json_encode($meta);
                $sort_order = $index + 1;

                $q_stmt = $conn->prepare("INSERT INTO questions (form_id, question_text, question_type, is_required, options, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                $q_stmt->bind_param("issisi", $form_id, $q_text, $q_type, $is_req, $options_json, $sort_order);
                $q_stmt->execute();
                $q_stmt->close();
            }
            
            $message = "<div class='alert alert-success'>Accessible Form created successfully! <a href='creator_dashboard.php'>Go to Dashboard</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>Database error occurred.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Title and at least one question are required.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Accessible Form - AccessForm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- SortableJS for Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="../js/accessibility.js"></script>
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<main class="container py-5" id="main-content" role="main" aria-labelledby="page-title">
    <div class="max-width-md mx-auto" style="max-width: 900px;">
        
        <h1 id="page-title" class="h3 mb-4">Accessible Survey & Form Builder</h1>
        <?php echo $message; ?>

        <form method="POST" id="form-builder">
            
            <!-- Form Details -->
            <div class="card p-4 border-0 shadow-sm mb-4" role="region" aria-label="Form Details">
                <div class="mb-3">
                    <label for="form-title" class="form-label fw-bold">Form Title <span class="text-danger">*</span></label>
                    <input type="text" id="form-title" name="title" class="form-control" required aria-required="true">
                </div>
                <div class="mb-3">
                    <label for="form-desc" class="form-label fw-bold">Form Description / Instructions</label>
                    <textarea id="form-desc" name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <!-- Questions Container -->
            <div id="questions-container" class="mb-4" role="list" aria-label="Form Questions"></div>

            <!-- Add Question Section -->
            <div class="card p-4 border-dashed border-2 text-center bg-white mb-4">
                <label for="question-type-select" class="form-label fw-bold d-block text-start mb-2">
                    Select Question Type:
                </label>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <select id="question-type-select" class="form-select">
                        <option value="text">Text Input (Short Answer)</option>
                        <option value="textarea">Paragraph (Long Answer)</option>
                        <option value="radio">Multiple Choice (Radio)</option>
                        <option value="checkbox">Checkboxes (Multiple Select)</option>
                        <option value="dropdown">Dropdown Menu</option>
                        <option value="rating">Rating Scale (1-5 Stars)</option>
                        <option value="file">File Upload</option>
                    </select>
                    <button type="button" onclick="addQuestionItem()" class="btn btn-primary px-4">
                        + Add Question
                    </button>
                </div>
            </div>

            <div class="text-end">
                <a href="creator_dashboard.php" class="btn btn-light border px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-success px-5 fw-bold">Save & Publish Form</button>
            </div>
        </form>
    </div>
</main>

<script>
// Drag & Drop + Question Management
let questionCounter = 0;

function addQuestionItem() {
    questionCounter++;
    const container = document.getElementById('questions-container');
    const type = document.getElementById('question-type-select').value;
    const typeText = document.getElementById('question-type-select').options[document.getElementById('question-type-select').selectedIndex].text;

    const qCard = document.createElement('div');
    qCard.className = 'card p-4 border-0 shadow-sm mb-4 question-node';
    qCard.setAttribute('role', 'listitem');
    qCard.setAttribute('id', `q-${questionCounter}`);
    qCard.draggable = true;

    let extraHTML = '';

    if (['radio', 'checkbox', 'dropdown'].includes(type)) {
        extraHTML = `
            <div class="mb-3">
                <label class="form-label small fw-bold">Options (Comma Separated)</label>
                <input type="text" name="questions[${questionCounter}][options]" class="form-control" placeholder="Option 1, Option 2, Option 3">
            </div>`;
    }

    if (type === 'rating') {
        extraHTML = `<div class="alert alert-info small">1 to 5 Star Rating will be shown to users.</div>`;
    }

    if (type === 'file') {
        extraHTML = `<div class="alert alert-info small">Users can upload files (PDF, Image, Document).</div>`;
    }

    qCard.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary">${typeText}</span>
            <button type="button" onclick="this.closest('.question-node').remove()" class="btn btn-sm btn-outline-danger" aria-label="Remove Question">Remove</button>
        </div>

        <input type="hidden" name="questions[${questionCounter}][type]" value="${type}">

        <div class="mb-3">
            <label class="form-label fw-bold">Question Text</label>
            <input type="text" name="questions[${questionCounter}][text]" class="form-control" required>
        </div>

        ${extraHTML}

        <!-- Accessibility Settings -->
        <div class="accordion mt-3">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#acc-${questionCounter}">
                        ⚙️ Accessibility Settings
                    </button>
                </h2>
                <div id="acc-${questionCounter}" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="mb-2">
                            <label class="form-label small">Alt Text (for image/video)</label>
                            <input type="text" name="questions[${questionCounter}][alt_text]" class="form-control form-control-sm">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">Video URL (Sign Language)</label>
                                <input type="url" name="questions[${questionCounter}][video_url]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Video Caption/Transcript</label>
                                <input type="text" name="questions[${questionCounter}][video_caption]" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="questions[${questionCounter}][required]" value="1">
                            <label class="form-check-label">Required Field</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.appendChild(qCard);
    
    // Auto focus
    setTimeout(() => {
        qCard.querySelector('input[type="text"]').focus();
    }, 100);
}

// Initialize Sortable (Drag & Drop)
document.addEventListener("DOMContentLoaded", function() {
    addQuestionItem(); // Default one question

    new Sortable(document.getElementById('questions-container'), {
        animation: 150,
        ghostClass: 'bg-warning',
        handle: '.card',
        onEnd: function() {
            // Optional: re-number if needed
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>