<?php
include('config.php');

$errors = [];
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $phone    = trim($_POST["phone"]);
    $role     = isset($_POST["role"]) ? trim($_POST["role"]) : ""; 

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }

    // Validate dynamic role against allowed ENUM values
    if (!in_array($role, ['Respondent', 'creator'])) {
        $errors[] = "Please select a valid role.";
    }

    // Checking duplicates only for Email and Phone now (vu_id removed)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email or phone number already exists.";
    }

    $stmt->close();

    if (empty($errors)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SQL Query matches exact layout architecture of the target schema
        $stmt = $conn->prepare("
            INSERT INTO users (name, email, password, role, phone)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssss",
            $name,
            $email,
            $hashed_password,
            $role,
            $phone
        );

        if ($stmt->execute()) {
            $success_msg = "Registration successful as " . htmlspecialchars($role) . ".";
        } else {
            $errors[] = "Database insertion error. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/accessibility.js"></script>
</head>

<body class="bg-light">

<?php include('navbar.php'); ?>

<div class="container py-5">

    <div class="card mx-auto shadow-sm border-0" style="max-width: 700px;">
        <div class="card-body p-4">

            <h3 class="text-center mb-2">Create Account</h3>
            <p class="text-center text-muted mb-4">Register your account details below</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validateForm();">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="e.g. +923001234567" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Select Role</label>
                        <select name="role" id="role" class="form-select">
                            <option value="" disabled <?php echo !isset($role) || $role == '' ? 'selected' : ''; ?>>Choose a role...</option>
                            <option value="Respondent" <?php echo isset($role) && $role == 'Respondent' ? 'selected' : ''; ?>>Respondent</option>
                            <option value="creator" <?php echo isset($role) && $role == 'creator' ? 'selected' : ''; ?>>Creator</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 6 characters">
                    </div>

                    <div class="col-md-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            Register Account
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </div>

</div>

<script>
function validateForm() {
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let role = document.getElementById("role").value;
    let password = document.getElementById("password").value.trim();

    if (name === "" || email === "" || phone === "" || password === "" || role === "") {
        alert("All fields are required.");
        return false;
    }

    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if (!email.match(emailPattern)) {
        alert("Invalid email format.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }

    return true;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>