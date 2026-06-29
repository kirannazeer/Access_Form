<?php
session_start();
include('config.php');

$email = $password = $role = "";
$email_err = $password_err = $role_err = "";
$login_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["role"]))) {
        $role_err = "Please select your role.";
    } else {
        $role = trim($_POST["role"]);

        // Roles updated to match exact database ENUM constraints
        if (!in_array($role, ['Respondent', 'creator'])) {
            $role_err = "Invalid role selected.";
        }
    }

    if (empty($email_err) && empty($password_err) && empty($role_err)) {

        // Query modified to match updated columns layout (removed status check)
        $stmt = $conn->prepare("
            SELECT id, name, email, password, role 
            FROM users 
            WHERE email = ? AND role = ?
            LIMIT 1
        ");

        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {

            $stmt->bind_result($id, $name, $db_email, $hashed_password, $db_role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {

                $_SESSION["user_id"] = $id;
                $_SESSION["user_name"] = $name;
                $_SESSION["user_email"] = $db_email;
                $_SESSION["user_role"] = $db_role;

                // Redirecting based on exact match of the new ENUM values
                if ($db_role == "Respondent") {
                    header("Location: respondent/respondent_dashboard.php");
                    exit();
                } elseif ($db_role == "creator") {
                    header("Location: creator/creator_dashboard.php");
                    exit();
                }

            } else {
                $password_err = "Incorrect password.";
            }

        } else {
            $email_err = "No active account found with this email and role.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/accessibility.js"></script>

</head>

<body class="bg-light">

<?php include('navbar.php'); ?>

<div class="container py-5">

    <div class="card mx-auto shadow-sm border-0" style="max-width: 500px;">
        <div class="card-body p-4">

            <h3 class="text-center mb-2">User Login</h3>
            
            <p class="text-center text-muted mb-4">Login to manage or fill access forms</p>

            <?php echo $login_msg; ?>

            <form method="POST" onsubmit="return validateLogin();">

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo htmlspecialchars($email); ?>"
                        placeholder="name@example.com"
                    >
                    <div class="invalid-feedback">
                        <?php echo $email_err; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                        placeholder="Enter your password"
                    >
                    <div class="invalid-feedback">
                        <?php echo $password_err; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Select Role</label>
                    <select 
                        name="role" 
                        id="role" 
                        class="form-select <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>"
                    >
                        <option value="">Select Role</option>
                        <option value="Respondent" <?php echo ($role == 'Respondent') ? 'selected' : ''; ?>>
                            Respondent (Survey Filler)
                        </option>
                        <option value="creator" <?php echo ($role == 'creator') ? 'selected' : ''; ?>>
                            Form Creator (Admin/Researcher)
                        </option>
                    </select>
                    <div class="invalid-feedback">
                        <?php echo $role_err; ?>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        Login
                    </button>
                </div>

                <p class="text-center mt-3 mb-0">
                    Don't have an account?
                    <a href="register.php">Register here</a>
                </p>

            </form>

        </div>
    </div>

</div>

<script>
function validateLogin() {
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value.trim();
    let role = document.getElementById("role").value;

    if (email === "" || password === "" || role === "") {
        alert("All fields are required.");
        return false;
    }

    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/i;
    if (!email.match(emailPattern)) {
        alert("Invalid email format.");
        return false;
    }

    if (role !== "Respondent" && role !== "creator") {
        alert("Invalid role selected.");
        return false;
    }

    return true;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>