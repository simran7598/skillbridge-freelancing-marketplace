<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$freelancerId = $_SESSION['user_id'];
$freelancerName = $_SESSION['full_name'];

$proposalCountSql = "SELECT COUNT(*) AS total FROM proposals WHERE freelancer_id = ?";
$stmt = $conn->prepare($proposalCountSql);
$stmt->bind_param("i", $freelancerId);
$stmt->execute();
$totalProposals = $stmt->get_result()->fetch_assoc()['total'];

$servicesCountSql = "SELECT COUNT(*) AS total FROM services WHERE freelancer_id = ?";
$stmt = $conn->prepare($servicesCountSql);
$stmt->bind_param("i", $freelancerId);
$stmt->execute();
$totalServices = $stmt->get_result()->fetch_assoc()['total'];

$profileSql = "SELECT title, skills, hourly_rate, experience_level, location
               FROM freelancer_profiles
               WHERE user_id = ?";
$stmt = $conn->prepare($profileSql);
$stmt->bind_param("i", $freelancerId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();

$latestProposalsSql = "SELECT proposals.id, proposals.bid_amount, proposals.delivery_time, proposals.status,
                              jobs.job_title
                       FROM proposals
                       JOIN jobs ON proposals.job_id = jobs.id
                       WHERE proposals.freelancer_id = ?
                       ORDER BY proposals.created_at DESC
                       LIMIT 5";
$stmt = $conn->prepare($latestProposalsSql);
$stmt->bind_param("i", $freelancerId);
$stmt->execute();
$latestProposals = $stmt->get_result();

$latestServicesSql = "SELECT id, title, price, delivery_time
                      FROM services
                      WHERE freelancer_id = ?
                      ORDER BY created_at DESC
                      LIMIT 5";
$stmt = $conn->prepare($latestServicesSql);
$stmt->bind_param("i", $freelancerId);
$stmt->execute();
$latestServices = $stmt->get_result();

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-freelancer.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Welcome, <?php echo htmlspecialchars($freelancerName); ?></h1>
                <p>Manage your profile, proposals, and services from your freelancer workspace.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $totalProposals; ?></h3>
                    <p>Proposals Submitted</p>
                    <span>Your job applications</span>
                </div>

                <div class="stat-card">
                    <h3><?php echo $totalServices; ?></h3>
                    <p>Services Created</p>
                    <span>Your marketplace listings</span>
                </div>

                <div class="stat-card">
                    <h3>
                        <a href="<?php echo url('freelancer-profile.php?id=' . $freelancerId); ?>" style="text-decoration:none; color:inherit;">
                            View
                        </a>
                    </h3>
                    <p>Public Profile</p>
                    <span>See your public freelancer page</span>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="section-head">
                    <div>
                        <h2>Profile Summary</h2>
                        <p>Your current freelancer profile data</p>
                    </div>
                </div>

                <div class="activity-list">
                    <div class="activity-item">
                        <h4>Title</h4>
                        <p><?php echo htmlspecialchars($profile['title'] ?? 'Not set'); ?></p>
                    </div>
                    <div class="activity-item">
                        <h4>Skills</h4>
                        <p><?php echo htmlspecialchars($profile['skills'] ?? 'Not set'); ?></p>
                    </div>
                    <div class="activity-item">
                        <h4>Rate / Experience / Location</h4>
                        <p>
                            $<?php echo htmlspecialchars($profile['hourly_rate'] ?? '0'); ?>/hr ·
                            <?php echo htmlspecialchars($profile['experience_level'] ?? 'Not set'); ?> ·
                            <?php echo htmlspecialchars($profile['location'] ?? 'Not set'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>Latest Proposals</h2>
                        <p>Your recent applications</p>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
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
                                    <td><?php echo htmlspecialchars($proposal['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['bid_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['delivery_time']); ?></td>
                                    <td><?php echo htmlspecialchars($proposal['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No proposals submitted yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>Latest Services</h2>
                        <p>Your recently added services</p>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Delivery</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($latestServices->num_rows > 0): ?>
                            <?php while ($service = $latestServices->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($service['title']); ?></td>
                                    <td><?php echo htmlspecialchars($service['price']); ?></td>
                                    <td><?php echo htmlspecialchars($service['delivery_time']); ?></td>
                                    <td>
                                        <a href="<?php echo url('service-details.php?id=' . $service['id']); ?>" class="mini-btn">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No services added yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>