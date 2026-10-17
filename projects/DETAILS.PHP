<?php

require "../auth.php";

requireLogin();

require "../database.php";


if (!isset($_GET["id"])) {

    header("Location: index.php");

    exit;
}


$id = $_GET["id"];


/*
    Get project
*/

$stmt = $conn->prepare("
    SELECT *
    FROM projects
    WHERE id = ?
");

$stmt->execute([$id]);

$project = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$project) {

    echo "Project not found.";

    exit;
}


/*
    Get tasks
*/

$stmt = $conn->prepare("
    SELECT
        tasks.*,
        workers.name AS worker_name
    FROM tasks
    LEFT JOIN workers
        ON tasks.worker_id = workers.id
    WHERE tasks.project_id = ?
    ORDER BY tasks.id DESC
");

$stmt->execute([$id]);

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Calculate project progress
*/

$totalTasks = count($tasks);

$totalProgress = 0;


foreach ($tasks as $task) {

    $totalProgress += $task["progress"];

}


if ($totalTasks > 0) {

    $projectProgress =
        $totalProgress / $totalTasks;

} else {

    $projectProgress = 0;

}


/*
    Get material usage
*/

$stmt = $conn->prepare("
    SELECT
        material_usage.*,
        materials.name AS material_name,
        materials.unit
    FROM material_usage
    LEFT JOIN materials
        ON material_usage.material_id = materials.id
    WHERE material_usage.project_id = ?
    ORDER BY material_usage.id DESC
");

$stmt->execute([$id]);

$materialUsage =
    $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Get expenses
*/

$stmt = $conn->prepare("
    SELECT *
    FROM expenses
    WHERE project_id = ?
    ORDER BY id DESC
");

$stmt->execute([$id]);

$expenses =
    $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Calculate total expenses
*/

$stmt = $conn->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM expenses
    WHERE project_id = ?
");

$stmt->execute([$id]);

$totalExpenses =
    $stmt->fetchColumn();


/*
    Calculate remaining budget
*/

$remainingBudget =
    $project["budget"] - $totalExpenses;


/*
    Calculate budget used percentage
*/

if ($project["budget"] > 0) {

    $budgetUsed =
        ($totalExpenses / $project["budget"]) * 100;

} else {

    $budgetUsed = 0;

}


/*
    Prevent percentage above 100
*/

if ($budgetUsed > 100) {

    $budgetUsed = 100;

}


/*
    Calculate total material used
*/

$stmt = $conn->prepare("
    SELECT COALESCE(SUM(quantity_used), 0)
    FROM material_usage
    WHERE project_id = ?
");

$stmt->execute([$id]);

$totalMaterialUsed =
    $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Project Details</title>

    <style>

        body {

            font-family: Arial, sans-serif;

            margin: 0;

            background-color: #f4f4f4;

        }


        .container {

            max-width: 1200px;

            margin: auto;

            padding: 30px;

        }


        .top-buttons {

            margin-bottom: 20px;

        }


        .button {

            display: inline-block;

            padding: 10px 15px;

            background-color: #333;

            color: white;

            text-decoration: none;

            border-radius: 5px;

            margin-right: 5px;

        }


        .button:hover {

            background-color: #555;

        }


        .box {

            background-color: white;

            padding: 20px;

            margin-bottom: 20px;

            border-radius: 8px;

            box-shadow:
                0 2px 5px rgba(0,0,0,0.1);

        }


        h1 {

            margin-bottom: 5px;

        }


        .info {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;

            margin-top: 20px;

        }


        .info div {

            padding: 15px;

            background-color: #f5f5f5;

            border-radius: 5px;

        }


        .progress-box {

            margin-top: 20px;

        }


        .progress-container {

            width: 100%;

            height: 30px;

            background-color: #ddd;

            border-radius: 15px;

            overflow: hidden;

        }


        .progress-bar {

            height: 100%;

            background-color: #333;

        }


        .progress-number {

            font-size: 28px;

            font-weight: bold;

            margin-top: 10px;

        }


        .budget-container {

            width: 100%;

            height: 25px;

            background-color: #ddd;

            border-radius: 15px;

            overflow: hidden;

        }


        .budget-bar {

            height: 100%;

            background-color: #555;

        }


        .budget-number {

            font-size: 24px;

            font-weight: bold;

            margin-top: 10px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            padding: 10px;

            border: 1px solid #ddd;

            text-align: left;

        }


        th {

            background-color: #eee;

        }


        .total {

            font-size: 24px;

            font-weight: bold;

        }


        @media (max-width: 700px) {

            .info {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>

<body>


<div class="container">


    <div class="top-buttons">

        <a
            href="index.php"
            class="button"
        >
            Back to Projects
        </a>


        <a
            href="../index.php"
            class="button"
        >
            Dashboard
        </a>

    </div>


    <!-- PROJECT INFORMATION -->

    <div class="box">


        <h1>

            <?= htmlspecialchars(
                $project["name"]
            ) ?>

        </h1>


        <p>

            <?= htmlspecialchars(
                $project["description"]
            ) ?>

        </p>


        <div class="info">


            <div>

                <strong>Location</strong>

                <br>

                <?= htmlspecialchars(
                    $project["location"]
                ) ?>

            </div>


            <div>

                <strong>Status</strong>

                <br>

                <?= htmlspecialchars(
                    $project["status"]
                ) ?>

            </div>


            <div>

                <strong>Start Date</strong>

                <br>

                <?= htmlspecialchars(
                    $project["start_date"]
                ) ?>

            </div>


            <div>

                <strong>End Date</strong>

                <br>

                <?= htmlspecialchars(
                    $project["end_date"]
                ) ?>

            </div>


            <div>

                <strong>Budget</strong>

                <br>

                ₱<?= number_format(
                    $project["budget"],
                    2
                ) ?>

            </div>


            <div>

                <strong>Total Expenses</strong>

                <br>

                ₱<?= number_format(
                    $totalExpenses,
                    2
                ) ?>

            </div>


            <div>

                <strong>Remaining Budget</strong>

                <br>

                ₱<?= number_format(
                    $remainingBudget,
                    2
                ) ?>

            </div>


        </div>


        <!-- PROJECT PROGRESS -->

        <div class="progress-box">


            <h2>Project Progress</h2>


            <div class="progress-container">

                <div
                    class="progress-bar"
                    style="width: <?= $projectProgress ?>%;"
                ></div>

            </div>


            <div class="progress-number">

                <?= number_format(
                    $projectProgress,
                    0
                ) ?>%

            </div>


            <p>

                Based on
                <?= $totalTasks ?>
                task(s)

            </p>


        </div>


        <!-- BUDGET PROGRESS -->

        <div class="progress-box">


            <h2>Budget Usage</h2>


            <div class="budget-container">

                <div
                    class="budget-bar"
                    style="width: <?= $budgetUsed ?>%;"
                ></div>

            </div>


            <div class="budget-number">

                <?= number_format(
                    $budgetUsed,
                    1
                ) ?>%

            </div>


            <p>

                ₱<?= number_format(
                    $totalExpenses,
                    2
                ) ?>

                spent out of

                ₱<?= number_format(
                    $project["budget"],
                    2
                ) ?>

            </p>


        </div>


    </div>


    <!-- TASKS -->

    <div class="box">


        <h2>Tasks</h2>


        <?php if (
            count($tasks) == 0
        ): ?>

            <p>
                No tasks for this project yet.
            </p>

        <?php else: ?>


            <table>

                <tr>

                    <th>Task</th>

                    <th>Worker</th>

                    <th>Start Date</th>

                    <th>Due Date</th>

                    <th>Status</th>

                    <th>Progress</th>

                </tr>


                <?php foreach (
                    $tasks as $task
                ): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $task["name"]
                            ) ?>

                        </td>


                        <td>

                            <?=
                                $task["worker_name"]
                                ? htmlspecialchars(
                                    $task["worker_name"]
                                )
                                : "Not assigned"
                            ?>

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


                        <td>

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


    <!-- MATERIAL USAGE -->

    <div class="box">


        <h2>Material Usage</h2>


        <p>

            Total Quantity Used:

            <strong>

                <?= number_format(
                    $totalMaterialUsed,
                    2
                ) ?>

            </strong>

        </p>


        <?php if (
            count($materialUsage) == 0
        ): ?>

            <p>
                No materials used for this project yet.
            </p>

        <?php else: ?>


            <table>

                <tr>

                    <th>Material</th>

                    <th>Quantity Used</th>

                    <th>Unit</th>

                    <th>Date</th>

                    <th>Notes</th>

                </tr>


                <?php foreach (
                    $materialUsage as $usage
                ): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $usage["material_name"]
                            ) ?>

                        </td>


                        <td>

                            <?= number_format(
                                $usage["quantity_used"],
                                2
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $usage["unit"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $usage["usage_date"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $usage["notes"]
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


            </table>


        <?php endif; ?>


    </div>


    <!-- EXPENSES -->

    <div class="box">


        <h2>Expenses</h2>


        <p class="total">

            Total:

            ₱<?= number_format(
                $totalExpenses,
                2
            ) ?>

        </p>


        <?php if (
            count($expenses) == 0
        ): ?>

            <p>
                No expenses recorded for this project yet.
            </p>

        <?php else: ?>


            <table>

                <tr>

                    <th>Expense</th>

                    <th>Description</th>

                    <th>Amount</th>

                    <th>Date</th>

                    <th>Category</th>

                </tr>


                <?php foreach (
                    $expenses as $expense
                ): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $expense["expense_name"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $expense["description"]
                            ) ?>

                        </td>


                        <td>

                            ₱<?= number_format(
                                $expense["amount"],
                                2
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $expense["expense_date"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $expense["category"]
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


            </table>


        <?php endif; ?>


    </div>


</div>


</body>

</html>