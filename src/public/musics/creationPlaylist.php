<?php include './header.php';

$_SESSION['token'] = bin2hex(random_bytes(32));

?>

<main>
    <article class="container">
        <form action="../actions/createPlaylist.php" method="post">
            <h2>Création d'une playlist</h2>
            <div>
                <label>Nom :</label>
                <input type="text" name="name">

                <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>">

                <input type="submit" value="Submit" class="btn">
                <a href="./index.php" class="btn">Retour</a>
            </div>
        </form>
    </article>
</main>
