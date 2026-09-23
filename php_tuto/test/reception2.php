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

htmlDebut('Validation des zones numériques');

htmlInfo('Validation des zones numériques');
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

    // les tests suivants seront placés ici
    // ...


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
