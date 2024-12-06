# SOLUTIONS

## Vulnérabilités

### 1. Default Credentials

**Description :**
L'application utilise des identifiants par défaut pour certains services, tels que l'utilisateur `admin` avec le mot de passe `password123`. Ces identifiants par défaut sont souvent bien connus et peuvent être facilement devinés par les attaquants.

**Fonctionnalité :**
Lors de l'authentification, si un attaquant utilise les identifiants par défaut, il peut accéder aux fonctionnalités administratives ou sensibles de l'application.

**Exploitation :**
Un attaquant peut simplement essayer les identifiants par défaut pour accéder à l'application. Par exemple, en utilisant `admin` et `password123` sur la page de connexion.

### 2. Path traversals / LFI

**Description :**
L'application permet aux utilisateurs de télécharger des fichiers sans vérifier correctement le chemin du fichier demandé. Cela peut permettre à un attaquant de lire des fichiers sensibles sur le serveur.

**Fonctionnalité :**
En manipulant les paramètres de la requête, un attaquant peut accéder à des fichiers en dehors du répertoire prévu, tels que `/etc/passwd`.

**Exploitation :**
Un attaquant peut envoyer une requête avec un chemin de fichier malveillant, par exemple `../../etc/passwd`, pour lire le contenu du fichier `/etc/passwd`.

### Remote code execution

**Description :**
L'application permet l'exécution de code à distance en raison d'une validation insuffisante des entrées utilisateur. Cela peut permettre à un attaquant d'exécuter des commandes arbitraires sur le serveur.

**Fonctionnalité :**
Dans le fichier `requests.php`, la fonction `dLFile` prend un chemin de fichier en entrée sans validation suffisante.

**Exploitation :**
Dans la fonction `dLFile`, un attaquant pourrait manipuler le paramètre file pour inclure des chemins de fichiers arbitraires, ce qui pourrait permettre l'accès à des fichiers sensibles ou l'exécution de commandes malveillantes.

### Bibliothèque vulnérable

**Description :**
L'application utilise des bibliothèques connues pour avoir des vulnérabilités.

**Fonctionnalité :**
Les librairies MD5 et libxml sont utilisées.

**Exploitation :**
Il est possible de retrouver les messages encodés avec MD5. L'attaquant peut facilement décoder le message. Pour libxml, l'attaque XXE est possible permettant de lire des fichiers arbitraires sur le système de fichier.



### IDOR

**Description :**
L'application permet aux utilisateurs d'accéder ou de manipuler des ressources auquelles ils ne devraient pas avoir accès en modifiant une entrée d'une requête.

**Fonctionnalité :**
Dans le fichier `requests.php`, la fonction `getinfoUser` récupère les informations de l'utilisateur enregistré. Il n'y a pas de vérification pour s'assurer que l'utilisateur authentifié est autorisé à accéder à ces informations.

**Exploitation :**
Un attaquant pourrait manipuler les paramètres de la requête pour accéder aux informations d'autres utilisateurs et accéder à des pages non autorisées: admin.hmtl


### Exposition de mot de passe encodé

**Description :**
Dans le code de l'application dans le fichier `requests.php` le mot de passe est encodé en base64, ce qui l'expose directement.

**Fonctionnalité :**
Un attaquant peut réussir à retrouver le mot de passe en le décodant.

**Exploitation :**
En interceptant le mot de passe encodé en base64, l'attaquant le décode et peut accéder au compte.


### Injection SQL
**Description :**
L'application permet de renseigner des transactions sur la page `synthese.html`. Le champ transactions n'est pas sécurisé permettant à un utilisateur d'éxecuter du code.

**Fonctionnalité :**
En renseignant du code SQL à la place de texte, l'attaquant va pouvoir accéder aux informations de la base de données.

**Exploitation :**
En injectant une commande SQL dans le champ de texte, le serveur va executer la commande vers la base de données client. Le résultat est affiché dans la partie transactions de la page.


### Entité externe XML (XXE)
**Description :**
L'application accepte des fichiers XML pour traiter certaines données. Cependant, elle ne désactive pas la résolution des entités externes, ce qui permet à un attaquant d'injecter des entités externes malveillantes.

**Fonctionnalité :**
En soumettant un fichier XML contenant une entité externe dans l'upload de fichier, l'attaquant peut forcer l'application à lire des fichiers arbitraires sur le serveur ou à effectuer des requêtes réseau non autorisées.

**Exploitation :**
En uploadant un fichier XML modifié à l'application, l'attaquant peut accéder à des fichiers sensibles sur le serveur.