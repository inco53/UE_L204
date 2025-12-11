<?php
// on arrête l'exécution si le fichier require n'existe pas
require_once "config.php";

//on séléctionne toutes les colonnes de la table outil
$sql = "SELECT * FROM outil WHERE 1";
// permet déviter les injonctions sql et stocke les paramètres pour la requête
$params = [];

//on vérifie et récupère les valeurs envoyer via la méthode get
if (!empty($_GET['nom'])) {
    //condition sql 
    $sql .= " AND nom LIKE :nom";
    $params[':nom'] = '%' . $_GET['nom'] . '%';
}

if (!empty($_GET['type'])) {
    if ($_GET['type'] == 1) {
        $sql .= " AND tarif_journee < 20";
    } elseif ($_GET['type'] == 2) {
        $sql .= " AND tarif_journee BETWEEN 20 AND 50";
    } elseif ($_GET['type'] == 3) {
        $sql .= " AND tarif_journee > 50";
    }
}

if (!empty($_GET['prix'])) {
    if ($_GET['prix'] == 1) {
        $sql .= " AND tarif_journee BETWEEN 0 AND 20";
    } elseif ($_GET['prix'] == 2) {
        $sql .= " AND tarif_journee BETWEEN 20 AND 50";
    } elseif ($_GET['prix'] == 3) {
        $sql .= " AND tarif_journee > 50";
    }
}

if (!empty($_GET['date'])) {
    $date = $_GET['date'];

    $sql .= " AND id NOT IN (
                SELECT outil_id 
                FROM reservation
                WHERE :date BETWEEN date_debut AND date_fin
             )";

    $params[':date'] = $date;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$outils = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locatools - Catalogue d'outils</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Xanh+Mono:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>

<header class="top-bar">
    <a href="./apropos.php" class="about-btn">À propos</a>
    <div class="profile-icon">
        <a href="./profil.php"><img src="./assets/images/profil.png" alt="Profil"></a>
    </div>
</header>

<!----------Filtres-------->
<form action="" method="get">
    <fieldset class="search-container">

        <section class="filters">

            <legend class="search-form-legend">FILTRER</legend>

            <p class="search-form-item">
                <label class="search-form-label" for="nom">Nom de l'outil :</label>
                <input class="search-form-input" type="text" name="nom" id="nom" placeholder="Ex: scie circulaire" value="<?= isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : '' ?>">
            </p>

            <p class="search-form-item">
                <label class="search-form-label" for="date">Date de réservation :</label>
                <input class="search-form-input" type="date" name="date" id="date" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
            </p>

            <p class="search-form-item">
                <label class="search-form-label" for="prix">Prix :</label>
                <select class="search-form-input" name="prix" id="prix">
                    <option value="">Tous</option>
                    <option value="1" <?= (isset($_GET['prix']) && $_GET['prix'] == 1) ? 'selected' : '' ?>>0 - 20 €</option>
                    <option value="2" <?= (isset($_GET['prix']) && $_GET['prix'] == 2) ? 'selected' : '' ?>>20 - 50 €</option>
                    <option value="3" <?= (isset($_GET['prix']) && $_GET['prix'] == 3) ? 'selected' : '' ?>>50 € +</option>
                </select>
            </p>

        </section>

        <button class="search-btn"><img class="recherche" src="./assets/images/rechercher.png" alt="recherche"></button>

    </fieldset>
</form>

<section class="gallerie">

<?php
// Extensions possibles
$extensions = ['jpg', 'jpeg', 'png', 'webp'];

// Lire tous les fichiers du dossier images
$imageFiles = [];
foreach ($extensions as $ext) {
    foreach (glob("./assets/images/*.$ext") as $file) {
        $imageFiles[] = $file;
    }
}

// Fonction pour normaliser les noms : minuscules, sans accents
function normalize($str) {
    // Supprime les accents
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9]/', '', $str); // supprime tout sauf lettres et chiffres
    return $str;
}

foreach ($outils as $outil):
    $outilNormal = normalize($outil['nom']);

    // Image par défaut
    $cheminImage = "./assets/images/default.jpg";

    // Chercher une image correspondant au nom de l'outil
    foreach ($imageFiles as $img) {
        $imgNom = basename($img, "." . pathinfo($img, PATHINFO_EXTENSION));
        $imgNormal = normalize($imgNom);

        if ($imgNormal === $outilNormal) {
            $cheminImage = $img;
            break;
        }
    }
?>

    <div class="card">
        <div class="img-placeholder">
            <img src="<?= $cheminImage ?>" alt="<?= htmlspecialchars($outil['nom']) ?>">
        </div>

        <h3><?= htmlspecialchars($outil['nom']) ?></h3>
        <p>Prix : <?= htmlspecialchars($outil['tarif_journee']) ?> € / jour</p>

        <button class="reserve-btn"
                onclick="window.location.href='produit.php?id=<?= urlencode($outil['id']) ?>'">
            Réserver
        </button>
    </div>

<?php endforeach; ?>

</section>


</body>
</html>
