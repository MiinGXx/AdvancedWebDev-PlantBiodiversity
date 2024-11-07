<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "plantbiodiversity";

// Create a new connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

?>  
