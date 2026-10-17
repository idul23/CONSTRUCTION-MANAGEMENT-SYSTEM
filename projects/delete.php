<?php

require "../auth.php";

requireRole(["Administrator", "Project Manager"]);

require "../database.php";


if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}


$id = $_GET["id"];


$stmt = $conn->prepare("
    DELETE FROM projects
    WHERE id = ?
");


$stmt->execute([$id]);


header("Location: index.php");
exit;

?>