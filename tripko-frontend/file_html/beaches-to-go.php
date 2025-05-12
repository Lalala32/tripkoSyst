<?php
session_start();

// Database connection configuration
$host = "localhost";     // Your database host
$username = "root";      // Your database username        @media (max-width: 768px) {
 // Your database password
$database = "tripko_db"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tourist spots with town information - only beaches
$query = "SELECT ts.*, t.town_name 
          FROM tourist_spots ts 
          LEFT JOIN towns t ON ts.town_id = t.town_id 
          WHERE ts.status = 'active' AND ts.category = 'Beach'
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
    <title>TripKo Pangasinan - Places to Go</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../file_css/navbar.css">
    <link rel="stylesheet" href="../file_css/beaches-to-go.css">
       
 
</head>
<body>
    <!-- Navigation -->
    <?php include_once 'navbar.php'; renderNavbar(); ?>

    <!-- Main Content -->
  
        <div class="title-row">
           
        </div>
                
       <!-- Main Content -->
    <section class="hero_content">
        <div class="title-row">
            <div class="title-row"> </div>
            <a href="javascript:history.back()" class="back-button">
                <i class='bx bx-arrow-back'></i> Back
            </a>
            <h1 class="hero_title">Where the Waves Meet your Soul</h1>
        </div>
        <div class="scroll-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-inner">
                        <div class="card-front">
                            <?php 
                                $imagePaths = [];
                                if (!empty($row['image_path'])) {
                                    $imagePaths = preg_split('/[;,]/', $row['image_path']);
                                    $imagePaths = array_map('trim', $imagePaths);
                                    $imagePaths = array_filter($imagePaths, function($img) { return $img && $img !== 'placeholder.jpg'; });
                                }
                            ?>
                            <?php if (!empty($imagePaths)): ?>
                                <img src="<?php echo '../../../uploads/' . htmlspecialchars($imagePaths[0]); ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>"
                                     onerror="this.src='../../assets/images/placeholder.jpg'">
                            <?php else: ?>
                                <img src="../../assets/images/placeholder.jpg" alt="No Image">
                            <?php endif; ?>
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
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.querySelector('.menu-btn');
            const navLinks = document.querySelector('.nav-links');
            const dropdownBtn = document.querySelector('.nav-dropdown > a');
            const dropdownContent = document.querySelector('.nav-dropdown-content');
            const dropdown = document.querySelector('.nav-dropdown');
            
            menuBtn?.addEventListener('click', () => {
                navLinks?.classList.toggle('active');
            });

            // Handle dropdown click
            dropdownBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                dropdownContent?.classList.toggle('show');
                dropdown?.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.nav-dropdown')) {
                    dropdownContent?.classList.remove('show');
                    dropdown?.classList.remove('active');
                }
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