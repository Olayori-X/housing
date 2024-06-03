<?php
//Just a function being used
function validate($data){
    $data= trim($data);
    $data = stripslashes($data);
    $data= htmlspecialchars($data);

    return $data;
}

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: *"); // Allow GET, POST, and OPTIONS requests
header("Access-Control-Allow-Headers: *"); // Allow Content-Type and Authorization headers

function verifyToken(){
    $headers = getallheaders();
    $sessionId = isset($headers['accessToken']) ? $headers['accessToken'] : '';

    validate($sessionId);
    session_id($sessionId);
    session_start();

    if(isset($_SESSION['userid'])){
        return true;
    }else{
        return false;
    }
}

$code = "123456789";
?>