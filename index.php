<?php

require "auth.php";

requireLogin();

require "database.php";


/*
    Dashboard counts
*/

$projects = $conn->query("
    SELECT COUNT(*)
    FROM projects
")->fetchColumn();


$workers = $conn->query("
    SELECT COUNT(*)
    FROM workers
    WHERE status = 'Active'
")->fetchColumn();


$tasks = $conn->query("
    SELECT COUNT(*)
    FROM tasks
")->fetchColumn();


$materials = $conn->query("
    SELECT COUNT(*)
    FROM materials
")->fetchColumn();


/*
    Today's attendance
*/

$today = date("Y-m-d");


$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM attendance
    WHERE attendance_date = ?
");

$stmt->execute([$today]);

$attendance = $stmt->fetchColumn();


/*
    Total expenses
*/

$totalExpenses = $conn->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM expenses
")->fetchColumn();


/*
    Total project budget
*/

$totalBudget = $conn->query("
    SELECT COALESCE(SUM(budget), 0)
    FROM projects
")->fetchColumn();


/*
    Remaining budget
*/

$remainingBudget =
    $totalBudget - $totalExpenses;


/*
    Recent projects
*/

$recentProjects = $conn->query("
    SELECT *
    FROM projects
    ORDER BY id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);


/*
    Recent tasks
*/

$recentTasks = $conn->query("
    SELECT
        tasks.*,
        projects.name AS project_name
    FROM tasks
    LEFT JOIN projects
        ON tasks.project_id = projects.id
    ORDER BY tasks.id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);


/*
    Calculate project progress
*/

foreach ($recentProjects as &$project) {

    $stmt = $conn->prepare("
        SELECT COALESCE(AVG(progress), 0)
        FROM tasks
        WHERE project_id = ?
    ");

    $stmt->execute([
        $project["id"]
    ]);

    $project["progress"] =
        $stmt->fetchColumn();

}

unset($project);


/*
    Logged in user
*/

$name = $_SESSION["user_name"];

$role = $_SESSION["user_role"];

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        Construction Management System
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            font-family: Arial, sans-serif;

            margin: 0;

            background-color: #f4f6f8;

        }


        /* SIDEBAR */

        .sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 230px;

            height: 100vh;

            background-color: #222;

            color: white;

            padding: 25px 15px;

            overflow-y: auto;

        }


        .sidebar h2 {

            margin-top: 0;

            margin-bottom: 30px;

            text-align: center;

            font-size: 20px;

        }


        .sidebar a {

            display: block;

            color: white;

            text-decoration: none;

            padding: 12px 15px;

            margin-bottom: 5px;

            border-radius: 5px;

        }


        .sidebar a:hover {

            background-color: #444;

        }


        .sidebar .logout {

            background-color: #b00020;

            margin-top: 20px;

        }


        /* MAIN CONTENT */

        .main {

            margin-left: 230px;

            padding: 30px;

        }


        /* HEADER */

        .header {

            background-color: white;

            padding: 25px;

            border-radius: 10px;

            margin-bottom: 25px;

            box-shadow:
                0 2px 6px rgba(0,0,0,0.1);

        }


        .header h1 {

            margin: 0;

        }


        .header p {

            margin-bottom: 0;

            color: #555;

        }


        /* MAIN CARDS */

        .cards {

            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(180px, 1fr));

            gap: 20px;

            margin-bottom: 25px;

        }


        .card {

            background-color: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow:
                0 2px 6px rgba(0,0,0,0.1);

        }


        .card h3 {

            margin: 0 0 10px;

            color: #555;

        }


        .number {

            font-size: 28px;

            font-weight: bold;

        }


        /* BUDGET */

        .budget-section {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 25px;

        }


        .budget-card {

            background-color: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow:
                0 2px 6px rgba(0,0,0,0.1);

        }


        .budget-card h3 {

            margin-top: 0;

            color: #555;

        }


        .budget-number {

            font-size: 23px;

            font-weight: bold;

        }


        /* GENERAL SECTION */

        .section {

            background-color: white;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 25px;

            box-shadow:
                0 2px 6px rgba(0,0,0,0.1);

        }


        .section h2 {

            margin-top: 0;

        }


        /* PROJECT PROGRESS */

        .project-progress {

            margin-bottom: 20px;

        }


        .project-progress:last-child {

            margin-bottom: 0;

        }


        .project-name {

            font-weight: bold;

            margin-bottom: 7px;

        }


        .progress-container {

            width: 100%;

            height: 20px;

            background-color: #ddd;

            border-radius: 10px;

            overflow: hidden;

        }


        .progress-bar {

            height: 100%;

            background-color: #333;

        }


        .progress-text {

            display: block;

            margin-top: 5px;

            color: #555;

            font-size: 14px;

        }


        /* TWO COLUMNS */

        .sections {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;

        }


        /* TABLE */

        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            padding: 10px;

            border-bottom: 1px solid #ddd;

            text-align: left;

        }


        th {

            background-color: #f1f1f1;

        }


        .status {

            font-weight: bold;

        }


        /* MOBILE */

        @media (max-width: 900px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

            }


            .sidebar a {

                display: inline-block;

            }


            .main {

                margin-left: 0;

            }


            .sections {

                grid-template-columns: 1fr;

            }


            .budget-section {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 500px) {

            .sidebar a {

                display: block;

            }


            .main {

                padding: 15px;

            }

        }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">


    <h2>

        Construction<br>

        Management

    </h2>


    <a href="index.php">

        Dashboard

    </a>


    <a href="projects/index.php">

        Projects

    </a>


    <a href="workers/index.php">

        Workers

    </a>


    <a href="tasks/index.php">

        Tasks

    </a>


    <a href="materials/index.php">

        Materials

    </a>


    <a href="material_usage/index.php">

        Material Usage

    </a>


    <a href="attendance/index.php">

        Attendance

    </a>


    <a href="expenses/index.php">

        Expenses

    </a>


    <a href="reports/index.php">

        Reports

    </a>


    <a href="users/index.php">

        Users

    </a>


    <a
        href="logout.php"
        class="logout"
    >

        Logout

    </a>


</div>


<!-- MAIN -->

<div class="main">


    <!-- HEADER -->

    <div class="header">


        <h1>

            Construction Management System

        </h1>


        <p>

            Welcome,

            <strong>

                <?= htmlspecialchars($name) ?>

            </strong>

            |

            <?= htmlspecialchars($role) ?>

        </p>


    </div>


    <!-- MAIN STATISTICS -->

    <div class="cards">


        <div class="card">

            <h3>

                Total Projects

            </h3>


            <div class="number">

                <?= $projects ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Active Workers

            </h3>


            <div class="number">

                <?= $workers ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Total Tasks

            </h3>


            <div class="number">

                <?= $tasks ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Materials

            </h3>


            <div class="number">

                <?= $materials ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Today's Attendance

            </h3>


            <div class="number">

                <?= $attendance ?>

            </div>

        </div>


    </div>


    <!-- BUDGET -->

    <div class="budget-section">


        <div class="budget-card">

            <h3>

                Total Project Budget

            </h3>


            <div class="budget-number">

                ₱<?= number_format(
                    $totalBudget,
                    2
                ) ?>

            </div>

        </div>


        <div class="budget-card">

            <h3>

                Total Expenses

            </h3>


            <div class="budget-number">

                ₱<?= number_format(
                    $totalExpenses,
                    2
                ) ?>

            </div>

        </div>


        <div class="budget-card">

            <h3>

                Remaining Budget

            </h3>


            <div class="budget-number">

                ₱<?= number_format(
                    $remainingBudget,
                    2
                ) ?>

            </div>

        </div>


    </div>


    <!-- PROJECT PROGRESS -->

    <div class="section">


        <h2>

            Project Progress

        </h2>


        <?php if (
            count($recentProjects) == 0
        ): ?>


            <p>

                No projects available.

            </p>


        <?php else: ?>


            <?php foreach (
                $recentProjects as $project
            ): ?>


                <div class="project-progress">


                    <div class="project-name">

                        <?= htmlspecialchars(
                            $project["name"]
                        ) ?>

                    </div>


                    <div class="progress-container">


                        <div
                            class="progress-bar"
                            style="
                                width:
                                <?= $project["progress"] ?>%;
                            "
                        ></div>


                    </div>


                    <span class="progress-text">

                        <?= number_format(
                            $project["progress"],
                            0
                        ) ?>%

                        complete

                    </span>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </div>


    <!-- RECENT INFORMATION -->

    <div class="sections">


        <!-- RECENT PROJECTS -->

        <div class="section">


            <h2>

                Recent Projects

            </h2>


            <?php if (
                count($recentProjects) == 0
            ): ?>


                <p>

                    No projects yet.

                </p>


            <?php else: ?>


                <table>


                    <tr>

                        <th>

                            Project

                        </th>


                        <th>

                            Status

                        </th>


                        <th>

                            Budget

                        </th>

                    </tr>


                    <?php foreach (
                        $recentProjects as $project
                    ): ?>


                        <tr>


                            <td>

                                <?= htmlspecialchars(
                                    $project["name"]
                                ) ?>

                            </td>


                            <td class="status">

                                <?= htmlspecialchars(
                                    $project["status"]
                                ) ?>

                            </td>


                            <td>

                                ₱<?= number_format(
                                    $project["budget"],
                                    2
                                ) ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </table>


            <?php endif; ?>


        </div>


        <!-- RECENT TASKS -->

        <div class="section">


            <h2>

                Recent Tasks

            </h2>


            <?php if (
                count($recentTasks) == 0
            ): ?>


                <p>

                    No tasks yet.

                </p>


            <?php else: ?>


                <table>


                    <tr>

                        <th>

                            Task

                        </th>


                        <th>

                            Project

                        </th>


                        <th>

                            Status

                        </th>


                        <th>

                            Progress

                        </th>

                    </tr>


                    <?php foreach (
                        $recentTasks as $task
                    ): ?>


                        <tr>


                            <td>

                                <?= htmlspecialchars(
                                    $task["name"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $task["project_name"]
                                ) ?>

                            </td>


                            <td class="status">

                                <?= htmlspecialchars(
                                    $task["status"]
                                ) ?>

                            </td>


                            <td>

                                <?= $task["progress"] ?>%

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </table>


            <?php endif; ?>


        </div>


    </div>


</div>


</body>

</html>