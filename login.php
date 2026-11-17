<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_number = $_POST['student_number'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE student_number = ?");
    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: main.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Student number not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - CampusFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .form-box {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            background-color: #10a37f;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #0e8e6b;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 14px;
        }
        .register-link a {
            color: #10a37f;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <div class="form-group">
            <input type="text" name="student_number" placeholder="Student Number" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <div class="register-link">
        <p>Don't have an account? <a href="register.php">Create one</a></p>
    </div>
    <div class="admin-box" style="margin-top: 2.5rem; padding: 0.5em; border: 1px solid #ccc; border-radius: 8px; background: #f9f9f9; text-align: center;">
        <a href="admin_login.php" style="text-decoration: none; color: #10a37f; font-weight: 600;">Access Admin</a>
    </div>
</div>
</body>
</html>
