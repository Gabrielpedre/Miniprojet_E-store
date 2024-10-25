<?php
session_start();
if (isset($_SESSION['utilisateur']) && isset($_SESSION['panier'])) {
    try {
        $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $utilisateur_email = $_SESSION['utilisateur']['email'];
        $panier = $_SESSION['panier'];

        // Supprimer les anciennes sauvegardes du panier
        $delete_stmt = $conn->prepare("DELETE FROM sauvegarde_panier WHERE utilisateur_email = :email");
        $delete_stmt->bindParam(':email', $utilisateur_email);
        $delete_stmt->execute();

        // Enregistrer le panier actuel
        foreach ($panier as $produit) {
            $insert_stmt = $conn->prepare("INSERT INTO sauvegarde_panier (utilisateur_email, id_produit, quantite, prix)
                                           VALUES (:email, :id_produit, :quantite, :prix)");
            $insert_stmt->bindParam(':email', $utilisateur_email);
            $insert_stmt->bindParam(':id_produit', $produit['id_produit']);
            $insert_stmt->bindParam(':quantite', $produit['quantite']);
            $insert_stmt->bindParam(':prix', $produit['prix']);
            $insert_stmt->execute();
        }
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Vider la session et rediriger
session_destroy();
header("Location: index.php");
exit();
?>
