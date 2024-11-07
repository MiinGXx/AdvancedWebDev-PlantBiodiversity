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

// Create tables
$conn->query("CREATE TABLE IF NOT EXISTS user_table (
    email VARCHAR(50) NOT NULL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dob DATE NULL,
    gender VARCHAR(6) NOT NULL,
    contact_number VARCHAR(15) NULL,
    hometown VARCHAR(50) NOT NULL,
    profile_image VARCHAR(100) NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS account_table (
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type VARCHAR(5) NOT NULL,
    FOREIGN KEY (email) REFERENCES user_table(email) ON DELETE CASCADE ON UPDATE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS plant_table (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Scientific_Name VARCHAR(50) NOT NULL,
    Common_Name VARCHAR(50) NOT NULL,
    family VARCHAR(100) NOT NULL,
    genus VARCHAR(100) NOT NULL,
    species VARCHAR(100) NOT NULL,
    plants_image VARCHAR(100) NULL,
    description VARCHAR(100) NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
)");


// Check if the admin user already exists
$result = $conn->query("SELECT email FROM user_table WHERE email = 'admin@swin.edu.my'");
if ($result->num_rows == 0) {
    // Insert dummy data if it doesn't exist
    $conn->query("INSERT INTO user_table (email, first_name, last_name, gender, hometown) VALUES
        ('admin@swin.edu.my', 'Admin', 'User', 'Male', 'City')");
    $conn->query("INSERT INTO account_table (email, password, type) VALUES
        ('admin@swin.edu.my', '" . password_hash('admin', PASSWORD_BCRYPT) . "', 'admin')");
}
?>  
