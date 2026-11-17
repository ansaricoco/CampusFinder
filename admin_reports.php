<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include("config.php");

// Handle delete logic if this page will also process deletions (optional)
// ...

// Fetch reports
$found_items = $conn->query("SELECT id, item_name, location, description, date_found AS date, 'found' as type FROM found_items");
$missing_items = $conn->query("SELECT id, item_name, location, description, date_lost AS date, 'missing' as type FROM missing_items");

$reports = [];
while ($row = $found_items->fetch_assoc()) {
    $reports[] = $row;
}
while ($row = $missing_items->fetch_assoc()) {
    $reports[] = $row;
}

// Sort by date descending
usort($reports, function ($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Reports - CampusFinder Admin</title>
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

        h2 {
            margin: 20px 0;
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

        .edit-btn:hover {
            background: #2980b9;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background: #c0392b;
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
        <h1>Manage Reports</h1>
    </div>

    <h2>All Reports</h2>
    <?php if (count($reports) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= ucfirst($row['type']) ?></td>
                        <td>
                            <a class="action-btn edit-btn" href="admin_edit_item.php?id=<?= $row['id'] ?>&type=<?= $row['type'] ?>">Edit</a>
                            <a class="action-btn delete-btn" href="admin_delete_item.php?id=<?= $row['id'] ?>&type=<?= $row['type'] ?>" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No reports found.</p>
    <?php endif; ?>
</div>
</body>
</html>
