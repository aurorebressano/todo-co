<h3>Prérequis:</h3>

PHP : ⩾ 8.1.0<br>
MySQL ⩾ 8.0.30<br>
Composer<br>
Symfony 6.3<br>
Symfony CLI<br>


<h3>Installation et configuration:</h3>

Télécharger ou cloner le repository (en ligne de commande: 'git clone https://github.com/aurorebressano/todo-co.git')<br>
Modifier les infos nécessaires dans fichier .env (notamment DATABASE_URL avec les infos de votre base de données)

En ligne de commande, jouer:<br>
->'symfony composer install --optimize-autoloader' (Installer les dépendances nécessaires à l'exécution de l'application)<br>
->'symfony console doctrine:database:create'<br>
->'symfony console doctrine:migrations:migrate --no-interaction'<br>
->'symfony console doctrine:fixtures:load --no-interaction'<br>

Cliquer sur les fichiers suivants à la racine du projet:<br>
->startup.bat (Mettre en route le serveur local)<br>
->open.bat (ouvrir le site dans le navigateur)<br>


<h3>Tests unitaires:</h3>

Il faudra, comme pour le fichier env.local, modifier le fichier env.test en renseignant le database_url de votre base de données.
Symfony ajoutera au nom de votre base le suffixe _test, aussi il faudra créer la base de données correspondante (nom de votre base + '_test').
Il faudra également modifier le fichier env.local: 'APP_ENV = test'.<br>
Hydratation de la base de données de test:<br>
En ligne de commande, jouer:<br>
->php bin/console --env=test doctrine:database:create<br>
->php bin/console --env=test doctrine:schema:create<br>
->php bin/console --env=test doctrine:fixtures:load<br>


<h3>Tests fonctionnels manuels:</h3>

Identifiants de connexion à des fins de test<br>
username: 'Admin'<br>
password: 'test'<br>


<h3>Liste des librairies:</h3>

Php Unit
Php CS Fixer
DAMA DoctrineTestBundle
