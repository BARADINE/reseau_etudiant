<?php
require_once 'config/db.php';
$erreur = $succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);
    $filiere = trim($_POST['filiere']);
    $promotion = trim($_POST['promotion']);

    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires";
    } else {
        $verif = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $verif->execute([$email]);
        if ($verif->rowCount() > 0) {
            $erreur = "Email déjà utilisé !";
        } else {
            // Hachage OBLIGATOIRE pour la sécurité
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $req = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, filiere, promotion) VALUES (?, ?, ?, ?, ?, ?)");
            if ($req->execute([$nom, $prenom, $email, $hash, $filiere, $promotion])) {
                $succes = "✅ Compte créé ! Tu peux te connecter maintenant.";
            } else {
                $erreur = "Erreur lors de la création du compte.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="conteneur petit">
    <h2>Créer ton compte</h2>
    <?php if ($erreur): ?><div class="alerte erreur"><?= $erreur ?></div><?php endif; ?>
    <?php if ($succes): ?><div class="alerte succes"><?= $succes ?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="email" placeholder="bacharmacki@gmail.com" required>
        <input type="password" name="mot_de_passe" placeholder="Ton mot de passe" required>
        <input type="text" name="filiere" placeholder="Ex: MIAGE" required>
        <input type="text" name="promotion" placeholder="Ex: 2026" required>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
</div>
</body>
</html>

