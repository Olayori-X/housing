<?php
//Will change the logic here

    if(isset($_POST['submit'])){
        include "connect.php";
        include "validate.php";
        
        $pass = md5(validate($_POST['nPass']));
        $passtwo = md5(validate($_POST['cPass']));
        $username = validate($_POST['username']);
        $email = validate($_POST['email']);
        $codedotp = md5(validate($_POST['otp']));
	    $otp = validate($_POST['otp']);


        if(empty($pass)){
            header("Location: ../changepassword.php?message=You have not input your new password&otp=$otp&key=$email");
        }    
        elseif($pass != $passtwo){
            header("Location: ../changepassword.php?message=The passwords do not match&otp=$otp&key=$email");
        }
        else{
            $update = "UPDATE users SET Password = '$pass' WHERE Username = '$username'";
            $queryupdate = mysqli_query($connect, $update);

            if($queryupdate){
                $updatelinkstatus = "UPDATE changepasswordlinkstatus SET used = true WHERE number = '$codedotp'";
			    $updatequery = mysqli_query($connect, $updatelinkstatus);
			    if($updatequery){
				    header("Location: ../Login.php");
				    exit();
			    }
            }
        }
    }else{
        header("Location: ../Login.php");
    }
?>