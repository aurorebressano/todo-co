<h1>Contribuer au projet</h1>

Cette documentation est destinée aux développeurs qui souhaitent participer à ce projet, elle expliquera en détail comment apporter des modifications au projet.
Elle détaille les étapes et les meilleures pratiques que tous les développeurs devraient suivre pour apporter des modifications au projet de manière cohérente, efficace et qualitative.
  
<h2>Flux de Travail</h2>

Tout d'abord, pour configurer le projet, vous devrez suivre les instructions du Read Me dans le dépôt GitHub du projet ici.<br>
Utilisez la commande git suivante pour cloner le dépôt du projet depuis GitHub : <br>
git clone https://github.com/aurorebressano/todo-co.git<br>

Pour commencer, créez une nouvelle branche à partir de la vôtre actuelle comme ceci :<br>
git checkout -b votreNom/nomDuSujetADevelopper<br>
Apportez vos modifications et testez vos changements localement pour vous assurer qu'ils fonctionnent comme prévu.<br>
Puis commit et push votre branche.<br>
Créez une pull request sur le GitHub du projet . Assurez-vous d'inclure une description claire de vos modifications.<br>
Ensuite, attendez la révision de votre collaborateur pour valider votre travail. Assurez-vous de répondre aux commentaires et d'apporter les ajustements nécessaires si besoin / demandé.<br>
Enfin, fusionnez votre branche dans la branche principale puis supprimez la vôtre.<br>
Après une fusion, mettez à jour votre copie locale de la branche principale : <br>
git checkout main git pull origin main <br>
<br>
Renseigner un fichier de documentation en parallèle peut s’avérer très utile et est vivement recommandé.

<h2>Conventions</h2>
  
La branche de production principale est "main", les modifications ne devraient y être fusionnées directement que dans le cas de correctifs d'urgence lorsqu'une anomalie de production critique survient. Dans la plupart des cas, les modifications doivent être fusionnées dans la branche de travail actuelle, jusqu’à ce que la pull request soit acceptée après avoir été relue, testée et validée.
Évidemment, vous devrez écrire les tests unitaires et fonctionnels liés à votre code et les lancer grâce à Php Unit pour vous assurer que tout fonctionne correctement.<br>
Les branches doivent suivre les conventions de nommage suivantes :<br>
votreNom/nomDuSujetADevelopper<br>
Et s’il s’agit d’un refix:<br>
fix/votreNom/nomDuSujetADevelopper

<h2>Suivi qualité</h2>
  
Suivre les normes de codage et de formatage pour maintenir une base de code cohérente : suivre les directives PSR pour le formatage, la structuration et le style du code.<br>
Lancer php cs fixer avant de push.<br>
Utilisez des commentaires de code clairs, des noms de variables significatifs et suivez les meilleures pratiques de développement actuelles.<br>
Mettre à jour la documentation si nécessaire.<br>
Effectuez des revues de code régulières pour assurer la qualité et la cohérence du code.<br>
Assurez-vous que les tests réussissent avant de soumettre une contribution.<br>
Utilisez des outils d'automatisation pour le formatage et les tests : cela signifie que chaque modification doit être accompagnée de tests appropriés.<br>
Fournissez des descriptions claires et concises dans les PR pour faciliter la revue.<br>

<h2>Outils obligatoires:</h2>

GitHub<br>
Php CS Fixer<br>
Php Unit<br>
Symfony Insight ou Codacy selon votre préférence<br>


