<?php

// database Connection variables
define('HOST', 'localhost'); // Database host name ex. localhost
define('USER', 'littletodoapp'); // Database user. ex. root ( if your on local server)
define('PASSWORD', 'Jnm21791480@@'); // Database user password  (if password is not set for user then keep it empty )
define('DATABASE', 'littletodoapp'); // Database Database name

$pdo = new PDO('mysql:host='.HOST.';dbname='.DATABASE.'', USER, PASSWORD);
return $pdo;

?>
