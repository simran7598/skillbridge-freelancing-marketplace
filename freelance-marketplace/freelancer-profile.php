<?php
include('includes/db.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT users.id, users.full_name, users.profile_image,
               freelancer_profiles.title, freelancer_profiles.bio,
               freelancer_profiles.skills, freelancer_profiles.hourly_rate,
               freelancer_profiles.experience_level, freelancer_profiles.location
        FROM users
        LEFT JOIN freelancer_profiles ON users.id = freelancer_profiles.user_id
        WHERE users.id = ? AND users.role = 'freelancer'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$freelancer = $result->fetch_assoc();
$portfolioSql = "SELECT * FROM portfolio_items
                 WHERE freelancer_id = ?
                 ORDER BY created_at DESC";
$stmtPortfolio = $conn->prepare($portfolioSql);
$stmtPortfolio->bind_param("i", $id);
$stmtPortfolio->execute();
$portfolioResult = $stmtPortfolio->get_result();

if (!$freelancer) {
    die("Freelancer not found.");
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
        <h1>Freelancer Profile</h1>
        <p>Review freelancer expertise and details.</p>
    </div>
</section>

<section class="detail-section">
    <div class="container detail-layout">
        <div class="detail-main">
            <div class="profile-hero">
               <?php if (!empty($freelancer['profile_image'])): ?>
    <img src="<?php echo url('uploads/profiles/' . $freelancer['profile_image']); ?>" class="profile-img">
<?php else: ?>
    <div class="profile-avatar"></div>
<?php endif; ?>

                <div class="profile-hero-text">
                    <h2><?php echo htmlspecialchars($freelancer['full_name']); ?></h2>
                    <p><?php echo htmlspecialchars($freelancer['title'] ?: 'Freelancer'); ?></p>

                    <div class="info-row">
                        <span class="info-pill">
                            <?php echo htmlspecialchars($freelancer['experience_level'] ?: 'Not set'); ?>
                        </span>
                        <span class="info-pill">
                            <?php echo htmlspecialchars($freelancer['location'] ?: 'Not set'); ?>
                        </span>
                        <span class="info-pill">
                            $<?php echo htmlspecialchars($freelancer['hourly_rate'] ?: '0'); ?>/hr
                        </span>
                    </div>
                </div>
            </div>

            <div class="detail-block">
                <h3>About</h3>
                <p><?php echo nl2br(htmlspecialchars($freelancer['bio'] ?: 'No bio added yet.')); ?></p>
            </div>

            <div class="detail-block">
                <h3>Skills</h3>
                <div class="skills-list">
                    <?php
                    $skills = !empty($freelancer['skills']) ? explode(',', $freelancer['skills']) : [];
                    if (!empty($skills)) {
                        foreach ($skills as $skill) {
                            echo '<span>' . htmlspecialchars(trim($skill)) . '</span>';
                        }
                    } else {
                        echo '<span>No skills added</span>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="detail-block">
    <h3>Portfolio</h3>

    <div class="related-grid">
        <?php if ($portfolioResult->num_rows > 0): ?>
            <?php while ($item = $portfolioResult->fetch_assoc()): ?>
                <div class="related-card">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?php echo url('uploads/portfolio/' . $item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="portfolio-img">
                    <?php else: ?>
                        <img src="<?php echo url('assets/images/defaults/service-placeholder.jpg'); ?>" alt="Portfolio Placeholder" class="portfolio-img">
                    <?php endif; ?>

                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>

                    <?php if (!empty($item['category'])): ?>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
                    <?php endif; ?>

                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No portfolio items added yet.</p>
        <?php endif; ?>
    </div>
</div>
        <div>
            <div class="detail-sidebar-card">
                <div class="sidebar-price">
                    $<?php echo htmlspecialchars($freelancer['hourly_rate'] ?: '0'); ?>/hr
                </div>

                <?php if (!$isLoggedIn): ?>
    <a href="<?php echo url('login.php'); ?>" class="full-btn primary">Hire Freelancer</a>
    <a href="<?php echo url('login.php'); ?>" class="full-btn secondary">Send Message</a>
    <a href="<?php echo url('login.php'); ?>" class="full-btn outline">Save Profile</a>

<?php elseif ($userRole === 'client'): ?>
    <a href="<?php echo url('messages.php?user=' . $freelancer['id']); ?>" class="full-btn primary">Hire Freelancer</a>
    <a href="<?php echo url('messages.php?user=' . $freelancer['id']); ?>" class="full-btn secondary">Send Message</a>
    <a href="<?php echo url('save-item.php?type=freelancer&id=' . $freelancer['id']); ?>" class="full-btn outline">Save Profile</a>

<?php elseif ($userRole === 'freelancer'): ?>
    <a href="<?php echo url('messages.php?user=' . $freelancer['id']); ?>" class="full-btn primary">Message</a>
    <a href="<?php echo url('freelancer/dashboard.php'); ?>" class="full-btn secondary">Go to Dashboard</a>
    <a href="<?php echo url('save-item.php?type=freelancer&id=' . $freelancer['id']); ?>" class="full-btn outline">Save Profile</a>

<?php else: ?>
    <a href="<?php echo url('admin/dashboard.php'); ?>" class="full-btn primary">Admin Panel</a>
    <a href="<?php echo url('messages.php?user=' . $freelancer['id']); ?>" class="full-btn secondary">Send Message</a>
    <a href="<?php echo url('save-item.php?type=freelancer&id=' . $freelancer['id']); ?>" class="full-btn outline">Save Profile</a>
<?php endif; ?>
            </div>

            <div class="detail-sidebar-card">
                <h3>Profile Details</h3>
                <ul class="sidebar-list">
                    <li>Title: <?php echo htmlspecialchars($freelancer['title'] ?: 'Not set'); ?></li>
                    <li>Experience: <?php echo htmlspecialchars($freelancer['experience_level'] ?: 'Not set'); ?></li>
                    <li>Location: <?php echo htmlspecialchars($freelancer['location'] ?: 'Not set'); ?></li>
                    <li>Hourly Rate: $<?php echo htmlspecialchars($freelancer['hourly_rate'] ?: '0'); ?>/hr</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>