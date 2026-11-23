# 🎓 Réseau Étudiant — Université de N'Djamena

Un réseau social interne pour les étudiants de l'Université de N'Djamena, permettant de publier des contenus, d'ajouter des amis, de commenter et d'échanger.

---

## 📋 Fonctionnalités

- 🔐 **Inscription & Connexion** sécurisée
- 📝 **Publications** texte + images
- ❤️ **Système de likes**
- 💬 **Commentaires** sur les publications
- 👥 **Liste d'amis** (demandes & acceptations)
- 👤 **Profil utilisateur** avec photo et informations
- 🔔 **Notifications**
- ✉️ **Messagerie entre amis**

---

## 🛠️ Technologies utilisées

| Outil | Version |
|-------|---------|
| PHP | 7.4+ |
| MySQL | 5.7+ / MariaDB |
| HTML5 | — |
| CSS3 | — |
| JavaScript | Basique |
| Serveur | Apache (XAMPP) |

---

## 📂 Structure du projet
reseau_etudiant/
├── index.php                  # Page d'accueil / Fil d'actualité
├── login.php                  # Page de connexion
├── inscription.php            # Page d'inscription
├── profil.php                 # Profil utilisateur
├── amis.php                   # Gestion des amis & demandes
├── messagerie.php             # Messagerie privée
├── notifications.php          # Notifications
├── deconnexion.php            # Déconnexion
├── ajouter_publication.php    # Traitement publication
├── ajouter_commentaire.php    # Traitement commentaire
├── config/
│   └── db.php                 # Connexion base de données
├── assets/
│   ├── css/
│   │   └── style.css          # Feuille de style
│   ├── js/
│   │   └── script.js          # Scripts côté client
│   ├── profil/                # Photos de profil
│   └── publications/          # Images des publications
└── reseau_etudiant.sql        # Base de données (structure)
 


## ⚙️ Installation locale

### Étape 1 — Prérequis
- Installer **XAMPP** (disponible sur [apachefriends.org](https://www.apachefriends.org/))
- Démarrer **Apache** et **MySQL** dans le panneau XAMPP

### Étape 2 — Base de données
1. Ouvrir `phpmyadmin` : [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Créer une base de données nommée : `reseau_etudiant`
3. Importer le fichier `reseau_etudiant.sql` fourni dans le projet

### Étape 3 — Configuration
Ouvrir `config/db.php` et vérifier les identifiants :
```php
$host = 'localhost';
$dbname = 'reseau_etudiant';
$utilisateur = 'root';
$mdp = '';
 
 
Étape 4 — Accès local
 
Placer le dossier du projet dans :
  
C:\xampp\htdocs\reseau_etudiant\
 
 
Puis ouvrir dans le navigateur :
    
http://localhost/reseau_etudiant/
 
 
 
 

 
👤 Auteur
 
- Nom : BARADINE BRDN BIN ATYA
- Formation : Étudiant à l'Université de N'Djamena
- Projet : Réalisé dans le cadre des études
 

 
 



