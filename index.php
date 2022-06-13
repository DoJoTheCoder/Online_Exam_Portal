<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content= "width=device-width, initial-scale = 1.0"/>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="styles/style.css">
        <link rel="icon" href="images/pencil.ico">
        
        <title>Exam portal</title>
    </head>

    <body class="d-flex flex-column h-100">
        <!-- navigation bar -home -->
        <main>        
            <div class="all-content">
                <br><br>                
                <h1 class="text-center">Choose your role to continue.</h1>
                <br><br>
                <div class="container">
                    <div class="floated first">
                        <a class="indexbutton" href="student/index.php"><h2>Student</h2></a>
                    </div>

                    <div class="floated">
                        <a class="indexbutton" href="admin/index.php"><h2>Admin</h2></a>
                    </div>
                </div>

<?php
    include_once "includes/footer.inc.php";
?>