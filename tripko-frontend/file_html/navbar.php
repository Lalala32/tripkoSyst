<?php
// navbar.php
// This file contains the HTML code for the navigation bar of the TripKo Pangasinan website.
function renderNavbar() {
    echo '<link rel="stylesheet" href="../file_css/navbar.css">';
    echo '<nav class="navbar">';
    echo '    <div class="nav-content">';
    echo '        <div class="logo">';
    echo '            <i class="bx bxs-plane-alt"></i> TripKo Pangasinan';
    echo '        </div>';
    echo '        <div class="nav-links">';
    echo '            <a href="homepage.php"><i class="bx bxs-home"></i> Home</a>';
    echo '            <a href="tourist_spot.php"><i class="bx bxs-map-alt"></i> Places to Go</a>';
    echo '            <a href="things-to-do.php"><i class="bx bxs-calendar-star"></i> Things to Do</a>';
    echo '            <a href="routeFinder.php"><i class="bx bxs-bus"></i> Route Finder</a>';
    echo '            <a href="terminal-locations.html"><i class="bx bxs-book-content"></i> Directory</a>';
    echo '            <a href="reports.php"><i class="bx bxs-check-circle"></i> Reports</a>';
    echo '            <a href="about.html"><i class="bx bxs-info-circle"></i> About Us</a>';
    echo '            <a href="contact.html"><i class="bx bxs-phone"></i> Contact Us</a>';
    echo '            <a href="../../tripko-backend/logout.php"><i class="bx bx-log-out"></i> Logout</a>';
    echo '        </div>';
    echo '        <div class="menu-btn">';
    echo '            <i class="bx bx-menu"></i>';
    echo '        </div>';
    echo '    </div>';
    echo '</nav>';
}
?>


