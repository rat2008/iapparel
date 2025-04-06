<?php 
session_start(); 

include("includes/glb_variable.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new PDO("mysql:host=localhost; dbname=apparelezi_demo; charset=utf8;", "root", "");

$acctid = 1;
$lang   = "EN";

?>