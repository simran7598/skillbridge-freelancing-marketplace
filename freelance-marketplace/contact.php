<?php
include('includes/db.php');

$message = "";
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $messageText = trim($_POST["message"] ?? "");

    if ($name === "" || $email === "" || $subject === "" || $messageText === "") {
        $message = "Please fill in all contact form fields.";
    } else {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $subject, $messageText);

        if ($stmt->execute()) {
            $isSuccess = true;
            $message = "Your message has been submitted successfully.";
        } else {
            $message = "Something went wrong. Please try again.";
        }
    }
}

include('includes/header.php');
include('includes/navbar.php');
?>

<section class="page-banner">
    <div class="container">
        <h1>Contact Us</h1>
        <p>
            Reach out to the SkillBridge team for support, project questions,
            and platform-related assistance.
        </p>
    </div>
</section>

<section class="content-section">
    <div class="container contact-layout">
        <div class="form-card">
            <div class="section-head">
                <div>
                    <h2>Send a Message</h2>
                    <p>We would love to hear from you</p>
                </div>
            </div>

            <?php if ($message != ""): ?>
                <div class="alert-box <?php echo $isSuccess ? 'alert-success' : 'alert-info'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Enter message subject" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Write your message here" required></textarea>
                </div>

                <button type="submit" class="auth-submit">Submit Message</button>
            </form>
        </div>

        <div class="contact-card">
            <h3>Contact Information</h3>

            <div class="contact-info-list">
                <div class="contact-info-item">
                    <h4>Email Support</h4>
                    <p>support@skillbridge.com</p>
                </div>

                <div class="contact-info-item">
                    <h4>Business Inquiries</h4>
                    <p>business@skillbridge.com</p>
                </div>

                <div class="contact-info-item">
                    <h4>Office Location</h4>
                    <p>New Delhi, India</p>
                </div>

                <div class="contact-info-item">
                    <h4>Response Time</h4>
                    <p>Within 24 business hours</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>