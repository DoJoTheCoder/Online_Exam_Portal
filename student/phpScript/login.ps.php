<?php
require_once '../../includes/database.inc.php';
require_once '../includes/functions.inc.php';

// Autthenticates login credentials
if(isset($_POST['submit'])){
    $uid = trim($_POST['uid'],);
    $pwd = trim($_POST['pwd'],);

    if(isLoginInputEmpty($uid, $pwd) !== false){
        header("location: ../login.php?error=inputempty");
        exit();
    }

    if(loginUser($conn, $uid, $pwd) === false){
        header("location: ../login.php?error=invalidLogin");
        exit();
    }
}
else{
    header("location: ../login.php");
    exit();
}