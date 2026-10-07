<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
$error = "";

$stmt = mysqli_prepare($conn, "SELECT * FROM sales_tracking WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sale = mysqli_fetch_assoc($result);

if (!$sale) {
    die("Sale not found.");
}

$customers = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $deal_name = trim($_POST['deal_name'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $stage = $_POST['stage'] ?? '';

    if ($customer_id <= 0) {
        $error = "Please select a customer.";
    } elseif ($deal_name == '') {
        $error = "Deal name is required.";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = "Enter a valid sales amount.";
    } elseif (!in_array($stage, ['Prospecting', 'Proposal', 'Negotiation', 'Won', 'Lost'])) {
        $error = "Invalid sales stage.";
    }

    if ($error == "") {
        $stmt = mysqli_prepare($conn,
            "UPDATE sales_tracking
             SET customer_id = ?, deal_name = ?, amount = ?, stage = ?
             WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "isdsi", $customer_id, $deal_name, $amount, $stage, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: sales.php");
            exit();
        }
        $error = "Unable to update sale.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Sale - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Edit Sale</h2>

    <?php if ($error != "") { ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Customer *</label>
            <select name="customer_id" required>
                <?php while ($customer = mysqli_fetch_assoc($customers)) { ?>
                    <option value="<?php echo $customer['id']; ?>"
                        <?php echo (($customer['id'] == ($_POST['customer_id'] ?? $sale['customer_id'])) ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($customer['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Deal Name *</label>
            <input type="text" name="deal_name" required
                   value="<?php echo htmlspecialchars($_POST['deal_name'] ?? $sale['deal_name']); ?>">
        </div>

        <div class="form-group">
            <label>Amount *</label>
            <input type="number" name="amount" step="0.01" min="0.01" required
                   value="<?php echo htmlspecialchars($_POST['amount'] ?? $sale['amount']); ?>">
        </div>

        <div class="form-group">
            <label>Stage *</label>
            <select name="stage" required>
                <?php
                $stages = ['Prospecting', 'Proposal', 'Negotiation', 'Won', 'Lost'];
                foreach ($stages as $item) {
                    $current = $_POST['stage'] ?? $sale['stage'];
                    $selected = ($current == $item) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($item) . "\" $selected>$item</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn">Update Sale</button>
        <a href="sales.php" class="btn btn-danger">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>