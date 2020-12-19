<?php

$host         = "localhost";
$username     = "root";
$password     = "";
$dbname       = "student";

try {
  $dbconn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}