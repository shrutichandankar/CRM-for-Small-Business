<?php
/*
    index.php --> LOGIN PAGE
*/

require_once __DIR__ . '/config.php';

// If already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$username_error = "";   // error for username field
$password_error = "";   // error for password field
$error = "";            // general error (login failed etc.)
$success_message = "";

// Form values
$username = "";
$password = "";

// Logout message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success_message = "Logged out successfully. See you soon!";
}

// If login form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form values
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ---------- Username validation (independent) ----------
    if ($username === '') {
        $username_error = "Please enter your username.";
    }
    elseif (strlen($username) < 8) {
        $username_error = "Username must be at least 8 characters long.";
    }
    elseif (strlen($username) > 20) {
        $username_error = "Username cannot be more than 20 characters.";
    }
    elseif (!preg_match('/[A-Za-z]/', $username)) {
        $username_error = "Username must contain at least one letter.";
    }
    elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
        $username_error = "Username can contain only letters, numbers and underscore.";
    }

    // ---------- Password validation (independent) ----------
    if ($password === '') {
        $password_error = "Please enter your password.";
    }
    elseif (strlen($password) < 8) {
        $password_error = "Password must be at least 8 characters long.";
    }
    elseif (strlen($password) > 25) {
        $password_error = "Password cannot be more than 25 characters.";
    }
    elseif (preg_match('/\s/', $password)) {
        $password_error = "Password cannot contain spaces.";
    }
    elseif (!preg_match('/[A-Z]/', $password)) {
        $password_error = "Password must contain at least one uppercase letter.";
    }
    elseif (!preg_match('/[a-z]/', $password)) {
        $password_error = "Password must contain at least one lowercase letter.";
    }
    elseif (!preg_match('/[0-9]/', $password)) {
        $password_error = "Password must contain at least one number.";
    }

    // Clear ONLY the field(s) that violated validation
    if ($username_error !== "") {
        $username = "";
    }
    if ($password_error !== "") {
        $password = "";
    }

    // ---------- Database check (only if both fields are valid) ----------
    if ($username_error === "" && $password_error === "") {

        // Safely escape username
        $safe_username = mysqli_real_escape_string($conn, $username);

        // Look up user
        $sql = "SELECT * FROM users WHERE username = '$safe_username'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: dashboard.php");
                exit();

            } else {

                $password_error = "Incorrect password. Please try again.";

                // Username is correct, keep it. Clear only password.
                $password = "";
            }

        } else {

            $username_error = "No account found with that username.";

            // Username doesn't exist, clear only username
            $username = "";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Simple CRM</title>

    <link rel="stylesheet" href="style.css?v=1.1">
</head>

<body>

    <div class="auth-box">

        <h2>Business CRM Login</h2>

        <?php if ($success_message != "") { ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php } ?>

        <?php if ($error != "") { ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <?php if ($username_error != "") { ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($username_error); ?>
            </div>
        <?php } ?>

        <?php if ($password_error != "") { ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($password_error); ?>
            </div>
        <?php } ?>

        <form method="POST" action="index.php">

            <div class="form-group">
                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    required
                    autocomplete="username"
                    value="<?php echo htmlspecialchars($username); ?>"
                >
            </div>

            <div class="form-group">
                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    value="<?php echo htmlspecialchars($password); ?>"
                >
            </div>

            <button type="submit" class="btn" style="width:100%;">
                Login
            </button>

        </form>

        <p class="auth-footer">
            Don't have an account?
            <a href="register.php">Register here</a>
        </p>

    </div>

<?php include 'footer.php'; ?>

</body>
</html>