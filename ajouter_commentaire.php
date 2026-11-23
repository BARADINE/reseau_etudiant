<?php require 'config/db.php';
if (!est_connecte() || $_SERVER['REQUEST_METHOD'] !== 'POST') header('Location: index.php');
$id_pub = (int)$_POST['id_publication'];
$contenu = trim($_POST['contenu']);
if (!empty($contenu)) {
    $req = $pdo->prepare("INSERT INTO commentaires (id_publication, id_utilisateur, contenu) VALUES (?, ?, ?)");
    $req->execute([$id_pub, $_SESSION['id_utilisateur'], $contenu]);
}
header('Location: index.php');
exit;

