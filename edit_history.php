<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
$error = "";

$stmt = mysqli_prepare($conn, "SELECT * FROM communication_history WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$history = mysqli_fetch_assoc($result);

if (!$history) {
    die("Communication record not found.");
}

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
            "UPDATE communication_history
             SET customer_id = ?, interaction_type = ?, notes = ?
             WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "issi", $customer_id, $interaction_type, $notes, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: history.php");
            exit();
        }
        $error = "Unable to update communication record.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Communication - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Edit Communication</h2>

    <?php if ($error != "") { ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Customer *</label>
            <select name="customer_id" required>
                <?php while ($customer = mysqli_fetch_assoc($customers)) { ?>
                    <option value="<?php echo $customer['id']; ?>"
                        <?php echo (($customer['id'] == ($_POST['customer_id'] ?? $history['customer_id'])) ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($customer['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Interaction Type *</label>
            <select name="interaction_type" required>
                <?php foreach (['Call', 'Email', 'Meeting'] as $item) {
                    $current = $_POST['interaction_type'] ?? $history['interaction_type'];
                    $selected = ($current == $item) ? 'selected' : '';
                    echo "<option value=\"$item\" $selected>$item</option>";
                } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Notes *</label>
            <textarea name="notes" rows="5" required><?php echo htmlspecialchars($_POST['notes'] ?? $history['notes']); ?></textarea>
        </div>

        <button type="submit" class="btn">Update Interaction</button>
        <a href="history.php" class="btn btn-danger">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>