<?php
session_start();
session_destroy();
header('Location: ../includes/connexion.php');
exit;
