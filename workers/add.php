<?php

session_start();


if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}


require "../database.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $skill = $_POST["skill"];
    $phone = $_POST["phone"];
    $daily_rate = $_POST["daily_rate"];
    $status = $_POST["status"];


    $stmt = $conn->prepare("
        INSERT INTO workers
        (
            name,
            skill,
            phone,
            daily_rate,
            status
        )
        VALUES (?, ?, ?, ?, ?)
    ");


    $stmt->execute([
        $name,
        $skill,
        $phone,
        $daily_rate,
        $status
    ]);


    header("Location: index.php");
    exit;

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Add Worker</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f8;
        }

        .header {
            background-color: #263238;
            color: white;
            padding: 20px;
        }

        .container {
            width: 600px;
            max-width: 90%;
            margin: 30px auto;
        }

        .form-box {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .buttons {
            margin-top: 20px;
        }

        button {
            background-color: #1565c0;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        .back {
            background-color: #555;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }

    </style>

</head>


<body>


    <div class="header">

        <h1>Construction Management System</h1>

    </div>


    <div class="container">


        <div class="form-box">

            <h2>Add Worker</h2>


            <form method="POST">


                <label>
                    Worker Name
                </label>

                <input
                    type="text"
                    name="name"
                    required
                >


                <label>
                    Skill
                </label>

                <input
                    type="text"
                    name="skill"
                    placeholder="Example: Mason"
                    required
                >


                <label>
                    Phone
                </label>

                <input
                    type="text"
                    name="phone"
                >


                <label>
                    Daily Rate
                </label>

                <input
                    type="number"
                    name="daily_rate"
                    step="0.01"
                    min="0"
                    required
                >


                <label>
                    Status
                </label>

                <select
                    name="status"
                    required
                >

                    <option value="Active">
                        Active
                    </option>

                    <option value="Inactive">
                        Inactive
                    </option>

                </select>


                <div class="buttons">

                    <a
                        href="index.php"
                        class="back"
                    >
                        Back
                    </a>

                    <button type="submit">
                        Save Worker
                    </button>

                </div>


            </form>


        </div>


    </div>


</body>

</html>