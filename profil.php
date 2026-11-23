<?php
require_once 'config/db.php';

if (!est_connecte()) {
    header('Location: login.php');
    exit;
}

$id_profil = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id_utilisateur'];
$id_moi = $_SESSION['id_utilisateur'];

// Récupération complète des infos utilisateur
$req = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$req->execute([$id_profil]);
$profil = $req->fetch(PDO::FETCH_ASSOC);

if (!$profil) {
    die("Profil introuvable.");
}

// Vérification du statut d'amitié
$statut_amitie = null;
if ($id_profil != $id_moi) {
    $verif = $pdo->prepare("
        SELECT statut FROM amis 
        WHERE (id_demandeur = ? AND id_recepteur = ?) 
           OR (id_demandeur = ? AND id_recepteur = ?)
    ");
    $verif->execute([$id_moi, $id_profil, $id_profil, $id_moi]);
    $res = $verif->fetch(PDO::FETCH_ASSOC);
    $statut_amitie = $res['statut'] ?? null;
}
?>
<?php include 'includes/header.php'; ?>

<div class="conteneur">
    <!-- Bouton de retour -->
    <div style="margin-bottom: 15px;">
        <?php if ($id_profil == $id_moi): ?>
            <a href="index.php" class="btn">← Retour au fil d'actualité</a>
        <?php else: ?>
            <a href="javascript:history.back()" class="btn">← Page précédente</a>
        <?php endif; ?>
    </div>

    <!-- 🎯 PARTIE INFOS PROFIL : EN HAUT -->
    <div class="carte profil-entete">
        <img src="<?= htmlspecialchars($profil['photo'] ?? 'assets/profil/default.png') ?>" class="photo-profil" alt="Photo de profil">
        
        <h2><?= htmlspecialchars($profil['prenom'] . ' ' . $profil['nom']) ?></h2>
        
        <p class="infos">
            📚 Filière : <?= htmlspecialchars($profil['filiere'] ?? 'Non renseignée') ?>
            <br>
            🎓 Promotion : <?= htmlspecialchars($profil['promotion'] ?? 'Non renseignée') ?>
        </p>
        
        <div class="bio">
            <strong>À propos :</strong>
            <p><?= nl2br(htmlspecialchars($profil['bio'] ?? 'Aucune biographie renseignée.')) ?></p>
        </div>

        <div class="actions-profil" style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
            <?php if ($id_profil != $id_moi): ?>
                <?php if ($statut_amitie === null): ?>
                    <a href="envoyer_demande.php?id=<?= $id_profil ?>" class="btn">➕ Ajouter en ami</a>
                <?php elseif ($statut_amitie === 'attente'): ?>
                    <button class="btn gris" disabled>⏳ Demande en attente</button>
                <?php elseif ($statut_amitie === 'accepte'): ?>
                    <button class="btn vert" disabled>✅ Déjà amis</button>
                    <a href="messagerie.php?dest=<?= $id_profil ?>" class="btn">💬 Envoyer un message</a>
                <?php elseif ($statut_amitie === 'refuse'): ?>
                    <a href="envoyer_demande.php?id=<?= $id_profil ?>" class="btn">🔄 Renouveler la demande</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="modifier_profil.php" class="btn">✏️ Modifier mon profil</a>
                <a href="amis.php" class="btn">👥 Mes amis</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 🎯 PARTIE PUBLICATIONS : EN BAS + PHOTO AFFICHÉE -->
    <div class="carte">
        <h3>📝 Ses publications</h3>
        <?php
        $pubs = $pdo->prepare("
            SELECT * FROM publications 
            WHERE id_auteur = ? 
            ORDER BY date_publication DESC
        ");
        $pubs->execute([$id_profil]);
        $mes_publications = $pubs->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (empty($mes_publications)): ?>
            <p style="color: #666; text-align: center;">Aucune publication pour l'instant.</p>
        <?php else: ?>
            <?php foreach ($mes_publications as $pub): ?>
            <div style="border-bottom: 1px solid #eee; padding: 15px 0;">
                <!-- Texte de la publication -->
                <p style="margin-bottom: 10px; font-size: 1.05em;"><?= nl2br(htmlspecialchars($pub['contenu'])) ?></p>
                
                <!-- Image DE LA PUBLICATION (pas la photo de profil) -->
                <?php if (!empty($pub['image']) && file_exists($pub['image'])): ?>
                    <img 
                        src="<?= htmlspecialchars($pub['image']) ?>" 
                        class="img-pub" 
                        alt="Image de la publication"
                        style="max-width: 100%; max-height: 500px; object-fit: contain; border-radius: 6px; margin: 10px 0;"
                    >
                <?php endif; ?>

                <!-- Date et statistiques -->
                <small style="color: #888; display: block; margin-top: 8px;">
                    Publié le <?= date('d/m/Y à H:i', strtotime($pub['date_publication'])) ?>
                    • ❤️ <?= $pub['nb_likes'] ?> J'aime
                </small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

