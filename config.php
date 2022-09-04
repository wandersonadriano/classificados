<?php
session_start();

global $pdo;

try
{
    $pdo = new PDO("mysql:dbname=classificados;host=localhost", "developer", "");
}
catch(PDOException $error)
{
    echo "Falhou: ".$error->getMessage();
}