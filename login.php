<?php

session_start();

require "database.php";


$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];


    $stmt = $conn->prepare("
        SELECT *
        FROM users
        WHERE email = ?
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];
        $_SESSION["user_role"] = $user["role"];


        header("Location: index.php");
        exit;

    } else {

        $error = "Invalid email or password.";

    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Login</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f8;
        }

        .container {
            width: 400px;
            margin: 100px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #263238;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #1565c0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error {
            background-color: #ffdddd;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

    </style>

</head>


<body>


    <div class="container">

        <h1>Construction Management System</h1>

        <h2>Login</h2>


        <?php if ($error != ""): ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php endif; ?>


        <form method="POST">


            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >


            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >


            <button type="submit">
                Login
            </button>


        </form>


    </div>


</body>

</html>