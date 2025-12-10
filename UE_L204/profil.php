<?php
session_start();
require_once "config.php";

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// Récupère infos de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Vérifie le rôle : utilisateur ou professionnel
$isPro = ($user['role'] === 'admin');

// =======================================================================
// SUPPRESSION D’UN COMPTE PAR LE PROFESSIONNEL
// =======================================================================
$messageSuppression = "";
if ($isPro && isset($_POST['supprimer_compte'])) {
    $idASupprimer = (int)$_POST['supprimer_compte'];
    if ($idASupprimer === $user['id']) {
        $messageSuppression = "<p style='color:red;'>Vous ne pouvez pas supprimer votre propre compte.</p>";
    } else {
        $verif = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = ?");
        $verif->execute([$idASupprimer]);
        if ($verif->fetch()) {
            $delete = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $delete->execute([$idASupprimer]);
            $messageSuppression = "<p style='color:green;'>Compte supprimé avec succès !</p>";
        }
    }
}

// =======================================================================
// ANNULATION D’UNE RÉSERVATION PAR L’UTILISATEUR
// =======================================================================
if (!$isPro && isset($_POST['annuler_reservation'])) {
    $resId = (int)$_POST['annuler_reservation'];
    // Vérifie que la réservation appartient bien à l'utilisateur
    $verif = $pdo->prepare("SELECT id FROM reservation WHERE id = ? AND utilisateur_id = ?");
    $verif->execute([$resId, $user['id']]);
    if ($verif->fetch()) {
        $del = $pdo->prepare("DELETE FROM reservation WHERE id = ?");
        $del->execute([$resId]);
    }
}

// =======================================================================
// UTILISATEUR NORMAL : récupère ses réservations
// =======================================================================
$reservations = [];
if (!$isPro) {
    $stmt = $pdo->prepare("
        SELECT r.*, o.nom AS outil_nom 
        FROM reservation r
        JOIN outil o ON r.outil_id = o.id
        WHERE r.utilisateur_id = ?
        ORDER BY r.date_debut DESC
    ");
    $stmt->execute([$user['id']]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// =======================================================================
// PROFESSIONNEL : liste des outils + liste des utilisateurs
// =======================================================================
$mesOutils = [];
$listeUtilisateurs = [];
if ($isPro) {
    $stmt = $pdo->query("SELECT * FROM outil ORDER BY nom");
    $mesOutils = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT id, identifiant, role FROM utilisateurs ORDER BY role DESC, identifiant");
    $stmt->execute();
    $listeUtilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// =======================================================================
// AJOUT D’UN OUTIL (professionnel)
// =======================================================================
$ajoutMsg = "";
if ($isPro && isset($_POST['ajout_outil'])) {
    $nom = trim($_POST['nom']);
    $quantite = (int)$_POST['quantite'];
    $prix = (int)$_POST['tarif_journee'];
    if ($nom && $quantite > 0 && $prix >= 0) {
        $stmt = $pdo->prepare("INSERT INTO outil (nom, quantite, tarif_journee) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $quantite, $prix]);
        $ajoutMsg = "Outil ajouté avec succès !";
        $mesOutils[] = ['nom'=>$nom,'quantite'=>$quantite,'tarif_journee'=>$prix];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil - <?= htmlspecialchars($user['identifiant']) ?></title>
<link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<header class="top-bar">
    <a href="./index.php" class="about-btn">Accueil</a>
    <div class="profile-icon">
        <a href="./profil.php"><img src="./assets/images/profil.png" alt="Profil"></a>
    </div>
</header>

<h1 class="title">Bonjour, <?= htmlspecialchars($user['identifiant']) ?> !</h1>

<div class="container">

    <!-- Colonne gauche -->
    <div class="profile-card">
        <p><strong>Identifiant :</strong> <?= htmlspecialchars($user['identifiant']) ?></p>
        <p><strong>Mot de passe :</strong> ****</p>
        <p><strong>Rôle :</strong> <?= $isPro ? 'Professionnel' : 'Utilisateur' ?></p>

        <form method="post" action="deconnexion.php">
            <button class="btn-logout" type="submit">Déconnexion</button>
        </form>
    </div>

    <!-- Colonne droite -->
    <div class="reservations">

        <?php if (!$isPro): ?>
            <!-- USER : réservations -->
            <h2>Mes Réservations</h2>

            <?php if ($reservations): ?>
                <table class="reserv-table">
                    <tr>
                        <th>Outil</th>
                        <th>Date</th>
                        <th>Quantité</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($reservations as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['outil_nom']) ?></td>
                            <td><?= htmlspecialchars($r['date_debut']) ?></td>
                            <td><?= htmlspecialchars($r['quantite']) ?></td>
                            <td>
                                <!-- Formulaire pour annuler la réservation -->
                                <form method="post" onsubmit="return confirm('Annuler cette réservation ?');">
                                    <button type="submit" name="annuler_reservation" value="<?= $r['id'] ?>" class="btn-delete">
                                        Annuler
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Aucune réservation pour le moment.</p>
            <?php endif; ?>

            <a href="index.php" class="btn-login">Réserver un outil</a>

        <?php else: ?>
            <!-- ADMIN : outils et utilisateurs -->
            <h2>Mes Outils</h2>

            <?php if ($mesOutils): ?>
                <table class="reserv-table">
                    <tr>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Prix / jour</th>
                    </tr>
                    <?php foreach ($mesOutils as $o): ?>
                        <tr>
                            <td><?= htmlspecialchars($o['nom']) ?></td>
                            <td><?= htmlspecialchars($o['quantite']) ?></td>
                            <td><?= htmlspecialchars($o['tarif_journee']) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <h3>Ajouter un outil</h3>
            <?php if ($ajoutMsg) echo "<p style='color:green;'>$ajoutMsg</p>"; ?>
            <form method="post">
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="number" name="quantite" placeholder="Quantité" min="1" required>
                <input type="number" name="tarif_journee" placeholder="Prix/jour" min="0" required>
                <button type="submit" name="ajout_outil" class="btn-login">Ajouter</button>
            </form>

            <h2>Gestion des utilisateurs</h2>
            <?= $messageSuppression ?>
            <table class="reserv-table">
                <tr>
                    <th>ID</th>
                    <th>Identifiant</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($listeUtilisateurs as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['identifiant']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td>
                            <?php if ($u['id'] != $user['id']): ?>
                                <form method="post" onsubmit="return confirm('Supprimer ce compte ?');">
                                    <button type="submit" name="supprimer_compte" value="<?= $u['id'] ?>" class="btn-logout">Supprimer</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php endif; ?>

    </div>
</div>

</body>
</html>

