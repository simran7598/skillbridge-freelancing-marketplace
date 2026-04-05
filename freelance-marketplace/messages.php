<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('includes/db.php');

$currentUserId = $_SESSION['user_id'];
$selectedUserId = isset($_GET['user']) ? (int)$_GET['user'] : 0;
$messageText = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiverId = (int)($_POST['receiver_id'] ?? 0);
    $messageText = trim($_POST['message'] ?? "");

    if ($receiverId > 0 && $messageText !== "") {
        $insertSql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("iis", $currentUserId, $receiverId, $messageText);

        if ($stmt->execute()) {
            $success = "Message sent successfully.";
            header("Location: messages.php?user=" . $receiverId);
            exit;
        }
    }
}

$contactsSql = "SELECT DISTINCT u.id, u.full_name, u.role
                FROM users u
                WHERE u.id != ?
                ORDER BY u.full_name ASC";
$stmtContacts = $conn->prepare($contactsSql);
$stmtContacts->bind_param("i", $currentUserId);
$stmtContacts->execute();
$contactsResult = $stmtContacts->get_result();

$messagesResult = null;

if ($selectedUserId > 0) {
    $chatSql = "SELECT m.*, s.full_name AS sender_name, r.full_name AS receiver_name
                FROM messages m
                JOIN users s ON m.sender_id = s.id
                JOIN users r ON m.receiver_id = r.id
                WHERE (m.sender_id = ? AND m.receiver_id = ?)
                   OR (m.sender_id = ? AND m.receiver_id = ?)
                ORDER BY m.created_at ASC";
    $stmtChat = $conn->prepare($chatSql);
    $stmtChat->bind_param("iiii", $currentUserId, $selectedUserId, $selectedUserId, $currentUserId);
    $stmtChat->execute();
    $messagesResult = $stmtChat->get_result();
}

include('includes/header.php');
include('includes/navbar.php');
?>

<section class="dashboard-section">
    <div class="container">
        <div class="message-layout" style="background:#fff; border-radius:22px; overflow:hidden;">
            <div class="message-sidebar">
                <div class="chat-list">
                    <?php while ($contact = $contactsResult->fetch_assoc()): ?>
                        <a href="<?php echo url('messages.php?user=' . $contact['id']); ?>" class="chat-user" style="text-decoration:none; color:inherit;">
                            <h4><?php echo htmlspecialchars($contact['full_name']); ?></h4>
                            <p><?php echo htmlspecialchars(ucfirst($contact['role'])); ?></p>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="chat-window">
                <?php if ($selectedUserId > 0): ?>
                    <?php if ($messagesResult && $messagesResult->num_rows > 0): ?>
                        <?php while ($msg = $messagesResult->fetch_assoc()): ?>
                            <div class="chat-bubble <?php echo ($msg['sender_id'] == $currentUserId) ? 'client' : 'freelancer'; ?>">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No messages yet. Start the conversation.</p>
                    <?php endif; ?>

                    <form method="POST" class="chat-input-box">
                        <input type="hidden" name="receiver_id" value="<?php echo $selectedUserId; ?>">

                        <div class="form-group">
                            <label for="message">Type Message</label>
                            <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                        </div>

                        <button type="submit" class="auth-submit">Send Message</button>
                    </form>
                <?php else: ?>
                    <p>Select a user from the left to start chatting.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>