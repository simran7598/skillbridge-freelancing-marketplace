<?php
session_start();
include('includes/db.php');

$message = "";
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirmPassword = trim($_POST["confirm_password"] ?? "");
    $role = trim($_POST["role"] ?? "");

    if ($fullName === "" || $email === "" || $password === "" || $confirmPassword === "" || $role === "") {
        $message = "Please complete all required fields.";
    } elseif ($password !== $confirmPassword) {
        $message = "Password and confirm password do not match.";
    } else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email already registered. Please login instead.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertSql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("ssss", $fullName, $email, $hashedPassword, $role);

            if ($stmt->execute()) {
                $userId = $stmt->insert_id;

                if ($role === "freelancer") {
                    $profileSql = "INSERT INTO freelancer_profiles (user_id) VALUES (?)";
                    $profileStmt = $conn->prepare($profileSql);
                    $profileStmt->bind_param("i", $userId);
                    $profileStmt->execute();
                } elseif ($role === "client") {
                    $profileSql = "INSERT INTO client_profiles (user_id) VALUES (?)";
                    $profileStmt = $conn->prepare($profileSql);
                    $profileStmt->bind_param("i", $userId);
                    $profileStmt->execute();
                }

                $_SESSION['user_id'] = $userId;
                $_SESSION['full_name'] = $fullName;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;

                if ($role === "client") {
                    header("Location: client/dashboard.php");
                } elseif ($role === "freelancer") {
                    header("Location: freelancer/dashboard.php");
                } else {
                    header("Location: admin/dashboard.php");
                }
                exit;
            } else {
                $message = "Registration failed. Please try again.";
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
                <h2>Join the SkillBridge Marketplace</h2>
                <p>
                    Create your account as a client or freelancer and start building
                    real-world work opportunities on a professional platform.
                </p>

                <div class="auth-features">
                    <div class="auth-feature-item">Clients can post jobs and hire top talent</div>
                    <div class="auth-feature-item">Freelancers can build profiles and win projects</div>
                    <div class="auth-feature-item">Now connected with real database registration</div>
                </div>
            </div>

            <div class="auth-right">
                <div class="auth-header">
                    <h1>Create Account</h1>
                    <p>Register as a client or freelancer to start using the platform.</p>
                </div>

                <?php if ($message != ""): ?>
                    <div class="alert-box <?php echo $isSuccess ? 'alert-success' : 'alert-info'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label>Select Role</label>
                        <div class="role-options">
                            <label class="role-card">
                                <input type="radio" name="role" value="client" required>
                                <h4>Client</h4>
                                <p>Post jobs, hire freelancers, and manage projects.</p>
                            </label>

                            <label class="role-card">
                                <input type="radio" name="role" value="freelancer" required>
                                <h4>Freelancer</h4>
                                <p>Create profile, send proposals, and offer services.</p>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Create password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                        </div>
                    </div>

                    <button type="submit" class="auth-submit">Create Account</button>

                    <div class="auth-links">
                        <a href="login.php">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>