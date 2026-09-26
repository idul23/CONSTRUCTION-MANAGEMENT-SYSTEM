<?php

require "../auth.php";
requireLogin();

require "../database.php";


// Get usage ID
$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}


// Get current material usage
$stmt = $conn->prepare("
    SELECT *
    FROM material_usage
    WHERE id = ?
");

$stmt->execute([$id]);

$usage = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usage) {
    die("Material usage record not found.");
}


// Get projects
$projects = $conn->query("
    SELECT id, name
    FROM projects
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


// Get materials
$materials = $conn->query("
    SELECT id, name, quantity, unit
    FROM materials
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


// Existing values
$project_id = $usage["project_id"];
$material_id = $usage["material_id"];
$quantity_used = $usage["quantity_used"];
$usage_date = $usage["usage_date"];
$notes = $usage["notes"];

$old_material_id = $usage["material_id"];
$old_quantity = $usage["quantity_used"];

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project_id = $_POST["project_id"] ?? "";
    $material_id = $_POST["material_id"] ?? "";
    $quantity_used = $_POST["quantity_used"] ?? "";
    $usage_date = $_POST["usage_date"] ?? "";
    $notes = trim($_POST["notes"] ?? "");


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


    // Validate material
    if ($error == "" && $material_id == "") {

        $error = "Please select a material.";

    } elseif ($error == "") {

        $stmt = $conn->prepare("
            SELECT id
            FROM materials
            WHERE id = ?
        ");

        $stmt->execute([$material_id]);

        if (!$stmt->fetch()) {
            $error = "Selected material does not exist.";
        }
    }


    // Validate quantity
    if ($error == "" && ($quantity_used == "" || !is_numeric($quantity_used))) {

        $error = "Quantity used is required.";

    } elseif ($error == "" && $quantity_used <= 0) {

        $error = "Quantity used must be greater than zero.";

    }


    // Validate date
    if ($error == "" && $usage_date == "") {

        $error = "Usage date is required.";

    } elseif ($error == "") {

        $date = DateTime::createFromFormat("Y-m-d", $usage_date);

        if (!$date || $date->format("Y-m-d") !== $usage_date) {
            $error = "Please enter a valid usage date.";
        }
    }


    // Validate notes
    if ($error == "" && strlen($notes) > 1000) {

        $error = "Notes are too long.";

    }


    // Update record and inventory
    if ($error == "") {

        try {

            $conn->beginTransaction();


            /*
             * Get old material.
             * The old quantity must first be returned
             * before checking the new quantity.
             */

            $stmt = $conn->prepare("
                SELECT *
                FROM materials
                WHERE id = ?
            ");

            $stmt->execute([$old_material_id]);

            $old_material = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$old_material) {

                throw new Exception("Old material was not found.");

            }


            /*
             * If the user selected the same material:
             *
             * Current stock + old used quantity
             * = stock available before this edit.
             */

            if ($material_id == $old_material_id) {

                $available_stock =
                    $old_material["quantity"] + $old_quantity;


                if ($quantity_used > $available_stock) {

                    throw new Exception(
                        "Not enough material stock available. Available stock: "
                        . $available_stock . " " . $old_material["unit"]
                    );

                }


                $new_quantity =
                    $available_stock - $quantity_used;


                // Determine status
                if ($new_quantity <= 0) {

                    $new_quantity = 0;
                    $new_status = "Out of Stock";

                } elseif ($new_quantity <= 10) {

                    $new_status = "Low Stock";

                } else {

                    $new_status = "Available";

                }


                // Update material stock
                $stmt = $conn->prepare("
                    UPDATE materials
                    SET quantity = ?, status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $new_quantity,
                    $new_status,
                    $material_id
                ]);

            }


            /*
             * If a different material was selected:
             *
             * 1. Return old quantity to old material.
             * 2. Remove new quantity from new material.
             */

            else {

                // Restore old material stock

                $restored_quantity =
                    $old_material["quantity"] + $old_quantity;


                if ($restored_quantity <= 0) {

                    $restored_status = "Out of Stock";

                } elseif ($restored_quantity <= 10) {

                    $restored_status = "Low Stock";

                } else {

                    $restored_status = "Available";

                }


                $stmt = $conn->prepare("
                    UPDATE materials
                    SET quantity = ?, status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $restored_quantity,
                    $restored_status,
                    $old_material_id
                ]);


                // Get new material

                $stmt = $conn->prepare("
                    SELECT *
                    FROM materials
                    WHERE id = ?
                ");

                $stmt->execute([$material_id]);

                $new_material = $stmt->fetch(PDO::FETCH_ASSOC);


                if (!$new_material) {

                    throw new Exception(
                        "New material was not found."
                    );

                }


                // Check new material stock

                if ($quantity_used > $new_material["quantity"]) {

                    throw new Exception(
                        "Not enough material stock available. Available stock: "
                        . $new_material["quantity"]
                        . " "
                        . $new_material["unit"]
                    );

                }


                $new_quantity =
                    $new_material["quantity"] - $quantity_used;


                // Determine new status

                if ($new_quantity <= 0) {

                    $new_quantity = 0;
                    $new_status = "Out of Stock";

                } elseif ($new_quantity <= 10) {

                    $new_status = "Low Stock";

                } else {

                    $new_status = "Available";

                }


                // Deduct new material stock

                $stmt = $conn->prepare("
                    UPDATE materials
                    SET quantity = ?, status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $new_quantity,
                    $new_status,
                    $material_id
                ]);

            }


            // Update usage record

            $stmt = $conn->prepare("
                UPDATE material_usage
                SET
                    project_id = ?,
                    material_id = ?,
                    quantity_used = ?,
                    usage_date = ?,
                    notes = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $project_id,
                $material_id,
                $quantity_used,
                $usage_date,
                $notes,
                $id
            ]);


            $conn->commit();


            header("Location: index.php");
            exit;


        } catch (Exception $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $error = $e->getMessage();
        }
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

    <title>Edit Material Usage</title>


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

        <a href="index.php" class="active">
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

            Material Usage
            /
            <span>Edit Material Usage</span>

        </div>



        <!-- PAGE HEADER -->

        <div class="page-header">

            <h1>
                Edit Material Usage
            </h1>

            <p>
                Update the project, material, quantity, date, and notes for this usage record.
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


                <!-- USAGE INFORMATION -->

                <div class="section">

                    <div class="section-title">
                        Usage Information
                    </div>


                    <div class="form-grid">


                        <!-- PROJECT -->

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



                        <!-- MATERIAL -->

                        <div class="form-group">

                            <label for="material_id">
                                Material
                            </label>

                            <select
                                id="material_id"
                                name="material_id"
                                required
                            >

                                <option value="">
                                    Select Material
                                </option>


                                <?php foreach ($materials as $material): ?>

                                    <option
                                        value="<?php echo $material["id"]; ?>"
                                        <?php
                                        echo ($material_id == $material["id"])
                                            ? "selected"
                                            : "";
                                        ?>
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $material["name"]
                                        );
                                        ?>

                                        -
                                        <?php
                                        echo htmlspecialchars(
                                            $material["quantity"]
                                        );
                                        ?>

                                        <?php
                                        echo htmlspecialchars(
                                            $material["unit"]
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <div class="help-text">
                                Available stock is shown beside each material.
                            </div>

                        </div>



                        <!-- QUANTITY -->

                        <div class="form-group">

                            <label for="quantity_used">
                                Quantity Used
                            </label>

                            <input
                                type="number"
                                id="quantity_used"
                                name="quantity_used"
                                value="<?php echo htmlspecialchars($quantity_used); ?>"
                                min="0.01"
                                step="0.01"
                                required
                            >

                            <div class="help-text">
                                Enter the amount of material used.
                            </div>

                        </div>



                        <!-- DATE -->

                        <div class="form-group">

                            <label for="usage_date">
                                Usage Date
                            </label>

                            <input
                                type="date"
                                id="usage_date"
                                name="usage_date"
                                value="<?php echo htmlspecialchars($usage_date); ?>"
                                required
                            >

                        </div>


                    </div>

                </div>



                <!-- NOTES -->

                <div class="section">

                    <div class="section-title">
                        Notes
                    </div>


                    <div class="form-grid">


                        <div class="form-group full">

                            <label for="notes">
                                Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                placeholder="Enter additional notes"
                            ><?php echo htmlspecialchars($notes); ?></textarea>

                            <div class="help-text">
                                Optional. Add any additional information about this material usage.
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
                        Update Material Usage
                    </button>

                </div>


            </form>

        </div>


    </div>

</div>


</body>

</html>