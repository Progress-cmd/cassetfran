<?php include './header.php';

$etape = filter_input(INPUT_GET, 'etape', FILTER_DEFAULT);

?>

<main>
    <?php if (!$etape)
        {
            $_SESSION['token'] = bin2hex(random_bytes(32));
            ?>
            <article class="container">
                <form action="ajoutMusic.php?etape=1" method="post">
                    <h2>Ajout d'une musique</h2>
                    <div>
                        <label>URL :</label>
                        <input type="url" name="url" placeholder="Lien YouTube Music" required>

                        <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>">

                        <div class="btns">
                            <button type="submit" class="btn">Voir</button>
                            <a href="../index.php" class="btn">Retour</a>
                        </div>
                    </div>
                </form>
            </article>
            <?php
        }
        else if ($etape)
        {
            if (
                    !isset($_POST['token'], $_SESSION['token']) ||
                    $_POST['token'] !== $_SESSION['token']
            ) {
                die('Token invalide');
            }

            $_SESSION['token'] = bin2hex(random_bytes(32));

            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['url']))
            {
                http_response_code(400);
                exit('Requête invalide');
            }

            $cmd = "yt-dlp --skip-download --no-playlist --dump-json ".escapeshellarg($_POST['url']);

            $json = shell_exec($cmd);
            $data = json_decode($json, true);

            $title = $data['track'] ?? null;
            $artist = $data['artist'] ?? null;
            $album = $data['album'] ?? null;
            $duration = $data['duration'] ?? 0;
            $thumb = $data['thumbnails'][count($data['thumbnails'])-1]['url'] ?? null;

            // Connexion à la base de données
            include_once "../../includes/config.php";
            $pdo = new PDO("mysql:host=".config::$HOST.";dbname=".Config::$NAME, Config::$USER, Config::$PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);

            $req = $pdo->prepare("SELECT title FROM tracks WHERE title = :title");
            $req->bindParam(':title', $title);
            $req->execute();

            if (!$req->fetch())
            {
                ?>
                <article class="container">
                    <form method="post" action="../actions/addMusic.php">
                        <h2>Est-ce bien celle-ci ?</h2>
                        <div>
                            <label>Title :</label>
                            <input type="text" value="<?php echo $title ?>" name="title" readonly>
                            <br>

                            <label>Artist :</label>
                            <input type="text" value="<?php echo $artist ?>" name="artist" readonly>
                            <br>

                            <label>Album :</label>
                            <input type="text" value="<?php echo $album ?>" name="album" readonly>
                            <br>

                            <label>Durée :</label>
                            <input type="number" value="<?php echo $duration ?>" name="duration" readonly>
                            <br>

                            <img src="<?php echo $thumb ?>" alt="image">
                            <input type="hidden" value="<?php echo $thumb ?>" name="miniature">

                            <input type="hidden" value="<?php echo $_POST['url'] ?>" name="url">
                            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

                            <div class="btns">
                                <input type="submit" class="btn" value="Download">
                                <a href="../index.php" class="btn">Retour</a>
                            </div>
                        </div>
                    </form>
                </article>
                <?php
            }
            else
            {
                echo "<i>".$title."</i> est déjà dans la base de donnée";
            }
        }
        ?>
</main>