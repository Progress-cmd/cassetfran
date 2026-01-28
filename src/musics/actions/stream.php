<?php
session_start();

// 1. VERIFICATION DE SECURITE
// Remplace 'user_id' par ta variable de session qui prouve que l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Accès refusé. Veuillez vous connecter.");
}

// 2. RECUPERATION ET NETTOYAGE DU NOM DE FICHIER
$file = $_GET['file'] ?? '';

// SECURITE CRITIQUE : Empêcher l'attaquant de sortir du dossier avec des ../../
$file = basename($file);

$base_path = '/var/www/music_data/';
$full_path = $base_path . $file;

// 3. VERIFICATION DE L'EXISTENCE
if (empty($file) || !file_exists($full_path)) {
    header("HTTP/1.1 404 Not Found");
    exit("Fichier introuvable.");
}

// 4. ENVOI DU FICHIER AU NAVIGATEUR
// On définit les headers pour que le navigateur comprenne que c'est de l'audio
header('Content-Type: audio/mpeg');
header('Content-Length: ' . filesize($full_path));
header('Accept-Ranges: bytes');

// On lit le fichier et on l'envoie
readfile($full_path);
exit;