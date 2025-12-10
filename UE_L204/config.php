<?php
$dsn = "mysql:host=localhost;dbname=bricolage_uel204;charset=utf8";
$user = "root";
$pass = "root";

//création de la variable pdo pour connecter la base de donnée
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}
?>
