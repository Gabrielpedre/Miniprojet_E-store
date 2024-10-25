<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$produit_id = $_GET['id'];
$sql = "SELECT * FROM produits WHERE id_produit = :produit_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':produit_id', $produit_id);
$stmt->execute();
$produit = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $produit['nom']; ?> - Détails</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1><?php echo $produit['nom']; ?></h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="panier.php">Mon panier</a>
        <?php if (isset($_SESSION['utilisateur'])): ?>
            <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            <a href="connexion.php">Connexion</a>
        <?php endif; ?>
    </nav>
</header>
<section>
    <img src="<?php echo $produit['image_url']; ?>" alt="<?php echo $produit['nom']; ?>">
    <h3>Prix : <?php echo $produit['prix']; ?> €</h3>
    <p><?php echo $produit['description']; ?></p>
    <form>
        <label for="quantite">Quantité :</label>
        <input type="number" id="quantite" name="quantite" value="1" min="1">
        <button type="button" onclick="ajouterAuPanier(<?php echo $produit['id_produit']; ?>, '<?php echo $produit['nom']; ?>', <?php echo $produit['prix']; ?>)">Ajouter au panier</button>
    </form>
</section>
<footer>
    <p>&copy; 2024 E-Store</p>
</footer>
<div class="signature">E-Store, powered by Excellence</div>
<script>
    function ajouterAuPanier(id, nom, prix) {
        const quantite = document.getElementById('quantite').value;
        let panier = JSON.parse(localStorage.getItem('panier')) || [];
        panier.push({id: id, nom: nom, prix: prix, quantite: parseInt(quantite)});
        localStorage.setItem('panier', JSON.stringify(panier));
        alert('Produit ajouté au panier !');
    }
</script>
</body>
</html>
