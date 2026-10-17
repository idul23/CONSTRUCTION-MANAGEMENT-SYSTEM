<?php

$database = __DIR__ . "/database/construction_management.sqlite";

try {
    $conn = new PDO("sqlite:" . $database);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>