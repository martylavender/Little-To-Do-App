<?php

session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
}

/**Include our MySQL connection.*/
require 'database.php';
include 'lib/functions.php';


$listID = $_GET['listID'];
$parentID = $_GET['parentID'];

/* try submitting a new list item when the user submits the form */
try {
    if(isset($_POST['newListItem'])) {

        /* setup our variables to use in our query and form */
        $createdOn = date("Y-m-d H:i:s");
        $listTitle = $_POST['listTitle'];
        $createdBy = $_SESSION['user_id'];
        $listStatus = $_POST['listStatus'];
        $var = 1;

        /* prepare the insert query */
        $insert = "INSERT INTO subtasks (createdOn, listTitle, createdBy, listStatus, parentID, isChild) 
                   VALUES (:createdOn, :listTitle, :createdBy, :listStatus, :parentID, :var)";
        $insert = $pdo->prepare($insert);
        /* bind our variables */
        $insert->bindParam(':createdOn', $createdOn);
        $insert->bindParam(':listTitle',$listTitle);
        $insert->bindParam(':createdBy',$_SESSION['user_id']);
        $insert->bindParam(':parentID',$parentID);
        $insert->bindParam(':listStatus',$listStatus);
        $insert->bindParam(':var',$var);
        /* execute the query */
        $insert->execute();
    }
    /* catch any exceptions that may occur */
} catch(PDOException $e) {
    echo "error:".$e->getMessage();
}

$sql = "SELECT count(accountType) FROM users WHERE userID = :userID AND accountType = 1";
$result = $pdo->prepare($sql);
$result->bindParam(':userID',$_SESSION['user_id']);
$result->execute();
$number_of_rows = $result->fetchColumn();

if($number_of_rows == 1) {
    $isAdmin = '<a href="adminpanel.php">Admin</a>
                <li class="divider"></li>';
} else {
    $isAdmin = '';
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
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="" style="margin-top: 8px; margin-right: 3px;"><img src="<?php echo $imageName; ?>" height="33px" width="37px" style="border-radius: 5px; border: solid 1px #c9c9c9"/></li>
                <li class="dropdown">
                    <button class="btn btn-default dropdown-toggle navbar-btn" value="Hi" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="glyphicon glyphicon-cog"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><?php echo $isAdmin ?></li>
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
    <h1 style="font-family: 'Raleway', sans-serif; font-weight: 300; font-size: 48px;">
    <?php
        $parentID = $_GET['parentID'];

        $select = "SELECT listID, listTitle
                   FROM userlists
                   WHERE listID = :parentID";
        $select = $pdo->prepare($select);
        $select->bindParam(':parentID',$_GET['parentID']);
        $select->execute();

        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $parentTitle = $row['listTitle'];
            echo $parentTitle;
        }
    ?>
    </h1>
    <h3 style="font-family: 'Raleway', sans-serif; font-weight: 300;">
        <?php

        /* prepare the count query */
        $select = "SELECT createdBy FROM subtasks WHERE createdBy = :createdBy";
        $select = $pdo->prepare($select);
        /* execute the query */
        $select->bindParam(':createdBy',$_SESSION['user_id']);
        $select->execute();
        /* count the rows that match the query */
        if ($no = $select = $select->rowCount()) {
            /* echo the results for the user */
            echo 'You currently have ' .$no. ' subtasks.';
        } else {
            echo 'You have no lists. Why not create one?';
        }
        ?>
    </h3>

    <div class="breadcrumb"><a href="loggedin.php">Go Back</a></div>
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
                    <input type="submit" id="newListItem" name="newListItem" class="btn btn-info pull-right" value="Add List Item">
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

        $var = 1;
        $listID = $_GET['listID'];

        $select = "SELECT subtasks.createdOn, subtasks.subtask_id, subtasks.listTitle, subtasks.createdBy, subtasks.listStatus, subtasks.createdOn
                   FROM subtasks
                   INNER JOIN userlists 
                   ON subtasks.parentID = userlists.listID
                   WHERE subtasks.isChild = :var
                   AND subtasks.parentID = :listID";
        $select = $pdo->prepare($select);
        $select->bindParam(':var',$var);
        $select->bindParam(':listID',$listID);
        $select->execute();

        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {

            $listID = $row['subtask_id'];
            $listTitle = $row['listTitle'];

            echo "<form action=\"deleteitem.php\" method=\"post\">";
            echo "<tr>";
            echo "<td>".$createdOn = $row['createdOn']."</td>";
            echo "<td>".$listTitle = $row['listTitle']. "</td>";
            echo "<td>".$listStatus = $row['listStatus']."</td>";
            echo "<td><a class=\"btn btn-warning\" href='edititem.php?edit_id=$listID'";
            echo "</a><i class=\"fa fa-pencil-square-o\"></i>";
            echo "<a>";
            echo " ";
            echo "<a href='deleteitem.php?listID=$listID&parentID=$parentID'>";
            echo "<input=\"submit\" class=\"btn btn-danger\" value=name=\"deleteItem\" id=\"deleteItem\"/>";
            echo "<i class=\"fa fa-trash-o\"></i></a>";
            echo "<a></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        ?>

        </tbody>
        </form>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteConfirm" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete list?</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this list?<br/>
                    <span style="color: red;"<strong>Warning!</strong> Once this is done, it cannot be undone!</span></p>
            </div>
            <div class="modal-footer">
                <div>
                    <a class="btn btn-warning" value="Cancel" class="close" data-dismiss="modal">Cancel</a>
                    <?php
                    echo "<a class=\"btn btn-danger\" href='deleteitem.php?del_id=$listID'>Delete</a>";
                    ?>
                </div>
            </div>

        </div>
    </div>

    <?php
    apc_fetch('parentID')
    ?>
</body>
</html>
