<?php

/*********************************************************
 *        Bibliothèque de fonctions génériques
 *
 * Les régles de nommage sont les suivantes.
 * Les noms des fonctions respectent la notation camel case.
 *
 * Ils commencent en général par un terme définisant le "domaine" de la fonction :
 *  aff   la fonction affiche (avec echo) du code html / texte destiné au navigateur
 *  html  la fonction renvoie du code html / texte avec une instruction return à la fonction appelante
 *  bd    la fonction gère la base de données
 *
 * Les fonctions qui ne sont utilisés que dans un seul script
 * sont définies dans ce script et les noms de ces fonctions se
 * sont suffixées avec la lettre 'L'.
 *
 *********************************************************/

define('IS_DEV', true);  //true en phase de développement, false en phase de production

if (IS_DEV){
    // Force l'affichage des erreurs
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting( E_ALL );
}

//____________________________________________________________________________
/**
 * Arrêt du script si erreur de base de données
 *
 * Affichage d'un message d'erreur, puis arrêt du script
 * Fonction appelée quand une erreur 'base de données' se produit :
 *      - lors de la phase de connexion au serveur MySQL ou MariaDB
 *      - ou lorsque l'envoi d'une requête échoue
 *
 * @param array    $err    Informations utiles pour le débogage
 *
 * @return void
 */
function bdErreurExit(array $err):void {
    ob_end_clean(); // Suppression de tout ce qui a pu être déjà généré

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
 * Ouverture de la connexion à la base de données en gérant les erreurs.
 *
 * En cas d'erreur de connexion, une page "propre" avec un message d'erreur
 * adéquat est affiché ET le script est arrêté.
 *
 * @return mysqli  objet connecteur à la base de données
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

//_______________________________________________________________
/**
 * Affichage du début de la page HTML (jusqu'au tag ouvrant de l'élément body).
 *
 * @param  string   $titre       le titre de la page
 * @param  ?string  $stylesheet  le chemin vers la feuille de style
 *
 * @return void
 */
function affDebut(string $titre, ?string $stylesheet = null) : void {
    echo
        '<!doctype html>',
        '<html lang="fr">',
        '<head>',
            '<title>', $titre, '</title>',
            '<meta charset="UTF-8">',
            $stylesheet !== null ? "<link rel='stylesheet' type='text/css' href='$stylesheet'>" : '',
        '</head>',
        '<body>';
}

//_______________________________________________________________
/**
 * Affichage de la fin de la page HTML.
 *
 * @return void
 */
function affFin() : void {
    echo
        '</body></html>';
}

//___________________________________________________________________
/**
 *  Protection des sorties (code HTML généré à destination du client).
 *
 *  Fonction à appeler pour toutes les chaines provenant de :
 *      - de saisies de l'utilisateur (formulaires)
 *      - de la bdD
 *  Permet de se protéger contre les attaques XSS (Cross site scripting)
 *  Convertit tous les caractères éligibles en entités HTML, notamment :
 *      - les caractères ayant une signification spéciales en HTML (<, >, ...)
 *      - les caractères accentués
 *
 *  Si on lui transmet un tableau, la fonction renvoie un tableau où toutes les chaines
 *  qu'il contient sont protégées, les autres données du tableau ne sont pas modifiées.
 *
 * @param  array|string  $content   la chaine à protéger ou un tableau contenant des chaines à protéger
 *
 * @return array|string             la chaîne protégée ou le tableau
 */
function htmlProtegerSorties(array|string $content): array|string {
    if (is_array($content)) {
        foreach ($content as &$value) {
            if (is_array($value) || is_string($value)){
                $value = htmlProtegerSorties($value);
            }
        }
        unset ($value); // à ne pas oublier (de façon générale)
        return $content;
    }
    // $content est de type string
    return htmlentities($content, ENT_QUOTES, encoding:'UTF-8');
}


//___________________________________________________________________
/**
 * Renvoie un tableau contenant le nom des mois (utile pour certains affichages)
 *
 * @return array    Tableau à indices numériques contenant les noms des mois
 */
function getArrayMonths() : array {
    return array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
}


//_______________________________________________________________
/**
* Transformation d'une date au format AAAAMMJJ vers le format JJ mois AAAA (1 janvier 2026)
*
* Aucune vérification n'est faite sur la validité de la date car
* on considère que c'est bien une date valide sous la forme AAAAMMJJ
*
* @param  int       $amj    La date sous la forme AAAAMMJJ
*
* @return string            La date sous la forme JJ mois AAAA
*/
function dateFormat(int $amj):string {
    $jj = (int)substr($amj, -2);
    $mm = (int)substr($amj, -4, 2);

    return $jj.' '.getArrayMonths()[$mm-1].' '.substr($amj, 0, -4); //fonctionne même si l'année est inférieure à 1000
}


//___________________________________________________________________
/**
 * Teste si une valeur est une valeur entière
 *
 * @param   mixed    $x     valeur à tester
 *
 * @return  bool     true si valeur entiere, false sinon
 */
function estEntier(mixed $x):bool {
    return is_numeric($x) && ($x == (int) $x);
}

//_______________________________________________________________
/**
* Transformation d'une heure au format HH:MM:SS vers le format HHhMM (exemple : 9h08)
*
*
* @param    string  $heure  L'heure sous la forme HH:MM:SS
*
* @return   string          L'heure sous la forme HHhMM
*/
function heureFormat(string $heure):string {

    $h = (int)substr($heure, 0, 2);
    $m = substr($heure, 3, 2);
    if (! estEntier($m)){ //$heure est une chaîne provenant de la BdD, donc méfiance
        $m = '00';
    }
    return "{$h}h{$m}";
}

//_______________________________________________________________
/**
* Retourne le code HTML d'un élément a
*
* @param string     $url            url du lien
* @param string     $supportLien    support du lien
* @param array      $queryString    couples 'cle=valeur' présents dans la query string
* @param ?string    $title          info bulle
*
* @return string    Le code HTML du lien
*
*/
function htmlLien(string $url, string $supportLien, array $queryString = [], ?string $title=null){
    $title = ($title !== null) ? " title='$title'" : '';
    $queryStringStr = '';
    if (count($queryString) > 0){
        $queryStringStr = '?';
        foreach($queryString as $cle => $val){
            $queryStringStr .= "$cle=". urlencode($val) .'&';
        }
        $queryStringStr = substr($queryStringStr, 0, -1);
    }
    return "<a href='{$url}{$queryStringStr}'{$title}>{$supportLien}</a>";
}

//___________________________________________________________________
/**
 * Contrôle des clés présentes dans les tableaux $_GET ou $_POST - piratage ?
 *
 * Soit $cles l'ensemble des clés contenues dans $_GET ou $_POST
 * L'ensemble des clés obligatoires doit être inclus dans $cles.
 * De même $cles doit être inclus dans l'ensemble des clés autorisées,
 * formé par l'union de l'ensemble des clés facultatives et de
 * l'ensemble des clés obligatoires. Si ces 2 conditions sont
 * vraies, la fonction renvoie true, sinon, elle renvoie false.
 * Dit autrement, la fonction renvoie false si une clé obligatoire
 * est absente ou si une clé non autorisée est présente; elle
 * renvoie true si "tout va bien"
 *
 * @param string    $tabGlobal          'post' ou 'get'
 * @param array     $clesObligatoires   tableau à indices numériques contenant les clés qui doivent
 *                                      obligatoirement être présentes
 * @param array     $clesFacultatives   tableau à indices numériques contenant les clés facultatives
 *
 * @return bool                         true si les paramètres sont corrects, false sinon
 */
function parametresControle(string $tabGlobal, array $clesObligatoires, array $clesFacultatives = []): bool{
    $cles = array_keys(strtolower($tabGlobal) == 'post' ? $_POST : $_GET);

    // vérifie que toutes les clés obligatoires sont présentes
    foreach($clesObligatoires as $v){
        if (! in_array($v, $cles)){
            return false;
        }
    }

    $clesAutorisees = array_merge($clesObligatoires, $clesFacultatives);
    // vérifie qu'il n'y a pas de clés non autorisées
    foreach($cles as $v){
        if (! in_array($v, $clesAutorisees)){
            return false;
        }
    }
    return true;
}
