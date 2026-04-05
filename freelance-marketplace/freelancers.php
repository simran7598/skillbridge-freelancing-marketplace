<?php
include('includes/db.php');
include('includes/header.php');
include('includes/navbar.php');

$sql = "SELECT users.id, users.full_name, users.profile_image,
               freelancer_profiles.title, freelancer_profiles.bio,
               freelancer_profiles.skills, freelancer_profiles.hourly_rate,
               freelancer_profiles.experience_level, freelancer_profiles.location
        FROM users
        LEFT JOIN freelancer_profiles ON users.id = freelancer_profiles.user_id
        WHERE users.role = 'freelancer'
        ORDER BY users.created_at DESC";

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
        <h1>Browse Top Freelancers</h1>
        <p>
            Discover skilled professionals in development, design, writing, marketing,
            and other high-demand categories.
        </p>
    </div>
</section>

<section class="listing-section">
    <div class="container">
        <div class="listing-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($freelancer = $result->fetch_assoc()): ?>
                    <div class="listing-card">
                        <img src="<?php echo !empty($freelancer['profile_image']) 
                            ? url('uploads/profiles/' . $freelancer['profile_image']) 
                         : url('assets/images/defaults/avatar.jpg'); ?>" class="card-img">

                        <h3><?php echo htmlspecialchars($freelancer['full_name']); ?></h3>
                        <p class="tagline">
                            <?php echo htmlspecialchars($freelancer['title'] ?: 'Freelancer'); ?>
                        </p>
                        <p class="text">
                            <?php echo htmlspecialchars(substr($freelancer['bio'] ?: 'No bio added yet.', 0, 120)); ?>...
                        </p>

                        <div class="card-meta">
                            <span>
                                <?php echo htmlspecialchars($freelancer['experience_level'] ?: 'Not set'); ?>
                            </span>
                            <span>
                                <?php echo htmlspecialchars($freelancer['location'] ?: 'Not set'); ?>
                            </span>
                            <span>
                                $<?php echo htmlspecialchars($freelancer['hourly_rate'] ?: '0'); ?>/hr
                            </span>
                        </div>

                        <div class="card-actions">
                            <a href="<?php echo url('freelancer-profile.php?id=' . $freelancer['id']); ?>" class="small-btn">View Profile</a>
                            <?php if (!$isLoggedIn): ?>
    <a href="<?php echo url('login.php'); ?>" class="outline-btn">Message</a>
<?php else: ?>
    <a href="<?php echo url('messages.php?user=' . $freelancer['id']); ?>" class="outline-btn">Message</a>
<?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No freelancers found</h3>
                    <p>No freelancer profiles are available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>