<!-- Contains login page content -->
<?php
    include_once "includes/header.inc.php";
?>
<h2>Enter your log-in details</h2>
<form action="phpScript/login.ps.php" method="post" autocomplete="off">
    <input class="form-control"type="text" name="uid" placeholder="Username">    
    <input class="form-control"type="password" name="pwd" placeholder="Password">
    <button class="btn btn-primary" type="submit" name="submit">Log in</button>  
</form>

<?php
    // Login error message check
    if(isset($_GET["error"])){
        if($_GET["error"] == "inputempty"){
            echo '<div class="alert alert-warning">Please fill in all fields!</div>';
        }
        else if($_GET["error"] == "invalidLogin"){
            echo '<div class="alert alert-warning">Invalid log in details!</div>';
        }
        else if($_GET["error"] == "failed"){
            echo '<div class="alert alert-warning">Something went wrong, please try again later.</div>';
        }
    }
?>
<?php
    include_once "../includes/footer.inc.php";
?>
