<h3>Prérequis:</h3>h3>

PHP : ⩾ 8.1.0
MySQL ⩾ 8.0.30
Composer
Symfony 6.3
Symfony CLI


<h3>Installation et configuration:</h3>h3>

Télécharger ou cloner le repository (en ligne de commande: 'git clone https://github.com/aurorebressano/todo-co.git')
Modifier les infos nécessaires dans fichier .env (notamment DATABASE_URL avec les infos de votre base de données)

En ligne de commande, jouer:
->'symfony composer install --optimize-autoloader' (Installer les dépendances nécessaires à l'exécution de l'application)
->'symfony console doctrine:database:create'
->'symfony console doctrine:migrations:migrate --no-interaction'
->'symfony console doctrine:fixtures:load --no-interaction'

Cliquer sur les fichiers suivants à la racine du projet:
->startup.bat (Mettre en route le serveur local)
->open.bat (ouvrir le site dans le navigateur)


<h3>Tests unitaires:</h3>

Il faudra, comme pour le fichier env.local, modifier le fichier env.test en renseignant le database_url de votre base de données.
Symfony ajoutera au nom de votre base le suffixe _test, aussi il faudra créer la base de données correspondante (nom de votre base + '_test').
Il faudra également modifier le fichier env.local: 'APP_ENV = test'.
Hydratation de la base de données de test:
En ligne de commande, jouer:
->php bin/console --env=test doctrine:database:create
->php bin/console --env=test doctrine:schema:create
->php bin/console --env=test doctrine:fixtures:load


<h3>Tests fonctionnels manuels:</h3>

Identifiants de connexion à des fins de test
username: 'Admin'
password: 'test'


<h3>Liste des librairies:</h3>

Php Unit
Php CS Fixer
DAMA DoctrineTestBundle
