<?php require 'config/db.php';
if (!est_connecte() || $_SERVER['REQUEST_METHOD'] !== 'POST') header('Location: index.php');

$id_user = $_SESSION['id_utilisateur'];
$contenu = trim($_POST['contenu']);
$image = null;

if (empty($contenu)) {
    header('Location: index.php?erreur=contenu_vide');
    exit;
}

// Gestion du téléversement d'image
if (!empty($_FILES['image']['name'])) {
    $dossier = 'assets/publications/';
    if (!is_dir($dossier)) mkdir($dossier, 0755, true);
    $nom_fichier = uniqid() . '_' . basename($_FILES['image']['name']);
    $chemin_complet = $dossier . $nom_fichier;
    $types_acceptees = ['image/jpeg', 'image/png', 'image/gif'];

    if (in_array($_FILES['image']['type'], $types_acceptees) && $_FILES['image']['size'] < 5 * 1024 * 1024) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $chemin_complet)) {
            $image = $chemin_complet;
        }
    }
}

// Enregistrement dans la BDD
$req = $pdo->prepare("INSERT INTO publications (id_auteur, contenu, image) VALUES (?, ?, ?)");
$req->execute([$id_user, $contenu, $image]);

header('Location: index.php');
exit;

