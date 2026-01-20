<?php
session_start();
session_destroy();
header('Location: /public/includes/connexion.php');
exit;
