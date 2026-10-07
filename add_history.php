<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$error = "";
$customers = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $interaction_type = $_POST['interaction_type'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if ($customer_id <= 0) {
        $error = "Please select a customer.";
    } elseif (!in_array($interaction_type, ['Call', 'Email', 'Meeting'])) {
        $error = "Please select a valid interaction type.";
    } elseif ($notes == '') {
        $error = "Interaction notes are required.";
    }

    if ($error == "") {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO communication_history
             (customer_id, interaction_type, notes)
             VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "iss", $customer_id, $interaction_type, $notes);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: history.php");
            exit();
        }
        $error = "Unable to save communication record.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Communication - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Add Communication</h2>

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
            <label>Interaction Type *</label>
            <select name="interaction_type" required>
                <option value="">Select Type</option>
                <?php foreach (['Call', 'Email', 'Meeting'] as $item) {
                    $selected = (($_POST['interaction_type'] ?? '') == $item) ? 'selected' : '';
                    echo "<option value=\"$item\" $selected>$item</option>";
                } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Notes *</label>
            <textarea name="notes" rows="5" required><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn">Save Interaction</button>
        <a href="history.php" class="btn btn-danger">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>