<?php
    session_start();
    require_once '../includes/database.inc.php';
    require_once 'includes/functions.inc.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Quiz</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../styles/test_style.css">
        <link rel="icon" href="../images/pencil.ico">

    </head>

    <body>
        <?php
            if(!isset($_SESSION['userUid']) || !isset($_POST['quizid']) || !isset($_POST['invigil_id']) || !isset($_POST['pwd'])){
                header("location: index.php");
                exit();
            }else{
                $quizid = $_POST['quizid'];
                $uid = $_SESSION['userUid'];
                $invigil_id_input = $_POST['invigil_id'];
                $pwd_input = $_POST['pwd'];
                
                // subject id for quiz 
                $subid = getSubId($conn, $quizid);
                if($subid === false){
                    header("location: index.php?error=failed");
                    exit();
                }

                if(empty($invigil_id_input) || empty($pwd_input)){
                    header("location: test_auth.php?quizid=".$quizid."&error=emptyInput");
                    exit();
                } 

                // Check if invilagtor id and pwd are correct
                $result = getInvigilatorInfo($conn, $quizid);
                if($result === false){
                    header("location: test_auth.php?quizid=".$quizid."&error=failed");
                    exit();
                }

                $invigilDetails = mysqli_fetch_assoc($result);
                $pwdCheck = password_verify($pwd_input, $invigilDetails['pwd']);
                if($invigil_id_input != $invigilDetails['invigil_id'] || $pwdCheck === false){
                    header("location: test_auth.php?quizid=".$quizid."&error=invalidInput");
                    exit();
                }

                ?>
                <div class="pt-5 container d-flex justify-content-center">

                    <!-- Webcam permission check-->
                    <div class="m-3 card">
                        <div class= "card-body">
                            <h3 class="card-title text-center">Web camera</h3>
                            <div class="py-2 border border-dark border-4">
                                <video id="video" class="mw-100" width="350px" height="230px" autoplay></video>
                            </div>
                            <br>
                            <div class="d-flex justify-content-center">
                                <button id="cameraStart" class="btn btn-primary">Turn On Camera</button>
                            </div>
                        </div>
                    </div>

                    <!-- vertical line using bootstrap -->
                    <div class="mx-5 vr rounded-3" style="width:8px" ></div>

                    <!-- Geo location permission check--> 
                    <div class="m-3 card">
                        <div class= "card-body">
                            <h3 class="card-title text-center">Geo-location</h3>
                            <img class="m-3 mw-100" src="../images/location_icon.png" height="180"></img>
                            <br><br>
                            <p id="geolocFeedbackLatitude">Latitude: Unknown</p>
                            <p id="geolocFeedbackLongitude">Longitude: Unknown</p>
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary" id="geolocStart">Turn on Location</button>
                            </div>
                        </div>
                    </div> 
                </div>

                <!-- Rules of exam and agreement -->
                <div class="px-5 container">
                    <div class="m-3 card">
                        <div class= "card-body">
                            <h3>Rules:</h3>
                            <ul>
                                <li>Please enable webcam and Geo-location access permissions for this site.</li>
                                <li>The test will be for 1 hour of multiple choice type. The timer will start as soon as you start the test.</li>
                                <li>Take the test in fullscreen. Leaving fullscreen will prevent access to the test.</li>
                                <li>Make sure your face is within the frame of the webcam's video.</li>
                                <li>Use Google Chrome or Firefox browser for the test.</li>
                            </ul>
                            <div id="webcamStatus"><i class="bi-exclamation-circle text-danger">&nbsp;Webcam access disabled</i></div>
                            <div id="geolocStatus"><i class="bi-exclamation-circle text-danger">&nbsp;Geo-location access disabled</i> </div>
                            <br>
                            <form action="test.php" method="post">
                                <input type="hidden" name="quizid" value="<?php echo $quizid;?>">
                                <input type="hidden" name="subid" value="<?php echo $subid;?>">
                                <input class="form-check-input" type="checkbox" id="agreementCheckbox">
                                <label class="form-check-label" for="agreementCheckbox"> I agree to follow the above rules and to take the test without any malpractise.</label>
                                <br><br>
                                <div class="d-flex justify-content-center">
                                    <button id="startTestBtn" type="submit" class="btn btn-success" disabled>Start Test</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <br>
                <?php
                
            }
        ?>
        <script src="js/test_permission.js"></script>
    </body>
</html>
    
        