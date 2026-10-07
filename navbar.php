<?php
// Get the current page filename to highlight the active menu link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="navbar">
    <h1>Small Business CRM</h1>
    <div class="nav-links">
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
        <a href="customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">Customers</a>
        <a href="leads.php" class="<?php echo ($current_page == 'leads.php') ? 'active' : ''; ?>">Leads</a>
        <a href="sales.php" class="<?php echo ($current_page == 'sales.php') ? 'active' : ''; ?>">Sales Tracking</a>
        <a href="support.php" class="<?php echo ($current_page == 'support.php') ? 'active' : ''; ?>">Support</a>
        <a href="history.php" class="<?php echo ($current_page == 'history.php') ? 'active' : ''; ?>">History</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>