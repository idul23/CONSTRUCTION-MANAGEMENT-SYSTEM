<?php

require "../database.php";


if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}


$id = $_GET["id"];


$stmt = $conn->prepare("
    DELETE FROM expenses
    WHERE id = ?
");


$stmt->execute([$id]);


header("Location: index.php");
exit;

?>