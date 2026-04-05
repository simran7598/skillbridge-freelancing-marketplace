<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$freelancerId = $_SESSION['user_id'];

$sql = "SELECT proposals.*, jobs.job_title, users.full_name AS client_name
        FROM proposals
        JOIN jobs ON proposals.job_id = jobs.id
        JOIN users ON jobs.client_id = users.id
        WHERE proposals.freelancer_id = ?
        ORDER BY proposals.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $freelancerId);
$stmt->execute();
$result = $stmt->get_result();

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-freelancer.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>My Proposals</h1>
                <p>
                    Track all submitted proposals, review client responses,
                    and identify which opportunities are moving forward.
                </p>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>Submitted Proposals</h2>
                        <p>Status overview for your applications</p>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Client</th>
                            <th>Bid</th>
                            <th>Delivery</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($proposal = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($proposal['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['bid_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['delivery_time']); ?></td>
                                    <td><span class="status-badge status-pending"><?php echo htmlspecialchars($proposal['status']); ?></span></td>
                                    <td>
                                        <a href="<?php echo url('job-details.php?id=' . $proposal['job_id']); ?>" class="mini-btn">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No proposals submitted yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>