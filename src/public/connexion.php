<?php
session_start();
include './header.php';

$_SESSION['token'] = bin2hex(random_bytes(32));

?>

<main>
    <div class="container-connexion">
        <h2>Connection :</h2>
        <form action="./actions/connection.php" method="POST">
            <label>Email :</label>
            <input type="email" name="email" required>

            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>">
            <div class="button-connexion">
                <button class="btn" type="submit">Connexion</button>
                <a href="./index.php" class="btn">Accueil</a>
            </div>
        </form>
        <form action="./actions/logout.php" method="POST" class="button-connexion">
            <button class="btn" type="submit">Déconnexion</button>
        </form>
    </div>
</main>

<?php include './footer.php'?>