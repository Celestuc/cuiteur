<?php

require_once 'bib_fonctions.php';

htmlDebut('Réception formulaire');

echo '<pre>';
htmlInfo('$_GET');
var_dump($_GET);
echo '</pre>';

echo '<hr>';

htmlInfo('$_POST');
echo '<pre>';
var_dump($_POST);
echo '</pre>';

htmlFin();
