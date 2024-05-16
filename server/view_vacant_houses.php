<?php

 if($_SERVER['REQUEST_METHOD'] === 'POST'){
    include 'connect.php';
	include "validate.php";

    $data = file_get_contents("php://input");
    $values = json_decode($data, true);

    $user = validate($values['userid']);


    //  ORDER BY id DESC
    $gethouses = "SELECT * FROM housedetails"; //WHERE accepted = true ORDER BY id DESC";
    $gethousesquery = mysqli_query($connect, $gethouses);

    if($gethousesquery){
        $houseslist = [];
        while($row = mysqli_fetch_assoc($gethousesquery)){
            unset($row['id']);
            $houseslist[] = $row;
        }
    }


    for($i=0; $i < count($contestslist); $i++){
        $landlordid = $contestslist[$i]['userid'];

        //get usernames of landlords of each house
        $getlandlordname = "SELECT * FROM users WHERE userid = '$landlordid'";

        $getlandlordnamequery = mysqli_query($connect, $getlandlordname);

        if($getlandlordnamequery){
            $row = mysqli_fetch_assoc($getlandlordnamequery);
            unset($row['Password']);
            unset($row['id']);
            $landlordname = [];
            $landlordname[] = $row;
        }


        //get landlord names ends here

        //  $contestantsusernames[] = $usernames;

    }
    
    $dataneeded = [
        'house-lists' => $houseslist,
        'landlord-names' => $landlordname
    ];

 }