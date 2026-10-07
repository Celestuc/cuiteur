<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();

affDebutMenuInfos('Accueil');
affFormPublier('Publication d\'un nouveau blabla');
$blablas = bdGetBlablas(BLABLAS_CUITEUR);

mysqli_close($bd); // fermée dès que possible

affBlablas($blablas);

affPiedFin();

// facultatif car fait automatiquement par PHP
ob_end_flush();





