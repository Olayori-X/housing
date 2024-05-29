<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include 'config.php';
include 'connect.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  include 'validate.php';

  $data = file_get_contents("php://input");
  $reference = json_decode($data, true);





  if(verifyToken()){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$reference}",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $secretKey"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      // echo "cURL Error #:" . $err;
      header("Content-Type: application/json");
      echo json_encode($err);
    } else {
      $responseData = json_decode($response, true);
      if($responseData['status'] === true){

        // Accessing metadata values
        $metadata = $responseData['data']['metadata'];

        // Accessing specific fields within metadata
        $email = $metadata['custom_fields'][1]['value']; // Email
        $fullName = $metadata['custom_fields'][0]['value']; // First Name
        $plan = $metadata['custom_fields'][3]['value']; // Last Name
        $contact = $metadata['custom_fields'][2]['value']; // Phone Number
      

        $UserVerification = "SELECT * FROM members WHERE email = ?";
        $stmt = mysqli_prepare($connect, $UserVerification);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $UserQuery = mysqli_stmt_get_result($stmt);

        if($UserQuery -> num_rows > 0){
          while($row = $UserQuery->fetch_assoc()) {
            if($row['email'] === $txtEmail){
              $message = [
                'response' => 'error',
                'message' => 'This email exists'
              ];
            }
          }
        }else{
          
          // Prepare the SQL statement with placeholders
          $sql = "INSERT INTO inspection_done (viewerid, houseid, views) VALUES (?, ?, ?)";

          // Prepare the statement
          $stmt = mysqli_prepare($connect, $sql);

          // Bind parameters to the prepared statement
          mysqli_stmt_bind_param($stmt, "sss", $viewerid, $houseid, $number_of_views);

          // Execute the prepared statement
          $rs = mysqli_stmt_execute($stmt);

          if($rs){
            $message = [
              'response' => 'successful',
              'userid' => $viewerid,
              'houseid' => $houseid
            ];
          }
          mysqli_stmt_close($stmt);
        }
      }else{
        header("Content-Type: application/json");
        echo json_encode($response);
      }
      header("Content-Type: application/json");
      echo json_encode($message);
    }
  }else{
    http_response_code(401);
  }
}else{
  http_response_code(405);
}