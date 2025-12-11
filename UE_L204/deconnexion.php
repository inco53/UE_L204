<?php
session_start();

// Détruire complètement la session
$_SESSION = [];
session_unset();
session_destroy();

// Rediriger vers la page de connexion
header("Location: connexion.php");
exit;
?>
