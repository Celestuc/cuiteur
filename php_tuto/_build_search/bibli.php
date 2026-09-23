<?php

function get_files(string $path = '..') : array {
    $dirs = readDirectory($path, true);
    $files = [];
    foreach($dirs as $dir){
        array_push($files, ...readDirectory($dir));
    }
    sort($files);
    return $files;
}

function readDirectory(string $path, bool $root = false) : array {
    $files = [];

    $flux = opendir($path);

    if (! $flux) {
        error_log("Error while opening \"$path\"\n", 3, ERROR_FILE);
        exit(1);
    }

    // Boucle de lecture du dossier
    while (($elem = readdir($flux)) !== false) {
        if ($elem == '.' || $elem == '..') {
            continue;
        }
        if ($root){
            if (preg_match('#php[0-9]{2}#u', $elem) && is_dir("$path/$elem")){
                $files[] = "$path/$elem";
            }
        }
        else{
            if (preg_match('#^php[0-9]{2}[a-z][0-9]+\\.html$#u', $elem) && is_file("$path/$elem")){
                $files[] = "$path/$elem";
            }
        }
    }

    // Fermeture du flux
    closedir($flux);
    return $files;
}
