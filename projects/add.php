<?php

require "../auth.php";
requireLogin();

require "../database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $start_date = trim($_POST["start_date"] ?? "");
    $end_date = trim($_POST["end_date"] ?? "");
    $budget = trim($_POST["budget"] ?? "");
    $status = trim($_POST["status"] ?? "");

    $allowedStatuses = [
        "Pending",
        "Ongoing",
        "Completed",
        "On Hold"
    ];

    if ($name === "") {

        $error = "Project name is required.";

    } elseif (strlen($name) > 150) {

        $error = "Project name must not exceed 150 characters.";

    } elseif (strlen($description) > 1000) {

        $error = "Description must not exceed 1000 characters.";

    } elseif ($location === "") {

        $error = "Project location is required.";

    } elseif (strlen($location) > 255) {

        $error = "Project location must not exceed 255 characters.";

    } elseif ($start_date === "") {

        $error = "Start date is required.";

    } elseif (
        !DateTime::createFromFormat("Y-m-d", $start_date) ||
        DateTime::createFromFormat("Y-m-d", $start_date)->format("Y-m-d") !== $start_date
    ) {

        $error = "Please enter a valid start date.";

    } elseif (
        $end_date !== "" &&
        (
            !DateTime::createFromFormat("Y-m-d", $end_date) ||
            DateTime::createFromFormat("Y-m-d", $end_date)->format("Y-m-d") !== $end_date
        )
    ) {

        $error = "Please enter a valid end date.";

    } elseif ($end_date !== "" && $end_date < $start_date) {

        $error = "End date cannot be earlier than the start date.";

    } elseif ($budget === "") {

        $error = "Project budget is required.";

    } elseif (!is_numeric($budget)) {

        $error = "Project budget must be a valid number.";

    } elseif ((float)$budget < 0) {

        $error = "Project budget cannot be negative.";

    } elseif ($status === "") {

        $error = "Project status is required.";

    } elseif (!in_array($status, $allowedStatuses, true)) {

        $error = "Invalid project status.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO projects
            (
                name,
                description,
                location,
                start_date,
                end_date,
                budget,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $description,
            $location,
            $start_date,
            $end_date,
            (float)$budget,
            $status
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

    <title>Add Project - Construction Management System</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f6f8;
            color: #222;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #202328;
            color: white;
            padding: 24px 18px;
            overflow-y: auto;
        }

        .logo {
            font-size: 19px;
            font-weight: bold;
            margin-bottom: 30px;
            padding-left: 10px;
        }

        .nav a {
            display: block;
            text-decoration: none;
            color: #cfd2d6;
            padding: 12px 14px;
            border-radius: 7px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .nav a:hover {
            background: #30343a;
            color: white;
        }

        .nav a.active {
            background: #343940;
            color: white;
        }

        /* MAIN */

        .main {
            margin-left: 240px;
            min-height: 100vh;
        }

        /* TOP BAR */

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e1e3e6;
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

        /* CONTENT */

        .content {
            padding: 35px;
            max-width: 1150px;
        }

        .breadcrumb {
            color: #777;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .breadcrumb a {
            color: #555;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .page-description {
            color: #777;
            margin-top: 8px;
            margin-bottom: 28px;
            font-size: 14px;
        }

        /* ERROR */

        .error {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f1aeb5;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        /* FORM */

        .form-card {
            background: white;
            border: 1px solid #e1e3e6;
            border-radius: 10px;
            padding: 30px;
            max-width: 900px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section:last-of-type {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e5e5;
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
            color: #333;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d6d9dd;
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            outline: none;
            background: white;
            color: #222;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #777;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* BUTTONS */

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
        }

        .btn {
            padding: 11px 18px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .btn-cancel {
            background: #e9ecef;
            color: #333;
        }

        .btn-cancel:hover {
            background: #dfe2e5;
        }

        .btn-save {
            background: #202328;
            color: white;
        }

        .btn-save:hover {
            background: #343940;
        }

        /* MOBILE */

        @media (max-width: 800px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
            }

            .content {
                padding: 25px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

        }

        @media (max-width: 600px) {

            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 0 20px;
            }

            .form-card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">
            Construction System
        </div>

        <nav class="nav">

            <a href="../index.php">
                Dashboard
            </a>

            <a href="index.php" class="active">
                Projects
            </a>

            <a href="../workers/index.php">
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

            <a href="../logout.php">
                Logout
            </a>

        </nav>

    </aside>


    <!-- MAIN -->

    <main class="main">

        <!-- TOP BAR -->

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


        <!-- CONTENT -->

        <section class="content">

            <div class="breadcrumb">

                <a href="../index.php">
                    Dashboard
                </a>

                &nbsp; / &nbsp;

                <a href="index.php">
                    Projects
                </a>

                &nbsp; / &nbsp;

                Add Project

            </div>


            <h1 class="page-title">
                Add Project
            </h1>

            <p class="page-description">
                Create a new construction project and enter its basic information.
            </p>


            <?php if ($error !== ""): ?>

                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <!-- FORM CARD -->

            <div class="form-card">

                <form method="POST">

                    <!-- PROJECT INFORMATION -->

                    <div class="section">

                        <div class="section-title">
                            Project Information
                        </div>

                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="name">
                                    Project Name
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder="Enter project name"
                                    value="<?= htmlspecialchars($_POST["name"] ?? "") ?>"
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
                                    placeholder="Enter project description"
                                ><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

                            </div>


                            <div class="form-group full">

                                <label for="location">
                                    Location
                                </label>

                                <input
                                    type="text"
                                    id="location"
                                    name="location"
                                    placeholder="Enter project location"
                                    value="<?= htmlspecialchars($_POST["location"] ?? "") ?>"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    <!-- PROJECT SCHEDULE -->

                    <div class="section">

                        <div class="section-title">
                            Project Schedule
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
                                    value="<?= htmlspecialchars($_POST["start_date"] ?? "") ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="end_date">
                                    End Date
                                </label>

                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    value="<?= htmlspecialchars($_POST["end_date"] ?? "") ?>"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- BUDGET AND STATUS -->

                    <div class="section">

                        <div class="section-title">
                            Budget and Status
                        </div>

                        <div class="form-grid">

                            <div class="form-group">

                                <label for="budget">
                                    Project Budget
                                </label>

                                <input
                                    type="number"
                                    id="budget"
                                    name="budget"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    value="<?= htmlspecialchars($_POST["budget"] ?? "") ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="status">
                                    Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    required
                                >

                                    <option value="Pending"
                                        <?= (($_POST["status"] ?? "Pending") == "Pending") ? "selected" : "" ?>>
                                        Pending
                                    </option>

                                    <option value="Ongoing"
                                        <?= (($_POST["status"] ?? "") == "Ongoing") ? "selected" : "" ?>>
                                        Ongoing
                                    </option>

                                    <option value="Completed"
                                        <?= (($_POST["status"] ?? "") == "Completed") ? "selected" : "" ?>>
                                        Completed
                                    </option>

                                    <option value="On Hold"
                                        <?= (($_POST["status"] ?? "") == "On Hold") ? "selected" : "" ?>>
                                        On Hold
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <!-- BUTTONS -->

                    <div class="form-actions">

                        <a href="index.php" class="btn btn-cancel">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-save"
                        >
                            Save Project
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</body>

</html>

