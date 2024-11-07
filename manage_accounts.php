<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Include the database connection
require 'db_connection.php';

// Fetch user accounts
$result = $conn->query("SELECT email, first_name, last_name, gender, hometown FROM user_table");

// Handle deletion if requested
if (isset($_GET['delete'])) {
    $emailToDelete = $_GET['delete'];
    $deleteUser = $conn->prepare("DELETE FROM user_table WHERE email = ?");
    $deleteUser->bind_param("s", $emailToDelete);
    
    if ($deleteUser->execute()) {
        header("Location: manage_accounts.php?deleted=1");
        exit;
    } else {
        $error = "Failed to delete the user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="manage-accounts">
<head>
    <title>Plant Biodiversity Portal | Manage Accounts</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
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
                <li><a href="logout.php">Logout</a></li> <!-- Consider creating a separate logout page -->
            </ul>
        </nav>    
    </header>

    <div class="heading">
        <h2>Manage User Accounts</h2>
        <p>View, edit, or delete user accounts below</p>
    </div>

    <main class="container">
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])) : ?>
            <div class="success">User deleted successfully.</div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Hometown</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['hometown']); ?></td>
                        <td>
                            <a href="edit_user.php?email=<?php echo urlencode($row['email']); ?>" class="button edit">Edit</a>
                            <a href="manage_accounts.php?delete=<?php echo urlencode($row['email']); ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p><a href="main_menu_admin.php" class="button">Back to Admin Menu</a></p>
    </main>
    <br><br><br>
</body>
</html>

<?php
$conn->close();
?>
