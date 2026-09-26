<?php

require "../auth.php";

requireLogin();

require "../database.php";


$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $category = $_POST["category"];
    $quantity = $_POST["quantity"];
    $unit = $_POST["unit"];
    $unit_cost = $_POST["unit_cost"];
    $supplier = trim($_POST["supplier"]);
    $status = $_POST["status"];


    if ($name == "") {

        $error = "Please enter the material name.";

    } elseif ($category == "") {

        $error = "Please select a category.";

    } elseif ($quantity < 0) {

        $error = "Quantity cannot be negative.";

    } elseif ($unit == "") {

        $error = "Please select a unit.";

    } elseif ($unit_cost < 0) {

        $error = "Unit cost cannot be negative.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO materials
            (
                name,
                category,
                quantity,
                unit,
                unit_cost,
                supplier,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");


        $stmt->execute([
            $name,
            $category,
            $quantity,
            $unit,
            $unit_cost,
            $supplier,
            $status
        ]);


        header("Location: index.php");

        exit;
    }

}

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Add Material | Construction Management System
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background-color: #f5f6f8;

            color: #1f2937;

        }


        /* SIDEBAR */

        .sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 240px;

            height: 100vh;

            background-color: #17191c;

            color: white;

            padding: 25px 15px;

        }


        .brand {

            padding: 5px 10px 30px;

        }


        .brand-title {

            font-size: 19px;

            font-weight: bold;

            line-height: 1.3;

        }


        .brand-subtitle {

            font-size: 12px;

            color: #9ca3af;

            margin-top: 5px;

        }


        .nav-title {

            font-size: 11px;

            color: #6b7280;

            text-transform: uppercase;

            letter-spacing: 1px;

            padding: 0 10px;

            margin-bottom: 10px;

        }


        .nav-link {

            display: flex;

            align-items: center;

            gap: 12px;

            color: #d1d5db;

            text-decoration: none;

            padding: 11px 12px;

            margin-bottom: 4px;

            border-radius: 7px;

            font-size: 14px;

        }


        .nav-link:hover {

            background-color: #272a2f;

            color: white;

        }


        .nav-link.active {

            background-color: #30343a;

            color: white;

        }


        .nav-icon {

            width: 20px;

            text-align: center;

        }


        .logout {

            margin-top: 25px;

            color: #fca5a5;

        }


        .logout:hover {

            background-color: #3b2020;

        }


        /* MAIN */

        .main {

            margin-left: 240px;

            min-height: 100vh;

        }


        /* HEADER */

        .header {

            height: 75px;

            background-color: white;

            border-bottom: 1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 35px;

        }


        .header h1 {

            margin: 0;

            font-size: 21px;

            color: #111827;

        }


        .header p {

            margin: 4px 0 0;

            font-size: 13px;

            color: #6b7280;

        }


        .user-box {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .user-info {

            text-align: right;

        }


        .user-name {

            font-size: 13px;

            font-weight: bold;

        }


        .user-role {

            font-size: 11px;

            color: #6b7280;

        }


        .user-avatar {

            width: 38px;

            height: 38px;

            border-radius: 50%;

            background-color: #272a2f;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: bold;

        }


        /* CONTENT */

        .content {

            padding: 30px 35px;

        }


        .breadcrumb {

            font-size: 12px;

            color: #6b7280;

            margin-bottom: 8px;

        }


        .breadcrumb a {

            color: #4b5563;

            text-decoration: none;

        }


        .page-title {

            margin-bottom: 25px;

        }


        .page-title h2 {

            margin: 0;

            font-size: 25px;

            color: #111827;

        }


        .page-title p {

            margin: 7px 0 0;

            color: #6b7280;

            font-size: 13px;

        }


        /* FORM */

        .form-container {

            max-width: 850px;

            background-color: white;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            overflow: hidden;

        }


        .form-header {

            padding: 22px 25px;

            border-bottom: 1px solid #e5e7eb;

        }


        .form-header h3 {

            margin: 0;

            font-size: 16px;

            color: #111827;

        }


        .form-header p {

            margin: 5px 0 0;

            color: #6b7280;

            font-size: 12px;

        }


        .form-body {

            padding: 25px;

        }


        .form-section {

            margin-bottom: 25px;

        }


        .form-section:last-child {

            margin-bottom: 0;

        }


        .section-title {

            font-size: 13px;

            font-weight: bold;

            color: #374151;

            margin-bottom: 15px;

            padding-bottom: 9px;

            border-bottom: 1px solid #f0f1f2;

        }


        .form-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 18px;

        }


        .form-group {

            display: flex;

            flex-direction: column;

        }


        .form-group.full {

            grid-column: 1 / -1;

        }


        label {

            font-size: 12px;

            font-weight: bold;

            color: #374151;

            margin-bottom: 7px;

        }


        .required {

            color: #b91c1c;

        }


        input,
        select {

            width: 100%;

            height: 42px;

            padding: 0 12px;

            border: 1px solid #d1d5db;

            border-radius: 6px;

            background-color: white;

            color: #374151;

            font-size: 13px;

            outline: none;

        }


        input:focus,
        select:focus {

            border-color: #6b7280;

            box-shadow:
                0 0 0 2px rgba(107,114,128,0.08);

        }


        .hint {

            margin-top: 6px;

            font-size: 11px;

            color: #9ca3af;

        }


        /* ERROR */

        .error {

            margin-bottom: 20px;

            padding: 12px 15px;

            background-color: #fef2f2;

            border: 1px solid #fecaca;

            color: #b91c1c;

            border-radius: 6px;

            font-size: 13px;

        }


        /* FORM FOOTER */

        .form-footer {

            padding: 18px 25px;

            background-color: #fafafa;

            border-top: 1px solid #e5e7eb;

            display: flex;

            justify-content: flex-end;

            gap: 10px;

        }


        .button {

            height: 40px;

            padding: 0 18px;

            border-radius: 6px;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            font-size: 13px;

            font-weight: bold;

            border: none;

            cursor: pointer;

        }


        .cancel-button {

            background-color: white;

            border: 1px solid #d1d5db;

            color: #4b5563;

        }


        .cancel-button:hover {

            background-color: #f3f4f6;

        }


        .save-button {

            background-color: #22252a;

            color: white;

        }


        .save-button:hover {

            background-color: #111317;

        }


        /* RESPONSIVE */

        @media (max-width: 900px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

            }


            .main {

                margin-left: 0;

            }


            .nav-link {

                display: inline-flex;

            }


            .form-container {

                max-width: 100%;

            }

        }


        @media (max-width: 600px) {

            .header {

                padding: 0 20px;

            }


            .content {

                padding: 20px;

            }


            .form-grid {

                grid-template-columns: 1fr;

            }


            .form-group.full {

                grid-column: auto;

            }


            .user-info {

                display: none;

            }

        }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">


    <div class="brand">

        <div class="brand-title">

            Construction<br>

            Management

        </div>


        <div class="brand-subtitle">

            Project & Labor System

        </div>

    </div>


    <div class="nav-title">

        Main Menu

    </div>


    <a
        href="../index.php"
        class="nav-link"
    >

        <span class="nav-icon">▣</span>

        Dashboard

    </a>


    <a
        href="../projects/index.php"
        class="nav-link"
    >

        <span class="nav-icon">▤</span>

        Projects

    </a>


    <a
        href="../workers/index.php"
        class="nav-link"
    >

        <span class="nav-icon">♙</span>

        Workers

    </a>


    <a
        href="../tasks/index.php"
        class="nav-link"
    >

        <span class="nav-icon">✓</span>

        Tasks

    </a>


    <a
        href="index.php"
        class="nav-link active"
    >

        <span class="nav-icon">▦</span>

        Materials

    </a>


    <a
        href="../material_usage/index.php"
        class="nav-link"
    >

        <span class="nav-icon">↗</span>

        Material Usage

    </a>


    <a
        href="../attendance/index.php"
        class="nav-link"
    >

        <span class="nav-icon">◷</span>

        Attendance

    </a>


    <a
        href="../expenses/index.php"
        class="nav-link"
    >

        <span class="nav-icon">₱</span>

        Expenses

    </a>


    <a
        href="../reports/index.php"
        class="nav-link"
    >

        <span class="nav-icon">▥</span>

        Reports

    </a>


    <a
        href="../users/index.php"
        class="nav-link"
    >

        <span class="nav-icon">♙</span>

        Users

    </a>


    <a
        href="../logout.php"
        class="nav-link logout"
    >

        <span class="nav-icon">↪</span>

        Logout

    </a>


</div>


<!-- MAIN -->

<div class="main">


    <!-- HEADER -->

    <div class="header">


        <div>

            <h1>

                Materials

            </h1>


            <p>

                Inventory Management

            </p>

        </div>


        <div class="user-box">


            <div class="user-info">

                <div class="user-name">

                    <?= htmlspecialchars(
                        $_SESSION["user_name"]
                    ) ?>

                </div>


                <div class="user-role">

                    <?= htmlspecialchars(
                        $_SESSION["user_role"]
                    ) ?>

                </div>

            </div>


            <div class="user-avatar">

                <?= strtoupper(
                    substr(
                        $_SESSION["user_name"],
                        0,
                        1
                    )
                ) ?>

            </div>


        </div>


    </div>


    <!-- CONTENT -->

    <div class="content">


        <div class="breadcrumb">

            <a href="index.php">

                Materials

            </a>

            &nbsp; / &nbsp;

            Add Material

        </div>


        <div class="page-title">

            <h2>

                Add Material

            </h2>


            <p>

                Add a new construction material to your inventory.

            </p>

        </div>


        <?php if ($error != ""): ?>


            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>


        <?php endif; ?>


        <div class="form-container">


            <div class="form-header">

                <h3>

                    Material Information

                </h3>


                <p>

                    Enter the details of the material below.

                </p>

            </div>


            <form
                method="POST"
            >


                <div class="form-body">


                    <!-- BASIC INFORMATION -->

                    <div class="form-section">


                        <div class="section-title">

                            Basic Information

                        </div>


                        <div class="form-grid">


                            <div class="form-group full">


                                <label>

                                    Material Name

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="name"
                                    placeholder="Enter material name"
                                    value="<?= isset($_POST["name"])
                                        ? htmlspecialchars($_POST["name"])
                                        : "" ?>"
                                    required
                                >


                                <div class="hint">

                                    Example: Cement, Steel Bar, Paint

                                </div>


                            </div>


                            <div class="form-group">


                                <label>

                                    Category

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <select
                                    name="category"
                                    required
                                >


                                    <option value="">

                                        Select Category

                                    </option>


                                    <option
                                        value="Construction"
                                        <?= (
                                            isset($_POST["category"])
                                            && $_POST["category"]
                                            == "Construction"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Construction

                                    </option>


                                    <option
                                        value="Electrical"
                                        <?= (
                                            isset($_POST["category"])
                                            && $_POST["category"]
                                            == "Electrical"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Electrical

                                    </option>


                                    <option
                                        value="Plumbing"
                                        <?= (
                                            isset($_POST["category"])
                                            && $_POST["category"]
                                            == "Plumbing"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Plumbing

                                    </option>


                                    <option
                                        value="Tools"
                                        <?= (
                                            isset($_POST["category"])
                                            && $_POST["category"]
                                            == "Tools"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Tools

                                    </option>


                                    <option
                                        value="Other"
                                        <?= (
                                            isset($_POST["category"])
                                            && $_POST["category"]
                                            == "Other"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Other

                                    </option>


                                </select>


                            </div>


                            <div class="form-group">


                                <label>

                                    Supplier

                                </label>


                                <input
                                    type="text"
                                    name="supplier"
                                    placeholder="Enter supplier name"
                                    value="<?= isset($_POST["supplier"])
                                        ? htmlspecialchars($_POST["supplier"])
                                        : "" ?>"
                                >


                            </div>


                        </div>


                    </div>


                    <!-- INVENTORY INFORMATION -->

                    <div class="form-section">


                        <div class="section-title">

                            Inventory Information

                        </div>


                        <div class="form-grid">


                            <div class="form-group">


                                <label>

                                    Quantity

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="number"
                                    name="quantity"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    value="<?= isset($_POST["quantity"])
                                        ? htmlspecialchars($_POST["quantity"])
                                        : "" ?>"
                                    required
                                >


                            </div>


                            <div class="form-group">


                                <label>

                                    Unit

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <select
                                    name="unit"
                                    required
                                >


                                    <option value="">

                                        Select Unit

                                    </option>


                                    <option
                                        value="pcs"
                                        <?= (
                                            isset($_POST["unit"])
                                            && $_POST["unit"]
                                            == "pcs"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        pcs

                                    </option>


                                    <option
                                        value="bags"
                                        <?= (
                                            isset($_POST["unit"])
                                            && $_POST["unit"]
                                            == "bags"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        bags

                                    </option>


                                    <option
                                        value="kg"
                                        <?= (
                                            isset($_POST["unit"])
                                            && $_POST["unit"]
                                            == "kg"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        kg

                                    </option>


                                    <option
                                        value="meters"
                                        <?= (
                                            isset($_POST["unit"])
                                            && $_POST["unit"]
                                            == "meters"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        meters

                                    </option>


                                    <option
                                        value="liters"
                                        <?= (
                                            isset($_POST["unit"])
                                            && $_POST["unit"]
                                            == "liters"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        liters

                                    </option>


                                    <option
                                        value="boxes"
                                        <?= (
                                            isset($_POST["unit"])
                                            && $_POST["unit"]
                                            == "boxes"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        boxes

                                    </option>


                                </select>


                            </div>


                            <div class="form-group">


                                <label>

                                    Unit Cost

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="number"
                                    name="unit_cost"
                                    min="0"
                                    step="0.01"
                                    placeholder="₱ 0.00"
                                    value="<?= isset($_POST["unit_cost"])
                                        ? htmlspecialchars($_POST["unit_cost"])
                                        : "" ?>"
                                    required
                                >


                                <div class="hint">

                                    Cost per unit of the material

                                </div>


                            </div>


                            <div class="form-group">


                                <label>

                                    Status

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <select
                                    name="status"
                                    required
                                >


                                    <option
                                        value="Available"
                                        <?= (
                                            !isset($_POST["status"])
                                            || $_POST["status"]
                                            == "Available"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Available

                                    </option>


                                    <option
                                        value="Low Stock"
                                        <?= (
                                            isset($_POST["status"])
                                            && $_POST["status"]
                                            == "Low Stock"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Low Stock

                                    </option>


                                    <option
                                        value="Out of Stock"
                                        <?= (
                                            isset($_POST["status"])
                                            && $_POST["status"]
                                            == "Out of Stock"
                                        )
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Out of Stock

                                    </option>


                                </select>


                            </div>


                        </div>


                    </div>


                </div>


                <!-- FOOTER -->

                <div class="form-footer">


                    <a
                        href="index.php"
                        class="button cancel-button"
                    >

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="button save-button"
                    >

                        Save Material

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>


</body>

</html>