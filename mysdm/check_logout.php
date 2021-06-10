<?php
session_start();

$pdatalog="";
if (!isset($_SESSION['IDCARD'])) $pdatalog="logout";

echo $pdatalog; exit;
?>
