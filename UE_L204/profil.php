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

<!-- Header -->
<header class="top-bar">
        <a href="./index.php" class="about-btn">Accueil</a>
        <div class="profile-icon">
        <a href="./profil.php"><img src="./assets/images/profil.png" alt="Profil"></a>
        </div>
    </header>

<h1 class="title">VOTRE PROFIL</h1>

<div class="container">

    <!-- ----------- COLONNE GAUCHE : PROFIL ----------- -->
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

    <!-- ----------- COLONNE DROITE : RÉSERVATIONS ----------- -->
    <div class="reservations">

        <h2 class="subtitle">Vos réservations</h2>

        <!-- EN COURS -->
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

        <!-- PASSÉES -->
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
