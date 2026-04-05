<?php
include('includes/db.php');
include('includes/header.php');
include('includes/navbar.php');

$sql = "SELECT jobs.*, users.full_name
        FROM jobs
        LEFT JOIN users ON jobs.client_id = users.id
        WHERE jobs.status = 'open'
        ORDER BY jobs.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
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
        <h1>Explore Freelance Jobs</h1>
        <p>
            Find projects posted by clients across web development, design, content,
            marketing, and more.
        </p>
    </div>
</section>

<section class="listing-section">
    <div class="container">
        <p>Total jobs found: <?php echo $result->num_rows; ?></p>

        <div class="listing-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($job = $result->fetch_assoc()): ?>
                    <div class="listing-card">
                        <span class="job-badge"><?php echo htmlspecialchars($job['job_type']); ?></span>
                        <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                        <p class="text">
                            <?php echo htmlspecialchars(substr($job['description'], 0, 120)); ?>...
                        </p>

                        <div class="card-meta">
                            <span><?php echo htmlspecialchars($job['budget']); ?></span>
                            <span><?php echo htmlspecialchars($job['experience_level']); ?></span>
                            <span><?php echo htmlspecialchars($job['category']); ?></span>
                        </div>

                        <div class="card-actions">
                            <a href="<?php echo url('job-details.php?id=' . $job['id']); ?>" class="small-btn">View Details</a>
                           <?php if (!$isLoggedIn): ?>
    <a href="<?php echo url('login.php'); ?>" class="outline-btn">Apply Now</a>
<?php elseif ($userRole === 'freelancer'): ?>
    <a href="<?php echo url('freelancer/browse-jobs.php'); ?>" class="outline-btn">Apply Now</a>
<?php else: ?>
    <a href="<?php echo url('jobs.php'); ?>" class="outline-btn">View Jobs</a>
<?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No jobs found in database.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?><?php include('includes/header.php'); ?>
