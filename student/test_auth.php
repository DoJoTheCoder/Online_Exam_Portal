<?php
    session_start();
    require_once "includes/functions.inc.php";
    require_once "../includes/database.inc.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Quiz</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
            
        <link rel="icon" href="../images/pencil.ico">
        <link rel="stylesheet" href="../styles/test_style.css">

    </head>
    
    <body>
        <div class="container">
            <?php
                if(isset($_SESSION["userUid"]) && isset($_GET['quizid']) && !empty($_GET['quizid'])){
                    // error messages
                    if(isset($_GET['error'])){
                        echo '<br><div class="alert alert-warning text-center">';
                        if($_GET['error']=='failed')
                            echo 'Something went wrong. Please try again later.';
                        if($_GET['error']=='invalidInput')
                            echo 'The ID or Password entered is invalid.';
                        if($_GET['error']=='emptyInput')
                            echo 'The input field is empty. Please enter details.';
        
                        echo '</div>';
                    }

                    $quizid = $_GET['quizid'];
                    $uid =$_SESSION['userUid'];

                    // check if quiz exists
                    $quizExist= isQuizExist($conn, $quizid);
                    if($quizExist === false || $quizExist == 0){
                        header("location: index.php?error=failed");
                        exit();
                    } 

                    // subject id for quiz 
                    $subid = getSubId($conn, $quizid);
                    if($subid === false){
                        header("location: index.php?error=failed");
                        exit();
                    }

                    // check if test was already completed
                    $testInfo = getTestInfo($conn, $quizid, $uid);
                    if($testInfo === false) {
                        header("location: index.php?choice=".$subid."&error=failed");
                        exit();
                    }
                    
                    if($testInfo !== 0){
                        // if test was already completed
                        if(!is_null($testInfo['endTime'])){
                            header("location: index.php?choice=".$subid."&error=testCompleted");
                            exit();
                        }
                    }

                    // check if invigilator ID and pwd exists for this quiz
                    $invigilDetails = getInvigilatorInfo($conn, $quizid);
                    if($invigilDetails === false){
                        header("location: index.php?choice=".$subid."&error=failed");
                        exit();
                    }
                    else if(mysqli_num_rows($invigilDetails) == 0){
                        header("location: index.php?choice=".$subid."&error=quizNotReady");
                        exit();
                    }

                    ?>
                    <div class="pt-5 d-flex justify-content-center">
                        <div class="card">
                            <div class="px-5 card-body">
                                <h3 class="text-center">Enter Invigilator ID and Password:</h2>
                                <hr>
                                <br>
                                <div class="pl-5 text-center">
                                    <p style="font-size: 20px">Ask your teacher/invigilator for the ID and password for permission to take the test.</p>
                                </div>
                                <br>
                                <div class="d-flex justify-content-center">
                                    <div class="w-75">
                                        <form action="test_permission.php" method="post" autocomplete="off">
                                            <input type="hidden" name="quizid" value="<?php echo $quizid;?>">
                                            <input class="form-control" name="invigil_id" type="text" placeholder="Enter invigilator ID" >
                                            <br>
                                            <input class="form-control" name="pwd" type="password" placeholder="Enter invigilator password">
                                            <br>
                                            <button class="btn btn-primary w-100" style="font-size: 20px;">Submit</button>
                                        </form>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <?php 
                }
                else{
                    header("location: index.php");
                    exit();
                }
            ?>
        </div>
    </body>
</html>