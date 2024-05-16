<?php
if($_SERVER['REQUEST_METHOD'] == "POST"){
    include "connect.php";
    include "validate.php";

    $values = json_decode(file_get_contents("php//input"), true);

    $viewerid = validate($values['userid']);
    $houseid = validate($values['houseid']);

    $checkvideoaccess = "SELECT * FROM inspection_done WHERE viewerid = ? AND houseid = ?";
    $stmt = mysqli_prepare($connect, $checkvideoaccess);
    mysqli_stmt_bind_param($stmt, "ss", $viewerid, $houseid);
    mysqli_stmt_execute($stmt);
    $checkvideoaccessquery = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($checkvideoaccessquery) <= 0){
        $response = [
            "message" => false 
        ];
    }else{
        $getvideo = "SELECT video FROM housedetails WHERE houseid = ?";
        $preparegetstmt = mysqli_prepare($connect, $getvideo);
        mysqli_stmt_bind_param($preparegetstmt, "s", $houseid);
        mysqli_stmt_execute($preparegetstmt);
        $getvideoquery = mysqli_stmt_get_result($preparegetstmt);

        if($getvideoquery){
            $video = [];
            while($row = mysqli_fetch_assoc($getvideoquery)){
                $video[] = $row;
            }
            $response = [
                "message" => true,
                "data" => $video
            ];
            $reducevalue = "UPDATE inspection_done SET views = views - 1 WHERE houseid = ? AND userid = ?";
            $preparereducevalue = mysqli_prepare($connect, $reducevalue);

            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($preparereducevalue, "ss", $houseid, $userid);

            // Execute the prepared statement
            $reducevaluequery = mysqli_stmt_execute($preparereducevalue);
            if($reducevaluequery){
                $deletezero = "DELETE FROM inspection_done WHERE views = 0";
                $deletezeroquery = mysqli_query($connect, $deletezero);
            }
        }
    }

    header("Content-Type: application/json");
    echo json_encode($response);
}