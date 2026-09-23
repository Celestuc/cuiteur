<?php
//
// Paramètres de l'application
//

/* Une des façons les plus simples de définir des paramètres
 * est de définir des constantes car elles sont "superglobales"
 */

// Phase de développement (TRUE) ou de production (FALSE)
// Permet d'afficher des messages de débuggage (TRUE)
define('IS_DEV', TRUE);

// Paramètres base de données
define('BD_SERVER', 'mariadb-hostname');
define('BD_USER', 'tuto_user');
define('BD_PASS', 'tuto_pass');
define('BD_NAME', 'php_tuto');
