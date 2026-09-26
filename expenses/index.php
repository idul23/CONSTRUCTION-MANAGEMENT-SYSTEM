<?php

require "../auth.php";

requireLogin();

require "../database.php";


/*
    Get expenses
*/

$stmt = $conn->query("
    SELECT
        expenses.*,
        projects.name AS project_name
    FROM expenses
    LEFT JOIN projects
        ON expenses.project_id = projects.id
    ORDER BY expenses.expense_date DESC, expenses.id DESC
");

$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Calculate summary
*/

$totalExpenses = 0;

$totalRecords = count($expenses);

$highestExpense = 0;


foreach ($expenses as $expense) {

    $amount = (float) $expense["amount"];

    $totalExpenses += $amount;


    if ($amount > $highestExpense) {

        $highestExpense = $amount;

    }

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Expense Management</title>

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


        .summary {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 15px;

            margin-bottom: 25px;

        }


        .card {

            background-color: white;

            padding: 20px;

            border-radius: 8px;

            box-shadow: 0 2px 5px rgba(0,0,0,0.1);

        }


        .card h3 {

            margin-top: 0;

            color: #555;

        }


        .number {

            font-size: 26px;

            font-weight: bold;

        }


        .table-box {

            background-color: white;

            padding: 20px;

            border-radius: 8px;

            box-shadow: 0 2px 5px rgba(0,0,0,0.1);

            overflow-x: auto;

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


        @media (max-width: 700px) {

            .summary {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>

<body>


<div class="container">


    <div class="top">

        <h1>Expense Management</h1>

        <a href="add.php" class="button">
            Add Expense
        </a>

    </div>


    <div class="summary">


        <div class="card">

            <h3>Total Expense Records</h3>

            <div class="number">
                <?= $totalRecords ?>
            </div>

        </div>


        <div class="card">

            <h3>Total Expenses</h3>

            <div class="number">
                ₱<?= number_format($totalExpenses, 2) ?>
            </div>

        </div>


        <div class="card">

            <h3>Highest Expense</h3>

            <div class="number">
                ₱<?= number_format($highestExpense, 2) ?>
            </div>

        </div>


    </div>


    <div class="table-box">


        <?php if ($totalRecords == 0): ?>

            <p>No expenses recorded yet.</p>

        <?php else: ?>


            <table>

                <tr>

                    <th>ID</th>

                    <th>Project</th>

                    <th>Expense</th>

                    <th>Description</th>

                    <th>Amount</th>

                    <th>Date</th>

                    <th>Category</th>

                    <th>Actions</th>

                </tr>


                <?php foreach ($expenses as $expense): ?>

                    <tr>

                        <td>
                            <?= $expense["id"] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($expense["project_name"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($expense["expense_name"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($expense["description"]) ?>
                        </td>

                        <td>
                            ₱<?= number_format($expense["amount"], 2) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($expense["expense_date"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($expense["category"]) ?>
                        </td>

                        <td>

                            <a
                                href="edit.php?id=<?= $expense["id"] ?>"
                                class="edit"
                            >
                                Edit
                            </a>

                            |

                            <a
                                href="delete.php?id=<?= $expense["id"] ?>"
                                class="delete"
                                onclick="return confirm('Are you sure you want to delete this expense?')"
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


    <a href="../index.php" class="button">
        Back to Dashboard
    </a>


</div>


</body>

</html>