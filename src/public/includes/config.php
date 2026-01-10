<?php

class config
{
    const HOST = 'localhost';
    const USER = 'root';
    const PASSWORD = '';
    const DBNAME = 'cassetfran_bdd';
}

try
{
    $pdo = new PDO("mysql:host=" . config::HOST . ";dbname=" . config::DBNAME, config::USER, config::PASSWORD);
}
catch (PDOException $e)
{
    die("<b>DataBase :</b> ".$e->getMessage());
}