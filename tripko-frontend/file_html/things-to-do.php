<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/tripko-system/tripko-backend/check_session.php');

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
    </script>
</head>
<body>
<?php include_once 'navbar.php'; renderNavbar(); ?>

    <section class="hero_content">
<!-- User Tours Display -->
<div class="user-tours-container">
  <h1 class="user-main-title">Wander and Experience More</h1>
  <div class="user-tours-grid" id="userToursGrid">
    <!-- Tours will load here automatically -->
    <div class="loading-state">
      <i class="fas fa-compass fa-spin"></i>
      <p>Loading available tours...</p>
    </div>
  </div>
</div>

<!-- Detailed Itinerary View -->
<div class="itinerary-detail-view" id="itineraryDetailView" style="display:none;">
  <!-- Content will be inserted here by JavaScript -->
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
  const toursGrid = document.getElementById('userToursGrid');
  const detailView = document.getElementById('itineraryDetailView');

  // Load tours from the same API endpoint used in itineraries.html
  fetch('../../tripko-backend/api/itineraries/read.php')
    .then(response => {
      if (!response.ok) throw new Error('Network response failed');
      return response.json();
    })
    .then(data => {
      if (data.records && data.records.length > 0) {
        displayTours(data.records);
      } else {
        showMessage('No tours available yet', 'info');
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      showMessage('Failed to load tours. Please try again later.', 'error');
    });

  // Display all tours in grid format
  function displayTours(tours) {
    toursGrid.innerHTML = '';
    
    tours.forEach(tour => {
        const card = document.createElement('div');
        card.className = 'tour-card';
        card.innerHTML = `
            <div class="card-image">
                <img src="${getImageUrl(tour.image_path)}" alt="${tour.name}">
            </div>
            <div class="card-content">
                <h3>${tour.name}</h3>
                <p class="tour-destination">
                    <i class='bx bxs-map-pin'></i> 
                    ${tour.destination_name || tour.destination}
                </p>
                ${tour.environmental_fee ? `
                    <p class="fee-info">
                        <i class='bx bx-money'></i> 
                        Environmental Fee: ₱${tour.environmental_fee}
                    </p>
                ` : ''}
                <button class="view-btn" data-id="${tour.itinerary_id}">View Itinerary</button>
            </div>
        `;
        toursGrid.appendChild(card);
    });

    // Add click handlers to all view buttons
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tourId = this.getAttribute('data-id');
            showTourDetails(tourId);
        });
    });
}

  // Show detailed view for a specific tour
  function showTourDetails(tourId) {
    // Fetch the specific tour details (using same endpoint as admin)
    fetch('../../tripko-backend/api/itineraries/read.php')
      .then(response => response.json())
      .then(data => {
        const tour = data.records.find(t => t.itinerary_id == tourId);
        if (tour) {
          renderDetailView(tour);
        } else {
          showMessage('Tour details not found', 'error');
        }
      })
      .catch(error => {
        console.error('Error loading tour details:', error);
        showMessage('Failed to load tour details', 'error');
      });
  }

  // Render the detailed view
  function renderDetailView(tour) {
    detailView.innerHTML = `
        <div class="detail-header">
            <button class="back-btn" id="backButton">&larr; Back to Tours</button>
            <h2 class="detail-title">${tour.name}</h2>
        </div>
        <div class="detail-image">
            <img src="${getImageUrl(tour.image_path)}" alt="${tour.name}">
        </div>
        <div class="detail-content">
            <p class="tour-destination">
                <i class='bx bxs-map-pin'></i>
                <strong>Destination:</strong> ${tour.destination_name || tour.destination}
            </p>

            
            ${tour.environmental_fee ? `
                <div class="fee-info">
                    <h3><i class='bx bx-money'></i> Environmental Fee</h3>
                    <p>₱${tour.environmental_fee}</p>
                </div>
            ` : ''}
            <div class="itinerary-info">
                <h3><i class='bx bxs-map'></i> Itinerary Details</h3>
                <div class="stops-list">
                    ${formatItineraryStops(tour.description)}
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('backButton').addEventListener('click', function() {
        detailView.style.display = 'none';
        toursGrid.style.display = 'grid';
    });
    
    toursGrid.style.display = 'none';
    detailView.style.display = 'block';
}

  // Helper function to extract numbered stops from description
  function extractStopsFromDescription(description) {
    return description.split('\n')
      .filter(line => line.match(/^\d+[\.\)]\s/))
      .map(line => line.replace(/^\d+[\.\)]\s/, '').trim());
  }

  // Helper function to get proper image URL
  function getImageUrl(imagePath) {
    if (!imagePath) return 'https://placehold.co/600x400?text=Tour+Image';
    return `/TripKo-System/uploads/${imagePath}`;
  }

  // Helper function to show messages
  function showMessage(message, type) {
    toursGrid.innerHTML = `
      <div class="${type === 'error' ? 'error-state' : 'loading-state'}">
        <i class="fas ${type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
        <p>${message}</p>
      </div>
    `;
  }
});

// Add this helper function if not already present
function formatItineraryStops(description) {
    const stops = description.split('\n')
        .filter(line => line.trim())
        .map(line => line.trim());
        
    if (stops.length === 0) {
        return '<p>Detailed itinerary will be provided upon booking.</p>';
    }

    return `<ol>${stops.map(stop => `<li>${stop}</li>`).join('')}</ol>`;
}
</script>     

      </section>
</body>
</html>