<?php require 'config/db.php';
if (!est_connecte()) header('Location: login.php');
$id_moi = $_SESSION['id_utilisateur'];

// Traitement des actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_ami = (int)$_GET['id'];
    if ($_GET['action'] === 'accepter') {
        $req = $pdo->prepare("UPDATE amis SET statut = 'accepte' WHERE id_demandeur = ? AND id_recepteur = ?");
        $req->execute([$id_ami, $id_moi]);
    } elseif ($_GET['action'] === 'refuser') {
        $req = $pdo->prepare("DELETE FROM amis WHERE id_demandeur = ? AND id_recepteur = ?");
        $req->execute([$id_ami, $id_moi]);
    } elseif ($_GET['action'] === 'supprimer') {
        $req = $pdo->prepare("DELETE FROM amis WHERE (id_demandeur = ? AND id_recepteur = ?) OR (id_demandeur = ? AND id_recepteur = ?)");
        $req->execute([$id_moi, $id_ami, $id_ami, $id_moi]);
    }
    header('Location: amis.php');
    exit;
}

// Récupérer les demandes reçues
$demandes = $pdo->prepare("
    SELECT u.* FROM amis a
    JOIN utilisateurs u ON a.id_demandeur = u.id
    WHERE a.id_recepteur = ? AND a.statut = 'attente'
");
$demandes->execute([$id_moi]);
$demandes = $demandes->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des amis
$amis = $pdo->prepare("
    SELECT u.* FROM amis a
    JOIN utilisateurs u ON (a.id_demandeur = u.id OR a.id_recepteur = u.id)
    WHERE statut = 'accepte' AND u.id != ?
");
$amis->execute([$id_moi]);
$amis = $amis->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>
<div class="conteneur">
    <h2>Mes Amis</h2>

    <!-- Demandes en attente -->
    <div class="carte">
        <h3>Demandes d'amis reçues (<?= count($demandes) ?>)</h3>
        <?php if (empty($demandes)): ?>
            <p>Aucune demande en attente</p>
        <?php else: ?>
            <?php foreach ($demandes as $d): ?>
            <div class="ligne-amis">
                <img src="<?= $d['photo'] ?>" class="mini-photo">
                <span><?= $d['prenom'] . ' ' . $d['nom'] ?> (<?= $d['filiere'] ?>)</span>
                <div class="actions">
                    <a href="amis.php?action=accepter&id=<?= $d['id'] ?>" class="btn vert">Accepter</a>
                    <a href="amis.php?action=refuser&id=<?= $d['id'] ?>" class="btn rouge">Refuser</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Liste des amis -->
    <div class="carte">
        <h3>Mes amis (<?= count($amis) ?>)</h3>
        <?php if (empty($amis)): ?>
            <p>Vous n'avez pas encore d'amis</p>
        <?php else: ?>
            <?php foreach ($amis as $a): ?>
            <div class="ligne-amis">
                <img src="<?= $a['photo'] ?>" class="mini-photo">
                <a href="profil.php?id=<?= $a['id'] ?>"><?= $a['prenom'] . ' ' . $a['nom'] ?></a>
                <div class="actions">
                    <a href="messagerie.php?dest=<?= $a['id'] ?>" class="btn">Message</a>
                    <a href="amis.php?action=supprimer&id=<?= $a['id'] ?>" class="btn rouge">Retirer</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

