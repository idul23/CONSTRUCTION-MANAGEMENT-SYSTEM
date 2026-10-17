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
    Get projects
*/

$sql = "
    SELECT *
    FROM projects
    WHERE 1 = 1
";

$params = [];


/*
    Search by project name or location
*/

if ($search != "") {

    $sql .= "
        AND (
            name LIKE ?
            OR location LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

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

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Summary
*/

$totalProjects = $conn->query("
    SELECT COUNT(*)
    FROM projects
")->fetchColumn();


$ongoingProjects = $conn->query("
    SELECT COUNT(*)
    FROM projects
    WHERE status = 'Ongoing'
")->fetchColumn();


$completedProjects = $conn->query("
    SELECT COUNT(*)
    FROM projects
    WHERE status = 'Completed'
")->fetchColumn();


$pendingProjects = $conn->query("
    SELECT COUNT(*)
    FROM projects
    WHERE status = 'Pending'
")->fetchColumn();


$totalBudget = $conn->query("
    SELECT COALESCE(SUM(budget), 0)
    FROM projects
")->fetchColumn();

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        Project Management
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
                1fr 200px auto auto;

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


        .details {

            color: #008000;

            text-decoration: none;

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


        /* RESPONSIVE */

        @media (max-width: 1000px) {

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

            Project Management

        </h1>


        <a
            href="add.php"
            class="button"
        >

            Add Project

        </a>


    </div>


    <!-- SUMMARY -->

    <div class="summary">


        <div class="card">

            <h3>

                Total Projects

            </h3>


            <div class="number">

                <?= $totalProjects ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Ongoing

            </h3>


            <div class="number">

                <?= $ongoingProjects ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Completed

            </h3>


            <div class="number">

                <?= $completedProjects ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Pending

            </h3>


            <div class="number">

                <?= $pendingProjects ?>

            </div>

        </div>


        <div class="card">

            <h3>

                Total Budget

            </h3>


            <div class="number">

                ₱<?= number_format(
                    $totalBudget,
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
                placeholder="Search project name or location"
                value="<?= htmlspecialchars($search) ?>"
            >


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
                    value="Ongoing"
                    <?= $statusFilter == "Ongoing"
                        ? "selected"
                        : "" ?>
                >

                    Ongoing

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


    <!-- PROJECT TABLE -->

    <div class="table-box">


        <div class="result-count">

            Showing

            <strong>
                <?= count($projects) ?>
            </strong>

            project(s)

        </div>


        <?php if (
            count($projects) == 0
        ): ?>


            <p>

                No projects found.

            </p>


        <?php else: ?>


            <table>


                <tr>


                    <th>

                        ID

                    </th>


                    <th>

                        Project

                    </th>


                    <th>

                        Location

                    </th>


                    <th>

                        Start Date

                    </th>


                    <th>

                        End Date

                    </th>


                    <th>

                        Budget

                    </th>


                    <th>

                        Status

                    </th>


                    <th>

                        Actions

                    </th>


                </tr>


                <?php foreach (
                    $projects as $project
                ): ?>


                    <tr>


                        <td>

                            <?= $project["id"] ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $project["name"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $project["location"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $project["start_date"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $project["end_date"]
                            ) ?>

                        </td>


                        <td>

                            ₱<?= number_format(
                                $project["budget"],
                                2
                            ) ?>

                        </td>


                        <td class="status">

                            <?= htmlspecialchars(
                                $project["status"]
                            ) ?>

                        </td>


                        <td>


                            <a
                                href="details.php?id=<?= $project["id"] ?>"
                                class="details"
                            >

                                Details

                            </a>


                            |


                            <a
                                href="edit.php?id=<?= $project["id"] ?>"
                                class="edit"
                            >

                                Edit

                            </a>


                            |


                            <a
                                href="delete.php?id=<?= $project["id"] ?>"
                                class="delete"
                                onclick="return confirm('Are you sure you want to delete this project?')"
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