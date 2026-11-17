<?php
include("config.php");
include("auth.php");

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_items.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch item
$stmt = $conn->prepare("SELECT * FROM missing_items WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    echo "Item not found or unauthorized.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $date_lost = $_POST['date_lost'];
    $time_lost = $_POST['time_lost'];

    $stmt = $conn->prepare("UPDATE missing_items SET item_name = ?, description = ?, location = ?, date_lost = ?, time_lost = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssiii", $item_name, $description, $location, $date_lost, $time_lost, $id, $user_id);
    $stmt->execute();

    header("Location: manage_items.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Missing Item - CampusFinder</title>
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

        .sidebar h2 { margin-bottom: 30px; font-size: 20px; color: #10a37f; text-align: center; }
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

        .form-container {
            background: #202123;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #10a37f;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
            color: #d1d5db;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #3c3f4a;
            background: #2d2f3a;
            color: #f3f4f6;
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
        <h1>Edit Missing Item</h1>
        <div class="user-info">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.png') ?>" alt="User">
            <?= htmlspecialchars($user['first_name']) ?>
        </div>
    </div>

    <div class="form-container">
        <h2>Update Lost Item</h2>
        <form method="POST">
            <div class="form-group">
                <label for="item_name">Item Name:</label>
                <input type="text" name="item_name" id="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location Last Seen:</label>
                <input type="text" name="location" id="location" value="<?= htmlspecialchars($item['location']) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_lost">Date Lost:</label>
                <input type="date" name="date_lost" id="date_lost" value="<?= $item['date_lost'] ?>" required>
            </div>

            <div class="form-group">
                <label for="time_lost">Time Lost:</label>
                <input type="time" name="time_lost" id="time_lost" value="<?= $item['time_lost'] ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>

            <button type="submit">Update Item</button>
        </form>
    </div>
</div>

</body>
</html>
