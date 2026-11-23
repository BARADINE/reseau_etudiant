<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion base de données
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = 'localhost';
$dbname = 'reseau_etudiant';
$utilisateur = 'root';
$mdp = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $utilisateur,
        $mdp,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

// Fonction vérification connexion
if (!function_exists('est_connecte')) {
    function est_connecte() {
        return isset($_SESSION['id_utilisateur']);
    }
}

// Vérification accès
if (!est_connecte()) {
    header('Location: login.php');
    exit;
}
$id_user = $_SESSION['id_utilisateur'];

// Récupération publications : tes contenus + amis
$req = $pdo->prepare("
    SELECT p.*, u.nom, u.prenom, u.photo 
    FROM publications p
    JOIN utilisateurs u ON p.id_auteur = u.id
    WHERE p.id_auteur = ? 
       OR EXISTS (
           SELECT 1 FROM amis a 
           WHERE ((a.id_demandeur = ? AND a.id_recepteur = p.id_auteur) 
               OR (a.id_recepteur = ? AND a.id_demandeur = p.id_auteur))
           AND a.statut = 'accepte'
       )
    ORDER BY p.date_publication DESC
");
$req->execute([$id_user, $id_user, $id_user]);
$publications = $req->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Réseau Étudiant Université de N'Djamena</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Barre de navigation -->
<nav class="navigation">
    <div class="logo">🎓 Réseau Étudiant</div>
    <div class="liens">
        <a href="index.php">Accueil</a>
        <a href="profil.php">Mon profil</a>
        <a href="amis.php">Amis</a>
        <a href="messagerie.php">Messages</a>
        <a href="notifications.php">Notifications</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
</nav>

<!-- Bouton retour -->
<div class="conteneur">
    <a href="javascript:history.back()" class="btn btn-retour">← Retour</a>
</div>

<!-- Contenu principal -->
<div class="conteneur">
    <!-- Formulaire nouvelle publication -->
    <div class="carte">
        <h3>📝 Nouvelle publication</h3>
        <form action="ajouter_publication.php" method="POST" enctype="multipart/form-data">
            <textarea name="contenu" placeholder="Partagez quelque chose avec vos amis..." required rows="3"></textarea>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap: wrap;">
                <input type="file" name="image" accept="image/*" style="flex:1; min-width:200px;">
                <button type="submit">Publier</button>
            </div>
        </form>
    </div>

    <!-- Affichage publications -->
    <?php if (empty($publications)): ?>
    <div class="carte vide">
        <p>Tu n'as pas encore publié de contenu. Crée ta première publication !</p>
    </div>
    <?php else: ?>
        <?php foreach ($publications as $pub): ?>
        <div class="carte">
            <div class="auteur">
                <img src="<?= htmlspecialchars($pub['photo'] ?? 'assets/profil/default.png') ?>" class="mini-photo" alt="Photo de profil">
                <strong><?= htmlspecialchars($pub['prenom'] . ' ' . $pub['nom']) ?></strong>
                <span class="date"><?= date('d/m/Y à H:i', strtotime($pub['date_publication'])) ?></span>
            </div>

            <p style="margin:10px 0;"><?= nl2br(htmlspecialchars($pub['contenu'])) ?></p>

            <?php if (!empty($pub['image'])): ?>
            <img src="<?= htmlspecialchars($pub['image']) ?>" class="img-publication" alt="Image de la publication">
            <?php endif; ?>

            <!-- Actions Likes -->
            <div class="actions">
                <?php
                $deja_like = $pdo->prepare("SELECT id FROM likes WHERE id_publication = ? AND id_utilisateur = ?");
                $deja_like->execute([$pub['id'], $id_user]);
                $classe_like = $deja_like->rowCount() > 0 ? 'aime' : '';
                ?>
                <button class="btn-like <?= $classe_like ?>" data-id="<?= $pub['id'] ?>">
                    ❤️ <span id="likes-<?= $pub['id'] ?>"><?= $pub['nb_likes'] ?></span> J'aime
                </button>
            </div>

            <!-- Commentaires -->
            <div class="commentaires">
                <h4>💬 Commentaires</h4>
                <form action="ajouter_commentaire.php" method="POST" class="form-commentaire">
                    <input type="hidden" name="id_publication" value="<?= $pub['id'] ?>">
                    <input type="text" name="contenu" placeholder="Écrire un commentaire..." required>
                    <button type="submit">OK</button>
                </form>

                <?php
                $comms = $pdo->prepare("
                    SELECT c.*, u.prenom, u.nom, u.photo FROM commentaires c
                    JOIN utilisateurs u ON c.id_utilisateur = u.id
                    WHERE c.id_publication = ? ORDER BY c.date_commentaire ASC
                ");
                $comms->execute([$pub['id']]);
                $tous_comms = $comms->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (empty($tous_comms)): ?>
                <p style="color:#888; font-size:0.9em;">Aucun commentaire pour l'instant</p>
                <?php else: ?>
                    <?php foreach ($tous_comms as $c): ?>
                    <div class="un-commentaire">
                        <img src="<?= htmlspecialchars($c['photo'] ?? 'assets/profil/default.png') ?>" class="mini-photo" alt="Profil">
                        <div>
                            <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong>
                            <p><?= htmlspecialchars($c['contenu']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>

