<?php

//Start a session.
session_start();

require 'password.php';
require 'database.php';


//Set a few variables for handling error and success messages.
$message = '';
$createSuc = '';
$createFail = '';

// If the POST var is clicked and exists, take the information in the form and process it for submission.
if(isset($_POST['register'])){

    //Grab our form data, strip any invalid tags, and trim blank space before and after the user input.
    $username = !empty($_POST['username']) ? strip_tags(trim($_POST['username'])) : null;
    $firstName = !empty($_POST['firstName']) ? strip_tags(trim($_POST['firstName'])) : null;
    $lastName = !empty($_POST['lastName']) ? strip_tags(trim($_POST['lastName'])) : null;
    $emailAddress = !empty($_POST['emailAddress']) ? trim($_POST['emailAddress']) : null;
    $pass = !empty($_POST['password']) ? trim($_POST['password']) : null;

    //Sanitize the email input.
    $emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);

    //Define and prepare our SQL query
    $sql = "SELECT COUNT(emailAddress) AS num FROM users WHERE emailAddress = :emailAddress";
    $stmt = $pdo->prepare($sql);

    //Bind the provided email address to our prepared statement.
    $stmt->bindValue(':emailAddress', $emailAddress);

    //Execute our query
    $stmt->execute();

    //Fetch the row.
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    //If the provided username already exists - display error.
    if($row['num'] > 0){
        //Define the error message that will be displayed
        $createFail = "<div class=\"alert alert-danger fade in\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>
                            <strong>Oh Snap!</strong> That email is already taken!</div>";
    } else {

        //Hash the password as we do NOT want to store our passwords in plain text.
        $passwordHash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));

        //Prepare our INSERT statement.
        $sql = "INSERT INTO users (firstName, lastName, emailAddress, password) VALUES (:firstName, :lastName, :emailAddress, :password)";
        $stmt = $pdo->prepare($sql);

        //Bind our variables.
        $stmt->bindValue(':firstName', $firstName);
        $stmt->bindValue(':lastName', $lastName);
        $stmt->bindValue(':emailAddress', $emailAddress);
        $stmt->bindValue(':password', $passwordHash);

        //Execute the statement and insert the new account.
        $result = $stmt->execute();

        //If the signup process is successful.
        if ($result) {
            //Define the success message that will be displayed
            $createSuc = "<div class=\"alert alert-success>\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>
                <strong>Success!</strong> Your account has been created!</div>";
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
            <a class="navbar-brand" href="index.php">
                <img class="img-responsive" alt="Brand" src="img/ltda.png" height="25" width="25" style="margin-top: -2px">
            </a>
            <a class="navbar-brand" href="index.php">Little To Do App</a>
        </div>
    </div><!-- /.container-fluid -->
</nav>

<div class="container">
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-body">
                <form method="post" action="">
                    <div class="form-group">
                        <h2>Create your account</h2>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="firstName">First name</label>
                        <input id="firstName" name="firstName" type="text" maxlength="50" class="form-control" placeholder="First name">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="lastName">Last name</label>
                        <input id="lastName" name="lastName" type="text" maxlength="50" class="form-control" placeholder="Last name">
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
                        <label class="control-label" for="passwordVerify">Password again</label>
                        <input id="password" name="passwordVerify" type="password" maxlength="25" class="form-control" placeholder="One more time">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="register" class="btn btn-info btn-block" value="Create your account">
                    </div>
                    <p class="form-group">By creating an account, you agree to our <a href="#">Terms of Use</a> and our <a href="#">Privacy Policy</a>.</p>
                    <hr>
                    <p></p>Already have an account? <a href="index.php">Sign in</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="messages">
    <div id="message" class="alert-success">
        <!-- echo our success message -->
        <?php if(isset($message)){ echo $createSuc; } ?>
    </div>
    <div id="message" class="alert-danger">
        <!-- echo our failure message -->
        <?php if(isset($message)){ echo $createFail; } ?>
    </div>
</div>
</div>
</body>
</html>