<!DOCTYPE html>
<html lang="en" class="about">
<head>
    <title>Plant Biodiversity Portal | About</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>          
    <header>
        <h1>Plant Biodiversity Portal</h1>
        <nav>
            <ul>            
                <li><a href="index.php">Home</a></li>          
                <li><a href="main_menu.php">Main Menu</a></li>             
                <li><a href="index.php">Logout</a></li>
            </ul>
        </nav>    
    </header>

    <main>
        <section class="hero">
            <h2>About</h2>
            <p>Presenting the taks completed, tasks not completed, frameworks/libraries used, and a link to the video presentation.</p>
        </section>

        <h3>Assignment Details</h3>
        <section class="details">
            <ul>
                <li><strong>PHP Version</strong>
                <?php echo phpversion(); ?></li>
                <br>
                <li><strong>Tasks Completed:</strong>
                    <ul>
                        <li>Set up project structure</li>
                        <li>Implemented user authentication</li>
                        <li>Designed database schema</li>
                        <li>Developed main menu interface</li>
                        <li>Created about page</li>
                    </ul>
                </li>
                <br>
                <li><strong>Tasks Not Completed:</strong>
                    <ul>
                        <li>Integration with external API</li>
                        <li>Automated testing</li>
                    </ul>
                </li>
                <br>
                <li><strong>Frameworks/Libraries Used:</strong>
                    <ul>
                        <li>Bootstrap 5.1.3</li>
                        <li>jQuery 3.6.0</li>
                    </ul>
                </li>
            </ul>
        </section>

        <section class="button-group">
            <strong>Video Presentation</strong>
            <a href="https://example.com/presentation" class="button">Watch here</a> <br><br>
            <strong>Return to Home Page</strong>
            <a href="index.php" class="button">Home</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plant Biodiversity</p>
    </footer>

</body>
</html>
