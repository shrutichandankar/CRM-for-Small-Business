<?php
/*
    view_customer.php  -->  VIEW ONE CUSTOMER'S DETAILS + THEIR LEADS
    ---------------------------------------------------------------------
*/
//require_once 'config.php';
require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

//$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

$id = intval($_GET['id'] ?? 0);

$result = mysqli_query($conn, "SELECT * FROM customers WHERE id=$id");
$customer = mysqli_fetch_assoc($result);

if (!$customer) {
    die("Customer not found.");
}

// Get all leads belonging to this customer
$leads = mysqli_query($conn, "SELECT * FROM leads WHERE customer_id=$id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Customer - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2><?php echo htmlspecialchars($customer['name']); ?></h2>

        <table>
            <tr><th>Email</th><td><?php echo htmlspecialchars($customer['email']); ?></td></tr>
            <tr><th>Phone</th><td><?php echo htmlspecialchars($customer['phone']); ?></td></tr>
            <tr><th>Company</th><td><?php echo htmlspecialchars($customer['company']); ?></td></tr>
            <tr><th>Address</th><td><?php echo htmlspecialchars($customer['address']); ?></td></tr>
            <tr><th>Customer Since</th><td><?php echo $customer['created_at']; ?></td></tr>
        </table>

        <div class="top-actions" style="margin-top:20px;">
            <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="btn">Edit</a>
            <a href="customers.php" class="btn btn-danger">Back to List</a>
        </div>

        <h2 style="margin-top:30px;">Leads for this Customer</h2>

        <table>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Created</th>
            </tr>
            <?php while ($lead = mysqli_fetch_assoc($leads)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($lead['title']); ?></td>
                    <td>
                        <?php
                            $badgeClass = "badge-new";
                            if ($lead['status'] == 'In Progress') $badgeClass = "badge-progress";
                            if ($lead['status'] == 'Won') $badgeClass = "badge-won";
                            if ($lead['status'] == 'Lost') $badgeClass = "badge-lost";
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $lead['status']; ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($lead['notes']); ?></td>
                    <td><?php echo $lead['created_at']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

<?php include 'footer.php'; ?>
</body>
</html>