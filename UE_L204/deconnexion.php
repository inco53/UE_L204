<?php 
    session_start();
    /*
        Se déconnecter
    */

    // vide le tableau de la session
    $_SESSION = [];     
    // supprime les variables de la session
    session_unset();  
    // permet de détruire la session        
    session_destroy();       

    // Rediriger vers la page d'accueil
    header("Location: ../index.php");
    exit;

?>