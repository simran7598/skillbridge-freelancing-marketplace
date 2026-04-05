<?php
include('includes/db.php');
include('includes/header.php');
include('includes/navbar.php');

$sql = "SELECT services.*, users.full_name
        FROM services
        JOIN users ON services.freelancer_id = users.id
        ORDER BY services.created_at DESC";

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
        <h1>Professional Services Marketplace</h1>
        <p>
            Buy ready-to-order freelance services for design, development, marketing,
            writing, and more.
        </p>
    </div>
</section>

<section class="listing-section">
    <div class="container">
        <div class="listing-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($service = $result->fetch_assoc()): ?>
                    <div class="listing-card">
                        <?php if (!empty($service['image'])): ?>
                            <img src="<?php echo url('uploads/services/' . $service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="card-img">
                        <?php endif; ?>

                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p class="text"><?php echo htmlspecialchars(substr($service['description'], 0, 110)); ?>...</p>

                        <div class="card-meta">
                            <span><?php echo htmlspecialchars($service['delivery_time']); ?></span>
                            <span><?php echo htmlspecialchars($service['full_name']); ?></span>
                        </div>

                        <div class="card-price"><?php echo htmlspecialchars($service['price']); ?></div>

                        <div class="card-actions">
                            <a href="<?php echo url('service-details.php?id=' . $service['id']); ?>" class="small-btn">View Service</a>
                            <?php if (!$isLoggedIn): ?>
    <a href="<?php echo url('login.php'); ?>" class="outline-btn">Order Now</a>
<?php else: ?>
    <a href="<?php echo url('messages.php?user=' . $service['freelancer_id']); ?>" class="outline-btn">Order Now</a>
<?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No services found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>