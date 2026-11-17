<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include("config.php");

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete related messages first
    $stmt1 = $conn->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?");
    $stmt1->bind_param("ii", $id, $id);
    $stmt1->execute();

    // Delete related found_items and missing_items if they exist
    $conn->query("DELETE FROM found_items WHERE user_id = $id");
    $conn->query("DELETE FROM missing_items WHERE user_id = $id");

    // Now delete the user
    $stmt2 = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    header("Location: admin_users.php");
    exit;
}


$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - CampusFinder Admin</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            background: #202123;
            border-radius: 10px;
            overflow: hidden;
            color: #d1d5db;
        }

        th, td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #3c3f4a;
        }

        th {
            background: #2a2b32;
            font-weight: bold;
        }

        .action-btn {
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            margin: 0 5px;
        }

        .edit-btn {
            background: #3498db;
            color: white;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .edit-btn:hover {
            background: #2980b9;
        }

        h2 {
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>CampusFinder</h2>
    <a href="admin.php">Dashboard</a>
    <a href="admin_users.php">Manage Users</a>
    <a href="admin_reports.php">Manage Reports</a>
    <a href="admin_logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Manage Users</h1>
    </div>

    <h2>Registered Users</h2>
    <?php if ($users->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Student Number</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= htmlspecialchars($row['student_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <a class="action-btn edit-btn" href="edit_user.php?id=<?= $row['user_id'] ?>">Edit</a>  
                            <a class="action-btn delete-btn" href="admin_users.php?delete=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>
</body>
</html>
