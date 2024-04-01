<?php
session_start();
$color = "navbar-dark cyan darken-3";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>SupplyChain - Scan Shipments</title>

  <link rel="SHORTCUT ICON" href="img/logo.jpg" type="image/x-icon" />
  <link rel="ICON" href="img/logo.jpg" type="image/ico" />

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/mdb.min.css" rel="stylesheet">

  <link href="css/style.css" rel="stylesheet">

</head>
<?php
if ($_SESSION['role'] == 0 || $_SESSION['role'] == 1) {
  date_default_timezone_set('Asia/Kolkata');
  $recvdate = date("d F Y - g:i A");
  $manufactureDate = date("d F Y");
  $date = date_create($manufactureDate);
  date_add($date, date_interval_create_from_date_string('15 days'));
  $expiryDate = date_format($date, 'd F Y');
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

    <div class="bgroles">
      <center>
        <div class="mycardstyle">
          <div class="greyarea" style="background-color: white;">
            <h5 style="font-weight: 600;"> Fill the Details of the Recieved Product Shipment </h5>
            <form id="form2" autocomplete="off">
              <div class="formitem">
                <label type="text" style="text-align:left;" class="formlabel"> Received Product ID: </label>
                <input type="text" class="forminput" id="prodid" onkeypress="isInputNumber(event)" required>

                <label class=qrcode-text-btn style="width:4%;display:none;">
                  <input type=file accept="image/*" id="selectedFile" style="display:none" capture=environment onchange="openQRCamera(this);" tabindex=-1>
                </label>
                <button class="qrbutton2" onclick="document.getElementById('selectedFile').click();" style="margin-bottom: 5px;margin-top: 5px;">
                  <i class='fa fa-qrcode'></i> Scan QR
                </button>
              </div>

              <div class="formitem">
                <label type="text" style="text-align:left;" class="formlabel"> Product Scanner Name: </label>
                <input type="text" class="forminput" id="prodname" value="<?php echo $_SESSION['username']; ?>" readonly required>

                <label style="text-align: left; margin-top: 20px; font-weight: 400px;" type="text" class="formlabel"> Expiry Date (15 Days from Manufacturing Date): </label>
                <input type="text" class="forminput" id="exp_date" value='<?php echo $expiryDate; ?>' readonly required>

                <label style="margin-top: 20px; text-align:left;" type="text" class="formlabel"> Recieved Date: </label>
                <input type="text" class="forminput" id="recvdate" value="<?php echo $recvdate; ?>" readonly required>

                <label style="margin-top: 20px; text-align:left;" type="text" class="formlabel"> <b>Product Scanner Location: </b></label>
                <input type="text" class="forminput" id="prodlocation" readonly required>
              </div>

              <button class="formbtn" id="mansub" type="submit">Update Product Information</button>
            </form>
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
    <script src="./dist/qrious.js"></script>

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
              // Display a truncated version of the account address
              // $("#accountAddress").text(defaultAccount);
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


      $('#form1').on('submit', function(event) {
        event.preventDefault(); // to prevent page reload when form is submitted
        prodname = $('#prodname').val();
        console.log(prodname);

        var today = new Date();
        var thisdate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();

        web3.eth.getAccounts().then(async function(accounts) {
          var receipt = await contract.methods.newItem(prodname, thisdate).send({
              from: accounts[0],
              gas: 1000000
            })

            .then(receipt => {
              var msg = "<h5 style='color: #53D769'><b>Item Added Successfully</b></h5><p>Product ID: " + receipt.events.Added.returnValues[0] + "</p>";
              qr.value = receipt.events.Added.returnValues[0];
              $bottom = "<p style='color: #FECB2E'> You may print the QR Code if required </p>"
              $("#alertText").html(msg);
              $("#qrious").show();
              $("#bottomText").html($bottom);
              $(".customalert").show("fast", "linear");
            });
          //console.log(receipt);
        });
        $("#prodname").val('');

      });

      // Code for detecting location
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
      }

      function showPosition(position) {
        var autoLocation = position.coords.latitude + ", " + position.coords.longitude;
        lat = position.coords.latitude;
        long = position.coords.longitude;
        $("#prodlocation").val(autoLocation);
      }

      $('#form2').on('submit', function(event) {
        event.preventDefault(); // to prevent page reload when form is submitted
        prodid = $('#prodid').val();
        prodlocation = $('#prodlocation').val();
        date = $('#recvdate').val();
        prodname = $('#prodname').val();
        // console.log(prodid);
        // console.log(prodlocation);
        latlongString = $('#prodlocation').val();
        locArray = latlongString.split(', ');
        currentLat = locArray[0];
        currentLong = locArray[1];

        // var today = new Date();
        // var thisdate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();

        var info = "<div class='vertical-line'></div><div class='circle'></div><b>Product Scanner Name: </b>" + prodname + "<br><b>Date: </b>" + date + "<br><b>Current Location: </b>" + prodlocation;

        console.log("productID : " + prodid);
        console.log("desciption : " + info);
        console.log("locationName : " + prodname);

        web3.eth.getAccounts().then(async function(accounts) {
          var receipt = await contract.methods.addState(prodid, info, prodname).send({
              from: accounts[0],
              gas: 1000000
            })
            .then(receipt => {
              var msg = "<h4> Successfully Item has been Scanned. </h4> ";
              $("#alertText").html(msg);
              $("#qrious").hide();
              $("#bottomText").hide();
              $(".customalert").show("fast", "linear");
            });
        });
        $("#prodid").val('');
      });


      function isInputNumber(evt) {
        var ch = String.fromCharCode(evt.which);
        if (!(/[0-9]/.test(ch))) {
          evt.preventDefault();
        }
      }


      function openQRCamera(node) {
        var reader = new FileReader();
        reader.onload = function() {
          node.value = "";
          qrcode.callback = function(res) {
            if (res instanceof Error) {
              alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
            } else {
              // Send the scanned QR code data to a PHP script for processing
              $.ajax({
                type: "POST",
                url: "process_qr_data.php", // PHP script URL
                data: {
                  qr_data: res
                }, // Send scanned QR code data
                success: function(response) {
                  // Handle the response from the PHP script
                  console.log("Product ID:", response);
                  // Set the product ID in the received product ID input field
                  $("#prodid").val(response);
                },
                error: function(xhr, status, error) {
                  // Handle AJAX errors
                  console.error("AJAX Error:", status, error);
                }
              });
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