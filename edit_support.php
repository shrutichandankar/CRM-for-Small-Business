<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
$error = "";

$stmt = mysqli_prepare($conn, "SELECT * FROM support_tickets WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$ticket = mysqli_fetch_assoc($result);

if (!$ticket) {
    die("Support ticket not found.");
}

$customers = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $status = $_POST['status'] ?? '';
    $priority = $_POST['priority'] ?? '';

    if ($customer_id <= 0) {
        $error = "Please select a customer.";
    } elseif ($subject == '') {
        $error = "Ticket subject is required.";
    } elseif ($message == '') {
        $error = "Ticket message is required.";
    } elseif (!in_array($status, ['Open', 'In Progress', 'Closed'])) {
        $error = "Invalid ticket status.";
    } elseif (!in_array($priority, ['Low', 'Medium', 'High'])) {
        $error = "Invalid ticket priority.";
    }

    if ($error == "") {
        $stmt = mysqli_prepare($conn,
            "UPDATE support_tickets
             SET customer_id = ?, subject = ?, message = ?, status = ?, priority = ?
             WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "issssi", $customer_id, $subject, $message, $status, $priority, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: support.php");
            exit();
        }
        $error = "Unable to update ticket.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Support Ticket - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=2.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Edit Support Ticket</h2>

    <?php if ($error != "") { ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Customer *</label>
            <select name="customer_id" required>
                <?php while ($customer = mysqli_fetch_assoc($customers)) { ?>
                    <option value="<?php echo $customer['id']; ?>"
                        <?php echo (($customer['id'] == ($_POST['customer_id'] ?? $ticket['customer_id'])) ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($customer['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Subject *</label>
            <input type="text" name="subject" required
                   value="<?php echo htmlspecialchars($_POST['subject'] ?? $ticket['subject']); ?>">
        </div>

        <div class="form-group">
            <label>Message *</label>
            <textarea name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? $ticket['message']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Priority *</label>
            <select name="priority" required>
                <?php foreach (['Low', 'Medium', 'High'] as $item) {
                    $current = $_POST['priority'] ?? $ticket['priority'];
                    $selected = ($current == $item) ? 'selected' : '';
                    echo "<option value=\"$item\" $selected>$item</option>";
                } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Status *</label>
            <select name="status" required>
                <?php foreach (['Open', 'In Progress', 'Closed'] as $item) {
                    $current = $_POST['status'] ?? $ticket['status'];
                    $selected = ($current == $item) ? 'selected' : '';
                    echo "<option value=\"$item\" $selected>$item</option>";
                } ?>
            </select>
        </div>

        <button type="submit" class="btn">Update Ticket</button>
        <a href="support.php" class="btn btn-danger">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>