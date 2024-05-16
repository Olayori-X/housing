<?php
//This code verifies a new user signing up, after a code was sent to his mail. This authenticates the mail
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	include 'connect.php';
	include 'validate.php';

	$data = file_get_contents("php://input");
    $values = json_decode($data, true);

	$userid = validate($values['userid']);
	$code = md5(validate($values['code']));

	$getcode = "SELECT code FROM users WHERE userid = ?";
	$preparegetcodestmt = mysqli_prepare($connect, $getcode);
	mysqli_stmt_bind_param($preparegetcodestmt, 's', $userid);
	$executegetcodestmt = mysqli_stmt_execute($preparegetcodestmt);
	$getcodequery = mysqli_stmt_get_result($preparegetcodestmt);

	if($getcodequery){
		$row = mysqli_fetch_assoc($getcodequery);
		if($code === $row['code']){
			$verifyuser = "UPDATE users SET verified = true WHERE userid = ?";

			$prepareverifyuserstmt = mysqli_prepare($connect, $verifyuser);
			mysqli_stmt_bind_param($prepareverifyuserstmt, 's', $userid);

			$verifyuserquery = mysqli_stmt_execute($preparegetcodestmt);

			if($verifyuserquery){
				$response = [
					'response' => 'successful',
					'userid' => $userid
				];
			}
		}else{
			$response = [
				'response' => 'error',
				'message' => 'Code is not a match'
			];
		}
	}

	header('Content-Type: application/json');
    echo json_encode($response); 
}