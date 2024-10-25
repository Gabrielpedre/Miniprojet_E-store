<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data)) {
        echo json_encode(['status' => 'error', 'message' => 'Le panier est vide.']);
        exit;
    }

    $utilisateur_id = $_SESSION['utilisateur']['id'];
    $sql = "INSERT INTO commandes (utilisateur_id, date_commande) VALUES (:utilisateur_id, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $commande_id = $conn->lastInsertId();

    foreach ($data as $produit) {
        $sql = "INSERT INTO commande_produits (commande_id, produit_id, quantite, prix) 
                VALUES (:commande_id, :produit_id, :quantite, :prix)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':commande_id', $commande_id);
        $stmt->bindParam(':produit_id', $produit['id']);
        $stmt->bindParam(':quantite', $produit['quantite']);
        $stmt->bindParam(':prix', $produit['prix']);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success', 'message' => 'Commande validée avec succès.']);
}
?>
