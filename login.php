<?php
require_once 'config/db.php';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp_saisi = trim($_POST['mot_de_passe']);

    $req = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $req->execute([$email]);
    $utilisateur = $req->fetch(PDO::FETCH_ASSOC);

    // Vérifie bien le hachage
    if ($utilisateur && password_verify($mdp_saisi, $utilisateur['mot_de_passe'])) {
        $_SESSION['id_utilisateur'] = $utilisateur['id'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['prenom'] = $utilisateur['prenom'];
        header('Location: index.php');
        exit;
    } else {
        $erreur = "❌ Email ou mot de passe incorrect, réessaie.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="conteneur petit">
    <h2>Connexion Étudiant</h2>
    <?php if ($erreur): ?><div class="alerte erreur"><?= $erreur ?></div><?php endif; ?>
    <form method="POST">
        <input type="email" name="email" value="bacharmacki@gmail.com" required>
        <input type="password" name="mot_de_passe" placeholder="Ton mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas de compte ? <a href="inscription.php">S'inscrire</a></p>
</div>
</body>
</html>

