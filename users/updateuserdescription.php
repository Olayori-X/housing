<?php
//Will still work on this when I get complete information
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $userid = validate($_POST['userid']);
    // $txtfirstName = $_POST['firNme'];
	// $txtlastName = $_POST['lasNme'];
	// $txtPhone = $_POST['phnNo'];
	$txtEmail = validate($_POST['email']);
	// $txtCountry = $_POST['country'];
	// $txtState = $_POST['state'];
	// $txtCity = $_POST['city'];
	$txtUsername = validate($_POST['username']);
	// $codedotp = md5(validate($_POST['otp']));
	// $otp = validate($_POST['otp']);

    $sql = "UPDATE users SET Email = '$txtEmail', Username = '$txtUsername' WHERE userid = '$userid'";

    // insert in database 
    $rs = mysqli_query($connect, $sql);

    if($rs){
        $users = "SELECT * FROM users WHERE userid = '$userid'";
        $usersquery = mysqli_query($connect, $users);

        if($usersquery){
            $data = [];
            while($row = mysqli_fetch_assoc($usersquery)){
                unset($row['Password']);
                unset($row['id']);
                $data[] = $row;
            }
            
            $message  = [
                'userprofile' => $data,
            ];
            header("Content-Type: application/json");
            echo json_encode($message);
        }
    }
}