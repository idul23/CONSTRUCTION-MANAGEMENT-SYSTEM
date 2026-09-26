<?php

require "../auth.php";

requireLogin();

require "../database.php";


/*
    Search and filters
*/

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";

$projectFilter = isset($_GET["project_id"])
    ? $_GET["project_id"]
    : "";

$statusFilter = isset($_GET["status"])
    ? $_GET["status"]
    : "";


/*
    Get projects for filter
*/

$projectsList = $conn->query("
    SELECT id, name
    FROM projects
    ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);


/*
    Get tasks
*/

$sql = "
    SELECT
        tasks.*,
        projects.name AS project_name,
        workers.name AS worker_name
    FROM tasks
    LEFT JOIN projects
        ON tasks.project_id = projects.id
    LEFT JOIN workers
        ON tasks.worker_id = workers.id
    WHERE 1 = 1
";

$params = [];


/*
    Search by task name
*/

if ($search != "") {

    $sql .= "
        AND tasks.name LIKE ?
    ";

    $params[] = "%" . $search . "%";

}


/*
    Filter by project
*/

if ($projectFilter != "") {

    $sql .= "
        AND tasks.project_id = ?
    ";

    $params[] = $projectFilter;

}


/*
    Filter by status
*/

if ($statusFilter != "") {

    $sql .= "
        AND tasks.status = ?
    ";

    $params[] = $statusFilter;

}


$sql .= "
    ORDER BY tasks.id DESC
";


$stmt = $conn->prepare($sql);

$stmt->execute($params);

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Task summary
*/

$totalTasks = $conn->query("
    SELECT COUNT(*)
    FROM tasks
")->fetchColumn();


$pendingTasks = $conn->query("
    SELECT COUNT(*)
    FROM tasks
    WHERE status = 'Pending'
")->fetchColumn();


$inProgressTasks = $conn->query("
    SELECT COUNT(*)
    FROM tasks
    WHERE status = 'In Progress'
")->fetchColumn();


$completedTasks = $conn->query("
    SELECT COUNT(*)
    FROM tasks
    WHERE status = 'Completed'
")->fetchColumn();


/*
    Average progress
*/

$averageProgress = $conn->query("
    SELECT COALESCE(AVG(progress), 0)
    FROM tasks
")->fetchColumn();

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        Task Management
    </title>


    <style>

        body {

            font-family: Arial, sans-serif;

            margin: 0;

            background-color: #f4f4f4;

        }


        .container {

            max-width: 1400px;

            margin: auto;

            padding: 30px;

        }


        .top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;

        }


        .top h1 {

            margin: 0;

        }


        .button {

            display: inline-block;

            padding: 10px 15px;

            background-color: #333;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }


        .button:hover {

            background-color: #555;

        }


        /* SUMMARY */

        .summary {

            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 15px;

            margin-bottom: 25px;

        }


        .card {

            background-color: white;

            padding: 20px;

            border-radius: 8px;

            box-shadow:
                0 2px 5px rgba(0,0,0,0.1);

        }


        .card h3 {

            margin-top: 0;

            color: #555;

        }


        .number {

            font-size: 26px;

            font-weight: bold;

        }


        /* SEARCH */

        .search-box {

            background-color: white;

            padding: 20px;

            border-radius: 8px;

            margin-bottom: 20px;

            box-shadow:
                0 2px 5px rgba(0,0,0,0.1);

        }


        .search-form {

            display: grid;

            grid-template-columns:
                1fr 200px 200px auto auto;

            gap: 10px;

        }


        input,
        select {

            width: 100%;

            padding: 10px;

            border: 1px solid #ccc;

            border-radius: 5px;

            box-sizing: border-box;

        }


        .search-button {

            padding: 10px 20px;

            background-color: #333;

            color: white;

            border: none;

            border-radius: 5px;

            cursor: pointer;

        }


        .search-button:hover {

            background-color: #555;

        }


        .clear-button {

            display: inline-block;

            padding: 10px 20px;

            background-color: #777;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }


        .clear-button:hover {

            background-color: #555;

        }


        /* TABLE */

        .table-box {

            background-color: white;

            padding: 20px;

            border-radius: 8px;

            box-shadow:
                0 2px 5px rgba(0,0,0,0.1);

            overflow-x: auto;

        }


        .result-count {

            margin-bottom: 15px;

            color: #555;

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            padding: 12px;

            border-bottom: 1px solid #ddd;

            text-align: left;

        }


        th {

            background-color: #eee;

        }


        .edit {

            color: #0066cc;

            text-decoration: none;

        }


        .delete {

            color: #cc0000;

            text-decoration: none;

        }


        .status {

            font-weight: bold;

        }


        /* PROGRESS */

        .progress-container {

            width: 100px;

            height: 15px;

            background-color: #ddd;

            border-radius: 10px;

            overflow: hidden;

            display: inline-block;

            vertical-align: middle;

            margin-right: 5px;

        }


        .progress-bar {

            height: 100%;

            background-color: #333;

        }


        /* RESPONSIVE */

        @media (max-width: 1100px) {

            .summary {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .search-form {

                grid-template-columns: 1fr 1fr;

            }

        }


        @media (max-width: 500px) {

            .summary {

                grid-template-columns: 1fr;

            }


            .search-form {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<div class="container">


    <!-- TOP -->

    <div class="top">


        <h1>

            Task Management

        </h1>


        <a
            href="add.php"
            class="button"
        >

            Add Task

        </a>


    </div>


    <!-- SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>

                Total Tasks

            </h3>


            <div class="number">

                <?= $totalTasks ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Pending

            </h3>


            <div class="number">

                <?= $pendingTasks ?>

            </div>

        </div>


        <div class="card">

            <h3>

                In Progress

            </h3>


            <div class="number">

                <?= $inProgressTasks ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Completed

            </h3>


            <div class="number">

                <?= $completedTasks ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Average Progress

            </h3>


            <div class="number">

                <?= number_format(
                    $averageProgress,
                    0
                ) ?>%

            </div>

        </div>


    </div>


    <!-- SEARCH AND FILTER -->

    <div class="search-box">


        <form
            method="GET"
            class="search-form"
        >


            <input
                type="text"
                name="search"
                placeholder="Search task name"
                value="<?= htmlspecialchars($search) ?>"
            >


            <select name="project_id">


                <option value="">

                    All Projects

                </option>


                <?php foreach (
                    $projectsList as $project
                ): ?>


                    <option
                        value="<?= $project["id"] ?>"
                        <?= $projectFilter == $project["id"]
                            ? "selected"
                            : "" ?>
                    >

                        <?= htmlspecialchars(
                            $project["name"]
                        ) ?>

                    </option>


                <?php endforeach; ?>


            </select>


            <select name="status">


                <option value="">

                    All Status

                </option>


                <option
                    value="Pending"
                    <?= $statusFilter == "Pending"
                        ? "selected"
                        : "" ?>
                >

                    Pending

                </option>


                <option
                    value="In Progress"
                    <?= $statusFilter == "In Progress"
                        ? "selected"
                        : "" ?>
                >

                    In Progress

                </option>


                <option
                    value="Completed"
                    <?= $statusFilter == "Completed"
                        ? "selected"
                        : "" ?>
                >

                    Completed

                </option>


                <option
                    value="On Hold"
                    <?= $statusFilter == "On Hold"
                        ? "selected"
                        : "" ?>
                >

                    On Hold

                </option>


            </select>


            <button
                type="submit"
                class="search-button"
            >

                Search

            </button>


            <a
                href="index.php"
                class="clear-button"
            >

                Clear

            </a>


        </form>


    </div>


    <!-- TASK TABLE -->

    <div class="table-box">


        <div class="result-count">

            Showing

            <strong>
                <?= count($tasks) ?>
            </strong>

            task(s)

        </div>


        <?php if (
            count($tasks) == 0
        ): ?>


            <p>

                No tasks found.

            </p>


        <?php else: ?>


            <table>


                <tr>


                    <th>

                        ID

                    </th>


                    <th>

                        Task

                    </th>


                    <th>

                        Project

                    </th>


                    <th>

                        Worker

                    </th>


                    <th>

                        Start Date

                    </th>


                    <th>

                        Due Date

                    </th>


                    <th>

                        Status

                    </th>


                    <th>

                        Progress

                    </th>


                    <th>

                        Actions

                    </th>


                </tr>


                <?php foreach (
                    $tasks as $task
                ): ?>


                    <tr>


                        <td>

                            <?= $task["id"] ?>

                        </td>


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


                        <td>


                            <?php if (
                                $task["worker_name"]
                            ): ?>

                                <?= htmlspecialchars(
                                    $task["worker_name"]
                                ) ?>

                            <?php else: ?>

                                Not assigned

                            <?php endif; ?>


                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $task["start_date"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $task["due_date"]
                            ) ?>

                        </td>


                        <td class="status">

                            <?= htmlspecialchars(
                                $task["status"]
                            ) ?>

                        </td>


                        <td>


                            <div
                                class="progress-container"
                            >

                                <div
                                    class="progress-bar"
                                    style="
                                        width:
                                        <?= $task["progress"] ?>%;
                                    "
                                ></div>

                            </div>


                            <?= $task["progress"] ?>%


                        </td>


                        <td>


                            <a
                                href="edit.php?id=<?= $task["id"] ?>"
                                class="edit"
                            >

                                Edit

                            </a>


                            |


                            <a
                                href="delete.php?id=<?= $task["id"] ?>"
                                class="delete"
                                onclick="return confirm('Are you sure you want to delete this task?')"
                            >

                                Delete

                            </a>


                        </td>


                    </tr>


                <?php endforeach; ?>


            </table>


        <?php endif; ?>


    </div>


    <br>


    <a
        href="../index.php"
        class="button"
    >

        Back to Dashboard

    </a>


</div>


</body>

</html>