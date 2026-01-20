<?php
session_start();

if (
    !isset($_POST['token'], $_SESSION['token']) ||
    $_POST['token'] !== $_SESSION['token']
) {
    die('Token invalide');
}

$title = filter_input(INPUT_POST, 'title', FILTER_DEFAULT);
$artist = filter_input(INPUT_POST, 'artist', FILTER_DEFAULT);
$duration = filter_input(INPUT_POST, 'duration', FILTER_DEFAULT);
$url = filter_input(INPUT_POST, 'url', FILTER_DEFAULT);
$miniature = filter_input(INPUT_POST, 'miniature', FILTER_DEFAULT);
$downloadDir = '/var/www/html/public/downloads/musics';  // chemin absolu dans le conteneur
$file_name = $title.'.mp3';
$file = $downloadDir.'/'.$file_name;
$cmd = "yt-dlp -f bestaudio -x --audio-format mp3 --audio-quality 0 -o \"$downloadDir/%(title)s.%(ext)s\" \"$url\"";

exec($cmd, $output, $code);

if ($code !== 0) {
    http_response_code(500);
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    exit;
}

// Connexion à la base de données
include_once "../../includes/config.php";
$pdo = new PDO("mysql:host=".config::$HOST.";dbname=".config::$DBNAME, config::$USER, config::$PASSWORD);

$req = $pdo->prepare("INSERT INTO tracks (title, duration, file, url, img, `added-by_id`) VALUES (:title, :duration, :file, :url, :img, :user)");
$req->bindParam(':title', $title);
$req->bindParam(':duration', $duration);
$req->bindParam(':file', $file);
$req->bindParam(':url', $url);
$req->bindParam(':img', $miniature);
$req->bindParam(':user', $_SESSION['user']['id']);
$req->execute();

$track_id = intval($pdo->lastInsertId());

$req = $pdo->prepare("SELECT id FROM artists WHERE name = :name");
$req->bindParam(':name', $artist);
$req->execute();

$artistData = $req->fetch(PDO::FETCH_ASSOC);

if ($artistData === false)
{
    $req = $pdo->prepare("INSERT INTO artists (name) VALUES (:name)");
    $req->bindParam(':name', $artist);
    $req->execute();

    $artist_id = intval($pdo->lastInsertId());
}
else
{
    $artist_id = intval($artistData["id"]);
}

$req = $pdo->prepare("INSERT INTO artist__track (artist_id, track_id) VALUES (:artist_id, :track_id)");
$req->bindParam(':artist_id', $artist_id);
$req->bindParam(':track_id', $track_id);
$req->execute();

header("Location: ../index.php");