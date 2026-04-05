<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-admin.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Transaction Monitoring</h1>
                <p>
                    This module is reserved for future payments, escrow,
                    order settlement, and payout management.
                </p>
            </div>

            <div class="empty-state">
                <h3>No transaction system yet</h3>
                <p>
                    Add this later when you build order flow, payments,
                    payout history, and financial records.
                </p>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>