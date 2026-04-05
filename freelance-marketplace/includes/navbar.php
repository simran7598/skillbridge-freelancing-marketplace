<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header class="main-header">
    <div class="container navbar">
        <div class="logo">
            <a href="<?php echo url('index.php'); ?>">SkillBridge</a>
        </div>

        <nav class="nav-links">
            <a href="<?php echo url('index.php'); ?>" class="<?php echo ($currentPage == 'index.php') ? 'active-nav' : ''; ?>">Home</a>
            <a href="<?php echo url('freelancers.php'); ?>" class="<?php echo ($currentPage == 'freelancers.php' || $currentPage == 'freelancer-profile.php') ? 'active-nav' : ''; ?>">Freelancers</a>
            <a href="<?php echo url('jobs.php'); ?>" class="<?php echo ($currentPage == 'jobs.php' || $currentPage == 'job-details.php') ? 'active-nav' : ''; ?>">Jobs</a>
            <a href="<?php echo url('services.php'); ?>" class="<?php echo ($currentPage == 'services.php' || $currentPage == 'service-details.php') ? 'active-nav' : ''; ?>">Services</a>
            <a href="<?php echo url('about.php'); ?>" class="<?php echo ($currentPage == 'about.php') ? 'active-nav' : ''; ?>">About</a>
            <a href="<?php echo url('contact.php'); ?>" class="<?php echo ($currentPage == 'contact.php') ? 'active-nav' : ''; ?>">Contact</a>
        </nav>

        <div class="nav-actions">
    <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['role'] === 'client'): ?>
            <a href="<?php echo url('client/dashboard.php'); ?>" class="login-btn">Dashboard</a>
        <?php elseif ($_SESSION['role'] === 'freelancer'): ?>
            <a href="<?php echo url('freelancer/dashboard.php'); ?>" class="login-btn">Dashboard</a>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="login-btn">Admin</a>
        <?php endif; ?>
        <a href="<?php echo url('saved-items.php'); ?>" class="login-btn">Saved</a>
        <a href="<?php echo url('logout.php'); ?>" class="signup-btn">Logout</a>
    <?php else: ?>
        <a href="<?php echo url('login.php'); ?>" class="login-btn">Login</a>
        <a href="<?php echo url('register.php'); ?>" class="signup-btn">Sign Up</a>
    <?php endif; ?>
</div>
    </div>
</header>