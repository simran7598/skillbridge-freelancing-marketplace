<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.php");
    exit;
}

include('../includes/db.php');

$message = "";
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $freelancerId = $_SESSION['user_id'];
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $deliveryTime = trim($_POST["delivery_time"] ?? "");
    $imageName = null;

    if ($title === "" || $description === "" || $price === "" || $deliveryTime === "") {
        $message = "Please fill all required fields.";
    } else {
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === 0) {
            $tmpName = $_FILES['service_image']['tmp_name'];
            $originalName = basename($_FILES['service_image']['name']);
            $imageName = time() . "_" . $originalName;
            $targetPath = "../uploads/services/" . $imageName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                $imageName = null;
            }
        }

        $sql = "INSERT INTO services (freelancer_id, title, description, price, delivery_time, image)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $freelancerId, $title, $description, $price, $deliveryTime, $imageName);

        if ($stmt->execute()) {
            $isSuccess = true;
            $message = "Service added successfully.";
        } else {
            $message = "Error adding service: " . $stmt->error;
        }
    }
}

include('../includes/header.php');
include('../includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container dashboard-layout">
        <?php include('../includes/sidebar-freelancer.php'); ?>

        <div class="dashboard-content">
            <div class="dashboard-header-card">
                <h1>Add Service</h1>
                <p>Create a service that clients can discover and order.</p>
            </div>

            <div class="form-card">
                <?php if ($message != ""): ?>
                    <div class="alert-box <?php echo $isSuccess ? 'alert-success' : 'alert-info'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="auth-form">
                    <div class="form-group">
                        <label for="title">Service Title</label>
                        <input type="text" id="title" name="title" placeholder="I will build a responsive PHP website" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Service Description</label>
                        <textarea id="description" name="description" placeholder="Describe your service" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="text" id="price" name="price" placeholder="$80" required>
                        </div>

                        <div class="form-group">
                            <label for="delivery_time">Delivery Time</label>
                            <input type="text" id="delivery_time" name="delivery_time" placeholder="5 Days" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="service_image">Service Image</label>
                        <input type="file" id="service_image" name="service_image" accept="image/*">
                    </div>

                    <button type="submit" class="auth-submit">Add Service</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>