<?php
    include_once "includes/header.inc.php";
?>
<h1>Welcome Admin!</h1>
<?php
    if(isset($_SESSION['admin_id'])){  
        echo '<p>What would you like to work with?</p>
        <div>
            <ul>
                <li><a class="text-decoration-none" href="subject.php">Subjects</a></li>
                <li><a class="text-decoration-none" href="quiz.php">Quizes</a></li>
                <li><a class="text-decoration-none" href="question.php">Questions</a></li>
            </ul>
        </div>';
    }
    else {
        echo '<p>Log in to get access to features like adding and modifying subjects, quizes and test questions.</p>';
        echo '<p>You can also upload test questions stored in an excel file format.</p>';
    }
    if(isset($_GET['error']) && $_GET['error']=='failed'){
        echo '<div class="alert alert-warning">Could\'t process the request. Try again later.</div>';
    }
?>

<?php
    include_once "../includes/footer.inc.php";
?>

        