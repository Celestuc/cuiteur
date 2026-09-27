<?php

require_once 'bib_fonctions.php';

htmlDebut('Réception formulaire');

htmlInfo('Pour débuggage');
echo '<pre>';
htmlInfo('$_POST');
var_dump($_POST);
echo '</pre>';

htmlInfo('Pour tester la fonction parametresControle() ...');
// si soumission du formulaire
if (isset($_POST['btnEnvoi'])){
    if (! parametresControle('post',
            clesObligatoires:['btnEnvoi', 'hidSecret', 'txtNom',
                              'numIdentifiant', 'radNiveau',
                              'lstTP', 'txtRemarques'],
            clesFacultatives:['chkHtml', 'chkCss',
                              'chkJs', 'chkPhp'])){
        echo '<p>Tentative de piratage ?</p>';
    }
    else {
        echo '<p>Ok, les clés sont valides.</p>';
    }
}
else {
  echo '<p>Le script est appelé via une requête HTTP GET.</p>';
}

htmlFin();
