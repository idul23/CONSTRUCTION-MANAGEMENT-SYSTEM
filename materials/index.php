<?php

require "../auth.php";

requireLogin();

require "../database.php";


/*
    Search and filters
*/

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";

$categoryFilter = isset($_GET["category"])
    ? $_GET["category"]
    : "";

$statusFilter = isset($_GET["status"])
    ? $_GET["status"]
    : "";


/*
    Get categories
*/

$categories = $conn->query("
    SELECT DISTINCT category
    FROM materials
    WHERE category != ''
    ORDER BY category
")->fetchAll(PDO::FETCH_COLUMN);


/*
    Get materials
*/

$sql = "
    SELECT *
    FROM materials
    WHERE 1 = 1
";

$params = [];


if ($search != "") {

    $sql .= "
        AND (
            name LIKE ?
            OR supplier LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;

}


if ($categoryFilter != "") {

    $sql .= "
        AND category = ?
    ";

    $params[] = $categoryFilter;

}


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

$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    Summary
*/

$totalMaterials = $conn->query("
    SELECT COUNT(*)
    FROM materials
")->fetchColumn();


$availableMaterials = $conn->query("
    SELECT COUNT(*)
    FROM materials
    WHERE status = 'Available'
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
        Materials | Construction Management System
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


        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 240px;

            height: 100vh;

            background-color: #17191c;

            color: white;

            padding: 25px 15px;

            overflow-y: auto;

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

            margin: 10px 0;

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

            transition: 0.2s;

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

            font-size: 15px;

        }


        .logout {

            margin-top: 25px;

            color: #fca5a5;

        }


        .logout:hover {

            background-color: #3b2020;

            color: #fecaca;

        }


        /* =========================
           MAIN
        ========================= */

        .main {

            margin-left: 240px;

            min-height: 100vh;

        }


        /* =========================
           HEADER
        ========================= */

        .header {

            height: 75px;

            background-color: white;

            border-bottom: 1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 35px;

        }


        .header-left h1 {

            margin: 0;

            font-size: 22px;

            color: #111827;

        }


        .header-left p {

            margin: 4px 0 0;

            font-size: 13px;

            color: #6b7280;

        }


        .user-box {

            display: flex;

            align-items: center;

            gap: 10px;

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


        /* =========================
           CONTENT
        ========================= */

        .content {

            padding: 30px 35px;

        }


        .page-heading {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .page-heading h2 {

            margin: 0;

            font-size: 25px;

            color: #111827;

        }


        .page-heading p {

            margin: 6px 0 0;

            color: #6b7280;

            font-size: 13px;

        }


        .add-button {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding: 11px 17px;

            background-color: #22252a;

            color: white;

            text-decoration: none;

            border-radius: 7px;

            font-size: 13px;

            font-weight: bold;

            transition: 0.2s;

        }


        .add-button:hover {

            background-color: #111317;

        }


        /* =========================
           SUMMARY CARDS
        ========================= */

        .summary {

            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 16px;

            margin-bottom: 25px;

        }


        .card {

            background-color: white;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            padding: 20px;

        }


        .card-top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 15px;

        }


        .card-title {

            font-size: 12px;

            color: #6b7280;

            font-weight: bold;

        }


        .card-icon {

            width: 34px;

            height: 34px;

            border-radius: 7px;

            background-color: #f1f2f4;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 15px;

        }


        .card-number {

            font-size: 25px;

            font-weight: bold;

            color: #111827;

        }


        .card-description {

            margin-top: 6px;

            font-size: 11px;

            color: #9ca3af;

        }


        /* =========================
           SEARCH PANEL
        ========================= */

        .filter-panel {

            background-color: white;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            padding: 20px;

            margin-bottom: 20px;

        }


        .filter-header {

            margin-bottom: 15px;

        }


        .filter-header h3 {

            margin: 0;

            font-size: 15px;

        }


        .filter-header p {

            margin: 4px 0 0;

            font-size: 12px;

            color: #6b7280;

        }


        .search-form {

            display: grid;

            grid-template-columns:
                1fr 200px 200px auto auto;

            gap: 10px;

        }


        input,
        select {

            width: 100%;

            height: 40px;

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

        }


        .search-button {

            height: 40px;

            padding: 0 18px;

            background-color: #22252a;

            color: white;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            font-size: 13px;

            font-weight: bold;

        }


        .search-button:hover {

            background-color: #111317;

        }


        .clear-button {

            display: flex;

            align-items: center;

            justify-content: center;

            height: 40px;

            padding: 0 18px;

            background-color: #f1f2f4;

            color: #4b5563;

            text-decoration: none;

            border-radius: 6px;

            font-size: 13px;

        }


        .clear-button:hover {

            background-color: #e5e7eb;

        }


        /* =========================
           TABLE PANEL
        ========================= */

        .table-panel {

            background-color: white;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            overflow: hidden;

        }


        .table-header {

            padding: 20px;

            border-bottom: 1px solid #e5e7eb;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .table-header h3 {

            margin: 0;

            font-size: 16px;

        }


        .result-count {

            font-size: 12px;

            color: #6b7280;

        }


        .table-wrapper {

            overflow-x: auto;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 1050px;

        }


        th {

            background-color: #f9fafb;

            color: #6b7280;

            font-size: 11px;

            text-transform: uppercase;

            letter-spacing: 0.4px;

            font-weight: bold;

            padding: 13px 16px;

            text-align: left;

            border-bottom: 1px solid #e5e7eb;

        }


        td {

            padding: 15px 16px;

            font-size: 13px;

            border-bottom: 1px solid #f0f1f2;

            color: #374151;

        }


        tr:hover td {

            background-color: #fafafa;

        }


        .material-name {

            font-weight: bold;

            color: #111827;

        }


        .material-id {

            color: #9ca3af;

            font-size: 12px;

        }


        .money {

            font-weight: bold;

            color: #111827;

        }


        /* =========================
           STATUS BADGES
        ========================= */

        .badge {

            display: inline-flex;

            align-items: center;

            padding: 5px 9px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: bold;

        }


        .badge-available {

            background-color: #ecfdf3;

            color: #16834d;

        }


        .badge-low {

            background-color: #fff8e6;

            color: #a16207;

        }


        .badge-out {

            background-color: #fef2f2;

            color: #b91c1c;

        }


        .badge-default {

            background-color: #f3f4f6;

            color: #4b5563;

        }


        /* =========================
           ACTIONS
        ========================= */

        .actions {

            display: flex;

            gap: 6px;

            align-items: center;

        }


        .action {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 6px 9px;

            border-radius: 5px;

            text-decoration: none;

            font-size: 11px;

            font-weight: bold;

        }


        .action-edit {

            background-color: #f1f5f9;

            color: #334155;

        }


        .action-use {

            background-color: #ecfdf3;

            color: #16834d;

        }


        .action-delete {

            background-color: #fef2f2;

            color: #b91c1c;

        }


        .action:hover {

            opacity: 0.75;

        }


        /* =========================
           EMPTY STATE
        ========================= */

        .empty {

            padding: 60px 20px;

            text-align: center;

        }


        .empty-icon {

            font-size: 35px;

            margin-bottom: 10px;

        }


        .empty h3 {

            margin: 0 0 5px;

            font-size: 16px;

        }


        .empty p {

            margin: 0;

            color: #6b7280;

            font-size: 13px;

        }


        /* =========================
           FOOTER
        ========================= */

        .footer {

            padding: 25px 0 10px;

            color: #9ca3af;

            font-size: 11px;

        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1200px) {

            .summary {

                grid-template-columns:
                    repeat(3, 1fr);

            }

        }


        @media (max-width: 950px) {

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

                margin-right: 4px;

            }


            .search-form {

                grid-template-columns:
                    1fr 1fr;

            }

        }


        @media (max-width: 650px) {

            .header {

                padding: 0 20px;

            }


            .content {

                padding: 20px;

            }


            .page-heading {

                align-items: flex-start;

                flex-direction: column;

                gap: 15px;

            }


            .summary {

                grid-template-columns: 1fr;

            }


            .search-form {

                grid-template-columns: 1fr;

            }


            .user-info {

                display: none;

            }

        }

    </style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

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


<!-- =========================
     MAIN
========================= -->

<div class="main">


    <!-- HEADER -->

    <div class="header">


        <div class="header-left">

            <h1>

                Materials

            </h1>


            <p>

                Manage construction materials and inventory

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


        <!-- PAGE HEADING -->

        <div class="page-heading">


            <div>

                <h2>

                    Material Inventory

                </h2>


                <p>

                    Track available materials, stock levels, and inventory value.

                </p>

            </div>


            <a
                href="add.php"
                class="add-button"
            >

                <span>+</span>

                Add Material

            </a>


        </div>


        <!-- SUMMARY CARDS -->

        <div class="summary">


            <div class="card">


                <div class="card-top">

                    <div class="card-title">

                        TOTAL MATERIALS

                    </div>


                    <div class="card-icon">

                        ▦

                    </div>

                </div>


                <div class="card-number">

                    <?= $totalMaterials ?>

                </div>


                <div class="card-description">

                    Items in inventory

                </div>


            </div>


            <div class="card">


                <div class="card-top">

                    <div class="card-title">

                        AVAILABLE

                    </div>


                    <div class="card-icon">

                        ✓

                    </div>

                </div>


                <div class="card-number">

                    <?= $availableMaterials ?>

                </div>


                <div class="card-description">

                    Currently available

                </div>


            </div>


            <div class="card">


                <div class="card-top">

                    <div class="card-title">

                        LOW STOCK

                    </div>


                    <div class="card-icon">

                        !

                    </div>

                </div>


                <div class="card-number">

                    <?= $lowStockMaterials ?>

                </div>


                <div class="card-description">

                    Needs attention

                </div>


            </div>


            <div class="card">


                <div class="card-top">

                    <div class="card-title">

                        OUT OF STOCK

                    </div>


                    <div class="card-icon">

                        ×

                    </div>

                </div>


                <div class="card-number">

                    <?= $outOfStockMaterials ?>

                </div>


                <div class="card-description">

                    Currently unavailable

                </div>


            </div>


            <div class="card">


                <div class="card-top">

                    <div class="card-title">

                        INVENTORY VALUE

                    </div>


                    <div class="card-icon">

                        ₱

                    </div>

                </div>


                <div class="card-number">

                    ₱<?= number_format(
                        $inventoryValue,
                        2
                    ) ?>

                </div>


                <div class="card-description">

                    Current stock value

                </div>


            </div>


        </div>


        <!-- FILTER PANEL -->

        <div class="filter-panel">


            <div class="filter-header">

                <h3>

                    Search and Filter

                </h3>


                <p>

                    Find materials by name, supplier, category, or stock status.

                </p>

            </div>


            <form
                method="GET"
                class="search-form"
            >


                <input
                    type="text"
                    name="search"
                    placeholder="Search material or supplier..."
                    value="<?= htmlspecialchars(
                        $search
                    ) ?>"
                >


                <select name="category">


                    <option value="">

                        All Categories

                    </option>


                    <?php foreach (
                        $categories as $category
                    ): ?>


                        <option
                            value="<?= htmlspecialchars(
                                $category
                            ) ?>"
                            <?= $categoryFilter == $category
                                ? "selected"
                                : "" ?>
                        >

                            <?= htmlspecialchars(
                                $category
                            ) ?>

                        </option>


                    <?php endforeach; ?>


                </select>


                <select name="status">


                    <option value="">

                        All Stock Status

                    </option>


                    <option
                        value="Available"
                        <?= $statusFilter == "Available"
                            ? "selected"
                            : "" ?>
                    >

                        Available

                    </option>


                    <option
                        value="Low Stock"
                        <?= $statusFilter == "Low Stock"
                            ? "selected"
                            : "" ?>
                    >

                        Low Stock

                    </option>


                    <option
                        value="Out of Stock"
                        <?= $statusFilter == "Out of Stock"
                            ? "selected"
                            : "" ?>
                    >

                        Out of Stock

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


        <!-- TABLE -->

        <div class="table-panel">


            <div class="table-header">


                <h3>

                    Inventory List

                </h3>


                <div class="result-count">

                    Showing

                    <strong>
                        <?= count($materials) ?>
                    </strong>

                    material(s)

                </div>


            </div>


            <?php if (
                count($materials) == 0
            ): ?>


                <div class="empty">


                    <div class="empty-icon">

                        ▦

                    </div>


                    <h3>

                        No materials found

                    </h3>


                    <p>

                        Try changing your search or filter.

                    </p>


                </div>


            <?php else: ?>


                <div class="table-wrapper">


                    <table>


                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    MATERIAL
                                </th>

                                <th>
                                    CATEGORY
                                </th>

                                <th>
                                    QUANTITY
                                </th>

                                <th>
                                    UNIT
                                </th>

                                <th>
                                    UNIT COST
                                </th>

                                <th>
                                    INVENTORY VALUE
                                </th>

                                <th>
                                    SUPPLIER
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    ACTIONS
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach (
                                $materials as $material
                            ): ?>


                                <tr>


                                    <td>

                                        <span class="material-id">

                                            #<?= $material["id"] ?>

                                        </span>

                                    </td>


                                    <td>

                                        <div class="material-name">

                                            <?= htmlspecialchars(
                                                $material["name"]
                                            ) ?>

                                        </div>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $material["category"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <strong>

                                            <?= number_format(
                                                $material["quantity"],
                                                2
                                            ) ?>

                                        </strong>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $material["unit"]
                                        ) ?>

                                    </td>


                                    <td class="money">

                                        ₱<?= number_format(
                                            $material["unit_cost"],
                                            2
                                        ) ?>

                                    </td>


                                    <td class="money">

                                        ₱<?= number_format(
                                            $material["quantity"]
                                            * $material["unit_cost"],
                                            2
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $material["supplier"]
                                        ) ?>

                                    </td>


                                    <td>


                                        <?php if (
                                            $material["status"]
                                            == "Available"
                                        ): ?>


                                            <span
                                                class="badge badge-available"
                                            >

                                                Available

                                            </span>


                                        <?php elseif (
                                            $material["status"]
                                            == "Low Stock"
                                        ): ?>


                                            <span
                                                class="badge badge-low"
                                            >

                                                Low Stock

                                            </span>


                                        <?php elseif (
                                            $material["status"]
                                            == "Out of Stock"
                                        ): ?>


                                            <span
                                                class="badge badge-out"
                                            >

                                                Out of Stock

                                            </span>


                                        <?php else: ?>


                                            <span
                                                class="badge badge-default"
                                            >

                                                <?= htmlspecialchars(
                                                    $material["status"]
                                                ) ?>

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td>


                                        <div class="actions">


                                            <a
                                                href="edit.php?id=<?= $material["id"] ?>"
                                                class="action action-edit"
                                            >

                                                Edit

                                            </a>


                                            <a
                                                href="use.php?id=<?= $material["id"] ?>"
                                                class="action action-use"
                                            >

                                                Use

                                            </a>


                                            <a
                                                href="delete.php?id=<?= $material["id"] ?>"
                                                class="action action-delete"
                                                onclick="return confirm('Are you sure you want to delete this material?')"
                                            >

                                                Delete

                                            </a>


                                        </div>


                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php endif; ?>


        </div>


        <div class="footer">

            Construction Project and Labor Management System

        </div>


    </div>


</div>


</body>

</html>