<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../connect.php';


if($_SERVER['REQUEST_METHOD'] === 'POST'){
	include '../validate.php';

    $data = file_get_contents("php://input");
    $values = json_decode($data, true);

    $username = validate($values['email']);
	$password = md5(validate($values['password']));

	if (empty($username)){
        $response = [
            'response' => 'error',
            'message' => 'Username is required'
        ];
	}else if(empty($password)) {
        $response = [
            'response' => 'error',
            'message' => 'Password is required'
        ];
	}else{
		// Prepare the SQL statement with a placeholder
        $sql = "SELECT * FROM users WHERE username = ?";

        // Prepare the statement
        $stmt = mysqli_prepare($connect, $sql);

        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "s", $username);

        // Execute the prepared statement
        $rs = mysqli_stmt_execute($stmt);

        // Store the result set
        $SQLpass = mysqli_stmt_get_result($stmt);


		if (mysqli_num_rows($SQLpass) === 1) {
            $row = mysqli_fetch_assoc($SQLpass);
            $userid = $row['userid'];

            if($row['verified'] === true){
                if($row['Password'] === $password){
                    unset($row['password']);
                    unset($row['id']);
                    session_start();
                    $_SESSION['userid'] = $row['userid'];

                    $data = $row['userid'];
                    $response = [
                        'response' => "successful",
                        'userid' => $data,
                        'accessToken' => session_id()
                    ];

                }else{	
                    $response = [
                        'response' => "error",
                        'message' => 'Incorrect Password'
                    ];
                }
            }else{
                require 'phpmailer/src/Exception.php';
                require 'phpmailer/src/PHPMailer.php';
                require 'phpmailer/src/SMTP.php';

                $email = $row['email'];

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
                    $sql = "UPDATE users SET code = '$otp' WHERE userid = ?";
                    $preparesqlstmt = mysqli_prepare($connect, $sql);

                    // Bind parameters to the prepared statement
                    mysqli_stmt_bind_param($preparesqlstmt, "s", $userid);

                    // Execute the prepared statement
                    $sqlquery = mysqli_stmt_execute($preparesqlstmt);


                    if($sqlquery){
                        $response = [
                            'response' => 'A code has been sent to your mail. Check your mail for the code to verify your account',
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

		}else{	
            $response = [
                'response' => "error",
                'message' => 'Incorrect Username'
            ];
		}
	}

    header("Content-Type: application/json");
    echo json_encode($response);
}else{
	http_response_code(405);
}
