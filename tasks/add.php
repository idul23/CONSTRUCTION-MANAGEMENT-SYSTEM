<?php

require "../auth.php";

requireLogin();

require "../database.php";


$error = "";


/*
    Get projects
*/

$projects = $conn->query("
    SELECT id, name
    FROM projects
    ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);


/*
    Get active workers
*/

$workers = $conn->query("
    SELECT id, name
    FROM workers
    WHERE status = 'Active'
    ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project_id = $_POST["project_id"];
    $worker_id = !empty($_POST["worker_id"])
        ? $_POST["worker_id"]
        : null;

    $name = $_POST["name"];
    $description = $_POST["description"];
    $start_date = $_POST["start_date"];
    $due_date = $_POST["due_date"];
    $status = $_POST["status"];
    $progress = $_POST["progress"];


    /*
        Make sure progress stays between 0 and 100
    */

    if ($progress < 0) {
        $progress = 0;
    }

    if ($progress > 100) {
        $progress = 100;
    }


    /*
        If task is completed,
        automatically make progress 100
    */

    if ($status == "Completed") {
        $progress = 100;
    }


    $stmt = $conn->prepare("
        INSERT INTO tasks
        (
            project_id,
            worker_id,
            name,
            description,
            start_date,
            due_date,
            status,
            progress
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");


    $stmt->execute([
        $project_id,
        $worker_id,
        $name,
        $description,
        $start_date,
        $due_date,
        $status,
        $progress
    ]);


    header("Location: index.php");

    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Task</title>

    <style>

        body {

            font-family: Arial, sans-serif;

            margin: 40px;

            background-color: #f4f4f4;

        }


        .container {

            max-width: 700px;

            margin: auto;

            background-color: white;

            padding: 25px;

            border-radius: 8px;

            box-shadow: 0 2px 5px rgba(0,0,0,0.1);

        }


        h1 {

            margin-top: 0;

        }


        label {

            display: block;

            margin-top: 15px;

            margin-bottom: 5px;

            font-weight: bold;

        }


        input,
        textarea,
        select {

            width: 100%;

            padding: 10px;

            border: 1px solid #ccc;

            border-radius: 5px;

            box-sizing: border-box;

        }


        textarea {

            height: 100px;

            resize: vertical;

        }


        button {

            margin-top: 20px;

            padding: 10px 20px;

            background-color: #333;

            color: white;

            border: none;

            border-radius: 5px;

            cursor: pointer;

        }


        button:hover {

            background-color: #555;

        }


        .back {

            display: inline-block;

            margin-top: 15px;

            color: #333;

            text-decoration: none;

        }

    </style>

</head>

<body>


<div class="container">


    <h1>Add Task</h1>


    <form method="POST">


        <label>Project</label>

        <select name="project_id" required>

            <option value="">
                Select Project
            </option>


            <?php foreach ($projects as $project): ?>

                <option value="<?= $project["id"] ?>">

                    <?= htmlspecialchars($project["name"]) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <label>Worker</label>

        <select name="worker_id">

            <option value="">
                Not Assigned
            </option>


            <?php foreach ($workers as $worker): ?>

                <option value="<?= $worker["id"] ?>">

                    <?= htmlspecialchars($worker["name"]) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <label>Task Name</label>

        <input
            type="text"
            name="name"
            required
        >


        <label>Description</label>

        <textarea name="description"></textarea>


        <label>Start Date</label>

        <input
            type="date"
            name="start_date"
            required
        >


        <label>Due Date</label>

        <input
            type="date"
            name="due_date"
        >


        <label>Status</label>

        <select name="status" required>

            <option value="Pending">
                Pending
            </option>

            <option value="In Progress">
                In Progress
            </option>

            <option value="Completed">
                Completed
            </option>

            <option value="On Hold">
                On Hold
            </option>

        </select>


        <label>Progress (%)</label>

        <input
            type="number"
            name="progress"
            min="0"
            max="100"
            value="0"
            required
        >


        <button type="submit">
            Add Task
        </button>


    </form>


    <a href="index.php" class="back">
        Back to Tasks
    </a>


</div>


</body>

</html>