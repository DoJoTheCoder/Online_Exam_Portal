<?php
    // Simplexlsx library for processing excel file
    // From https://github.com/shuchkin/simplexlsx/
    require_once "https://raw.githubusercontent.com/shuchkin/simplexlsx/master/src/SimpleXLSX.php";
    require_once "../includes/functions.inc.php";
    require_once "../../includes/database.inc.php";
    if(isset($_POST['submit']) && isset($_FILES['excelFile'])){
       
        $quizid = $_POST['quizid'];
        $prev_page = "../question.php?quizid=".$quizid;
        $fileExt = strtolower(pathinfo($_FILES["excelFile"]["name"],PATHINFO_EXTENSION));

        $target_file = "../uploads/". pathinfo($_FILES["excelFile"]["tmp_name"])['filename'] . ".".$fileExt;

        if (is_uploaded_file($_FILES["excelFile"]["tmp_name"]) === false) {
            header("Location: ".$prev_page."&error=emptyfile");
            exit();
        }
        
        if($fileExt != "xlsx"){
            header("Location: ".$prev_page."&error=invalidExtension");
            exit();
        }
        
        if (move_uploaded_file($_FILES["excelFile"]["tmp_name"], $target_file) === false) {
            header("Location: ".$prev_page."&error=failed");
            exit();
        }

        // get data from excel file using simpleXLSX class
        $xlsx = SimpleXLSX::parse($target_file);
        unlink($target_file);
        if ($xlsx === false) {
            header("Location: ".$prev_page."&error=failed");
            exit();
        }

        $result = $xlsx->rows();

        //check if there are maximum of 50 questions
        if(count($result)>51){
            header("Location: ".$prev_page."&error=maxQuestionLimit");
            exit();
        }
        
        for($i=1; $i<count($result); ++$i){
            
            // check if there are only 7 columns in use
            if(count($result[$i])!=7){
                header("Location: ".$prev_page."&error=invalidXlsxFormat");
                exit();
            }
            
            // check for blank cells in any row
            foreach($result[$i] as $cell){
                if(empty($cell)){
                    header("Location: ".$prev_page."&error=emptyXlsxCell");
                    exit();
                }
            }
            
            // check if questions have the right format
            // if option number is invalid
            if(!preg_match("/^[1-4]$/", $result[$i][6])){
                header("Location: ".$prev_page."&error=invalidans");
                exit();
            }
        }

        // delete current questions in the quiz
        $sql = "DELETE FROM question_answer WHERE quizid='".$quizid."';";
        mysqli_query($conn, $sql);

        // delete quiz tests taken by students
        $sql = "DELETE FROM tests WHERE quizid =".$quizid.";";
        if(mysqli_query($conn, $sql) === false){
            header("Location: ".$prev_page."&error=failed");
            exit();
        }

        // question addition from excel
        for($i=1; $i<count($result); ++$i){
            $ques_name = $result[$i][1];
            $opt1 = $result[$i][2];
            $opt2 = $result[$i][3];
            $opt3 = $result[$i][4];
            $opt4 = $result[$i][5];
            $opt_ans = $result[$i][6];

            // add question
            $sql = "INSERT INTO question_answer (quizid, question, opt1, opt2, opt3, opt4, answer) VALUES (?,?,?,?,?,?,?);";
            
            $stmt = prep_sql_execute($conn, $sql, array($quizid, $ques_name, $opt1, $opt2, $opt3, $opt4, $opt_ans), "isssssi");
            if($stmt === false){
                header("location: ".$prev_page."&error=failed");
                exit();
            }
            mysqli_stmt_close($stmt);

        }

        header("Location: ".$prev_page."&result=successUpd");
        exit();
    }
    else{
        header("Location: ../index.php");
        exit();
    }

