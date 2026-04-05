<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$message = "";
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobId = (int)($_POST["job_id"] ?? 0);
    $bidAmount = trim($_POST["bid_amount"] ?? "");
    $deliveryTime = trim($_POST["delivery_time"] ?? "");
    $proposalText = trim($_POST["proposal_text"] ?? "");
    $freelancerId = $_SESSION['user_id'];

    if ($jobId <= 0 || $bidAmount === "" || $deliveryTime === "" || $proposalText === "") {
        $message = "Please complete all proposal fields.";
    } else {
        $checkSql = "SELECT id FROM proposals WHERE job_id = ? AND freelancer_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $jobId, $freelancerId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $message = "You have already submitted a proposal for this job.";
        } else {
            $sql = "INSERT INTO proposals (job_id, freelancer_id, bid_amount, delivery_time, proposal_text)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisss", $jobId, $freelancerId, $bidAmount, $deliveryTime, $proposalText);

            if ($stmt->execute()) {
                $isSuccess = true;
                $message = "Proposal submitted successfully.";
            } else {
                $message = "Error submitting proposal: " . $stmt->error;
            }
        }
    }
}

$jobsSql = "SELECT jobs.*, users.full_name
            FROM jobs
            LEFT JOIN users ON jobs.client_id = users.id
            WHERE jobs.status = 'open'
            ORDER BY jobs.created_at DESC";
$jobsResult = $conn->query($jobsSql);

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-freelancer.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Browse Jobs</h1>
                <p>
                    Discover relevant freelance opportunities, review project details,
                    and submit tailored proposals to potential clients.
                </p>
            </div>

            <div class="dashboard-card">
                <div class="section-head">
                    <div>
                        <h2>Available Opportunities</h2>
                        <p>Jobs matching freelance skills and categories</p>
                    </div>
                </div>

                <div class="proposal-list">
                    <?php if ($jobsResult->num_rows > 0): ?>
                        <?php while ($job = $jobsResult->fetch_assoc()): ?>
                            <div class="proposal-item">
                                <h4><?php echo htmlspecialchars($job['job_title']); ?></h4>
                                <p><?php echo htmlspecialchars(substr($job['description'], 0, 180)); ?>...</p>
                                <div class="item-meta">
                                    <span>Budget: <?php echo htmlspecialchars($job['budget']); ?></span>
                                    <span><?php echo htmlspecialchars($job['experience_level']); ?></span>
                                    <span><?php echo htmlspecialchars($job['category']); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <h3>No jobs available</h3>
                            <p>No open jobs found right now.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-card">
                <div class="section-head">
                    <div>
                        <h2>Submit Proposal</h2>
                        <p>Apply to a real job from the database</p>
                    </div>
                </div>

                <?php if ($message != ""): ?>
                    <div class="alert-box <?php echo $isSuccess ? 'alert-success' : 'alert-info'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="job_id">Select Job</label>
                        <select id="job_id" name="job_id" required>
                            <option value="">Select a job</option>
                            <?php
                            $jobsDropdownSql = "SELECT id, job_title FROM jobs WHERE status = 'open' ORDER BY created_at DESC";
                            $jobsDropdownResult = $conn->query($jobsDropdownSql);
                            while ($jobOption = $jobsDropdownResult->fetch_assoc()):
                            ?>
                                <option value="<?php echo $jobOption['id']; ?>">
                                    <?php echo htmlspecialchars($jobOption['job_title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="bid_amount">Bid Amount</label>
                            <input type="text" id="bid_amount" name="bid_amount" placeholder="Example: 280" required>
                        </div>

                        <div class="form-group">
                            <label for="delivery_time">Delivery Time</label>
                            <input type="text" id="delivery_time" name="delivery_time" placeholder="Example: 7 Days" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="proposal_text">Proposal Message</label>
                        <textarea id="proposal_text" name="proposal_text" placeholder="Write a tailored proposal for the selected job" required></textarea>
                    </div>

                    <button type="submit" class="auth-submit">Submit Proposal</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>