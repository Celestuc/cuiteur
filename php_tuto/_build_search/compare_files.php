<?php

// $ php compare_files.php
// Files with different content :
// ../php07/php07b1.html - /var/www/html/php_tuto/php07/php07b1.html
// ../php07/php07b2.html - /var/www/html/php_tuto/php07/php07b2.html
// ../php07/php07b3.html - /var/www/html/php_tuto/php07/php07b3.html

// ATTENTION : d'autres fichiers diffèrent notamment ceux concernant les bibliothèques !


error_reporting(E_ALL);

require_once 'bibli.php';

define('ERROR_FILE', 'compare_error.txt');

$files1 = get_files();

$files2 = get_files('/var/www/html/php_tuto');

$n1 = count($files1);
$n2 = count($files2);

if ($n1 != $n2){
    echo 'Error : not the same number of files : ', $n1, ' vs ', $n2, "\n";
    exit(1);
}


echo 'Files with different content : ', "\n";
for($i = 0; $i < $n1; ++$i){
    $buffer1 = file_get_contents($files1[$i]);
    $buffer2 = file_get_contents($files2[$i]);
    if ($buffer1 !== $buffer2){
        echo $files1[$i], ' - ', $files2[$i], "\n";
    }
}

exit(0);

