<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$sql = "SELECT t.*, c.name AS customer_name
        FROM support_tickets t
        LEFT JOIN customers c ON t.customer_id = c.id
        ORDER BY t.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support Desk - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=2.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Support Tickets</h2>
        <a href="add_support.php" class="btn">+ Add Ticket</a>
    </div>

    <table class="crm-table">
        <thead>
            <tr>
                <th>Ticket Subject</th>
                <th>Customer</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) {
            $ticket_status_class = strtolower(str_replace(' ', '-', $row['status']));
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown'); ?></td>
                <td>
                    <span class="badge prio-<?php echo strtolower($row['priority']); ?>">
                        <?php echo htmlspecialchars($row['priority']); ?>
                    </span>
                </td>
                <td>
                    <span class="badge status-<?php echo $ticket_status_class; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <a href="edit_support.php?id=<?php echo $row['id']; ?>" class="btn btn-small">Edit</a>
                    <a href="delete_support.php?id=<?php echo $row['id']; ?>"
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