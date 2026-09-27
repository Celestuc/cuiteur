<?php
//$serveurTP_L2 = '172.20.128.72';

if (!isset($_POST['txtCode'])) {
	exit;
}

$code = $_POST['txtCode'];

//if (function_exists('get_magic_quotes_gpc')) {
//	(get_magic_quotes_gpc() == 1) && $Code = stripslashes($Code);
//}

$code = trim($code);

// if (strpos($code, 'unlink') !== false) {
// 	$code = 'L\'utilisation de unlink() dans cet environnement n\'est pas possible.';
// } elseif (strpos($code, 'rmdir') !== false) {
// 	$code = 'L\'utilisation de rmdir() dans cet environnement n\'est pas possible.';
// }


$code = '<?php header(\'Content-Type: text/html; charset=UTF-8\'); ini_set(\'display_errors\', \'1\'); ini_set(\'display_startup_errors\', \'1\'); error_reporting(E_ALL); ?>'.$code;


$leFichier = str_replace('.', '_', $_SERVER['REMOTE_ADDR']);
$leFichier = str_replace(':', '_', $leFichier);
$leFichier = str_replace(' ', '_', $leFichier);

//$leFichier = ($_SERVER['SERVER_ADDR'] == $serveurTP_L2)
//				? "../../test/$leFichier.php"
//				: "../test/$leFichier.php";

$leFichier = "../test/$leFichier.php";

$F = @fopen($leFichier, 'wb');
if ($F){
	fwrite($F, $code);
	fclose($F);

	chmod($leFichier, 0660);
}
// On renvoie le nom du fichier qui sera "exécuté" dans la
// fenêtre de test
$leFichier = substr($leFichier, 3);	//si frames

echo $leFichier;

