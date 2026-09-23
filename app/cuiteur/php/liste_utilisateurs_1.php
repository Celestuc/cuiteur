<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();
$titre='liste utilisateur';
affDebut($titre);

$sql = 'SELECT utID, utPrenomNom, utPseudo, utVille, utMail, utBio, utDateNaissance, utDateInscription, utCivilite FROM utilisateur ORDER BY utID ASC';

$res = bdSendRequest($bd, $sql);

affTeteUt();
while($t = mysqli_fetch_assoc($res)){
    affListeUt($t);
}

affFin();
//mysqli_fetch_assoc : on lui passee une variable et ca permet de voir ce quelle contient








