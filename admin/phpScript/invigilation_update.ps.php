<!-- Updates the invigilator ID and password -->
<?php 
    require_once "../../includes/database.inc.php";
    if(isset($_POST['submit'])){
        if(empty($_POST['quizid'])){
            header('Location: question.php?error=failed');
            exit();
        }
        $quizid = $_POST['quizid'];
        $prev_page = "../question.php?quizid=".$quizid;

        if(empty($_POST['invigil_id']) || empty($_POST['invigil_pwd'])){
            header('Location: '.$prev_page.'&error=inputempty');
            exit();
        }
        $id = $_POST['invigil_id'];
        $pwd = $_POST['invigil_pwd'];
        $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);

        // check if id and pwd are already set
        $sql = "SELECT * FROM invigilator_info WHERE quizid=".$quizid.";";
        $result =mysqli_query($conn, $sql);
        if($result === false){
            header('Location: '.$prev_page.'&error=failed');
            exit();
        }
        
        if(mysqli_num_rows($result) == 0){
            $sql = "INSERT INTO invigilator_info (invigil_id, quizid, pwd) 
                VALUES (".$id.", ".$quizid.", '".$hashedpwd."');";
        }
        else {
            $sql = "UPDATE invigilator_info 
                SET invigil_id=".$id.", pwd='".$hashedpwd."'
                WHERE quizid=".$quizid.";";
        }

        // set new value of id and pwd
        $result =mysqli_query($conn, $sql);
        if($result === false){
            header('Location: '.$prev_page.'&error=failed');
            exit();
        }
        
        header('Location: '.$prev_page.'&result=successInvigilUpd');
        exit();
    }   
    else{
        header('Location: ../index.php');
        exit();
    }