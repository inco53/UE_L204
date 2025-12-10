<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Xanh+Mono:ital@0;1&display=swap" rel="stylesheet">
    <title>Locatools - Catalogue d'outils</title>
    <link rel="stylesheet" href="./assets/css/style.css" type="text/css">
</head>
<body>

    <!-- Header -->
    <header class="top-bar">
        <a href="./apropos.php" class="about-btn">À propos</a>
        <div class="profile-icon">
            <a href="./profil.php"><img src="./assets/images/profil.png" alt="Profil"></a>
        </div>
    </header>

    <!----------Filtres-------->
    <form action="">
        <fieldset class="search-container">
             
                <section class="filters">

                    <legend class="search-form-legend">FILTRER</legend>

                    <p class="search-form-item">
                    <label class="search-form-label" for="search-outil">Type d'outils :</label>
                        <select class="search-form-input" name="search-outil" id="search-outil" required>
                            <option value="">Type d'outil : </option>
                            <option value="1">type outil 1</option>
                            <option value="2">type outil 2</option>
                            <option value="3">type outil 3</option>
                        </select>
                    </p>    

                    <p class="search-form-item">                        
                        <label class="search-form-label" for="search-reservation">Date de réservation :</label>
                        <input class="search-form-input" type="date" name="search-reservation" id="search-reservation" required>
                    </p>
                           
                    <p class="search-form-item">
                    <label class="search-form-label" for="search-outil">Prix :</label>
                        <select class="search-form-input" name="search-outil" id="search-outil" required>
                            <option value="">Type d'outil : </option>
                            <option value="1">0 - 20 €</option>
                            <option value="2">20 - 50 €</option>
                            <option value="3">50 € +</option>
                        </select>
                    </p>    
                      
                </section>          

                <button class="search-btn"><img class="recherche" src="./assets/images/rechercher.png" alt="recherche"></button>

        </fieldset>        
        
    </form>



   
   
   
    <!-- Catalogue -->
    <section class="gallerie">

        <div class="card">
            <div class="img-placeholder">
                <img src="https://img.icons8.com/ios/200/image.png" alt="image">
            </div>
            <h3>Nom de l’outil</h3>
            <p>Prix : 20€</p>
            <button class="reserve-btn">Réserver</button>
        </div>

        <div class="card">
            <div class="img-placeholder">
                <img src="https://img.icons8.com/ios/200/image.png" alt="image">
            </div>
            <h3>Nom de l’outil</h3>
            <p>Prix : 35€</p>
            <button class="reserve-btn">Réserver</button>
        </div>

        <div class="card">
            <div class="img-placeholder">
                <img src="https://img.icons8.com/ios/200/image.png" alt="image">
            </div>
            <h3>Nom de l’outil</h3>
            <p>Prix : 15€</p>
            <button class="reserve-btn">Réserver</button>
        </div>

    </section>

</body>
</html>
