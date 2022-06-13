<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content= "width=device-width, initial-scale = 1.0"/>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
        
        <link rel="icon" href="../images/pencil.ico">
        <link rel="stylesheet" href="../styles/style.css">
        
        <title>Exam portal - Administrator</title>
    </head>

    <body class="d-flex flex-column h-100">
        <!-- navigation bar - admin-->
        <header>
            <nav class="top-toolbar">
                <div>
                    <table cellpadding="5" cellspacing="0">
                        <tr>
                            <?php
                                if(isset($_SESSION['admin_id'])){
                                    ?>
                                    <td>
                                        <a class="navbar-brand navsel" href="../index.php"><img src="../images/logo.jpg" alt="Quizzer logo" height="50"></a>
                                        <a class="navsel" href="index.php"><i class="bi bi-house-door-fill"></i>&nbsp;Home</a>
                                        <a class="navsel" href="proctor.php"><i class="bi bi-camera-video-fill"></i>&nbsp;Proctoring</a>
                                        <a class="navsel" href="logout.php"><i class="bi bi-door-open-fill"></i>&nbsp;Log out</a>
                                    </td>
                                    <?php
                                }
                                else{
                                    ?>
                                    <td>
                                        <a class="navbar-brand navsel" href="../index.php"><img src="../images/logo.jpg" alt="Quizzer logo" height="50"></a>
                                        <a class="navsel" href="index.php"><i class="bi bi-house-door-fill"></i>&nbsp;Home</a>
                                        <a class="navsel" href="login.php"><i class="bi bi-person-fill"></i>&nbsp;Log In</a>
                                    </td>
                                    <?php
                                }
                            ?>
                        </tr>
                    </table>
                </div>
            </nav>
        </header>       
        <main>        
            <div class="all-content">
                <br>

