<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$message = "";
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobTitle = trim($_POST["job_title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $budget = trim($_POST["budget"] ?? "");
    $experience = trim($_POST["experience"] ?? "");
    $jobType = trim($_POST["job_type"] ?? "");
    $skills = trim($_POST["skills"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $clientId = $_SESSION['user_id'];

    if ($jobTitle === "" || $category === "" || $budget === "" || $experience === "" || $jobType === "" || $description === "") {
        $message = "Please complete all required job posting fields.";
    } else {
        $sql = "INSERT INTO jobs (client_id, job_title, category, budget, experience_level, job_type, skills, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $clientId, $jobTitle, $category, $budget, $experience, $jobType, $skills, $description);

        if ($stmt->execute()) {
            $isSuccess = true;
            $message = "Job posted successfully.";
        } else {
            $message = "Error posting job.";
        }
    }
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-client.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Post a New Job</h1>
                <p>
                    Publish a professional job listing and define requirements clearly
                    so relevant freelancers can apply to your project.
                </p>
            </div>

            <div class="form-card">
                <div class="section-head">
                    <div>
                        <h2>Job Information</h2>
                        <p>Create a detailed and high-quality project post</p>
                    </div>
                </div>

                <?php if ($message != ""): ?>
                    <div class="alert-box <?php echo $isSuccess ? 'alert-success' : 'alert-info'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="job_title">Job Title</label>
                        <input type="text" id="job_title" name="job_title" placeholder="Example: Build a professional business website in PHP">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category">
                                <option value="">Select category</option>
                                <option>Web Development</option>
                                <option>UI/UX Design</option>
                                <option>Graphic Design</option>
                                <option>Content Writing</option>
                                <option>Digital Marketing</option>
                                <option>Video Editing</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="budget">Budget</label>
                            <input type="text" id="budget" name="budget" placeholder="Example: $300">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="experience">Experience Level</label>
                            <select id="experience" name="experience">
                                <option value="">Select experience level</option>
                                <option>Beginner</option>
                                <option>Intermediate</option>
                                <option>Expert</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="job_type">Project Type</label>
                            <select id="job_type" name="job_type">
                                <option value="">Select type</option>
                                <option>Fixed Price</option>
                                <option>Hourly</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="skills">Required Skills</label>
                        <input type="text" id="skills" name="skills" placeholder="Example: PHP, HTML, CSS, Responsive Design">
                    </div>

                    <div class="form-group">
                        <label for="description">Project Description</label>
                        <textarea id="description" name="description" placeholder="Explain project scope, pages/modules needed, style expectations, and delivery goals"></textarea>
                    </div>

                    <button type="submit" class="auth-submit">Publish Job</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>