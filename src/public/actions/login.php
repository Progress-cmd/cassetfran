<?php
session_start();

if (
    !isset($_POST['token'], $_SESSION['token']) ||
    $_POST['token'] !== $_SESSION['token']
) {
    die('Token invalide');
}
// Récupération des données
$email = filter_input(INPUT_POST, 'email', FILTER_DEFAULT);
$password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);

// Connexion à la base de données
include_once "../includes/config.php";
$pdo = new PDO("mysql:host=".config::$HOST.";dbname=".Config::$DBNAME, Config::$USER, Config::$PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

$req = $pdo->prepare("SELECT id, username, email, `password-hash` FROM users WHERE email = :email");
$req->bindValue(':email', $email);
$req->execute();

$user = $req->fetch();

// Initialisation ou réinitialisation du mot de passe, à ne pas toucher
$initPassword = false;
if ($user != NULL && $initPassword)
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $req = $pdo->prepare("UPDATE users SET `password-hash` = :password WHERE id = :id");
    $req->bindParam(':password', $hashedPassword);
    $req->bindParam(':id', $user['id']);
    $req->execute();

    $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email']];
    header('Location: /index.php');
}

if ($user != NULL && password_verify($password, $user['password-hash']) && $user['password-hash'] != NULL)
{
    session_regenerate_id(true); // Permet de générer un nouvel id de session à chaque connection, limite les attaques

    $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email']];

    header('Location: /index.php');
}
else
{
    header("Location: /includes/connexion.php");
    echo "Identifiant ou mot de passe incorrect";
}