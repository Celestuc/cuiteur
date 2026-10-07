<?php
/*********************************************************
 *        Bibliothèque de fonctions spécifiques          *
 *        à l'application Cuiteur          *
 *********************************************************/

define('BD_SERVER', 'mariadb-hostname'); // nom d'hôte ou adresse IP du serveur de base de données
define('BD_NAME', 'cuiteur_bdd'); // nom de la base sur le serveur de base de données
define('BD_USER', 'cuiteur_user'); // nom de l'utilisateur de la base
define('BD_PASS', 'cuiteur_pass'); // mot de passe de l'utilisateur de la base

define('UT_ID_CONNECTE', 7);  // à supprimer dans le projet

// constantes utilisées pour le paramètre $type de la fonction bdGetBlablas()
define('BLABLAS_CUITEUR', 1);
define('BLABLAS_REPONSES', 2);
define('BLABLA_INITIAL', 3);


//_______________________________________________________________
/**
 * Affichage du début de la page HTML, du menu et du bloc d'informations
 *
 * @param  string   $titre      la partie du titre de la page après le |, et la valeur de l'élément h1
 * @param  bool     $connecte   true si et seulement si l'utilisateur est connecté
 *
 * @return void
 */
function affDebutMenuInfos(string $titre, bool $connecte = true) : void {
    affDebut("Cuiteur | $titre", '../styles/cuiteur.css');

    echo
        '<div id="bcContenu"', $connecte ? ' class="connecte"' : '', '>',
            '<h1>', $titre, '</h1>';
    if ($connecte){
        echo
            '<nav>',
                '<ul>',
                    '<li><a href="cuiteur.php">Accueil</a></li>',
                    '<li><a href="recherche.php">Recherche</a></li>',
                    '<li><a href="compte.php">Votre profil</a></li>',
                    '<li><a href="deconnexion.php">Se déconnecter</a></li>',
                '</ul>',
            '</nav>';
        affInfos();
    }
    echo    '<main>';
}


//_______________________________________________________________
/**
 * Affichage du pied et de la fin de la page (tag fermant de l'élément main + élément footer jusqu'à la fin)
 *
 * @return void
 */
function affPiedFin() : void {
    echo
            '</main>',
            '<footer>&copy; Licence Informatique - Septembre 2026 - Tous droits réservés</footer>',
            '</div>';
    affFin();
}

//_______________________________________________________________
/**
 * Affichage du contenu statique de l'élément aside
 *
 * @return void
 */
function affInfos() : void {
    echo <<< '_HTML_'
        <aside>
            <img src="../upload/7.jpg" alt="avatar @jobs" class="avatar">@<a href="utilisateur.php?utID=7" title="Voir le profil et les blablas de @jobs"><strong>jobs</strong></a>
            Steve Jobs
            <br>
            <a href="abonnements.php?utID=7" title="Voir les personnes que je suis">0 abonnement</a>
            -
            <a href="abonnes.php?utID=7" title="Voir les personnes qui me suivent">0 abonné</a>

            <h2>Tendances</h2>

            <ul>
                <li>
                    #<a href="tendances.php?taID=murphy" title="Voir les blablas originaux contenant le tag #murphy (10)">murphy</a>
                </li>
                <li>
                    #<a href="tendances.php?taID=info" title="Voir les blablas originaux contenant le tag #info (5)">info</a>
                </li>
                <li>
                    #<a href="tendances.php?taID=intelligence" title="Voir les blablas originaux contenant le tag #intelligence (4)">intelligence</a>
                </li>
                <li>
                    #<a href="tendances.php?taID=conseil" title="Voir les blablas originaux contenant le tag #conseil (3)">conseil</a>
                </li>
                <li>
                    <a href="tendances.php">Toutes les tendances</a>
                </li>
            </ul>

            <h2>Suggestions</h2>

            <ul>
                <li>
                    <img src="../upload/3.jpg" alt="avatar @albert" class="avatar">@<a href="utilisateur.php?utID=3" title="Voir le profil et les blablas de @albert"><strong>albert</strong></a>
                    Albert Einstein
                </li>
                <li>
                    <span class="avatar">C</span>@<a href="utilisateur.php?utID=23" title="Voir le profil et les blablas de @nono"><strong>nono</strong></a>
                    Chuck Norris
                </li>
                <li>
                    <a href="suggestions.php">Plus de suggestions</a>
                </li>
            </ul>
        </aside>
_HTML_;
}

//_______________________________________________________________
/**
 * Affichage du formulaire de publication d'un nouveau blabla (blabla original ou réponse)
 *
 * @param   string  $titre          titre h2 de la section
 *
 * @return void
 */
function affFormPublier(string $titre): void{
    echo
    '<section>',
        '<h2>', $titre, '</h2>',
        '<form action="', basename($_SERVER['PHP_SELF']), '" method="post">',
            '<textarea name="txtMessage"></textarea>',
            '<footer><input type="submit" name="btnPublier" value="Publier"></footer>',
        '</form>',
    '</section>';
}


//_______________________________________________________________
/**
 * Affiche l'avatar, le pseudo sous la forme d'un lien vers la page utilisateur.php, et le prénom et le nom de l'utilisateur
 *
 * @param   int     $id         identifiant de l'utilisateur
 * @param   string  $pseudo     pseudo de l'utilisateur
 * @param   string  $prenomNom  prénom et nom de l'utilisateur
 *
 * @return void
 */
function affUtilisateur(int $id, string $pseudo, string $prenomNom) : void{
    $pseudoProtege = htmlProtegerSorties($pseudo);
    echo    htmlAvatar($id, $pseudo, $prenomNom), '@',
            htmlLien('utilisateur.php', "<strong>$pseudoProtege</strong>", ['utID'=> $id], "Voir le profil et les blablas de @$pseudoProtege"), ' ',
            htmlProtegerSorties($prenomNom);
}

//_______________________________________________________________
/**
 * Retourne le code HTML d'un avatar
 *
 * @param   int     $id         identifiant de l'utilisateur
 * @param   string  $pseudo     pseudo de l'utilisateur
 * @param   string  $prenomNom  prénom et nom de l'utilisateur
 *
 * @return string   Code HTML
*/
function htmlAvatar(int $id, string $pseudo, string $prenomNom) : string{
    if (!is_file("../upload/{$id}.jpg")) {
        $prenomNom = trim($prenomNom);
        $lettre = mb_strlen($prenomNom, encoding:'UTF-8') > 0 ?
                  htmlProtegerSorties(mb_strtoupper(mb_substr($prenomNom, 0, 1, encoding:'UTF-8'), encoding:'UTF-8')) : '?';
        return "<span class='avatar'>$lettre</span>";
    }
    $pseudoProtege = htmlProtegerSorties($pseudo);
    $refImage = "../upload/{$id}.jpg";
    return "<img src='{$refImage}' alt='avatar @{$pseudoProtege}' class='avatar'>";
}

//_______________________________________________________________
/**
 * Affiche le code HTML d'un blabla
 *
 * @param  array  $t    tableau associatif contenant les caractéristiques d'un blabla
 *                      dont les clés sont égales aux champs de la clause SELECT de la fonction bdGetBlablas()
 *
 * @return void
 */
function affUnBlabla(array $t) : void{
    affUtilisateur($t['utID'], $t['utPseudo'], $t['utPrenomNom']);
    echo
        '<p>',
            htmlProtegerSorties($t['blTexte1']),
        '</p>',
        '<footer class="footerBlabla">', dateFormat($t['blDate1']), ' à ', heureFormat($t['blHeure1']);
            if ($t['utID'] == UT_ID_CONNECTE){
                echo htmlLien('cuiteur.php', 'Supprimer', ['x' => 'XXX']);
            }
            echo htmlLien('reponses.php', "{$t['NB_REPONSES']} réponse" . ($t['NB_REPONSES'] > 1 ? 's' : ''),
                    ['blID' => $t['blID1']], 'Voir les réponses et éventuellement répondre'),
        '</footer>';
}

//_______________________________________________________________
/**
 * Affiche les blablas dans une liste non ordonnée
 *
 * @param array  $blablas   tableau à indices numériques de blablas. Les caractéristiques de chaque blabla sont mémorisées
 *                          dans un tableau associatif dont les clés sont égales aux champs de la clause SELECT de la fonction bdGetBlablas()
 *
 * @return void
 */
function affBlablas(array $blablas) : void{
    echo '<ul class="mainList">';
    if (count($blablas) == 0){
        echo '<li>Liste de blablas vide.</li>';
    }
    else{
        foreach ($blablas as $blabla) {
            echo '<li>';
            affUnBlabla($blabla);
            echo '</li>';
        }
    }
    echo '</ul>';
}

//_______________________________________________________________
/**
* Sélectionne dans la BdD le ou les blablas à afficher.
*
* @param int  $type       Indique quels sont le ou les blablas à sélectionner :
*                           - BLABLAS_CUITEUR pour les blablas du fil de l'utilisateur connecté (page cuiteur.php)
*                           - BLABLAS_REPONSES pour les réponses à un blabla initial (page reponses.php)
*                           - BLABLA_INITIAL pour sélectionner les informations d'un blabla précis (blabla initial
*                             de la page reponses.php)
* @param int  $cle        Clé utilisée dans le select suivant $type, soit
*                           - non utilisée quand $type = BLABLAS_CUITEUR
*                           - ID du blabla initial quand $type = BLABLAS_REPONSES ou BLABLA_INITIAL
*
* @return   ?array        - tableau à indices numériques de blablas (les caractéristiques de chaque blabla
*                           sont mémorisées dans un tableau associatif) quand $type = BLABLAS_CUITEUR ou BLABLAS_REPONSES
*                         - quand $type = BLABLA_INITIAL, tableau associatif contenant les caractéristiques du blabla
*                           initial, ou null si le blabla initial n'existe pas
*/
function bdGetBlablas(int $type, ?int $cle = null) : ?array {

    //-----------------------------------------------------------
    // Factorisation d'éléments communs

    // La clause SELECT des requêtes est identique partout.
    // => on la met dans une variable. En cas de modification des
    // champs sélectionnés, il y a un seul endroit à modifier.
    $select = 'SELECT   b1.blID as blID1, b1.blTexte as blTexte1, b1.blDate as blDate1, b1.blHeure as blHeure1,
                        utID, utPseudo,utPrenomNom, COUNT(b2.blID) AS NB_REPONSES';

    switch ($type) {
    case BLABLAS_CUITEUR :
        $utID = UT_ID_CONNECTE;
        $sql = "$select
                FROM    ((utilisateur INNER JOIN blabla as b1 ON b1.blIDAuteur = utID)
                LEFT OUTER JOIN blabla AS b2 ON b2.blIDParent = b1.blID)
                WHERE   utID = $utID AND b1.blIDParent IS NULL
                GROUP BY b1.blID
                UNION
                $select
                FROM    (((utilisateur INNER JOIN blabla as b1 ON b1.blIDAuteur = utID)
                INNER JOIN estabonne ON eaIDAbonne = b1.blIDAuteur)
                LEFT OUTER JOIN blabla AS b2 ON b2.blIDParent = b1.blID)
                WHERE   eaIDUtilisateur = $utID AND b1.blIDParent IS NULL
                GROUP BY b1.blID
                ORDER BY blID1 DESC";
        break;
    case BLABLAS_REPONSES:
        $sql = "$select
                FROM    ((utilisateur INNER JOIN blabla as b1 ON b1.blIDAuteur = utID)
                LEFT OUTER JOIN blabla AS b2 ON b2.blIDParent = b1.blID)
                WHERE   b1.blIDParent = $cle
                GROUP BY b1.blID
                ORDER BY blID1 DESC";
        break;

    case BLABLA_INITIAL:
        $sql = "$select
                FROM    ((utilisateur INNER JOIN blabla as b1 ON b1.blIDAuteur = utID)
                LEFT OUTER JOIN blabla AS b2 ON b2.blIDParent = b1.blID)
                WHERE   b1.blID = $cle
                GROUP BY b1.blID"; // nécessaire pour que la requête renvoie aucun enregistrement quand le blabla n'existe pas
        break;

    default:
        return null;
    }

    $res = bdSendRequest($GLOBALS['bd'], $sql);
    if ($type == BLABLA_INITIAL){
        // Dans ce cas, $res (objet de type mysqli_result) contient 1 ligne ou 0 ligne.
        // Il n'est donc pas nécessaire d'utiliser une boucle while($t = mysqli_fetch_assoc($res)) pour
        // accéder à toutes les lignes. Un seul appel de mysqli_fetch_assoc() suffit.
        // mysqli_fetch_assoc() renvoie null si le blabla initial n'existe pas dans la base de données.
        $tab = mysqli_fetch_assoc($res);
    }
    else {
        $tab = [];
        while($t = mysqli_fetch_assoc($res)){
            $tab[] = $t;
        }
    }
    mysqli_free_result($res);
    return $tab;
}

//_______________________________________________________________
/**
 * Affichage d'un message d'erreur dans une section.
 *
 * @param  string  $msg    le message d'erreur à afficher.
 * @param  string  $titre  titre h2 dans la section
 *
 * @return void
 */
function affSectionErreur(string $message, string $titre = 'Oups, il y a eu une erreur...') : void {
    echo
        '<section>',
            '<h2>', $titre, '</h2>',
            '<p class="erreur">', $message, '</p>',
        '</section>';
}

