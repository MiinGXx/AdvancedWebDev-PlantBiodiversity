<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Include the database connection
require 'db_connection.php';

// Handle approval and deletion actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $plantId = $_GET['id'];
    if ($_GET['action'] == 'approve') {
        $stmt = $conn->prepare("UPDATE plant_table SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $plantId);
        $stmt->execute();
        $stmt->close();
    } elseif ($_GET['action'] == 'reject') {
        $stmt = $conn->prepare("UPDATE plant_table SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $plantId);
        $stmt->execute();
        $stmt->close();
    } elseif ($_GET['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM plant_table WHERE id = ?");
        $stmt->bind_param("i", $plantId);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_plants.php");
    exit;
}

// Fetch all plants from the plant_table
$result = $conn->query("SELECT id, Scientific_Name, Common_Name, Family, Genus, Species, plants_image, description, status FROM plant_table");
?>

<!DOCTYPE html>
<html lang="en" class="manage-plants">
<head>
    <title>Plant Biodiversity Portal | Manage Plants</title>
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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>    
    </header>

    <div class="heading">
        <h2>Manage Plant Entries</h2>
        <p>View, approve, reject, or delete plant entries below:</p>
    </div>

    <main class="container">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Scientific Name</th>
                        <th>Common Name</th>
                        <th>Family</th>
                        <th>Genus</th>
                        <th>Species</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Actions</th> <!-- Updated header -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Scientific_Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Common_Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Family']); ?></td>
                            <td><?php echo htmlspecialchars($row['Genus']); ?></td>
                            <td><?php echo htmlspecialchars($row['Species']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['plants_image']); ?>" alt="Plant Image" width="50"></td>
                            <td><a href="<?php echo htmlspecialchars($row['description']); ?>" download>Download</a></td>
                            <td class="status-actions">
                                <?php 
                                    $status = $row['status'];
            
                                    // Determine which buttons to display and disable
                                    if ($status === 'approved') {
                                        // Disable the Approve button
                                        echo '<button class="approve-button" disabled>Approve</button>';
                                        // Enable the Reject button
                                        echo '<a href="manage_plants.php?action=reject&id=' . $row['id'] . '" class="reject-button">Reject</a>';
                                    } elseif ($status === 'rejected') {
                                        // Enable the Approve button
                                        echo '<a href="manage_plants.php?action=approve&id=' . $row['id'] . '" class="approve-button">Approve</a>';
                                        // Disable the Reject button
                                        echo '<button class="reject-button" disabled>Reject</button>';
                                    } else {
                                        // If status is pending or other, show both buttons enabled
                                        echo '<a href="manage_plants.php?action=approve&id=' . $row['id'] . '" class="approve-button">Approve</a>';
                                        echo '<a href="manage_plants.php?action=reject&id=' . $row['id'] . '" class="reject-button">Reject</a>';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <p><a href="main_menu_admin.php" class="button">Back to Admin Menu</a></p>
    </main>
    <br><br><br>
</body>
</html>

<?php
$conn->close();
?>
