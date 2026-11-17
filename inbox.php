<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get messages received
$inbox = $conn->prepare("
    SELECT m.*, u.first_name AS sender_name, u.user_id AS sender_id 
    FROM messages m 
    JOIN users u ON m.sender_id = u.user_id 
    WHERE m.receiver_id = ? 
    ORDER BY m.created_at DESC
");
$inbox->bind_param("i", $user_id);
$inbox->execute();
$result = $inbox->get_result();

// Get user info
$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inbox - CampusFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: #343541; color: #d1d5db; }

        .sidebar {
            width: 240px;
            background: #202123;
            min-height: 100vh;
            padding: 20px;
            color: white;
            position: fixed;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 20px;
            text-align: center;
            color: #10a37f;
        }

        .sidebar a {
            color: #d1d5db;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .sidebar a:hover { background: #343541; }

        .main-content {
            margin-left: 240px;
            width: calc(100% - 240px);
            padding: 20px;
        }

        .header {
            background: #202123;
            padding: 15px 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .logout-btn {
            padding: 8px 12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .section {
            background: #202123;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.2);
        }

        .section h2 {
            margin-bottom: 15px;
            color: #10a37f;
        }

        .message {
            padding: 15px;
            border-bottom: 1px solid #3c3f4a;
        }

        .message:last-child {
            border-bottom: none;
        }

        .message strong {
            display: block;
            margin-bottom: 5px;
            color: #ffffff;
        }

        .message em {
            font-size: 12px;
            color: #a0a0a0;
        }

        .new-msg-link {
            display: inline-block;
            margin-bottom: 15px;
            text-decoration: none;
            background: #3498db;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
        }

        .new-msg-link:hover {
            background: #2980b9;
        }

        .reply-btn {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 10px;
            background: #10a37f;
            color: white;
            border-radius: 5px;
            font-size: 13px;
            text-decoration: none;
        }

        .reply-btn:hover {
            background: #0e8a6d;
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="header">
        <h1>Inbox</h1>
        <div class="user-info">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.png') ?>" alt="User">
            <?= htmlspecialchars($user['first_name']) ?>
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </div>

    <div class="section">
        <a class="new-msg-link" href="send_message.php">➕ New Message</a>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($msg = $result->fetch_assoc()): ?>
                <div class="message">
                    <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong>
                    <?= htmlspecialchars($msg['message']) ?>
                    <br><em><?= $msg['created_at'] ?></em>
                    <br>
                    <a class="reply-btn" href="send_message.php?to=<?= $msg['sender_id'] ?>">↩️ Reply</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #a0a0a0;">No messages yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
