<?php

require "../auth.php";

requireLogin();

require "../database.php";


/*
    Get attendance records
*/

$stmt = $conn->query("
    SELECT
        attendance.*,
        workers.name AS worker_name
    FROM attendance
    LEFT JOIN workers
        ON attendance.worker_id = workers.id
    ORDER BY attendance.attendance_date DESC, attendance.id DESC
");

$attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Attendance summary
*/

$totalRecords = count($attendance);

$totalPresent = 0;

$totalAbsent = 0;

$totalHours = 0;


foreach ($attendance as $record) {

    if ($record["status"] == "Present") {

        $totalPresent++;

    }


    if ($record["status"] == "Absent") {

        $totalAbsent++;

    }


    $totalHours += $record["hours_worked"];

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Attendance Management</title>

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

            grid-template-columns: repeat(4, 1fr);

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

            font-size: 28px;

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


        .status {

            font-weight: bold;

        }


        @media (max-width: 800px) {

            .summary {

                grid-template-columns: 1fr 1fr;

            }

        }


        @media (max-width: 500px) {

            .summary {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>

<body>


<div class="container">


    <div class="top">

        <h1>Attendance Management</h1>

        <a href="add.php" class="button">
            Add Attendance
        </a>

    </div>


    <div class="summary">


        <div class="card">

            <h3>Total Records</h3>

            <div class="number">
                <?= $totalRecords ?>
            </div>

        </div>


        <div class="card">

            <h3>Present</h3>

            <div class="number">
                <?= $totalPresent ?>
            </div>

        </div>


        <div class="card">

            <h3>Absent</h3>

            <div class="number">
                <?= $totalAbsent ?>
            </div>

        </div>


        <div class="card">

            <h3>Total Hours</h3>

            <div class="number">
                <?= number_format($totalHours, 2) ?>
            </div>

        </div>


    </div>


    <div class="table-box">


        <?php if ($totalRecords == 0): ?>

            <p>No attendance records yet.</p>

        <?php else: ?>


            <table>

                <tr>

                    <th>ID</th>

                    <th>Worker</th>

                    <th>Date</th>

                    <th>Time In</th>

                    <th>Time Out</th>

                    <th>Hours</th>

                    <th>Status</th>

                    <th>Notes</th>

                    <th>Actions</th>

                </tr>


                <?php foreach ($attendance as $record): ?>

                    <tr>

                        <td>
                            <?= $record["id"] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($record["worker_name"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($record["attendance_date"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($record["time_in"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($record["time_out"]) ?>
                        </td>

                        <td>
                            <?= number_format($record["hours_worked"], 2) ?>
                        </td>

                        <td class="status">
                            <?= htmlspecialchars($record["status"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($record["notes"]) ?>
                        </td>

                        <td>

                            <a
                                href="edit.php?id=<?= $record["id"] ?>"
                                class="edit"
                            >
                                Edit
                            </a>

                            |

                            <a
                                href="delete.php?id=<?= $record["id"] ?>"
                                class="delete"
                                onclick="return confirm('Are you sure you want to delete this attendance record?')"
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