<?php
    define("host", "localhost");
    define("username", "root");
    define("password", "");
    define('dbname', 'mixes');

    $dsn="mysql:host=".host."; dbname=".dbname;
    try{
        $pdo= new PDO($dsn, username, password);
    }
    catch(Exception $e){
        echo ("Could not connect to the database because".$e->getMessage());
    }
    
?>