<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();
$titre='cuiteur';
$css=../styles/cuiteur.css
affDebut($titre,$css);

$sql = 'SELECT utID, utPrenomNom, utPseudo, utVille, utMail, utBio, utDateNaissance, utDateInscription, utCivilite FROM utilisateur ORDER BY utID ASC';

$res = bdSendRequest($bd, $sql);


echo
affFin();
