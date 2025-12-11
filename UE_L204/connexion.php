<?php
session_start();

require_once "config.php";

$erreur = "";
$succes = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -------------------------
    //      INSCRIPTION
    // -------------------------
    if (isset($_POST['action']) && $_POST['action'] === 'inscription') {

        $identifiant = trim($_POST['identifiant']);
        $motdepasse = trim($_POST['motdepasse']);
        $role = $_POST['role']; // user ou professionnel

        // Vérifier si identifiant déjà existant
        $sql = "SELECT * FROM utilisateurs WHERE identifiant = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $erreur = "Cet identifiant existe déjà.";

        } else {
            // Hashage sécurisé du mot de passe
            $hash = password_hash($motdepasse, PASSWORD_BCRYPT);

            $sql = "INSERT INTO utilisateurs (identifiant, motdepasse, role)
                    VALUES (:id, :mdp, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $identifiant,
                ':mdp' => $hash,
                ':role' => $role
            ]);

            $succes = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }

    // -------------------------
    //      CONNEXION
    // -------------------------
    elseif (isset($_POST['action']) && $_POST['action'] === 'connexion') {

        $identifiant = trim($_POST['identifiant']);
        $motdepasse = trim($_POST['motdepasse']);

        $sql = "SELECT * FROM utilisateurs WHERE identifiant = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($motdepasse, $user['motdepasse'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['identifiant'] = $user['identifiant'];
            $_SESSION['role'] = $user['role'];

            header("Location: profil.php");
            exit;
        } else {
            $erreur = "Identifiant ou mot de passe incorrect.";
        }
    }
}

// Choix du formulaire
$formulaire = $_GET['form'] ?? 'connexion';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= $formulaire === 'connexion' ? 'Se connecter' : 'S\'inscrire' ?></title>
<link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<div class="login-box">
    <h2><?= $formulaire === 'connexion' ? 'Se connecter' : 'S\'inscrire' ?></h2>

    <?php if ($erreur) echo "<p style='color:red;'>$erreur</p>"; ?>
    <?php if ($succes) echo "<p style='color:green;'>$succes</p>"; ?>

    <form method="POST">
        <div class="inputs">

            <?php if ($formulaire === 'inscription'): ?>
                <div class="input-block">
                    <label>Type de compte :</label>
                    <select name="role" required>
                        <option value="user">Utilisateur</option>
                        <option value="admin">Professionnel</option>
                    </select>
                </div>
            <?php endif; ?>

            <div class="input-block">
                <label>Identifiant</label>
                <input type="text" name="identifiant" required>
            </div>

            <div class="input-block">
                <label>Mot de passe</label>
                <input type="password" name="motdepasse" required>
            </div>

        </div>

        <?php if ($formulaire === 'connexion'): ?>
            <input type="hidden" name="action" value="connexion">
            <button class="btn-login" type="submit">SE CONNECTER</button>
            <div class="toggle-form" onclick="window.location='?form=inscription'">Créer un compte</div>
        <?php else: ?>
            <input type="hidden" name="action" value="inscription">
            <button class="btn-login" type="submit">S'INSCRIRE</button>
            <div class="toggle-form" onclick="window.location='?form=connexion'">Déjà un compte ? Se connecter</div>
        <?php endif; ?>

    </form>
</div>

</body>
</html>
