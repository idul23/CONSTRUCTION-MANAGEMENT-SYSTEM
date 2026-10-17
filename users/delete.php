<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["user_role"] != "Administrator") {
    header("Location: ../index.php");
    exit;
}

require "../database.php";


if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}


$id = $_GET["id"];


/*
    Prevent the administrator from deleting
    the account currently being used.
*/

if ($id == $_SESSION["user_id"]) {
    header("Location: index.php");
    exit;
}


$stmt = $conn->prepare("
    DELETE FROM users
    WHERE id = ?
");

$stmt->execute([$id]);


header("Location: index.php");
exit;

?>