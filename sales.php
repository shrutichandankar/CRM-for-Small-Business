<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$sql = "SELECT s.*, c.name AS customer_name
        FROM sales_tracking s
        LEFT JOIN customers c ON s.customer_id = c.id
        ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Tracking - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Sales Pipeline</h2>
        <a href="add_sale.php" class="btn">+ Add Sale</a>
    </div>

    <table class="crm-table">
        <thead>
            <tr>
                <th>Deal Name</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Stage</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['deal_name']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown'); ?></td>
                <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                <td>
                    <span class="badge <?php echo strtolower($row['stage']); ?>">
                        <?php echo htmlspecialchars($row['stage']); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <a href="edit_sale.php?id=<?php echo $row['id']; ?>" class="btn btn-small">Edit</a>
                    <a href="delete_sale.php?id=<?php echo $row['id']; ?>"
                       class="btn btn-danger btn-small">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
</body>
</html>