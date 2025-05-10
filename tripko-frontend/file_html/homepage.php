<?php
session_start();
require_once '../../tripko-backend/check_session.php';

// Check if user is logged in and redirect admin to dashboard
if (!isLoggedIn()) {
    header("Location: SignUp_LogIn_Form.php");
    exit();
} elseif (isAdmin()) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripKo Pangasinan - Home</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../file_css/userpage.css">
    <link rel="stylesheet" href="../file_css/navbar.css">
  
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile menu functionality
            const menuBtn = document.querySelector('.menu-btn');
            const navLinks = document.querySelector('.nav-links');
            
            menuBtn?.addEventListener('click', () => {
                navLinks?.classList.toggle('active');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.nav-content')) {
                    navLinks?.classList.remove('active');
                }
            });

            // Background image rotation
            const backgrounds = document.querySelectorAll('.hero-background');
            const locationText = document.getElementById('currentLocation');
            let currentIndex = 0;

            // Initialize the first background
            backgrounds[0].classList.add('active');
            locationText.textContent = backgrounds[0].getAttribute('data-title');

            function rotateBackgrounds() {
                // Remove active class from current background
                backgrounds[currentIndex].classList.remove('active');
                
                // Move to next background
                currentIndex = (currentIndex + 1) % backgrounds.length;
                
                // Add active class to new background and update location
                backgrounds[currentIndex].classList.add('active');
                locationText.textContent = backgrounds[currentIndex].getAttribute('data-title');
            }

            // Start the rotation
            setInterval(rotateBackgrounds, 5000);

            // Category filtering and slider functionality
            const filterButtons = document.querySelectorAll('.filter-btn');
            const allCards = document.querySelectorAll('.card');
            const slider = document.querySelector('.cards-slider');
            const prevBtn = document.querySelector('.slider-nav.prev');
            const nextBtn = document.querySelector('.slider-nav.next');
            
            let currentSlide = 0;
            const cardsPerSlide = window.innerWidth >= 1200 ? 3 : window.innerWidth >= 768 ? 2 : 1;
            const totalSlides = Math.ceil(allCards.length / cardsPerSlide);

            // Filter functionality
            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    const category = button.getAttribute('data-category');
                    
                    allCards.forEach(card => {
                        if (category === 'all' || card.getAttribute('data-category') === category) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });

                    // Reset slider position when filtering
                    currentSlide = 0;
                    updateSliderPosition();
                });
            });

            // Slider functionality
            function updateSliderPosition() {
                const cardWidth = allCards[0].offsetWidth;
                const gap = 32; // 2rem gap
                slider.style.transform = `translateX(-${currentSlide * (cardWidth + gap) * cardsPerSlide}px)`;
                
                // Update navigation buttons
                prevBtn.classList.toggle('hidden', currentSlide === 0);
                nextBtn.classList.toggle('hidden', currentSlide >= totalSlides - 1);
            }

            function showSliderNavigation() {
                if (allCards.length > cardsPerSlide) {
                    nextBtn.classList.remove('hidden');
                }
            }

            nextBtn.addEventListener('click', () => {
                if (currentSlide < totalSlides - 1) {
                    currentSlide++;
                    updateSliderPosition();
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentSlide > 0) {
                    currentSlide--;
                    updateSliderPosition();
                }
            });

            // Initialize slider
            showSliderNavigation();

            // Handle window resize
            window.addEventListener('resize', () => {
                currentSlide = 0;
                updateSliderPosition();
            });

            // Handle image errors
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    this.src = '../images/placeholder.jpg';
                    this.alt = 'Image not available';
                });
            });
        });

        // Spot management functions
        async function editSpot(spot) {
            window.location.href = `tourist_spot.php?edit=${spot.spot_id}`;
        }

        async function deleteSpot(spotId, spotName) {
            if (confirm(`Are you sure you want to delete the tourist spot "${spotName}"?`)) {
                try {
                    const response = await fetch(`../../tripko-backend/api/tourist_spot/delete.php?spot_id=${spotId}`, {
                        method: 'DELETE'
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Tourist spot deleted successfully!');
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Failed to delete tourist spot');
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    alert('Error: ' + error.message);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const transportDropdown = document.getElementById('transportDropdown');
            const transportDropdownIcon = document.getElementById('transportDropdownIcon');

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#transportDropdown') && !e.target.closest('[onclick*="toggleTransportDropdown"]')) {
                    transportDropdown?.classList.add('hidden');
                    if (transportDropdownIcon) {
                        transportDropdownIcon.style.transform = 'rotate(0deg)';
                    }
                }
            });
        });

        function toggleTransportDropdown(event) {
            event.preventDefault();
            const dropdown = document.getElementById('transportDropdown');
            const icon = document.getElementById('transportDropdownIcon');
            dropdown.classList.toggle('hidden');
            icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    </script>
</head>
<body>
    <?php include_once 'navbar.php'; renderNavbar(); ?>

    <section class="hero">
        <?php
        // Array of featured destination images
        $backgroundImages = [
            ['url' => '../images/hundred-islands.jpg', 'title' => 'Hundred Islands'],
            ['url' => '../images/patar-white-beach.jpg', 'title' => 'Patar White Beach'],
            ['url' => '../images/cathedral-joseph.jpg', 'title' => 'Cathedral of Saint Joseph'],
            ['url' => '../images/bolinao-falls.jpg', 'title' => 'Bolinao Falls'],
            ['url' => '../images/enchanted-cave.jpg', 'title' => 'Enchanted Cave'],
            ['url' => '../images/abagatanen-beach.jpg', 'title' => 'Abagatanen Beach'],
            ['url' => '../images/agno-beach.jpg', 'title' => 'Agno Beach']
        ];

        foreach ($backgroundImages as $index => $image) {
            echo '<div class="hero-background ' . ($index === 0 ? 'active' : '') . '" 
                      style="background-image: url(\'' . $image['url'] . '\')" 
                      data-title="' . $image['title'] . '"></div>';
        }
        ?>
        
        <div class="hero-content">
            <h1>Discover Pangasinan</h1>
            <p>Experience the breathtaking natural wonders, rich cultural heritage, and unforgettable adventures in the heart of Luzon.</p>
            <div class="location-indicator">
                <i class='bx bxs-map'></i>
                <span id="currentLocation">Hundred Islands</span>
            </div>
            <div class="search-container">
                <i class='bx bx-search search-icon'></i>
                <input type="text" class="search-bar" placeholder="Search destinations, activities, or places to visit...">
            </div>
            <a href="tourist_spot.php" class="cta-button">Start Your Journey</a>
        </div>
    </section>

    <section class="featured-section">
        <h2 class="section-title">Popular Destinations</h2>
        
        <div class="category-filters">
            <button class="filter-btn active" data-category="all">All</button>
            <button class="filter-btn" data-category="Beach">Beaches</button>
            <button class="filter-btn" data-category="Islands">Islands</button>
            <button class="filter-btn" data-category="Waterfalls">Waterfalls</button>
            <button class="filter-btn" data-category="Caves">Caves</button>
            <button class="filter-btn" data-category="Churches">Churches</button>
        </div>

        <div class="cards-container">
            <!-- Navigation buttons -->
            <div class="slider-nav prev hidden"><i class='bx bx-chevron-left'></i></div>
            <div class="slider-nav next hidden"><i class='bx bx-chevron-right'></i></div>
            
            <div class="cards-slider">
                <?php
                require_once '../../tripko-backend/config/Database.php';
                
                $database = new Database();
                $conn = $database->getConnection();
                
                $sql = "SELECT 
                            ts.*, 
                            t.town_name,
                            COALESCE(SUM(vt.visitor_count), 0) as total_visitors
                        FROM tourist_spots ts
                        LEFT JOIN towns t ON ts.town_id = t.town_id
                        LEFT JOIN visitors_tracking vt ON ts.spot_id = vt.spot_id
                        WHERE ts.status = 'active' OR ts.status IS NULL
                        GROUP BY ts.spot_id
                        ORDER BY total_visitors DESC
                        LIMIT 9";
                
                $result = $conn->query($sql);
                if (!$result) {
                    throw new Exception("Query failed: " . $conn->error);
                }

                while ($spot = $result->fetch_assoc()): ?>
                    <div class="card" data-category="<?php echo htmlspecialchars($spot['category']); ?>">
                        <div class="relative">
                            <img src="../../uploads/<?php echo htmlspecialchars($spot['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($spot['name']); ?>"
                                 loading="lazy"
                                 onerror="this.src='../images/placeholder.jpg'">
                        </div>
                        <div class="card-content">
                            <div class="category-tag">
                                <span class="inline-block bg-[#255D8A] text-white text-xs px-3 py-1 rounded-full font-semibold mb-2"><?php echo htmlspecialchars($spot['category']); ?></span>
                                <span class="inline-block bg-[#ffd700] text-[#255D8A] text-xs px-3 py-1 rounded-full font-semibold mb-2 ml-2"><?php echo htmlspecialchars($spot['town_name']); ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($spot['name']); ?></h3>
                            <p><?php echo htmlspecialchars($spot['description']); ?></p>
                            <div class="contact-info">
                                <?php if (!empty($spot['contact_info'])): ?>
                                    <p class="text-sm text-gray-600"><i class="fas fa-phone-alt mr-2"></i><?php echo htmlspecialchars($spot['contact_info']); ?></p>
                                <?php endif; ?>
                            </div>
                            <a href="tourist_spot.php" class="cta-button">Learn More</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="text-center mt-8">
            <a href="tourist_spot.php" class="main-cta-button">View All Destinations</a>
        </div>
    </section>

    <div id="transportDropdown" class="hidden pl-4 space-y-2">
        <!-- Transport options will be populated by JavaScript -->
    </div>
</body>
</html>