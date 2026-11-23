<?php if (!est_connecte()) return;
$id_user = $_SESSION['id_utilisateur'];

// Compter demandes d'amis en attente
$demandes_attente = $pdo->prepare("SELECT COUNT(*) FROM amis WHERE id_recepteur = ? AND statut = 'attente'");
$demandes_attente->execute([$id_user]);
$nb_demandes = $demandes_attente->fetchColumn();

// Compter messages non lus
$msg_non_lus = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE id_destinataire = ? AND lu = 0");
$msg_non_lus->execute([$id_user]);
$nb_msg = $msg_non_lus->fetchColumn();
?>
<nav class="navigation">
    <div class="logo">🎓 Réseau Étudiant</div>
    <div class="liens">
        <a href="index.php">Fil d'actualité</a>
        <a href="profil.php?id=<?= $id_user ?>">Mon profil</a>
        <a href="amis.php">Amis <?php if ($nb_demandes > 0): ?><span class="badge"><?= $nb_demandes ?></span><?php endif; ?></a>
        <a href="messagerie.php">Messages <?php if ($nb_msg > 0): ?><span class="badge"><?= $nb_msg ?></span><?php endif; ?></a>
        <a href="notifications.php">Notifications</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
</nav>

