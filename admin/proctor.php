<?php
    require_once "includes/header.inc.php";
    require_once "includes/functions.inc.php";
    require_once "../includes/database.inc.php";
?>

<?php
    // start with a selection of quizes
    // after selecting a quiz, then Show a list of students who has taken a test for the quiz
    // display the minutes, images and location information, 

    if(isset($_SESSION['admin_id'])){
        if(isset($_GET['error'])){
            echo '<div class="alert alert-warning">';
            if($_GET['error'] =='empty')
                echo 'Input Field cannot be empty!';
            else if ($_GET['error'] == 'failed')
                echo 'Something went wrong, please try again.';
            else if ($_GET['error'] == 'quizNotExist')
                echo 'Quiz does not exist!';            

            echo '</div>';
        }

        // when a student username was chosen 
        if(isset($_POST['userName']) && isset($_POST['quizid'])){
            if(empty($_POST['userName']) || empty($_POST['quizid'])){
                header('location: proctor.php?error=empty');
                exit();
            }
            $userName = trim($_POST['userName']);
            $quizid = trim($_POST['quizid']);

            //check if the student exists
            $sql = "SELECT quizName, fullName, phone, email, startTime, endTime, minute, imgFile, latitude, longitude
                FROM user_info ui, tests t, proctor_info pri, quizes q
                WHERE t.uid = ui.uid AND pri.testid = t.testid AND t.quizid = q.quizid
                    AND userName=? AND q.quizid=".$quizid.";";

            $stmt = prep_sql_execute($conn, $sql, array($userName), "s");
            if($stmt === false){
                header('location: proctor.php?error=failed');
                exit();
            }
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            if($result === false){
                header('location: proctor.php?error=failed');
                exit();
            }

            if(mysqli_num_rows($result) == 0){
                echo '<h6>No details of this student taking the test.</h6>';                
            }
            else{
                $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
                ?>

                <div class="card-body">
                    <div class="px-3 pt-3 card">
                        <h3 class="text-center">Test details</h3>
                        <hr>
                        <table class="table">
                            <tr>
                                <td><b>Full Name:</b> <?php echo $arr[0]['fullName']; ?></td>
                                <td><b>Quiz Name:</b> <?php echo $arr[0]['quizName']?></td>
                            </tr>
                            <tr>
                                <td><b>Phone:</b> <?php echo $arr[0]['phone']?></td>
                                <td><b>Email:</b> <?php echo $arr[0]['email']?></td>
                            </tr>
                            <tr>
                                <td><b>Test start time:</b> <?php echo $arr[0]['startTime']?></td>
                                <?php
                                    $endTime = $arr[0]['endTime'];
                                    if(empty($arr[0]['endTime'])){
                                        $endTime = 'Failed to Complete Test';
                                    }
                                ?>
                                <td><b>Test end time:</b> <?php echo $endTime;?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <script>
                    // function to set the canvas with dataUrl
                    function setCanvasImage(canvasid, data){
                        var myCanvas = document.getElementById(canvasid);
                        console.log("this is from myfunction");
                        console.log("myCanvas: ", myCanvas);
                        var img = new Image;
                        img.src = data;
                        img.onload = function(){
                            myCanvas.getContext('2d').drawImage(img,0,0);
                        };
                    }
                </script>

                <div class="card-body">
                    <div class="px-3 pt-3 card">
                        <table class="table mw-100">
                            <thead>
                                <tr>
                                    <th scope="col">Minutes (count down from 60)</th>
                                    <th scope="col">Image taken</th>
                                    <th scope="col">Latitude</th>
                                    <th scope="col">Longitude</th>
                                </tr>
                            </thead>


                            <?php
                                $i = 0;
                                foreach ($arr as $row){
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle" style="font-size:20px;"><?php echo $row['minute']; ?></td>
                                        <td>
                                            <?php
                                                $dataUrl = "data:image/jpeg;base64,".$row['imgFile'];
                                                $latitude = $row['latitude'];
                                                $longitude = $row['longitude'];
                                                if(empty($latitude) || empty($longitude)){
                                                    $latitude = 'Not available';
                                                    $longitude = 'Not available';
                                                }
                                            ?>

                                            <canvas class="border border-dark" id="myImgCanvas_<?php echo $i;?>" width="310" height="230px"></canvas>'

                                            <script>
                                                setCanvasImage('myImgCanvas_<?php echo $i;?>' , '<?php echo $dataUrl;?>');
                                            </script>

                                        </td>
                                        <td class="text-center align-middle" style="font-size:20px;"><?php echo $latitude; ?></td>
                                        <td class="text-center align-middle" style="font-size:20px;"><?php echo $longitude; ?></td>
                                    </tr>
                                    <?php
                                    ++$i;
                                }
                            ?>

                        </table>
                    </div>
                </div>
                <?php
            }
        }
        // Choosing a student to view their proctor info during test
        else if(isset($_POST['quizName'])){
            if(empty($_POST['quizName'])){
                header('location: proctor.php?error=empty');
                exit();                
            }
            $quizName = trim($_POST['quizName']);

            // check if quiz exists
            $quizid = isQuizExists($conn, $quizName);
            if($quizid === false){
                header('location: proctor.php?error=failed');
                exit();
            }
            else if($quizid === 0 ){
                header('location: proctor.php?error=quizNotExist');
                exit();
            }

            // get list of students who took the quiz test
            $sql = "SELECT userName 
            FROM quizes q, tests t, user_info ui
            WHERE q.quizid = t.quizid AND ui.uid = t.uid
                AND q.quizid = ?;";
            
            $stmt = prep_sql_execute($conn, $sql, array($quizid), "s");
            if($stmt === false){
                header('location: proctor.php?error=failed');
                exit();
            }
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            if($result === false){
                header('location: proctor.php?error=failed');
                exit();
            }

            if(mysqli_num_rows($result)> 0){
                ?>
                <h3>Select Student</h3>
                <p>Choose the student to see his/her test details:</p>
                <form method="post" autocomplete="off">
                    <input type="hidden" name="quizid" value="<?php echo $quizid;?>">
                    <input class="form-control" list="students" name="userName" placeholder="Enter Student Name ...">
                    <datalist id="students">
                        <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value = "'.$row['userName'].'">';
                            }
                        ?>
                    </datalist>
                    <button class="btn btn-primary" type="submit" name="userNameSubmit">Get student details</button>
                </form>
                <?php
            }
            else{
                echo '<h6>There are no students who took this quiz.</h6>';
            }

        }
        // when choosing a quiz to see info on students who took the test
        else{
            $sql = "SELECT quizName FROM quizes;";
            $result = mysqli_query($conn, $sql);
            if($result === false) {
                header('location: index.php?error=failed');
                exit();
            }
            if (mysqli_num_rows($result) > 0) {
                ?>
                <h3>Select quiz</h3>
                <p>Choose a quiz to see the proctor details:</p>
                <form method="post" autocomplete="off">
                    <input class="form-control" list="quizes" name="quizName" placeholder="Enter Quiz Name ...">
                    <datalist id="quizes">
                        <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value = "'.$row['quizName'].'">';
                            }
                        ?>
                    </datalist>
                    <button class="btn btn-primary" type="submit" name="quizNameSubmit">Get Student List</button>
                </form>
                <?php
            }else{
                echo "<h6>There are no quizes to view.<h6>";
            }            
        }
    }
    else{
        header('location: index.php');
        exit();
    }
?>

<?php
    include_once "../includes/footer.inc.php";
?>