<?php
    include_once "includes/header.inc.php";
    require_once "../includes/database.inc.php";    
?>

<?php
    if(isset($_SESSION['userUid'])){
        if(isset($_GET['choice'])){
            echo '<div><a class="btn btn-primary" href="index.php">Go Back</a></div><br>';
            
            // error messages
            if(isset($_GET['error'])){
                echo '<div class="alert alert-warning">';
                if($_GET['error']=='failed')
                    echo 'Something went wrong, please try again later.';
                else if($_GET['error']=='NoQuestions')
                    echo 'This quiz has no questions. Please contact your administrator if this is a mistake.';
                else if($_GET['error']=='testCompleted')
                    echo 'This quiz test was already completed. Please refer results page to see your marks for this quiz.';
                else if($_GET['error']=='quizNotReady')
                    echo 'This quiz is not available for tests yet. Please contact your administrator to resolve this issue.';

                echo '</div>';
            }

            echo '<br>
            <h2>Quiz list</h2>';

            $choice = $_GET['choice'];
            $sql = "SELECT * FROM quizes WHERE subid=?;";

            $stmt = prep_sql_execute($conn, $sql, array($choice), "s");
            if($stmt === false){
                header("location: index.php?error=failed");
                exit();
            }

            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            if(mysqli_num_rows($result)>0){
                echo '<ul>';
                while($row = mysqli_fetch_assoc($result)){
                    echo '<li><a class="text-decoration-none" href="test_auth.php?quizid='.$row['quizid'].'">'. $row['quizName'] .'</a></li>';
                }
                echo '</ul>';
            }
            else{
                echo 'No quizes found.';
            }
        }
        else{
            if(isset($_GET['error'])){
                echo '<div class="alert alert-warning">';
                if($_GET['error']=='failed')
                    echo 'Something went wrong, please try again later.';
                echo '</div>';
            }
            echo '<br><h2>Subject List</h2>
            To take a quiz, choose the relevant subject.
            <ul>';
            $sql = "SELECT * FROM subjects;";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result)>0){
                while($row = mysqli_fetch_assoc($result)){
                    echo '<li><a class="text-decoration-none" href="index.php?choice='.$row['subid'].'">'. $row['subName'].'</a></li>';
                }
            }
            else{
                echo 'No subjects found.';
            }
            echo '</ul>';
        }
    }
    else{
        echo '<br><h1>Welcome!</h1>
        <p>To get access to quizes, log in or sign up.</p>';
    }
?>

<?php
    include_once "../includes/footer.inc.php";
?>
