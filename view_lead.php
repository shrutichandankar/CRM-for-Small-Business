<?php
/*
    view_lead.php
    -------------------------
    Displays complete information about a single lead.
*/

require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: leads.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT leads.*, customers.name AS customer_name
        FROM leads
        JOIN customers ON leads.customer_id = customers.id
        WHERE leads.id = $id";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: leads.php");
    exit();
}

$lead = mysqli_fetch_assoc($result);

$status_class = strtolower(str_replace(' ', '-', $lead['status']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Lead - CRM</title>
    <link rel="stylesheet" href="style.css?v=2.1">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

    <h2>Lead Details</h2>

    <table>

        <tr>
            <th width="200">Customer</th>
            <td><?php echo htmlspecialchars($lead['customer_name']); ?></td>
        </tr>

        <tr>
            <th>Lead Title</th>
            <td><?php echo htmlspecialchars($lead['title']); ?></td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                <span class="badge status-<?php echo $status_class; ?>">
                    <?php echo htmlspecialchars($lead['status']); ?>
                </span>
            </td>
        </tr>

        <tr>
            <th>Notes</th>
            <td>
                <?php
                if ($lead['notes'] != "") {
                    echo nl2br(htmlspecialchars($lead['notes']));
                } else {
                    echo "-";
                }
                ?>
            </td>
        </tr>

        <tr>
            <th>Created On</th>
            <td><?php echo htmlspecialchars($lead['created_at']); ?></td>
        </tr>

    </table>

    <br>

    <a href="edit_lead.php?id=<?php echo $lead['id']; ?>" class="btn">Edit</a>

    <a href="delete_lead.php?id=<?php echo $lead['id']; ?>" class="btn btn-danger">Delete</a>

    <a href="leads.php" class="btn">Back</a>

</div>

<?php include 'footer.php'; ?>
</body>
</html>