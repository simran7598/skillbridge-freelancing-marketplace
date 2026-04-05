<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$clientId = $_SESSION['user_id'];
$clientName = $_SESSION['full_name'];

$jobsCountSql = "SELECT COUNT(*) AS total FROM jobs WHERE client_id = ?";
$stmt = $conn->prepare($jobsCountSql);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$totalJobs = $stmt->get_result()->fetch_assoc()['total'];

$proposalCountSql = "SELECT COUNT(*) AS total
                     FROM proposals
                     JOIN jobs ON proposals.job_id = jobs.id
                     WHERE jobs.client_id = ?";
$stmt = $conn->prepare($proposalCountSql);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$totalProposals = $stmt->get_result()->fetch_assoc()['total'];

$latestJobsSql = "SELECT id, job_title, category, budget, status, created_at
                  FROM jobs
                  WHERE client_id = ?
                  ORDER BY created_at DESC
                  LIMIT 5";
$stmt = $conn->prepare($latestJobsSql);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$latestJobs = $stmt->get_result();

$latestProposalsSql = "SELECT proposals.id, proposals.bid_amount, proposals.delivery_time, proposals.status,
                              jobs.job_title, users.full_name AS freelancer_name
                       FROM proposals
                       JOIN jobs ON proposals.job_id = jobs.id
                       JOIN users ON proposals.freelancer_id = users.id
                       WHERE jobs.client_id = ?
                       ORDER BY proposals.created_at DESC
                       LIMIT 5";
$stmt = $conn->prepare($latestProposalsSql);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$latestProposals = $stmt->get_result();

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-client.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Welcome, <?php echo htmlspecialchars($clientName); ?></h1>
                <p>Manage your posted jobs, review proposals, and track hiring activity.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $totalJobs; ?></h3>
                    <p>Total Jobs Posted</p>
                    <span>Your live job listings</span>
                </div>

                <div class="stat-card">
                    <h3><?php echo $totalProposals; ?></h3>
                    <p>Total Proposals Received</p>
                    <span>Applications from freelancers</span>
                </div>

                <div class="stat-card">
                    <h3><a href="<?php echo url('client/post-job.php'); ?>" style="text-decoration:none; color:inherit;">+</a></h3>
                    <p>Post New Job</p>
                    <span>Create a new project listing</span>
                </div>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>Latest Jobs</h2>
                        <p>Your most recent job posts</p>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Category</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($latestJobs->num_rows > 0): ?>
                            <?php while ($job = $latestJobs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($job['category']); ?></td>
                                    <td><?php echo htmlspecialchars($job['budget']); ?></td>
                                    <td><?php echo htmlspecialchars($job['status']); ?></td>
                                    <td>
                                        <a href="<?php echo url('job-details.php?id=' . $job['id']); ?>" class="mini-btn">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No jobs posted yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>Latest Proposals</h2>
                        <p>Recent freelancer applications on your jobs</p>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($latestProposals->num_rows > 0): ?>
                            <?php while ($proposal = $latestProposals->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($proposal['freelancer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['bid_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['delivery_time']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No proposals received yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>