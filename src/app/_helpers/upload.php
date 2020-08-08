<?php

// phpinfo();
$target_dir     = "/var/www/vhosts/3dintegrationgroup.com/httpdocs/secure-file-upload/uploads/";
$fileName       = basename($_FILES["fileToUpload"]["name"]);
$target_file    = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk       = 1;
$fileType       = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$postdata       = file_get_contents("php://input");
$projectType    = $_POST['projectType'];
$customerName   = $_POST['customerName'];
$company        = $_POST['company'];
$zipcode        = $_POST['zipcode'];
$email          = $_POST['email'];
$phone          = $_POST['phone'];
$notes          = $_POST['notes'];
$servername     = "localhost:3306";
//Prod Values
// $username       = "mstr_3digsfu";
// $password       = "_0b8fG6d";
// $dbName         = "3dig_sfu";
//dev Values
$username       = "root";
$password       = "";
$dbName         = "pixrite";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// print "Connected successfully";

mysqli_select_db($conn, $dbName) or die( mysqli_error($conn) );

// print 'Here is some more debugging info:';
// print_r($_FILES);
// print_r($_POST);


// print "target_dir " . $target_dir;
// print "target_file " . $target_file;
// print "uploadOk " . $uploadOk;
// print "fileType " . $fileType;
// print "projectType " . $projectType;
// print "customerName " . $customerName;
// print "company " . $company;
// print "zipcode " . $zipcode;
// print "email " . $email;
// print "phone " . $phone;
// print "notes " . $notes;

// Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
//   $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
//   if($check !== false) {
//     print "File is an image - " . $check["mime"] . ".";
//     $uploadOk = 1;
//   } else {
//     print "File is not an image.";
//     $uploadOk = 0;
//   }
// }

// Check if file already exists
if (file_exists($target_file)) {
  // print "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
// if ($_FILES["fileToUpload"]["size"] > 500000) {
//   print "Sorry, your file is too large.";
//   $uploadOk = 0;
// }

// Allow certain file formats
if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
&& $fileType != "gif" && $fileType != "pdf") {
  // print "Sorry, only JPG, JPEG, PNG, GIF and PDF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  // print "Sorry, your file was not uploaded.";
  
  
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    // print "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
  } else {
    // print "Sorry, there was an error uploading your file.";
  }
}

$sql = "INSERT INTO upload_transaction (ID, TXN_STATUS, DATE_CREATED) VALUES (NULL, 'PENDING', CURRENT_TIMESTAMP())";

if ($conn->query($sql) === TRUE) {
  // print "New upload_transaction record created successfully";
} else {
  // print "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "SELECT ID, TXN_STATUS FROM `upload_transaction` WHERE ID = (select MAX(id) from upload_transaction)";
$result = $conn->query($sql);
$txnId = "";
$txnStatus = "";

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    // print "txnId: " . $row["ID"]. "txnStatus: " . $row["TXN_STATUS"]. "<br>";
    $txnId = $row["ID"];
    $txnStatus = $row["TXN_STATUS"];
  }
} else {
  // print "0 results from upload_transaction";
}
if ($txnId == ""){
  die("Transaction ID is null");
}

$sql = "INSERT INTO customers (ID, CUSTOMER_NAME, COMPANY, ZIPCODE, EMAIL, PHONE, DATE_CREATED) 
VALUES (NULL, '$customerName', '$company', '$zipcode', '$email', '$phone', CURRENT_TIMESTAMP())";

if ($conn->query($sql) === TRUE) {
  // print "New customers record created successfully";
} else {
  // print "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "SELECT ID FROM `customers` WHERE ID = (select MAX(id) from customers)";
$result = $conn->query($sql);
$custId = "";

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    // print "custId: " . $row["ID"]. "<br>";
    $custId = $row["ID"];
  }
} else {
  // print "0 results from customers";
}

if ($custId == ""){
  die("Customer ID is null");
}

$sql = "INSERT INTO file_upload (ID, PROJECT_TYPE, NOTES, TXN_ID, CUST_ID, DATE_CREATED) 
VALUES ( NULL, '$projectType', '$notes', $txnId, $custId, CURRENT_TIMESTAMP())";

if ($conn->query($sql) === TRUE) {
  // print "New file_upload record created successfully";
} else {
  // print "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "SELECT ID FROM `file_upload` WHERE ID = (select MAX(id) from file_upload)";
$result = $conn->query($sql);
$fileUploadId = "";

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    // print "fileUploadId: " . $row["ID"]. "<br>";
    $fileUploadId = $row["ID"];
  }
} else {
  // print "0 results from file_upload";
}

if ($fileUploadId == ""){
  die("File Upload ID is null");
}

$sql = "INSERT INTO uploaded_files (ID, FILE_UPLOAD_ID, FILE_URL, DATE_CREATED) 
VALUES (NULL, $fileUploadId, '$target_file', CURRENT_TIMESTAMP())";

if ($conn->query($sql) === TRUE) {
  // print "New uploaded_files record created successfully";
} else {
  // print "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "SELECT ID FROM `uploaded_files` WHERE ID = (select MAX(id) from uploaded_files)";
$result = $conn->query($sql);
$uploadedFilesId = "";

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    // print "uploadedFilesId: " . $row["ID"]. "<br>";
    $uploadedFilesId = $row["ID"];
  }
} else {
  // print "0 results from file_upload";
}

if ($uploadedFilesId == ""){
  die("File Upload ID is null");
}

$conn->close();

//Send an customer email message
$msg = "Thank you for submitting a potential 3D Printing Project! One of our Design and Build Specialists will review your project files and get back to your within 24 hours!";
$msg = $msg . "\nProject Status: " . $txnStatus . "\nCustomer Name: " . $customerName . " \nCompany: " . $company . " \nZip Code: " . $zipcode . " \nEmail: " . $email . 
" \nPhone: " . $phone . " \nNotes: " . $notes;

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
mail($email, "Here is your 3D Integration Group Project ID ".$txnId,$msg);

//Send an Admin email message
$msg = "Hi Admin, \n";
$msg = $msg . $customerName . " has uploaded project files for review. You can download the files here:\n";
$msg = $msg . "https://www.3dintegrationgroup.com/secure-file-upload/uploads/". $fileName . " \n";
$msg = $msg . "Be sure to remove the file from the server once you have a local copy for the plant. \n";
$msg = $msg . "Cheers, \n";
$msg = $msg . "PIXRITE Secure File Upload Team";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

$adminEmail = "alger.brigham@gmail.com";
$adminSubject = "New 3D Integration Project Submitted!";
$headers = array(
  'From' => 'jaredrowe@pixrite.com',
  'Reply-To' => 'jaredrowe@pixrite.com',
  'X-Mailer' => 'PHP/' . phpversion()
);

// send email
// mail($adminEmail, $adminSubject, $msg, $headers);


$data = [ 'txnId' => $txnId, 'txnStatus' => $txnStatus ];
header('Content-Type: application/json;charset=utf-8');
echo json_encode($data);

?>