<?php
session_start();
require_once "config.php"; // doit définir $pdo (PDO connecté)

// Récupère l'id de l'outil demandé 
$outil_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($outil_id <= 0) {
    echo "Outil non spécifié.";
    exit;
}

// Récupère l'outil depuis la BDD 
$stmt = $pdo->prepare("SELECT * FROM outil WHERE id = :id");
$stmt->execute([':id' => $outil_id]);
$outil = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$outil) {
    echo "Outil introuvable.";
    exit;
}

// Messages pour l'UI
$errors = [];
$success = '';

// --- Traitement du formulaire de connexion ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $identifiant = isset($_POST['identifiant']) ? trim($_POST['identifiant']) : '';
    $motdepasse = isset($_POST['motdepasse']) ? trim($_POST['motdepasse']) : '';

    if ($identifiant === '' || $motdepasse === '') {
        $errors[] = "Veuillez renseigner identifiant et mot de passe.";
    } else {
        $stm = $pdo->prepare("SELECT * FROM utilisateurs WHERE identifiant = :identifiant LIMIT 1");
        $stm->execute([':identifiant' => $identifiant]);
        $user = $stm->fetch(PDO::FETCH_ASSOC);

        // Dans ton dump les mots de passe sont en clair; si tu comptes utiliser password_hash, remplace la vérif
        if ($user && $user['motdepasse'] === $motdepasse) {
            // Connexion OK
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['identifiant'] = $user['identifiant'];

            // redirection vers la même page (pour éviter repost du form)
            header("Location: reserve.php?id=" . $outil_id);
            exit;
        } else {
            $errors[] = "Identifiant ou mot de passe incorrect.";
        }
    }
}

// --- Traitement du formulaire de réservation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reserve') {
    // Vérifier si l'utilisateur est connecté
    if (empty($_SESSION['user_id'])) {
        $errors[] = "Vous devez être connecté pour réserver.";
    } else {
        // Récupère les champs
        $date = isset($_POST['date_resa']) ? trim($_POST['date_resa']) : '';
        $quantite_demande = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;
        if ($date === '') {
            $errors[] = "Veuillez sélectionner une date de réservation.";
        }
        if ($quantite_demande <= 0) {
            $errors[] = "Quantité invalide.";
        }

        if (empty($errors)) {
            // Calculer la quantité déjà réservée pour cet outil à cette date
            $stm = $pdo->prepare("
                SELECT SUM(quantite) AS total_reserve
                FROM reservation
                WHERE outil_id = :outil_id
                  AND :date BETWEEN date_debut AND date_fin
            ");
            $stm->execute([
                ':outil_id' => $outil_id,
                ':date' => $date
            ]);
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            $reservees = (int)($row['total_reserve'] ?? 0);

            $disponible = (int)$outil['quantite'] - $reservees;

            if ($quantite_demande > $disponible) {
                $errors[] = "Quantité indisponible pour la date choisie. Disponible : $disponible.";
            } else {
                // Insérer la réservation (ici réservation sur une journée : date_debut = date_fin = $date)
                $ins = $pdo->prepare("INSERT INTO reservation (utilisateur_id, outil_id, date_debut, date_fin, quantite) VALUES (:uid, :outil, :deb, :fin, :qty)");
                $ins->execute([
                    ':uid' => $_SESSION['user_id'],
                    ':outil' => $outil_id,
                    ':deb' => $date,
                    ':fin' => $date,
                    ':qty' => $quantite_demande
                ]);

                $success = "Réservation effectuée pour le " . htmlspecialchars($date) . " (quantité : $quantite_demande).";
                // Rafraîchir les infos d'outil (pour recalculer disponibilité si nécessaire)
                $stmt = $pdo->prepare("SELECT * FROM outil WHERE id = :id");
                $stmt->execute([':id' => $outil_id]);
                $outil = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }
}

// Fonction helper pour générer le chemin d'image (même logique que ton index)
function chemin_image_pour_nom($nom) {
    $nomFichier = strtolower(str_replace(' ', '-', $nom)) . ".jpg";
    return "./assets/images/" . $nomFichier;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Produit - <?= htmlspecialchars($outil['nom']) ?></title>
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

<div class="container">

    <!-- --- CARTE PRODUIT --- -->
    <div class="product-card">
        <div class="product-image">
            <?php $img = chemin_image_pour_nom($outil['nom']); ?>
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($outil['nom']) ?>" style="max-width:100%;height:auto;">
        </div>

        <div class="info">
            <p class="label">Nom</p>
            <p class="value"><?= htmlspecialchars($outil['nom']) ?></p>

            <p class="label">Type</p>
            <p class="value"><?= htmlspecialchars($outil['tarif_journee']) ?> € / jour</p>

            <p class="label">Quantité disponible (total)</p>
            <p class="value"><?= htmlspecialchars($outil['quantite']) ?></p>

        </div>

        <!-- Si user connecté, afficher bouton pour ouvrir le formulaire de resa, sinon le bouton redirige sur la même page (la logique d'affichage du formulaire dépend de la session ci-dessous) -->
        <button class="btn-reserver" id="btn-reserver" type="button">
            Réserver
        </button>
    </div>

    <!--------- BLOC CONNEXION ------->
    <div class="login-box" id="login-box" style="<?= empty($_SESSION['user_id']) ? '' : 'display:none;' ?>">
        <h2>
            Pour pouvoir réserver un outil veuillez vous connecter
        </h2>

        <?php if (!empty($errors) && isset($_POST['action']) && $_POST['action'] === 'login'): ?>
            <div class="errors">
                <?php foreach ($errors as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="reserve.php?id=<?= $outil_id ?>">
            <input type="hidden" name="action" value="login">
            <div class="inputs">
                <div class="input-block">
                    <label>Identifiant</label>
                    <input type="text" name="identifiant" required>
                </div>

                <div class="input-block">
                    <label>Mot de passe</label>
                    <input type="password" name="motdepasse" required>
                </div>
            </div>

            <button class="btn-login" type="submit">Se connecter</button>
        </form>
    </div>

    <!-- Formulaire de réservation (visible si connecté ou après login) -->
    <div class="reservation-form" id="reservation-form" style="<?= empty($_SESSION['user_id']) ? 'display:none;' : '' ?>">
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors) && isset($_POST['action']) && $_POST['action'] === 'reserve'): ?>
            <div class="errors">
                <?php foreach ($errors as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
            </div>
        <?php endif; ?>

        <h3>Réserver : <?= htmlspecialchars($outil['nom']) ?></h3>
        <form method="post" action="reserve.php?id=<?= $outil_id ?>">
            <input type="hidden" name="action" value="reserve">
            <div class="input-block">
                <label>Date de réservation</label>
                <input type="date" name="date_resa" required>
            </div>

            <div class="input-block">
                <label>Quantité</label>
                <input type="number" name="quantite" value="1" min="1" max="<?= htmlspecialchars($outil['quantite']) ?>" required>
            </div>

            <button type="submit" class="btn-reserver">Confirmer la réservation</button>
        </form>
    </div>

</div>

<!-- Bloc réservations (exemples statiques conservés comme ton HTML) -->
<div class="bloc-reservation">
    <h2>EN COURS</h2>
    <table class="reserv-table">
        <tr>
            <td><strong>NOM OUTILS</strong></td>
            <td><strong>TYPE</strong></td>
            <td><strong>DATE/réservation</strong></td>
            <td><strong>Compte à rebours</strong></td>
        </tr>
        <tr>
            <td>NOM OUTILS</td>
            <td>TYPE</td>
            <td>DATE/réservation</td>
            <td>Compte à rebours</td>
        </tr>
    </table>
</div>

<div class="bloc-reservation">
    <h2>PASSÉES</h2>
    <table class="reserv-table">
        <tr>
            <td><strong>NOM OUTILS</strong></td>
            <td><strong>TYPE</strong></td>
            <td><strong>DATE/réservation</strong></td>
            <td><strong>A temps</strong></td>
        </tr>
        <tr>
            <td>NOM OUTILS</td>
            <td>TYPE</td>
            <td>DATE/réservation</td>
            <td>A temps</td>
        </tr>
    </table>
</div>

<script>
// Comportement du bouton Réserver : si connecté (PHP) on affiche le formulaire sinon afficher la box de login
// on laisse le contrôle principal à PHP via le style inline mais on gère l'ouverture au clic pour l'UX
document.getElementById('btn-reserver').addEventListener('click', function() {
    var isConnected = <?= empty($_SESSION['user_id']) ? 'false' : 'true' ?>;
    if (isConnected) {
        // afficher formulaire de réservation
        document.getElementById('reservation-form').style.display = 'block';
        // faire défiler vers le formulaire
        document.getElementById('reservation-form').scrollIntoView({behavior: 'smooth'});
    } else {
        // afficher box de login
        document.getElementById('login-box').style.display = 'block';
        document.getElementById('login-box').scrollIntoView({behavior: 'smooth'});
    }
});
</script>

</body>
</html>
