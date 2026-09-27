<?php


// chargement des bibliothèques de fonctions
require_once('bibli_cuiteur.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

$bd = bdConnect();
$titre='liste blabla';
affDebut($titre);

affTeteBl();

$sql = 'SELECT utPrenomNom, utPseudo, blTexte, blDate, blHeure FROM utilisateur INNER JOIN blabla ON blIDAuteur  WHERE utID = 7 ORDER BY blDate DESC, blHeure DESC;';

$res = bdSendRequest($bd, $sql);

if(mysqli_num_rows($res) === 0) {
    echo '<p>Cet utilisateur n\'a pas publié de blabla.</p>';

}else{
    while($t = mysqli_fetch_assoc($res)){
    affListeBl($t);
    }
}

affFin();
mysqli_free_result($res);




