<?php

require "../auth.php";
requireLogin();

require "../database.php";

$error = "";

$projects = $conn->query("
    SELECT *
    FROM projects
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project_id = trim($_POST["project_id"] ?? "");
    $expense_name = trim($_POST["expense_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $amount = trim($_POST["amount"] ?? "");
    $expense_date = trim($_POST["expense_date"] ?? "");
    $category = trim($_POST["category"] ?? "");

    $allowedCategories = [
        "Labor",
        "Materials",
        "Equipment",
        "Transportation",
        "Utilities",
        "Other"
    ];


    // Validate project
    if ($project_id === "") {

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
    if ($error === "" && $expense_name === "") {

        $error = "Expense name is required.";

    } elseif ($error === "" && strlen($expense_name) > 150) {

        $error = "Expense name must not exceed 150 characters.";

    }


    // Validate description
    if ($error === "" && strlen($description) > 1000) {

        $error = "Description must not exceed 1000 characters.";

    }


    // Validate amount
    if ($error === "" && $amount === "") {

        $error = "Expense amount is required.";

    } elseif ($error === "" && !is_numeric($amount)) {

        $error = "Expense amount must be a valid number.";

    } elseif ($error === "" && (float)$amount <= 0) {

        $error = "Expense amount must be greater than zero.";

    }


    // Validate date
    if ($error === "" && $expense_date === "") {

        $error = "Expense date is required.";

    } elseif (
        $error === "" &&
        (
            !DateTime::createFromFormat("Y-m-d", $expense_date) ||
            DateTime::createFromFormat("Y-m-d", $expense_date)->format("Y-m-d") !== $expense_date
        )
    ) {

        $error = "Please enter a valid expense date.";

    }


    // Validate category
    if ($error === "" && $category === "") {

        $error = "Expense category is required.";

    } elseif (
        $error === "" &&
        !in_array($category, $allowedCategories, true)
    ) {

        $error = "Invalid expense category.";

    }


    // Save expense
    if ($error === "") {

        $stmt = $conn->prepare("
            INSERT INTO expenses
            (
                project_id,
                expense_name,
                description,
                amount,
                expense_date,
                category
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $project_id,
            $expense_name,
            $description,
            (float)$amount,
            $expense_date,
            $category
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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Expense - Construction Management System</title>

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
            background: #202328;
            color: white;
            padding: 24px 18px;
            overflow-y: auto;
        }

        .logo {
            font-size: 19px;
            font-weight: bold;
            margin-bottom: 30px;
            padding-left: 10px;
        }

        .nav a {
            display: block;
            text-decoration: none;
            color: #cfd2d6;
            padding: 12px 14px;
            border-radius: 7px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .nav a:hover {
            background: #30343a;
            color: white;
        }

        .nav a.active {
            background: #343940;
            color: white;
        }

        /* MAIN */

        .main {
            margin-left: 240px;
            min-height: 100vh;
        }

        /* TOP BAR */

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e1e3e6;
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
            max-width: 1150px;
        }

        .breadcrumb {
            color: #777;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .breadcrumb a {
            color: #555;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .page-description {
            color: #777;
            margin-top: 8px;
            margin-bottom: 28px;
            font-size: 14px;
        }

        /* ERROR */

        .error {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f1aeb5;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        /* FORM */

        .form-card {
            background: white;
            border: 1px solid #e1e3e6;
            border-radius: 10px;
            padding: 30px;
            max-width: 900px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section:last-of-type {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e5e5;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            color: #333;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d6d9dd;
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            outline: none;
            background: white;
            color: #222;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #777;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .help-text {
            font-size: 12px;
            color: #777;
            margin-top: 6px;
        }

        /* BUTTONS */

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
        }

        .btn {
            padding: 11px 18px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .btn-cancel {
            background: #e9ecef;
            color: #333;
        }

        .btn-cancel:hover {
            background: #dfe2e5;
        }

        .btn-save {
            background: #202328;
            color: white;
        }

        .btn-save:hover {
            background: #343940;
        }

        /* MOBILE */

        @media (max-width: 800px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
            }

            .content {
                padding: 25px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

        }

        @media (max-width: 600px) {

            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 0 20px;
            }

            .form-card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">
            Construction System
        </div>

        <nav class="nav">

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

            <a href="../logout.php">
                Logout
            </a>

        </nav>

    </aside>


    <!-- MAIN -->

    <main class="main">

        <!-- TOP BAR -->

        <header class="topbar">

            <div class="user-info">

                <div class="user-name">
                    <?= htmlspecialchars($_SESSION["user_name"]) ?>
                </div>

                <div class="user-role">
                    <?= htmlspecialchars($_SESSION["user_role"]) ?>
                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">

            <div class="breadcrumb">

                <a href="../index.php">
                    Dashboard
                </a>

                &nbsp; / &nbsp;

                <a href="index.php">
                    Expenses
                </a>

                &nbsp; / &nbsp;

                Add Expense

            </div>


            <h1 class="page-title">
                Add Expense
            </h1>

            <p class="page-description">
                Record a project expense and keep track of construction costs.
            </p>


            <?php if ($error !== ""): ?>

                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <!-- FORM CARD -->

            <div class="form-card">

                <form method="POST">

                    <!-- EXPENSE INFORMATION -->

                    <div class="section">

                        <div class="section-title">
                            Expense Information
                        </div>

                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="project_id">
                                    Project
                                </label>

                                <select
                                    id="project_id"
                                    name="project_id"
                                    required
                                >

                                    <option value="">
                                        Select a project
                                    </option>

                                    <?php foreach ($projects as $project): ?>

                                        <option
                                            value="<?= $project["id"] ?>"
                                            <?= (($_POST["project_id"] ?? "") == $project["id"]) ? "selected" : "" ?>
                                        >
                                            <?= htmlspecialchars($project["name"]) ?>
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
                                    placeholder="Enter expense name"
                                    value="<?= htmlspecialchars($_POST["expense_name"] ?? "") ?>"
                                    required
                                >

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
                                        Select category
                                    </option>

                                    <?php foreach ($allowedCategories as $item): ?>

                                        <option
                                            value="<?= htmlspecialchars($item) ?>"
                                            <?= (($_POST["category"] ?? "") == $item) ? "selected" : "" ?>
                                        >
                                            <?= htmlspecialchars($item) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

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
                                    min="0.01"
                                    step="0.01"
                                    placeholder="0.00"
                                    value="<?= htmlspecialchars($_POST["amount"] ?? "") ?>"
                                    required
                                >

                                <div class="help-text">
                                    Enter the total amount of the expense.
                                </div>

                            </div>


                            <div class="form-group">

                                <label for="expense_date">
                                    Expense Date
                                </label>

                                <input
                                    type="date"
                                    id="expense_date"
                                    name="expense_date"
                                    value="<?= htmlspecialchars($_POST["expense_date"] ?? date("Y-m-d")) ?>"
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
                                    placeholder="Enter additional details about this expense"
                                ><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

                            </div>

                        </div>

                    </div>


                    <!-- BUTTONS -->

                    <div class="form-actions">

                        <a href="index.php" class="btn btn-cancel">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-save"
                        >
                            Save Expense
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</body>

</html>
