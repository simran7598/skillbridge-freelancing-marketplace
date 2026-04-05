<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$message = "";
$isSuccess = false;
$freelancerId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $projectTitle = trim($_POST["project_title"] ?? "");
    $projectCategory = trim($_POST["project_category"] ?? "");
    $projectDesc = trim($_POST["project_desc"] ?? "");
    $imageName = null;

    if ($projectTitle === "" || $projectDesc === "") {
        $message = "Please complete portfolio title and description.";
    } else {
        if (isset($_FILES['portfolio_image']) && $_FILES['portfolio_image']['error'] === 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['portfolio_image']['type'], $allowedTypes)) {
                $tmpName = $_FILES['portfolio_image']['tmp_name'];
                $originalName = basename($_FILES['portfolio_image']['name']);
                $imageName = time() . "_" . $originalName;
                $targetPath = "../uploads/portfolio/" . $imageName;

                if (!move_uploaded_file($tmpName, $targetPath)) {
                    $imageName = null;
                }
            }
        }

        $sql = "INSERT INTO portfolio_items (freelancer_id, title, category, description, image)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $freelancerId, $projectTitle, $projectCategory, $projectDesc, $imageName);

        if ($stmt->execute()) {
            $isSuccess = true;
            $message = "Portfolio item added successfully.";
        } else {
            $message = "Error adding portfolio item: " . $stmt->error;
        }
    }
}

$portfolioSql = "SELECT * FROM portfolio_items
                 WHERE freelancer_id = ?
                 ORDER BY created_at DESC";
$stmtPortfolio = $conn->prepare($portfolioSql);
$stmtPortfolio->bind_param("i", $freelancerId);
$stmtPortfolio->execute();
$portfolioResult = $stmtPortfolio->get_result();

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-freelancer.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Portfolio Manager</h1>
                <p>
                    Showcase previous projects, build credibility, and strengthen
                    your freelancer profile with professional portfolio entries.
                </p>
            </div>

            <?php if ($message != ""): ?>
                <div class="alert-box <?php echo $isSuccess ? 'alert-success' : 'alert-info'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="portfolio-manager-grid">
                <div class="form-card">
                    <div class="section-head">
                        <div>
                            <h2>Add Portfolio Item</h2>
                            <p>Create a new showcase entry</p>
                        </div>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="auth-form">
                        <div class="form-group">
                            <label for="project_title">Project Title</label>
                            <input type="text" id="project_title" name="project_title" placeholder="Example: Business Website Redesign">
                        </div>

                        <div class="form-group">
                            <label for="project_category">Project Category</label>
                            <select id="project_category" name="project_category">
                                <option value="">Select Category</option>
                                <option>Web Development</option>
                                <option>UI/UX Design</option>
                                <option>Graphic Design</option>
                                <option>Content Writing</option>
                                <option>Marketing</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="project_desc">Project Description</label>
                            <textarea id="project_desc" name="project_desc" placeholder="Explain the project outcome, design style, or features delivered"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="portfolio_image">Project Image</label>
                            <input type="file" id="portfolio_image" name="portfolio_image" accept="image/*">
                        </div>

                        <button type="submit" class="auth-submit">Add Portfolio Item</button>
                    </form>
                </div>

                <div class="dashboard-card">
                    <div class="section-head">
                        <div>
                            <h2>Current Portfolio</h2>
                            <p>Your visible showcase projects</p>
                        </div>
                    </div>

                    <div class="activity-list">
                        <?php if ($portfolioResult->num_rows > 0): ?>
                            <?php while ($item = $portfolioResult->fetch_assoc()): ?>
                                <div class="portfolio-manage-card">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo url('uploads/portfolio/' . $item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="portfolio-img">
                                    <?php else: ?>
                                        <img src="<?php echo url('assets/images/defaults/service-placeholder.jpg'); ?>" alt="Portfolio Placeholder" class="portfolio-img">
                                    <?php endif; ?>

                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                                    <?php if (!empty($item['category'])): ?>
                                        <p><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <h3>No portfolio items yet</h3>
                                <p>Add your first project to build trust with clients.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>