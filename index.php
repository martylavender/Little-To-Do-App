<?php

//login.php

/**
 * Start the session.
 */
session_start();

/**
 * Include ircmaxell's password_compat library.
 */
require 'password.php';

/**
 * Include our MySQL connection.
 */
require 'database.php';

$message = '';
$noUser = '';
$badUserPass = '';

//If the POST var "login" exists (our submit button), then we can
//assume that the user has submitted the login form.
if(isset($_POST['login'])){

    //Retrieve the field values from our login form.
    $emailAddress = !empty($_POST['emailAddress']) ? trim($_POST['emailAddress']) : null;
    $passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;

    //Retrieve the user account information for the given username.
    $sql = "SELECT userID, emailAddress, password FROM users WHERE emailAddress = :emailAddress";
    $stmt = $pdo->prepare($sql);

    //Bind value.
    $stmt->bindValue(':emailAddress', $emailAddress);

    //Execute.
    $stmt->execute();

    //Fetch row.
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //If $row is FALSE.
    if($user === false){
        //Could not find a user with that username!
        //PS: You might want to handle this error in a more user-friendly manner!
        $noUser = "<div class=\"alert alert-danger fade in\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>
                            <strong>Uh oh!</strong> That is not correct!</div>";
    } else{
        //User account found. Check to see if the given password matches the
        //password hash that we stored in our users table.

        //Compare the passwords.
        $validPassword = password_verify($passwordAttempt, $user['password']);

        //If $validPassword is TRUE, the login has been successful.
        if($validPassword){

            //Provide the user with a login session.
            $_SESSION['user_id'] = $user['userID'];
            $_SESSION['logged_in'] = time();

            //Redirect to our protected page, which we called home.php
            header('Location: loggedin.php');
            exit;

        } else {
            //$validPassword was FALSE. Passwords do not match.
            $badUserPass = "<div class=\"alert alert-danger fade in\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>
                            <strong>Uh oh!</strong> Your username and/or password is incorrect!</div>";
        }
    }
    if(!empty($_POST["remember"])) {
        setcookie ("username",$_POST["emailAddress"],time()+ (10 * 365 * 24 * 60 * 60));
        setcookie ("password",$_POST["password"],time()+ (10 * 365 * 24 * 60 * 60));
    } else {
        if(isset($_COOKIE["username"])) {
            setcookie ("username","");
        }
        if(isset($_COOKIE["password"])) {
            setcookie ("password","");
        }
    }

}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/flatui.css">
    <link rel="stylesheet" href="css/site.css">
    <link rel="stylesheet" href="css/styletest.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">
                <img class="img-responsive" alt="Brand" src="img/ltda.png" height="25" width="25" style="margin-top: -2px">
            </a>
            <a class="navbar-brand" href="#">Little To Do App</a>
        </div>
    </div><!-- /.container-fluid -->
</nav>
<div class="container">
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-body">
                <form method="post" action="">
                    <div class="form-group">
                        <h2>Log into your account</h2>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="emailAddress">Email Address</label>
                        <input id="emailAddress" name="emailAddress" type="email" maxlength="50" class="form-control" placeholder="Email address">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="password">Password</label>
                        <input id="password" name="password" type="password" maxlength="25" class="form-control" length="40" placeholder="Password" >
                    </div>
                    <div class="form-group">
                        <input type="submit" name="login" class="btn btn-info btn-block" value="Login">
                    </div>
                    <p class="form-group">By creating an account, you agree to our <a href="#">Terms of Use</a> and our <a href="#">Privacy Policy</a>.</p>
                    <input type="checkbox" name="remember" value="1">Remember Me
                    <hr>

                    <p>Need an account? <a href="register.php">Click here.</a><br/>
                        Forgot your password? <a href="#">Click here.</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="messages">
    <div id="message" class="alert-success"><?php if(isset($message)){ echo $noUser; } ?></div>
    <div id="message" class="alert-success"><?php if(isset($message)){ echo $badUserPass; } ?></div>
</div>

</body>
</html>