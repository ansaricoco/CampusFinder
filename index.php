<!DOCTYPE html>
<html>
<head>
    <title>CampusFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
            min-height: 100vh;
            color: #333;
        }
        .card {
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.125rem;
            color: #555;
            margin-bottom: 2rem;
        }
        .buttons a {
            display: inline-block;
            margin: 0.5rem;
            padding: 0.75rem 2rem;
            background: #10a37f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        .buttons a:hover {
            background: #0e8e6b;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Welcome to CampusFinder</h1>
        <p>Lost Something? We'll Help you find it</p>
        <div class="buttons">
            <a href="login.php">Login</a>
            <a href="register.php">Create Account</a>
        </div>
    </div>
</body>
</html>