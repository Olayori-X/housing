<?php
//This gets the profile of a particular user. Landlord or Tenant
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        
        include "connect.php";
        include "validate.php";

        // $values = json_decode(file_get_contents("php://input"), true);

        $user = validate($_GET['userid']);

        $users = "SELECT * FROM users WHERE userid = ?";
        $prepareuserstmt = mysqli_prepare($connect, $users);
        
        mysqli_stmt_bind_param($prepareuserstmt, 's', $userid);
        
        $rs = mysqli_stmt_execute($prepareuserstmt);

        $usersquery = mysqli_stmt_get_result($prepareuserstmt);

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
    }else{
        http_response_code(405);
    }
