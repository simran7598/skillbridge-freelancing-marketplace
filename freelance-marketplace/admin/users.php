<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$sql = "SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-admin.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Manage Users</h1>
                <p>Review registered clients, freelancers, and admin accounts.</p>
            </div>

            <div class="table-card">
                <div class="section-head">
                    <div>
                        <h2>User Directory</h2>
                        <p>Real user records from database</p>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($user = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                   <td>
                                    <div class="table-actions">
                                      <?php if ($user['role'] === 'freelancer'): ?>
                                      <a href="<?php echo url('freelancer-profile.php?id=' . $user['id']); ?>" class="mini-btn">View</a>
                                      <?php elseif ($user['role'] === 'client'): ?>
                                      <a href="<?php echo url('admin/view-client.php?id=' . $user['id']); ?>" class="mini-btn">View</a>
                                      <?php else: ?>
                                      <a href="<?php echo url('admin/dashboard.php'); ?>" class="mini-btn">View</a>
                                      <?php endif; ?>
                                    </div>
                                </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>