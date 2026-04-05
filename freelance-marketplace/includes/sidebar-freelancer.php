<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<aside class="dashboard-sidebar">
    <div class="sidebar-brand">
        <h2>Freelancer Panel</h2>
        <p>Manage work and earnings</p>
    </div>

    <nav class="sidebar-menu">
        <a href="<?php echo url('freelancer/dashboard.php'); ?>" class="<?php echo ($currentPage == 'dashboard.php') ? 'active-sidebar' : ''; ?>">Dashboard</a>
        <a href="<?php echo url('freelancer/edit-profile.php'); ?>" class="<?php echo ($currentPage == 'edit-profile.php') ? 'active-sidebar' : ''; ?>">Edit Profile</a>
        <a href="<?php echo url('freelancer/browse-jobs.php'); ?>" class="<?php echo ($currentPage == 'browse-jobs.php') ? 'active-sidebar' : ''; ?>">Browse Jobs</a>
        <a href="<?php echo url('freelancer/my-proposals.php'); ?>" class="<?php echo ($currentPage == 'my-proposals.php') ? 'active-sidebar' : ''; ?>">My Proposals</a>
        <a href="<?php echo url('freelancer/add-service.php'); ?>" class="<?php echo ($currentPage == 'add-service.php') ? 'active-sidebar' : ''; ?>">Add Service</a>
        <a href="<?php echo url('freelancer/portfolio.php'); ?>" class="<?php echo ($currentPage == 'portfolio.php') ? 'active-sidebar' : ''; ?>">Portfolio</a>
        <a href="<?php echo url('freelancer/earnings.php'); ?>" class="<?php echo ($currentPage == 'earnings.php') ? 'active-sidebar' : ''; ?>">Earnings</a>
        <a href="<?php echo url('messages.php'); ?>" class="<?php echo ($currentPage == 'messages.php') ? 'active-sidebar' : ''; ?>">Messages</a>
    
    </nav>
</aside>