<?php
/*
    add_customer.php --> ADD NEW CUSTOMER
    ------------------------------------------
    Shows a form, and on submit, inserts a new row into "customers".
    Every field is validated separately. Only the field that fails
    is cleared; valid fields keep their value.
*/

require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

/*
    Returns true if the number contains a run of $len consecutive
    ascending or descending digits (e.g. 123456, 987654).
*/
function has_digit_sequence($number, $len = 6) {
    $asc  = "0123456789";
    $desc = "9876543210";

    for ($i = 0; $i <= strlen($number) - $len; $i++) {
        $chunk = substr($number, $i, $len);
        if (strpos($asc, $chunk) !== false || strpos($desc, $chunk) !== false) {
            return true;
        }
    }
    return false;
}

// Per-field errors
$errors = [
    'name'    => '',
    'email'   => '',
    'phone'   => '',
    'company' => '',
    'address' => ''
];
$general_error = "";

// Form values
$name = $email = $phone = $company = $address = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ---------- Get and clean values ----------
    $name    = preg_replace('/\s+/', ' ', trim($_POST['name'] ?? ''));
    $email   = strtolower(trim($_POST['email'] ?? ''));
    $phone   = trim($_POST['phone'] ?? '');
    $company = preg_replace('/\s+/', ' ', trim($_POST['company'] ?? ''));
    $address = preg_replace('/[ \t]+/', ' ', trim($_POST['address'] ?? ''));

    // ---------- NAME ----------
    if ($name === '') {
        $errors['name'] = "Customer name is required.";
    }
    elseif (strlen($name) < 2 || strlen($name) > 50) {
        $errors['name'] = "Name must be between 2 and 50 characters.";
    }
    elseif (!preg_match("/^[A-Za-z][A-Za-z .'-]*$/", $name)) {
        $errors['name'] = "Name must start with a letter and contain only letters, spaces, dots, apostrophes or hyphens.";
    }
    elseif (preg_match_all('/[A-Za-z]/', $name) < 2) {
        $errors['name'] = "Name must contain at least 2 letters.";
    }
    elseif (preg_match('/(.)\1{3,}/i', $name)) {
        $errors['name'] = "Enter a valid name. A letter cannot repeat 4 or more times in a row.";
    }

    // ---------- EMAIL ----------
    if ($email === '') {
        $errors['email'] = "Email address is required.";
    }
    elseif (strlen($email) > 100) {
        $errors['email'] = "Email cannot be more than 100 characters.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email address.";
    }
    else {
        list($local_part, $domain) = explode('@', $email, 2);

        if (strlen($local_part) < 3) {
            $errors['email'] = "Email must have at least 3 characters before @.";
        }
        elseif (!preg_match('/^[a-z][a-z0-9._-]*$/', $local_part)) {
            $errors['email'] = "Email must start with a letter and cannot be only numbers or symbols.";
        }
        elseif (strpos($local_part, '..') !== false || substr($local_part, -1) === '.') {
            $errors['email'] = "Email has an invalid format.";
        }
        elseif (in_array($domain, ['gmail.com', 'googlemail.com'])
                && (strlen($local_part) < 6 || strlen($local_part) > 30)) {
            $errors['email'] = "Gmail address must be 6 to 30 characters before @.";
        }
        // Domain must be able to receive mail (needs internet on the server)
        elseif (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            $errors['email'] = "This email domain does not exist.";
        }
    }

    // ---------- PHONE ----------
    if ($phone === '') {
        $errors['phone'] = "Phone number is required.";
    }
    elseif (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
        $errors['phone'] = "Enter a valid 10-digit mobile number starting with 6, 7, 8 or 9.";
    }
    elseif (preg_match('/(\d)\1{4,}/', $phone)) {
        $errors['phone'] = "Enter a valid mobile number. A digit cannot repeat 5 or more times in a row.";
    }
    elseif (count(array_unique(str_split($phone))) < 4) {
        $errors['phone'] = "Enter a valid mobile number. It has too few different digits.";
    }
    elseif (has_digit_sequence($phone, 6)) {
        $errors['phone'] = "Enter a valid mobile number. Sequential digits like 123456 are not allowed.";
    }

    // ---------- COMPANY (optional) ----------
    if ($company !== '') {
        if (strlen($company) < 2 || strlen($company) > 100) {
            $errors['company'] = "Company name must be between 2 and 100 characters.";
        }
        elseif (!preg_match("/^[A-Za-z0-9 &.,'()-]+$/", $company)) {
            $errors['company'] = "Company name contains invalid characters.";
        }
        elseif (!preg_match('/[A-Za-z]/', $company)) {
            $errors['company'] = "Company name must contain at least one letter.";
        }
    }

    // ---------- ADDRESS (optional) ----------
    if ($address !== '') {
        if (strlen($address) < 5 || strlen($address) > 255) {
            $errors['address'] = "Address must be between 5 and 255 characters.";
        }
        elseif (!preg_match("/^[A-Za-z0-9 \r\n,.\/#&'()-]+$/", $address)) {
            $errors['address'] = "Address contains invalid characters.";
        }
        elseif (!preg_match('/[A-Za-z]/', $address)) {
            $errors['address'] = "Address must contain at least one letter.";
        }
    }

    // ---------- DUPLICATE CHECKS (only for fields that are valid so far) ----------
    if ($errors['email'] === '') {
        $stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['email'] = "A customer with this email already exists.";
        }
        mysqli_stmt_close($stmt);
    }

    if ($errors['phone'] === '') {
        $stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE phone = ?");
        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['phone'] = "A customer with this phone number already exists.";
        }
        mysqli_stmt_close($stmt);
    }

    // ---------- Clear ONLY the fields that failed ----------
    if ($errors['name']    !== '') { $name    = ''; }
    if ($errors['email']   !== '') { $email   = ''; }
    if ($errors['phone']   !== '') { $phone   = ''; }
    if ($errors['company'] !== '') { $company = ''; }
    if ($errors['address'] !== '') { $address = ''; }

    // ---------- Insert if everything is valid ----------
    if (implode('', $errors) === '') {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO customers (name, email, phone, company, address) VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $company, $address);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: customers.php");
            exit();
        }
        else {
            $general_error = "Error: " . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container">

    <h2>Add New Customer</h2>

    <?php if ($general_error != "") { ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($general_error); ?>
        </div>
    <?php } ?>

    <form method="POST" action="add_customer.php">

        <div class="form-group">
            <label>Full Name <span style="color:red;">*</span></label>
            <input
                type="text"
                name="name"
                required
                minlength="2"
                maxlength="50"
                value="<?php echo htmlspecialchars($name); ?>"
            >
            <?php if ($errors['name'] != "") { ?>
                <small style="color:red; display:block;"><?php echo htmlspecialchars($errors['name']); ?></small>
            <?php } ?>
        </div>

        <div class="form-group">
            <label>Email <span style="color:red;">*</span></label>
            <input
                type="email"
                name="email"
                required
                maxlength="100"
                value="<?php echo htmlspecialchars($email); ?>"
            >
            <?php if ($errors['email'] != "") { ?>
                <small style="color:red; display:block;"><?php echo htmlspecialchars($errors['email']); ?></small>
            <?php } ?>
        </div>

        <div class="form-group">
            <label>Phone Number <span style="color:red;">*</span></label>
            <input
                type="tel"
                name="phone"
                required
                maxlength="10"
                pattern="[6-9][0-9]{9}"
                inputmode="numeric"
                title="10-digit mobile number starting with 6, 7, 8 or 9"
                value="<?php echo htmlspecialchars($phone); ?>"
            >
            <?php if ($errors['phone'] != "") { ?>
                <small style="color:red; display:block;"><?php echo htmlspecialchars($errors['phone']); ?></small>
            <?php } ?>
        </div>

        <div class="form-group">
            <label>Company</label>
            <input
                type="text"
                name="company"
                maxlength="100"
                value="<?php echo htmlspecialchars($company); ?>"
            >
            <?php if ($errors['company'] != "") { ?>
                <small style="color:red; display:block;"><?php echo htmlspecialchars($errors['company']); ?></small>
            <?php } ?>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea
                name="address"
                rows="3"
                maxlength="255"><?php echo htmlspecialchars($address); ?></textarea>
            <?php if ($errors['address'] != "") { ?>
                <small style="color:red; display:block;"><?php echo htmlspecialchars($errors['address']); ?></small>
            <?php } ?>
        </div>

        <button type="submit" class="btn">Save Customer</button>

        <a href="customers.php" class="btn btn-danger">Cancel</a>

    </form>

</div>

<?php include 'footer.php'; ?>

</body>
</html>