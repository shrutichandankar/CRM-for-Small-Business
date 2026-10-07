
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php';

/* ---------- Dashboard Counts ---------- */

$customerCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM customers")
)['total'];

$leadCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM leads")
)['total'];

$wonCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM leads WHERE status='Won'")
)['total'];

$salesCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales_tracking")
)['total'];

$supportCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM support_tickets WHERE status != 'Closed'")
)['total'];

$communicationCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM communication_history")
)['total'];

/* ---------- Sales Pipeline ---------- */

$prospectingCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales_tracking WHERE stage='Prospecting'")
)['total'];

$proposalCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales_tracking WHERE stage='Proposal'")
)['total'];

$negotiationCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales_tracking WHERE stage='Negotiation'")
)['total'];

$salesWonCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales_tracking WHERE stage='Won'")
)['total'];

$salesLostCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales_tracking WHERE stage='Lost'")
)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Simple CRM</title>
    <link rel="stylesheet" href="style.css?v=1.2">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container dashboard-container">

    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
            <p class="dashboard-subtitle">
                Here's an overview of your customer relationship activities.
            </p>
        </div>
    </div>

    <!-- Dashboard Cards -->

    <div class="cards dashboard-cards">

        <div class="card">
            <h3><?php echo $customerCount; ?></h3>
            <p>Total Customers</p>
        </div>

        <div class="card orange">
            <h3><?php echo $leadCount; ?></h3>
            <p>Total Leads</p>
        </div>

        <div class="card green">
            <h3><?php echo $wonCount; ?></h3>
            <p>Leads Won</p>
        </div>

        <div class="card purple">
            <h3><?php echo $salesCount; ?></h3>
            <p>Total Sales</p>
        </div>

        <div class="card red">
            <h3><?php echo $supportCount; ?></h3>
            <p>Open Support Tickets</p>
        </div>

        <div class="card teal">
            <h3><?php echo $communicationCount; ?></h3>
            <p>Interactions</p>
        </div>

    </div>


    <!-- Sales Pipeline -->

    <div class="dashboard-section">

        <div class="section-header">
            <h3>Sales Pipeline</h3>
            <a href="sales.php" class="view-link">View Sales →</a>
        </div>

        <div class="pipeline">

            <div class="pipeline-item">
                <span class="badge prospecting">Prospecting</span>
                <strong><?php echo $prospectingCount; ?></strong>
            </div>

            <div class="pipeline-item">
                <span class="badge proposal">Proposal</span>
                <strong><?php echo $proposalCount; ?></strong>
            </div>

            <div class="pipeline-item">
                <span class="badge negotiation">Negotiation</span>
                <strong><?php echo $negotiationCount; ?></strong>
            </div>

            <div class="pipeline-item">
                <span class="badge won">Won</span>
                <strong><?php echo $salesWonCount; ?></strong>
            </div>

            <div class="pipeline-item">
                <span class="badge lost">Lost</span>
                <strong><?php echo $salesLostCount; ?></strong>
            </div>

        </div>

    </div>


    <!-- Quick Actions -->

    <div class="dashboard-section">

        <div class="section-header">
            <h3>Quick Actions</h3>
        </div>

        <div class="quick-actions">

            <a href="add_customer.php" class="quick-action">
                <span>+</span>
                <div>
                    <strong>Add Customer</strong>
                    <small>Create a new customer record</small>
                </div>
            </a>

            <a href="add_lead.php" class="quick-action">
                <span>+</span>
                <div>
                    <strong>Add Lead</strong>
                    <small>Create a new business opportunity</small>
                </div>
            </a>

            <a href="add_sale.php" class="quick-action">
                <span>+</span>
                <div>
                    <strong>Add Sale</strong>
                    <small>Track a new sales opportunity</small>
                </div>
            </a>

            <a href="add_support.php" class="quick-action">
                <span>+</span>
                <div>
                    <strong>Add Support Ticket</strong>
                    <small>Record a customer issue</small>
                </div>
            </a>

            <a href="add_history.php" class="quick-action">
                <span>+</span>
                <div>
                    <strong>Add Interaction</strong>
                    <small>Record a call, email or meeting</small>
                </div>
            </a>

        </div>

    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>

