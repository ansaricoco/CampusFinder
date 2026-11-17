<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include("config.php");

// Count total users
$user_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// Count total reports
$report_count_query = "
    SELECT (
        (SELECT COUNT(*) FROM missing_items) + 
        (SELECT COUNT(*) FROM found_items)
    ) AS total
";
$report_count = $conn->query($report_count_query)->fetch_assoc()['total'];

// Load recent 10 items
$items = $conn->query("
    SELECT fi.item_name, fi.description, fi.image, fi.location, fi.date_found AS date, fi.time_found AS time, fi.status, u.student_number, 'Found' AS type
    FROM found_items fi
    JOIN users u ON fi.user_id = u.user_id
    UNION
    SELECT mi.item_name, mi.description, mi.image, mi.location, mi.date_lost AS date, mi.time_lost AS time, mi.status, u.student_number, 'Lost' AS type
    FROM missing_items mi
    JOIN users u ON mi.user_id = u.user_id
    ORDER BY date DESC, time DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - CampusFinder</title>
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

        .dashboard-boxes {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .box {
            flex: 1;
            background: #202123;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #3c3f4a;
        }

        .box h3 {
            font-size: 20px;
            color: #fff;
            margin-bottom: 10px;
        }

        .box p {
            font-size: 28px;
            color: #10a37f;
            font-weight: bold;
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

        .status {
            padding: 6px 10px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            display: inline-block;
        }

        .missing { background: #e74c3c; }
        .found { background: #3498db; }
        .unclaimed { background: #f1c40f; color: #333; }
        .claimed { background: #2ecc71; }

        .view-btn {
            background: #10a37f;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }

        .view-btn:hover {
            background: #0e8a6d;
        }

        #popupModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.7);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        #popupModal .content {
            background: #202123;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            position: relative;
            text-align: center;
            color: #d1d5db;
        }

        #popupModal img {
            max-width: 100%;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        #popupModal span.close {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-weight: bold;
            font-size: 20px;
            color: #fff;
        }

        h2 {
            margin: 20px 0;
        }
    </style>
    <script>
        function showDetails(image, description) {
            document.getElementById('popupImage').src = "images/" + image;
            document.getElementById('popupDescription').innerText = description;
            document.getElementById('popupModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('popupModal').style.display = 'none';
        }
    </script>
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
        <h1>Admin Dashboard</h1>
    </div>

    <div class="dashboard-boxes">
        <div class="box">
            <h3>Total Users</h3>
            <p><?= $user_count ?></p>
        </div>
        <div class="box">
            <h3>Total Reports</h3>
            <p><?= $report_count ?></p>
        </div>
    </div>

    <h2>Recently Reported Items</h2>
    <?php if ($items->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Student No.</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['student_number']) ?></td>
                        <td><?= $row['type'] ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td>
                            <span class="status <?= strtolower($row['status']) ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="#" class="view-btn" onclick="showDetails('<?= htmlspecialchars($row['image']) ?>', `<?= htmlspecialchars($row['description']) ?>`); return false;">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items reported yet.</p>
    <?php endif; ?>
</div>

<div id="popupModal" onclick="closeModal()">
    <div class="content" onclick="event.stopPropagation();">
        <span class="close" onclick="closeModal()">×</span>
        <img id="popupImage" src="" alt="Item Image">
        <p id="popupDescription"></p>
    </div>
</div>
</body>
</html>
