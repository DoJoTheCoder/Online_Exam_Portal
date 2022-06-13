<?php
require_once "includes/header.inc.php";
require_once "includes/functions.inc.php";
require_once "../includes/database.inc.php";
?>

<?php
    //main module with add, modify, delete options
    if (isset($_SESSION['admin_name'])) {
        if (isset($_GET['quizid']) && !empty($_GET['quizid'])) {
            $quizid =$_GET['quizid'];
            // check if quiz id is valid
            $sql = "SELECT * FROM quizes WHERE quizid ='".$quizid."';";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) <=0) {
                header("Location: question.php?error=quiznotexist");
                exit();
            }

            $invigilator_id = 'not set';
            $invigilator_pwd = 'not set';
            $invigilDetails = getInvigilatorInfo($conn, $quizid);
            if($invigilDetails === false) {
                header("Location: question.php?error=failed");
                exit();
            }
            else if(mysqli_num_rows($invigilDetails) != 0){
                $row = mysqli_fetch_assoc($invigilDetails);
                $invigilator_id = "'".$row['invigil_id']."'";
                $invigilator_pwd = 'successfully set';
            }
            ?>
            <div><a class="btn btn-primary" href="question.php">Choose different quiz</a></div>
            <br>
            
            <?php
            // error message
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-warning">';

                if ($_GET['error'] == 'excessquestions')
                    echo 'Max limit for number of questions reached!';
                else if ($_GET['error'] == 'failed')
                    echo 'Failed request! Please try again later.';
                else if ($_GET['error'] == 'inputempty') 
                    echo 'Please fill all fields!';
                else if ($_GET['error'] == 'invalidans')
                    echo 'Invalid option number for answer. Must be from 1-4.';
                else if($_GET['error'] == 'invalidExtension')
                    echo 'Only \'.xlsx\' extension files are allowed.';
                else if($_GET['error'] == 'emptyfile')
                    echo 'No file was chosen';
                else if($_GET['error'] == 'invalidXlsxFormat')
                    echo 'The excel file that was uploaded doesn\'t follow the format.';
                else if ($_GET['error'] == 'maxQuestionLimit')
                    echo 'The excel file must only have maximum of 50 questions.';
                else if ($_GET['error'] == 'emptyXlsxCell')
                    echo 'The excel file must not have a empty row or cell.';

                echo '</div>';
            }

            //success message
            if (isset($_GET['result'])) {
                echo '<div class="alert alert-success">';
                if($_GET['result'] == 'successAdd')
                    echo 'Question added successfully.';
                else if ($_GET['result'] == 'successMod')
                    echo 'Question changed successfully.';
                else if($_GET['result'] == 'successDel')
                    echo 'Question deleted successfully.';
                else if($_GET['result'] == 'successUpd')
                    echo 'Upload successful.';
                else if($_GET['result'] == 'successInvigilUpd')
                    echo 'Successfully updated Invigilator ID and password.';

                echo '</div>';
            }
?>
<!-- ############################################################# -->
<!-- uploading excel questions and setting invigilator ID and pwd -->
    <div class="d-flex justify-content-center">
        <!-- for uploading questions using a excel file -->
        <div class="card-body">
            <div class="p-3 card text-center">
                <form action="phpScript/upload.ps.php" method="post" enctype="multipart/form-data" id="quesUploadForm" autocomplete="off">
                    <label for="excelFile" class= "form-label">Upload a excel file with all the questions.</label>

                    <input type="hidden" name="quizid" value="<?php echo $quizid;?>">
                    <input class="form-control" type="file" id="excelFile" name="excelFile">
                </form>

                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#quesUploadModal">Upload Questions</button>

                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#excelFormatModal">Show Excel format</button>
            </div>
        </div>

        <div class="card-body">
            <div class="p-3 card text-center">
                <h5>Invigilator Details</h5>
                <br>
                <div>Invigilator ID is <?php echo $invigilator_id;?></div>
                <div>Invigilator password is <?php echo $invigilator_pwd;?></div>
                <br>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#invigilatorModal">Set new ID/password</button>
            </div>
        </div>
    </div>
    <br>
    <hr class="solid">
    <br>
    <h3 class="text-center">Question List:</h3>

<!-- #################################################### -->
<!-- Question add, modify and delete buttons. Table of questions display-->
        <div class="container">
            <?php
                $sql = "SELECT * FROM question_answer WHERE quizid ='".$quizid."';";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result)==0){
                    echo '<br><h5>No questions here. Start adding them!</h5>';
                }
            ?>
            <div class="card-body">
                <div class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Question</th>
                                <th scope="col">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quesAddModal">Add</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(mysqli_num_rows($result)>0){
                                    $i = 1;
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo'
                                            <tr>   
                                                <th scope="row">'.$i.'</th>
                                                <td>'.$row['question'].'</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary modbtn" >Modify</button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger delbtn">Delete</button>
                                                </td>
                                                <input type="hidden" value="'.$row['qid'].'">
                                                <input type="hidden" value="'.$row['quizid'].'">
                                                <input type="hidden" value="'.$row['question'].'">
                                                <input type="hidden" value="'.$row['opt1'].'">
                                                <input type="hidden" value="'.$row['opt2'].'">
                                                <input type="hidden" value="'.$row['opt3'].'">
                                                <input type="hidden" value="'.$row['opt4'].'">
                                                <input type="hidden" value="'.$row['answer'].'">
                                            </tr>';
                                        ++$i;
                                    }  
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<!-- ################################################### -->
<?php
    // Contains all modals for add, delete, modify
    require_once 'includes/question_modal.inc.php';
?>

<?php
    } 
    else {
        ?>
        <h3>Select quiz</h3>
        <p>Choose a quiz to modify it's questions:</p>
        <form action="phpScript/question.ps.php" method="post" autocomplete="off">

            <input class="form-control" list="quizes" name="quizChoice" placeholder="Quiz of question">
            <datalist id="quizes">
            <?php
                $sql = "SELECT quizName, subName FROM (quizes q INNER JOIN subjects s on s.subid = q.subid);";
                $subList = mysqli_query($conn, $sql);
                if (mysqli_num_rows($subList) > 0) {
                    while ($row = mysqli_fetch_assoc($subList)) {
                        echo '<option value = "' . $row['quizName'] . '">' . $row['subName'] . '</option>';
                    }
                }
            ?>
            </datalist>
            <button class="btn btn-primary" type="submit" name="quizNameSubmit">Show questions</button>
        </form>
        <?php

        if (isset($_GET['error'])) {
            echo '<div class="alert alert-warning">';
            
            if ($_GET['error'] == 'inputempty') 
                echo 'Please fill in the field!';
            else if ($_GET['error'] == 'failed') 
                echo 'Failed request! Please try again later.';
            else if ($_GET['error'] == 'quiznotexist') 
                echo 'Quiz does not exist!';

            echo "</div>";
        }
    }
} 
else {
    header("Location: index.php");
    exit();
}
?>

<?php
include_once "../includes/footer.inc.php";
?>