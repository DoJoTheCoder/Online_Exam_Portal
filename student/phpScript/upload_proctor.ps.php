<?php

require_once "../../includes/database.inc.php";

if(isset($_POST["testid"]) && isset($_POST["minute"]) && isset($_POST["dataUrl"])){
    extract($_POST);
    // Fixing changes in dataUrl and removing start part
    $dataUrl = str_replace("data:image/jpeg;base64,", "", $dataUrl);
    $dataUrl = str_replace(" ", "+", $dataUrl);
    echo "length of dataUrl :".strlen($dataUrl);

    // if geolocation failed to send position
    $sql = "INSERT INTO proctor_info (testid, minute, imgFile) 
            VALUES (".$testid.", ".$minute.", '".$dataUrl."');";
    
    // if geolocation position was sent
    if(isset($_POST["latitude"]) && isset($_POST["longitude"])){
        $sql = "INSERT INTO proctor_info (testid, minute, imgFile, latitude, longitude) 
            VALUES (".$testid.", ".$minute.", '".$dataUrl."', ".$latitude.", ".$latitude.");";
    }

    if(mysqli_query($conn, $sql) === false){
        echo "QUERY_ERROR: Failed to add invigilate data to server! file: upload_proctor.ps.php";
    }   
}

