<?php
session_start();

if (
    !isset($_POST['token'], $_SESSION['token']) ||
    $_POST['token'] !== $_SESSION['token']
) {
    die('Token invalide');
}

$name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);

// Connexion à la base de données
include_once "../config.php";
$pdo = new PDO("mysql:host=".config::$HOST.";dbname=".config::$DBNAME, config::$USER, config::$PASSWORD);

$req = $pdo->prepare("INSERT INTO playlists (name, created_by_id) VALUES (:name, :user)");
$req->bindParam(':name', $name);
$req->bindParam(':user', $_SESSION['user']['id']);
$req->execute();

header('Location: ../musics/index.php');