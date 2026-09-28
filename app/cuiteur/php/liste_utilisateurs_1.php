<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();

$sql = 'SELECT * FROM utilisateur ORDER BY utID';

$res = bdSendRequest($bd, $sql);

mysqli_close($bd); // fermée dès que possible

affDebut('Liste utilisateurs');

echo '<h1>Liste des utilisateurs de Cuiteur</h1>';


while($t = mysqli_fetch_assoc($res)){
    echo  '<h2>Utilisateur ', $t['utID'], '</h2>',
          '<ul>';
    unset($t['utID']);
    unset($t['utPasse']);
    $t = htmlProtegerSorties($t); // ATTENTION à ne pas oublier de protéger toutes les chaines de caractères provenant de la BdD
    foreach($t as $cle => $val){
        echo '<li>', substr($cle, 2), ' : ', $val, '</li>';
    }
    echo '</ul>';
}

mysqli_free_result($res);

affFin();
