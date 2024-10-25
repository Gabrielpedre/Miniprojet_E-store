<?php
session_start();

// Connexion à la base de données avec PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Requête pour récupérer des produits phares
$sql = "SELECT * FROM produits LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - E-Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur E-Store</h1>
        <nav>
            <a href="produits.php">Nos produits</a>
            <a href="panier.php">Mon panier</a>
            
            <!-- Vérification de la session utilisateur -->
            <?php if (isset($_SESSION['utilisateur'])): ?>
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <section>
        <h2>Nos produits phares</h2>
        <div class="produits">
            <?php foreach ($produits as $produit): ?>
                <div class="produit">
                    <img src="<?php echo $produit['image_url']; ?>" alt="<?php echo $produit['nom']; ?>">
                    <h3><?php echo $produit['nom']; ?></h3>
                    <p><?php echo $produit['prix']; ?> €</p>
                    <a href="produit_detail.php?id=<?php echo $produit['id_produit']; ?>">Voir le produit</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 E-Store</p>
    </footer>

    <div class="signature">E-Store, powered by GabyExcellence</div>
</body>
</html>
