<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();

// génération de la page
affDebutMenuInfos('Reponses');

echo 'bonjour'. htmlspecialchars($_GET["blID"]);

affPiedFin();

