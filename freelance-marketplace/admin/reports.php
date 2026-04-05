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
                <h1>Reports & Moderation</h1>
                <p>
                    This module is reserved for future complaint handling,
                    abuse reports, and moderation workflows.
                </p>
            </div>

            <div class="empty-state">
                <h3>No reports module yet</h3>
                <p>
                    You can add this later with a reports table for user complaints,
                    suspicious jobs, abusive messages, or fake profiles.
                </p>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>