<!-- Adds a subject, quiz or question -->
<?php
if(isset($_POST['submit'])){
    require_once '../../includes/database.inc.php';
    require_once '../includes/functions.inc.php';
    
    //add subject
    if($_POST['submit']=='add_subject'){ 
        $sub_name = trim($_POST['sub_name']);           
        if(empty($sub_name)){
            header("location: ../subject.php?error=inputempty");
            exit();
        }

        // check if subject exists
        $id = isSubjectExists($conn, $sub_name);
        if($id === false){
            header("location: ../subject.php?error=failed");
            exit();
        }
        else if($id >0){
            header("location: ../subject.php?error=subjectexists");
            exit();
        }
    
        $sql = "INSERT INTO subjects (subName) VALUES (?);";

        $stmt = prep_sql_execute($conn, $sql, array($sub_name), "s");
        if($stmt === false){
            header("location: ../subject.php?error=failed");
            exit();
        }
        mysqli_stmt_close($stmt);
        header("location: ../subject.php?result=success");
        exit();
    }
    // add quiz 
    else if($_POST['submit']=='add_quiz'){
        $sub_name = trim($_POST['sub_name']);           
        $quiz_name = trim($_POST['quiz_name']);           

        if(empty($sub_name)|| empty($quiz_name)){
            header("location: ../quiz.php?error=inputempty");
            exit();
        }

        // check if quiz exists and return id if exists
        $qid = isQuizExists($conn, $quiz_name);
        if($qid === false){
            header("location: ../quiz.php?error=failed");
            exit();
        }
        else if($qid >0){
            header("location: ../quiz.php?error=quizexists");
            exit();
        }
    
        $sql = "INSERT INTO quizes (`quizName`, subid) VALUES (?,?);";
        $sid = isSubjectExists($conn, $sub_name);
        $stmt = prep_sql_execute($conn, $sql, array($quiz_name, $sid), "si");
        
        if($stmt === false || $sid ===false){
            header("location: ../quiz.php?error=failed");
            exit();
        }
        mysqli_stmt_close($stmt);
        header("location: ../quiz.php?result=success");
        exit();
    }

    //add question
    else if($_POST['submit']=="add_question"){
        $quizid = trim($_POST['quizid']); 
        $ques_name = trim($_POST['ques_name']);
        $opt1 = trim($_POST['opt1']);
        $opt2 = trim($_POST['opt2']);
        $opt3 = trim($_POST['opt3']);
        $opt4 = trim($_POST['opt4']);
        $opt_ans = trim($_POST['opt_ans']);

        // check if quizid is valid
        if(empty($quizid) || !is_numeric($quizid)){
            header("location: ../question.php?error=quiznotexist");
            exit();
        }
        $sql = "SELECT * FROM quizes WHERE quizid='".$quizid."';";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result)==0){
            header("location: ../question.php?error=quiznotexist");
            exit();
        }

        if(empty($ques_name)|| empty($opt1) || empty($opt2) || empty($opt3) || empty($opt4) || empty($opt_ans)){
            header("location: ../question.php?quizid=".$quizid."&error=inputempty");
            exit();
        }
        
        $sql= "SELECT * FROM question_answer WHERE quizid='".$quizid."';";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result)>=50){
            header("location: ../question.php?quizid=".$quizid."&error=excessquestions");
            exit();
        }

        if(!preg_match("/^[1-4]$/", $opt_ans)){
            header("location: ../question.php?quizid=".$quizid."&error=invalidans");
            exit();
        }
    
        // add question
        $sql = "INSERT INTO question_answer (quizid, question, opt1, opt2, opt3, opt4, answer) VALUES (?,?,?,?,?,?,?);";
        
        $stmt = prep_sql_execute($conn, $sql, array($quizid, $ques_name, $opt1, $opt2, $opt3, $opt4, $opt_ans), "isssssi");
        if($stmt === false){
            header("location: ../question.php?quizid=".$quizid."&error=failed");
            exit();
        }
        
        mysqli_stmt_close($stmt);
        header("location: ../question.php?quizid=".$quizid."&result=successAdd");
        exit();
    }
}
else{
    header("location: ../index.php?error=failed");
    exit();
}