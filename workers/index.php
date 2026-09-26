<?php

require "../auth.php";

requireLogin();

require "../database.php";


/*
    Search and filter
*/

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";

$statusFilter = isset($_GET["status"])
    ? $_GET["status"]
    : "";


/*
    Get workers
*/

$sql = "
    SELECT *
    FROM workers
    WHERE 1 = 1
";

$params = [];


/*
    Search by name, skill, or phone
*/

if ($search != "") {

    $sql .= "
        AND (
            name LIKE ?
            OR skill LIKE ?
            OR phone LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

}


/*
    Filter by status
*/

if ($statusFilter != "") {

    $sql .= "
        AND status = ?
    ";

    $params[] = $statusFilter;

}


$sql .= "
    ORDER BY id DESC
";


$stmt = $conn->prepare($sql);

$stmt->execute($params);

$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Get total workers
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
    Average daily rate
*/

$averageDailyRate = $conn->query("
    SELECT COALESCE(AVG(daily_rate), 0)
    FROM workers
")->fetchColumn();

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        Worker Management
    </title>


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


        /* SUMMARY */

        .summary {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

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
                1fr 180px auto auto;

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


        .result-count {

            margin-bottom: 15px;

            color: #555;

        }


        /* RESPONSIVE */

        @media (max-width: 900px) {

            .summary {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .search-form {

                grid-template-columns: 1fr;

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


    <!-- TOP -->

    <div class="top">


        <h1>

            Worker Management

        </h1>


        <a
            href="add.php"
            class="button"
        >

            Add Worker

        </a>


    </div>


    <!-- SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>

                Total Workers

            </h3>


            <div class="number">

                <?= $totalWorkers ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Active Workers

            </h3>


            <div class="number">

                <?= $activeWorkers ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Inactive Workers

            </h3>


            <div class="number">

                <?= $inactiveWorkers ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Average Daily Rate

            </h3>


            <div class="number">

                ₱<?= number_format(
                    $averageDailyRate,
                    2
                ) ?>

            </div>

        </div>


    </div>


    <!-- SEARCH -->

    <div class="search-box">


        <form
            method="GET"
            class="search-form"
        >


            <input
                type="text"
                name="search"
                placeholder="Search name, skill, or phone"
                value="<?= htmlspecialchars($search) ?>"
            >


            <select name="status">


                <option value="">

                    All Status

                </option>


                <option
                    value="Active"
                    <?= $statusFilter == "Active"
                        ? "selected"
                        : "" ?>
                >

                    Active

                </option>


                <option
                    value="Inactive"
                    <?= $statusFilter == "Inactive"
                        ? "selected"
                        : "" ?>
                >

                    Inactive

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


    <!-- WORKER TABLE -->

    <div class="table-box">


        <div class="result-count">

            Showing

            <strong>
                <?= count($workers) ?>
            </strong>

            worker(s)

        </div>


        <?php if (
            count($workers) == 0
        ): ?>


            <p>

                No workers found.

            </p>


        <?php else: ?>


            <table>


                <tr>


                    <th>

                        ID

                    </th>


                    <th>

                        Name

                    </th>


                    <th>

                        Skill

                    </th>


                    <th>

                        Phone

                    </th>


                    <th>

                        Daily Rate

                    </th>


                    <th>

                        Status

                    </th>


                    <th>

                        Actions

                    </th>


                </tr>


                <?php foreach (
                    $workers as $worker
                ): ?>


                    <tr>


                        <td>

                            <?= $worker["id"] ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $worker["name"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $worker["skill"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $worker["phone"]
                            ) ?>

                        </td>


                        <td>

                            ₱<?= number_format(
                                $worker["daily_rate"],
                                2
                            ) ?>

                        </td>


                        <td class="status">

                            <?= htmlspecialchars(
                                $worker["status"]
                            ) ?>

                        </td>


                        <td>


                            <a
                                href="edit.php?id=<?= $worker["id"] ?>"
                                class="edit"
                            >

                                Edit

                            </a>


                            |


                            <a
                                href="delete.php?id=<?= $worker["id"] ?>"
                                class="delete"
                                onclick="return confirm('Are you sure you want to delete this worker?')"
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