<?php

error_reporting(E_ALL);

require_once 'bib_fonctions.php';

if (count($_GET) != 1){
    htmlDebut('Lecture d\'un fichier');
    htmlInfo('Seul un paramètre \'file\' doit être présent l\'URL');
    htmlFin();
    exit();
}

if (! isset($_GET['file'])){
    htmlDebut('Lecture d\'un fichier');
    htmlInfo('Pas de paramètre \'file\' dans l\'URL');
    htmlFin();
    exit();
}

$nom = $_GET['file'];

if (! is_file($nom)) {
    htmlDebut('Lecture d\'un fichier');
	htmlInfo("'$nom' n'existe pas ou n'est pas un fichier régulier");
	htmlFin();
	exit();
}

htmlDebut("Lecture du fichier '$nom'");

// Ouverture du fichier
$flux = @fopen($nom, 'r');
if ($flux === false){
	htmlInfo("Erreur lors de l'ouverture de '$nom'");
	htmlFin();
	exit();
}

echo '<pre>';
// boucle de lecture
while (true){
	$line = fgets($flux);
	if ($line === false){
		break;
	}
	echo htmlspecialchars($line, encoding:'UTF-8');
}
echo '</pre>';
// fermeture du flux
$r = fclose($flux);
if (! $r){
	htmlInfo('Erreur lors de la fermeture du flux');
	htmlFin();
	exit();
}

htmlFin();
