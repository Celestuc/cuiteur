<?php


// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();
$titre='liste blabla';
affDebut($titre);

$sql = 'SELECT utPrenomNom, utPseudo, blTexte, blDate, blHeure FROM utilisateur INNER JOIN blabla ON blIDAuteur  WHERE utID = 7 ORDER BY blDate DESC;';

$res = bdSendRequest($bd, $sql);

affTeteBl();
while($t = mysqli_fetch_assoc($res)){
    affListeBl($t);
}

affFin();

