<?php

require "../auth.php";
requireLogin();

require "../database.php";


// Get material ID
$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}


// Get material
$stmt = $conn->prepare("
    SELECT *
    FROM materials
    WHERE id = ?
");

$stmt->execute([$id]);

$material = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$material) {
    die("Material not found.");
}


// Allowed options
$categories = [
    "Construction",
    "Electrical",
    "Plumbing",
    "Tools",
    "Other"
];

$units = [
    "pcs",
    "bags",
    "kg",
    "meters",
    "liters",
    "boxes"
];

$statuses = [
    "Available",
    "Low Stock",
    "Out of Stock"
];


// Form values
$name = $material["name"];
$category = $material["category"];
$supplier = $material["supplier"];
$quantity = $material["quantity"];
$unit = $material["unit"];
$unit_cost = $material["unit_cost"];
$status = $material["status"];

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $category = $_POST["category"] ?? "";
    $supplier = trim($_POST["supplier"] ?? "");
    $quantity = $_POST["quantity"] ?? "";
    $unit = $_POST["unit"] ?? "";
    $unit_cost = $_POST["unit_cost"] ?? "";
    $status = $_POST["status"] ?? "";


    // Validate material name
    if ($name == "") {

        $error = "Material name is required.";

    } elseif (strlen($name) > 150) {

        $error = "Material name is too long.";

    }


    // Validate category
    elseif (!in_array($category, $categories)) {

        $error = "Please select a valid category.";

    }


    // Validate quantity
    elseif ($quantity == "" || !is_numeric($quantity)) {

        $error = "Quantity is required.";

    } elseif ($quantity < 0) {

        $error = "Quantity cannot be negative.";

    }


    // Validate unit
    elseif (!in_array($unit, $units)) {

        $error = "Please select a valid unit.";

    }


    // Validate unit cost
    elseif ($unit_cost == "" || !is_numeric($unit_cost)) {

        $error = "Unit cost is required.";

    } elseif ($unit_cost < 0) {

        $error = "Unit cost cannot be negative.";

    }


    // Validate status
    elseif (!in_array($status, $statuses)) {

        $error = "Please select a valid status.";

    }


    // Update material
    else {

        $stmt = $conn->prepare("
            UPDATE materials
            SET
                name = ?,
                category = ?,
                quantity = ?,
                unit = ?,
                unit_cost = ?,
                supplier = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $name,
            $category,
            $quantity,
            $unit,
            $unit_cost,
            $supplier,
            $status,
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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Material</title>


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
        select {
            width: 100%;

            padding: 11px 12px;

            border: 1px solid #cfd3d7;

            border-radius: 6px;

            font-size: 14px;

            background: white;

            outline: none;
        }


        input:focus,
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

        <a href="index.php" class="active">
            Materials
        </a>

        <a href="../material_usage/index.php">
            Material Usage
        </a>

        <a href="../attendance/index.php">
            Attendance
        </a>

        <a href="../expenses/index.php">
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
                <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </div>

            <div class="user-role">
                <?php echo htmlspecialchars($_SESSION["user_role"]); ?>
            </div>

        </div>

    </div>



    <!-- CONTENT -->

    <div class="content">


        <!-- BREADCRUMB -->

        <div class="breadcrumb">

            Materials
            /
            <span>Edit Material</span>

        </div>



        <!-- PAGE HEADER -->

        <div class="page-header">

            <h1>
                Edit Material
            </h1>

            <p>
                Update the information and inventory details of this material.
            </p>

        </div>



        <!-- FORM -->

        <div class="form-card">


            <?php if ($error != ""): ?>

                <div class="error">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <!-- BASIC INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Basic Information
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="name">
                                Material Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?php echo htmlspecialchars($name); ?>"
                                placeholder="Example: Cement"
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
                                    Select Category
                                </option>


                                <?php foreach ($categories as $item): ?>

                                    <option
                                        value="<?php echo htmlspecialchars($item); ?>"
                                        <?php echo ($category == $item) ? "selected" : ""; ?>
                                    >

                                        <?php echo htmlspecialchars($item); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>



                        <div class="form-group full">

                            <label for="supplier">
                                Supplier
                            </label>

                            <input
                                type="text"
                                id="supplier"
                                name="supplier"
                                value="<?php echo htmlspecialchars($supplier); ?>"
                                placeholder="Enter supplier name"
                            >

                            <div class="help-text">
                                Optional. Enter the company or person supplying this material.
                            </div>

                        </div>


                    </div>

                </div>



                <!-- INVENTORY INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Inventory Information
                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="quantity">
                                Quantity
                            </label>

                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="<?php echo htmlspecialchars($quantity); ?>"
                                min="0"
                                step="0.01"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label for="unit">
                                Unit
                            </label>

                            <select
                                id="unit"
                                name="unit"
                                required
                            >

                                <option value="">
                                    Select Unit
                                </option>


                                <?php foreach ($units as $item): ?>

                                    <option
                                        value="<?php echo htmlspecialchars($item); ?>"
                                        <?php echo ($unit == $item) ? "selected" : ""; ?>
                                    >

                                        <?php echo htmlspecialchars($item); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>



                        <div class="form-group">

                            <label for="unit_cost">
                                Unit Cost
                            </label>

                            <input
                                type="number"
                                id="unit_cost"
                                name="unit_cost"
                                value="<?php echo htmlspecialchars($unit_cost); ?>"
                                min="0"
                                step="0.01"
                                required
                            >

                            <div class="help-text">
                                Cost of one unit of this material.
                            </div>

                        </div>



                        <div class="form-group">

                            <label for="status">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                            >

                                <option value="">
                                    Select Status
                                </option>


                                <?php foreach ($statuses as $item): ?>

                                    <option
                                        value="<?php echo htmlspecialchars($item); ?>"
                                        <?php echo ($status == $item) ? "selected" : ""; ?>
                                    >

                                        <?php echo htmlspecialchars($item); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

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
                        Update Material
                    </button>

                </div>


            </form>

        </div>


    </div>

</div>


</body>

</html>