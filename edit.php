<?php

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
}

/**Include our MySQL connection.*/
require 'database.php';

try {

    $edit_id = $_GET['edit_id'];


    $select = $pdo -> prepare("SELECT * FROM userlists where listID = :edit_id ");
    $select->bindParam(':edit_id',$edit_id);
    $select->setFetchMode(PDO::FETCH_ASSOC);
    $select->execute();

    while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
        $listTitle = $row['listTitle'];
        $listStatus = $row['listStatus'];
    }

} catch (PDOException $e) {
    echo "error:".$e -> getMessage();
}

try {
    if(isset($_POST['submitChanges'])) {

        $listTitle= $_POST['listTitle'];
        $listStatus = $_POST['listStatus'];
        $edit_id = $_GET["edit_id"];

        $sql = "UPDATE userlists SET listTitle=:listTitle, listStatus=:listStatus WHERE listID=:edit_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":listTitle", $listTitle, PDO::PARAM_STR);
        $stmt->bindValue(":listStatus", $listStatus, PDO::PARAM_STR);
        $stmt->bindParam(':edit_id',$edit_id);
        $result = $stmt->execute();
        header("Location: loggedin.php");
    }
    /* catch any exceptions that may occur */
} catch(PDOException $e) {
    echo "error:".$e->getMessage();
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Little To Do App - Your Lists</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/flatui.css">
    <link rel="stylesheet" href="css/site.css">

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,700" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <style>
    body {
        overflow-x: hidden;
    }
    </style>
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

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <button class="btn btn-default dropdown-toggle navbar-btn" value="Hi" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="glyphicon glyphicon-cog"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php">Account Settings</a></li>
                        <li class="divider"></li>
                        <li><a href="loggedin.php">Lists</a></li>
                        <li class="divider"></li>
                        <li><a href="logouttest.php">Sign Out</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container">
    <h1 style="font-family: 'Raleway', sans-serif; font-weight: 300;">Edit your list!</h1>
</div>
<form class="form-horizontal" action="" method="post">
    <fieldset>
        <!-- List Description-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="listDescription"></label>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">Title / Description</span>
                    <input id="listTitle" name="listTitle" class="form-control" type="text" value="<?php echo $listTitle; ?>">
                </div>
            </div>
        </div>
        <!-- List Status -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="listStatus"></label>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">List Item Status&nbsp&nbsp&nbsp&nbsp</span>
                    <select id="listStatus" name="listStatus" class="form-control">
                        <option value="" disabled selected><?php echo $listStatus; ?></option>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Complete">Complete</option>
                    </select>
                </div>
                <input type="hidden" name="createdOn"/>
                <input type="hidden" name="createdBy" value="<?php $_SESSION['user_id'] ?>"/>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="btnAddList"></label>
            <div class="col-md-4">
                <button onclick="window.history.go(-1); return false;" class="btn btn-danger pull-right">Cancel</button>
                <span class="pull-right">&nbsp</span>
                <input type="submit" name="submitChanges" class="btn btn-info pull-right" value="Update List">
            </div>
        </div>
    </fieldset>
</form>

</body>
</html>
