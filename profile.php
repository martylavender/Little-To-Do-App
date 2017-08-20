<?php

// Start Session
session_start();

// check user login
if(empty($_SESSION['user_id'])) {
    header("Location: index.php");
}

$message = '';
$updateSuccess = '';
$updateFailure = '';

require 'database.php';

        $result = $pdo->prepare("SELECT firstName, lastName, emailAddress, username FROM users WHERE userID = '" . $_SESSION['user_id'] . "'");
        $result->execute();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $firstName = $row['firstName'];
            $lastName = $row['lastName'];
            $emailAddress = $row['emailAddress'];
            $username = $row['username'];
        }

try {
    if(isset($_POST['updateUser'])) {

        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $userID = $_SESSION['user_id'];
        // query
        $sql = "UPDATE users SET firstName=:firstName, lastName=:lastName WHERE userID = :userID";
        $stmt = $pdo->prepare($sql);     
        $stmt->bindValue(":firstName", $firstName, PDO::PARAM_STR);                             
        $stmt->bindValue(":lastName", $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':userID',$_SESSION['user_id']);
        $result = $stmt->execute(); 
        
        if ($result) {
            //What you do here is up to you!
            $updateSuccess = "<div class=\"alert alert-success>\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>
                <strong>Success!</strong> Your account has been updated!</div>";
            } else {
                $updateFailure = "<div class=\"alert alert-success>\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>
                <strong>Balls!</strong> Something went wrong!</div>";
            }
    } 
/* catch any exceptions that may occur */
} catch(PDOException $e) {
    echo "error:".$e->getMessage();
}


if(isset($_POST["updatePhoto"])) {

    $folder = "uploads/";
    $upload_image = $folder . basename($_FILES["fileToUpload"]["name"]);

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $upload_image)) {

        $checkImage = "SELECT imageID FROM photos WHERE userID = :userID";
        $checkImage = $pdo->prepare($checkImage);
        $checkImage->bindParam(':userID',$_SESSION['user_id']);
        $checkImage->execute();

        $row = $checkImage->fetch(PDO::FETCH_ASSOC);

        if($checkImage->rowCount() > 0) {

            $checkImage = "UPDATE photos SET imageID = :uploadImage, userID = :userID WHERE userID = :userID";
            $checkImage = $pdo->prepare($checkImage);
            $checkImage->bindParam(':uploadImage',$upload_image);
            $checkImage->bindParam(':userID',$_SESSION['user_id']);
            $checkImage->execute();

        } else {

                $checkImage = "INSERT INTO photos (imageID, userID) VALUES (:uploadImage, :userID) WHERE userID = :userID";
                $checkImage = $pdo->prepare($checkImage);
                $checkImage->bindParam(':uploadImage',$upload_image);
                $checkImage->bindParam(':userID',$_SESSION['user_id']);
                $checkImage->execute();
        }
    }
}




?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/flatui.css">
    <link rel="stylesheet" href="css/site.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/debug.css">

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
        <a class="navbar-brand" href="loggedin.php">Little To Do App</a>
        </div>
        <?php

        $defaultImage = "uploads/default.png";

        $getImage = "SELECT * FROM photos WHERE userID = :userID";
        $getImage = $pdo->prepare($getImage);
        $getImage->bindParam(':userID',$_SESSION['user_id']);
        $getImage->execute();

        //to verify if a record is found
        $num = $getImage->rowCount();
        if( $num ){
            //if found
            $row = $getImage->fetch(PDO::FETCH_ASSOC);

            $imageName =  $row['imageID'];
          } else {
            $imageName = 'uploads/default.png';
        }

        ?>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="" style="margin-top: 15px; margin-right: 8px;">
                    <script>
                        var currentDate = new Date(),
                            day = currentDate.getDate(),
                            month = currentDate.getMonth() + 1,
                            year = currentDate.getFullYear();
                        document.write(month + "/" + day + "/" + year)
                    </script>
                    <script>

                        var currentTime = new Date(),
                            hours = currentTime.getHours(),
                            minutes = currentTime.getMinutes();

                        if (minutes < 10) {
                            minutes = "0" + minutes;
                        }

                        var suffix = "AM";
                        if (hours >= 12) {
                            suffix = "PM";
                            hours = hours - 12;
                        }
                        if (hours == 0) {
                            hours = 12;
                        }

                        document.write(hours + ":" + minutes + " " + suffix)
                    </script>
                </li>
                    <li class="" style="margin-top: 8px; margin-right: 3px;"><img src="<?php echo $imageName;?>" height="33px" width="37px" style="border-radius: 5px; border: solid 1px #c9c9c9"/></li>
                    <li class="dropdown">
                        <button class="btn btn-default dropdown-toggle navbar-btn" value="Hi" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span class="glyphicon glyphicon-cog"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="loggedin.php">Your Lists</a></li>
                            <li class="divider"></li>
                            <li><a href="profile.php">Profile</a></li>
                            <li class="divider"></li>
                            <li><a href="logout.php">Sign Out</a></li>
                        </ul>
                    </li>
                </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container">
    <h1 style="font-family: 'Raleway', sans-serif; font-weight: 300; margin-bottom: 75px; font-size: 48px;">Profile


    </h1>
    <div class="row">
        <!-- left column -->
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="text-center">
                <form action="" enctype="multipart/form-data" method="post">
                <img src="<?php echo $imageName; ?>" width="150px" height="150px" class="avatar img-circle img-thumbnail" alt="avatar">
                <h6>Upload a different photo...</h6>
                <div class="well well-sm">
                    <label class="btn btn-default btn-file" >
                        Browse <input type="file" name="fileToUpload" id="fileToUpload" style="display: none;">
                    </label>

                <input type="submit" class="btn btn-warning" name="updatePhoto" value="Upload Image"/>
                </div>
                </form>
            </div>

        </div>
        <!-- edit form column -->
        <div class="col-md-8 col-sm-6 col-xs-12 personal-info">
            <form class="form-horizontal" action="" method="post">
                <div class="form-group">
                    <label class="col-lg-3 control-label">First name:</label>
                    <div class="col-lg-8">
                        <input class="form-control" type="text" name="firstName" value="<?php echo $firstName ?>" placeholder=""/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Last name:</label>
                    <div class="col-lg-8">
                        <input class="form-control" type="text" name="lastName" value="<?php echo $lastName ?>" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-8">
                        <input class="btn btn-primary" name="updateUser" value="Save Changes" type="submit">
                        <span></span>
                        <input class="btn btn-default" value="Cancel" type="reset">
                    </div>
                </div>
                <div id="messages">
                    <div id="message" class="alert-success messsage-container" style="width: 450px; margin: 0 auto;"><?php if(isset($message)){ echo $updateSuccess; } ?></div>
                    <div id="message" class="alert-danger messsage-container" style="width: 450px; margin: 0 auto;"><?php if(isset($message)){ echo $updateFailure; } ?></div>
                </div>
            </form>
        </div>
    </div>
</div>



</body>
</html>
