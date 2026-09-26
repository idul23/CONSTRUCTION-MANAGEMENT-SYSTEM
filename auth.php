<?php

session_start();


function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: ../login.php");
        exit;
    }
}


function requireRole($allowedRoles)
{
    requireLogin();

    if (!in_array($_SESSION["user_role"], $allowedRoles)) {
        header("Location: ../index.php");
        exit;
    }
}


function isAdmin()
{
    return isset($_SESSION["user_role"])
        && $_SESSION["user_role"] == "Administrator";
}


function isProjectManager()
{
    return isset($_SESSION["user_role"])
        && $_SESSION["user_role"] == "Project Manager";
}


function isSiteStaff()
{
    return isset($_SESSION["user_role"])
        && $_SESSION["user_role"] == "Site Staff";
}

?>