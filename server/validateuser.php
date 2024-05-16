<?php
//This code validates if an email or a username input ny the user is correct, if they want to change their password, when they forget their password
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
    include 'connect.php';
    include "validate.php";
    
    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $values = json_decode(file_get_contents("php://input"), true);

        $userinput = validate($values['userinput']);

        if(empty($userinput)){
            $response = [
                "response" => "Error",
                "message" => "User need to input their username or email"
            ];
        }
        else{
            $check = "SELECT * FROM users WHERE email = ?";

            $preparecheckstmt = mysqli_prepare($connect, $check);
            mysqli_stmt_bind_param($preparecheckstmt, "s", $userinput);

            $executecheckstmt = mysqli_stmt_execute($preparecheckstmt);
            if($executecheckstmt){
                $confirm = mysqli_stmt_get_result($preparecheckstmt);

                if (mysqli_num_rows($confirm) >= 1) {
                    
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
                        $response = [
                            "response" => "Successful",
                            "message" => "A code has been sent to your mail"
                        ];
                    }
       
                }else{
                    $response = [
                        "response" => "Error",
                        "message" => "Account does not exist"
                    ];
                }
            }            
        }

        header('Content-Type: application/json');
        echo json_encode($response); 
            
    }else{
        header("Location: ../Login.php");
    }