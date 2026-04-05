<?php
include('includes/db.php');

$message = "";
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $newPassword = trim($_POST["new_password"] ?? "");
    $confirmPassword = trim($_POST["confirm_password"] ?? "");

    if ($email === "" || $newPassword === "" || $confirmPassword === "") {
        $message = "Please fill in all fields.";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "New password and confirm password do not match.";
    } else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $message = "No account found with this email.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateSql = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("ss", $hashedPassword, $email);

            if ($stmt->execute()) {
                $isSuccess = true;
                $message = "Password updated successfully. You can now login.";
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}

include('includes/header.php');
include('includes/navbar.php');
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-left">
                <h2>Reset Your Password</h2>
                <p>
                    Enter your registered email and set a new password to regain access
                    to your SkillBridge account.
                </p>

                <div class="auth-features">
                    <div class="auth-feature-item">Works directly with your database</div>
                    <div class="auth-feature-item">Updates password securely using hashing</div>
                    <div class="auth-feature-item">Lets you login again immediately</div>
                </div>
            </div>

            <div class="auth-right">
                <div class="auth-header">
                    <h1>Forgot Password</h1>
                    <p>Reset your account password below.</p>
                </div>

                <?php if ($message != ""): ?>
                    <div class="alert-box <?php echo $isSuccess ? 'alert-success' : 'alert-info'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email">Registered Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>

                    <button type="submit" class="auth-submit">Reset Password</button>

                    <div class="auth-links">
                        <a href="login.php">Back to Login</a>
                        <a href="register.php">Create New Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>