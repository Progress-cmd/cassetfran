<?php

class Config
{
    public static string $HOST;
    public static string $USER;
    public static string $PASSWORD;
    public static string $DBNAME;
}

Config::$HOST = getenv('DB_HOST');
Config::$USER = getenv('DB_USER');
Config::$PASSWORD = getenv('DB_PASS');
Config::$DBNAME = getenv('DB_NAME');

try
{
    $pdo = new PDO(
        "mysql:host=".Config::$HOST.";dbname=".Config::$DBNAME.";charset=utf8mb4",
        Config::$USER,
        Config::$PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
}
catch (PDOException $e)
{
    error_log($e->getMessage());
    die("Erreur de connexion à la base de données.");
}