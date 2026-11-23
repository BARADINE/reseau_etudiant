-- Création de la base
CREATE DATABASE IF NOT EXISTS reseau_etudiant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reseau_etudiant;

-- Table utilisateurs
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT 'assets/profil/default.png',
    bio TEXT,
    filiere VARCHAR(100) NOT NULL,
    promotion VARCHAR(20) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table publications
CREATE TABLE publications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_auteur INT NOT NULL,
    contenu TEXT NOT NULL,
    image VARCHAR(255) NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    nb_likes INT DEFAULT 0,
    FOREIGN KEY (id_auteur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table amis/demandes
CREATE TABLE amis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_demandeur INT NOT NULL,
    id_recepteur INT NOT NULL,
    statut ENUM('attente', 'accepte', 'refuse') DEFAULT 'attente',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_demandeur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_recepteur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_demande (id_demandeur, id_recepteur)
) ENGINE=InnoDB;

-- Table messages privés
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_expediteur INT NOT NULL,
    id_destinataire INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_expediteur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_destinataire) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table commentaires (ajout obligatoire pour les interactions)
CREATE TABLE commentaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_publication INT NOT NULL,
    id_utilisateur INT NOT NULL,
    contenu TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_publication) REFERENCES publications(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table likes (pour éviter les doublons)
CREATE TABLE likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_publication INT NOT NULL,
    id_utilisateur INT NOT NULL,
    UNIQUE KEY unique_like (id_publication, id_utilisateur),
    FOREIGN KEY (id_publication) REFERENCES publications(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

