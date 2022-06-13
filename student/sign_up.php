<?php
    include_once "includes/header.inc.php";
?>
<div>
    <h2>Welcome New User!</h2>
    <p>Enter your details</p>
    
    <form action="phpScript/sign_up.ps.php" method="post" autocomplete="off">
        <input class="form-control"type="text" name="user_name" placeholder="User name">    
        <input class="form-control"type="text" name="name" placeholder="Full name">
        <input class="form-control"type="text" name="email" placeholder="Email">
        <input class="form-control"type="tel" name="phone" placeholder="Phone no">
        <input class="form-control"type="password" name="pwd" placeholder="Password">
        <input class="form-control"type="password" name="confirm_pwd" placeholder="Confirm password">
        <button class="btn btn-primary" type="submit" name="submit">Sign up</button>  
    </form>
</div>

<?php
    // Checking for Error after sign up

    if(isset($_GET["error"])){
        echo '<div class="alert alert-warning">';

        if($_GET["error"] == "inputempty")
            echo 'Please fill in all fields!';
        else if($_GET["error"] == "invalidusername")
            echo 'Username is invalid! Use letters and numbers only.';
        else if($_GET["error"] == "usernameExists")
            echo 'Username or Email already exists!';
        else if($_GET["error"] == "invalidName")
            echo 'Name is invalid! Use only letters.';
        else if($_GET["error"] == "invalidEmail")
            echo 'Email is invalid!';
        else if($_GET["error"] == "invalidPhone")
            echo 'Phone number is invalid! Use numbers only.';
        else if($_GET["error"] == "passwordnotmatch")
            echo 'Passwords don\'t match!';
        else if($_GET["error"] == "invalidPassword")
            echo 'Password is invalid!';
        else if($_GET["error"] == "failed")
            echo 'Failed to sign up, try again later.';
        
        echo '</div>';
    }
    if(isset($_GET["success"]) && $_GET["success"] == "signupSuccess"){
        echo '<div class="alert alert-success">Sign up was successful!</div>';
    }
?>
<?php
    include_once "../includes/footer.inc.php";
?>
