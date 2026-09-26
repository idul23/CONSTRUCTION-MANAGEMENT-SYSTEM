<?php

require "../auth.php";
requireLogin();

require "../database.php";


// Get project ID
$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}


// Get project
$stmt = $conn->prepare("
    SELECT *
    FROM projects
    WHERE id = ?
");

$stmt->execute([$id]);

$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Project not found.");
}


// Allowed statuses
$statuses = [
    "Pending",
    "Ongoing",
    "Completed",
    "On Hold"
];


// Existing values
$name = $project["name"];
$description = $project["description"];
$location = $project["location"];
$start_date = $project["start_date"];
$end_date = $project["end_date"];
$budget = $project["budget"];
$status = $project["status"];

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";
    $budget = $_POST["budget"] ?? "";
    $status = $_POST["status"] ?? "";


    // Validate project name
    if ($name == "") {

        $error = "Project name is required.";

    } elseif (strlen($name) > 150) {

        $error = "Project name is too long.";

    }


    // Validate description
    elseif (strlen($description) > 1000) {

        $error = "Description is too long.";

    }


    // Validate location
    elseif ($location == "") {

        $error = "Project location is required.";

    } elseif (strlen($location) > 255) {

        $error = "Location is too long.";

    }


    // Validate start date
    elseif ($start_date == "") {

        $error = "Start date is required.";

    } elseif (!DateTime::createFromFormat("Y-m-d", $start_date)) {

        $error = "Please enter a valid start date.";

    }


    // Validate end date
    elseif ($end_date != "" && !DateTime::createFromFormat("Y-m-d", $end_date)) {

        $error = "Please enter a valid end date.";

    }


    // Validate date order
    elseif ($end_date != "" && $end_date < $start_date) {

        $error = "End date cannot be earlier than start date.";

    }


    // Validate budget
    elseif ($budget == "" || !is_numeric($budget)) {

        $error = "Budget is required.";

    } elseif ($budget < 0) {

        $error = "Budget cannot be negative.";

    }


    // Validate status
    elseif (!in_array($status, $statuses)) {

        $error = "Please select a valid project status.";

    }


    // Update project
    else {

        $stmt = $conn->prepare("
            UPDATE projects
            SET
                name = ?,
                description = ?,
                location = ?,
                start_date = ?,
                end_date = ?,
                budget = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $name,
            $description,
            $location,
            $start_date,
            $end_date,
            $budget,
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

    <title>Edit Project</title>


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

            background: #1f2429;
            color: white;

            padding: 25px 15px;

            overflow-y: auto;
        }


        .logo {
            font-size: 20px;
            font-weight: bold;

            padding: 0 15px 25px;

            border-bottom: 1px solid #3a3f44;

            margin-bottom: 20px;
        }


        .menu-title {
            font-size: 11px;
            color: #9da3a8;

            padding: 0 15px;

            margin-bottom: 10px;

            text-transform: uppercase;
        }


        .menu a {
            display: block;

            color: #d8dcdf;
            text-decoration: none;

            padding: 12px 15px;

            border-radius: 7px;

            margin-bottom: 5px;

            font-size: 14px;
        }


        .menu a:hover {
            background: #343a40;
            color: white;
        }


        .menu a.active {
            background: #2e7559;
            color: white;
        }


        .logout {
            margin-top: 20px;

            border-top: 1px solid #3a3f44;

            padding-top: 20px;
        }


        /* MAIN */

        .main {
            margin-left: 240px;

            min-height: 100vh;
        }


        /* TOPBAR */

        .topbar {
            height: 70px;

            background: white;

            border-bottom: 1px solid #e2e4e7;

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

            max-width: 1100px;
        }


        .breadcrumb {
            color: #777;

            font-size: 13px;

            margin-bottom: 10px;
        }


        .breadcrumb span {
            color: #2e7559;
        }


        .page-header {
            margin-bottom: 25px;
        }


        .page-header h1 {
            margin: 0;

            font-size: 28px;

            color: #202428;
        }


        .page-header p {
            margin-top: 8px;

            color: #70757a;

            font-size: 14px;
        }


        /* FORM CARD */

        .form-card {
            background: white;

            border: 1px solid #e3e5e8;

            border-radius: 10px;

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

            margin-bottom: 20px;

            padding-bottom: 12px;

            border-bottom: 1px solid #e5e7e9;

            color: #292d31;
        }


        .form-grid {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

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

            color: #404448;
        }


        input,
        textarea,
        select {
            width: 100%;

            padding: 11px 12px;

            border: 1px solid #cfd3d7;

            border-radius: 6px;

            font-size: 14px;

            background: white;

            outline: none;

            font-family: Arial, Helvetica, sans-serif;
        }


        textarea {
            min-height: 110px;

            resize: vertical;
        }


        input:focus,
        textarea:focus,
        select:focus {
            border-color: #2e7559;

            box-shadow: 0 0 0 2px rgba(46, 117, 89, 0.1);
        }


        .help-text {
            font-size: 12px;

            color: #888;

            margin-top: 5px;
        }


        /* ERROR */

        .error {
            background: #fff1f1;

            border: 1px solid #e6b8b8;

            color: #b42323;

            padding: 12px 15px;

            border-radius: 6px;

            margin-bottom: 25px;

            font-size: 14px;
        }


        /* BUTTONS */

        .form-actions {
            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 30px;

            padding-top: 20px;

            border-top: 1px solid #e5e7e9;
        }


        .btn {
            display: inline-block;

            padding: 11px 20px;

            border-radius: 6px;

            border: none;

            font-size: 14px;

            font-weight: bold;

            text-decoration: none;

            cursor: pointer;
        }


        .btn-cancel {
            background: #e9ecef;

            color: #333;
        }


        .btn-cancel:hover {
            background: #dfe2e5;
        }


        .btn-save {
            background: #2e7559;

            color: white;
        }


        .btn-save:hover {
            background: #256047;
        }


        /* RESPONSIVE */

        @media (max-width: 900px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 700px) {

            .sidebar {
                position: relative;

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

            .form-card {
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        Construction Management
    </div>


    <div class="menu-title">
        Main Menu
    </div>


    <div class="menu">

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


        <div class="logout">

            <a href="../logout.php">
                Logout
            </a>

        </div>

    </div>

</div>



<!-- MAIN -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div class="user-info">

            <div class="user-name">
                <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </div>

            <div class="user-role">
                <?php echo htmlspecialchars($_SESSION["user_role"]); ?>
            </div>

        </div>

    </div>



    <!-- CONTENT -->

    <div class="content">


        <!-- BREADCRUMB -->

        <div class="breadcrumb">

            Projects
            /
            <span>Edit Project</span>

        </div>



        <!-- PAGE HEADER -->

        <div class="page-header">

            <h1>
                Edit Project
            </h1>

            <p>
                Update the project information, schedule, budget, and status.
            </p>

        </div>



        <!-- FORM CARD -->

        <div class="form-card">


            <?php if ($error != ""): ?>

                <div class="error">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <!-- PROJECT INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Project Information
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="name">
                                Project Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?php echo htmlspecialchars($name); ?>"
                                placeholder="Example: House Construction"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label for="location">
                                Location
                            </label>

                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="<?php echo htmlspecialchars($location); ?>"
                                placeholder="Example: Jagna, Bohol"
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
                            ><?php echo htmlspecialchars($description); ?></textarea>

                            <div class="help-text">
                                Optional. Add a short description of the project.
                            </div>

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
                                value="<?php echo htmlspecialchars($start_date); ?>"
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
                                value="<?php echo htmlspecialchars($end_date); ?>"
                            >

                            <div class="help-text">
                                Optional. Leave blank if there is no fixed end date.
                            </div>

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
                                value="<?php echo htmlspecialchars($budget); ?>"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                required
                            >

                            <div class="help-text">
                                Enter the total allocated budget for this project.
                            </div>

                        </div>



                        <div class="form-group">

                            <label for="status">
                                Project Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                            >

                                <option value="">
                                    Select Status
                                </option>


                                <?php foreach ($statuses as $item): ?>

                                    <option
                                        value="<?php echo htmlspecialchars($item); ?>"
                                        <?php echo ($status == $item) ? "selected" : ""; ?>
                                    >

                                        <?php echo htmlspecialchars($item); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                    </div>

                </div>



                <!-- BUTTONS -->

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
                        Update Project
                    </button>

                </div>


            </form>

        </div>


    </div>

</div>


</body>

</html>