<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Delete related messages first (both sender and receiver)
    $msgStmt = $conn->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?");
    $msgStmt->bind_param("ii", $id, $id);
    $msgStmt->execute();

    // Now delete the user
    $del = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    header("Location: admin_users.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete User - CampusFinder</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { margin: 0; display: flex; background: #f4f6f9; }

        .sidebar {
            width: 240px; background-color: #2c3e50; color: white;
            min-height: 100vh; padding: 20px;
            display: flex; flex-direction: column; justify-content: space-between;
        }

        .sidebar h2 { margin-bottom: 30px; }
        .sidebar a {
            display: block; color: #ecf0f1; padding: 12px; text-decoration: none;
            margin-bottom: 10px; border-radius: 4px;
        }
        .sidebar a:hover { background-color: #34495e; }

        .main-content { flex: 1; padding: 20px; }

        .header {
            background: white; display: flex; justify-content: space-between;
            align-items: center; padding: 15px 25px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1); margin-bottom: 20px;
        }

        .header h1 { font-size: 24px; color: #333; }

        .form-box {
            max-width: 500px; background: white; padding: 30px;
            margin: auto; border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .form-box h2 { color: #c0392b; margin-bottom: 20px; }

        .form-box p {
            font-size: 16px; color: #444; margin-bottom: 30px;
        }

        .btns {
            display: flex; gap: 10px; justify-content: center;
        }

        button {
            padding: 10px 20px; border: none; border-radius: 5px;
            cursor: pointer; font-size: 15px;
        }

        .delete-btn {
            background-color: #e74c3c; color: white;
        }

        .cancel-btn {
            background-color: #7f8c8d; color: white;
            text-decoration: none; line-height: 38px;
        }

        .delete-btn:hover { background-color: #c0392b; }
        .cancel-btn:hover { background-color: #616a6b; }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="header">
        <h1>Delete User</h1>
    </div>

    <div class="form-box">
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>?</p>

        <form method="POST">
            <div class="btns">
                <button type="submit" class="delete-btn">Yes, Delete</button>
                <a href="admin_users.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
