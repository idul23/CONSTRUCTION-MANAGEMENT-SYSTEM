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
    FROM workers
    WHERE id = ?
");

$stmt->execute([$id]);

$worker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$worker) {
    header("Location: index.php");
    exit;
}

$error = "";

$name = $worker["name"];
$skill = $worker["skill"];
$phone = $worker["phone"];
$daily_rate = $worker["daily_rate"];
$status = $worker["status"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $skill = trim($_POST["skill"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $daily_rate = $_POST["daily_rate"] ?? "";
    $status = $_POST["status"] ?? "";

    $allowedStatuses = [
        "Active",
        "Inactive"
    ];

    if ($name == "") {

        $error = "Worker name is required.";

    } elseif (strlen($name) > 150) {

        $error = "Worker name must not exceed 150 characters.";

    } elseif ($skill == "") {

        $error = "Skill is required.";

    } elseif (strlen($skill) > 100) {

        $error = "Skill must not exceed 100 characters.";

    } elseif ($phone != "" && strlen($phone) > 30) {

        $error = "Phone number must not exceed 30 characters.";

    } elseif ($daily_rate == "" || !is_numeric($daily_rate)) {

        $error = "Please enter a valid daily rate.";

    } elseif ($daily_rate < 0) {

        $error = "Daily rate cannot be negative.";

    } elseif (!in_array($status, $allowedStatuses)) {

        $error = "Please select a valid worker status.";

    } else {

        $update = $conn->prepare("
            UPDATE workers
            SET name = ?,
                skill = ?,
                phone = ?,
                daily_rate = ?,
                status = ?
            WHERE id = ?
        ");

        $update->execute([
            $name,
            $skill,
            $phone,
            $daily_rate,
            $status,
            $id
        ]);

        header("Location: index.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Worker</title>

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
        select {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d5d9dc;
            border-radius: 7px;
            font-size: 14px;
            background: white;
            outline: none;
        }

        input:focus,
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

        <a href="index.php" class="active">
            Workers
        </a>

        <a href="../tasks/index.php">
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
                <a href="index.php">Workers</a>
                &nbsp; / &nbsp;
                Edit Worker
            </div>

            <h1 class="page-title">
                Edit Worker
            </h1>

            <p class="page-description">
                Update the worker's information, skill, rate, and status.
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
                            Worker Information
                        </div>

                        <div class="section-description">
                            Update the basic information of this worker.
                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="name">
                                    Worker Name
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


                            <div class="form-group">

                                <label for="skill">
                                    Skill
                                </label>

                                <input
                                    type="text"
                                    id="skill"
                                    name="skill"
                                    value="<?= htmlspecialchars($skill) ?>"
                                    maxlength="100"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="phone">
                                    Phone Number
                                </label>

                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="<?= htmlspecialchars($phone) ?>"
                                    maxlength="30"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="section">

                        <div class="section-title">
                            Work Information
                        </div>

                        <div class="section-description">
                            Update the worker's daily rate and current status.
                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="daily_rate">
                                    Daily Rate
                                </label>

                                <input
                                    type="number"
                                    id="daily_rate"
                                    name="daily_rate"
                                    value="<?= htmlspecialchars($daily_rate) ?>"
                                    min="0"
                                    step="0.01"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="status">
                                    Worker Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    required
                                >

                                    <option value="Active"
                                        <?= $status == "Active" ? "selected" : "" ?>>
                                        Active
                                    </option>

                                    <option value="Inactive"
                                        <?= $status == "Inactive" ? "selected" : "" ?>>
                                        Inactive
                                    </option>

                                </select>

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
                            Update Worker
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

</body>

</html>