<?php

//lists.php

/*tart the session.*/
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
}

/**Include our MySQL connection.*/
require 'database.php';

/* try submitting a new list item when the user submits the form */
try{
    if(isset($_POST['newItem'])) {

        /* setup our variables to use in our query and form */
        $createdOn = date("Y-m-d H:i:s");
        $listTitle = $_POST['listTitle'];
        $createdBy = $_SESSION['user_id'];
        $listStatus = $_POST['listStatus'];

        /* prepare the insert query */
        $insert = $pdo->prepare("INSERT INTO userlists (createdOn, listTitle, createdBy, listStatus) VALUES (:createdOn, :listTitle, :createdBy, :listStatus)");
        /* bind our variables */
        $insert->bindParam(':createdOn', $createdOn);
        $insert->bindParam(':listTitle',$listTitle);
        $insert->bindParam(':createdBy',$_SESSION['user_id']);
        $insert->bindParam(':listStatus',$listStatus);
        /* execute the query */
        $insert->execute();
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

    <link rel="stylesheet" href="css/flatui.css">
    <link rel="stylesheet" href="css/site.css">
    <link rel="stylesheet" href="css/styles.css">
    

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,700" rel="stylesheet">
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>




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
                <li class="" style="margin-top: 8px; margin-right: 3px;"><img src="<?php echo $imageName; ?>" height="33px" width="37px" style="border-radius: 5px; border: solid 1px #c9c9c9"/></li>
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
    <h1 style="font-family: 'Raleway', sans-serif; font-weight: 300; font-size: 48px;">Welcome back,
        <?php

        $result = $pdo->prepare("SELECT firstName FROM users WHERE userID = '" . $_SESSION['user_id'] . "'");
        $result->execute();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $firstName = $row['firstName'];
            echo $firstName = $row['firstName'];
        }
        ?>

    </h1>
    <h3 style="font-family: 'Raleway', sans-serif; font-weight: 300;">
        <?php

        /* prepare the count query */
        $select = "SELECT createdBy FROM userlists WHERE createdBy = :createdBy";
        $select = $pdo->prepare($select);
        /* execute the query */
        $select->bindParam(':createdBy',$_SESSION['user_id']);
        $select->execute();
        /* count the rows that match the query */
        if ($no = $select = $select->rowCount()) {
            /* echo the results for the user */
            echo 'You currently have ' .$no. ' lists.';
        } else {
            echo 'You have no lists. Why not create one?';
        }
        ?>
    </h3>
</div>



<div class="form-group" style="margin-top: 20px;">
    <form class="form-horizontal" action="" method="post">
        <fieldset>
            <!-- List Description-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="listDescription"></label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Title / Description</span>
                        <input id="listTitle" name="listTitle" class="form-control" placeholder="Give your list a name" type="text" required="">
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
                            <option value="" disabled selected>Choose a status for your list</option>
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
                    <button type="submit" id="newItem" name="newItem" class="btn btn-info pull-right" value="Create List">Create New List</button>

                </div>
            </div>
        </fieldset>
    </form>
</div>

<div class="container">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>
                Created On
            </th>
            <th>
                Title / Description
            </th>
            <th>
                Status
            </th>
            <th>
                Edit - Delete
            </th>
        </tr>
        </thead>
        <tbody>
        <?php

        $select = "SELECT * 
                   FROM userlists 
                   WHERE createdBy = :createdBy";
        $select = $pdo->prepare($select);
        $select->bindParam(':createdBy',$_SESSION['user_id']);
        $select->execute();

        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {

            $listID = $row['listID'];
            $parentID = $row['listID'];

            echo "<tr>";
            echo "<td>".$createdOn = $row['createdOn']."</td>";
            echo "<td><a href='list.php?listID=$listID&parentID=$parentID'>".$listTitle = $row['listTitle']."</a></td>";
            echo "<td>".$listStatus = $row['listStatus']."</td>";
            echo "<td><a class=\"btn btn-warning\" href='edit.php?edit_id=$listID'";
            echo "</a><i class=\"fa fa-pencil-square-o\"></i>";
            echo "<a>";
            echo " ";
            echo "<a class=\"btn btn-danger\" href='delete.php?del_id=$listID'\"";
            echo "</a><i class=\"fa fa-trash-o\"></i>";
            echo "<a></td>";
            echo "</tr>";
        }
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        ?>
</body>
</html>
