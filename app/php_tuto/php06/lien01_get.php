<?php
require('bib_fonctions.php');

htmlDebut('Informations transmises dans une URL');

foreach ($_GET as $nom => $valeur) {
	htmlInfo($nom);
	echo $valeur;
}

echo '<hr>';
htmlInfo('var_dump($_GET)');
echo '<pre>';
var_dump($_GET);
echo '</pre>';

htmlFin();
