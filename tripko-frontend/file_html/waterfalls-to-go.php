84% of storage used … If you run out of space, you can't save to Drive or back up Google Photos. Get 100 GB of storage for ₱119.00 ₱29.00 for 1 month.
<?php
session_start();

// Database connection configuration
$host = "localhost";     // Your database host
$username = "root";      // Your database username
$password = "";         // Your database password
$database = "tripko_db"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tourist spots with town information - only waterfalls
$query = "SELECT ts.*, t.town_name 
          FROM tourist_spots ts 
          LEFT JOIN towns t ON ts.town_id = t.town_id 
          WHERE ts.status = 'active' AND ts.category = 'Waterfalls'
          ORDER BY ts.name ASC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripKo Pangasinan - Waterfalls</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../file_css/navbar.css">
    <link rel="stylesheet" href="../file_css/waterfalls-to-go.css">
        

        
</head>
<body>
   <!-- Navigation -->
    <?php include_once 'navbar.php'; renderNavbar(); ?>

    <!-- Main Content -->
    <section class="hero_content">
        <div class="title-row">
            <a href="homepage.php" class="back-button">
                <i class='bx bx-arrow-back'></i> Back
            </a>
            <h1 class="hero_title">Where Traditions Shine, and Cultures Unite</h1>
        </div>
                
       <!-- Main Content -->
    <section class="hero_content">
        <div class="title-row">
            <a href="javascript:history.back()" class="back-button">
                <i class='bx bx-arrow-back'></i> Back
            </a>
            <h1 class="hero_title">Let the Waters Lead You to the Wild</h1>
        </div>

        <div class="scroll-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-inner">
                        <div class="card-front">
                            <img src="<?php 
                                $imagePath = $row['image_path'];
                                if (!$imagePath || $imagePath === 'placeholder.jpg') {
                                    echo '../../assets/images/placeholder.jpg';
                                } else {
                                    echo '../../../uploads/' . htmlspecialchars($imagePath);
                                }
                            ?>" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 onerror="this.src='../../assets/images/placeholder.jpg'">
                            <div class="content">
                                <div class="spot-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="spot-location"><?php echo htmlspecialchars($row['town_name']); ?></div>
                            </div>
                        </div>
                        <div class="card-back">
                            <h3 class="spot-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="spot-description"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <script>
        // Menu Toggle Functionality
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.querySelector('.menu-btn');
            const navLinks = document.querySelector('.nav-links');
            
            // Existing menu toggle functionality
            menuBtn?.addEventListener('click', () => {
                navLinks?.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.nav-content')) {
                    navLinks?.classList.remove('active');
                }
            });
            // Add click handlers for cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    // Remove flipped class from all other cards
                    cards.forEach(c => {
                        if (c !== card) c.classList.remove('flipped');
                    });
                    // Toggle flipped class on clicked card
                    card.classList.toggle('flipped');
                });
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>