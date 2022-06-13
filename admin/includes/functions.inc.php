<?php
    // if user exists, then return user data, else false.
    function getAdminData($conn, $user_name){
        $sql = "SELECT * FROM admins WHERE `name` = ?;";

        $stmt = prep_sql_execute($conn, $sql, array($user_name), "s");

        if($stmt === false) return false;
        $resultData = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if($row = mysqli_fetch_assoc($resultData)){
            return $row;
        }

        return false;
    }

    // checks if the input is empty
    function isAdminLoginInputEmpty($uid, $pwd){
        if(empty($uid) || empty($pwd)){
            return true;
        }
        return false;
    }

    //returns true for valid login
    // else false
    function loginAdmin($conn, $uid, $pwd){
        $uidData = getAdminData($conn, $uid); 

        $hashedPwd = $uidData["pwd"];
        $pwdCheck = password_verify($pwd, $hashedPwd);
        if($uidData === false || $pwdCheck === false){
            return false;
        }
        
        else if($pwdCheck === true){
            session_start();
            $_SESSION["admin_name"] = $uidData["name"];
            $_SESSION["admin_id"] = $uidData["adminid"];
            return true;
        }
    }

    // returns false for error, 
    // 0 for no records found, 
    // else the subject ID if it exists.
    function isSubjectExists($conn, $subName){
        $sql = "SELECT * FROM subjects WHERE `subName` = ?;";
        $stmt = prep_sql_execute($conn, $sql, array($subName), "s");

        if($stmt === false) return false;
        $resultData = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    
        if(mysqli_num_rows($resultData) >0){
            $row = mysqli_fetch_assoc($resultData);
            return $row['subid'];
        }
        
        return 0;
    }

    // returns false for error, 
    // 0 for no records found, 
    // else the Quiz ID if it exists.
    function isQuizExists($conn, $quizName){
        $sql = "SELECT * FROM quizes WHERE `quizName`=?;";
        $stmt = prep_sql_execute($conn, $sql, array($quizName), "s");

        if($stmt === false) return false;
        $resultData = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    
        if(mysqli_num_rows($resultData) >0){
            $row = mysqli_fetch_assoc($resultData);
            return $row['quizid'];
        }
        return 0;
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

   