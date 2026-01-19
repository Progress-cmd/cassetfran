<?php include './header.php';

$recherche = filter_input(INPUT_POST, 'recherche', FILTER_DEFAULT);

include '../config.php';
$pdo = new PDO("mysql:host=".config::$HOST.";dbname=".config::$DBNAME, config::$USER, config::$PASSWORD);

$req = $pdo->prepare("SELECT * FROM tracks JOIN artist__track ON artist__track.track_id = tracks.id LEFT JOIN artists ON artists.id = artist__track.artist_id");
$req->execute();

$tracks = $req->fetchAll();
?>

<main>
    <section>
        <div class="table table-search" style="height: 100%;">
            <div class="row header">
                <span>Résultats :</span>
            </div>
            <div class="body">
                <?php foreach ($tracks as $track)
                    {

                       ?>
                        <div class="row">
                            <span><?php echo $track["title"] ?></span>
                            <span><?php echo $track["name"] ?></span>
                            <a href="#" class="btn"><span class="material-symbols-outlined">more_vert</span></a>
                        </div>
                        <?php
                    }
                    ?>
            </div>
        </div>
    </section>
</main>