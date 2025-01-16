<?php
    session_start();
    include ("connection.php");
    include("functions.php");
    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $user_name = $_POST['user_name'];
        $password = $_POST['password'];
        if (!empty($user_name) && !empty($password) && !is_numeric($user_name))
        {
            $user_id = random_num(20);
            $query = "insert into users (user_id, user_name, password) values ('$user_id', '$user_name', '$password')";
            mysqli_query($con, $query);
            header("Location: login.php");
            die;
        }
        else
        {
            echo "Please enter some valid information!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 30px;
            width: 350px;
            text-align: center;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px -15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            background-color: #f9f9f9;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #6a11cb;
            outline: none;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        button {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
        }

        button:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
        }

        .footer {
            margin-top: 15px;
            font-size: 14px;
            color: #666;
        }

        .footer a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Signup</h1>
        <form method="POST" action="#">
            <input type="text" name="user_name" placeholder="Enter your username" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Sign Up</button>
        </form>
        <div class="footer">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
