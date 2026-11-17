<?php
session_start();
include("config.php");

$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($step) {
        case 1:
            $_SESSION['first_name'] = $_POST['first_name'];
            $_SESSION['last_name'] = $_POST['last_name'];
            $step = 2;
            break;
        case 2:
            $_SESSION['birthdate'] = $_POST['birthdate'];
            $_SESSION['gender'] = $_POST['gender'];
            $step = 3;
            break;
        case 3:
            $_SESSION['student_number'] = $_POST['student_number'];
            $_SESSION['year_level'] = $_POST['year_level'];
            $step = 4;
            break;
        case 4:
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];

            if ($password !== $confirm) {
                $error = "Passwords do not match.";
            } else {
                $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                $check->store_result();
                if ($check->num_rows > 0) {
                    $error = "Email already exists.";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users 
                        (first_name, last_name, birthdate, gender, student_number, year_level, email, password) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param(
                        "ssssssss",
                        $_SESSION['first_name'],
                        $_SESSION['last_name'],
                        $_SESSION['birthdate'],
                        $_SESSION['gender'],
                        $_SESSION['student_number'],
                        $_SESSION['year_level'],
                        $email,
                        $hashed
                    );
                    $stmt->execute();
                    session_destroy();
                    header("Location: login.php");
                    exit;
                }
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - CampusFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .form-card {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
        }
        input, select {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .btn {
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
        .btn:hover {
            background-color: #0e8e6b;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="form-card">
    <h2>Create Account</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="hidden" name="step" value="<?php echo $step; ?>">
        <?php if ($step == 1): ?>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name (optional)</label>
                <input type="text" name="last_name">
            </div>
        <?php elseif ($step == 2): ?>
            <div class="form-group">
                <label>Birthday</label>
                <input type="date" name="birthdate" required>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="">Select gender</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Prefer not to say</option>
                </select>
            </div>
        <?php elseif ($step == 3): ?>
            <div class="form-group">
                <label>Student Number</label>
                <input type="text" name="student_number" required>
            </div>
            <div class="form-group">
                <label>Year Level</label>
                <select name="year_level" required>
                    <option value="">Select year level</option>
                    <option>1st Year</option>
                    <option>2nd Year</option>
                    <option>3rd Year</option>
                    <option>4th Year</option>
                </select>
            </div>
        <?php elseif ($step == 4): ?>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
        <?php endif; ?>
        <button class="btn" type="submit"><?php echo $step < 4 ? 'Next' : 'Create Account'; ?></button>
    </form>
</div>
</body>
</html>
