<?php
    require_once "../includes/database.inc.php";
    require_once "includes/header.inc.php";
?>

<?php
    if(isset($_SESSION['userUid'])){
        $uid = $_SESSION['userUid'];

        // use error message
        if(isset($_GET['error'])){
            echo '<div class="alert alert-warning">';
            
            if($_GET['error'] == "failed")
                echo 'Something went wrong. Please try again.';
            else if($_GET['error'] == "invalidQuizName")
                echo 'Requested quiz does not exist.';
            else if($_GET['error'] == "noResults")
                echo 'You have not taken/completed the quiz.';
            else if($_GET['error'] == "empty")
                echo 'Please fill in the field!';

            echo '</div>';
        }

        if(isset($_POST['quizName'])){
            if(empty($_POST['quizName'])){
                header('location: results.php?error=empty');
                exit();
            }

            $quizName = trim($_POST['quizName']);

            // check if quizName is a valid existing quiz
            $sql = "SELECT * FROM quizes WHERE quizName=?;";
            $stmt= prep_sql_execute($conn, $sql, array($quizName), "s");
            if($stmt === false){
                header('location: results.php?error=failed');
                exit();
            }
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt)==0){
                header('location: results.php?error=invalidQuizName');
                exit();
            }
            mysqli_stmt_close($stmt);
            
            $sql = "SELECT qa.qid, question, opt1, opt2, opt3, opt4, answer, u_answer
                FROM question_answer qa, quizes q, user_answer ua, tests t
                WHERE qa.quizid = q.quizid AND qa.qid = ua.qid AND t.testid = ua.testid
                    AND q.quizName = '".$quizName."' AND t.uid =".$uid.";";

            $result = mysqli_query($conn, $sql);
            if($result === false) {
                header("Location: results.php?error=failed");
                exit();
            }

            if(mysqli_num_rows($result)>0){
                $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
                $numQues = sizeof($arr);
                $correctQues = 0;
                foreach($arr as $row){
                    if($row['answer'] == $row['u_answer'])
                        ++$correctQues;
                }

                ?>  
                <div><a class="btn btn-primary" href="results.php" style="font-size: 20px;">Go Back</a></div>
                <br>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center"><?php echo $quizName." Result"; ?></h2>
                        <h3 class="text-center">Score : <?php echo $correctQues.'/'.$numQues;?></h3>
                        <hr class="solid">
                        <?php
                        
                            $i =1;
                            foreach($arr as $row){
                                echo "<br>";
                                if($i>1) echo '<hr class="solid">';
                                ?>   
                                <div class="px-5 solution">
                                    <div><?php echo $i?>. <?php echo $row['question'];?></div>
                                    <div class="pb-4 pt-2">
                                        <b>Correct Answer :</b>
                                        <?php 
                                            $option = 'opt'. $row['answer'];
                                            echo $row[$option];
                                        ?>
                                    </div>                                    
                                    <?php 
                                        if($row['u_answer'] == 0)
                                            echo '<div class="px-2 py-3 bg-normal rounded-2"><b>Your Answer:</b> No option selected.</div>';
                                        else{
                                            $option = 'opt'. $row['u_answer'];
                                            if($row['answer'] == $row['u_answer'])
                                                echo '<div class="px-2 py-3 bg-correct rounded-2"><b>Your Answer:</b> '.$row[$option].'</div>';
                                            else
                                                echo '<div class="px-2 py-3 bg-incorrect rounded-2"><b>Your Answer:</b> '.$row[$option].'</div>';
                                        }
                                    ?>
                                </div>  
                                <?php  
                                ++$i; 
                            }
                        ?>
                        <br>
                    </div>
                </div>
                <?php
            }
            else {
                header('location: results.php?error=noResults');
                exit();
            }
                    
        }
        else {
            // get all completed tests
            $sql = "SELECT quizName 
                FROM tests t INNER JOIN quizes q
                ON t.quizid = q.quizid
                WHERE uid =".$uid.";";

            $result = mysqli_query($conn, $sql);
            if($result === false) {
                header("Location: index.php?error=failed");
                exit();
            }

            if(mysqli_num_rows($result)>0){
                ?>
                <br>
                <h3 class="py-3">Test Results</h3>
                <form method="POST" autocomplete="off">
                    <input class="form-control" list="tests" name="quizName" placeholder="Quiz Name">
                    <datalist id="tests">
                        <?php
                            while($row = mysqli_fetch_assoc($result)){
                                echo '<option>'.$row['quizName'].'</option>';
                            }
                        ?>
                    </datalist>
                    <button type="submit" class="btn btn-primary">Get Result</button>
                </form>
                <?php

            }
            else{
                echo '<br><br><p style="font-size: 20px;">You have not done any tests so far. Come back after a test to see your results.</p>';
            }
        }
        
    }
    else{
        header('location: index.php');
        exit();
    }
?>
<?php
    require_once "../includes/footer.inc.php"
?>