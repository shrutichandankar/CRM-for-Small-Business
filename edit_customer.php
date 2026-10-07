<?php
/*
    edit_customer.php  -->  EDIT AN EXISTING CUSTOMER
    -----------------------------------------------------
    Step 1: Load the customer's current data using the ?id= in the URL.
    Step 2: On form submit, run an UPDATE query.
*/
//require_once 'config.php';
require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

$error = "";

// Get the id from the URL, e.g. edit_customer.php?id=3
$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

// Handle form submission (update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE customers SET
                name='$name', email='$email', phone='$phone',
                company='$company', address='$address'
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: customers.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Fetch existing customer data to pre-fill the form
$result = mysqli_query($conn, "SELECT * FROM customers WHERE id=$id");
$customer = mysqli_fetch_assoc($result);

if (!$customer) {
    die("Customer not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Edit Customer</h2>

        <?php if ($error != "") { ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="edit_customer.php">
            <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">

            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
            </div>
            <div class="form-group">
                <label>Company</label>
                <input type="text" name="company" value="<?php echo htmlspecialchars($customer['company']); ?>">
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3"><?php echo htmlspecialchars($customer['address']); ?></textarea>
            </div>

            <button type="submit" class="btn">Update Customer</button>
            <a href="customers.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>

<?php include 'footer.php'; ?>
</body>
</html>
