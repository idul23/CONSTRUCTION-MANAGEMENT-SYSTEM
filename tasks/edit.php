<?php

require "../auth.php";
requireLogin();

require "../database.php";

$id = $_GET["id"] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM tasks
    WHERE id = ?
");

$stmt->execute([$id]);

$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: index.php");
    exit;
}

$error = "";

$project_id = $task["project_id"];
$worker_id = $task["worker_id"];
$name = $task["name"];
$description = $task["description"];
$start_date = $task["start_date"];
$due_date = $task["due_date"];
$status = $task["status"];
$progress = $task["progress"] ?? 0;


$projects = $conn->query("
    SELECT id, name
    FROM projects
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


$workers = $conn->query("
    SELECT id, name, skill
    FROM workers
    WHERE status = 'Active'
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project_id = $_POST["project_id"] ?? "";
    $worker_id = $_POST["worker_id"] ?? "";
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $due_date = $_POST["due_date"] ?? "";
    $status = $_POST["status"] ?? "";
    $progress = $_POST["progress"] ?? 0;

    $allowedStatuses = [
        "Pending",
        "In Progress",
        "Completed",
        "On Hold"
    ];


    if ($project_id == "") {

        $error = "Please select a project.";

    } elseif (!is_numeric($project_id)) {

        $error = "Invalid project.";

    } elseif ($name == "") {

        $error = "Task name is required.";

    } elseif (strlen($name) > 150) {

        $error = "Task name must not exceed 150 characters.";

    } elseif (strlen($description) > 1000) {

        $error = "Description must not exceed 1000 characters.";

    } elseif ($start_date == "") {

        $error = "Start date is required.";

    } elseif (!DateTime::createFromFormat("Y-m-d", $start_date)) {

        $error = "Please enter a valid start date.";

    } elseif ($due_date != "" && !DateTime::createFromFormat("Y-m-d", $due_date)) {

        $error = "Please enter a valid due date.";

    } elseif ($due_date != "" && $due_date < $start_date) {

        $error = "Due date cannot be earlier than the start date.";

    } elseif (!in_array($status, $allowedStatuses)) {

        $error = "Please select a valid task status.";

    } elseif (!is_numeric($progress) || $progress < 0 || $progress > 100) {

        $error = "Progress must be between 0 and 100.";

    } else {

        $projectCheck = $conn->prepare("
            SELECT id
            FROM projects
            WHERE id = ?
        ");

        $projectCheck->execute([$project_id]);

        if (!$projectCheck->fetch()) {

            $error = "Selected project does not exist.";

        } else {

            if ($worker_id == "") {
                $worker_id = null;
            }

            if ($worker_id !== null) {

                $workerCheck = $conn->prepare("
                    SELECT id
                    FROM workers
                    WHERE id = ?
                ");

                $workerCheck->execute([$worker_id]);

                if (!$workerCheck->fetch()) {
                    $error = "Selected worker does not exist.";
                }
            }


            if ($error == "") {

                if ($status == "Completed") {
                    $progress = 100;
                }

                $update = $conn->prepare("
                    UPDATE tasks
                    SET project_id = ?,
                        worker_id = ?,
                        name = ?,
                        description = ?,
                        start_date = ?,
                        due_date = ?,
                        status = ?,
                        progress = ?
                    WHERE id = ?
                ");

                $update->execute([
                    $project_id,
                    $worker_id,
                    $name,
                    $description,
                    $start_date,
                    $due_date,
                    $status,
                    $progress,
                    $id
                ]);

                header("Location: index.php");
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Task</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #1e2124;
            color: white;
            padding: 24px 16px;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .sidebar .system-name {
            color: #aeb4ba;
            font-size: 12px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: #cfd3d6;
            text-decoration: none;
            padding: 12px 14px;
            margin-bottom: 5px;
            border-radius: 7px;
            font-size: 14px;
        }

        .sidebar a:hover {
            background: #2c2f33;
            color: white;
        }

        .sidebar a.active {
            background: #2e7559;
            color: white;
        }

        .sidebar .logout {
            margin-top: 20px;
            color: #e2a5a5;
        }

        .main {
            margin-left: 240px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e1e4e7;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 35px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: bold;
            font-size: 14px;
        }

        .user-role {
            color: #777;
            font-size: 12px;
            margin-top: 3px;
        }

        .content {
            padding: 35px;
            max-width: 1100px;
        }

        .breadcrumb {
            color: #777;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .breadcrumb a {
            color: #2e7559;
            text-decoration: none;
        }

        .page-title {
            font-size: 28px;
            margin-bottom: 7px;
        }

        .page-description {
            color: #777;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-card {
            background: white;
            border-radius: 10px;
            border: 1px solid #e1e4e7;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .section {
            margin-bottom: 30px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .section-description {
            color: #777;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d5d9dc;
            border-radius: 7px;
            font-size: 14px;
            background: white;
            outline: none;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #2e7559;
        }

        .error {
            background: #fce8e8;
            color: #a33a3a;
            border: 1px solid #efc5c5;
            padding: 12px 14px;
            border-radius: 7px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .help-text {
            font-size: 12px;
            color: #888;
            margin-top: 6px;
        }

        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-wrapper input {
            flex: 1;
        }

        .percent {
            font-size: 13px;
            color: #777;
            white-space: nowrap;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
        }

        .btn {
            padding: 11px 20px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-cancel {
            background: #e9ecef;
            color: #333;
        }

        .btn-save {
            background: #2e7559;
            color: white;
        }

        .btn-save:hover {
            background: #255f49;
        }

        .btn-cancel:hover {
            background: #dfe2e5;
        }

        @media (max-width: 800px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .content {
                padding: 25px;
            }

        }

        @media (max-width: 600px) {

            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 0 20px;
            }

            .content {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

    <aside class="sidebar">

        <h2>Construction</h2>

        <div class="system-name">
            Management System
        </div>

        <a href="../index.php">
            Dashboard
        </a>

        <a href="../projects/index.php">
            Projects
        </a>

        <a href="../workers/index.php">
            Workers
        </a>

        <a href="index.php" class="active">
            Tasks
        </a>

        <a href="../materials/index.php">
            Materials
        </a>

        <a href="../material_usage/index.php">
            Material Usage
        </a>

        <a href="../attendance/index.php">
            Attendance
        </a>

        <a href="../expenses/index.php">
            Expenses
        </a>

        <a href="../reports/index.php">
            Reports
        </a>

        <a href="../users/index.php">
            Users
        </a>

        <a href="../logout.php" class="logout">
            Logout
        </a>

    </aside>


    <main class="main">

        <header class="topbar">

            <div class="user-info">

                <div class="user-name">
                    <?= htmlspecialchars($_SESSION["user_name"]) ?>
                </div>

                <div class="user-role">
                    <?= htmlspecialchars($_SESSION["user_role"]) ?>
                </div>

            </div>

        </header>


        <div class="content">

            <div class="breadcrumb">
                <a href="index.php">Tasks</a>
                &nbsp; / &nbsp;
                Edit Task
            </div>

            <h1 class="page-title">
                Edit Task
            </h1>

            <p class="page-description">
                Update the task assignment, schedule, status, and progress.
            </p>


            <div class="form-card">

                <?php if ($error != ""): ?>

                    <div class="error">
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endif; ?>


                <form method="POST">

                    <div class="section">

                        <div class="section-title">
                            Task Information
                        </div>

                        <div class="section-description">
                            Update the project task and assigned worker.
                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="project_id">
                                    Project
                                </label>

                                <select
                                    id="project_id"
                                    name="project_id"
                                    required
                                >

                                    <option value="">
                                        Select Project
                                    </option>

                                    <?php foreach ($projects as $project): ?>

                                        <option
                                            value="<?= $project["id"] ?>"
                                            <?= $project_id == $project["id"] ? "selected" : "" ?>
                                        >
                                            <?= htmlspecialchars($project["name"]) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <div class="form-group">

                                <label for="worker_id">
                                    Assigned Worker
                                </label>

                                <select
                                    id="worker_id"
                                    name="worker_id"
                                >

                                    <option value="">
                                        No Worker Assigned
                                    </option>

                                    <?php foreach ($workers as $worker): ?>

                                        <option
                                            value="<?= $worker["id"] ?>"
                                            <?= $worker_id == $worker["id"] ? "selected" : "" ?>
                                        >
                                            <?= htmlspecialchars($worker["name"]) ?>
                                            - <?= htmlspecialchars($worker["skill"]) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <div class="form-group full">

                                <label for="name">
                                    Task Name
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="<?= htmlspecialchars($name) ?>"
                                    maxlength="150"
                                    required
                                >

                            </div>


                            <div class="form-group full">

                                <label for="description">
                                    Description
                                </label>

                                <textarea
                                    id="description"
                                    name="description"
                                    maxlength="1000"
                                ><?= htmlspecialchars($description) ?></textarea>

                            </div>

                        </div>

                    </div>


                    <div class="section">

                        <div class="section-title">
                            Task Schedule
                        </div>

                        <div class="section-description">
                            Update when the task starts and when it should be completed.
                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="start_date">
                                    Start Date
                                </label>

                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    value="<?= htmlspecialchars($start_date) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="due_date">
                                    Due Date
                                </label>

                                <input
                                    type="date"
                                    id="due_date"
                                    name="due_date"
                                    value="<?= htmlspecialchars($due_date) ?>"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="section">

                        <div class="section-title">
                            Status and Progress
                        </div>

                        <div class="section-description">
                            Update the current status and completion percentage.
                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="status">
                                    Task Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    required
                                >

                                    <option value="Pending"
                                        <?= $status == "Pending" ? "selected" : "" ?>>
                                        Pending
                                    </option>

                                    <option value="In Progress"
                                        <?= $status == "In Progress" ? "selected" : "" ?>>
                                        In Progress
                                    </option>

                                    <option value="Completed"
                                        <?= $status == "Completed" ? "selected" : "" ?>>
                                        Completed
                                    </option>

                                    <option value="On Hold"
                                        <?= $status == "On Hold" ? "selected" : "" ?>>
                                        On Hold
                                    </option>

                                </select>

                            </div>


                            <div class="form-group">

                                <label for="progress">
                                    Progress
                                </label>

                                <div class="progress-wrapper">

                                    <input
                                        type="number"
                                        id="progress"
                                        name="progress"
                                        value="<?= htmlspecialchars($progress) ?>"
                                        min="0"
                                        max="100"
                                        required
                                    >

                                    <span class="percent">
                                        %
                                    </span>

                                </div>

                                <div class="help-text">
                                    Enter a value from 0 to 100.
                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="form-actions">

                        <a
                            href="index.php"
                            class="btn btn-cancel"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-save"
                        >
                            Update Task
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

</body>

</html>