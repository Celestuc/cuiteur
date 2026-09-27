<?php

require_once 'bib_fonctions.php';

/**
 * Fonction appelée quand une tentative de piratage est détectée
 *
 * Affiche un message et termine le script
 *
 * @return void
 */
function piratageDetecteExit() : void {
    echo '<p>Tentative de piratage ?</p>';
    htmlFin();
    exit;
}

htmlDebut('Validation de toutes les zones');

htmlInfo('Validation de toutes les zones');
// si soumission du formulaire
if (isset($_POST['btnEnvoi'])){
    if (! parametresControle('post',
            clesObligatoires:['btnEnvoi', 'hidSecret', 'txtNom',
                              'numIdentifiant', 'radNiveau',
                              'lstTP', 'txtRemarques'],
            clesFacultatives:['chkHtml', 'chkCss',
                              'chkJs', 'chkPhp'])){

        piratageDetecteExit(); //=> fin du script
    }
    // les clés sont valides

    // pour mémoriser les erreurs des utilisateurs "distraits"
    $errs = [];

    /* début du test des valeurs reçues une à une */

    // test de l'identifiant
    $id = $_POST['numIdentifiant'];
    if (estEntier($id)){
        if ($id <= 0){
            $errs[] = 'L\'identifiant doit être un entier strictement positif';
        }
    }
    else{
        $errs[] = 'L\'identifiant doit être un entier';
    }

    // test du niveau
    $niveau = $_POST['radNiveau'];
    if (! (estEntier($niveau) && estEntre($niveau, 1, 3))){
        piratageDetecteExit(); //=> fin du script
    }

    // test du nom
    $nom = trim($_POST['txtNom']);
    // c'est la longueur en nombre de caractères
    // qui nous intéresse
    $l = mb_strlen($nom, encoding:'UTF-8');
    if ($l < 2 || $l > 50) {
        $errs[] = 'Le nom doit contenir entre 2 et 50 caractères';
    }
    $noTags = strip_tags($nom);
    if ($noTags != $nom){
        $errs[] = 'Le nom ne doit pas contenir de tags HTML';
    }

    // test des remarques
    $rq = trim($_POST['txtRemarques']);
    $l = mb_strlen($rq, encoding:'UTF-8');
    if ($l > 255) {
        $errs[] = 'Le texte des remarques'.
                         ' ne doit pas contenir plus de 255 caractères';
    }
    $noTags = strip_tags($rq);
    if ($noTags != $rq){
        $errs[] = 'Le texte des remarques ne doit pas contenir de tags HTML';
    }

    // test de la liste déroulante
    if (! in_array($_POST['lstTP'],
                   ['A', 'B', 'C', 'indifférent'], true)){
        piratageDetecteExit(); //=> fin du script
    }

    // test des checkbox
    $tCheck = ['chkHtml', 'chkCss', 'chkJs', 'chkPhp'];
    foreach ($tCheck as $check){
        if (isset($_POST[$check])){
            if ($_POST[$check] !== '1'){
                piratageDetecteExit(); //=> fin du script
            }
        }
    }

    // test de l'input de type hidden
    if ($_POST['hidSecret'] !== 'pas vu'){
        piratageDetecteExit(); //=> fin du script
    }

    /* fin du test des valeurs reçues une à une */

    // affichage des résultats
    if (count($errs) > 0){
        echo '<p>Des erreurs ont été détectées :';
        foreach($errs as $err){
            echo '<br>- ', $err;
        }
        echo '</p>';
    }
    else {
        echo '<p>Ok, les données saisies sont valides.</p>';
    }

}

htmlFin();
