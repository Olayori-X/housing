<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
	include '../connect.php';
	include '../validate.php';
    include '../time.php';
    

    $userid = validate($_POST['userid']);
    $housename = validate($_POST['housename']);
    $street = validate($_POST['street']);
    $dateadded = $currentMonth . " " . $currentYear;

    if(verifyToken()){
        $checkstatus = "SELECT status FROM users WHERE userid = ?";
        $preparecheckstatus = mysqli_prepare($connect, $checkstatus);

        mysqli_stmt_bind_param($preparecheckstatus, 's', $userid);
        $checkstatusquery = mysqli_stmt_execute($preparecheckstatus);

        $getresult = mysqli_stmt_get_result($preparecheckstatus);

        $row = mysqli_fetch_assoc($getresult);
        if($row['status'] === "Landlord"){


            if(isset($_POST['image'])){
                $target_dir = "C:/Xampp/htdocs/housing/server/uploads/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if($check == false) {
                    $response = [
                        'response' => 'error',
                        'message' => 'File is not an image.'
                    ];
                } else {
                    $uploadOk = 1;
                    // Check file size
                    if ($_FILES["image"]["size"] > 500000) {
                        $response = [
                            'response' => 'error',
                            'message' => 'Sorry, File limit is 50MB'
                        ];
                    }else{
                        // Allow certain file formats
                        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                        && $imageFileType != "gif" ) {
                            $response = [
                                'response' => 'error',
                                'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.'
                            ];
                        }else {
                            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                                $image =  basename($_FILES["image"]["name"]);
                                
                                $insertarticle = "INSERT INTO housedetails(userid, housename, street, image, date, month, year) VALUES ('$userid', '$housename', '$street', '$image', '$dateadded', '$currentMonth', '$currentYear')";
                                
                                $insertquery = mysqli_query($connect, $insertarticle);
                    
                                if($insertquery){
                                    // $time = $currentHour. ":". $currentMinute; 
                                    // $insertactivity = "INSERT INTO activitytable(username, activity, time, date, month, year) VALUES('$user', 'Posted $title', '$time', '$currentFullDate', '$currentMonth', '$currentYear')";
                                    // $insertactivityquery = mysqli_query($connect, $insertactivity);
                                    // if($insertactivityquery){
                                        // header("Location: newpost.php");
                                        $response = [
                                            'response' => 'successful'
                                        ];
                                    // }
                                }else{
                                    echo "Error";
                                }
                            }else{
                                $response = [
                                    'response' => 'error',
                                    'message' => 'A problem came up.'
                                ];
                            }
                        }
                    }
                }
            }else {
                $insertarticle = "INSERT INTO housedetails(userid, housename, street, date, month, year) VALUES ('$userid', '$housename', '$street', '$dateadded', '$currentMonth', '$currentYear')";
                                
                $insertquery = mysqli_query($connect, $insertarticle);

                if($insertquery){
                    // $time = $currentHour. ":". $currentMinute; 
                    // $insertactivity = "INSERT INTO activitytable(username, activity, time, date, month, year) VALUES('$user', 'Posted $title', '$time', '$currentFullDate', '$currentMonth', '$currentYear')";
                    // $insertactivityquery = mysqli_query($connect, $insertactivity);
                    // if($insertactivityquery){
                        // header("Location: newpost.php");
                        $response = [
                            'response' => 'successful'
                        ];
                    // }
                }else{
                    echo "Error";
                }

            }
        }else{
            $response = [
                "response" => "Error",
                "message" => "No access"
            ];
        }
    }else{
        http_response_code(401);
    }

    header('Content-Type: application/json');
    echo json_encode($response); 
}else{
    http_response_code(405);
}