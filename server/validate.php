<?php
//Just a function being used
function validate($data){
    $data= trim($data);
    $data = stripslashes($data);
    $data= htmlspecialchars($data);

    return $data;
}
$code = "123456789";
?>