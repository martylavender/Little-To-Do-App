<?php
session_start();

if (!isset($_SESSION['user_id']))
{
    header("Location: index.php");
}

/**Include our MySQL connection.*/
require 'database.php';

$listID = $_GET['listID'];
$parentID = $_GET['parentID'];

try
{
    $delete = "DELETE FROM subtasks 
                   WHERE subtask_id = :listID 
                   AND parentID = :parentID";
    $delete = $pdo->prepare($delete);
    $delete->bindParam(':listID', $listID);
    $delete->bindParam(':parentID', $parentID);
    $delete->execute();

    $listID = $parentID;

    header("location:list.php?listID=$listID&parentID=$parentID");
    echo 'The delete happened';
    /* catch any exceptions that may occur */
}
catch(PDOException $e)
{
    echo "error:" . $e->getMessage();
}
?>
