<?php
session_start();
$host = 'localhost';
$dbname = 'reseau_etudiant';
$utilisateur = 'root';
$mdp = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $utilisateur,
        $mdp,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}

if (!function_exists('est_connecte')) {
    function est_connecte() {
        return isset($_SESSION['id_utilisateur']);
    }
}
?>

