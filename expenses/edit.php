<?php

require "../auth.php";
requireLogin();

require "../database.php";


// Get expense ID
$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}


// Get expense
$stmt = $conn->prepare("
    SELECT *
    FROM expenses
    WHERE id = ?
");

$stmt->execute([$id]);

$expense = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expense) {
    die("Expense record not found.");
}


// Get projects
$projects = $conn->query("
    SELECT id, name
    FROM projects
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


// Allowed categories
$categories = [
    "Labor",
    "Materials",
    "Equipment",
    "Transportation",
    "Utilities",
    "Other"
];


// Existing values
$project_id = $expense["project_id"];
$expense_name = $expense["expense_name"];
$description = $expense["description"];
$amount = $expense["amount"];
$expense_date = $expense["expense_date"];
$category = $expense["category"];

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project_id = $_POST["project_id"] ?? "";
    $expense_name = trim($_POST["expense_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $amount = $_POST["amount"] ?? "";
    $expense_date = $_POST["expense_date"] ?? "";
    $category = $_POST["category"] ?? "";


    // Validate project
    if ($project_id == "") {

        $error = "Please select a project.";

    } else {

        $stmt = $conn->prepare("
            SELECT id
            FROM projects
            WHERE id = ?
        ");

        $stmt->execute([$project_id]);

        if (!$stmt->fetch()) {
            $error = "Selected project does not exist.";
        }
    }


    // Validate expense name
    if ($error == "" && $expense_name == "") {

        $error = "Expense name is required.";

    } elseif ($error == "" && strlen($expense_name) > 150) {

        $error = "Expense name is too long.";

    }


    // Validate description
    if ($error == "" && strlen($description) > 1000) {

        $error = "Description is too long.";

    }


    // Validate amount
    if ($error == "" && ($amount == "" || !is_numeric($amount))) {

        $error = "Amount is required.";

    } elseif ($error == "" && $amount <= 0) {

        $error = "Amount must be greater than zero.";

    }


    // Validate date
    if ($error == "" && $expense_date == "") {

        $error = "Expense date is required.";

    } elseif ($error == "") {

        $date = DateTime::createFromFormat(
            "Y-m-d",
            $expense_date
        );

        if (
            !$date ||
            $date->format("Y-m-d") !== $expense_date
        ) {
            $error = "Please enter a valid expense date.";
        }
    }


    // Validate category
    if ($error == "" && !in_array($category, $categories)) {

        $error = "Please select a valid expense category.";
    }


    // Update expense
    if ($error == "") {

        $stmt = $conn->prepare("
            UPDATE expenses
            SET
                project_id = ?,
                expense_name = ?,
                description = ?,
                amount = ?,
                expense_date = ?,
                category = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $project_id,
            $expense_name,
            $description,
            $amount,
            $expense_date,
            $category,
            $id
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Expense</title>


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

            background: #1f2429;

            color: white;

            padding: 25px 15px;

            overflow-y: auto;
        }


        .logo {
            font-size: 20px;

            font-weight: bold;

            padding: 0 15px 25px;

            border-bottom: 1px solid #3a3f44;

            margin-bottom: 20px;
        }


        .menu-title {
            font-size: 11px;

            color: #9da3a8;

            padding: 0 15px;

            margin-bottom: 10px;

            text-transform: uppercase;
        }


        .menu a {
            display: block;

            color: #d8dcdf;

            text-decoration: none;

            padding: 12px 15px;

            border-radius: 7px;

            margin-bottom: 5px;

            font-size: 14px;
        }


        .menu a:hover {
            background: #343a40;

            color: white;
        }


        .menu a.active {
            background: #2e7559;

            color: white;
        }


        .logout {
            margin-top: 20px;

            border-top: 1px solid #3a3f44;

            padding-top: 20px;
        }


        /* MAIN */

        .main {
            margin-left: 240px;

            min-height: 100vh;
        }


        /* TOPBAR */

        .topbar {
            height: 70px;

            background: white;

            border-bottom: 1px solid #e2e4e7;

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

            max-width: 1100px;
        }


        .breadcrumb {
            color: #777;

            font-size: 13px;

            margin-bottom: 10px;
        }


        .breadcrumb span {
            color: #2e7559;
        }


        .page-header {
            margin-bottom: 25px;
        }


        .page-header h1 {
            margin: 0;

            font-size: 28px;

            color: #202428;
        }


        .page-header p {
            margin-top: 8px;

            color: #70757a;

            font-size: 14px;
        }


        /* FORM CARD */

        .form-card {
            background: white;

            border: 1px solid #e3e5e8;

            border-radius: 10px;

            padding: 30px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }


        .section {
            margin-bottom: 30px;
        }


        .section:last-child {
            margin-bottom: 0;
        }


        .section-title {
            font-size: 18px;

            font-weight: bold;

            margin-bottom: 20px;

            padding-bottom: 12px;

            border-bottom: 1px solid #e5e7e9;

            color: #292d31;
        }


        .form-grid {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

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

            color: #404448;
        }


        input,
        textarea,
        select {
            width: 100%;

            padding: 11px 12px;

            border: 1px solid #cfd3d7;

            border-radius: 6px;

            font-size: 14px;

            background: white;

            outline: none;

            font-family: Arial, Helvetica, sans-serif;
        }


        textarea {
            min-height: 110px;

            resize: vertical;
        }


        input:focus,
        textarea:focus,
        select:focus {
            border-color: #2e7559;

            box-shadow: 0 0 0 2px rgba(46, 117, 89, 0.1);
        }


        .help-text {
            font-size: 12px;

            color: #888;

            margin-top: 5px;
        }


        /* ERROR */

        .error {
            background: #fff1f1;

            border: 1px solid #e6b8b8;

            color: #b42323;

            padding: 12px 15px;

            border-radius: 6px;

            margin-bottom: 25px;

            font-size: 14px;
        }


        /* BUTTONS */

        .form-actions {
            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 30px;

            padding-top: 20px;

            border-top: 1px solid #e5e7e9;
        }


        .btn {
            display: inline-block;

            padding: 11px 20px;

            border-radius: 6px;

            border: none;

            font-size: 14px;

            font-weight: bold;

            text-decoration: none;

            cursor: pointer;
        }


        .btn-cancel {
            background: #e9ecef;

            color: #333;
        }


        .btn-cancel:hover {
            background: #dfe2e5;
        }


        .btn-save {
            background: #2e7559;

            color: white;
        }


        .btn-save:hover {
            background: #256047;
        }


        /* RESPONSIVE */

        @media (max-width: 900px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 700px) {

            .sidebar {
                position: relative;

                width: 100%;

                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 0 20px;
            }

            .content {
                padding: 20px;
            }

            .form-card {
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        Construction Management
    </div>


    <div class="menu-title">
        Main Menu
    </div>


    <div class="menu">

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

        <a href="../attendance/index.php">
            Attendance
        </a>

        <a href="index.php" class="active">
            Expenses
        </a>

        <a href="../reports/index.php">
            Reports
        </a>

        <a href="../users/index.php">
            Users
        </a>


        <div class="logout">

            <a href="../logout.php">
                Logout
            </a>

        </div>

    </div>

</div>



<!-- MAIN -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div class="user-info">

            <div class="user-name">

                <?php
                echo htmlspecialchars($_SESSION["user_name"]);
                ?>

            </div>


            <div class="user-role">

                <?php
                echo htmlspecialchars($_SESSION["user_role"]);
                ?>

            </div>

        </div>

    </div>



    <!-- CONTENT -->

    <div class="content">


        <!-- BREADCRUMB -->

        <div class="breadcrumb">

            Expenses
            /
            <span>Edit Expense</span>

        </div>



        <!-- PAGE HEADER -->

        <div class="page-header">

            <h1>
                Edit Expense
            </h1>

            <p>
                Update the project expense, amount, date, category, and description.
            </p>

        </div>



        <!-- FORM -->

        <div class="form-card">


            <?php if ($error != ""): ?>

                <div class="error">

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <!-- EXPENSE INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Expense Information
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="project_id">
                                Project
                            </label>

                            <select
                                id="project_id"
                                name="project_id"
                                required
                            >

                                <option value="">
                                    Select Project
                                </option>


                                <?php foreach ($projects as $project): ?>

                                    <option
                                        value="<?php echo $project["id"]; ?>"
                                        <?php
                                        echo ($project_id == $project["id"])
                                            ? "selected"
                                            : "";
                                        ?>
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $project["name"]
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>



                        <div class="form-group">

                            <label for="expense_name">
                                Expense Name
                            </label>

                            <input
                                type="text"
                                id="expense_name"
                                name="expense_name"
                                value="<?php echo htmlspecialchars($expense_name); ?>"
                                placeholder="Example: Cement Purchase"
                                required
                            >

                        </div>


                    </div>

                </div>



                <!-- COST INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Cost Information
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="amount">
                                Amount
                            </label>

                            <input
                                type="number"
                                id="amount"
                                name="amount"
                                value="<?php echo htmlspecialchars($amount); ?>"
                                min="0.01"
                                step="0.01"
                                placeholder="0.00"
                                required
                            >

                            <div class="help-text">
                                Enter the total amount of this expense.
                            </div>

                        </div>



                        <div class="form-group">

                            <label for="category">
                                Category
                            </label>

                            <select
                                id="category"
                                name="category"
                                required
                            >

                                <option value="">
                                    Select Category
                                </option>


                                <?php foreach ($categories as $item): ?>

                                    <option
                                        value="<?php echo htmlspecialchars($item); ?>"
                                        <?php
                                        echo ($category == $item)
                                            ? "selected"
                                            : "";
                                        ?>
                                    >

                                        <?php
                                        echo htmlspecialchars($item);
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>



                        <div class="form-group">

                            <label for="expense_date">
                                Expense Date
                            </label>

                            <input
                                type="date"
                                id="expense_date"
                                name="expense_date"
                                value="<?php echo htmlspecialchars($expense_date); ?>"
                                required
                            >

                        </div>


                    </div>

                </div>



                <!-- DESCRIPTION -->

                <div class="section">

                    <div class="section-title">
                        Description
                    </div>


                    <div class="form-grid">


                        <div class="form-group full">

                            <label for="description">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                placeholder="Enter details about this expense"
                            ><?php echo htmlspecialchars($description); ?></textarea>

                            <div class="help-text">
                                Optional. Add additional information about the expense.
                            </div>

                        </div>


                    </div>

                </div>



                <!-- BUTTONS -->

                <div class="form-actions">

                    <a
                        href="index.php"
                        class="btn btn-cancel"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-save"
                    >
                        Update Expense
                    </button>

                </div>


            </form>

        </div>


    </div>

</div>


</body>

</html>