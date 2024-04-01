<?php
// Get the scanned QR code data from the AJAX request
$qr_data = $_POST['qr_data'];

// Extract the specific ID from the QR code data (you may need to adjust this based on the QR code format)
$matches = [];
if (preg_match('/ProductId:\s(\d+)/', $qr_data, $matches)) {
    $product_id = $matches[1];

    // Perform any operations with the extracted product ID
    // For example, you can store it in a database or use it in further processing
    // Here, we simply echo the product ID as a response
    echo $product_id;
} else {
    // If the specific ID is not found in the QR code data
    echo "Specific ID not found in QR code data.";
}
