<?php
include("config.php");
include("auth.php");

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_items.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM found_items WHERE id = ? AND user_id = ?");
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
    $date_found = $_POST['date_found'];

    $stmt = $conn->prepare("UPDATE found_items SET item_name = ?, description = ?, location = ?, date_found = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssii", $item_name, $description, $location, $date_found, $id, $user_id);
    $stmt->execute();

    header("Location: manage_items.php");
    exit();
}

$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Found Item - CampusFinder</title>
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
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .form-container {
            background: #202123;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
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
            font-weight: 600;
            margin-bottom: 5px;
            color: #d1d5db;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #555;
            background: #343541;
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
        <h1>Edit Found Item</h1>
        <div class="user-info">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.png') ?>" alt="User">
            <?= htmlspecialchars($user['first_name']) ?>
        </div>
    </div>

    <div class="form-container">
        <h2>Edit Found Item</h2>
        <form method="POST">
            <div class="form-group">
                <label for="item_name">Item Name:</label>
                <input type="text" name="item_name" id="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location Found:</label>
                <input type="text" name="location" id="location" value="<?= htmlspecialchars($item['location']) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_found">Date Found:</label>
                <input type="date" name="date_found" id="date_found" value="<?= $item['date_found'] ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>

            <button type="submit">Update</button>
        </form>
    </div>
</div>

</body>
</html>
