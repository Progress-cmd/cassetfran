<?php include './mainHeader.php'; ?>

<main>
    <?php
    include_once "../includes/config.php";
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

            echo '<div><button class="proposition" data-track-id="'.$listTrack.'"><img src='.$track[0]["img"].' alt="image" class="miniature"/><div><div class="titleTrack">'.$track[0]["title"].'</div><div class="nameTrack">'.$track[0]["name"].'</div></div></button></div>';
        }
        ?>
    </section>
    <div class="container-music">
        <section class="infos">
            <article class="playlists-infos">
                <div class="table">
                    <div class="header">
                        <span>Playlists</span>
                        <a href="./includes/creationPlaylist.php" class="btn">
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
                        <div class="header">
                            <span>Artists</span>
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
                <article class="listened-infos">
                    <div class="table queue">
                        <div class="header">
                            <span>Liste d'attente</span>
                        </div>
                        <div class="body" id="queueList">
                        </div>
                    </div>
                </article>
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
                        "id" => $track["id"],
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
                <img class="img-listen" alt="image">
                <div class="name-listen"></div>
                <div class="artist-listen"></div>

                <audio id="audio"></audio>

                <div class="audio-slide">
                    <div class="audio-progress">0:00</div>
                    <input type="range" id="progress" value="0" min="0" max="100">
                    <div class="audio-total">0:00</div>
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
                    <button class="btn" id="volume">
                        <span class="material-symbols-outlined" id="volumeIcon">volume_up</span>
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
                    const propositions = document.querySelectorAll(".proposition");

                    const queueList = document.getElementById("queueList");

                    const audio = document.getElementById("audio");
                    const progress = document.getElementById("progress");
                    const playBtn = document.getElementById("play");
                    const playIcon = document.getElementById("playIcon");

                    const audioCurrent = document.querySelector(".audio-progress");
                    const audioTotal = document.querySelector(".audio-total");

                    const prevBtn = document.getElementById("previous");
                    const nextBtn = document.getElementById("next");

                    const titleEl = document.querySelector(".name-listen");
                    const artistEl = document.querySelector(".artist-listen");
                    const imgEl = document.querySelector(".img-listen");

                    const volumeBtn = document.getElementById("volume");
                    const volumeIcon = document.getElementById("volumeIcon");

                    let currentIndex = 0;

                    propositions.forEach(btn => {
                        btn.addEventListener("click", async () => {
                            const trackId = btn.dataset.trackId;

                            const response = await fetch(`../actions/getTrack.php?id=${trackId}`);
                            const track = await response.json();

                            if (!track || !track.src) return;

                            audio.src = track.src;
                            audio.play();

                            titleEl.textContent = track.title;
                            artistEl.textContent = track.artist;
                            imgEl.src = track.img;

                            playIcon.textContent = "pause";
                        });
                    });


                    function renderQueue() {
                        queueList.innerHTML = "";

                        playlist.forEach((track, index) => {
                            const row = document.createElement("div");
                            row.classList.add("row");
                            row.classList.add("row-btn");

                            row.innerHTML = `<span>${track.title}</span>
                                            <span>${track.artist}</span>
                                            <a href="#" class="btn"><span class="material-symbols-outlined">more_vert</span></a>`;

                            if (index === currentIndex) {
                                row.classList.add("active");
                            }

                            row.addEventListener("click", () => {
                                currentIndex = index;
                                loadTrack(currentIndex);
                                audio.play();
                                playIcon.textContent = "pause";
                                renderQueue();
                            });

                            queueList.appendChild(row);
                        });
                    }

                    document.querySelector(".queue .row.active")?.scrollIntoView({
                        behavior: "smooth",
                        block: "nearest"
                    });


                    function formatTime(seconds) {
                        const m = Math.floor(seconds / 60);
                        const s = Math.floor(seconds % 60).toString().padStart(2, "0");
                        return `${m}:${s}`;
                    }

                    function loadTrack(index) {
                        const track = playlist[index];

                        audio.src = track.src;
                        audio.load();

                        titleEl.textContent = track.title;
                        artistEl.textContent = track.artist;
                        imgEl.src = track.img;

                        progress.value = 0;
                        audioCurrent.textContent = "0:00";
                        audioTotal.textContent = "0:00";
                        playIcon.textContent = "play_arrow";
                        renderQueue();
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

                    audio.addEventListener("loadedmetadata", () => {
                        audioTotal.textContent = formatTime(audio.duration);
                    });

                    audio.addEventListener("timeupdate", () => {
                        if (!audio.duration) return;

                        progress.value = (audio.currentTime / audio.duration) * 100;
                        audioCurrent.textContent = formatTime(audio.currentTime);
                    });

                    progress.addEventListener("input", () => {
                        audio.currentTime = (progress.value / 100) * audio.duration;
                    });

                    audio.addEventListener("ended", () => {
                        nextTrack(true);
                    });

                    function nextTrack(autoPlay = false) {
                        currentIndex = (currentIndex + 1) % playlist.length;
                        loadTrack(currentIndex);
                        if (autoPlay) {
                            audio.play();
                            playIcon.textContent = "pause";
                        }
                    }

                    function prevTrack() {
                        if (audio.currentTime > 3) {
                            audio.currentTime = 0;
                            return;
                        }
                        currentIndex =
                            (currentIndex - 1 + playlist.length) % playlist.length;
                        loadTrack(currentIndex);
                    }

                    nextBtn.addEventListener("click", () => nextTrack(true));
                    prevBtn.addEventListener("click", prevTrack);

                    if (playlist.length) {
                        loadTrack(currentIndex);
                    }

                    volumeBtn.addEventListener("click", () => {
                        if (audio.muted) {
                            audio.muted = false;
                            volumeIcon.textContent = "volume_up";
                        } else {
                            audio.muted = true;
                            volumeIcon.textContent = "volume_off";
                        }
                    });

                    // --- GESTION DU LECTEUR RESPONSIVE ---
                    const listenSection = document.querySelector('.listen');
                    const listenButtons = document.querySelectorAll('.listen button');
                    const inputRanges = document.querySelectorAll('.listen input[type="range"]');

                    listenSection.addEventListener('click', (e) => {
                        // On vérifie si l'écran est petit (mode mobile)
                        if (window.innerWidth <= 768) {
                            // Si le lecteur est déjà ouvert, on le ferme si on clique en haut (sur l'image/titre)
                            // OU si le lecteur est fermé, on l'ouvre.

                            // Astuce : Si on clique sur un bouton (play/pause) ou la barre de progression, on ne veut PAS toggle l'écran
                            let isInteractiveElement = false;

                            // Vérifie si le clic vient d'un bouton ou d'un slider
                            listenButtons.forEach(btn => {
                                if(btn.contains(e.target)) isInteractiveElement = true;
                            });
                            inputRanges.forEach(input => {
                                if(input === e.target) isInteractiveElement = true;
                            });

                            if (!isInteractiveElement) {
                                listenSection.classList.toggle('expanded');

                                // Petit hack pour empêcher le scroll du body quand le lecteur est ouvert
                                if(listenSection.classList.contains('expanded')) {
                                    document.body.style.overflow = 'hidden';
                                } else {
                                    document.body.style.overflow = '';
                                }
                            }
                        }
                    });
                });
            </script>
        </section>
    </div>
</main>
<?php include './footer.php'; ?>