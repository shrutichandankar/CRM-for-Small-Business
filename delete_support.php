<?php
/*
    delete_support.php  -->  DELETE WITH CONFIRMATION PAGE
    Opening the page only SHOWS the record. The row is deleted
    only when the confirmation form is submitted (POST).
*/

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

// ---------- Step 2: confirmed -> delete ----------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM support_tickets WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: support.php");
    exit();
}

// ---------- Step 1: show the record and ask for confirmation ----------
$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn,
    "SELECT t.*, c.name AS customer_name
     FROM support_tickets t
     LEFT JOIN customers c ON t.customer_id = c.id
     WHERE t.id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: support.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Support Ticket - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

    <h2>Delete Support Ticket</h2>

    <div class="alert alert-error">
        Are you sure you want to delete this record? This cannot be undone.
    </div>

    <table>
        <tr><th>Customer</th><td><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown'); ?></td></tr>
        <tr><th>Subject</th><td><?php echo htmlspecialchars($row['subject']); ?></td></tr>
        <tr><th>Priority</th><td><?php echo htmlspecialchars($row['priority']); ?></td></tr>
        <tr><th>Status</th><td><?php echo htmlspecialchars($row['status']); ?></td></tr>
    </table>

    <br>

    <form method="POST" action="delete_support.php">
        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a href="support.php" class="btn">Cancel</a>
    </form>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
