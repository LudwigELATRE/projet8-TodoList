# ✅ ToDo & Co – Application Symfony de gestion de tâches

ToDo & Co est une application Symfony permettant aux utilisateurs de gérer leurs tâches quotidiennes.  
Elle intègre un système d'authentification avec gestion des rôles (`ROLE_USER`, `ROLE_MANAGER`, `ROLE_ADMIN`) et un ensemble de règles d'autorisation.

---

## ⚙️ Prérequis

Avant de commencer, assurez-vous d’avoir installé :

- PHP >= 8.1
- Composer
- Symfony CLI (`symfony`)
- Docker + Docker Compose
- Make (`make` installé sur votre OS)

---

## 🚀 Installation du projet

Clonez ce dépôt :

```bash
git clone https://github.com/<votre-utilisateur>/todo-co.git
cd todo-co
```

Installez les dépendances :

```bash
composer install
```

Copiez le fichier `.env` :

```bash
cp .env .env.local
```

Configurez les accès à la base de données dans `.env.local` :

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/todo_co_db"
```

Lancez la base de données :

```bash
make start-db
```

Lancez les migrations :

```bash
make migrate
```

---

## ▶️ Lancer l'application

Démarrez le serveur Symfony avec :

```bash
make start
```

L'application est accessible à l'adresse : [http://localhost:8000](http://localhost:8000)

Pour arrêter le serveur :

```bash
make stop
```

---

## 📌 Fonctionnalités principales

- Inscription et connexion des utilisateurs
- Gestion des rôles : `ROLE_USER`, `ROLE_MANAGER`, `ROLE_ADMIN`
- Attribution automatique de l’utilisateur connecté à la tâche créée
- Affichage et gestion des tâches selon les droits
- Suppression des tâches uniquement par l’auteur
- Suppression des tâches anonymes uniquement par les administrateurs et managers
- Accès aux pages d'administration restreint aux administrateurs
- Sélection et modification des rôles utilisateurs (ADMIN uniquement vers admin ou manager)

---

## 🔐 Authentification

Le projet utilise le système de sécurité de Symfony :

- Stockage des utilisateurs dans la base de données (Entity `User`)
- Authentification via formulaire de login
- Attribution des rôles lors de la création ou modification d’un utilisateur
- Droits gérés avec un système de `Voter` (ex. : suppression de tâche)
- Encodage des mots de passe avec `UserPasswordHasher`

---

## 🧪 Tests automatisés

L’application utilise `PHPUnit` pour les tests automatisés.

Exécution des tests + chargement des fixtures + rapport de couverture :

```bash
make test
```

Cela effectue :

- Le chargement des fixtures en base de test
- L’exécution de tous les tests unitaires et fonctionnels
- La génération d’un rapport HTML dans : `var/coverage/index.html`

### Objectif qualité

- Couverture minimale visée : **70%**
- Cas critiques testés :
    - Accès rôles / droits
    - Création et suppression de tâches
    - Modification de rôle utilisateur
    - Suppression conditionnelle des tâches “anonymes”

---

## 🛠 Commandes Make disponibles

| Commande             | Description                                                                 |
|----------------------|-----------------------------------------------------------------------------|
| `make start`         | Démarre le serveur Symfony en arrière-plan                                 |
| `make stop`          | Arrête le serveur Symfony                                                   |
| `make start-db`      | Lance les conteneurs Docker (ex : base de données)                          |
| `make migrate`       | Exécute les migrations Doctrine dans le conteneur Docker                    |
| `make test`          | Charge les fixtures de test, lance les tests PHPUnit, génère la couverture |

---

## 🗂 Structure du projet

- `src/` – Code source Symfony
- `tests/` – Tests unitaires et fonctionnels
- `config/` – Fichiers de configuration
- `public/` – Point d’entrée de l’application
- `Makefile` – Commandes automatisées
- `docker-compose.yml` – Configuration Docker

---

## 📊 Audit & Qualité

Des audits ont été réalisés avec :

- ✅ **Codacy** ou **SonarCloud** : audit qualité du code
- ✅ **Symfony Profiler** : audit des performances
- ✅ **PHPStan** : vérification statique du code

Un rapport d’audit complet est fourni dans le dossier `/docs`.

---

## 🤝 Contribution

Consultez le fichier `CONTRIBUTING.md` pour connaître :

- les conventions de développement ;
- les règles de nommage ;
- le workflow Git ;
- les étapes de contribution.

---

## 📄 Licence

Projet à but pédagogique – OpenClassrooms – Tous droits réservés.
