<?php
/*********************************************************
 *        Bibliothèque de fonctions spécifiques          *
 *        à l'application Cuiteur          *
 *********************************************************/

define('BD_SERVER', 'mariadb-hostname'); // nom d'hôte ou adresse IP du serveur de base de données
define('BD_NAME', 'cuiteur_bdd'); // nom de la base sur le serveur de base de données
define('BD_USER', 'cuiteur_user'); // nom de l'utilisateur de la base
define('BD_PASS', 'cuiteur_pass'); // mot de passe de l'utilisateur de la base

function affInfos(){
    echo    '<aside>',
            '<img src="../upload/7.jpg" alt="avatar @jobs" class="avatar">@<a href="utilisateur.php?utID=7" title="Voir le profil et les blablas de @jobs"><strong>jobs</strong></a>',
            'Steve Jobs',
            '<br>',
            '<a href="abonnements.php?utID=7" title="Voir les personnes que je suis">0 abonnement</a>',
            '-',
            '<a href="abonnes.php?utID=7" title="Voir les personnes qui me suivent">0 abonné</a>',

            '<h2>Tendances</h2>',

            '<ul>',
                '<li>',
                    '#<a href="tendances.php?taID=murphy" title="Voir les blablas originaux contenant le tag #murphy (10)">murphy</a>',
                '</li>',
                '<li>',
                    '#<a href="tendances.php?taID=info" title="Voir les blablas originaux contenant le tag #info (5)">info</a>',
                '</li>',
                '<li>',
                    '#<a href="tendances.php?taID=intelligence" title="Voir les blablas originaux contenant le tag #intelligence (4)">intelligence</a>',
                '</li>',
                '<li>',
                    '#<a href="tendances.php?taID=conseil" title="Voir les blablas originaux contenant le tag #conseil (3)">conseil</a>',
                '</li>',
                '<li>',
                    '<a href="tendances.php">Toutes les tendances</a>',
                '</li>',
            '</ul>',

            '<h2>Suggestions</h2>',

            '<ul>',
                '<li>',
                    '<img src="../upload/3.jpg" alt="avatar @albert" class="avatar">@<a href="utilisateur.php?utID=3" title="Voir le profil et les blablas de @albert"><strong>albert</strong></a>',
                    'Albert Einstein',
                '</li>',
                '<li>',
                    '<span class="avatar">C</span>@<a href="utilisateur.php?utID=23" title="Voir le profil et les blablas de @nono"><strong>nono</strong></a>',
                    'Chuck Norris',
                '</li>',
                '<li>',
                    '<a href="suggestions.php">Plus de suggestions</a>',
                '</li>',
            '</ul>',
        '</aside>';
}

function affDebutMenuInfos(string $titreh1, bool $estConnecter): void{
    $classCtn = $estConnecter ? ' class="connecte"' : '';

    echo '<div id ="bcContenu"', $classCtn, '>',
        '<h1>', $titreh1, '</h1>';

    if($estConnecter){
        echo '<nav>','<ul>','<li><a href="cuiteur.php">Accueil</a></li>
                    <li><a href="recherche.php">Recherche</a></li>
                    <li><a href="compte.php">Votre profil</a></li>
                    <li><a href="deconnexion.php">Se déconnecter</a></li>',
                    '</ul>','</nav>';

        affInfos();
    }

    echo '<main>';

}

function htmlLien(string $href, string $t, string $titre =""): string{
    $html = "<a href=\"$href\"";
    if ($titre !== ""){
        $html .= " title=\"titre\"";
    }

    $html .= ">$t</a>";

    return $html;

}

function htmlAvatar(int $idUt, string $pseudo = ""): string {
    $alt = $pseudo !== "" ? "avater @$pseudo" : "avatar";
    return "<img src=\"../upload/$idUt.jpg\" alt=\"$alt\" class=\"avatar\">";


}

function affBlablas(mysqli_result $t): void{
    if (mysqli_num_rows($t) === 0){
        echo '<p>l\'utilisateur n\'a pas encore publier de blabla.<p>';
        return;
    }

    echo '<ul class="mainList">';

    while ($row = mysqli_fetch_assoc($t)) {

        $idAuteur = (int)$row['blIDAuteur'];
        $idBlabla = (int)$row['blID'];
        $nbReponses = (int)$row['nbReponses'];
        
        $pseudo = htmlspecialchars($row['utPseudo'], ENT_QUOTES, 'UTF-8');
        $prenomNom = htmlspecialchars($row['utPrenomNom'], ENT_QUOTES, 'UTF-8');
        $texte = htmlspecialchars($row['blTexte'], ENT_QUOTES, 'UTF-8');

        $texteReponse = $nbReponses > 1 ? "$nbReponses réponses" : "$nbReponses réponse";
        
        echo '<li>';
        
        echo htmlAvatar($idAuteur, $pseudo), 
             '@', htmlLien("utilisateur.php?utID=$idAuteur", "<strong>$pseudo</strong>", "Voir le profil et les blablas de @$pseudo"), 
             "\n", $prenomNom;

        echo '<p>', $texte, '</p>';
        
        
        echo '<footer class="footerBlabla">';
        
       
        echo convertDate($row['blDate']), ' à ', convertHeure($row['blHeure']), "\n";
        
        
        if ($idAuteur === ID_USER_CONNECTER) {
            
            echo htmlLien("cuiteur.php?x=XXX", "Supprimer"), "\n";
        }
        
        echo htmlLien("reponses.php?blID=$idBlabla", $texteReponse, "Voir les réponses et éventuellement répondre");
        
        echo '</footer>';
        echo '</li>';
    }

    echo '</ul>';
}


