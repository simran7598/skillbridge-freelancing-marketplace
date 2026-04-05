<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM users WHERE id = ? AND role = 'client'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

if (!$client) {
    die("Client not found.");
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-admin.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Client Details</h1>
                <p>Admin view of client account information</p>
            </div>

            <div class="dashboard-card">
                <div class="activity-list">
                    <div class="activity-item">
                        <h4>Name</h4>
                        <p><?php echo htmlspecialchars($client['full_name']); ?></p>
                    </div>

                    <div class="activity-item">
                        <h4>Email</h4>
                        <p><?php echo htmlspecialchars($client['email']); ?></p>
                    </div>

                    <div class="activity-item">
                        <h4>Role</h4>
                        <p><?php echo htmlspecialchars($client['role']); ?></p>
                    </div>

                    <div class="activity-item">
                        <h4>Joined On</h4>
                        <p><?php echo htmlspecialchars($client['created_at']); ?></p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="section-head">
                    <div>
                        <h2>Client Jobs</h2>
                        <p>Jobs posted by this client</p>
                    </div>
                </div>

                <?php
                $jobsSql = "SELECT * FROM jobs WHERE client_id = ? ORDER BY created_at DESC";
                $stmtJobs = $conn->prepare($jobsSql);
                $stmtJobs->bind_param("i", $id);
                $stmtJobs->execute();
                $jobsResult = $stmtJobs->get_result();
                ?>

                <div class="activity-list">
                    <?php if ($jobsResult->num_rows > 0): ?>
                        <?php while ($job = $jobsResult->fetch_assoc()): ?>
                            <div class="activity-item">
                                <h4><?php echo htmlspecialchars($job['job_title']); ?></h4>
                                <p>Budget: <?php echo htmlspecialchars($job['budget']); ?></p>

                                <div class="table-actions">
                                    <a href="<?php echo url('job-details.php?id=' . $job['id']); ?>" class="mini-btn">View Job</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No jobs posted by this client.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>