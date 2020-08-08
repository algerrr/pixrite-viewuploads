<?php


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Credentials: true');
// phpinfo();
// $target_dir     = "/var/www/vhosts/3dintegrationgroup.com/httpdocs/secure-file-upload/uploads/";
// $fileName       = basename($_FILES["fileToUpload"]["name"]);
// $target_file    = $target_dir . basename($_FILES["fileToUpload"]["name"]);
// $uploadOk       = 1;
// $fileTdype      = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$postdata       = file_get_contents("php://input");
// $projectType    = $_POST['projectType'];
// $customerName   = $_POST['customerName'];
// $company        = $_POST['company'];
// $zipcode        = $_POST['zipcode'];
// $email          = $_POST['email'];
// $phone          = $_POST['phone'];
// $notes          = $_POST['notes'];
$action          = $_POST['action'];
$servername      = "localhost:3306";
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
$data = array();

switch ($action) {
    case "findusers":
        echo "i is apple";
        break;
    case "getuploads":
        $sql = "SELECT upload_transaction.ID AS TRANSACTION_ID, upload_transaction.TXN_STATUS, file_upload.PROJECT_TYPE, customers.CUSTOMER_NAME, customers.EMAIL, SUBSTRING(uploaded_files.FILE_URL,48) AS FILE_URL, upload_transaction.DATE_CREATED FROM file_upload INNER JOIN upload_transaction ON file_upload.TXN_ID = upload_transaction.ID INNER JOIN customers ON file_upload.CUST_ID = customers.ID INNER JOIN uploaded_files ON uploaded_files.FILE_UPLOAD_ID = file_upload.ID";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
          // output data of each row
          // $data = ['OKAY'];

          while($row = $result->fetch_assoc()) {
            // print_r($row);
            array_push($data, 
            array('transaction_id' => $row["TRANSACTION_ID"], 
            'transaction_status' => $row["TXN_STATUS"], 
            'project_type' => $row["PROJECT_TYPE"], 
            'customer_name' => $row["CUSTOMER_NAME"], 
            'email' => $row["EMAIL"], 
            'file_url' => $row["FILE_URL"], 
            'date_created' => $row["DATE_CREATED"]));
          }
        } else {
          $data = ['NO_ROWS'];
        }
        break;
}

// $data = [ 'txnId' => $txnId, 'txnStatus' => $txnStatus ];
header('Content-Type: application/json;charset=utf-8');
echo json_encode($data);

?>
