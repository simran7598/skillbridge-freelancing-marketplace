<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalJobs = $conn->query("SELECT COUNT(*) AS total FROM jobs")->fetch_assoc()['total'];
$totalProposals = $conn->query("SELECT COUNT(*) AS total FROM proposals")->fetch_assoc()['total'];
$totalServices = $conn->query("SELECT COUNT(*) AS total FROM services")->fetch_assoc()['total'];

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-admin.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Admin Dashboard</h1>
                <p>Monitor the real platform activity from database records.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Users</p>
                    <span>Clients, freelancers, admins</span>
                </div>
                <div class="stat-card">
                    <h3><?php echo $totalJobs; ?></h3>
                    <p>Total Jobs</p>
                    <span>All posted jobs</span>
                </div>
                <div class="stat-card">
                    <h3><?php echo $totalProposals; ?></h3>
                    <p>Total Proposals</p>
                    <span>Freelancer applications</span>
                </div>
                <div class="stat-card">
                    <h3><?php echo $totalServices; ?></h3>
                    <p>Total Services</p>
                    <span>Marketplace services</span>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="section-head">
                    <div>
                        <h2>Platform Summary</h2>
                        <p>Quick view of real platform data</p>
                    </div>
                </div>

                <div class="activity-list">
                    <div class="activity-item">
                        <h4><?php echo $totalUsers; ?> users registered</h4>
                        <p>The platform currently has stored user accounts from the database.</p>
                    </div>
                    <div class="activity-item">
                        <h4><?php echo $totalJobs; ?> jobs created</h4>
                        <p>Clients are posting real jobs through the platform.</p>
                    </div>
                    <div class="activity-item">
                        <h4><?php echo $totalProposals; ?> proposals submitted</h4>
                        <p>Freelancers are applying to jobs using real proposal records.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>