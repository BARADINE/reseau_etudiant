<?php require 'config/db.php';
if (!est_connecte()) header('Location: login.php');
$id_user = $_SESSION['id_utilisateur'];
$succes = $erreur = '';

// Récupérer infos actuelles
$req = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$req->execute([$id_user]);
$profil = $req->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $bio = trim($_POST['bio']);
    $filiere = trim($_POST['filiere']);
    $promotion = trim($_POST['promotion']);
    $nouvelle_photo = $profil['photo'];

    // Gestion nouvelle photo de profil
    if (!empty($_FILES['photo']['name'])) {
        $dossier = 'assets/profil/';
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);
        $nom_photo = uniqid() . '_' . basename($_FILES['photo']['name']);
        $chemin = $dossier . $nom_photo;
        $types_ok = ['image/jpeg', 'image/png'];

        if (in_array($_FILES['photo']['type'], $types_ok) && $_FILES['photo']['size'] < 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $chemin)) {
                if ($profil['photo'] !== 'assets/profil/default.png' && file_exists($profil['photo'])) {
                    unlink($profil['photo']);
                }
                $nouvelle_photo = $chemin;
            }
        }
    }

    // Mise à jour BDD
    if (!empty($nom) && !empty($prenom) && !empty($filiere) && !empty($promotion)) {
        $maj = $pdo->prepare("UPDATE utilisateurs SET nom=?, prenom=?, bio=?, filiere=?, promotion=?, photo=? WHERE id=?");
        $maj->execute([$nom, $prenom, $bio, $filiere, $promotion, $nouvelle_photo, $id_user]);
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $succes = "Profil mis à jour !";
        $profil['photo'] = $nouvelle_photo;
    } else {
        $erreur = "Champs obligatoires manquants";
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="conteneur petit">
    <h2>Modifier mon profil</h2>
    <?php if ($erreur): ?><div class="alerte erreur"><?= $erreur ?></div><?php endif; ?>
    <?php if ($succes): ?><div class="alerte succes"><?= $succes ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <img src="<?= $profil['photo'] ?>" class="photo-profil">
        <input type="file" name="photo" accept="image/*">

        <div class="ligne">
            <input type="text" name="nom" value="<?= $profil['nom'] ?>" required>
            <input type="text" name="prenom" value="<?= $profil['prenom'] ?>" required>
        </div>
        <textarea name="bio" rows="3" placeholder="Votre biographie..."><?= $profil['bio'] ?></textarea>
        <div class="ligne">
            <input type="text" name="filiere" value="<?= $profil['filiere'] ?>" required>
            <input type="text" name="promotion" value="<?= $profil['promotion'] ?>" required>
        </div>
        <button type="submit">Enregistrer les modifications</button>
    </form>
    <p style="text-align:center; margin-top:15px;"><a href="profil.php?id=<?= $id_user ?>">Retour au profil</a></p>
</div>
<?php include 'includes/footer.php'; ?>

