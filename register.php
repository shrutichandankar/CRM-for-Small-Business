<?php
/*
    register.php  -->  REGISTRATION PAGE
    -------------------------------------
    Creates a new row in the "users" table.
    Password is hashed before saving.
*/

require_once __DIR__ . '/config.php';

$success = "";

// Per-field errors
$username_error = "";
$email_error    = "";
$password_error = "";
$general_error  = "";

// Form values (shown back in the form)
$username = "";
$email    = "";
$password = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // ---------- Username validation ----------
   if (!preg_match('/^[a-zA-Z_]{8,20}$/', $username)) {
    $username_error = "Username must be 8-20 characters and contain only letters and underscore.";
}

    // ---------- Email validation ----------
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Please enter a valid email address.";
    }
    else {
        $email = strtolower($email);
        list($local_part, $domain) = explode('@', $email, 2);

        if (strlen($email) > 100) {
            $email_error = "Email is too long.";
        }
        // Must start with a letter and contain only letters, numbers, dot, underscore, hyphen
        elseif (!preg_match('/^[a-z][a-z0-9._-]*$/', $local_part)) {
            $email_error = "Email must start with a letter and cannot be only numbers or symbols.";
        }
        // No consecutive dots, and cannot end with a dot
        elseif (strpos($local_part, '..') !== false || substr($local_part, -1) === '.') {
            $email_error = "Email has an invalid format.";
        }
        // Gmail rule: 6 to 30 characters before @gmail.com
        elseif (in_array($domain, ['gmail.com', 'googlemail.com'])
                && (strlen($local_part) < 3 || strlen($local_part) > 30)) {
            $email_error = "Gmail address must be 3 to 30 characters before @.";
        }
        // Domain must actually be able to receive mail (needs internet on the server)
        elseif (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            $email_error = "This email domain does not exist.";
        }
    }

    // ---------- Password validation ----------
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d\s])[^\s]{8,25}$/', $password)) {
        $password_error = "Password must be 8-25 characters and contain uppercase, lowercase, number, and special character.";
    }

    // ---------- Duplicate check (only for fields that are valid so far) ----------
    if ($username_error === "") {
        $safe_username = mysqli_real_escape_string($conn, $username);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$safe_username'");
        if ($check && mysqli_num_rows($check) > 0) {
            $username_error = "This username is already taken.";
        }
    }

    if ($email_error === "") {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$safe_email'");
        if ($check && mysqli_num_rows($check) > 0) {
            $email_error = "This email is already registered.";
        }
    }

    // ---------- Clear ONLY the fields that failed ----------
    if ($username_error !== "") { $username = ""; }
    if ($email_error    !== "") { $email    = ""; }
    if ($password_error !== "") { $password = ""; }

    // ---------- Save if everything is valid ----------
    if ($username_error === "" && $email_error === "" && $password_error === "") {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $safe_username = mysqli_real_escape_string($conn, $username);
        $safe_email    = mysqli_real_escape_string($conn, $email);

        $sql = "INSERT INTO users (username, email, password)
                VALUES ('$safe_username', '$safe_email', '$hashedPassword')";

        if (mysqli_query($conn, $sql)) {
            $success = "Account created successfully! You can login now.";

            // Clear the whole form after success
            $username = "";
            $email    = "";
            $password = "";
        } else {
            $general_error = "Something went wrong: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>

<body>

    <div class="auth-box">
        <h2>Create Account</h2>

        <?php if ($general_error != "") { ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($general_error); ?></div>
        <?php } ?>

        <?php if ($username_error != "") { ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($username_error); ?></div>
        <?php } ?>

        <?php if ($email_error != "") { ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($email_error); ?></div>
        <?php } ?>

        <?php if ($password_error != "") { ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($password_error); ?></div>
        <?php } ?>

        <?php if ($success != "") { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php } ?>

        <form method="POST" action="register.php">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required
                       value="<?php echo htmlspecialchars($username); ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required
                       value="<?php echo htmlspecialchars($email); ?>">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required
                       value="<?php echo htmlspecialchars($password); ?>">
            </div>

            <button type="submit" class="btn" style="width:100%;">
                Register
            </button>

        </form>

        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>

<?php include 'footer.php'; ?>

</body>
</html>