<?php

require "../auth.php";

requireLogin();

require "../database.php";


/*
    PROJECT SUMMARY
*/

$projects = $conn->query("
    SELECT
        projects.*,
        COALESCE(
            (
                SELECT SUM(amount)
                FROM expenses
                WHERE expenses.project_id = projects.id
            ),
            0
        ) AS total_expenses
    FROM projects
    ORDER BY projects.id DESC
")->fetchAll(PDO::FETCH_ASSOC);


/*
    Calculate project progress
*/

foreach ($projects as &$project) {

    $stmt = $conn->prepare("
        SELECT COALESCE(AVG(progress), 0)
        FROM tasks
        WHERE project_id = ?
    ");

    $stmt->execute([$project["id"]]);

    $project["progress"] = $stmt->fetchColumn();


    $project["remaining_budget"] =
        $project["budget"] - $project["total_expenses"];

}

unset($project);


/*
    WORKER SUMMARY
*/

$totalWorkers = $conn->query("
    SELECT COUNT(*)
    FROM workers
")->fetchColumn();


$activeWorkers = $conn->query("
    SELECT COUNT(*)
    FROM workers
    WHERE status = 'Active'
")->fetchColumn();


$inactiveWorkers = $conn->query("
    SELECT COUNT(*)
    FROM workers
    WHERE status = 'Inactive'
")->fetchColumn();


/*
    TASK SUMMARY
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
    MATERIAL SUMMARY
*/

$totalMaterials = $conn->query("
    SELECT COUNT(*)
    FROM materials
")->fetchColumn();


$lowStockMaterials = $conn->query("
    SELECT COUNT(*)
    FROM materials
    WHERE status = 'Low Stock'
")->fetchColumn();


$outOfStockMaterials = $conn->query("
    SELECT COUNT(*)
    FROM materials
    WHERE status = 'Out of Stock'
")->fetchColumn();


$inventoryValue = $conn->query("
    SELECT COALESCE(
        SUM(quantity * unit_cost),
        0
    )
    FROM materials
")->fetchColumn();


/*
    ATTENDANCE SUMMARY
*/

$totalAttendance = $conn->query("
    SELECT COUNT(*)
    FROM attendance
")->fetchColumn();


$totalPresent = $conn->query("
    SELECT COUNT(*)
    FROM attendance
    WHERE status = 'Present'
")->fetchColumn();


$totalAbsent = $conn->query("
    SELECT COUNT(*)
    FROM attendance
    WHERE status = 'Absent'
")->fetchColumn();


$totalHours = $conn->query("
    SELECT COALESCE(
        SUM(hours_worked),
        0
    )
    FROM attendance
")->fetchColumn();


/*
    EXPENSE SUMMARY
*/

$totalExpenses = $conn->query("
    SELECT COALESCE(
        SUM(amount),
        0
    )
    FROM expenses
")->fetchColumn();


$highestExpense = $conn->query("
    SELECT COALESCE(
        MAX(amount),
        0
    )
    FROM expenses
")->fetchColumn();


/*
    TOTAL PROJECT BUDGET
*/

$totalBudget = $conn->query("
    SELECT COALESCE(
        SUM(budget),
        0
    )
    FROM projects
")->fetchColumn();


$remainingBudget =
    $totalBudget - $totalExpenses;

?>

<!DOCTYPE html>
<html>

<head>

    <title>System Reports</title>

    <style>

        body {

            font-family: Arial, sans-serif;

            margin: 0;

            background-color: #f4f4f4;

        }


        .container {

            max-width: 1300px;

            margin: auto;

            padding: 30px;

        }


        .top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

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

            border: none;

            cursor: pointer;

        }


        .button:hover {

            background-color: #555;

        }


        .summary {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 30px;

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

            font-size: 25px;

            font-weight: bold;

        }


        .section {

            background-color: white;

            padding: 20px;

            margin-bottom: 25px;

            border-radius: 8px;

            box-shadow:
                0 2px 5px rgba(0,0,0,0.1);

            overflow-x: auto;

        }


        .section h2 {

            margin-top: 0;

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


        .progress-container {

            width: 100px;

            height: 15px;

            background-color: #ddd;

            border-radius: 10px;

            overflow: hidden;

            display: inline-block;

            vertical-align: middle;

        }


        .progress-bar {

            height: 100%;

            background-color: #333;

        }


        .print-button {

            margin-left: 10px;

        }


        @media (max-width: 900px) {

            .summary {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media (max-width: 500px) {

            .summary {

                grid-template-columns: 1fr;

            }

        }


        @media print {

            .top-buttons {

                display: none;

            }

            body {

                background-color: white;

            }

            .section,
            .card {

                box-shadow: none;

            }

        }

    </style>

</head>

<body>


<div class="container">


    <div class="top top-buttons">

        <h1>System Reports</h1>


        <div>

            <button
                onclick="window.print()"
                class="button"
            >
                Print Report
            </button>

            <a
                href="../index.php"
                class="button"
            >
                Dashboard
            </a>

        </div>

    </div>


    <!-- PROJECT AND BUDGET SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>Total Projects</h3>

            <div class="number">

                <?= count($projects) ?>

            </div>

        </div>


        <div class="card">

            <h3>Total Budget</h3>

            <div class="number">

                ₱<?= number_format(
                    $totalBudget,
                    2
                ) ?>

            </div>

        </div>


        <div class="card">

            <h3>Total Expenses</h3>

            <div class="number">

                ₱<?= number_format(
                    $totalExpenses,
                    2
                ) ?>

            </div>

        </div>


        <div class="card">

            <h3>Remaining Budget</h3>

            <div class="number">

                ₱<?= number_format(
                    $remainingBudget,
                    2
                ) ?>

            </div>

        </div>


    </div>


    <!-- WORKER SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>Total Workers</h3>

            <div class="number">

                <?= $totalWorkers ?>

            </div>

        </div>


        <div class="card">

            <h3>Active Workers</h3>

            <div class="number">

                <?= $activeWorkers ?>

            </div>

        </div>


        <div class="card">

            <h3>Inactive Workers</h3>

            <div class="number">

                <?= $inactiveWorkers ?>

            </div>

        </div>


        <div class="card">

            <h3>Total Work Hours</h3>

            <div class="number">

                <?= number_format(
                    $totalHours,
                    2
                ) ?>

            </div>

        </div>


    </div>


    <!-- TASK SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>Total Tasks</h3>

            <div class="number">

                <?= $totalTasks ?>

            </div>

        </div>


        <div class="card">

            <h3>Pending Tasks</h3>

            <div class="number">

                <?= $pendingTasks ?>

            </div>

        </div>


        <div class="card">

            <h3>In Progress</h3>

            <div class="number">

                <?= $inProgressTasks ?>

            </div>

        </div>


        <div class="card">

            <h3>Completed</h3>

            <div class="number">

                <?= $completedTasks ?>

            </div>

        </div>


    </div>


    <!-- MATERIAL SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>Total Materials</h3>

            <div class="number">

                <?= $totalMaterials ?>

            </div>

        </div>


        <div class="card">

            <h3>Low Stock</h3>

            <div class="number">

                <?= $lowStockMaterials ?>

            </div>

        </div>


        <div class="card">

            <h3>Out of Stock</h3>

            <div class="number">

                <?= $outOfStockMaterials ?>

            </div>

        </div>


        <div class="card">

            <h3>Inventory Value</h3>

            <div class="number">

                ₱<?= number_format(
                    $inventoryValue,
                    2
                ) ?>

            </div>

        </div>


    </div>


    <!-- ATTENDANCE SUMMARY -->

    <div class="section">

        <h2>Attendance Summary</h2>


        <table>

            <tr>

                <th>Total Records</th>

                <th>Present</th>

                <th>Absent</th>

                <th>Total Hours</th>

            </tr>


            <tr>

                <td>
                    <?= $totalAttendance ?>
                </td>

                <td>
                    <?= $totalPresent ?>
                </td>

                <td>
                    <?= $totalAbsent ?>
                </td>

                <td>
                    <?= number_format(
                        $totalHours,
                        2
                    ) ?>
                </td>

            </tr>

        </table>

    </div>


    <!-- PROJECT REPORT -->

    <div class="section">

        <h2>Project Report</h2>


        <?php if (count($projects) == 0): ?>

            <p>No projects available.</p>

        <?php else: ?>


            <table>

                <tr>

                    <th>Project</th>

                    <th>Status</th>

                    <th>Budget</th>

                    <th>Expenses</th>

                    <th>Remaining</th>

                    <th>Progress</th>

                </tr>


                <?php foreach (
                    $projects as $project
                ): ?>


                    <tr>


                        <td>

                            <?= htmlspecialchars(
                                $project["name"]
                            ) ?>

                        </td>


                        <td>

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


                        <td>

                            ₱<?= number_format(
                                $project["total_expenses"],
                                2
                            ) ?>

                        </td>


                        <td>

                            ₱<?= number_format(
                                $project["remaining_budget"],
                                2
                            ) ?>

                        </td>


                        <td>

                            <div
                                class="progress-container"
                            >

                                <div
                                    class="progress-bar"
                                    style="width: <?= $project["progress"] ?>%;"
                                ></div>

                            </div>

                            <?= number_format(
                                $project["progress"],
                                0
                            ) ?>%

                        </td>


                    </tr>


                <?php endforeach; ?>


            </table>


        <?php endif; ?>


    </div>


    <!-- EXPENSE SUMMARY -->

    <div class="section">

        <h2>Expense Summary</h2>


        <table>

            <tr>

                <th>Total Expense Records</th>

                <th>Total Expenses</th>

                <th>Highest Expense</th>

            </tr>


            <tr>

                <td>

                    <?= $conn->query("
                        SELECT COUNT(*)
                        FROM expenses
                    ")->fetchColumn() ?>

                </td>

                <td>

                    ₱<?= number_format(
                        $totalExpenses,
                        2
                    ) ?>

                </td>

                <td>

                    ₱<?= number_format(
                        $highestExpense,
                        2
                    ) ?>

                </td>

            </tr>

        </table>

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