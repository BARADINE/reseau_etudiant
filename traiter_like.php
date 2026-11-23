<?php require 'config/db.php';
if (!est_connecte() || !isset($_GET['id'])) exit('0');
$id_pub = (int)$_GET['id'];
$id_user = $_SESSION['id_utilisateur'];

// Vérifier si l'utilisateur a déjà liké
$verif = $pdo->prepare("SELECT id FROM likes WHERE id_publication = ? AND id_utilisateur = ?");
$verif->execute([$id_pub, $id_user]);

if ($verif->rowCount() > 0) {
    // Supprimer le like
    $pdo->prepare("DELETE FROM likes WHERE id_publication = ? AND id_utilisateur = ?")->execute([$id_pub, $id_user]);
    $pdo->prepare("UPDATE publications SET nb_likes = nb_likes - 1 WHERE id = ?")->execute([$id_pub]);
} else {
    // Ajouter le like
    $pdo->prepare("INSERT INTO likes (id_publication, id_utilisateur) VALUES (?, ?)")->execute([$id_pub, $id_user]);
    $pdo->prepare("UPDATE publications SET nb_likes = nb_likes + 1 WHERE id = ?")->execute([$id_pub]);
}

// Retourner le nouveau nombre de likes
$res = $pdo->prepare("SELECT nb_likes FROM publications WHERE id = ?");
$res->execute([$id_pub]);
echo $res->fetchColumn();

