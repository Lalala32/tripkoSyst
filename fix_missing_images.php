<?php
// Database connection
require_once 'tripko-backend/config/Database.php';

$database = new Database();
$conn = $database->getConnection();

// Directory containing uploaded images
$uploadsDir = __DIR__ . '/uploads';

// Fetch all image files in the uploads directory
$imageFiles = scandir($uploadsDir);
$imageMap = [];

// Map image files by their base name (without extension)
foreach ($imageFiles as $file) {
    $baseName = pathinfo($file, PATHINFO_FILENAME);
    $imageMap[$baseName] = $file;
}

// Fetch festivals with incomplete image_path values
$query = "SELECT id, image_path FROM festivals WHERE image_path NOT LIKE '%.%'";
$stmt = $conn->prepare($query);
$stmt->execute();
$festivals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update image_path values in the database
foreach ($festivals as $festival) {
    $id = $festival['id'];
    $imagePath = $festival['image_path'];

    if (isset($imageMap[$imagePath])) {
        $correctedPath = $imageMap[$imagePath];

        $updateQuery = "UPDATE festivals SET image_path = :image_path WHERE id = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':image_path', $correctedPath);
        $updateStmt->bindParam(':id', $id);
        $updateStmt->execute();

        echo "Updated festival ID $id with image path $correctedPath\n";
    } else {
        echo "No matching file found for festival ID $id with image path $imagePath\n";
    }
}

?>