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
            $req = $pdo->prepare("SELECT img, title, name FROM tracks LEFT JOIN artist__track ON artist__track.track_id = tracks.id LEFT JOIN artists ON artists.id = artist__track.artist_id WHERE tracks.id = :track");
            $req->bindParam(":track", $listTrack);
            $req->execute();

            $track = $req->fetchAll();

            echo '<div><button class="proposition"><img src='.$track[0]["img"].' alt="image" class="miniature"/><div><div class="titleTrack">'.$track[0]["title"].'</div><div class="nameTrack">'.$track[0]["name"].'</div></div></button></div>';
        }
        ?>
    </section>
    <div class="container-music">
        <section class="infos">
            <article class="playlists-infos">
                <div class="table">
                    <div class="row header">
                        <span>Playlists</span>
                        <a href="./creationPlaylist.php" class="btn">
                            <span class="material-symbols-outlined">add</span>
                        </a>
                    </div>
                    <div class="body">
                        <?php
                            $req = $pdo->prepare("SELECT playlists.id, name, username FROM playlists LEFT JOIN users ON playlists.`created-by_id` = users.id WHERE name != 'Wait Tracks'");
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
                                    <span class="img-playlists-infos material-symbols-outlined">
                                    <?php if ($playlist["name"] == "Favorite Tracks") { echo "favorite"; }
                                        else if ($playlist["name"] == "Instru Piano") { echo "piano"; } ?>
                                    </span>
                                    <span><?php echo $playlist["name"]; ?></span>
                                    <span><?php echo $playlist["username"]; ?></span>
                                    <span><?php if ($occurrence > 1) { echo $occurrence.' musiques'; } else { echo $occurrence.' musique'; } ?></span>
                                    <a href="#" class="btn"><span class="material-symbols-outlined">more_vert</span></a>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            </article>
            <div class="infos-bottom">
                <article class="artists-infos">
                    <div class="table">
                        <div class="row header">
                            <span>Artists</span>
                            <a href="#" class="btn">
                                <span class="material-symbols-outlined">add</span>
                            </a>
                        </div>
                        <div class="body">
                            <?php
                            $req = $pdo->prepare("SELECT name FROM artists");
                            $req->execute();

                            $artists = $req->fetchAll();

                            foreach ($artists as $artist)
                            {
                                ?>
                                <div class="row">
                                    <span><?php echo $artist["name"]; ?></span>
                                    <a href="#" class="btn"><span class="material-symbols-outlined">more_vert</span></a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </article>
                <article class="listened-infos">In My Blood</article>
            </div>
        </section>
        <section class="listen">
            <?php
//            $req = $pdo->prepare("SELECT * FROM tracks JOIN artist__track ON artist__track.track_id = tracks.id LEFT JOIN artists ON artists.id = artist__track.artist_id JOIN track__playlist ON tracks.id = track__playlist.track_id LEFT JOIN playlists ON playlists.id = track__playlist.playlist_id WHERE playlists.name = 'Wait Tracks' AND playlists.`created-by_id` = :id");
//            $req->bindParam(":id", $_SESSION['user']['id']);
            $req = $pdo->prepare("SELECT * FROM tracks JOIN artist__track ON artist__track.track_id = tracks.id LEFT JOIN artists ON artists.id = artist__track.artist_id ORDER BY RAND()");
            $req->execute();

            $tracks = $req->fetchAll();

            $playlist = [];

            foreach ($tracks as $track) {
                $playlist[] = [
                        "src" => "../downloads/musics/".$track["file"],
                        "title" => $track["title"],
                        "artist" => $track["name"],
                        "img" => $track["img"],
                        "duration" => $track["duration"]
                ];
            }
            ?>
            <script>
                const playlist = <?php echo json_encode($playlist, JSON_UNESCAPED_SLASHES); ?>;
            </script>

            <article class="info-listen">
                <img class="img-listen" src="" alt="image"/>
                <div class="name-listen"></div>
                <div class="artist-listen"></div>
                <audio class="audio-listen" id="audio" src=""></audio>
                <div class="audio-slide">
                    <div class="audio-progress" id="audioProgress">0</div>
                    <input type="range" id="progress" value="0" min="0" max="100">
                    <div class="audio-total"></div>
                </div>
            </article>
            <article class="button-listen">
                <div>
                    <button class="relisten-listen btn">
                        <span class="material-symbols-outlined">repeat</span>
    <!--                    <span class="material-symbols-outlined">repeat_one</span>-->
                    </button>
                    <button class="before-listen btn" id="previous">
                        <span class="material-symbols-outlined">skip_previous</span>
                    </button>
                    <button class="play-listen btn" id="play">
                        <span class="material-symbols-outlined" id="playIcon">play_arrow</span>
                    </button>
                    <button class="after-listen btn" id="next">
                        <span class="material-symbols-outlined">skip_next</span>
                    </button>
                    <button class="random-listen btn">
                        <span class="material-symbols-outlined">shuffle</span>
                    </button>
                </div>
                <div>
                    <button class="btn">
                        <!--            <span class="material-symbols-outlined">volume_off</span>-->
                        <span class="material-symbols-outlined">volume_up</span>
                    </button>
                    <button class="btn">
                        <!--            <span class="material-symbols-outlined">playlist_play</span>-->
                        <span class="material-symbols-outlined">queue_music</span>
                        <!--            <span class="material-symbols-outlined">playlist_remove</span>-->
                    </button>
                    <button class="btn">
                        <span class="material-symbols-outlined">more_vert</span>
                    </button>
                </div>
            </article>
            <script>
                document.addEventListener("DOMContentLoaded", () => {

                    const audio = document.getElementById("audio");
                    const progress = document.getElementById("progress");
                    const playBtn = document.getElementById("play");
                    const playIcon = document.getElementById("playIcon");
                    const audioTime = document.getElementById("audioProgress");
                    const audioStart = document.getElementById("previous");
                    const audioEnd = document.getElementById("next");

                    let currentIndex = 0;

                    function loadTrack(index) {
                        const track = playlist[index];

                        audio.src = track.src;
                        audio.load();

                        document.querySelector(".name-listen").textContent = track.title;
                        document.querySelector(".artist-listen").textContent = track.artist;
                        document.querySelector(".img-listen").src = track.img;
                        document.querySelector(".audio-total").textContent = track.duration + " secondes";

                        progress.value = 0;
                        audioTime.textContent = 0;

                        audio.play();
                        playIcon.textContent = "pause";
                    }

                    playBtn.addEventListener("click", () => {
                        if (audio.paused) {
                            audio.play();
                            playIcon.textContent = "pause";
                        } else {
                            audio.pause();
                            playIcon.textContent = "play_arrow";
                        }
                    });

                    audio.addEventListener("timeupdate", () => {
                        if (!audio.duration) return;
                        progress.value = (audio.currentTime / audio.duration) * 100;
                        audioTime.textContent = Math.floor(audio.currentTime);
                    });

                    progress.addEventListener("input", () => {
                        audio.currentTime = (progress.value / 100) * audio.duration;
                    });

                    audio.addEventListener("ended", () => {
                        currentIndex++;
                        if (currentIndex >= playlist.length) currentIndex = 0;
                        loadTrack(currentIndex);
                    });

                    audioStart.addEventListener("click", () => {
                        currentIndex--;
                        if (currentIndex < 0) currentIndex = playlist.length - 1;
                        loadTrack(currentIndex);
                    });

                    audioEnd.addEventListener("click", () => {
                        currentIndex++;
                        if (currentIndex >= playlist.length) currentIndex = 0;
                        loadTrack(currentIndex);
                    });

                    // chargement initial
                    if (playlist.length > 0) {
                        loadTrack(currentIndex);
                    }

                });
            </script>

        </section>
    </div>
</main>
<?php include './footer.php'; ?>