<?php
/*
    customers.php  -->  LIST ALL CUSTOMERS
    -----------------------------------------
    Fetches every row from the "customers" table and displays it
    in a table, with links to view/edit/delete each one.
*/

require_once __DIR__ . '/config.php';
require_once 'auth_check.php';


$result = mysqli_query($conn , "SELECT * FROM customers ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Customers</h2>

        <div class="top-actions">
            <a href="add_customer.php" class="btn">+ Add New Customer</a>
        </div>

        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                    <td>
                        <a href="view_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-small">View</a>
                        <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-small">Edit</a>
                        <a href="delete_customer.php?id=<?php echo $row['id']; ?>"
                           class="btn btn-small btn-danger">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

<?php include 'footer.php'; ?>
</body>
</html>