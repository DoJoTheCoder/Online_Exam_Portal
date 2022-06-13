<!-- deletes a subject, quiz or question -->

<?php
if(isset($_POST['submit'])){
    require_once '../../includes/database.inc.php';
    require_once '../includes/functions.inc.php';
    
    // Deletion of subject
    if($_POST['submit']=='del_subject'){
        $sub_name = trim($_POST['sub_name']);

        if(empty($sub_name)){
            header("location: ../subject.php?error3=inputempty");
            exit();
        }

        $id = isSubjectExists($conn, $sub_name);
        if($id === false){
            header("location: ../subject.php?error3=failed");
            exit();
        }
        else if($id == 0){
            header("location: ../subject.php?error3=notexist");
            exit();
        }

        $sql = "DELETE FROM subjects WHERE `subName`=?";
        $stmt = prep_sql_execute($conn,$sql, array($sub_name), "s");
        if($stmt === false){
            header("location: ../subject.php?error3=failed");
            exit();
        }

        mysqli_stmt_close($stmt);
        header("location: ../subject.php?result3=success");
        exit();
    }
    // deletion of quiz
    else if($_POST['submit']=='del_quiz'){
        $quiz_name = trim($_POST['quiz_name']);

        if(empty($quiz_name)){
            header("location: ../quiz.php?error3=inputempty");
            exit();
        }
    
        $id = isQuizExists($conn, $quiz_name);
        if($id === false){
            header("location: ../quiz.php?error3=failed");
            exit();
        }
        else if($id == 0){
            header("location: ../quiz.php?error3=notexist");
            exit();
        }
    
        $sql = "DELETE FROM quizes WHERE quizName = ?;";
        $stmt = prep_sql_execute($conn, $sql, array($quiz_name), "s");
        if($stmt === false){
            header("location: ../quiz.php?error3=failed");
            exit();
        }
    
        mysqli_stmt_close($stmt);
        header("location: ../quiz.php?result3=success");
        exit();
    }
    // deletion of question
    else if($_POST['submit']=='del_question'){      
        $qid = $_POST['qid'];
        $quizid = $_POST['quizid'];
        
        $sql = "DELETE FROM question_answer WHERE qid =".$qid.";";
        if(mysqli_query($conn, $sql) === false){
            header("location: ../question.php?quizid=".$quizid."&error=failed");
            exit();
        }

        // if quiz has no questions, then delete all related tests taken by students
        $sql = "SELECT * FROM question_answer WHERE quizid =".$quizid.";";
        $result = mysqli_query($conn, $sql);
        if($result === false){
            header("location: ../question.php?quizid=".$quizid."&error=failed");
            exit();
        }

        if(mysqli_num_rows($result) == 0){
            // delete quiz tests taken by students
            $sql = "DELETE FROM tests WHERE quizid =".$quizid.";";
            if(mysqli_query($conn, $sql) === false){
                header("location: ../question.php?quizid=".$quizid."&error=failed");
                exit();
            }
        }

        header("location: ../question.php?quizid=".$quizid."&result=successDel");
        exit();
    }

}
else{
    header("location: ../index.php");
    exit();
}