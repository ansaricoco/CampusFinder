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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $date_found = $_POST['date_found'];
    $time_found = $_POST['time_found'];
    $status = "Found";
    $image = "";

    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target_dir = "images/";
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    $stmt = $conn->prepare("INSERT INTO found_items 
        (user_id, item_name, description, location, date_found, time_found, image, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("isssssss", $user_id, $item_name, $description, $location, $date_found, $time_found, $image, $status);
    $stmt->execute();

    header("Location: main.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Found Item - CampusFinder</title>
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

        .form-container {
            background: #202123;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            color: #d1d5db;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
            color: #10a37f;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #d1d5db;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #3c3f4a;
            background: #2a2b32;
            color: #d1d5db;
        }

        textarea {
            resize: vertical;
        }

        button {
            padding: 12px 20px;
            background: #10a37f;
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }

        button:hover {
            background: #0e8a6d;
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="header">
        <h1>Report Found Item</h1>
        <div class="user-info">
            <img src="images/<?php echo htmlspecialchars($user['profile_photo'] ?? 'default.jfif'); ?>" alt="User">
            <span><?php echo htmlspecialchars($user['first_name']); ?></span>
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </div>

    <div class="form-container">
        <h2>Report a Found Item</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="item_name">Item Name:</label>
                <input type="text" name="item_name" id="item_name" required>
            </div>

            <div class="form-group">
                <label for="location">Location Found:</label>
                <input type="text" name="location" id="location" required>
            </div>

            <div class="form-group">
                <label for="date_found">Date Found:</label>
                <input type="date" name="date_found" id="date_found" required>
            </div>

            <div class="form-group">
                <label for="time_found">Time Found:</label>
                <input type="time" name="time_found" id="time_found" required>
            </div>

            <div class="form-group">
                <label for="image">Item Photo (optional):</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>
            </div>

            <button type="submit">Submit Report</button>
        </form>
    </div>
</div>
</body>
</html>
