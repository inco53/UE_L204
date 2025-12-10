<?php
session_start();
require_once "bdd.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupérer infos utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Vérification rôle admin
$isAdmin = ($user['role'] === 'admin');

// Suppression d’un utilisateur (si admin)
if ($isAdmin && isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);

    // Un admin ne peut pas se supprimer lui-même
    if ($deleteId == $_SESSION['user_id']) {
        $error = "Vous ne pouvez pas supprimer votre propre compte.";
    } else {
        $del = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $del->execute([$deleteId]);
        $success = "Le compte a bien été supprimé.";
    }
}

// Liste des utilisateurs si admin
if ($isAdmin) {
    $users = $pdo->query("SELECT * FROM utilisateurs ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Xanh+Mono:ital@0;1&display=swap" rel="stylesheet">
    <title>Votre Profil</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<!----------------- Header -------------------->
<header class="top-bar">
        <a href="./index.php" class="about-btn">Accueil</a>
        <div class="profile-icon">
        <a href="./profil.php"><img src="./assets/images/profil.png" alt="Profil"></a>
        </div>
    </header>

<h1 class="title">VOTRE PROFIL</h1>

<div class="container">

    <!------------------- Colonne de gauche : profil ------------------>
    <div class="profile-card">

        <div class="profile-img"></div>

        <div class="profile-info">
            <p class="label">Nom :</p>
            <p class="value">...</p>

            <p class="label">Mail :</p>
            <p class="value">...</p>

            <p class="label">Identifiant :</p>
            <p class="value">...</p>

            <p class="label">Mot de passe :</p>
            <p class="value">...</p>
        </div>

        <button class="btn-delete">Supprimer le compte</button>

        <button class="btn-logout">DÉCONNEXION</button>

    </div>

    <!-- -----------Colonne de droite : reservation----------- -->
    <div class="reservations">

        <h2 class="subtitle">Vos réservations</h2>

        <!--------en cours-------->
        <div class="box">
            <h3 class="section-title">EN COURS</h3>

            <div class="line">
                <span>NOM OUTILS</span><span>TYPE</span>
                <span>DATE/réservation</span><span>Compte à rebours</span>
            </div>

            <div class="line">
                <span>NOM OUTILS</span><span>TYPE</span>
                <span>DATE/réservation</span><span>Compte à rebours</span>
            </div>
        </div>

        <!--------passées------->
        <div class="box">
            <h3 class="section-title">PASSÉES</h3>

            <div class="line">
                <span>NOM OUTILS</span><span>TYPE</span>
                <span>DATE/réservation</span><span>A temps</span>
            </div>

            <div class="line">
                <span>NOM OUTILS</span><span>TYPE</span>
                <span>DATE/réservation</span><span>Retard</span>
            </div>

            <div class="line">
                <span>NOM OUTILS</span><span>TYPE</span>
                <span>DATE/réservation</span><span>A temps</span>
            </div>
        </div>

    </div>

</div>

</body>
</html>
