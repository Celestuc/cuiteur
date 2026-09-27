#! /bin/dash

# Important : ce script n'est à utiliser que sur les machines de l'université. Cela ne servirait à rien
# d'arrêter puis de redémarrer les conteneurs sur votre machine personnelle.

# Ce script est à exécuter depuis le répertoire qui contient les fichiers Dockerfile et compose.yaml, et le répertoire app.

# construit l'image alpine-mariadb si elle n'existe pas déjà.
if ! docker image ls --format 'table' | grep -Eq 'alpine-mariadb'; then
    docker build --tag=alpine-mariadb --target=mariadb-config .
fi

# construit l'image alpine-apache-php8.3 si elle n'existe pas déjà.
if ! docker image ls --format 'table' | grep -Eq 'alpine-apache-php8.3'; then
    docker build --tag=alpine-apache-php8.3 --target=apache-php8.3-config .
fi

# arrête et supprime tous les conteneurs
IDS=$(docker ps -a | tail -n +2 | cut -d' ' -f1)
if test ${#IDS} -eq 0; then
    echo 'No services running'
else
    docker kill $IDS
    docker rm -f $IDS
fi

# suppression du volume s'il existe
VOLUME_NAME=$(docker volume ls | grep -Eo '[^[:space:]]*_db-volume')
if test -n "$VOLUME_NAME"; then
    docker volume rm $VOLUME_NAME
fi

# suppression du réseau s'il existe
NET_NAME=$(docker network ls | grep -Eo '[^[:space:]]*_net-lamp')
if test -n "$NET_NAME"; then
    docker network rm $NET_NAME
fi

# crée le volume et le réseau, et démarre les 2 conteneurs à partir du fichier compose.yaml
docker compose up -d

# gestion des permissions :
# - l'utilisateur système qui exécute les scripts PHP est l'utilisateur "apache"
# - "apache" n'est pas l'utilisateur propriétaire des scripts PHP, il n'appartient pas non plus au groupe propriétaire de ces fichiers. Les permissions de l'utilisateur "apache" pour les scripts PHP sont donc celles des autres.
chmod -R a-rwx,u+rwX,go+rX app/
