<?php
/*
    delete_customer.php  -->  DELETE A CUSTOMER (with confirmation page)
    -------------------------------------------------------------------
    Since we are not using JavaScript, we cannot use a JS "confirm()" popup.
    Instead: visiting this page with ?id=X shows a confirmation form.
    Only when that form is POSTed do we actually delete the row.
*/

require_once __DIR__ . '/config.php';
require_once 'auth_check.php';



// Step 1: User clicked "Delete" link -> confirm via POST button
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    mysqli_query($conn, "DELETE FROM customers WHERE id=$id");
    header("Location: customers.php");
    exit();
}

// Step 2: Show confirmation page
$id = intval($_GET['id'] ?? 0);
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
    <title>Delete Customer - Simple CRM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Delete Customer</h2>

        <div class="alert alert-error">
            Are you sure you want to delete
            <strong><?php echo htmlspecialchars($customer['name']); ?></strong>?
            This will also delete all their leads, sales, support tickets and communication history. This cannot be undone.
        </div>

        <form method="POST" action="delete_customer.php">
            <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
            <a href="customers.php" class="btn">Cancel</a>
        </form>
    </div>

<?php include 'footer.php'; ?>
</body>
</html>
