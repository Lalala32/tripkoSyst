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

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            const exploreButton = document.getElementById('exploreButton');
            let selectedSpotId = null;            // Search input handler
            let searchTimeout;
            searchInput?.addEventListener('input', async function() {
                const query = this.value.trim().toLowerCase();
                
                // Clear the previous timeout
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                // Add a small delay to prevent too many API calls
                searchTimeout = setTimeout(async () => {

                try {
                    const response = await fetch('../../tripko-backend/api/tourist_spot/search.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ search: query })
                    });

                    const data = await response.json();
                      if (data.records && data.records.length > 0) {
                        // Check for exact match
                        const exactMatch = data.records.find(spot => 
                            spot.name.toLowerCase() === query
                        );
                          if (exactMatch) {
                            // Redirect immediately for exact match with both town_id and spot_id
                            window.location.href = `user side/municipality.php?id=${exactMatch.town_id}&spot=${exactMatch.spot_id}`;
                            return;
                        }
                        
                        searchResults.style.display = 'block';// Add click handlers for results
                        document.querySelectorAll('.search-result-item').forEach(item => {
                            item.addEventListener('click', function() {
                                selectedSpotId = Number(this.dataset.spotId);
                                console.log('Selected spot ID set to:', selectedSpotId, typeof selectedSpotId);
                                searchInput.value = this.querySelector('div > div:first-child').textContent;
                                searchResults.style.display = 'none';
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div class="search-result-item">No results found</div>';
                        searchResults.style.display = 'block';
                    }                } catch (error) {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="search-result-item">Error performing search</div>';
                    searchResults.style.display = 'block';
                }
                }, 300); // 300ms delay before searching
            });            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchResults?.contains(e.target) && e.target !== searchInput) {
                    searchResults.style.display = 'none';
                }
            });            // Handle explore button click
            exploreButton?.addEventListener('click', async function() {
                if (!selectedSpotId) {
                    window.location.href = 'user side/municipality.php';
                    return;
                }

                try {
                    const response = await fetch('../../tripko-backend/api/tourist_spot/read.php', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    const spotIdNum = Number(selectedSpotId);
                    const spot = data.records.find(s => Number(s.spot_id) === spotIdNum);

                    if (spot?.town_id) {
                        window.location.href = `user side/municipality.php?id=${spot.town_id}`;
                    } else {
                        window.location.href = 'user side/municipality.php';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    window.location.href = 'user side/municipality.php';
                }
            });

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
    </script>
</head>
<body>
    <?php include 'navbar.php'; renderNavbar(); ?>

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
                <input type="text" id="searchInput" placeholder="Search tourist spots...">
                <div id="searchResults" class="search-results"></div>
            </div>
        </div>
    </section>

    <section class="featured-section">
        <h2 class="section-title">Popular Destinations</h2>
        
        <div class="category-filters">
            <a href="user side/places-to-go.php" class="filter-btn">Beaches</a>
            <a href="user side/islands-to-go.php" class="filter-btn">Islands</a>
            <a href="user side/waterfalls-to-go.php" class="filter-btn">Waterfalls</a>
            <a href="user side/caves-to-go.php" class="filter-btn">Caves</a>
            <a href="user side/churches-to-go.php" class="filter-btn">Churches</a>
            <a href="user side/festivals-to-go.php" class="filter-btn">Festivals</a>
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
        
        // Modified query to remove visitor count and spot count
        $sql = "SELECT 
                    t.town_id,
                    t.town_name,
                    GROUP_CONCAT(DISTINCT ts.category) as categories
                FROM towns t
                INNER JOIN tourist_spots ts ON t.town_id = ts.town_id
                WHERE ts.status = 'active'
                GROUP BY t.town_id
                ORDER BY t.town_name ASC
                LIMIT 9";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $municipalities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($municipalities as $muni): ?>
            <div class="card" data-category="municipality">
                <div class="relative">
                    <img src="../uploads/<?php echo strtolower($muni['town_name']); ?>.jpg" 
                         alt="<?php echo htmlspecialchars($muni['town_name']); ?>"
                         loading="lazy"
                         onerror="this.src='../images/placeholder.jpg'">
                </div>
                <div class="card-content">
                    <h3><?php echo htmlspecialchars($muni['town_name']); ?></h3>
                    <a href="user side/municipality.php?id=<?php echo $muni['town_id']; ?>" class="cta-button">
                        Explore <?php echo htmlspecialchars($muni['town_name']); ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>