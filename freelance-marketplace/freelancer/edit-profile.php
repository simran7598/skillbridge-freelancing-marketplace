<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$message = "";
$userId = $_SESSION['user_id'];

$sql = "SELECT users.full_name, users.email, users.profile_image,
               freelancer_profiles.title, freelancer_profiles.bio,
               freelancer_profiles.skills, freelancer_profiles.hourly_rate,
               freelancer_profiles.experience_level, freelancer_profiles.location
        FROM users
        LEFT JOIN freelancer_profiles ON users.id = freelancer_profiles.user_id
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $title = trim($_POST["title"] ?? "");
    $bio = trim($_POST["bio"] ?? "");
    $skills = trim($_POST["skills"] ?? "");
    $hourlyRate = trim($_POST["hourly_rate"] ?? "");
    $experienceLevel = trim($_POST["experience_level"] ?? "");
    $location = trim($_POST["location"] ?? "");

    $profileImageName = $profile['profile_image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $imageTmp = $_FILES['profile_image']['tmp_name'];
        $imageName = time() . "_" . basename($_FILES['profile_image']['name']);
        $targetPath = "../uploads/profiles/" . $imageName;

        if (move_uploaded_file($imageTmp, $targetPath)) {
            $profileImageName = $imageName;
        }
    }

    $updateUser = "UPDATE users SET full_name = ?, profile_image = ? WHERE id = ?";
    $stmt1 = $conn->prepare($updateUser);
    $stmt1->bind_param("ssi", $fullName, $profileImageName, $userId);
    $stmt1->execute();

    $updateProfile = "UPDATE freelancer_profiles
                      SET title = ?, bio = ?, skills = ?, hourly_rate = ?, experience_level = ?, location = ?
                      WHERE user_id = ?";
    $stmt2 = $conn->prepare($updateProfile);
    $stmt2->bind_param("sssdssi", $title, $bio, $skills, $hourlyRate, $experienceLevel, $location, $userId);
    $stmt2->execute();

    $_SESSION['full_name'] = $fullName;
    $message = "Profile updated successfully.";

    header("Location: edit-profile.php?success=1");
    exit;
}

if (isset($_GET['success'])) {
    $message = "Profile updated successfully.";
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-freelancer.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Edit Freelancer Profile</h1>
                <p>Update your professional profile, skills, rates, and profile photo.</p>
            </div>

            <div class="form-card">
                <?php if ($message != ""): ?>
                    <div class="alert-box alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="auth-form">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="title">Professional Title</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($profile['title'] ?? ''); ?>" placeholder="Full Stack PHP Developer">
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" placeholder="Write your professional summary"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="skills">Skills</label>
                        <input type="text" id="skills" name="skills" value="<?php echo htmlspecialchars($profile['skills'] ?? ''); ?>" placeholder="PHP, HTML, CSS, MySQL">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="hourly_rate">Hourly Rate</label>
                            <input type="number" step="0.01" id="hourly_rate" name="hourly_rate" value="<?php echo htmlspecialchars($profile['hourly_rate'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="experience_level">Experience Level</label>
                            <input type="text" id="experience_level" name="experience_level" value="<?php echo htmlspecialchars($profile['experience_level'] ?? ''); ?>" placeholder="Intermediate">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="profile_image">Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    </div>

                    <?php if (!empty($profile['profile_image'])): ?>
                        <div class="form-group">
                            <img src="<?php echo url('uploads/profiles/' . $profile['profile_image']); ?>" alt="Profile Image" class="profile-img">
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="auth-submit">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>