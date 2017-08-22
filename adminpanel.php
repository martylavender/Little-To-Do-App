<?php

//lists.php

/*tart the session.*/
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: loggedin.php");
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

//$type = 'Admin';
//
//$adminStatus = $pdo->prepare("SELECT accountType FROM users WHERE userID = :userID AND accountType = :accountType");
//$adminStatus->bindValue(':userID', $_SESSION['userID']);
//$adminStatus->bindParam(':accountType', $accountType);
//$adminStatus->execute();
//$result = $adminStatus->fetchObject();
//
//if ($result->total > 0) {
//    $accountType = '<a href="adminpanel.php">Admin</a>
//                    <li class="divider"></li>';
//} else {
//    $accountType =  '';
//}

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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>




    <style>
        body {
            overflow-x: hidden;
        }
        @import url("https://bootswatch.com/flatly/bootstrap.min.css");

        footer {
            padding-left: 15px;
            padding-right: 15px;
        }

        /*
         * Off Canvas
         * --------------------------------------------------
         */
        @media screen and (max-width: 768px) {
            .row-offcanvas {
                position: relative;
                -webkit-transition: all 0.25s ease-out;
                -moz-transition: all 0.25s ease-out;
                transition: all 0.25s ease-out;
                background:#ecf0f1;
            }

            .row-offcanvas-left
            .sidebar-offcanvas {
                left: -40%;
            }

            .row-offcanvas-left.active {
                left: 40%;
            }

            .sidebar-offcanvas {
                position: absolute;
                top: 0;
                width: 40%;
                margin-left: 12px;
            }
        }

        #sidebar {
            padding:15px;
            margin-top:10px;
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
                        <li><?php echo $isAdmin; ?></li>
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

<div class="container-fluid">
    <div class="row row-offcanvas row-offcanvas-left">
        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
            <div class="sidebar-nav">
                <ul class="nav">

                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="#">Users</a></li>
                    <li><a href="#">Lists</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="#">Mailbox</a></li>
                    <li><a href="#">Notifications</a></li>
                    <li class="nav-divider"></li>
                </ul>
            </div>
            <!--/.well -->
        </div>
        <!--/span-->

        <div class="col-xs-12 col-sm-9">
            <br>
            <div class="jumbotron">
                <a href="#" class="visible-xs" data-toggle="offcanvas"><i class="fa fa-lg fa-reorder"></i></a>
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
                        $select = "SELECT userID FROM users";
                        $select = $pdo->prepare($select);
                        /* execute the query */
                        $select->execute();
                        /* count the rows that match the query */
                        if ($no = $select->rowCount()) {
                            /* echo the results for the user */
                            echo 'There are currently ' .$no. ' users.';
                        } else {
                            echo 'There are no users';
                        }
                        ?>
                    </h3>
            </div>
        </div>
        <!--/span-->
<div class="form-group" style="margin-top: 20px;">
    <form class="form-horizontal" action="" method="post">
        <fieldset>
            <!-- List Description-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="listDescription"></label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">First Name&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        <input id="listTitle" name="listTitle" class="form-control" placeholder="First Name" type="text" required="">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="listDescription"></label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Last Name&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        <input id="listTitle" name="listTitle" class="form-control" placeholder="Last Name" type="text" required="">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="listDescription"></label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Email Address&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        <input id="listTitle" name="listTitle" class="form-control" placeholder="Email Address" type="text" required="">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="listDescription"></label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Password&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        <input id="listTitle" name="listTitle" class="form-control" placeholder="Password" type="text" required="">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label" for="listDescription"></label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Password Again&nbsp&nbsp&nbsp</span>
                        <input id="listTitle" name="listTitle" class="form-control" placeholder="One More Time" type="text" required="">
                    </div>
                </div>
            </div>
            <!-- List Status -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="listStatus"></label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Account Type&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        <select id="listStatus" name="listStatus" class="form-control">
                            <option value="" disabled selected>What kind of account is this?</option>
                            <option value="Open">User</option>
                            <option value="In Progress">Administrator</option>
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
                    <button type="submit" id="newItem" name="newItem" class="btn btn-info " value="Create List">Create New User</button>
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
                First Name
            </th>
            <th>
                Last Name
            </th>
            <th>
                Email Address
            </th>
            <th>
                Account Type
            </th>
            <th>
                Edit - Delete
            </th>
        </tr>
        </thead>
        <tbody>




        <?php

        $select = "SELECT * 
                   FROM users";
        $select = $pdo->prepare($select);
        $select->bindParam(':createdBy',$_SESSION['user_id']);
        $select->execute();

        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {

            $userID = $row['userID'];
            $firstName = $row['firstName'];
            $lastName = $row['lastName'];
            $emailAddress = $row['emailAddress'];
            $accountType = $row['accountType'];

            echo "<tr>";
            echo "<td>".$firstName."</td>";
            echo "<td>".$lastName."</td>";
            echo "<td>".$emailAddress."</td>";
            echo "<td>".$accountType."</td>";
            echo "<td><a class=\"btn btn-warning\" href='edit.php?edit_id=$userID'";
            echo "</a><i class=\"fa fa-pencil-square-o\"></i>";
            echo "<a>";
            echo " ";
            echo "<a class=\"btn btn-danger\" href='delete.php?del_id=$userID'\"";
            echo "</a><i class=\"fa fa-trash-o\"></i>";
            echo "<a></td>";

        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        ?>

</div>
        <!--/row-->

        <hr>

        <footer>
            <p>© Company 2013</p>
        </footer>

    </div>
    <!--/.container-->

    <script>
        $(document).ready(function() {
            $('[data-toggle=offcanvas]').click(function() {
                $('.row-offcanvas').toggleClass('active');
            });
        });
        </script>
</body>
</html>
