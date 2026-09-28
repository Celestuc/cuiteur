<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect(); 

$id = 7;

$sql = "SELECT utPseudo, utPrenomNom, blTexte, blDate, blHeure, blIDParent
        FROM blabla
        RIGHT OUTER JOIN utilisateur ON utID = blIDAuteur
        WHERE utID = $id
        ORDER BY blID DESC";

$res = bdSendRequest($bd, $sql);

mysqli_close($bd); // fermée dès que possible


affDebut('Liste de blablas');

$t = mysqli_fetch_assoc($res);

if ($t === null){
    echo '<p>L\'utilisateur "', $id , '" n\'existe pas.</p>';
    affFin();
    exit;
}

echo    '<h1>Les blablas de ', htmlProtegerSorties($t['utPseudo']), '</h1>';

if (isset($t['blTexte'])){
    echo '<ul>';
    do{
        $t = htmlProtegerSorties($t); // ATTENTION à ne pas oublier de protéger toutes les chaines de caractères provenant de la BdD
        echo '<li>',
                '<strong>', $t['utPseudo'], '</strong>  ', $t['utPrenomNom'], '<br>',
                $t['blTexte'], '<br>',
                '<i>', $t['blIDParent'] === null ? 'Blabla original' : "Réponse au blabla d'identifiant \"{$t['blIDParent']}\"", '</i><br>',
                dateFormat($t['blDate']), ' à ', heureFormat($t['blHeure']), '<br>',

            '</li>';
    }while($t = mysqli_fetch_assoc($res));
    echo '</ul>';
}
else{
    echo '<p>Pas de blablas publiés</p>';
}

mysqli_free_result($res);

affFin();

