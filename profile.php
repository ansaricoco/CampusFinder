<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// === FORM SUBMISSION HANDLER ===
if (isset($_POST['update'])) {
    $new_name = trim($_POST['name']);
    $new_student_number = trim($_POST['student_number']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['profile_image']['name']);
        $target_path = "images/" . $image_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
            $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE user_id = ?");
            $stmt->bind_param("si", $image_name, $user_id);
            $stmt->execute();
        }
    }

    // Update user info
    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, student_number = ?, email = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $new_name, $new_student_number, $new_email, $hashed, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, student_number = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $new_name, $new_student_number, $new_email, $user_id);
    }

    $stmt->execute();
}

// Fetch updated user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - CampusFinder</title>
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

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header .user-info img {
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

        .profile-card {
            background: #202123;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.3);
            color: #d1d5db;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3c3f4a;
            margin-bottom: 10px;
        }

        form { text-align: left; }
        .form-group {
            margin: 15px 0;
        }

        label { font-weight: 600; display: block; margin-bottom: 5px; color: #d1d5db; }
        input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #3c3f4a;
            background: #2a2b32;
            color: #d1d5db;
        }

        .btn {
            margin-top: 15px;
            padding: 10px 20px;
            background: #10a37f;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn:hover {
            background: #0e8a6d;
        }
    </style>
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="main-content">
    <div class="header">
        <h1>My Profile</h1>
        <div class="user-info">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.jfif') ?>" alt="User">
            <span><?= htmlspecialchars($user['first_name']) ?></span>
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </div>

    <div class="profile-card">
        <form method="POST" enctype="multipart/form-data">
            <img src="images/<?= htmlspecialchars($user['profile_photo'] ?? 'default.png') ?>" class="profile-img" alt="Profile Image"><br>
            <input type="file" name="profile_image" accept="image/*">

            <h2><?= htmlspecialchars($user['first_name'] ?? '') ?></h2>

            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Student Number:</label>
                <input type="text" name="student_number" value="<?= htmlspecialchars($user['student_number'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password">
            </div>

            <button class="btn" type="submit" name="update">Save Changes</button>
        </form>
    </div>
</div>
</body>
</html>
