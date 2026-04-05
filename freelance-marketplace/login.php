<?php
session_start();
include('includes/db.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "" || $password === "") {
        $message = "Please fill in both email and password.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === "client") {
                    header("Location: client/dashboard.php");
                } elseif ($user['role'] === "freelancer") {
                    header("Location: freelancer/dashboard.php");
                } else {
                    header("Location: admin/dashboard.php");
                }
                exit;
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No account found with this email.";
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
                <h2>Welcome Back to SkillBridge</h2>
                <p>
                    Sign in to manage freelance jobs, proposals, services, projects,
                    and collaboration workflows from one place.
                </p>

                <div class="auth-features">
                    <div class="auth-feature-item">Access your client and freelancer dashboards</div>
                    <div class="auth-feature-item">Track proposals, jobs, and active projects</div>
                    <div class="auth-feature-item">Real database-based login is now active</div>
                </div>
            </div>

            <div class="auth-right">
                <div class="auth-header">
                    <h1>Login</h1>
                    <p>Enter your credentials to continue to your account.</p>
                </div>

                <?php if ($message != ""): ?>
                    <div class="alert-box alert-info">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="auth-submit">Login to Account</button>

                    <div class="auth-links">
                        <a href="forgot-password.php">Forgot Password?</a>
                        <a href="register.php">Create New Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>