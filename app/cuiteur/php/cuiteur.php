<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();

// génération de la page
affDebutMenuInfos('Accueil');
affFormPublier();
$blablas = bdGetBlablas();

mysqli_close($bd); // fermée dès que possible

affBlablas($blablas);
affPiedFin();

// facultatif car fait automatiquement par PHP
ob_end_flush();





