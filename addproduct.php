<?php
session_start();
$color = "navbar-light orange darken-4";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="SHORTCUT ICON" href="img/logo.jpg" type="image/x-icon" />
  <link rel="ICON" href="img/logo.jpg" type="image/ico" />

  <title>SupplyChain - Add New Products</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/mdb.min.css" rel="stylesheet">

  <link href="css/style.css" rel="stylesheet">

</head>

<?php
if ($_SESSION['role'] == 0) {
  date_default_timezone_set('Asia/Kolkata');
  $manufactureDate = date("d F Y");
  $currentDateTime = date("d F Y - g:i A");
  $date = date_create($manufactureDate);
  date_add($date, date_interval_create_from_date_string('15 days'));
  $expiryDate = date_format($date, "d F Y");
?>

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

    <div class="bgrolesadd">
      <center>
        <div class="mycardstyle">
          <!-- <div class="greyarea"> -->
          <h5 style="font-weight: 600;"> Please Fill Product Details </h5>

          <form id="form1" autocomplete="off" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="formitem">
              <label style="text-align: left; font-weight: 400;" type="text" class="formlabel"><b> Product Name: </b></label>
              <input type="text" class="forminput" id="prodname" name="prodname" required>

              <label style="text-align: left; margin-top: 20px; font-weight: 400;" type="text" class="formlabel"><b> Manufacturer Name: </b></label>
              <input type="text" class="forminput" id="man_name" value='<?php echo $_SESSION['username']; ?>' readonly required>

              <label style="text-align: left; margin-top: 20px; font-weight: 400;" type="text" class="formlabel"><b> Manufacturing Date and Time: </b></label>
              <input type="text" class="forminput" id="man_date" name="man_date" value='<?php echo $currentDateTime; ?>' readonly required>

              <label style="text-align: left; margin-top: 20px; font-weight: 400;" type="text" class="formlabel"><b> Expiry Date (15 Days from Manufacturing Date): </b></label>
              <input type="text" class="forminput" id="exp_date" name="exp_date" value='<?php echo $expiryDate; ?>' readonly required>

              <input type="hidden" class="forminput" id="user" value=<?php echo $_SESSION['username']; ?> required>
            </div>
            <button class="formbtn" id="mansub" type="submit"><b> Register Product </b></button>
          </form>
          <!-- </div> -->
        </div>
      </center>

    <?php
  } else {
    include 'redirection.php';
    redirect('index.php');
  }
    ?>
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


      $("#manufacturer").on("click", function() {
        $("#districard").hide("fast", "linear");
        $("#manufacturercard").show("fast", "linear");
      });

      $("#distributor").on("click", function() {
        $("#manufacturercard").hide("fast", "linear");
        $("#districard").show("fast", "linear");
      });

      $("#closebutton").on("click", function() {
        $(".customalert").hide("fast", "linear");
      });

      var qr;
      $('#form1').on('submit', function(event) {
        event.preventDefault(); // to prevent page reload when form is submitted
        var prodname = "<b>Product Name: </b>" + $('#prodname').val();
        var username = $('#user').val();
        prodname = prodname + "<br>" + "," + "\n<b>Product Registered By: </b>" + username;
        var manufactureDate = "<br><b>Manufacturer Date: </b>" + $('#man_date').val();
        var expiryDate = "<br><b>Expiry Date: </b>" + $('#exp_date').val();

        console.log("Product Name : " + prodname);
        console.log("Registered By : " + prodname);
        console.log("Manufacture Date : " + manufactureDate);
        console.log("Expiry Date : " + expiryDate);

        // Function to generate QR code and set its value
        function generateQRAndStoreData() {
          // Set QR code value to formatted string
          qr.set({
            value: formattedDetails
          });

          // Convert the QR code image data to Base64
          var imageData = qr.toDataURL();

          // Make AJAX request
          $.ajax({
            type: 'POST',
            url: "save_qr.php",
            data: {
              prodname: prodname,
              username: username,
              manufactureDate: manufactureDate,
              expiryDate: expiryDate,
              imageData: imageData
            },
            success: function(response) {
              // Handle successful response
              console.log(response); // Display success message
            },
            error: function(xhr, status, error) {
              // Handle errors
              console.error(xhr.responseText);
            }
          });
        }

        // Function to remove HTML tags from a string
        function stripHtmlTags(html) {
          var doc = new DOMParser().parseFromString(html, 'text/html');
          return doc.body.textContent || "";
        }

        var formattedDetails = '';
        // Then use the function inside your promise chain
        web3.eth.getAccounts().then(async function(accounts) {
          var receipt = await contract.methods.newItem(prodname, manufactureDate, expiryDate).send({
              from: accounts[0],
              gas: 1000000
            })
            .then(receipt => {
              var productDetails = {
                ProductId: receipt.events.Added.returnValues[0],
                ProductName: prodname,
                ManufactureDate: manufactureDate,
                ExpiryDate: expiryDate
              };

              // Extract the values from the productDetails object
              var values = Object.values(productDetails);

              // Remove HTML tags from ProductName, ManufactureDate, and ExpiryDate
              var productName = stripHtmlTags(productDetails.ProductName);
              var manufacturedate = stripHtmlTags(productDetails.ManufactureDate);
              var expirydate = stripHtmlTags(productDetails.ExpiryDate);

              // Create a formatted string with the values of Product Name, Manufacturer Date, and Expiry Date
              formattedDetails = `ProductId: ${productDetails.ProductId}, \n${productName}, \n${manufacturedate}, \n${expirydate}`;

              // Log the formatted product details
              console.log(formattedDetails);

              // Call function to generate QR code and store data
              generateQRAndStoreData();

              // Display success message
              var msg = "<h5 style='color: #53D769'><b>Item Added Successfully</b></h5><br><p>Product ID: " + receipt.events.Added.returnValues[0] + "</p>";
              $bottom = "<p style='color: red'> You may print the QR Code if required </p>"
              $("#alertText").html(msg);
              $("#qrious").show();
              $("#bottomText").html($bottom);
              $(".customalert").show("fast", "linear");
            });
          //console.log(receipt);
        });

        $("#prodname").val('');

      });

      $('#form2').on('submit', function(event) {
        event.preventDefault(); // to prevent page reload when form is submitted
        prodid = $('#prodid').val();
        prodlocation = $('#prodlocation').val();
        console.log(prodid);
        console.log(prodlocation);

        var today = new Date();
        var thisdate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();

        var info = "<div class='vertical-line'></div><div class='circle'></div>Date: " + thisdate + "<br>Current Location: " + prodlocation;

        web3.eth.getAccounts().then(async function(accounts) {
          var receipt = await contract.methods.addState(prodid, info).send({
              from: accounts[0],
              gas: 1000000
            })
            .then(receipt => {
              var msg = "Item has been updated ";
              $("#alertText").html(msg);
              $("#qrious").hide();
              $("#bottomText").hide();
              $(".customalert").show("fast", "linear");
            });
        });
        $("#prodid").val('');
        $("#prodlocation").val('');
      });


      function isInputNumber(evt) {
        var ch = String.fromCharCode(evt.which);
        if (!(/[0-9]/.test(ch))) {
          evt.preventDefault();
        }
      }

      (function() {
        qr = window.qr = new QRious({
          element: document.getElementById('qrious'),
          size: 200,
          value: ''
        });

      })();

      function openQRCamera(node) {
        var reader = new FileReader();
        reader.onload = function() {
          node.value = "";
          qrcode.callback = function(res) {
            if (res instanceof Error) {
              alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
            } else {
              node.parentNode.previousElementSibling.value = res;
              document.getElementById('searchButton').click();
            }
          };
          qrcode.decode(reader.result);
        };
        reader.readAsDataURL(node.files[0]);
      }

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