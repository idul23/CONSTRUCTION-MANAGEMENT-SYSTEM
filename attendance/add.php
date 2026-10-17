<?php

require "../auth.php";
requireLogin();

require "../database.php";

$error = "";

$workers = $conn->query("
    SELECT *
    FROM workers
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $worker_id = trim($_POST["worker_id"] ?? "");
    $attendance_date = trim($_POST["attendance_date"] ?? "");
    $time_in = trim($_POST["time_in"] ?? "");
    $time_out = trim($_POST["time_out"] ?? "");
    $status = trim($_POST["status"] ?? "");
    $notes = trim($_POST["notes"] ?? "");

    $allowedStatuses = [
        "Present",
        "Absent",
        "Late",
        "Half Day"
    ];


    // Validate worker
    if ($worker_id === "") {

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
    if ($error === "" && $attendance_date === "") {

        $error = "Attendance date is required.";

    } elseif (
        $error === "" &&
        (
            !DateTime::createFromFormat("Y-m-d", $attendance_date) ||
            DateTime::createFromFormat("Y-m-d", $attendance_date)->format("Y-m-d") !== $attendance_date
        )
    ) {

        $error = "Please enter a valid attendance date.";

    }


    // Validate status
    if ($error === "" && $status === "") {

        $error = "Attendance status is required.";

    } elseif (
        $error === "" &&
        !in_array($status, $allowedStatuses, true)
    ) {

        $error = "Invalid attendance status.";

    }


    // Validate time
    if ($error === "" && $status !== "Absent") {

        if ($time_in === "") {

            $error = "Time in is required.";

        } elseif ($time_out === "") {

            $error = "Time out is required.";

        } elseif ($time_out <= $time_in) {

            $error = "Time out must be later than time in.";

        }

    }


    // Calculate hours
    $hours_worked = 0;

    if ($error === "" && $status !== "Absent") {

        $timeIn = strtotime($time_in);
        $timeOut = strtotime($time_out);

        $hours_worked = ($timeOut - $timeIn) / 3600;

        if ($hours_worked <= 0) {

            $error = "Invalid working hours.";

        } elseif ($hours_worked > 24) {

            $error = "Working hours cannot exceed 24 hours.";

        }

    }


    // Save attendance
    if ($error === "") {

        $stmt = $conn->prepare("
            INSERT INTO attendance
            (
                worker_id,
                attendance_date,
                time_in,
                time_out,
                hours_worked,
                status,
                notes
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $worker_id,
            $attendance_date,
            $time_in !== "" ? $time_in : null,
            $time_out !== "" ? $time_out : null,
            $hours_worked,
            $status,
            $notes
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

    <title>Add Attendance - Construction Management System</title>

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

        .help-text {
            font-size: 12px;
            color: #777;
            margin-top: 6px;
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
                    Attendance
                </a>

                &nbsp; / &nbsp;

                Add Attendance

            </div>


            <h1 class="page-title">
                Add Attendance
            </h1>

            <p class="page-description">
                Record a worker's attendance and working hours.
            </p>


            <?php if ($error !== ""): ?>

                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <!-- FORM CARD -->

            <div class="form-card">

                <form method="POST">

                    <!-- WORKER INFORMATION -->

                    <div class="section">

                        <div class="section-title">
                            Worker Information
                        </div>

                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="worker_id">
                                    Worker
                                </label>

                                <select
                                    id="worker_id"
                                    name="worker_id"
                                    required
                                >

                                    <option value="">
                                        Select a worker
                                    </option>

                                    <?php foreach ($workers as $worker): ?>

                                        <option
                                            value="<?= $worker["id"] ?>"
                                            <?= (($_POST["worker_id"] ?? "") == $worker["id"]) ? "selected" : "" ?>
                                        >
                                            <?= htmlspecialchars($worker["name"]) ?>
                                            - <?= htmlspecialchars($worker["skill"]) ?>
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
                                    value="<?= htmlspecialchars($_POST["attendance_date"] ?? date("Y-m-d")) ?>"
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

                                    <option value="Present"
                                        <?= (($_POST["status"] ?? "Present") == "Present") ? "selected" : "" ?>>
                                        Present
                                    </option>

                                    <option value="Late"
                                        <?= (($_POST["status"] ?? "") == "Late") ? "selected" : "" ?>>
                                        Late
                                    </option>

                                    <option value="Half Day"
                                        <?= (($_POST["status"] ?? "") == "Half Day") ? "selected" : "" ?>>
                                        Half Day
                                    </option>

                                    <option value="Absent"
                                        <?= (($_POST["status"] ?? "") == "Absent") ? "selected" : "" ?>>
                                        Absent
                                    </option>

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
                                    value="<?= htmlspecialchars($_POST["time_in"] ?? "") ?>"
                                >

                                <div class="help-text">
                                    Required when the worker is not absent.
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
                                    value="<?= htmlspecialchars($_POST["time_out"] ?? "") ?>"
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
                                ><?= htmlspecialchars($_POST["notes"] ?? "") ?></textarea>

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
                            Save Attendance
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</body>

</html>