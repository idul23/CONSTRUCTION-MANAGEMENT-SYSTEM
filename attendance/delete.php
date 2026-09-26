<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require "../database.php";


if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}


$id = $_GET["id"];


$stmt = $conn->prepare("
    DELETE FROM attendance
    WHERE id = ?
");


$stmt->execute([$id]);


header("Location: index.php");
exit;

?>