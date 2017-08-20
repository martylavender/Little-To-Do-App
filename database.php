<?php

// database Connection variables
define('HOST', 'localhost'); // Database host name ex. localhost
define('USER', ''); // Database user. ex. root ( if your on local server)
define('PASSWORD', ''); // Database user password  (if password is not set for user then keep it empty )
define('DATABASE', ''); // Database Database name

$pdo = new PDO('mysql:host='.HOST.';dbname='.DATABASE.'', USER, PASSWORD);
return $pdo;

?>
