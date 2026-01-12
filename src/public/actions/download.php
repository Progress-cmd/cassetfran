<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['url'])) {
    http_response_code(400);
    exit('Requête invalide');
}

$url = escapeshellarg($_POST['url']);
$downloadDir = '/var/www/html/public/downloads';  // chemin absolu dans le conteneur

$cmd = "yt-dlp -x --audio-format mp3 --audio-quality 0 -o \"$downloadDir/%(title)s.%(ext)s\" $url 2>&1";

exec($cmd, $output, $code);

if ($code !== 0) {
    http_response_code(500);
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    exit;
}

echo "Téléchargement terminé";
