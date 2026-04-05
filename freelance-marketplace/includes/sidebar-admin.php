<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<aside class="dashboard-sidebar">
    <div class="sidebar-brand">
        <h2>Admin Panel</h2>
        <p>Control platform operations</p>
    </div>

    <nav class="sidebar-menu">
        <a href="<?php echo url('admin/dashboard.php'); ?>" class="<?php echo ($currentPage == 'dashboard.php') ? 'active-sidebar' : ''; ?>">Dashboard</a>
        <a href="<?php echo url('admin/users.php'); ?>" class="<?php echo ($currentPage == 'users.php') ? 'active-sidebar' : ''; ?>">Users</a>
        <a href="<?php echo url('admin/jobs.php'); ?>" class="<?php echo ($currentPage == 'jobs.php') ? 'active-sidebar' : ''; ?>">Jobs</a>
        <a href="<?php echo url('admin/reports.php'); ?>" class="<?php echo ($currentPage == 'reports.php') ? 'active-sidebar' : ''; ?>">Reports</a>
        <a href="<?php echo url('admin/transactions.php'); ?>" class="<?php echo ($currentPage == 'transactions.php') ? 'active-sidebar' : ''; ?>">Transactions</a>
        <a href="<?php echo url('admin/contact-messages.php'); ?>" class="<?php echo ($currentPage == 'contact-messages.php') ? 'active-sidebar' : ''; ?>">Contact Messages</a>
    </nav>
</aside>