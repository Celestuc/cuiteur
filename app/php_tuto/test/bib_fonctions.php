<?php
//
// Bibliothèque de fonctions PHP
//
// Remarque :
// Le code de ce fichier est le résultat de divers ajouts faits tout
// au long du tutoriel au fur et à mesure de la progression des
// connaissances. Si à un moment T vous regardez ce code, ne vous
// étonnez donc pas qu'il ne corresponde pas forcément à ce qui est
// indiqué dans les pages du tutoriel.
//
//
//___________________________________________________________________
/**
 * Envoie à la sortie standard le début du code HTML d'une page
 *
 * @param string	$titre	Titre de la page
 * @param ?string	$css	Fichier CSS éventuel
 *
 * @return void
 */
function htmlDebut(string $titre, ?string $css = null): void {
	$titre = htmlentities($titre, ENT_QUOTES, 'UTF-8');

	$css = ($css) ? "<link rel='stylesheet' type='text/css' href='$css'>" : '';

	echo '<!DOCTYPE html>',
			'<html lang="fr">',
				'<head>',
					'<meta charset="UTF-8">',
					'<title>', $titre, '</title>',
					'<style>',
					'body {font-size: 13px;font-family: Verdana, sans-serif}',
					'h3 {font-size: 15px;margin: 0 0 15px 0;padding: 5px 0;text-align:center;background: #FFF5AB}',
					'h4 {font-size: 13px;margin: 1em 0 0 0;padding: 3px;background: #ebebeb;clear:both}',
					'label {display: block;font-weight: bold;margin-top: 10px;}',
					'h5 {font-size: 13px; font-weight: normal; margin: 1em 0 0 0; padding: 3px; border: 1px solid #aaa}',
					'.exp {font-weight: bold; color: green;}',
					'</style>',
					$css,
				'</head>',
				'<body>',
					'<h3>', $titre, '</h3>';
}
//___________________________________________________________________
/**
 * Envoie à la sortie standard la fin du code HTML d'une page
 *
 * @return void
 */
function htmlFin() : void {
	echo '</body></html>';
}
//___________________________________________________________________
/**
 * Envoie à la sortie standard une info / titre pour les exemples
 *
 * @param string	$txt	Texte à afficher
 *
 * @return void
 */
function htmlInfo(string $txt) :void {
	echo '<h4>', htmlentities($txt, ENT_QUOTES, 'UTF-8'), '</h4>';
}
//___________________________________________________________________
/**
 * Envoie à la sortie standard l'en-tête d'une table HTML
 *
 * @param array     $titres Titres de colonnes de la table
 * @param ?string   $css    Classe CSS éventuelle de la table
 *
 * @return void
 */
function htmlEnteteTable(array $titres, ?string $css = null)
                                                     : void {
    echo '<table', ($css === null) ? '>' : " class='$css'>";

    htmlLigne($titres);
}
//___________________________________________________________________
/**
 * Envoie à la sortie standard le nombre d'éléments et le contenu d'un tableau
 *
 * @param string	$t	Tableau
 */
function infoTableau($t) {
	echo 'Tableau de ', count($t), ' &eacute;l&eacute;ments',
			'<pre>', print_r($t, true), '</pre>';
}
//___________________________________________________________________
/**
 * Envoie à la sortie standard une ligne d'une table HTML
 *
 * @param array     $elts   Elements à afficher dans les colonnes
 * @param ?string   $css    Classe CSS éventuelle de la ligne
 *
 * @return void
 */
function htmlLigne(array $elts, ?string $css = null) {
    echo '<tr', ($css === null) ? '>' : " class='$css'>";

    foreach ($elts as $elt) {
        echo '<td>', $elt, '</td>';
    }

    echo '</tr>';
}
//___________________________________________________________________
/**
 * Testeur de fonction
 *
 * @param string	$nomFct			nom de la fonction à tester
 * @param mixed		$attendu		résultat attendu
 * @param mixed		$params			paramètres à passer à $nomFct
 *
 * @return void
 */
function tester(string $nomFct, mixed $attendu, mixed ...$params) : void {
	$nbParams = count($params);
	if ($nbParams == 0) {
		echo '<hr>Il faut au moins un paramètre à transmettre à la fonction "', $nomFct, '".';
		return;
	}

	if (! function_exists($nomFct)) {
		echo '<hr>La fonction "', $nomFct, '" n\'existe pas.';
		return;
	}

	$retour = $nomFct(...$params);

	echo '<hr><span style="color:', ($retour === $attendu) ? 'green' : 'red', '">',
			'<b>', $nomFct, '</b></span>',
			'<br>Paramètre', ($nbParams > 1) ? 's : ' : ' : ';

	for ($i = 0; $i < $nbParams; ++$i) {
		echo '<span style="background-color: #ebebeb; margin: 0 5px;">';
		var_dump($params[$i]);
		echo '</span>';
	}

	echo '<br>Attendu : ';
	var_dump($attendu);

	echo '<br>Retourné; : ';
	var_dump($retour);
}
//___________________________________________________________________
/**
 * Contrôle des clés présentes dans les tableaux $_GET ou
 * $_POST - piratage ?
 *
 * Soit $x l'ensemble des clés contenues dans $_GET ou $_POST
 * L'ensemble des clés obligatoires doit être inclus dans $x.
 * De même $x doit être inclus dans l'ensemble des clés autorisées,
 * formé par l'union de l'ensemble des clés facultatives et de
 * l'ensemble des clés obligatoires. Si ces 2 conditions sont
 * vraies, la fonction renvoie true, sinon, elle renvoie false.
 * Dit autrement, la fonction renvoie false si une clé obligatoire
 * est absente ou si une clé non autorisée est présente; elle
 * renvoie true si "tout va bien"
 *
 * @param string    $tabGlobal 'post' ou 'get'
 * @param array     $clesObligatoires tableau contenant les clés
 *                  qui doivent obligatoirement être présentes
 * @param array     $clesFacultatives tableau contenant
 *                  les clés facultatives
 *
 * @return bool     true si les paramètres sont corrects, false sinon
*/
function parametresControle(string $tabGlobal,
							array $clesObligatoires,
							array $clesFacultatives = []): bool{
    $x = strtolower($tabGlobal) == 'post' ? $_POST : $_GET;

    $x = array_keys($x);
    // $clesObligatoires doit être inclus dans $x
    if (count(array_diff($clesObligatoires, $x)) > 0){
        return false;
    }
    // $x doit être inclus dans
    // $clesObligatoires Union $clesFacultatives
    if (count(array_diff($x,
						 array_merge($clesObligatoires,
						             $clesFacultatives))) > 0){
        return false;
    }

    return true;
}

//___________________________________________________________________
/**
 * Teste si une valeur est une valeur entière
 *
 * @param   mixed    $x  valeur à tester
 *
 * @return  bool     true si entier, FALSE sinon
 */
function estEntier(mixed $x):bool {
    return is_numeric($x) && ($x == (int) $x);
}

//___________________________________________________________________
/**
 * Teste si un nombre est compris entre 2 autres
 *
 * @param integer	$x	nombre à tester
 * @return boolean	TRUE si ok, FALSE sinon
 */
function estEntre($x, $min, $max) {
	return ($x >= $min) && ($x <= $max);
}

//____________________________________________________________________________
/**
 * Arrêt du script si erreur de base de données
 *
 * Affichage d'un message d'erreur, puis arrêt du script
 * Fonction appelée quand une erreur 'base de données' se produit :
 *      - lors de la phase de connexion au serveur MySQL
 *      - ou lorsque l'envoi d'une requête échoue
 *
 * @param array    $err    Informations utiles pour le débogage
 *
 * @return void
 */
function bdErreurExit(array $err):void {
    ob_end_clean(); // Suppression de tout ce qui a pu être déja généré

    echo    '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">',
            '<title>Erreur',
            IS_DEV ? ' base de données': '', '</title>',
            '</head><body>';
    if (IS_DEV){
        // Affichage de toutes les infos contenues dans $err
        echo    '<h4>', $err['titre'], '</h4>',
                '<pre>',
                    '<strong>Erreur mysqli</strong> : ',  $err['code'], "\n",
                    $err['message'], "\n";
        if (isset($err['autres'])){
            echo "\n";
            foreach($err['autres'] as $cle => $valeur){
                echo    '<strong>', $cle, '</strong> :', "\n", $valeur, "\n";
            }
        }
        echo    "\n",'<strong>Pile des appels de fonctions :</strong>', "\n", $err['appels'],
                '</pre>';
    }
    else {
        echo 'Une erreur s\'est produite';
    }

    echo    '</body></html>';

    if (! IS_DEV){
        // Mémorisation des erreurs dans un fichier de log
        $fichier = @fopen('error.log', 'a');
        if($fichier){
            fwrite($fichier, '['.date('d/m/Y').' '.date('H:i:s')."]\n");
            fwrite($fichier, $err['titre']."\n");
            fwrite($fichier, "Erreur mysqli : {$err['code']}\n");
            fwrite($fichier, "{$err['message']}\n");
            if (isset($err['autres'])){
                foreach($err['autres'] as $cle => $valeur){
                    fwrite($fichier,"{$cle} :\n{$valeur}\n");
                }
            }
            fwrite($fichier,"Pile des appels de fonctions :\n");
            fwrite($fichier, "{$err['appels']}\n\n");
            fclose($fichier);
        }
    }
    exit(1);        // ==> ARRET DU SCRIPT
}

//____________________________________________________________________________
/**
 *  Ouverture de la connexion à la base de données en gérant les erreurs.
 *
 *  En cas d'erreur de connexion, une page "propre" avec un message d'erreur
 *  adéquat est affiché ET le script est arrêté.
 *
 *  @return mysqli  objet connecteur à la base de données
 */
function bdConnect(): mysqli {
    // pour forcer la levée de l'exception mysqli_sql_exception
    // si la connexion échoue
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try{
        $conn = mysqli_connect(BD_SERVER, BD_USER, BD_PASS, BD_NAME);
    }
    catch(mysqli_sql_exception $e){
        $err['titre'] = 'Erreur de connexion';
        $err['code'] = $e->getCode();
        // $e->getMessage() est encodée en ISO-8859-1, il faut la convertir en UTF-8
        $err['message'] = mb_convert_encoding($e->getMessage(), 'UTF-8', 'ISO-8859-1');
        $err['appels'] = $e->getTraceAsString(); //Pile d'appels
        $err['autres'] = array('Paramètres' =>   'BD_SERVER : '. BD_SERVER
                                                    ."\n".'BD_USER : '. BD_USER
                                                    ."\n".'BD_PASS : '. BD_PASS
                                                    ."\n".'BD_NAME : '. BD_NAME);
        bdErreurExit($err); // ==> ARRET DU SCRIPT
    }
    try{
        //mysqli_set_charset() définit le jeu de caractères par défaut à utiliser lors de l'envoi
        //de données depuis et vers le serveur de base de données.
        mysqli_set_charset($conn, 'utf8');
        return $conn;     // ===> Sortie connexion OK
    }
    catch(mysqli_sql_exception $e){
        $err['titre'] = 'Erreur lors de la définition du charset';
        $err['code'] = $e->getCode();
        $err['message'] = mb_convert_encoding($e->getMessage(), 'UTF-8', 'ISO-8859-1');
        $err['appels'] = $e->getTraceAsString();
        bdErreurExit($err); // ==> ARRET DU SCRIPT
    }
}

//____________________________________________________________________________
/**
 * Envoie une requête SQL au serveur de BdD en gérant les erreurs.
 *
 * En cas d'erreur, une page propre avec un message d'erreur est affichée et le
 * script est arrêté. Si l'envoi de la requête réussit, cette fonction renvoie :
 *      - un objet de type mysqli_result dans le cas d'une requête SELECT
 *      - true dans le cas d'une requête INSERT, DELETE ou UPDATE
 *
 * @param   mysqli              $bd     Objet connecteur sur la base de données
 * @param   string              $sql    Requête SQL
 *
 * @return  mysqli_result|bool          Résultat de la requête
 */
function bdSendRequest(mysqli $bd, string $sql): mysqli_result|bool {
    try{
        return mysqli_query($bd, $sql);
    }
    catch(mysqli_sql_exception $e){
        $err['titre'] = 'Erreur de requête';
        $err['code'] = $e->getCode();
        $err['message'] = $e->getMessage();
        $err['appels'] = $e->getTraceAsString();
        $err['autres'] = array('Requête' => $sql);
        bdErreurExit($err);    // ==> ARRET DU SCRIPT
    }
}

//__________________________________________________________
/**
 * Protection des sorties (chaînes d'un tableau)
 *
 * La fonction renvoie un tableau où toutes les chaines qu'il
 * contient suivant sa première dimension sont protégées, les
 * autres données du tableau ne sont pas modifiées.
 *
 * @param  array  $tab  tableau contenant des chaines à protéger
 *
 * @return array        tableau avec les chaînes protégées
 */
function tabProtegerSorties(array $tab): array {
    foreach ($tab as &$val) {
        if (is_string($val)){
            $val = htmlentities($val, ENT_QUOTES, 'UTF-8');
        }
    }
    unset ($val); // à ne pas oublier (de façon générale)
    return $tab;
}
//___________________________________________________________________
/**
 * Chiffre une valeur pour la passer dans une URL.
 *
 * @param  string		$val	La valeur à chiffrer
 *
 * @return string	La valeur chiffrée encodée URL
 */
function chiffreSigneURL(string $val) : string {
	$ivlen = openssl_cipher_iv_length($cipher='AES-128-CBC');
	$sha2len=32;
	if (! isset ($_SESSION['cle_chiffrement'])){
		$_SESSION['cle_chiffrement'] = base64_encode(
		                           openssl_random_pseudo_bytes($ivlen));
		$_SESSION['cle_hachage'] = base64_encode(
		                           openssl_random_pseudo_bytes($sha2len));
	}
	
	// -- génération du vecteur d'initialisation
	$iv = openssl_random_pseudo_bytes($ivlen);
	// -- chiffrement de $val
	$x = openssl_encrypt($val, $cipher, 
						 base64_decode($_SESSION['cle_chiffrement']),
	                     OPENSSL_RAW_DATA, $iv);
	// -- calcul de la signature de la valeur chiffrée
	$hmac = hash_hmac('sha256', $x, 
	                  base64_decode($_SESSION['cle_hachage']), true);
	
	$x = substr($hmac, 0, $sha2len/2)
	     .$iv.$x.substr($hmac, $sha2len/2);
	$x = base64_encode($x);
	return urlencode($x);
}
//___________________________________________________________________
/**
 * Déchiffre une valeur chiffrée avec la chiffreSigneURL()
 *
 * @param  string	     $x	La valeur à déchiffrer
 *
 * @return string|false	 La valeur déchiffrée ou false
 *  					 si erreur
 */
function dechiffreSigneURL(string $x) : string|false {
	$ivlen = openssl_cipher_iv_length($cipher='AES-128-CBC');
	$x = base64_decode($x);
	$sha2len=32;
	$hmac = substr($x, 0, $sha2len/2).substr($x, -$sha2len/2);
	$iv = substr($x, $sha2len/2, $ivlen);
	$x = substr($x, $sha2len/2 + $ivlen, -$sha2len/2);
	// calcul de  la signature de la chaine chiffrée reçue
	$hmacCalc = hash_hmac('sha256', $x, 
	                      base64_decode($_SESSION['cle_hachage']), true);
	if (! hash_equals($hmac, $hmacCalc)){
		return FALSE;
	}
	return openssl_decrypt($x, $cipher, 
	                       base64_decode($_SESSION['cle_chiffrement']),
	                       OPENSSL_RAW_DATA, $iv);
}
//___________________________________________________________________
/**
 * Test et affichage des correspondances à une expression régulière
 *
 * @param string	$exp	Expression régulière
 * @param string	$txt	Texte sur lequel l'expression est appliquée
 *
 * @return void
 */
function testerExp(string $exp, string $txt) : void {
	// on découpe le texte suivant l'expression régulière
	$t = preg_split($exp, $txt);

	// on affiche le résultat
	echo '<h5>Le modèle <span class="exp">', $exp, '</span> ',
			'a été trouvé ', (count($t) - 1), ' fois</h5>',
			preg_replace($exp,
						'<span class="exp">$0</span>',
						$txt);
}
//__________________________________________________________
/**
 * Test et affichage des correspondances à une expression régulière
 * appliquée sur un texte qui contient du code HTML
 *
 * L'expression régulière peut également contenir du code HTML
 *
 * @param string	$exp	Expression régulière
 * @param string	$txt	Texte sur lequel l'expression est appliquée
 *
 * @retour void
 */
function testerExpHtml(string $exp, string $txt) : void {
	// on découpe la chaîne suivant l'expression régulière
	$t = preg_split($exp, $txt);

	// on affiche le résultat
	$r = preg_replace($exp, '[span class=exp]$0[/span]', $txt);
	$r = htmlentities($r, ENT_QUOTES, 'UTF-8');
	$r = str_replace(array('[', ']'), array('<', '>'), $r);
	echo '<h5>Le modèle <span class="exp">',
          htmlentities($exp, ENT_QUOTES, 'UTF-8'), '</span> ',
			'a été trouvé ', (count($t) - 1), ' fois</h5>', $r;
}

