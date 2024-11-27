<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Include the database connection
require 'db_connection.php';

// Fetch resumes
$result = $conn->query("SELECT email, first_name, last_name, resume FROM user_table WHERE resume IS NOT NULL AND resume != ''");

// handle delete resume
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $conn->query("UPDATE user_table SET resume = NULL WHERE email = '$email'");
    echo "<script>alert('Resume deleted successfully.');</script>";
    $resumePath = $conn->query("SELECT resume FROM user_table WHERE email = '$email'")->fetch_assoc()['resume'];
    if (file_exists($resumePath)) {
        unlink($resumePath);
    }
    header("Location: view_resumes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="view-resumes">
<head>
    <title>Plant Biodiversity Portal | Manage Resumes</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
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

    <div class="heading">
        <h2>Manage User Resumes</h2>
        <p>View or delete user resumes below:</p>
    </div>

    <main class="container">
        <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Resume</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                <td><a href="<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">View Resume</a></td>
                <td>
                    <form method="post">
                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                    <button type="submit" class="button delete" onclick="return confirm('Are you sure you want to delete this resume?');">Delete</button>
                    </form>
                </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No resumes found.</p>
        <?php endif; ?>

        <div class="button-container">
            <p><a href="main_menu_admin.php" class="button">Back to Admin Menu</a></p>
        </div>
    </main>
    <br><br><br>
</body>
</html>

<?php
$conn->close();
?>