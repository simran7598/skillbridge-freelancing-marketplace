<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

if (isset($_GET['action']) && isset($_GET['id'])) {
    $jobId = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'close') {
        $update = $conn->prepare("UPDATE jobs SET status = 'closed' WHERE id = ?");
        $update->bind_param("i", $jobId);
        $update->execute();
    } elseif ($action === 'open') {
        $update = $conn->prepare("UPDATE jobs SET status = 'open' WHERE id = ?");
        $update->bind_param("i", $jobId);
        $update->execute();
    }

    header("Location: jobs.php");
    exit;
}

$sql = "SELECT jobs.*, users.full_name
        FROM jobs
        JOIN users ON jobs.client_id = users.id
        ORDER BY jobs.created_at DESC";

$result = $conn->query($sql);

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-admin.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Manage Jobs</h1>
                <p>Review and moderate real jobs posted on the platform.</p>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>Job Listings</h2>
                        <p>Database-driven jobs moderation panel</p>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Client</th>
                            <th>Category</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($job = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($job['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($job['category']); ?></td>
                                    <td><?php echo htmlspecialchars($job['budget']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $job['status'] === 'closed' ? 'status-closed' : 'status-open'; ?>">
                                            <?php echo htmlspecialchars($job['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="<?php echo url('job-details.php?id=' . $job['id']); ?>" class="mini-btn">View</a>

                                            <?php if ($job['status'] === 'open'): ?>
                                                <a href="<?php echo url('admin/jobs.php?action=close&id=' . $job['id']); ?>" class="mini-outline-btn">Close</a>
                                            <?php else: ?>
                                                <a href="<?php echo url('admin/jobs.php?action=open&id=' . $job['id']); ?>" class="mini-outline-btn">Open</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No jobs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>