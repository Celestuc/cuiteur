<?php

// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();

define('ID_USER_CONNECTER', 7);

affDebut('Cuiteur | Accueil','../styles/cuiteur.css');

affDebutMenuInfos('Accueil', true);

echo '<section>',
        '<h2>Publication d\'un nouveau blabla</h2>',
        '<form action="cuiteur.php" method="post">',
            '<textarea name="txtMessage"></textarea>',
            '<footer>',
                '<input type="submit" name="btnPublier" value="Publier">',
            '</footer>',
        '</form>',
      '</section>';

$sql = $sql = "
    (
        SELECT b.blID, b.blTexte, b.blDate, b.blHeure, b.blIDParent, b.blIDAuteur , u.utPseudo, u.utPrenomNom,
               (SELECT COUNT(*) FROM blabla rep WHERE rep.blIDParent = b.blID) AS nbReponses
        FROM blabla b
        INNER JOIN utilisateur u ON b.blIDAuteur = u.utID
        WHERE b.blIDAuteur = " . ID_USER_CONNECTER . "
    )
    UNION
    (
        SELECT b.blID, b.blTexte, b.blDate, b.blHeure, b.blIDParent, b.blIDAuteur, u.utPseudo, u.utPrenomNom,
               (SELECT COUNT(*) FROM blabla rep WHERE rep.blIDParent = b.blID) AS nbReponses
        FROM blabla b
        INNER JOIN utilisateur u ON b.blIDAuteur = u.utID
        INNER JOIN estabonne a ON b.blIDAuteur = a.eaIDUtilisateur
        WHERE a.eaIDAbonne = " . ID_USER_CONNECTER . "
    )
    ORDER BY blDate DESC, blHeure DESC
";
$res = bdSendRequest($bd, $sql);

affBlablas($res);
mysqli_free_result($res);

echo    '</main>',
        '<footer>',
            '&copy; Licence Informatique - Septembre 2026 - Tous droits réservés',
        '</footer>',
    '</div>';

affFin();
