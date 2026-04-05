<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('includes/db.php');

$userId = $_SESSION['user_id'];

$savedSql = "SELECT * FROM saved_items WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($savedSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$savedResult = $stmt->get_result();

include('includes/header.php');
include('includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container">
        <div class="dashboard-header-card">
            <h1>Saved Items</h1>
            <p>Review all your saved jobs, freelancers, and services.</p>
        </div>

        <div class="dashboard-card">
            <div class="section-head">
                <div>
                    <h2>Your Saved Collection</h2>
                    <p>Items you marked for later review</p>
                </div>
            </div>

            <div class="activity-list">
                <?php if ($savedResult->num_rows > 0): ?>
                    <?php while ($saved = $savedResult->fetch_assoc()): ?>
                        <?php
                        $title = "Unknown item";
                        $link = "#";

                        if ($saved['item_type'] === 'job') {
                            $q = $conn->prepare("SELECT job_title FROM jobs WHERE id = ?");
                            $q->bind_param("i", $saved['item_id']);
                            $q->execute();
                            $r = $q->get_result()->fetch_assoc();
                            if ($r) {
                                $title = $r['job_title'];
                                $link = url('job-details.php?id=' . $saved['item_id']);
                            }
                        } elseif ($saved['item_type'] === 'freelancer') {
                            $q = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
                            $q->bind_param("i", $saved['item_id']);
                            $q->execute();
                            $r = $q->get_result()->fetch_assoc();
                            if ($r) {
                                $title = $r['full_name'];
                                $link = url('freelancer-profile.php?id=' . $saved['item_id']);
                            }
                        } elseif ($saved['item_type'] === 'service') {
                            $q = $conn->prepare("SELECT title FROM services WHERE id = ?");
                            $q->bind_param("i", $saved['item_id']);
                            $q->execute();
                            $r = $q->get_result()->fetch_assoc();
                            if ($r) {
                                $title = $r['title'];
                                $link = url('service-details.php?id=' . $saved['item_id']);
                            }
                        }
                        ?>

                        <div class="activity-item">
                            <h4><?php echo htmlspecialchars($title); ?></h4>
                            <p>Type: <?php echo htmlspecialchars(ucfirst($saved['item_type'])); ?></p>

                            <div class="table-actions">
                                <a href="<?php echo $link; ?>" class="mini-btn">View</a>
                                <a href="<?php echo url('remove-saved-item.php?type=' . $saved['item_type'] . '&id=' . $saved['item_id']); ?>" class="mini-outline-btn">Remove</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No saved items yet</h3>
                        <p>Save jobs, freelancers, or services to view them here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>