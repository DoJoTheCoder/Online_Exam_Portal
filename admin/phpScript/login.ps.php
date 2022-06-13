<?php
require_once '../../includes/database.inc.php';
require_once '../includes/functions.inc.php';

// Autthenticates login credentials
if(isset($_POST['submit'])){
    extract($_POST);

    $uid = trim($uid); 
    if(isAdminLoginInputEmpty($uid, $pwd) !== false){
        header("location: ../login.php?error=inputempty");
        exit();
    }

    if(loginAdmin($conn, $uid, $pwd) === false){
        header("location: ../login.php?error=invalidLogin");
    }else {
        header("location: ../index.php");
    }
    exit();
}
else{
    header("location: ../login.php");
    exit();
}