<?php

require "../auth.php";
requireLogin();

require "../database.php";


// Get attendance ID
$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}


// Get attendance record
$stmt = $conn->prepare("
    SELECT *
    FROM attendance
    WHERE id = ?
");

$stmt->execute([$id]);

$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attendance) {
    die("Attendance record not found.");
}


// Get workers
$workers = $conn->query("
    SELECT id, name, skill
    FROM workers
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


// Allowed statuses
$statuses = [
    "Present",
    "Absent",
    "Late",
    "Half Day"
];


// Existing values
$worker_id = $attendance["worker_id"];
$attendance_date = $attendance["attendance_date"];
$time_in = $attendance["time_in"];
$time_out = $attendance["time_out"];
$hours_worked = $attendance["hours_worked"];
$status = $attendance["status"];
$notes = $attendance["notes"];

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $worker_id = $_POST["worker_id"] ?? "";
    $attendance_date = $_POST["attendance_date"] ?? "";
    $time_in = $_POST["time_in"] ?? "";
    $time_out = $_POST["time_out"] ?? "";
    $status = $_POST["status"] ?? "";
    $notes = trim($_POST["notes"] ?? "");

    $hours_worked = 0;


    // Validate worker
    if ($worker_id == "") {

        $error = "Please select a worker.";

    } else {

        $stmt = $conn->prepare("
            SELECT id
            FROM workers
            WHERE id = ?
        ");

        $stmt->execute([$worker_id]);

        if (!$stmt->fetch()) {
            $error = "Selected worker does not exist.";
        }
    }


    // Validate date
    if ($error == "" && $attendance_date == "") {

        $error = "Attendance date is required.";

    } elseif ($error == "") {

        $date = DateTime::createFromFormat(
            "Y-m-d",
            $attendance_date
        );

        if (
            !$date ||
            $date->format("Y-m-d") !== $attendance_date
        ) {
            $error = "Please enter a valid attendance date.";
        }
    }


    // Validate status
    if ($error == "" && !in_array($status, $statuses)) {

        $error = "Please select a valid attendance status.";
    }


    // Validate time and calculate hours
    if ($error == "") {

        if ($status == "Absent") {

            // Absent workers have no working hours.
            $time_in = "";
            $time_out = "";
            $hours_worked = 0;

        } else {

            if ($time_in == "") {

                $error = "Time in is required.";

            } elseif ($time_out == "") {

                $error = "Time out is required.";

            } else {

                $start = DateTime::createFromFormat(
                    "H:i",
                    $time_in
                );

                $end = DateTime::createFromFormat(
                    "H:i",
                    $time_out
                );


                if (!$start || !$end) {

                    $error = "Please enter valid time values.";

                } elseif ($end <= $start) {

                    $error = "Time out must be later than time in.";

                } else {

                    $seconds = $end->getTimestamp()
                        - $start->getTimestamp();

                    $hours_worked = $seconds / 3600;


                    if ($hours_worked > 24) {

                        $error = "Working hours cannot be more than 24 hours.";

                    }

                }

            }

        }

    }


    // Validate notes
    if ($error == "" && strlen($notes) > 1000) {

        $error = "Notes are too long.";

    }


    // Update attendance
    if ($error == "") {

        $stmt = $conn->prepare("
            UPDATE attendance
            SET
                worker_id = ?,
                attendance_date = ?,
                time_in = ?,
                time_out = ?,
                hours_worked = ?,
                status = ?,
                notes = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $worker_id,
            $attendance_date,
            $time_in,
            $time_out,
            $hours_worked,
            $status,
            $notes,
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Attendance</title>


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

        <a href="../projects/index.php">
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

        <a href="index.php" class="active">
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

                <?php
                echo htmlspecialchars($_SESSION["user_name"]);
                ?>

            </div>


            <div class="user-role">

                <?php
                echo htmlspecialchars($_SESSION["user_role"]);
                ?>

            </div>

        </div>

    </div>



    <!-- CONTENT -->

    <div class="content">


        <!-- BREADCRUMB -->

        <div class="breadcrumb">

            Attendance
            /
            <span>Edit Attendance</span>

        </div>



        <!-- PAGE HEADER -->

        <div class="page-header">

            <h1>
                Edit Attendance
            </h1>

            <p>
                Update the worker attendance record and working hours.
            </p>

        </div>



        <!-- FORM -->

        <div class="form-card">


            <?php if ($error != ""): ?>

                <div class="error">

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <!-- WORKER INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Worker Information
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="worker_id">
                                Worker
                            </label>

                            <select
                                id="worker_id"
                                name="worker_id"
                                required
                            >

                                <option value="">
                                    Select Worker
                                </option>


                                <?php foreach ($workers as $worker): ?>

                                    <option
                                        value="<?php echo $worker["id"]; ?>"
                                        <?php
                                        echo ($worker_id == $worker["id"])
                                            ? "selected"
                                            : "";
                                        ?>
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $worker["name"]
                                        );
                                        ?>

                                        -
                                        <?php
                                        echo htmlspecialchars(
                                            $worker["skill"]
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                    </div>

                </div>



                <!-- ATTENDANCE INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Attendance Information
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="attendance_date">
                                Attendance Date
                            </label>

                            <input
                                type="date"
                                id="attendance_date"
                                name="attendance_date"
                                value="<?php echo htmlspecialchars($attendance_date); ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label for="status">
                                Attendance Status
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
                                        <?php
                                        echo ($status == $item)
                                            ? "selected"
                                            : "";
                                        ?>
                                    >

                                        <?php
                                        echo htmlspecialchars($item);
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                    </div>

                </div>



                <!-- WORKING HOURS -->

                <div class="section">

                    <div class="section-title">
                        Working Hours
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="time_in">
                                Time In
                            </label>

                            <input
                                type="time"
                                id="time_in"
                                name="time_in"
                                value="<?php echo htmlspecialchars($time_in); ?>"
                            >

                            <div class="help-text">
                                Required unless the worker is marked Absent.
                            </div>

                        </div>



                        <div class="form-group">

                            <label for="time_out">
                                Time Out
                            </label>

                            <input
                                type="time"
                                id="time_out"
                                name="time_out"
                                value="<?php echo htmlspecialchars($time_out); ?>"
                            >

                            <div class="help-text">
                                Hours worked are calculated automatically.
                            </div>

                        </div>


                    </div>

                </div>



                <!-- NOTES -->

                <div class="section">

                    <div class="section-title">
                        Notes
                    </div>


                    <div class="form-grid">


                        <div class="form-group full">

                            <label for="notes">
                                Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                placeholder="Enter additional notes"
                            ><?php echo htmlspecialchars($notes); ?></textarea>

                            <div class="help-text">
                                Optional. Add notes about the worker's attendance.
                            </div>

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
                        Update Attendance
                    </button>

                </div>


            </form>

        </div>


    </div>

</div>


</body>

</html>