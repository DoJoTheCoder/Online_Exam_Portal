<?php
    // Sign up functions
    function isSignupInputEmpty($user_name, $name, $email, $phone,  $pwd, $confirm_pwd){

        if(empty($user_name) || empty($name) || empty($email) || empty($phone) || empty($confirm_pwd) || empty($pwd) ){
            return true;
        }

        return false;
    }

    function isUserNameValid($user_name){
        if(preg_match("/^[a-zA-Z0-9]+$/" , $user_name)){
            return true;
        }
        return false;
    }

    // if user exists, then return user data, else false.
    function getUserData($conn, $user_name, $email){
        $sql = "SELECT * FROM `user_info` WHERE `userName` = ? or `email` = ?;";
        $stmt = prep_sql_execute($conn, $sql, array($user_name, $email), "ss");
        if($stmt === false){
            return false;
        }
        
        $resultData = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if($row = mysqli_fetch_assoc($resultData)){
            return $row;
        }

        return false;
    }

    function dbCreateUser($conn, $user_name, $name, $email, $phone, $pwd){
        if(strlen($pwd) >= 8){
            if (!preg_match("/^[A-Za-z0-9]+$/",$pwd)) {
                return 'password';
            }
        }

        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `user_info` ( `userName`, `fullName`, `phone`, `email`, `pwd`) values (?,?,?,?,?);";
        $stmt = prep_sql_execute($conn, $sql, array($user_name, $name, $phone, $email, $hashedPwd), "sssss");
        if($stmt === false){
            return false;
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    function isNameValid($name){
        if(preg_match("/^[a-zA-Z(\s)]*$/" , $name)){
            return true;
        }
        return false;
    }

    function isEmailValid($email){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;
    }

    function isEmailExists($conn, $email){
        $sql = "SELECT * FROM user_info WHERE email= ?;";
        $stmt = prep_sql_execute($conn, $sql, array($email), "s");

        if($stmt === false) return false;
        $resultData = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if(mysqli_fetch_assoc($resultData)){
            return true;
        }
        return false;
    }

    function isPhoneValid($phone){
        if(preg_match("/^[0-9]*$/" , $phone)){
            return true;
        }
        return false;
    }

    function isPasswordValid($pwd, $confirm_pwd){
        if($pwd!== $confirm_pwd){
            return false;
        }
        return true; 
    }


    // Log in functions
    function isLoginInputEmpty($uid, $pwd){
        if(empty($uid) || empty($pwd)){
            return true;
        }
        return false;
    }

    function loginUser($conn, $uid, $pwd){
        $uidData = getUserData($conn, $uid, $uid); 

        if($uidData === false){
            return false;
        }
        
        $hashedPwd = $uidData["pwd"];
        $pwdCheck = password_verify($pwd, $hashedPwd);
        if($pwdCheck === false){
            return false;
        }
        else{
            session_start();
            $_SESSION["userName"] = $uidData["userName"];
            $_SESSION["userUid"]= $uidData["uid"];
            header("location: ../index.php");
            exit();
        }
    }

    // return the subject id to which a quiz belongs to
    // returns false otherwise
    function getSubId($conn, $quizid){
        $sql = "SELECT subid FROM quizes WHERE quizid='".$quizid."';";
        $result = mysqli_query($conn, $sql);
        if($result === false || mysqli_num_rows($result)===0){
            return false;
        }

        $row = mysqli_fetch_assoc($result);
        return $row['subid'];
    }

    // returns an array with details for a test the user took for some quiz
    // returns false for error
    // returns 0 if no test was found
    function getTestInfo($conn, $quizid, $uid){
        $sql = "SELECT * from tests WHERE quizid='".$quizid."' and uid='".$uid."';"; 
        $result = mysqli_query($conn, $sql);
        if($result === false) {
            return false;
        }
        
        if(mysqli_num_rows($result) == 0){
            return 0;
        }

        return mysqli_fetch_assoc($result);
    }

    // function to check if quiz ID is valid
    function isQuizExist($conn, $quizid) {
        $sql = "SELECT * FROM quizes WHERE quizid =?;";
        $stmt = prep_sql_execute($conn, $sql, array($quizid), "s");
        if($stmt === false) {
            return false;
        };
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        if(mysqli_num_rows($result)===0){
            return 0;            
        }
        return true;
    }

    // returns the invilator ID and pwd for a given quizid
    // return false for any error
    function getInvigilatorInfo($conn, $quizid){
        $sql = "SELECT * FROM invigilator_info WHERE quizid=?;";
        $stmt= prep_sql_execute($conn, $sql, array($quizid), "s");
        if($stmt === false) return false;

        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
