<?php
$servername = "REDACTED";
$username = "REDACTED";
$password = "REDACTED";
$dbname = "REDACTED";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die ("Failed to connect to the database. Pester <a href='https://twitter.com/SummerSolsta7'>@summersolsta7</a> on twitter about this.");
}
?>