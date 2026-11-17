<?php
include("config.php");
include("auth.php");

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch user
$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Fetch item info
$stmt = $conn->prepare("SELECT * FROM found_items WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "<p style='padding: 20px;'>Item not found or you are not authorized to delete it.</p>";
    exit();
}

// Handle deletion after confirmation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $delete = $conn->prepare("DELETE FROM found_items WHERE id = ? AND user_id = ?");
    $delete->bind_param("ii", $id, $user_id);
    $delete->execute();
    header("Location: manage_items.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Found Item - CampusFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: #343541; color: #d1d5db; }

        .sidebar {
            width: 240px;
            background: #202123;
            min-height: 100vh;
            padding: 20px;
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

        .sidebar a:hover {
            background: #343541;
        }

        .main-content {
            margin-left: 240px;
            width: calc(100% - 240px);
            padding: 30px;
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

        .box {
            background: #202123;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #10a37f;
        }

        .item-details {
            margin-bottom: 30px;
            color: #a0a0a0;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .cancel-btn {
            background-color: #3b3b3f;
            color: #d1d5db;
            text-decoration: none;
            display: inline-block;
            line-height: 38px;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .cancel-btn:hover {
            background-color: #505050;
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="header">
        <h1>Delete Found Item</h1>
        <div class="user-info">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.png') ?>" alt="User">
            <?= htmlspecialchars($user['first_name']) ?>
        </div>
    </div>

    <div class="box">
        <h2>Are you sure you want to delete this found item?</h2>
        <div class="item-details">
            <p><strong>Item Name:</strong> <?= htmlspecialchars($item['item_name']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
            <p><strong>Date Found:</strong> <?= htmlspecialchars($item['date_found']) ?></p>
        </div>

        <form method="POST">
            <button type="submit" class="delete-btn">Yes, Delete</button>
            <a href="manage_items.php" class="cancel-btn">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
