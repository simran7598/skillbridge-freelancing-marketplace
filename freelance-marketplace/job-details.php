<?php
include('includes/db.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT jobs.*, users.full_name
        FROM jobs
        JOIN users ON jobs.client_id = users.id
        WHERE jobs.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    die("Job not found.");
}

include('includes/header.php');
include('includes/navbar.php');
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? '';
?>

<section class="page-banner">
    <div class="container">
        <h1>Job Details</h1>
        <p>
            Review complete project requirements, budget, skills, and client information.
        </p>
    </div>
</section>

<section class="detail-section">
    <div class="container detail-layout">
        <div class="detail-main">
            <h2 class="job-title"><?php echo htmlspecialchars($job['job_title']); ?></h2>

            <div class="job-summary">
                <span><?php echo htmlspecialchars($job['job_type']); ?></span>
                <span>Budget: <?php echo htmlspecialchars($job['budget']); ?></span>
                <span><?php echo htmlspecialchars($job['experience_level']); ?></span>
                <span><?php echo htmlspecialchars($job['category']); ?></span>
            </div>

            <div class="detail-block">
                <h3>Project Description</h3>
                <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
            </div>

            <div class="detail-block">
                <h3>Required Skills</h3>
                <div class="skills-list">
                    <?php
                    $skills = !empty($job['skills']) ? explode(',', $job['skills']) : [];
                    foreach ($skills as $skill):
                    ?>
                        <span><?php echo htmlspecialchars(trim($skill)); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="detail-block">
                <h3>About the Client</h3>
                <div class="client-box">
                    <h4><?php echo htmlspecialchars($job['full_name']); ?></h4>
                    <p class="muted-text">Client on SkillBridge</p>
                </div>
            </div>
        </div>

        <div>
            <div class="detail-sidebar-card">
                <div class="sidebar-price"><?php echo htmlspecialchars($job['budget']); ?></div>
                <p class="sidebar-subtext">Project opportunity details.</p>

<?php if (!$isLoggedIn): ?>
    <a href="<?php echo url('login.php'); ?>" class="full-btn primary">Apply Now</a>
    <a href="<?php echo url('login.php'); ?>" class="full-btn secondary">Save Job</a>
    <a href="<?php echo url('contact.php'); ?>" class="full-btn outline">Share Job</a>

<?php elseif ($userRole === 'freelancer'): ?>
    <a href="<?php echo url('freelancer/browse-jobs.php'); ?>" class="full-btn primary">Apply Now</a>
    <a href="<?php echo url('save-item.php?type=job&id=' . $job['id']); ?>" class="full-btn secondary">Save Job</a>
    <a href="<?php echo url('contact.php'); ?>" class="full-btn outline">Share Job</a>

<?php elseif ($userRole === 'client'): ?>
    <a href="<?php echo url('client/dashboard.php'); ?>" class="full-btn primary">Client Dashboard</a>
    <a href="<?php echo url('save-item.php?type=job&id=' . $job['id']); ?>" class="full-btn secondary">Save Job</a>
    <a href="<?php echo url('contact.php'); ?>" class="full-btn outline">Share Job</a>

<?php else: ?>
    <a href="<?php echo url('admin/dashboard.php'); ?>" class="full-btn primary">Admin Panel</a>
    <a href="<?php echo url('save-item.php?type=job&id=' . $job['id']); ?>" class="full-btn secondary">Save Job</a>
    <a href="<?php echo url('contact.php'); ?>" class="full-btn outline">Share Job</a>
<?php endif; ?>
            </div>

            <div class="detail-sidebar-card">
                <h3>Job Overview</h3>
                <ul class="sidebar-list">
                    <li>Category: <?php echo htmlspecialchars($job['category']); ?></li>
                    <li>Experience: <?php echo htmlspecialchars($job['experience_level']); ?></li>
                    <li>Project Type: <?php echo htmlspecialchars($job['job_type']); ?></li>
                    <li>Status: <?php echo htmlspecialchars($job['status']); ?></li>
                    <li>Posted: <?php echo htmlspecialchars($job['created_at']); ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>