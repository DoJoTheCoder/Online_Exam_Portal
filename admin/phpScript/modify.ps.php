<!-- modify a subject, quiz or question -->
<?php
if(isset($_POST['submit'])){
    require_once '../../includes/database.inc.php';
    require_once '../includes/functions.inc.php';

    // To modify the subject
    if($_POST['submit']=='mod_subject'){
        $oldsubname = trim($_POST['oldsubname']);
        $newsubname = trim($_POST['newsubname']);

        if(empty($oldsubname)||empty($newsubname)){
            header("location: ../subject.php?error2=inputempty");
            exit();
        }
        
        $prev_id = isSubjectExists($conn, $oldsubname);
        $id = isSubjectExists($conn, $newsubname);
        if($id === false|| $prev_id === false || $prev_id == 0){
            header("location: ../subject.php?error2=failed");
            exit();
        }
        else if($id >0){
            header("location: ../subject.php?error2=subjectexists");
            exit();
        }
    
        $sql = "UPDATE subjects SET subName = ? WHERE subName = ?;";
        $stmt = prep_sql_execute($conn, $sql, array($newsubname, $oldsubname), "ss");
        if($stmt === false){
            header("location: ../subject.php?error2=failed");
            exit();
        }
    
        mysqli_stmt_close($stmt);
        header("location: ../subject.php?result2=success");
        exit();
    }
    // To modify the quiz
    else if($_POST['submit']=='mod_quiz'){
        $oldquizname = trim($_POST['oldquizname']);
        $newquizname = trim($_POST['newquizname']);

        if(empty($oldquizname)||empty($newquizname)){
            header("location: ../quiz.php?error2=inputempty");
            exit();
        }

        $prev_id = isQuizExists($conn, $oldquizname);
        $id = isQuizExists($conn, $newquizname);
        if($id === false|| $prev_id === false || $prev_id ==0){
            header("location: ../quiz.php?error2=failed");
            exit();
        }
        else if($id >0){
            header("location: ../quiz.php?error2=subjectexists");
            exit();
        }

        $sql = "UPDATE quizes SET `quizName` = ? WHERE `quizName` = ?;";
        $stmt = prep_sql_execute($conn, $sql, array($newquizname, $oldquizname), "ss");
        if($stmt === false){
            header("location: ../quiz.php?error2=failed");
            exit();
        }

        mysqli_stmt_close($stmt);
        header("location: ../quiz.php?result2=success");
        exit();
    }
    // to modify a question
    else if($_POST['submit']=="mod_question"){
        // check if qid is valid
        $qid = $_POST['qid'];
        $quizid = $_POST['quizid'];
        $ques_name = trim($_POST['ques_name']);
        $opt1 = trim($_POST['opt1']);
        $opt2 = trim($_POST['opt2']);
        $opt3 = trim($_POST['opt3']);
        $opt4 = trim($_POST['opt4']);
        $opt_ans = trim($_POST['opt_ans']);
        
        if(empty($ques_name)|| empty($opt1) || empty($opt2) || empty($opt3) || empty($opt4) || empty($opt_ans)){
            header("location: ../question.php?quizid=".$quizid."&error=inputempty");
            exit();
        }

        if(!preg_match("/^[1-4]$/", $opt_ans)){
            header("location: ../question.php?quizid=".$quizid."&error=invalidans");
            exit();
        }
    
        // update question
        $sql = "UPDATE question_answer
        SET question=?, opt1=?, opt2=?, opt3=?, opt4=?, answer=?
        WHERE qid=?;";
        $stmt = prep_sql_execute($conn, $sql, array($ques_name, $opt1, $opt2, $opt3, $opt4, $opt_ans, $qid), "sssssii");
        if($stmt === false){
            header("location: ../question.php?quizid=".$quizid."&error=failed");
            exit();
        }

        mysqli_stmt_close($stmt);
        header("location: ../question.php?quizid=".$quizid."&result=successMod");
        exit();
    }
    else{
        header("location: ../index.php?error=failed");
        exit();
    }
}
else{
    header("location: ../index.php?error=failed");
    exit();
}