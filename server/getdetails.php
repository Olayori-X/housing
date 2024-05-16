<?php
  //This searches for the houses that are available based on the whatever was searched. Will be adjusted in due time
  include "connect.php";

  $users = "SELECT * FROM users";
  $usersquery = mysqli_query($connect, $users);

  if($usersquery){
    $data = [];
    while($row = mysqli_fetch_assoc($usersquery)){
        unset($row['Password']);
        unset($row['id']);
        $data[] = $row;
    }
    header("Content-Type: application/json");
    echo json_encode($data);
  }
  
?>