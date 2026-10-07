<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$erreurAppelPage = null;

$bd = bdConnect();

if (parametresControle('get', ['blID'])){
// if (isset($_GET['blID']) && count($_GET) == 1){
    if (! estEntier($_GET['blID'])){
        $erreurAppelPage = "La valeur de \"blID\" n'est pas un entier.";
    }
    else{
        $blIDInitial = (int) $_GET['blID'];
        if ($blIDInitial <= 0){
            $erreurAppelPage = "La valeur de \"blID\" n'est pas un entier strictement positif.";
        }
    }
}
else{
    $erreurAppelPage = 'L\'URL n\'a pas la bonne forme. Il manque le paramètre "bliD" ou il y a un paramètre en trop.';
}

if ($erreurAppelPage === null){
    $blablaInitial = bdGetBlablas(BLABLA_INITIAL, $blIDInitial);
    if ($blablaInitial === null){
        $erreurAppelPage = "Le blabla initial \"$blIDInitial\" n'existe pas.";
    }
}

// génération de la page
affDebutMenuInfos('Réponses');
if ($erreurAppelPage){
    affSectionErreur($erreurAppelPage);
}
else{
    echo '<section><h2>Blabla initial</h2>';
    affUnBlabla($blablaInitial);
    echo '</section>';
    affFormPublier('Publication d\'une nouvelle réponse');
    $blablas = bdGetBlablas(BLABLAS_REPONSES, $blIDInitial);
    affBlablas($blablas);
}

mysqli_close($bd);

affPiedFin();

// facultatif car fait automatiquement par PHP
ob_end_flush();













