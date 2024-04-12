<?php
// Start the session
session_start();

// Include the database connection file
include 'connectdb.php';
$conn = openConnection();

// Check if the image data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 0 && isset($_POST['imageData'])) {
    // Decode the Base64 encoded image data
    $imageData = $_POST['imageData'];
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $imageData = base64_decode($imageData);

    // Extract product name from the string and remove HTML tags
    $productNameWithTags = $_POST['prodname'];
    // Remove HTML tags
    $productName = str_replace('Product Name:', '', strip_tags($productNameWithTags));
    // Find the position of ':' in the string
    $pos = strpos($productName, ',');
    // Extract the substring before ','
    $productName = trim(substr($productName, 0, $pos));
    // Prepare data for insertion
    $manufacturerName = $_SESSION['username'];
    $manufactureDate = date("Y-m-d H:i:s"); // Current date and time
    $expiryDate = date("Y-m-d H:i:s", strtotime($manufactureDate . "+15 days"));

    // Store the image data in the database
    $stmt = $conn->prepare("INSERT INTO products (product_name, username, manufacture_date, expiry_date, qr_code_image) VALUES (?,?,?,?,?)");

    // Check if preparation failed
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssss", $productName, $manufacturerName, $manufactureDate, $expiryDate, $imageData);

    // Execute statement
    if ($stmt->execute()) {
        echo "QR code image stored successfully in the database and Product details added in database.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Error: Image data not received";
}

// Close the database connection
$conn->close();
