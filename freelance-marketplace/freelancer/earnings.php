<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.php");
    exit;
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-freelancer.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Earnings</h1>
                <p>
                    This section will track freelancer earnings after order and payment
                    systems are integrated.
                </p>
            </div>

            <div class="empty-state">
                <h3>No earnings data yet</h3>
                <p>
                    Earnings will appear here after project payments and transactions are added.
                </p>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>