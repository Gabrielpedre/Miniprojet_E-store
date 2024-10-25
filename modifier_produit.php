<?php
session_start();

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] != 'admin') {
    header("Location: connexion.php");
    exit;
}

// Connexion à la base de données avec PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$produit_id = $_GET['id'];

// Récupérer les détails du produit
$sql = "SELECT * FROM produits WHERE id_produit = :produit_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':produit_id', $produit_id);
$stmt->execute();
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie_id = $_POST['categorie_id'];
    $image_url = $_POST['image_url'];

    // Requête pour modifier le produit
    $sql = "UPDATE produits SET nom = :nom, description = :description, prix = :prix, 
            categorie_id = :categorie_id, image_url = :image_url WHERE id_produit = :produit_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':prix', $prix);
    $stmt->bindParam(':categorie_id', $categorie_id);
    $stmt->bindParam(':image_url', $image_url);
    $stmt->bindParam(':produit_id', $produit_id);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Modifier un produit</h1>
    <nav>
        <a href="produits.php">Produits</a>
        <a href="admin_dashboard.php">Tableau de bord</a>
        <a href="deconnexion.php">Déconnexion</a>
    </nav>
</header>

<section>
    <form method="POST" action="modifier_produit.php?id=<?php echo $produit_id; ?>">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo $produit['nom']; ?>" required>
        
        <label for="description">Description :</label>
        <textarea id="description" name="description" required><?php echo $produit['description']; ?></textarea>
        
        <label for="prix">Prix :</label>
        <input type="number" id="prix" name="prix" value="<?php echo $produit['prix']; ?>" required>
        
        <label for="categorie_id">Catégorie :</label>
        <input type="number" id="categorie_id" name="categorie_id" value="<?php echo $produit['categorie_id']; ?>" required>
        
        <label for="image_url">URL de l'image :</label>
        <input type="text" id="image_url" name="image_url" value="<?php echo $produit['image_url']; ?>" required>
        
        <button type="submit">Modifier</button>
    </form>
</section>

</body>
</html>
