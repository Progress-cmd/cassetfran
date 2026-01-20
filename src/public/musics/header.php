<?php
session_start();

// Vérification si l'utilisateur est bien connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>cassetfran</title>
        <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap">
</head>
<body>
<header class="header-musics">
    <a href="./index.php" class="btn home-btn">
        <span class="material-symbols-outlined">home</span>
        <span class="label">Home</span>
    </a>
    <a href="#" class="btn share-btn">
        <span class="material-symbols-outlined">partner_heart</span>
        <span class="label">Share</span>
    </a>
    <form action="./includes/rechercher.php" method="post" class="form search-form">
        <input type="text" class="search-entry" placeholder="Search">
        <button type="submit" class="btn search-btn"><span class="material-symbols-outlined">search</span></button>
    </form>
    <a href="./includes/ajoutMusic.php?etape=0" class="btn add-btn">
        <span class="material-symbols-outlined">music_note_add</span>
        <span class="label">Add music</span>
    </a>
    <a href="../includes/connexion.php" class="btn login-btn">
        <span class="material-symbols-outlined">account_circle</span>
        <span class="label">Account</span>
    </a>
</header>