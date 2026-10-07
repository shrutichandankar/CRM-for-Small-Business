<?php

require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: leads.php");
    exit();
}

$id=intval($_GET['id']);

$result=mysqli_query($conn,"
SELECT leads.*,customers.name AS customer_name
FROM leads
JOIN customers
ON customers.id=leads.customer_id
WHERE leads.id=$id
");

if(mysqli_num_rows($result)==0){
    header("Location: leads.php");
    exit();
}

$lead=mysqli_fetch_assoc($result);

if(isset($_POST['confirm'])){

    mysqli_query($conn,"DELETE FROM leads WHERE id=$id");

    header("Location: leads.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Delete Lead</title>

<link rel="stylesheet" href="style.css?v=2.1">

</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h2>Delete Lead</h2>

<div class="alert alert-error">

Are you sure you want to delete this lead?

</div>

<table>

<tr>

<th>Customer</th>

<td><?php echo htmlspecialchars($lead['customer_name']); ?></td>

</tr>

<tr>

<th>Lead</th>

<td><?php echo htmlspecialchars($lead['title']); ?></td>

</tr>

<tr>

<th>Status</th>

<td><?php echo htmlspecialchars($lead['status']); ?></td>

</tr>

</table>

<br>

<form method="POST">

<button
type="submit"
name="confirm"
class="btn btn-danger">

Yes, Delete

</button>

<a href="leads.php" class="btn">

Cancel

</a>

</form>

</div>

<?php include 'footer.php'; ?>
</body>
</html>