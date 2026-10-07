<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$error = "";
$customers = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $status = $_POST['status'] ?? 'Open';
    $priority = $_POST['priority'] ?? 'Medium';

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
            "INSERT INTO support_tickets
             (customer_id, subject, message, status, priority)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "issss", $customer_id, $subject, $message, $status, $priority);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: support.php");
            exit();
        }
        $error = "Unable to save ticket.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Support Ticket - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=2.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Add Support Ticket</h2>

    <?php if ($error != "") { ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Customer *</label>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php while ($customer = mysqli_fetch_assoc($customers)) { ?>
                    <option value="<?php echo $customer['id']; ?>"
                        <?php echo (isset($_POST['customer_id']) && $_POST['customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($customer['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Subject *</label>
            <input type="text" name="subject" required
                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Message *</label>
            <textarea name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Priority *</label>
            <select name="priority" required>
                <?php foreach (['Low', 'Medium', 'High'] as $item) {
                    $selected = (($_POST['priority'] ?? 'Medium') == $item) ? 'selected' : '';
                    echo "<option value=\"$item\" $selected>$item</option>";
                } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Status *</label>
            <select name="status" required>
                <?php foreach (['Open', 'In Progress', 'Closed'] as $item) {
                    $selected = (($_POST['status'] ?? 'Open') == $item) ? 'selected' : '';
                    echo "<option value=\"$item\" $selected>$item</option>";
                } ?>
            </select>
        </div>

        <button type="submit" class="btn">Save Ticket</button>
        <a href="support.php" class="btn btn-danger">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>