<?php

// Recherche de mots contitués de lettres, de underscores (_) et de tirets (-).
// Un tiret ne peut pas être ni le premier caractère, ni le dernier caractère du mot.

// ATTENTION : certaines occurrences peuvent être "cachées" dans les exemples

// $exp[] = '#\\$[[:alpha:]][_[:alnum:]]*#u'; // suppression des noms de variables qui commencent par une lettre
// ATTENTION finalement pas de suppression des noms de variables pour ne pas supprimer la superglobale $GLOBALS


// Info trouvée dans /etc/php/8.1/apache2/php.ini
// ; Maximum execution time of each script, in seconds
// ; https://php.net/max-execution-time
// ; Note: This directive is hardcoded to 0 for the CLI SAPI
// max_execution_time = 30

// Si temps d'exécution d’un script est trop long (supérieur à max_execution_time = 30), un lancement depuis le navigateur
// conduit à une erreur fatale. Dans ce cas, possibilité de lancer le script en ligne de commande avec :
// $ php script.php
// Encore faut-il que php soit installé sur la machine ! Ou alors, il faut aller chercher l'exécutable php dans le conteneur (pas essayé
// mais ça doit être faisable)
// eric@eric-Latitude-5590:~/travail/docker/web3/app/php_tuto/_build_search$ php build_search.php

// ATTENTION : après refactoring (dans words_files_occurrences(), chaque fichier file n'est lu qu'une seule fois
// au lieu de "nb_words" fois), le temps d'exécution est maintenant < à 30s. On peut donc lancer le script
// depuis un navigateur !!!

error_reporting(E_ALL);

require_once 'bibli.php';

define('ERROR_FILE', 'build_error.txt');
define('FILE_TO_BUILD', 'search_php.js');

$files = get_files();

echo '<pre>', print_r($files, true), '</pre>';

$words = [];
foreach($files as $file){
    array_push($words, ...file_words($file));
    sort($words);
    $words = array_unique($words);
}
remove_words_repeat_one_char($words);

//$t = file_words('../php06/php06a1.html');

echo '<pre>', print_r($words, true), '</pre>';

$occurs = words_files_occurrences($files, $words);

// echo '<pre>', print_r($occurs, true), '</pre>';

$fluxJS = fopen(FILE_TO_BUILD, 'w');
if (! $fluxJS) {
    error_log('Error while opening '. FILE_TO_BUILD . "\n", 3, ERROR_FILE);
    exit(1);
}

write_files($files);
write_options($words);
write_occurrences($occurs);

fclose($fluxJS);

echo 'End : job done !';

exit(0);

function write_files(array $files) : void {
    global $fluxJS;
    fwrite($fluxJS, 'FP.Search.fichiers = [');
    fwrite($fluxJS, '\''. preg_replace('#^.*(php[a-z0-9]+)\\.html$#u', '$1', $files[0]) . '\'');
    $n = count($files);
    for($i = 1; $i < $n; ++$i){
        fwrite($fluxJS, ',\''. preg_replace('#^.*(php[a-z0-9]+)\\.html$#u', '$1', $files[$i]) . '\'');
    }
    fwrite($fluxJS, "];\n");
}

function write_options(array &$words) : void {
    global $fluxJS;
    $firstLetters = [];
    foreach($words as $word){
        $firstLetters [] = $word[0];
    }
    //sort($firstLetters);
    $firstLetters = array_unique($firstLetters);
    foreach($firstLetters as $letter){
        fwrite($fluxJS, "FP.Search.{$letter} = {};\n");
    }
    $lastFirstLetter = null;
    foreach($words as $word){
        if($lastFirstLetter === null || $lastFirstLetter != $word[0]){
            if ($lastFirstLetter !== null){
                fwrite($fluxJS, "';\n");
            }
            fwrite($fluxJS, "FP.Search.{$word[0]} = '");
            $lastFirstLetter = $word[0];
        }
        fwrite($fluxJS, "<option value=\"{$word}\">");
    }
    fwrite($fluxJS, "';\n");
}

function write_occurrences(array &$occurs) : void {
    global $fluxJS;
    fwrite($fluxJS, 'FP.Search.mots = {');
    $n = count($occurs);
    $i = 0;
    foreach($occurs as $word => $t){
        fwrite($fluxJS, "'{$word}':[");
        $buffer = '';
        foreach($t as $cle => $value){
            $buffer .= "{$cle},{$value},";
        }
        fwrite($fluxJS, substr($buffer, 0, -1) . ']');
        ++$i;
        if ($i < $n){
            fwrite($fluxJS, ',');
        }
    }
    fwrite($fluxJS, "};\nFP.Search.tabMots = Object.keys(FP.Search.mots)\n");
}




function str_to_noaccent(string $str) : string {

    $exp = [];
    $exp[] = '#ç#u';
    $exp[] = '#è|é|ê|ë#u';
    $exp[] = '#à|á|â|ã|ä|å#u'; // '#@|À|Á|Â|Ã|Ä|Å#u';
    $exp[] = '#ì|í|î|ï#u';
    $exp[] = '#ð|ò|ó|ô|õ|ö#u';
    $exp[] = '#µ|ù|ú|û|ü#u'; // '#ù|ú|û|ü#u
    $exp[] = '#ý|ÿ#u';

    $t = [];
    $t[] = 'c';
    $t[] = 'e';
    $t[] = 'a';
    $t[] = 'i';
    $t[] = 'o';
    $t[] = 'u';
    $t[] = 'y';

//     $ret = $str;
//     $ret = preg_replace('#Ç#u', 'C', $ret);
//     $ret = preg_replace('#ç#u', 'c', $ret);
//     $ret = preg_replace('#è|é|ê|ë#u', 'e', $ret);
//     $ret = preg_replace('#È|É|Ê|Ë#u', 'E', $ret);
//     $ret = preg_replace('#à|á|â|ã|ä|å#u', 'a', $ret);
//     //$ret = preg_replace('#@|À|Á|Â|Ã|Ä|Å#u', 'A', $ret);
//     $ret = preg_replace('#À|Á|Â|Ã|Ä|Å#u', 'A', $ret);
//     $ret = preg_replace('#ì|í|î|ï#u', 'i', $ret);
//     $ret = preg_replace('#Ì|Í|Î|Ï#u', 'I', $ret);
//     $ret = preg_replace('#ð|ò|ó|ô|õ|ö#u', 'o', $ret);
//     $ret = preg_replace('#Ò|Ó|Ô|Õ|Ö#u', 'O', $ret);
//     //$ret = preg_replace('#ù|ú|û|ü#u', 'u', $ret);
//     $ret = preg_replace('#µ|ù|ú|û|ü#u', 'u', $ret);
//     $ret = preg_replace('#Ù|Ú|Û|Ü#u', 'U', $ret);
//     $ret = preg_replace('#ý|ÿ#u', 'y', $ret);
//     $ret = preg_replace('#Ý#u', 'Y', $ret);
//     return $ret;
    return preg_replace($exp, $t, $str);
}

function process_file_content(string $buffer) : string {

    $buffer = mb_strtolower($buffer, encoding:'UTF-8');

    $buffer = str_to_noaccent($buffer);

    $exp = [];
    $exp[] = '#<script\\b[^>]*>.*?</script>#su';
    $exp[] = '#<!--.*?-->#su';
    $exp[] = '#</?[^>]+>#u';

    //$exp[] = '#&lt;.+?&gt;#iu';
    $exp[] = '#&[a-z]+;#u';

    //$exp[] = '#\\$[[:alpha:]][_[:alnum:]]*#u';
    // ATTENTION pas de suppression des noms de variables pour ne pas supprimer la superglobale $GLOBALS

    //$exp[] = '#\\bbtn\\w*\\b#u';

    //$exp[] = '#\\b([a-z_])\\1+\\b#u';
    // ATTENTION : un tiret '-' valide l'assertion \b, or il ne faut pas supprimer la ss-chaine 'www' dans 'www-data' !

    //$exp[] = '#x{2,}#u';

    $ws = [ 'ait?',
            'a?lors',
            'apres',
            'au.',
            'auquel',
            'auxquel(le)s',
            'aussi',
            'autres?',
            'avec',
            'avez',
            'avons',
            'bien',
            'body',
            'cas?',
            'ces?',
            'cet(tes?)?',
            'chacune?',
            'chaque',
            'codemi',
            'comme',
            'dans',
            'des?',
            'dit(es)?',
            'don[ct]',
            'du',
            'elles?',
            'es?t?',
            'etai(en)?t',
            'etant',
            'etes',
            'etions',
            'etre',
            'eux',
            'exemples?',
            'fait(es)?',
            'fr',
            'html',
            'ils?',
            'la',
            'laquelle',
            'lequel',
            'les?',
            'ne',
            'non',
            'mal',
            'moins',
            '[mt]oi',
            'on',
            'oui?',
            'parmis?',
            'pa[rs]',
            'php',
            'plus',
            'que',
            'quel(le)?',
            'remarques?',
            'rien',
            'sans',
            'si(non)?',
            'ta',
            'ton',
            'tou(t|s|tes?)',
            'une?s?'
        ];

    foreach($ws as $w){
        $exp[] = "#\\b{$w}\\b#u";
    }
    return preg_replace($exp, ' ', $buffer);
}

function words_files_occurrences(array $files, array $words): array {
    $t = [];
    foreach ($words as $word){
        $t[$word] = [];
    }
    $nfile = 0;
    foreach($files as $file){ // pour les performances, important de lire chaque fichier une seule fois
        $buffer = read_and_process_file_content($file);
        foreach($words as $word){
            $occurrences = word_file_occur($buffer, $word);
            if ($occurrences){
                $t[$word][$nfile] = $occurrences;
            }
        }
        ++$nfile;;
    }
    foreach ($words as $word){
        if(count($t[$word]) == 0){
            error_log("Error in words_files_occurrences with word \"$word\" : not found in any file\n", 3, ERROR_FILE);
            exit(1);
        }
        arsort($t[$word], SORT_NUMERIC);
    }
    return $t;
}

function word_file_occur(string &$buffer, string $word) : int{
//     if ($word[0] == '$'){
//         $word = "\\$word";
//     }

    //$n = preg_match_all("#$word\\b#u", $buffer);
    //$n = preg_match_all("#{$word}[^[:alpha:]_]#u", $buffer);
    //$n = preg_match_all("#\\b{$word}([^[:alpha:]_]|$)#u", $buffer);
    $n = preg_match_all("#\\b{$word}\\b#u", $buffer);
    if ($n === false){
        error_log("Error in word_file_occur with word \"$word\" : preg_match_all() failure\n", 3, ERROR_FILE);
        exit(1);
    }
    return $n;
}


function read_and_process_file_content(string $path) : string {
    $buffer = file_get_contents($path);
    if ($buffer === false){
        error_log("\"$path\" could not be read\n", 3, ERROR_FILE);
        exit(1);
    }

    return process_file_content($buffer);
}

function file_words(string $path) : array {

    $buffer = read_and_process_file_content($path);

    //preg_match_all('#(\\$_)?[[:alpha:]](_?[[:alpha:]]+)+#u', $buffer, $t);
    //preg_match_all('#(\\$_)?[[:alpha:]_]{2,}#u', $buffer, $t);
    preg_match_all('#\\b[[:alpha:]_](-?[[:alpha:]_]+)+\\b#u', $buffer, $t);
    // ATTENTION un tiret '-' valide l'assertion \b, mais comme l'expression est greedy, le mot entier 'www-data' match le pattern,
    // et pas uniquement le mot 'www'. Donc fonctionne.

    $t = $t[0];
    sort($t);
    $t = array_unique($t);

    return $t;
}

function remove_words_repeat_one_char(array &$words) : void {
    foreach($words as $cle => &$word){ // pour éviter une copie du tableau
        if (preg_match('#^([a-z_])\\1+$#u', $word)){
            unset($words[$cle]);
        }
    }
    unset($word);

    $words = array_values($words); // suppression des trous
}


// $newnom = 'resultat_build_search.html';
//
// $flux = fopen($newnom, 'w');
// if ($flux === FALSE) {
//     echo 'Erreur lors de l\'ouverture de "', $newnom, '"';
//     exit();
// }
//
// fwrite($flux, $buffer);
//
// // Fermeture du flux
// $r = fclose($flux);
// if (! $r){
//     echo 'Erreur lors de la fermeture du flux';
// }
