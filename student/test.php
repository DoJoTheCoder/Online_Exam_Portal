<?php
    session_start();
    require_once '../includes/database.inc.php';
    require_once 'includes/functions.inc.php';
    if(!isset($_SESSION['userUid']) && !isset($_POST['quizid']) && !isset($_POST['subid'])){
        header("Location: index.php");
        exit();
    }

    $_SESSION['quizid'] = $_POST['quizid'];
    $quizid = $_POST['quizid'];
    $subid = $_POST['subid'];
    $uid = $_SESSION['userUid'];

    // get questions belonging to quiz
    $sql = "SELECT * FROM question_answer WHERE quizid='".$quizid."' ORDER BY RAND();";
    $result = mysqli_query($conn, $sql);
    if($result === false){
        header("location: index.php?error=failed");
        exit();
    }
    $num_ques = mysqli_num_rows($result); 
    
    // If there are no questions in the quiz
    if($num_ques == 0){
        header("location: index.php?choice=".$subid."&error=NoQuestions");
        exit();
    }   

    $ques_arr = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // setting default options for mcqs and timer
    $timeLeft = 60;
    $mcqOptions = [];
    foreach($ques_arr as $mcq){
        $qid = $mcq['qid'];
        $mcqOptions[$qid] = 0;
    }

    // Check if test is being resumed, is started from scratch, or was completed before.
    $testInfo = getTestInfo($conn, $quizid, $uid);
    if($testInfo === false){
        header("location: index.php?choice=".$subid."&error=failed");
        exit();
    }
    // if the test was started from scratch 
    else if($testInfo === 0){
        // setting time zone to UTC 
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/London'));
        
        // create a test record for test table in database
        $sql = "INSERT INTO tests (uid, quizid, startTime) VALUES( '".$uid."', ".$quizid.", '".$now->format('Y-m-d H:i:s')."' );";
        if(mysqli_query($conn, $sql) === false){
            header("location: index.php?choice=".$subid."&error=failed");
            exit();
        }

        // get the new test info
        $testInfo = getTestInfo($conn, $quizid, $uid);
        if($testInfo === false){
            header("location: index.php?choice=".$subid."&error=failed");
            exit();
        }
    }
    // if the test was partially done, due to some interuption like power failure
    else{
        // get remaining time
        $testCookieName = "test_".$testInfo['testid'];

        // check if time cookie exists
        if(!array_key_exists($testCookieName, $_COOKIE) || !isset($_COOKIE[$testCookieName])){
            header("location: index.php?choice=".$subid."&error=failed");
            exit();
        }
        $timeLeft = $_COOKIE[$testCookieName];

        // get the user's saved answers from cookie and set the mcq option
        foreach($ques_arr as $mcq){
            $qid = $mcq['qid'];
            if(array_key_exists($qid, $_COOKIE) && isset($_COOKIE[$qid])){
                $mcqOptions[$qid] = $_COOKIE[$qid];
            }
        }
    }
    
    $_SESSION['testid'] = $testInfo['testid'];
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Quiz</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

        <!-- cookie API for javascript from https://github.com/js-cookie/js-cookie/tree/latest#readme -->
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
            
        <link rel="icon" href="../images/pencil.ico">
        <link rel="stylesheet" href="../styles/test_style.css">

    </head>

    <body onload="initTest(<?php echo $timeLeft.','.$testInfo['testid'];?>)">
        <!-- Canvas is used to draw image from webcam and convert to dataURI -->
        <canvas class="d-none" id="canvas" width="320px" height="230px"></canvas>

        <!-- Timer is shown here -->
        <div class="shadow bg-success" style="z-index: 1070" id="timer">1:00:00</div>

        <!-- Modal to make user take test in fullscreen mode -->
        <div class="modal fade pt-5" id="fullscreenModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="fullscreenModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h1 class="modal-title text-danger" id="fullscreenModalLabel"><b>Do not exit fullscreen!!!</b></h1>
                    </div>
                    <div class="modal-body">
                        <h3 class="text-center">Please enable fullscreen to continue taking the test!!!</h3>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button id="fullbtn" type="button" class="btn btn-success mw-100" data-bs-dismiss="modal">Go Fullscreen and Continue</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal to make sure camera and geolocation permission is on -->
        <div class="modal fade pt-5" id="permissionModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="permissionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h1 class="modal-title text-danger" id="permissionModalLabel"><b>Please enable permissions!!!</b></h1>
                    </div>
                    <div class="modal-body">
                        <h3 class="text-center">To take this test, permission to access webcam and geolocation must be enabled.</h3>
                        <br>
                        <div class="d-flex justify-content-center">
                            <div class="py-2 border border-dark border-4">
                                <video id="video" class="mw-100" width="350px" height="230px" autoplay></video>
                            </div>

                            <!-- vertical line using bootstrap -->
                            <div class="mx-5 vr rounded-3" style="width:8px" ></div>

                            <table class="px-3">
                                <tr><td><h4 id="geolocFeedbackLatitude">Latitude: Unknown</h4></td></tr>
                                <tr><td><h4 id="geolocFeedbackLongitude">Longitude: Unknown</h4></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button id="permissionbtn" type="button" class="btn btn-success mw-100" data-bs-dismiss="modal" disabled>Continue</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content displayed -->
        <div class="container"> 
            <form method="post" action="test_result.php">
                <div class = 'card-body'>
                <?php 

                $i=1;
                foreach($ques_arr as $mcq){
                    ?>
                    <!-- Question and options card -->
                    <div class = "card">
                        <?php
                            $qid = $mcq['qid'];
                            $defOption = $mcqOptions[$qid];
                        ?>

                        <p class="question"><?php echo $i.'. '.$mcq['question'];?></p>
                        
                        <input type="hidden" value="0" 
                            name="<?php echo $mcq['qid'];?>" 
                            <?php if($defOption == 0) echo "checked";?>
                        /> 
                        
                        <label>
                            <input onchange="saveOption(this)" class="form-check-input option" type="radio" value="1" 
                                name="<?php echo $mcq['qid'];?>"
                                <?php if($defOption == 1) echo "checked";?>
                            />
                            <?php echo $mcq['opt1'];?>
                        </label>

                        <label>
                            <input onchange="saveOption(this)" class="form-check-input option" type="radio" value="2" 
                                name="<?php echo $mcq['qid'];?>"
                                <?php if($defOption == 2) echo "checked";?>
                            />
                            <?php echo $mcq['opt2'];?>
                        </label>

                        <label>
                            <input onchange="saveOption(this)" class="form-check-input option" type="radio" value="3" 
                                name="<?php echo $mcq['qid'];?>"
                                <?php if($defOption == 3) echo "checked";?>
                            />
                            <?php echo $mcq['opt3'];?>
                        </label>
                        
                        <label>
                            <input onchange="saveOption(this)" class="form-check-input option" type="radio" value="4" 
                                name="<?php echo $mcq['qid'];?>"
                                <?php if($defOption == 4) echo "checked";?>
                            />
                            <?php echo $mcq['opt4'];?>
                        </label>
                    </div>
                    <br>
                    <?php
                    ++$i;
                }
                ?>
                </div>
                <div style="text-align:center">
                    <button id="sub" class="btn btn-success" type="submit">Submit</button>
                </div>
                <br>
            </form>
        </div>
        
        <script src="js/test.js"></script>
    </body>
</html>