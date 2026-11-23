<?php require 'config/db.php';
if (!est_connecte()) header('Location: login.php');
$id_moi = $_SESSION['id_utilisateur'];
$id_dest = isset($_GET['dest']) ? (int)$_GET['dest'] : 0;

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_dest > 0) {
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        $req = $pdo->prepare("INSERT INTO messages (id_expediteur, id_destinataire, contenu) VALUES (?, ?, ?)");
        $req->execute([$id_moi, $id_dest, $contenu]);
    }
    header("Location: messagerie.php?dest=$id_dest");
    exit;
}

// Marquer les messages comme lus
if ($id_dest > 0) {
    $pdo->prepare("UPDATE messages SET lu = 1 WHERE id_expediteur = ? AND id_destinataire = ?")->execute([$id_dest, $id_moi]);
}

// Récupérer les conversations
$conv = $pdo->prepare("
    SELECT DISTINCT u.id, u.nom, u.prenom, u.photo,
    (SELECT contenu FROM messages WHERE (id_expediteur = u.id AND id_destinataire = ?) OR (id_expediteur = ? AND id_destinataire = u.id) ORDER BY date_envoi DESC LIMIT 1) AS dernier_msg,
    (SELECT date_envoi FROM messages WHERE (id_expediteur = u.id AND id_destinataire = ?) OR (id_expediteur = ? AND id_destinataire = u.id) ORDER BY date_envoi DESC LIMIT 1) AS date_dernier
    FROM messages m
    JOIN utilisateurs u ON (m.id_expediteur = u.id OR m.id_destinataire = u.id)
    WHERE u.id != ?
    ORDER BY date_dernier DESC
");
$conv->execute([$id_moi, $id_moi, $id_moi, $id_moi, $id_moi]);
$conversations = $conv->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les messages de la discussion en cours
$messages = [];
if ($id_dest > 0) {
    $req = $pdo->prepare("
        SELECT m.*, u.photo FROM messages m
        JOIN utilisateurs u ON m.id_expediteur = u.id
        WHERE (id_expediteur = ? AND id_destinataire = ?) OR (id_expediteur = ? AND id_destinataire = ?)
        ORDER BY date_envoi ASC
    ");
    $req->execute([$id_moi, $id_dest, $id_dest, $id_moi]);
    $messages = $req->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php include 'includes/header.php'; ?>
<div class="conteneur messagerie">
    <div class="colonne-gauche">
        <h3>Conversations</h3>
        <?php if (empty($conversations)): ?>
            <p>Pas encore de message</p>
        <?php else: ?>
            <?php foreach ($conversations as $c): ?>
            <a href="messagerie.php?dest=<?= $c['id'] ?>" class="conv-item <?= $id_dest == $c['id'] ? 'actif' : '' ?>">
                <img src="<?= $c['photo'] ?>" class="mini-photo">
                <div>
                    <div><strong><?= $c['prenom'] . ' ' . $c['nom'] ?></strong></div>
                    <small><?= mb_substr($c['dernier_msg'], 0, 25) ?>...</small>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="colonne-droite">
        <?php if ($id_dest > 0): ?>
            <?php $dest = $pdo->prepare("SELECT prenom, nom FROM utilisateurs WHERE id = ?"); $dest->execute([$id_dest]); $dest = $dest->fetch(); ?>
            <h3>Discussion avec <?= $dest['prenom'] . ' ' . $dest['nom'] ?></h3>
            <div class="zone-messages">
                <?php foreach ($messages as $m): ?>
                <div class="message <?= $m['id_expediteur'] == $id_moi ? 'moi' : 'lui' ?>">
                    <p><?= nl2br($m['contenu']) ?></p>
                    <small><?= $m['date_envoi'] ?></small>
                </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" class="form-message">
                <input type="text" name="contenu" placeholder="Écrivez votre message..." required>
                <button type="submit">Envoyer</button>
            </form>
        <?php else: ?>
            <div class="vide">Sélectionnez une conversation pour afficher les messages</div>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

