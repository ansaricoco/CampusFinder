<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

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
    <title>Dashboard - CampusFinder</title>
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
            border: none;
            border-radius: 6px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #c0392b;
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

        .box a {
            text-decoration: none;
            color: #10a37f;
            font-size: 18px;
            font-weight: bold;
        }

        h2 { color: #fff; }

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
<?php include("sidebar.php"); ?>
<div class="main-content">
    <div class="header">
        <h1>Dashboard</h1>
        <div class="user-info">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.jfif') ?>" alt="User">
            <span><?= htmlspecialchars($user['first_name']) ?></span>
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </div>
    <div class="dashboard-boxes">
        <div class="box"><a href="add_missing_item.php">➕ Add Missing Item</a></div>
        <div class="box"><a href="report_found_item.php">📍 Report Found Item</a></div>
    </div>
    <h2 style="margin: 20px 0;"> Recently Reported Items</h2>
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
