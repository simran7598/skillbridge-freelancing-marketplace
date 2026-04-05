<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$clientId = $_SESSION['user_id'];

$sql = "SELECT * FROM jobs WHERE client_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$result = $stmt->get_result();

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-client.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Manage Posted Jobs</h1>
                <p>Review all active, pending, and closed jobs from your client account.</p>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>All Jobs</h2>
                        <p>Monitor job status and actions</p>
                    </div>
                    <div class="page-top-actions">
                        <a href="<?php echo url('client/post-job.php'); ?>" class="small-btn">Post Another Job</a>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Category</th>
                            <th>Budget</th>
                            <th>Experience</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($job = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($job['category']); ?></td>
                                <td><?php echo htmlspecialchars($job['budget']); ?></td>
                                <td><?php echo htmlspecialchars($job['experience_level']); ?></td>
                                <td><span class="status-badge status-open"><?php echo htmlspecialchars($job['status']); ?></span></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?php echo url('job-details.php?id=' . $job['id']); ?>" class="mini-btn">View</a>
                                        <a href="<?php echo url('client/post-job.php'); ?>" class="mini-outline-btn">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?><?php
include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-client.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Manage Posted Jobs</h1>
                <p>
                    Review all active, pending, and closed jobs from your client account.
                </p>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>All Jobs</h2>
                        <p>Monitor job status, proposal count, and actions</p>
                    </div>
                    <div class="page-top-actions">
                        <a href="<?php echo url('client/post-job.php'); ?>" class="small-btn">Post Another Job</a>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Category</th>
                            <th>Budget</th>
                            <th>Proposals</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>Build a Business Website in PHP</td>
                            <td>Web Development</td>
                            <td>$300</td>
                            <td>12</td>
                            <td><span class="status-badge status-open">Open</span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo url('job-details.php?id=1'); ?>" class="mini-btn">View</a>
                                    <a href="<?php echo url('client/post-job.php'); ?>" class="mini-outline-btn">Edit</a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Design Admin Dashboard UI</td>
                            <td>UI/UX Design</td>
                            <td>$220</td>
                            <td>9</td>
                            <td><span class="status-badge status-pending">Reviewing</span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo url('job-details.php?id=2'); ?>" class="mini-btn">View</a>
                                    <a href="<?php echo url('client/post-job.php'); ?>" class="mini-outline-btn">Edit</a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>SEO Blog Content Package</td>
                            <td>Content Writing</td>
                            <td>$140</td>
                            <td>7</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo url('job-details.php?id=3'); ?>" class="mini-btn">View</a>
                                    <a href="<?php echo url('client/manage-jobs.php'); ?>" class="mini-outline-btn">Close</a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Brand Identity Design</td>
                            <td>Graphic Design</td>
                            <td>$190</td>
                            <td>15</td>
                            <td><span class="status-badge status-closed">Closed</span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo url('job-details.php?id=1'); ?>" class="mini-btn">View</a>
                                    <a href="<?php echo url('client/manage-jobs.php'); ?>" class="mini-outline-btn">Repost</a>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="dashboard-card">
                <div class="section-head">
                    <div>
                        <h2>Client Tips for Better Hiring</h2>
                        <p>Improve the quality of proposals you receive</p>
                    </div>
                </div>

                <div class="activity-list">
                    <div class="activity-item">
                        <h4>Write clear job titles</h4>
                        <p>Specific titles attract more relevant freelancers.</p>
                    </div>
                    <div class="activity-item">
                        <h4>Describe deliverables clearly</h4>
                        <p>Define pages, features, and deadlines clearly.</p>
                    </div>
                    <div class="activity-item">
                        <h4>Set realistic budgets</h4>
                        <p>Balanced budgets attract better freelancers.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>