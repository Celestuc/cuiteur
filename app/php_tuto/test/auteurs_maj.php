<?php
ob_start();
session_start();
error_reporting(E_ALL);

require 'bib_params.php';
require 'bib_fonctions.php';

// Traitement  suivant le bouton trouvé dans
// le post ou l'id trouvé dans le get

//---------------------------------------------------------
// Nouvel auteur : initialisation des valeurs par
// défaut des zones du formulaires, affichage
// du formulaire de saisie.
if (isset($_POST['btnNouveau'])) {
	//nécessaire quand on vient de auteurs_maj.php
	$_SESSION['idAuteur'] = 0;

	$z = ['auNom' => '',
		  'auPrenom' => '',
		  'auBiographie' => '',
		  'auPays' => 'FR'];
	htmlDebut('Ajout auteur', 'bd.css');
	afficherForm($z);
	htmlFin();
	exit();		//==> FIN du traitement nouveau
}

$bd = bdConnect();

//--------------------------------------------------------
// Affichage d'un auteur : on vient de la page de
// liste. Vérification de l'ID passé dans l'url,
// sélection de l'enregistrement auteur dans la
// base, affichage du formulaire.
if (count($_GET) > 0) {

	if (! parametresControle('get',['x'])){
		header('Location: auteurs_cherche.php');
		exit();	//==> FIN : piratage ?
	}
	$idAuteur = dechiffreSigneURL($_GET['x']);
	if ($idAuteur === FALSE) {
		header('Location: auteurs_cherche.php');
		exit();	//==> FIN : piratage ?
	}
	if (!estEntier($idAuteur) || $idAuteur <= 0){
		header('Location: auteurs_cherche.php');
		exit();	//==> FIN : piratage ?
	}

	$idAuteur = (int) $idAuteur;

	$sql = "SELECT *
			FROM auteurs
			WHERE auID = $idAuteur";

	$r = bdSendRequest($bd, $sql);

	$enr = mysqli_fetch_assoc($r);

	// Libération de la mémoire associée au résultat de la requête
	mysqli_free_result($r);

	if ($enr === NULL) {
		header('Location: auteurs_cherche.php');
		exit();	//==> FIN : piratage ?
	}

	$_SESSION['idAuteur'] = $idAuteur;

	htmlDebut('Mise à jour/suppression auteur', 'bd.css');
	afficherForm($enr);
	htmlFin();

	//-- Déconnexion ------------------------------------
	mysqli_close($bd);

	exit();		//==> FIN du traitement affichage
}

//--------------------------------------------------------
// Validation d'une saisie dans le cas d'un ajout ou
// ou d'une modification d'un auteur.
// Mise à jour de la base de
// données si Ok,
// sinon ré-affichage du formulaire de
// saisie avec les erreurs détectées.
if (isset($_POST['btnValider'])) {
	$errs = verifierForm();

	$titre = $_SESSION['idAuteur'] != 0 ?
	         'Mise à jour/suppression' : 'Ajout';
	if (count($errs) == 0) {
		if ($_SESSION['idAuteur'] != 0){
			$titre = 'Mise à jour';
		}
		htmlDebut($titre.' auteur', 'bd.css');
		mettreAJourBase($bd);
		afficherFin();
	}
	else{
		// Il y a des erreurs de saisie. Il faut réafficher
		// le formulaire avec ce qui a été saisi
		htmlDebut($titre.' auteur', 'bd.css');
		afficherForm($_POST, $errs);
	}
	mysqli_close($bd);
	htmlFin();
	exit();		//==> FIN du traitement ajout/modification
}

//--------------------------------------------------------
// Suppression d'un auteur
if (isset($_POST['btnSupprimer'])) {
	$sql = "DELETE
			FROM auteurs
			WHERE auID = {$_SESSION['idAuteur']}";
	bdSendRequest($bd, $sql);
	mysqli_close($bd);
	$_SESSION['idAuteur'] = 0; //plus prudent
	htmlDebut('Suppression auteur', 'bd.css');
	afficherFin();
	htmlFin();
	exit();		//==> FIN du traitement suppression
}

//--------------------------------------------------------
// Si on arrive ici c'est que l'utilisateur n'est
// pas passé par les pages imposées.
mysqli_close($bd);
header('Location: auteurs_cherche.php');
exit(); // ===> FIN
// l'appel d'exit() n'est pas nécessaire car fin du script

//--------------------------------------------------------
/**
 * Affichage du formulaire de saisie
 *
 * @param array		$z		Tab assoc. zone => valeur
 * @param array		$errs	Tab assoc. zone => msg erreur
 *
 * @return void
 */
function afficherForm(array $z, array $errs = []): void {
	$z = tabProtegerSorties($z);

	echo '<form method="POST" class="maj"',
				' action="auteurs_maj.php">',

			'<label>Nom ',
				'<span class="err">',
				(isset($errs['auNom']) ? $errs['auNom'] : ''),
				'</span>',
				'<input type="text" name="auNom" ',
				'value="',$z['auNom'], '">',
			'</label>',

			'<label>Prénom ',
				'<span class="err">',
				(isset($errs['auPrenom']) ? $errs['auPrenom'] : ''),
				'</span>',
				'<input type="text" name="auPrenom" ',
					'value="', $z['auPrenom'], '">',
			'</label>',

			'<label>Pays',
				'<select name="auPays">';

	$t = array( 'FR' => 'France',
	            'US' => 'Etats-unis',
				'XX' => 'Autre'  );

	foreach($t as $cle => $val){
		echo 	'<option value="', $cle, '"',
		        ($z['auPays'] == $cle) ? ' selected>' : '>',
				$val,
				'</option>';
	}

	echo    	'</select>',
			'</label>',

			'<label>Biographie ',
				'<span class="err">',
				(isset($errs['auBiographie']) ? $errs['auBiographie'] : ''),
				'</span>',
				'<textarea name="auBiographie">',
					$z['auBiographie'],
				'</textarea>',
			'</label>',

			'<p class="pagination">',
				'<input type="submit" value="Recherche" ',
					'name="btnChercher" ',
					'formaction="auteurs_cherche.php">',
				'<input type="submit" value="Liste" ',
					'name="btnListe" ',
					'formaction="auteurs_liste.php">';

	// Si nouveau => pas de bouton supprimer, 1 bouton Ajouter
	// Sinon => 1 bouton supprimer et 1 bouton Modifier
	if ($_SESSION['idAuteur'] == 0) {
		echo 	'<input type="submit" value="Ajouter" ',
					'name="btnValider">';
	} else {
		echo 	'<input type="submit" value="Supprimer" ',
					'name="btnSupprimer">',
				'<input type="submit" value="Modifier" ',
					'name="btnValider">';
	}

	echo '</p></form>';
}
//--------------------------------------------------------
/**
 * Vérification des zones de saisie.
 *
 * @return array	Tab. assoc. zone => msg erreur
 */
function verifierForm() : array {
	// contrôle que les clés attendues
	// dans $_POST sont bien présentes
	$clesAttendues = array();
	if (! parametresControle('post', ['btnValider' ,'auNom', 'auPrenom',
							'auBiographie', 'auPays'])){
		header('Location: auteurs_cherche.php');
		exit();	//==> FIN : piratage ?

	}

	$errs  = array();

	$_POST['auNom'] = trim($_POST['auNom']);
	$noTags = strip_tags($_POST['auNom']);
	if ($noTags != $_POST['auNom']){
		$errs['auNom'] = 'Pas de tags HTML';
	}
	else{
		$long = mb_strlen($_POST['auNom'], 'UTF-8');
		if ($long < 2) {
			$errs['auNom'] = '2 caractères minimum';
		} elseif ($long > 30) {
			$errs['auNom'] = '30 caractères maximum';
		}
	}

	$_POST['auPrenom'] = trim($_POST['auPrenom']);
	$noTags = strip_tags($_POST['auPrenom']);
	if ($noTags != $_POST['auPrenom']){
		$errs['auPrenom'] = 'Pas de tags HTML';
	}
	else{
		$long = mb_strlen($_POST['auPrenom'], 'UTF-8');
		if ($long < 2) {
			$errs['auPrenom'] = '2 caractères minimum';
		} elseif ($long > 20) {
			$errs['auPrenom'] = '20 caractères maximum';
		}
	}

	$_POST['auBiographie'] = trim($_POST['auBiographie']);
	$noTags = strip_tags($_POST['auBiographie']);
	if ($noTags != $_POST['auBiographie']){
		$errs['auBiographie'] = 'Pas de tags HTML';
	}

	$vals = array('FR', 'US', 'XX');
	if (!in_array($_POST['auPays'], $vals)) {
		header('Location: auteurs_cherche.php');
		exit();	//==> FIN : piratage ?
	}
	return $errs;
}
//--------------------------------------------------------
/**
 * Mise à jour de la base de données
 *
 * @param array		$bd	  objet représentant la connexion au serveur MySQL
 *
 * @return  void
 */
function mettreAJourBase($bd) : void {
	// Protection SQL
	$auNom = mysqli_real_escape_string($bd, $_POST['auNom']);
	$auPrenom = mysqli_real_escape_string($bd, $_POST['auPrenom']);
	$auBiographie = mysqli_real_escape_string($bd,
	                                       $_POST['auBiographie']);

	$sql = "auNom = '$auNom',
			auPrenom = '$auPrenom',
			auPays = '{$_POST['auPays']}',
			auBiographie = '$auBiographie'";

	if ($_SESSION['idAuteur'] == 0) {
		$sql = "INSERT INTO auteurs SET $sql";
	} else {
		$sql = "UPDATE auteurs
				SET $sql
				WHERE auID = {$_SESSION['idAuteur']}";
	}

	bdSendRequest($bd, $sql);
}
//--------------------------------------------------------
/**
 * Affichage de la page de fin de traitement de la bd.
 *
 * @return void
 */
function afficherFin() : void {
	echo '<form method="POST" class="maj pagination" ',
				'action="auteurs_cherche.php">';

	if (isset($_POST['btnSupprimer'])) {
		echo '<p>L\'auteur a bien été supprimé.</p>';
	} elseif ($_SESSION['idAuteur'] == 0) {
		echo '<p>Le nouvel auteur a bien été ajouté.</p>';
	} else {
		echo '<p>L\'auteur a bien été modifié.</p>';
	}

	echo '<p>',
			'<input type="submit" value="Rechercher" name="btnChercher">',
			'<input type="submit" value="Liste" name="btnListe" ',
				'formaction="auteurs_liste.php">',
			'<input type="submit" value="Ajouter" name="btnNouveau" ',
				'formaction="auteurs_maj.php">',
		'</p>';

	echo '</form>';
}
