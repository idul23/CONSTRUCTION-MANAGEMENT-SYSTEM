<?php

require "../auth.php";

requireLogin();

require "../database.php";


if (!isset($_GET["id"])) {

    header("Location: index.php");

    exit;
}


$id = $_GET["id"];


try {

    $conn->beginTransaction();


    /*
        Get material usage record
    */

    $stmt = $conn->prepare("
        SELECT *
        FROM material_usage
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $usage = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$usage) {

        throw new Exception("Material usage record not found.");

    }


    /*
        Get the material
    */

    $stmt = $conn->prepare("
        SELECT *
        FROM materials
        WHERE id = ?
    ");

    $stmt->execute([$usage["material_id"]]);

    $material = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$material) {

        throw new Exception("Material not found.");

    }


    /*
        Return the used quantity
        back to inventory
    */

    $newQuantity =
        $material["quantity"] + $usage["quantity_used"];


    /*
        Update material status
    */

    if ($newQuantity <= 0) {

        $status = "Out of Stock";

        $newQuantity = 0;

    } elseif ($newQuantity <= 10) {

        $status = "Low Stock";

    } else {

        $status = "Available";

    }


    $stmt = $conn->prepare("
        UPDATE materials
        SET quantity = ?, status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $newQuantity,
        $status,
        $usage["material_id"]
    ]);


    /*
        Delete usage record
    */

    $stmt = $conn->prepare("
        DELETE FROM material_usage
        WHERE id = ?
    ");

    $stmt->execute([$id]);


    $conn->commit();


    header("Location: index.php");

    exit;


} catch (Exception $e) {

    $conn->rollBack();

    echo "Error: " . htmlspecialchars($e->getMessage());

}

?>