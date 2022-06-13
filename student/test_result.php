<?php
    session_start();
    require_once '../includes/database.inc.php';
    $quizid = $_SESSION['quizid'];
    $testid = $_SESSION['testid'];
    $uid = $_SESSION['userUid'];
    unset($_SESSION['quizid']);
    unset($_SESSION['testid'])
?>

<?php 
    // update tests table and record submission time
    $now = new DateTime();
    $now->setTimezone(new DateTimeZone('Europe/London'));
    $sql = "UPDATE tests SET endTime = '".$now->format('Y-m-d H:i:s').
        "' WHERE testid=".$testid.";";
    if(mysqli_query($conn, $sql) === false){
        header("location: index.php?error=failed");
        exit();
    }

    $right_answer=0;
    $keys=array_keys($_POST);
    $order=join(",",$keys);

    $sql = "SELECT * from question_answer where qid IN (".$order.");";
    $result=mysqli_query($conn, $sql);
    if($result === false){
        header("location: index.php?error=failed");
        exit();
    }
    $total = mysqli_num_rows($result);

    while($row=mysqli_fetch_assoc($result)){
        $qid = $row['qid'];
        $res1 = $row['answer']; // correct answer
        $res2 = $_POST[$qid]; // user's answer

        if($res2==$res1){
            ++$right_answer;
        }

        // saving user answers in user_answer table 
        $sql = "INSERT INTO user_answer (testid, qid, u_answer) VALUES (".$testid.", ".$qid.", ".$res2.");";
        mysqli_query($conn, $sql) or die("ERROR: Answer upload to Database failed. file: test_result.php");
        
        //unsetting cookies for options chosen
        setcookie($row['qid'], "", time()-3600);
    }
    // unsetting cookies for test timer
    setcookie("test_".$testid, "", time()-3600);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Result</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">     
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../styles/test_style.css">
        <link rel="icon" href="../images/pencil.ico">

    </head>
    <body>
        <div class="container">
           <div class="card-body"> 
                <div class="card"> 
                    <div class=" d-flex justify-content-center">
                        <div>
                            <h1>Score : <?php echo $right_answer;?>/<?php echo $total?></h1>
                            <?php 
                                if($right_answer < $total/4) { 
                                    echo "<h1>Work hard, you should do better next time.</h1>";
                                }
                                else if($right_answer< $total/2){
                                    echo "<h1>Good, but needs more practice.</h1>";
                                } else if($right_answer<$total){
                                    echo "<h1>Very good, aim for the perfect score next time.</h1>";
                                } else {
                                    echo "<h1>Excellent! You got a perfect score.</h1>";
                                } 
                            ?>
                        </div>
                    </div>
                </div>
            </div>    
            
            <div class="card">
                <?php 
                    $sql = "SELECT * from question_answer where quizid=".$quizid.";";
                    $res = mysqli_query($conn,$sql);
                    $i=1;
                    while($row=mysqli_fetch_assoc($res))
                    { 
                        echo "<br>";
                        if($i>1) echo '<hr class="solid">';
                        ?>       
                        <div class="px-5 solution">
                            <div> <?php echo $i?>. <?php echo $row['question'];?></div>
                            <div class="pb-4 pt-2">
                                <b>Correct Answer :</b>
                                <?php 
                                    $option = 'opt'. $row['answer'];
                                    echo $row[$option];
                                ?>
                            </div>
                            <?php 
                                $qid = $row['qid'];
                                $option = $_POST[$qid];
                                if($option == 0)
                                    echo '<div class="px-2 py-3 bg-normal rounded-2"><b>Your Answer:</b> No option selected.</div>';
                                else{
                                    $option = 'opt'. $_POST[$qid];
                                    if($row['answer'] == $_POST[$qid])
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
                <div class="d-flex justify-content-center"><a class="btn btn-primary mw-100" href="index.php"><span style="font-size: 20px;">Continue<span></a></div>          
            </div>     
        </div>
    </body>
</html>