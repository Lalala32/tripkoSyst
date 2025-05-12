<?php
session_start();
// Fetch festival data from the backend API (same as festival.html)
$apiUrl = 'http://localhost/tripkolang/tripko-backend/api/festival/read.php';
$response = file_get_contents($apiUrl);
$festivals = json_decode($response, true)['records'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripKo Pangasinan - Festivals to Go</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../file_css/navbar.css">
    <link rel="stylesheet" href="../file_css/festivals-to-go.css">
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

        <div class="scroll-container">
            <?php if (empty($festivals)): ?>
                <p class="text-center py-8 text-gray-500">No festivals found.</p>
            <?php else: ?>
                <?php foreach ($festivals as $f): ?>
                    <div class="card">
                        <div class="card-inner">
                            <div class="card-front">
                                <img src="<?php echo !empty($f['image_path']) && strpos($f['image_path'], '.') !== false ? '../../uploads/' . htmlspecialchars($f['image_path']) : '../../tripko-frontend/images/placeholder.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($f['name']); ?>"
                                     onerror="this.src='../../tripko-frontend/images/placeholder.jpg';">
                                <div class="content">
                                    <div class="festival-date">Date: <?php echo htmlspecialchars($f['date']); ?></div>
                                    <div class="spot-name">Name: <?php echo htmlspecialchars($f['name']); ?></div>
                                    <div class="spot-location">Location: <?php echo htmlspecialchars($f['town_name'] ?? 'Unknown'); ?></div>
                                    <div class="spot-description">Description: <?php echo htmlspecialchars($f['description']); ?></div>
                                    <div class="spot-status">Status: <span class="<?php echo $f['status'] === 'active' ? 'text-green-700' : 'text-red-700'; ?>"><?php echo htmlspecialchars($f['status'] ?? 'Unknown'); ?></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.querySelector('.menu-btn');
            const navLinks = document.querySelector('.nav-links');
            const dropdownBtn = document.querySelector('.nav-dropdown > a');
            const dropdownContent = document.querySelector('.nav-dropdown-content');
            const dropdown = document.querySelector('.nav-dropdown');
            const cards = document.querySelectorAll('.card');
            const formInputs = document.querySelectorAll('input, textarea, select');

            // Toggle navigation menu for mobile
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

            // Prevent rapid toggling of cards
            let isAnimating = false;
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    if (isAnimating) return;
                    isAnimating = true;
                    setTimeout(() => {
                        isAnimating = false;
                    }, 800); // Match the CSS transition duration
                });
            });

            // Add event listeners to inputs to prevent glitches
            formInputs.forEach(input => {
                input.addEventListener('input', () => {
                    input.classList.remove('error'); // Remove error class on input
                });

                input.addEventListener('focus', () => {
                    input.classList.add('focused'); // Add focused class for styling
                });

                input.addEventListener('blur', () => {
                    input.classList.remove('focused'); // Remove focused class
                });
            });

            // Handle form submission
            const form = document.querySelector('form');
            form?.addEventListener('submit', (e) => {
                e.preventDefault();

                // Validate inputs
                let isValid = true;
                formInputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('error');
                        isValid = false;
                    }
                });

                if (isValid) {
                    form.submit(); // Submit the form if valid
                } else {
                    alert('Please fill out all required fields.');
                }
            });

            // Log constructed image paths
            const images = document.querySelectorAll('.card img');
            images.forEach(img => {
                console.log('Image path:', img.src);
            });
        });
    </script>
</body>
</html>