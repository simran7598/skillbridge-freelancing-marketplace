<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<aside class="dashboard-sidebar">
    <div class="sidebar-brand">
        <h2>Client Panel</h2>
        <p>Manage hiring and projects</p>
    </div>

    <nav class="sidebar-menu">
        <a href="<?php echo url('client/dashboard.php'); ?>" class="<?php echo ($currentPage == 'dashboard.php') ? 'active-sidebar' : ''; ?>">Dashboard</a>
        <a href="<?php echo url('client/edit-profile.php'); ?>" class="<?php echo ($currentPage == 'edit-profile.php') ? 'active-sidebar' : ''; ?>">Edit Profile</a>
        <a href="<?php echo url('client/post-job.php'); ?>" class="<?php echo ($currentPage == 'post-job.php') ? 'active-sidebar' : ''; ?>">Post Job</a>
        <a href="<?php echo url('client/manage-jobs.php'); ?>" class="<?php echo ($currentPage == 'manage-jobs.php') ? 'active-sidebar' : ''; ?>">Manage Jobs</a>
        <a href="<?php echo url('client/proposals.php'); ?>" class="<?php echo ($currentPage == 'proposals.php') ? 'active-sidebar' : ''; ?>">Proposals</a>
        <a href="<?php echo url('messages.php'); ?>" class="<?php echo ($currentPage == 'messages.php') ? 'active-sidebar' : ''; ?>">Messages</a>
    </nav>
</aside>