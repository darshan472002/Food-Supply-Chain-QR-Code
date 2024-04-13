<?php
session_start();
// Include database connection file
require_once 'connectdb.php';
$conn = openConnection();

// Check if user is logged in and has a role
if (!isset($_SESSION['role'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: login.php");
    exit();
}

// Fetch QR code image paths and product names from the products table for the current user
$sql = "SELECT qr_code_image, product_name FROM products";
$result = $conn->query($sql);

// Fetch QR code image paths and product names into an array
$qrCodeData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $qrCodeData[] = $row;
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Viewer</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/mdb.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">
    <style>
        .qr-code-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 30px;
        }

        .qr-code-item {
            margin: 10px;
            text-align: center;
        }
    </style>
</head>

<body class="violetgradient">
    <?php include 'navbar.php'; ?>
    <center>
        <div class="customalert">
            <div class="alertcontent">
                <div id="alertText"> &nbsp </div>
                <img id="qrious">
                <div id="bottomText" style="margin-top: 10px; margin-bottom: 15px;"> &nbsp </div>
                <button id="closebutton" class="formbtn"> Done </button>
            </div>
        </div>
    </center>

    <h1>QR Code Viewer</h1>
    <?php if (empty($qrCodeData)) : ?>
        <p style="color: #ff0000; font-weight: 400; font-size: 20px; text-align: center; margin-top: 20%;">No QR code images found for your account...</p>
    <?php else : ?>
        <div class="qr-code-container">
            <?php foreach ($qrCodeData as $data) : ?>
                <div class="qr-code-item">
                    <?php
                    // Convert binary data to base64 encoding
                    $base64Image = base64_encode($data['qr_code_image']);
                    // Create a data URI for the image
                    $imageSrc = 'data:image/png;base64,' . $base64Image;
                    ?>
                    <img src="<?php echo $imageSrc; ?>" alt="QR Code">
                    <p style="font-weight: 700;"><?php echo $data['product_name']; ?></p>
                    <a type="button" class="btn btn-primary" href="<?php echo $imageSrc; ?>" download="<?php echo $data['product_name']; ?>.png"><i class="fas fa-download"></i> Download QR Code</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class='box'>
        <div class='wave -one'></div>
        <div class='wave -two'></div>
        <div class='wave -three'></div>
    </div>
    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <!-- Material Design Bootstrap-->
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/mdb.min.js"></script>

    <!-- Web3.js -->
    <script src="web3.min.js"></script>

    <!-- QR Code Library-->
    <script src="./QRious/dist/qrious.js"></script>

    <!-- QR Code Reader -->
    <script src="https://rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>

    <script src="app.js"></script>

    <!-- Web3 Injection -->
    <script>
        // Initialize Web3
        var web3;
        if (typeof window.ethereum !== 'undefined') {
            web3 = new Web3(window.ethereum);
            // Request account access if needed
            window.ethereum.enable().then(function(accounts) {
                web3.eth.getAccounts().then(async function(accounts) {
                    var defaultAccount = accounts[0];
                    if (defaultAccount) {
                        web3.eth.defaultAccount = defaultAccount;
                        console.log(defaultAccount);
                        // $("#accountAddress").text(defaultAccount);
                        // Display a truncated version of the account address
                        var truncatedAddress = truncateAddress(defaultAccount, 15);
                        $("#accountAddress").text(truncatedAddress);
                    } else {
                        console.error("No default account found.");
                    }
                });
            }).catch(function(error) {
                // Handle error
                console.error("Error connecting to MetaMask:", error);
            });
        } else {
            console.warn("MetaMask is not installed. Please install MetaMask to use this website.");
        }

        // Function to truncate the address and add ellipsis
        function truncateAddress(address, length) {
            if (address.length <= length) {
                return address;
            } else {
                return address.substring(0, length) + '...';
            }
        }

        // Set the Contract
        var contract = new web3.eth.Contract(contractAbi, contractAddress);

        $("#closebutton").on("click", function() {
            $(".customalert").hide("fast", "linear");
        });

        function showAlert(message) {
            $("#alertText").html(message);
            $("#qrious").hide();
            $("#bottomText").hide();
            $(".customalert").show("fast", "linear");
        }

        $("#aboutbtn").on("click", function() {
            showAlert("<h3 style='font-weight: 800';><b>ABOUT US</b></h3><br> A Decentralised End to End Logistics Application that stores the whereabouts of product at every freight hub to the Blockchain. At consumer end, customers can easily scan product's QR CODE and get complete information about the provenance of that product hence empowering	consumers to only purchase authentic and quality products.");
        });
    </script>
</body>

</html>