<?php

// echo '<img src="attaque_xss.php">'; // l'envoi de cette chaine piégée (il s'agit d'une attaque XSS) au navigateur entraine l'exécution du script attaque_xss.php
//
// Avec "echo '<img src="attaque_xss.pnp">';", où attaque_xss.pnp est une copie du fichier attaque_xss.php (il s'agit d'un faux png), le script n'est pas exécuté

// echo '<a href="attaque_xss.php">lien</a>'; // => l'attaque fonctionne
// echo '<a href="attaque_xss.png">lien</a>'; // => l'attaque ne fonctionne pas non plus

header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', '1'); ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

ob_start();
require('bib_params.php');
require('bib_fonctions.php');



$bd = bdConnect();

//-- Requête ----------------------------------------
$sql = 'UPDATE livres SET liTitre = "1984" WHERE liID = 3';
bdSendRequest($bd, $sql);

// pour annuler la modif : UPDATE livres SET liTitre = "Performances PHP" WHERE liID = 3



mysqli_close($bd);
