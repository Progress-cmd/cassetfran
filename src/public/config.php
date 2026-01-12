<?php

class Config
{
    public static $HOST;
    public static $USER;
    public static $PASSWORD;
    public static $DBNAME;
}

// Initialisation
Config::$HOST = getenv('DB_HOST');
Config::$USER = getenv('DB_USER');
Config::$PASSWORD = getenv('DB_PASS');
Config::$DBNAME = getenv('DB_NAME');

try
{
    $pdo = new PDO("mysql:host=" . config::$HOST . ";dbname=" . config::$DBNAME, config::$USER, config::$PASSWORD);
}
catch (PDOException $e)
{
    die("<b>DataBase :</b> ".$e->getMessage());
}