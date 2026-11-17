<?php
session_start();
include("config.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$edit_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch user to edit
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $edit_id);
$stmt->execute();
$result = $stmt->get_result();
$edit_user = $result->fetch_assoc();

if (!$edit_user) {
    echo "<p style='padding: 20px;'>User not found.</p>";
    exit;
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $student_number = trim($_POST['student_number']);
    $email = trim($_POST['email']);

    $update = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, student_number = ?, email = ? WHERE user_id = ?");
    $update->bind_param("ssssi", $first_name, $last_name, $student_number, $email, $edit_id);
    $update->execute();
    header("Location: admin_users.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User - CampusFinder Admin</title>
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

        input {
            width: 100%;
            padding: 10px;
            background: #2a2b32;
            border: none;
            border-radius: 5px;
            color: #d1d5db;
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
    <h2>Edit User</h2>
    <form method="POST">
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" required value="<?= htmlspecialchars($edit_user['first_name']) ?>">

        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" required value="<?= htmlspecialchars($edit_user['last_name']) ?>">

        <label for="student_number">Student Number</label>
        <input type="text" name="student_number" required value="<?= htmlspecialchars($edit_user['student_number']) ?>">

        <label for="email">Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($edit_user['email']) ?>">

        <button type="submit" class="btn">Save Changes</button>
        <a href="admin_users.php" class="back-link">Cancel</a>
    </form>
</div>
</body>
</html>
