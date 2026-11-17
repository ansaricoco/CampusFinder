<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$default_student_number = '';

// Prefill student number if replying
if (isset($_GET['to'])) {
    $to_id = intval($_GET['to']);
    $stmt = $conn->prepare("SELECT student_number FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $to_id);
    $stmt->execute();
    $stmt->bind_result($default_student_number);
    $stmt->fetch();
    $stmt->close();
}

// Handle sending message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_number = trim($_POST['student_number']);
    $message = trim($_POST['message']);

    if (!empty($student_number) && !empty($message)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE student_number = ?");
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $stmt->bind_result($receiver_id);
        if ($stmt->fetch()) {
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
            $stmt->execute();
            header("Location: inbox.php");
            exit;
        } else {
            $error = "Student number not found.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Message - CampusFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: #343541; color: #d1d5db; }

        .sidebar {
            width: 240px;
            background: #202123;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 20px;
            text-align: center;
            color: #10a37f;
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
            padding: 30px;
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

        .form-container {
            background: #202123;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
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
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #ffffff;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #555;
            background: #2c2c2e;
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
            width: 100%;
        }

        button:hover {
            background: #0e8a6d;
        }

        .error {
            color: #ff6b6b;
            background: #2c2c2e;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="header">
        <h1>Send a Message</h1>
    </div>

    <div class="form-container">
        <h2>Send Message</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <div class="form-group">
                <label for="student_number">Recipient's Student Number:</label>
                <input type="text" name="student_number" id="student_number" value="<?= htmlspecialchars($default_student_number) ?>" required>
            </div>

            <div class="form-group">
                <label for="message">Message:</label>
                <textarea name="message" id="message" rows="5" required></textarea>
            </div>

            <button type="submit">Send</button>
        </form>
    </div>
</div>
</body>
</html>
