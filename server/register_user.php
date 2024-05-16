<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if($_SERVER['REQUEST_METHOD'] === "POST"){
	include 'connect.php';
	include "validate.php";

	$txtfirstName = $_POST['firstName'];
	$txtlastName = $_POST['lasNme'];
	$txtPhone = $_POST['phnNo'];
	$txtEmail = validate($_POST['email']);
	$txtCountry = $_POST['country'];
	$txtState = $_POST['state'];
	$txtCity = $_POST['city'];
	$txtPassword = md5(validate($_POST['password']));
	$txtStatus = validate($_POST['status']);

	$getlastuserid = "SELECT userid FROM users ORDER BY id DESC LIMIT 1";
	$getquery = mysqli_query($connect, $getlastuserid);

	if($getquery){
		$row = mysqli_fetch_assoc($getquery);
		$data = $row['id'] + 1;
		$userid = "user" . $data;
	}

	$UserVerification = "SELECT * FROM users WHERE email = ?";
	$prepareverificationstmt = mysqli_prepare($connect, $UserVerification);

	mysqli_stmt_bind_param($prepareverificationstmt, "s", $txtEmail);
	$executeverificationstmt = mysqli_stmt_execute($prepareverificationstmt);
	if($executeverificationstmt){
		$UserQuery = mysqli_stmt_get_result($prepareverificationstmt);

		if($UserQuery -> num_rows > 0){
			$response = [
				'response' => 'error',
				'message' => 'This email exists'
			];
		}else{
			require 'phpmailer/src/Exception.php';
			require 'phpmailer/src/PHPMailer.php';
			require 'phpmailer/src/SMTP.php';
	
			$min = 100000000000000;  // 10-digit number with all digits being 0
			$max = 999999999999999;  // 10-digit number with all digits being 9
			$otp = random_int($min, $max);
			$code = md5($otp);
	
			$mail = new PHPMailer(true);
	
			$mail->isSMTP();
			$mail->Host = 'vote.netcarvers.com.ng';
			$mail->SMTPAuth = true;
			$mail->Username= "email to send mail";
			$mail->Password = 'password';
			$mail->SMTPSecure = 'ssl';
			$mail->Port = 465;
	
			$mail->setFrom('fameduel@vote.netcarvers.com.ng');
	
			$mail->addAddress($email);
	
			$mail->isHTML(true);
	
			$mail->Subject = "Verify your account";
			$mail->Body = "The code to verify your fameduel account is $otp. <br>Do not share with anyone.";
	
			if($mail->send()){
				$sql = "INSERT INTO users (userid, Email, password, code, status, verified) VALUES (?, ?, ?, ?, ?, ?)";
				$verified = false;
	
				// Prepare the statement
				$stmt = mysqli_prepare($connect, $sql);
	
				// Bind parameters to the prepared statement
				mysqli_stmt_bind_param($stmt, "sssssi", $userid, $txtEmail, $txtPassword, $code, $txtStatus, $verified);
		
				// Execute the prepared statement
				$rs = mysqli_stmt_execute($stmt);
	
	
				if($rs){
					$response = [
						'response' => 'successful',
						'userid' => $userid
					];
				}
			}else{
				$response = [
					'response' => 'error',
					'message' => 'Sending the mail was not successful. Try again later'
				];
			}
		}
	}


	header('Content-Type: application/json');
    echo json_encode($response); 
}else{
	header("Location: ../Signup.php");
}
