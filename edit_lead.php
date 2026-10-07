<?php
require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: leads.php");
    exit();
}

$id = intval($_GET['id']);
$error = "";

$result = mysqli_query($conn, "SELECT * FROM leads WHERE id=$id");

if (mysqli_num_rows($result) == 0) {
    header("Location: leads.php");
    exit();
}

$lead = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer_id = intval($_POST['customer_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    if ($customer_id == 0 || $title == "") {
        $error = "Please fill all required fields.";
    } else {

        $sql = "UPDATE leads SET
                customer_id=$customer_id,
                title='$title',
                status='$status',
                notes='$notes'
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            header("Location: leads.php");
            exit();
        } else {
            $error = mysqli_error($conn);
        }
    }
}

$customers = mysqli_query($conn, "SELECT id,name FROM customers ORDER BY name");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Lead</title>
<link rel="stylesheet" href="style.css?v=2.1">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h2>Edit Lead</h2>

<?php if($error!=""){ ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">

<div class="form-group">
<label>Customer</label>

<select name="customer_id">

<?php while($c=mysqli_fetch_assoc($customers)){ ?>

<option value="<?php echo $c['id']; ?>"
<?php if($c['id']==$lead['customer_id']) echo "selected"; ?>>

<?php echo htmlspecialchars($c['name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Lead Title</label>

<input
type="text"
name="title"
value="<?php echo htmlspecialchars($lead['title']); ?>"
required>

</div>

<div class="form-group">

<label>Status</label>

<select name="status">

<option <?php if($lead['status']=="New") echo "selected"; ?>>New</option>

<option <?php if($lead['status']=="In Progress") echo "selected"; ?>>In Progress</option>

<option <?php if($lead['status']=="Won") echo "selected"; ?>>Won</option>

<option <?php if($lead['status']=="Lost") echo "selected"; ?>>Lost</option>

</select>

</div>

<div class="form-group">

<label>Notes</label>

<textarea name="notes" rows="5"><?php echo htmlspecialchars($lead['notes']); ?></textarea>

</div>

<button class="btn">Update Lead</button>

<a href="leads.php" class="btn btn-danger">Cancel</a>

</form>

</div>

<?php include 'footer.php'; ?>
</body>
</html>