<?php
require_once '../../includes/database.inc.php';
require_once '../includes/functions.inc.php';

if(isset($_POST["submit"])){
    extract($_POST);
    $user_name = trim($user_name);
    $name = trim($name);
    $email = trim($email);
    $phone = trim($phone);

    // checking validity of sign up details
    if(isSignupInputEmpty($user_name, $name, $email, $phone,  $pwd, $confirm_pwd) !== false){
        header("location: ../sign_up.php?error=inputempty");
        exit();
    }
    if(isUserNameValid($user_name) == false){
        header("location: ../sign_up.php?error=invalidusername");
        exit();
    }
    if(getUserData($conn, $user_name, $email) !== false) {
        header("location: ../sign_up.php?error=usernameExists");
        exit();
    }
    if(isNameValid($name) == false){
        header("location: ../sign_up.php?error=invalidName");
        exit();
    }
    if(isEmailValid($email) == false){
        header("location: ../sign_up.php?error=invalidEmail");
        exit();
    }
    if(isEmailExists($conn, $email) !== false){
        header("location: ../sign_up.php?error=emailExists");
        exit();
    }
    if(isPhoneValid($phone) == false){
        header("location: ../sign_up.php?error=invalidPhone");
        exit();
    }
    if(isPasswordValid($pwd, $confirm_pwd) == false){
        header("location: ../sign_up.php?error=invalidPassword");
        exit();
    }
    
    $dbresult = dbCreateUser($conn, $user_name, $name, $email, $phone, $pwd);
    if($dbresult === 'password'){
        header("Location: ../sign_up.php?error=invalidPassword");
        exit();
    }
    else if($dbresult === false){
        header("location: ../signup.php?error=failed");
        exit();
    }

    header("location: ../sign_up.php?success=signupSuccess");
    exit();
}
else{
    header("location: ../sign_up.php");
}