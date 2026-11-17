<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$missing_stmt = $conn->prepare("SELECT * FROM missing_items WHERE user_id = ? ORDER BY created_at DESC");
$missing_stmt->bind_param("i", $user_id);
$missing_stmt->execute();
$missing_items = $missing_stmt->get_result();

$found_stmt = $conn->prepare("SELECT * FROM found_items WHERE user_id = ? ORDER BY created_at DESC");
$found_stmt->bind_param("i", $user_id);
$found_stmt->execute();
$found_items = $found_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage My Items - CampusFinder</title>
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

        .sidebar h2 { margin-bottom: 30px; font-size: 20px; text-align: center; color: #10a37f; }
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
            border-radius: 6px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .section {
            background: #202123;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.2);
        }

        h2 {
            margin-bottom: 15px;
            color: #10a37f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #2a2b32;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #3c3f4a;
            text-align: left;
        }

        th {
            background: #2f3136;
            color: #10a37f;
        }

        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 5px;
            color: white;
        }

        .edit-btn { background-color: #3498db; }
        .delete-btn { background-color: #e74c3c; }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="header">
        <h1>Manage My Items</h1>
        <div class="user-info">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.jfif') ?>" alt="User">
            <span><?= htmlspecialchars($user['first_name']) ?></span>
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </div>

    <div class="section">
        <h2>🔍 My Lost Items</h2>
        <?php if ($missing_items->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Location</th>
                    <th>Date Lost</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $missing_items->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['date_lost']) ?></td>
                        <td>
                            <a class="action-btn edit-btn" href="edit_missing_item.php?id=<?= $row['id'] ?>">Edit</a>
                            <a class="action-btn delete-btn" href="delete_missing_item.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No lost items reported.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>📌 My Found Items</h2>
        <?php if ($found_items->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Location</th>
                    <th>Date Found</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $found_items->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['date_found']) ?></td>
                        <td>
                            <a class="action-btn edit-btn" href="edit_found_item.php?id=<?= $row['id'] ?>">Edit</a>
                            <a class="action-btn delete-btn" href="delete_found_item.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No found items reported.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
