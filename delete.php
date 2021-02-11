<?php
session_start();

if (!isset($_SESSION['user_id']))
{
    header("Location: index.php");
}

/**Include our MySQL connection.*/
require 'database.php';

try
{
    $del_id = $_GET['del_id'];

    $delete = "DELETE userlists, subtasks FROM userlists INNER JOIN subtasks ON userlists.listID = subtasks.parentID WHERE listID = :del_id";
    $delete = $pdo->prepare($delete);
    $delete->bindParam(':del_id', $_GET['del_id']);
    $delete->execute();
    header("location:loggedin.php");
}
catch(PDOException $e)
{
    echo "error:" . $e->getMessage();
}

?>
