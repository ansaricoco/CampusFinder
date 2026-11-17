<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include("config.php");

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: admin_reports.php");
    exit;
}

$id = intval($_GET['id']);
$type = $_GET['type'];

$table = ($type === 'found') ? 'found_items' : 'missing_items';
$date_column = ($type === 'found') ? 'date_found' : 'date_lost';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE $table SET item_name = ?, location = ?, description = ?, $date_column = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $item_name, $location, $description, $date, $id);
    $stmt->execute();
    header("Location: admin_reports.php");
    exit;
}

$item = $conn->query("SELECT * FROM $table WHERE id = $id")->fetch_assoc();
$date_value = ($type === 'found') ? $item['date_found'] : $item['date_lost'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Report - CampusFinder Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #343541;
            color: #d1d5db;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #202123;
            padding: 30px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #10a37f;
        }

        label {
            display: block;
            margin: 15px 0 5px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            background: #2a2b32;
            border: none;
            border-radius: 5px;
            color: #d1d5db;
        }

        textarea {
            height: 100px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #10a37f;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #0e8a6e;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #10a37f;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit <?= ucfirst($type) ?> Report</h2>
    <form method="post">
        <label>Item Name</label>
        <input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>

        <label>Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($item['location']) ?>" required>

        <label>Description</label>
        <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>

        <label>Date <?= ($type === 'found') ? 'Found' : 'Lost' ?></label>
        <input type="date" name="date" value="<?= htmlspecialchars($date_value) ?>" required>

        <button class="btn" type="submit">Update Report</button>
        <a href="admin_reports.php" class="back-link">Cancel</a>
    </form>
</div>
</body>
</html>
