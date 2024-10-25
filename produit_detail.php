<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    echo "<script>
            alert('Veuillez vous connecter ou vous inscrire pour accéder aux détails du produit.');
            window.location.href = 'connexion.php';
          </script>";
    exit;
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM produits WHERE id_produit = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produit) {
            echo "Produit non trouvé.";
            exit;
        }
    } else {
        echo "Produit non trouvé.";
        exit;
    }

    // Gestion de l'ajout au panier
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quantite = 1;

        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        $dejaDansPanier = false;
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['id'] === $id) {
                $item['quantite'] += $quantite;
                $dejaDansPanier = true;
                break;
            }
        }

        if (!$dejaDansPanier) {
            $_SESSION['panier'][] = ['id' => $id, 'quantite' => $quantite];
        }

        echo "<script>alert('Produit ajouté au panier !');</script>";
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Détails du produit</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="produits.php">Produits</a>
        <a href="panier.php">Panier</a>
        <a href="deconnexion.php">Déconnexion</a>
    </nav>
</header>

<section class="produit-detail">
    <h2><?php echo htmlspecialchars($produit['nom']); ?></h2>
    <p>Description : <?php echo htmlspecialchars($produit['description']); ?></p>
    <p>Prix : <?php echo htmlspecialchars($produit['prix']); ?> €</p>
    <form method="post">
        <button type="submit">Ajouter au panier</button>
    </form>
</section>

</body>
</html>
