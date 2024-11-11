<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="admin-main-menu">
<head>
    <title>Plant Biodiversity Portal | Admin Main Menu</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <style>
        
    </style>
</head>

<body>
    <header>
        <h1>Plant Biodiversity Portal - Admin Dashboard</h1>
        <nav>
            <ul>            
                <li><a href="main_menu_admin.php">Main Menu</a></li>         
                <li><a href="manage_accounts.php">Manage Accounts</a></li>
                <li><a href="manage_plants.php">Manage Plants</a></li>          
                <li><a href="about.php">About</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>    
    </header>

    <main class="admin-main-menu">
        <h1>Admin Dashboard</h1>
        <p>Welcome to the Admin Main Menu. Choose an option below:</p>
        
        <div class="admin-options">
            <a href="manage_accounts.php" class="button">Manage Accounts</a>
            <a href="manage_plants.php" class="button">Manage Plants</a>
            <a href="view_resumes.php" class="button">View Resumes</a>
        </div>
    </main>
    <br><br><br>
</body>
</html>
