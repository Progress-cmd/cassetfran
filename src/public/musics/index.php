<?php include './header.php'; ?>

<main>
    <?php
    include_once "../config.php";
    $pdo = new PDO("mysql:host=".config::$HOST.";dbname=".config::$DBNAME, config::$USER, config::$PASSWORD);

    $req = $pdo->prepare("SELECT id FROM tracks ORDER BY RAND() LIMIT 8");
    $req->execute();

    $listTracks = $req->fetchAll(PDO::FETCH_COLUMN);
    ?>

    <section class="propositions">
        <?php
        foreach ($listTracks as $listTrack)
        {
            $req = $pdo->prepare("SELECT img_path, title, name FROM tracks LEFT JOIN artist__track ON artist__track.track_id = tracks.id LEFT JOIN artists ON artists.id = artist__track.artist_id WHERE tracks.id = :track");
            $req->bindParam(":track", $listTrack);
            $req->execute();

            $track = $req->fetchAll();
            $imgPath = str_replace('C:\\Users\\Haspot\\Documents\\01_Perso\\Codes\\Projets\\cassetfran\\data\\pictures\\', '',$track[0]["img_path"]);
            echo '<div><button class="btn proposition"><img src="../downloads/pictures/'.$imgPath.'" alt="image" class="miniature"/><div><div class="titleTrack">'.$track[0]["title"].'</div><div class="nameTrack">'.$track[0]["name"].'</div></div></button></div>';
        }
        ?>
    </section>
    <div class="container-music">
        <section class="infos">
            <article class="playlists-infos">
                <div class="table">
                    <div class="row header">
                        <span>Playlists</span>
                        <button class="btn">
                            <span class="material-symbols-outlined">add</span>
                        </button>
                    </div>
                    <?php
                        $req = $pdo->prepare("SELECT playlists.id, name, username FROM playlists LEFT JOIN users ON playlists.created_by_id = users.id");
                        $req->execute();

                        $playlists = $req->fetchAll();

                        foreach ($playlists as $playlist)
                        {
                            $req = $pdo->prepare("SELECT COUNT(*) FROM track__playlist WHERE playlist_id = :playlist");
                            $req->bindParam(":playlist", $playlist["id"]);
                            $req->execute();

                            $occurrence = $req->fetchColumn();
                            ?>
                            <div class="row">
                                <span class="img-playlists-infos material-symbols-outlined">favorite</span>
                                <span><?php echo $playlist["name"]; ?></span>
                                <span><?php echo $playlist["username"]; ?></span>
                                <span><?php if ($occurrence > 1) { echo $occurrence.' musiques';} else { echo $occurrence.' musique';} ?></span>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </article>
            <div class="infos-bottom">
                <article class="artists-infos">artists</article>
                <article class="listened-infos">In My Blood</article>
            </div>
        </section>
        <section class="listen">
            <article class="info-listen">
                <img class="img-listen" src="../downloads/pictures/In%20My%20Blood.jpg" alt="image"/>
                <div class="name-listen">In My Blood</div>
                <div class="artist-listen">The Score</div>
                <audio class="audio-listen" controls src="../downloads/musics/In%20My%20Blood%20-%20The%20Score.mp3"></audio>
            </article>
            <article class="button-listen">
                <div>
                    <button class="relisten-listen">
                        <span class="material-symbols-outlined">repeat</span>
    <!--                    <span class="material-symbols-outlined">repeat_one</span>-->
                    </button>
                    <button class="before-listen">
                        <span class="material-symbols-outlined">skip_previous</span>
                    </button>
                    <button class="play-listen">
                        <span class="material-symbols-outlined">play_arrow</span>
    <!--                    <span class="material-symbols-outlined">pause</span>-->
                    </button>
                    <button class="after-listen">
                        <span class="material-symbols-outlined">skip_next</span>
                    </button>
                    <button class="random-listen">
                        <span class="material-symbols-outlined">shuffle</span>
                    </button>
                </div>
                <div>
                    <button>
                        <!--            <span class="material-symbols-outlined">volume_off</span>-->
                        <span class="material-symbols-outlined">volume_up</span>
                    </button>
                    <button>
                        <!--            <span class="material-symbols-outlined">playlist_play</span>-->
                        <span class="material-symbols-outlined">queue_music</span>
                        <!--            <span class="material-symbols-outlined">playlist_remove</span>-->
                    </button>
                    <button>
                        <span class="material-symbols-outlined">more_vert</span>
                    </button>
                </div>
            </article>
        </section>
    </div>
</main>
<?php include './footer.php'; ?>