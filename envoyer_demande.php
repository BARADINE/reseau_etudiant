<?php require 'config/db.php';
if (!est_connecte() || !isset($_GET['id'])) header('Location: index.php');
$id_dest = (int)$_GET['id'];
$id_moi = $_SESSION['id_utilisateur'];

if ($id_dest != $id_moi) {
    try {
        $req = $pdo->prepare("INSERT IGNORE INTO amis (id_demandeur, id_recepteur, statut) VALUES (?, ?, 'attente')");
        $req->execute([$id_moi, $id_dest]);
    } catch (Exception $e) {}
}
header("Location: profil.php?id=$id_dest");
exit;

