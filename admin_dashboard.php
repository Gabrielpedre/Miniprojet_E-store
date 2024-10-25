<?php
session_start();

// Vérification du rôle de l'utilisateur pour restreindre l'accès aux administrateurs uniquement
if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] != 'admin') {
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
    $sql = "INSERT INTO produits (nom, description, prix, categorie_id, image_url, quantite) 
            VALUES (:nom, :description, :prix, :categorie_id, :image_url, :quantite)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $_POST['nom']);
    $stmt->bindParam(':description', $_POST['description']);
    $stmt->bindParam(':prix', $_POST['prix']);
    $stmt->bindParam(':categorie_id', $_POST['categorie_id']);
    $stmt->bindParam(':image_url', $_POST['image_url']);
    $stmt->bindParam(':quantite', $_POST['quantite']); // Nouvelle liaison pour la quantité
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Modifier un produit
if (isset($_POST['action']) && $_POST['action'] == 'modifier') {
    $sql = "UPDATE produits SET nom = :nom, description = :description, prix = :prix, 
            categorie_id = :categorie_id, image_url = :image_url, quantite = :quantite WHERE id_produit = :produit_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $_POST['nom']);
    $stmt->bindParam(':description', $_POST['description']);
    $stmt->bindParam(':prix', $_POST['prix']);
    $stmt->bindParam(':categorie_id', $_POST['categorie_id']);
    $stmt->bindParam(':image_url', $_POST['image_url']);
    $stmt->bindParam(':quantite', $_POST['quantite']);
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
        <a href="produits.php">Voir tous les produits</a>
        <a href="deconnexion.php">Déconnexion</a>
    </nav>
</header>

<section class="dashboard-section">
    <h2>Gestion des produits</h2>
    
    <div class="form-container">
        <h3>Ajouter un produit</h3>
        <form method="POST" action="admin_dashboard.php" class="product-form">
            <input type="hidden" name="action" value="ajouter">
            
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" placeholder="Nom du produit" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" placeholder="Brève description du produit" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="prix">Prix :</label>
                <input type="number" id="prix" name="prix" placeholder="Prix en euros" required>
            </div>
            
            <div class="form-group">
                <label for="categorie_id">Catégorie :</label>
                <input type="number" id="categorie_id" name="categorie_id" placeholder="ID de la catégorie" required>
            </div>
            
            <div class="form-group">
                <label for="image_url">URL de l'image :</label>
                <input type="text" id="image_url" name="image_url" placeholder="URL de l'image" required>
            </div>

            <div class="form-group">
                <label for="quantite">Quantité :</label>
                <input type="number" id="quantite" name="quantite" placeholder="Quantité en stock" required>
            </div>

            <button type="submit" class="btn">Ajouter le produit</button>
        </form>
    </div>

    <h3>Liste des produits</h3>
    <table class="product-table">
        <thead>
            <tr><th>Nom</th><th>Description</th><th>Prix (€)</th><th>Catégorie</th><th>Quantité</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                <td><?php echo htmlspecialchars($produit['description']); ?></td>
                <td><?php echo htmlspecialchars($produit['prix']); ?> €</td>
                <td><?php echo htmlspecialchars($produit['categorie_id']); ?></td>
                <td><?php echo htmlspecialchars($produit['quantite']); ?></td>
                <td>
                    <form method="POST" action="admin_dashboard.php" style="display:inline-block;">
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                        <button type="submit" class="btn btn-modify">Modifier</button>
                    </form>
                    <form method="POST" action="admin_dashboard.php" style="display:inline-block;">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                        <button type="submit" class="btn btn-delete">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
</body>
</html>
