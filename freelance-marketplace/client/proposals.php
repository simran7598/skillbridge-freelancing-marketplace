<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$clientId = $_SESSION['user_id'];

$sql = "SELECT proposals.*, jobs.job_title, users.full_name AS freelancer_name
        FROM proposals
        JOIN jobs ON proposals.job_id = jobs.id
        JOIN users ON proposals.freelancer_id = users.id
        WHERE jobs.client_id = ?
        ORDER BY proposals.created_at DESC";

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
                <h1>Freelancer Proposals</h1>
                <p>
                    Review applications submitted by freelancers, compare budgets,
                    evaluate skills, and shortlist top candidates.
                </p>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>Proposal Inbox</h2>
                        <p>Applications received for your posted jobs</p>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Freelancer</th>
                            <th>Job</th>
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
                                    <td><?php echo htmlspecialchars($proposal['freelancer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['bid_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['delivery_time']); ?></td>
                                    <td><span class="status-badge status-pending"><?php echo htmlspecialchars($proposal['status']); ?></span></td>
                                    <td>
                                        <a href="<?php echo url('client/messages.php'); ?>" class="mini-btn">Shortlist</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No proposals received yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>