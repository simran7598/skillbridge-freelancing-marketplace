<?php
include('includes/db.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT services.*, users.full_name
        FROM services
        JOIN users ON services.freelancer_id = users.id
        WHERE services.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();

if (!$service) {
    die("Service not found.");
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
        <h1>Service Details</h1>
        <p>Review service packages, delivery time, and seller information.</p>
    </div>
</section>

<section class="detail-section">
    <div class="container detail-layout">
        <div class="detail-main">
            <?php if (!empty($service['image'])): ?>
                <img src="<?php echo url('uploads/services/' . $service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="detail-img">
            <?php endif; ?>

            <h2 class="job-title"><?php echo htmlspecialchars($service['title']); ?></h2>

            <div class="job-summary">
                <span>Delivery: <?php echo htmlspecialchars($service['delivery_time']); ?></span>
                <span>Seller: <?php echo htmlspecialchars($service['full_name']); ?></span>
                <span>Price: <?php echo htmlspecialchars($service['price']); ?></span>
            </div>

            <div class="detail-block">
                <h3>Service Description</h3>
                <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
            </div>
        </div>

        <div>
            <div class="detail-sidebar-card">
                <div class="sidebar-price"><?php echo htmlspecialchars($service['price']); ?></div>
                <p class="sidebar-subtext">Professional freelance service offering.</p>

                <?php if (!$isLoggedIn): ?>
    <a href="<?php echo url('login.php'); ?>" class="full-btn primary">Order Now</a>
    <a href="<?php echo url('login.php'); ?>" class="full-btn secondary">Contact Seller</a>
    <a href="<?php echo url('login.php'); ?>" class="full-btn outline">Save Service</a>

<?php elseif ($userRole === 'client'): ?>
    <a href="<?php echo url('messages.php?user=' . $service['freelancer_id']); ?>" class="full-btn primary">Order Now</a>
    <a href="<?php echo url('messages.php?user=' . $service['freelancer_id']); ?>" class="full-btn secondary">Contact Seller</a>
    <a href="<?php echo url('save-item.php?type=service&id=' . $service['id']); ?>" class="full-btn outline">Save Service</a>

<?php elseif ($userRole === 'freelancer'): ?>
    <a href="<?php echo url('messages.php?user=' . $service['freelancer_id']); ?>" class="full-btn primary">Contact Seller</a>
    <a href="<?php echo url('freelancer/dashboard.php'); ?>" class="full-btn secondary">Go to Dashboard</a>
    <a href="<?php echo url('save-item.php?type=service&id=' . $service['id']); ?>" class="full-btn outline">Save Service</a>

<?php else: ?>
    <a href="<?php echo url('admin/dashboard.php'); ?>" class="full-btn primary">Admin Panel</a>
    <a href="<?php echo url('messages.php?user=' . $service['freelancer_id']); ?>" class="full-btn secondary">Contact Seller</a>
    <a href="<?php echo url('save-item.php?type=service&id=' . $service['id']); ?>" class="full-btn outline">Save Service</a>
<?php endif; ?>
            </div>

            <div class="detail-sidebar-card">
                <h3>Seller Information</h3>
                <ul class="sidebar-list">
                    <li>Name: <?php echo htmlspecialchars($service['full_name']); ?></li>
                    <li>Delivery: <?php echo htmlspecialchars($service['delivery_time']); ?></li>
                    <li>Price: <?php echo htmlspecialchars($service['price']); ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>