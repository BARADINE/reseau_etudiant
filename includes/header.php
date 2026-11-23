<?php
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réseau Social Étudiant - Université de N'Djamena</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if (est_connecte()): ?>
<!-- Barre de navigation -->
<nav class="navigation">
    <div class="logo">🎓 Réseau Étudiant</div>
    <div class="liens">
        <a href="index.php">Fil d'actualité</a>
        <a href="profil.php">Mon profil</a>
        <a href="amis.php">Amis</a>
        <a href="messagerie.php">Messages</a>
        <a href="notifications.php">Notifications</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
</nav>

<!-- 🎯 BOUTON DE RETOUR PRÉSENT SUR TOUTES LES PAGES -->
<div class="conteneur" style="margin-top: 15px;">
    <a href="javascript:history.back()" class="btn btn-retour">← Retour</a>
</div>
<?php endif; ?>

<main>

