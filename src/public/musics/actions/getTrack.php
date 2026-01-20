<?php
include_once "../../includes/config.php";
$pdo = new PDO("mysql:host=".config::$HOST.";dbname=".config::$DBNAME, config::$USER, config::$PASSWORD);

$id = (int)$_GET['id'];

$req = $pdo->prepare("
    SELECT tracks.file, tracks.title, tracks.img, artists.name
    FROM tracks
    LEFT JOIN artist__track ON artist__track.track_id = tracks.id
    LEFT JOIN artists ON artists.id = artist__track.artist_id
    WHERE tracks.id = :id
    LIMIT 1
");
$req->execute(['id' => $id]);

$track = $req->fetch(PDO::FETCH_ASSOC);

if (!$track) {
    echo json_encode(null);
    exit;
}

echo json_encode([
    "src" => "../downloads/musics/".$track["file"],
    "title" => $track["title"],
    "artist" => $track["name"],
    "img" => $track["img"]
]);
