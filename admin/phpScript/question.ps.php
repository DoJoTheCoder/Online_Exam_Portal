<!-- Validates the quiz choice and redirects to question edit page of the quiz chosen -->
<?php
    require_once '../../includes/database.inc.php';
    require_once '../includes/functions.inc.php';

    if(isset($_POST['quizChoice'])){
        $quiz_name = trim($_POST['quizChoice']);
        if(empty($quiz_name)){
            header('Location: ../question.php?error=inputempty');
            exit();
        }

        $quizid = isQuizExists($conn, $quiz_name);
        if($quizid === false){
            header('Location: ../question.php?error=failed');
            exit();
        }
        else if($quizid ==0){
            header('Location: ../question.php?error=quiznotexist');
            exit();
        }

        header("Location: ../question.php?quizid=".$quizid);
        exit();
    }   
    else{
        header("Location: question.php");
        exit();
    }
