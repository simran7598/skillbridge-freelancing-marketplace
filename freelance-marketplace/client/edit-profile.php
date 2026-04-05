<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$message = "";
$userId = $_SESSION['user_id'];

$sql = "SELECT users.full_name, users.email, users.profile_image,
               client_profiles.company_name, client_profiles.company_description,
               client_profiles.location
        FROM users
        LEFT JOIN client_profiles ON users.id = client_profiles.user_id
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $companyName = trim($_POST["company_name"] ?? "");
    $companyDescription = trim($_POST["company_description"] ?? "");
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

    $updateProfile = "UPDATE client_profiles
                      SET company_name = ?, company_description = ?, location = ?
                      WHERE user_id = ?";
    $stmt2 = $conn->prepare($updateProfile);
    $stmt2->bind_param("sssi", $companyName, $companyDescription, $location, $userId);
    $stmt2->execute();

    $_SESSION['full_name'] = $fullName;

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
        <?php include('../includes/sidebar-client.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Edit Client Profile</h1>
                <p>Update your account details, company information, and profile image.</p>
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
                        <label for="company_name">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="company_description">Company Description</label>
                        <textarea id="company_description" name="company_description"><?php echo htmlspecialchars($profile['company_description'] ?? ''); ?></textarea>
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