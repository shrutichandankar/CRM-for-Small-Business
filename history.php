<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$sql = "SELECT h.*, c.name AS customer_name
        FROM communication_history h
        LEFT JOIN customers c ON h.customer_id = c.id
        ORDER BY h.interaction_date DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Communication History - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Communication History</h2>
        <a href="add_history.php" class="btn">+ Add Interaction</a>
    </div>

    <table class="crm-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Interaction Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['interaction_date']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown'); ?></td>
                <td><span class="badge"><?php echo htmlspecialchars($row['interaction_type']); ?></span></td>
                <td><?php echo htmlspecialchars($row['notes']); ?></td>
                <td>
                    <a href="edit_history.php?id=<?php echo $row['id']; ?>" class="btn btn-small">Edit</a>
                    <a href="delete_history.php?id=<?php echo $row['id']; ?>"
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