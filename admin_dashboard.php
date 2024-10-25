<?php
session_start();

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] != 'admin') {
    header("Location: connexion.php");
    exit;
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Ajouter un produit
if (isset($_POST['action']) && $_POST['action'] == 'ajouter') {
    $sql = "INSERT INTO produits (nom, description, prix, categorie_id, image_url) 
            VALUES (:nom, :description, :prix, :categorie_id, :image_url)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $_POST['nom']);
    $stmt->bindParam(':description', $_POST['description']);
    $stmt->bindParam(':prix', $_POST['prix']);
    $stmt->bindParam(':categorie_id', $_POST['categorie_id']);
    $stmt->bindParam(':image_url', $_POST['image_url']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Modifier un produit
if (isset($_POST['action']) && $_POST['action'] == 'modifier') {
    $sql = "UPDATE produits SET nom = :nom, description = :description, prix = :prix, 
            categorie_id = :categorie_id, image_url = :image_url WHERE id_produit = :produit_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $_POST['nom']);
    $stmt->bindParam(':description', $_POST['description']);
    $stmt->bindParam(':prix', $_POST['prix']);
    $stmt->bindParam(':categorie_id', $_POST['categorie_id']);
    $stmt->bindParam(':image_url', $_POST['image_url']);
    $stmt->bindParam(':produit_id', $_POST['produit_id']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Supprimer un produit
if (isset($_POST['action']) && $_POST['action'] == 'supprimer') {
    $sql = "DELETE FROM produits WHERE id_produit = :produit_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':produit_id', $_POST['produit_id']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Récupérer tous les produits
$sql = "SELECT * FROM produits";
$stmt = $conn->prepare($sql);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Tableau de bord administrateur</h1>
    <nav>
        <a href="produits.php">Produits</a>
        <a href="deconnexion.php">Déconnexion</a>
    </nav>
</header>
<section>
    <h2>Gérer les produits</h2>
    <h3>Ajouter un produit</h3>
    <form method="POST" action="admin_dashboard.php">
        <input type="hidden" name="action" value="ajouter">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea>
        <label for="prix">Prix :</label>
        <input type="number" id="prix" name="prix" required>
        <label for="categorie_id">Catégorie :</label>
        <input type="number" id="categorie_id" name="categorie_id" required>
        <label for="image_url">URL de l'image :</label>
        <input type="text" id="image_url" name="image_url" required>
        <button type="submit">Ajouter</button>
    </form>
    <h3>Liste des produits</h3>
    <table>
        <thead>
            <tr><th>Nom</th><th>Description</th><th>Prix</th><th>Catégorie</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?php echo $produit['nom']; ?></td>
                <td><?php echo $produit['description']; ?></td>
                <td><?php echo $produit['prix']; ?> €</td>
                <td><?php echo $produit['categorie_id']; ?></td>
                <td>
                    <form method="POST" action="admin_dashboard.php" style="display:inline-block;">
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                        <button type="submit">Modifier</button>
                    </form>
                    <form method="POST" action="admin_dashboard.php" style="display:inline-block;">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
</body>
</html>
