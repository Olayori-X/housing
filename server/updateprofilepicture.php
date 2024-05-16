<?php
    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $userid = $_POST['userid'];
        
        $target_dir = "C:/Xampp/htdocs/housing/uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check == false) {
            $message = ["message" => "File is not an image."];
        } else {
            $uploadOk = 1;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = "server/uploads/" . basename($_FILES["image"]["name"]);
                $sql = "UPDATE users SET profilepic = '$image' WHERE userid = '$userid'";

                // insert in database 
                $rs = mysqli_query($connect, $sql);
                if($rs){
                    $message = ["message" => "Successful"];
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($message); 
    }