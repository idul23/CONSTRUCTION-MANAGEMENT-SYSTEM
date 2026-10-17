<?php

require "../auth.php";

requireLogin();

require "../database.php";


/*
    Get material usage records
*/

$stmt = $conn->query("
    SELECT
        material_usage.*,
        projects.name AS project_name,
        materials.name AS material_name,
        materials.unit
    FROM material_usage
    LEFT JOIN projects
        ON material_usage.project_id = projects.id
    LEFT JOIN materials
        ON material_usage.material_id = materials.id
    ORDER BY
        material_usage.usage_date DESC,
        material_usage.id DESC
");

$usageRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Calculate summary
*/

$totalRecords = count($usageRecords);

$totalQuantityUsed = 0;


foreach ($usageRecords as $usage) {

    $totalQuantityUsed +=
        (float) $usage["quantity_used"];

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Material Usage</title>

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

            grid-template-columns: 1fr 1fr;

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

            font-size: 28px;

            font-weight: bold;

        }


        .table-box {

            background-color: white;

            padding: 20px;

            border-radius: 8px;

            box-shadow:
                0 2px 5px rgba(0,0,0,0.1);

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


        @media (max-width: 600px) {

            .summary {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>

<body>


<div class="container">


    <div class="top">

        <h1>Material Usage</h1>


        <a
            href="add.php"
            class="button"
        >
            Record Material Usage
        </a>

    </div>


    <!-- SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>Total Usage Records</h3>

            <div class="number">

                <?= $totalRecords ?>

            </div>

        </div>


        <div class="card">

            <h3>Total Quantity Used</h3>

            <div class="number">

                <?= number_format(
                    $totalQuantityUsed,
                    2
                ) ?>

            </div>

        </div>


    </div>


    <!-- USAGE TABLE -->

    <div class="table-box">


        <?php if ($totalRecords == 0): ?>

            <p>
                No material usage records yet.
            </p>

        <?php else: ?>


            <table>

                <tr>

                    <th>ID</th>

                    <th>Project</th>

                    <th>Material</th>

                    <th>Quantity Used</th>

                    <th>Unit</th>

                    <th>Date</th>

                    <th>Notes</th>

                    <th>Actions</th>

                </tr>


                <?php foreach (
                    $usageRecords as $usage
                ): ?>


                    <tr>


                        <td>

                            <?= $usage["id"] ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $usage["project_name"]
                            ) ?>

                        </td>


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


                        <td>


                            <a
                                href="edit.php?id=<?= $usage["id"] ?>"
                                class="edit"
                            >
                                Edit
                            </a>

                            |

                            <a
                                href="delete.php?id=<?= $usage["id"] ?>"
                                class="delete"
                                onclick="return confirm('Are you sure you want to delete this material usage record? The quantity will be returned to inventory.')"
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