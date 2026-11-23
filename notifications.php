<?php require 'config/db.php';
if (!est_connecte()) header('Location: login.php');
$id_user = $_SESSION['id_utilisateur'];
?>
<?php include 'includes/header.php'; ?>
<div class="conteneur">
    <h2>Vos Notifications</h2>

    <div class="carte">
        <h3>Demandes d'amis</h3>
        <?php
        $demandes = $pdo->prepare("
            SELECT u.prenom, u.nom, u.photo, a.date_demande
            FROM amis a JOIN utilisateurs u ON a.id_demandeur = u.id
            WHERE a.id_recepteur = ? AND statut = 'attente' ORDER BY a.date_demande DESC
        ");
        $demandes->execute([$id_user]);
        $demandes = $demandes->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if (empty($demandes)): ?>
            <p>Aucune demande en attente</p>
        <?php else: ?>
            <?php foreach ($demandes as $d): ?>
            <div class="ligne-amis">
                <img src="<?= $d['photo'] ?>" class="mini-photo">
                <span><?= $d['prenom'] . ' ' . $d['nom'] ?> vous a envoyé une demande d'amis</span>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="carte">
        <h3>Messages non lus</h3>
        <?php
        $msgs = $pdo->prepare("
            SELECT u.prenom, u.nom, m.contenu, m.date_envoi
            FROM messages m JOIN utilisateurs u ON m.id_expediteur = u.id
            WHERE m.id_destinataire = ? AND m.lu = 0 ORDER BY m.date_envoi DESC
        ");
        $msgs->execute([$id_user]);
        $msgs = $msgs->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if (empty($msgs)): ?>
            <p>Aucun message non lu</p>
        <?php else: ?>
            <?php foreach ($msgs as $m): ?>
            <div class="ligne-amis">
                <strong><?= $m['prenom'] . ' ' . $m['nom'] ?></strong>
                <p><?= mb_substr($m['contenu'], 0, 40) ?>...</p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

