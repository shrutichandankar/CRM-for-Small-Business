<?php
/*
    add_lead.php  -->  ADD A NEW LEAD
    -------------------------------------
    Shows a form with a dropdown of existing customers.
    On submit, inserts a new row into "leads".
*/

require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

$error = "";



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer_id = intval($_POST['customer_id']);
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);
    $notes       = mysqli_real_escape_string($conn, $_POST['notes']);

    if ($customer_id == 0 || $title == "") {
        $error = "Please choose a customer and enter a title.";
    } else {
        $sql = "INSERT INTO leads (customer_id, title, status, notes)
                VALUES ($customer_id, '$title', '$status', '$notes')";

        if (mysqli_query($conn, $sql)) {
            header("Location: leads.php");
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Get list of customers for the dropdown
$customers = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Lead - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.1">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Add New Lead</h2>

        <?php if ($error != "") { ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="add_lead.php">
            <div class="form-group">
                <label>Customer *</label>
                <select name="customer_id" required>
                    <option value="">-- Select Customer --</option>
                    <?php while ($c = mysqli_fetch_assoc($customers)) { ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Lead Title *</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Won">Won</option>
                    <option value="Lost">Lost</option>
                </select>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="4"></textarea>
            </div>

            <button type="submit" class="btn">Save Lead</button>
            <a href="leads.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>

<?php include 'footer.php'; ?>
</body>
</html>