<?php
/*
    leads.php --> LIST ALL LEADS
    ---------------------------------
    Shows all leads along with customer names and actions.
*/

require_once __DIR__ . '/config.php';
require_once 'auth_check.php';

$sql = "SELECT leads.*, customers.name AS customer_name
        FROM leads
        INNER JOIN customers
        ON leads.customer_id = customers.id
        ORDER BY leads.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leads - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=2.2">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

    <h2>Lead Management</h2>

    <div class="top-actions">
        <a href="add_lead.php" class="btn">+ Add New Lead</a>
    </div>

    <table>

        <thead>
            <tr>
                <th>Title</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Created</th>
                <th width="240">Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php
        if(mysqli_num_rows($result)>0){

            while($row=mysqli_fetch_assoc($result)){

                $lead_status_class=strtolower(str_replace(' ','-',$row['status']));
        ?>

            <tr>

                <td><?php echo htmlspecialchars($row['title']); ?></td>

                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>

                <td>

                    <span class="badge status-<?php echo $lead_status_class; ?>">

                        <?php echo htmlspecialchars($row['status']); ?>

                    </span>

                </td>

                <td>

                    <?php echo date("d M Y",strtotime($row['created_at'])); ?>

                </td>

                <td>

                    <div class="actions">

                        <a href="view_lead.php?id=<?php echo $row['id']; ?>" class="btn btn-small">
                            View
                        </a>

                        <a href="edit_lead.php?id=<?php echo $row['id']; ?>" class="btn btn-small">
                            Edit
                        </a>

                        <a href="delete_lead.php?id=<?php echo $row['id']; ?>" class="btn btn-small btn-danger">
                            Delete
                        </a>

                    </div>

                </td>

            </tr>

        <?php

            }

        }else{

        ?>

        <tr>

            <td colspan="5" style="text-align:center;padding:20px;">

                No Leads Found.

            </td>

        </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<?php include 'footer.php'; ?>
</body>
</html>