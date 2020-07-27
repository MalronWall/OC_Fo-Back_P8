# Contributions au projet “To Do List”


1. Règles de contribution
    
    1. Utilisation de github

        Le lien vers le repository est : [https://github.com/MalronWall/OC_Fo-Back_P8/](https://github.com/MalronWall/OC_Fo-Back_P8/).
        
        Pour contribuer au projet il sera nécessaire de faire un Fork du projet avec le bouton situé en haut à droite de l’écran.

    1. Utilisation d’une nouvelle branche
    
        Il faudra ensuite créer une nouvelle branche à partir de la branche “development”.
        La branche a comme règle de nommage :
        
        → nom_de_la_feature_concernée/modification_proposée
        
        → exemple : task/refactoring_view

        Liste des features actuelles :
        - task
        - user
        - security

    1. Création d’une “Pull requests”

        Lorsque vous pensez avoir fini les modifications que vous souhaitiez faire. Proposez une Pull requests, avec l’onglet en haut à gauche, en direction de la branche “development” du dépôt principal.
        
        La Pull requests doit être rédigée en anglais. Le titre doit être parlant et résumer les modifications proposées. Le contenu doit être plus complet : expliquer les changements proposés, les améliorations visées et/ou le bug qui seront corrigés.

    1. Validation ou abandon

        Une fois votre Pull requests proposée. Il suffira d’attendre qu’un responsable de l’équipe de développement la valide ou la clôture sans la “merger” sur la branche ”development”.
        
        Dans le cas d’une clôture sans validation, une explication de l’abandon sera donnée dans les commentaires de votre Pull request.

1. Les bonnes pratiques

    1. Normes de codage
    
        Il est important d'avoir un code bien soigné afin d'avoir un lisibilité optimale et que chaque puisse s'y retrouver en lisant votre code.
        Ainsi, il est important de respecter quelques pratiques !
    
        1. Structure du code
            
            - ajoutez un espace après chaque séparateur de virgule
            - ajoutez un seul espace autour des opérateurs binaires (==, &&, ...) sauf pour la concaténation "."
            - placez les opérateurs unaires (!, --, ...) à côté de la variable affectée
            - utilisez toujours une comparaison identique (===) sauf si vous avez besoin de jongler avec les types
            - utilisez des conditions Yoda ("stable" === $var) pour éviter des affectation accidentelles
            - ajoutez une virgule après CHAQUE élément d'un tableau, même la dernière
            - ajoutez une ligne vide avant les `return` sauf s'il est seul à l'intérieur de son groupe d'instruction
            - utilisez `return null;` quand la fonction retourne explicitement un `null` et utilisez `return;` quand la fonction retourne des valeurs `void`
            - utilisez des accolades pour indiquer le corps de la structure de contrôle quel que soit le nombre d'instructions qu'elle contient
            - définissez une classe par fichier
            - déclarez l'héritage de classe et toutes les interfaces implémentées sur la même ligne que le nom de la classe
            - déclarez les propriétés de classe avant les méthodes
            - déclarez d'abord les méthodes publiques, puis les méthodes protégées et enfin les méthodes privées. Les exceptions à cette règle sont le constructeur de classe et les méthodes `setUp()` et `tearDown()` des tests PHPUnit qui doivent toujours être les premières méthodes pour augmenter la lisibilité
            - déclarez tous les arguments sur la même ligne que le nom de la méthode / fonction, quel que soit le nombre d'arguments
            - utilisez des parenthèses lors de l'instanciation des classes, quel que soit le nombre d'arguments du constructeur
            - les chaînes d'exception et de message d'erreur doivent être concaténées en utilisant `sprintf`
            - ne pas utiliser `else`, `elseif`, `break` après les `if` et les `case` conditions
            - n'utilisez pas d'espaces autour de `[` `]`
            - ajoutez une `use` instruction pour chaque classe qui ne fait pas partie de l'espace de noms global
            - lorsque les balises PHPDoc comme `@param` ou `@return` incluent `null` et d'autres types, placez toujours `null` à la fin de la liste des types
            
        1. Conventions de nommage
        
            - utilisez camelCase pour les variables PHP, les noms de fonctions et de méthodes et les arguments (par exemple `$acceptableContentTypes`, `hasSession()`)
            - utilisez snake_case pour les paramètres de configuration et des variables de modèle Twig (par exemple `framework.csrf_protection`, `http_status_code`)
            - utilisez des espaces de noms pour toutes les classes PHP et UpperCamelCase pour leurs noms (par exemple `ConsoleLogger`)
            - préfixez toutes les classes abstraites avec `Abstract` sauf dans PHPUnit `*TestCase`
            - suffixez les interfaces avec `Interface`
            - suffixez les traits avec `Trait`
            - suffixez les exceptions avec `Exception`
            - utilisez UpperCamelCase pour nommer les fichiers PHP (par exemple `EnvVarProcessor.php`) et le cas snake pour nommer les modèles Twig et les ressources Web (par exemple `section_layout.html.twig`, `index.scss`)
            - pour les indications de type dans PHPDocs et le cast, utilisez `bool` (au lieu de `boolean` ou `Boolean`), `int` (au lieu de `integer`), `float` (au lieu de `double` ou `real`)
            
        1. Convention de nommage des services
        
            - un nom de service doit être le même que son nom complet (par exemple `App\EventSubscriber\UserSubscriber`)
            - s'il existe plusieurs services pour la même classe, utilisez le nom complet pour le service principal et utilisez des noms en minuscules et soulignés pour le reste des services. Divisez-les éventuellement en groupes séparés par des points (par exemple `something.service_name`, `fos_user.something.service_name`)
            - utilisez des lettres minuscules pour les noms de paramètres (sauf lorsque vous vous référez aux variables d'environnement avec la syntaxe `%env(VARIABLE_NAME)%`)
            - ajoutez des alias de classe pour les services publics (par exemple, un alias `Symfony\Component\Something\ClassName` vers `something.service_name`)
            
        1. Documentation
            
            - ajoutez des blocs PHPDoc pour toutes les classes, méthodes et fonctions (bien que vous puissiez être invité à supprimer PHPDoc qui n'ajoute pas de valeur)
            - regroupez les annotations de sorte que les annotations du même type se suivent immédiatement et que les annotations d'un type différent soient séparées par une seule ligne vierge
            - mettez la `@return` balise si la méthode ne renvoie rien
            - les annotations `@package` et `@subpackage` ne sont plus utilisées
            - ne pas insérer les blocs PHPDoc, même s'ils ne contiennent qu'une seule balise (par exemple, ne pas insérer une seule ligne) `/** {@inheritdoc} */`
            - lors de l'ajout d'une nouvelle classe ou lors de modifications significatives d'une classe existante, une `@author` balise avec des informations de contact personnelles peut être ajoutée ou développée
            
    1. Conventions
        
        Lorsqu'un objet a une relation multiple «principale» avec des «choses» liées (objets, paramètres, …), les noms de méthodes sont normalisés:
        
        - `get()`
        - `set()`
        - `has()`
        - `all()`
        - `replace()`
        - `remove()`
        - `clear()`
        - `isEmpty()`
        - `add()`
        - `register()`
        - `count()`
        - `keys()`
        
        L'utilisation de ces méthodes n'est autorisée que lorsqu'il est clair qu'il existe une relation principale :
        
        - une `CookieJar` a de nombreux `Cookie` objets
        - un service `Container` a de nombreux services et de nombreux paramètres (comme les services sont la relation principale, la convention est utilisée pour cette relation)
        - une console `Input` a de nombreux arguments et de nombreuses options. Il n'y a pas de relation «principale», et donc la convention ne s'applique pas
        
        Pour de nombreuses relations où la convention ne s'applique pas, on peut compléter le nom de la méthode par le nom de l'objet associé (par exemple pour `get()` ce sera `getObject()`)
        
    1. Les tests
    
        Les projets Symfony utilisent PhpUnit qui exécute des tests afin de garder le code pérenne dans le temps. Si le nouveau code rompt un test, la console affichera un message d'erreur avec le nom du ou des tests qui ne sont pas passés.
        
        Dans tous les cas, c'est une bonne pratique d'exécuter des tests pour vérifier que vous n'avez rien cassé.
        
        Commande a lancer pour exécuter les tests : `php bin/phpunit`