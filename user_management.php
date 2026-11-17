<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management - CampusFinder</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            display: flex;
        }
        .sidebar {
            width: 240px;
            background-color: #2c3e50;
            color: white;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar h2 {
            color: #fff;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: #ecf0f1;
            padding: 12px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .header {
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .user-table th, .user-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .user-table th {
            background: #2c3e50;
            color: white;
        }
        .user-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include("sidebar.php"); ?>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1>User Management</h1>
    </div>

    <table class="user-table">
        <tr>
            <th>Photo</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Birthdate</th>
            <th>Registered</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="images/<?php echo htmlspecialchars($row['profile_photo'] ?: 'default.png'); ?>" class="user-photo"></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td><?php echo htmlspecialchars($row['birthdate']); ?></td>
            <td><?php echo htmlspecialchars(date("M d, Y", strtotime($row['created_at']))); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
