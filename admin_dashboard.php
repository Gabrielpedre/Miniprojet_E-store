<?php
session_start();

// Vérification de l'accès administrateur
if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] != 'admin') {
    header("Location: connexion.php");
    exit;
}

// Connexion à la base de données
try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Ajout d'un produit
    if ($action === 'ajouter') {
        try {
            $sql = "INSERT INTO produits (nom, description, prix, categorie_id, image_url, quantite) 
                    VALUES (:nom, :description, :prix, :categorie_id, :image_url, :quantite)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nom' => $_POST['nom'],
                ':description' => $_POST['description'],
                ':prix' => $_POST['prix'],
                ':categorie_id' => $_POST['categorie_id'],
                ':image_url' => $_POST['image_url'],
                ':quantite' => $_POST['quantite']
            ]);
            header("Location: admin_dashboard.php");
            exit;
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout du produit : " . $e->getMessage();
        }
    }

    // Modification d'un produit
    if ($action === 'modifier') {
        try {
            $sql = "UPDATE produits SET nom = :nom, description = :description, prix = :prix, 
                    categorie_id = :categorie_id, image_url = :image_url, quantite = :quantite 
                    WHERE id_produit = :id_produit";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nom' => $_POST['nom'],
                ':description' => $_POST['description'],
                ':prix' => $_POST['prix'],
                ':categorie_id' => $_POST['categorie_id'],
                ':image_url' => $_POST['image_url'],
                ':quantite' => $_POST['quantite'],
                ':id_produit' => $_POST['id_produit']
            ]);
            header("Location: admin_dashboard.php");
            exit;
        } catch (PDOException $e) {
            echo "Erreur lors de la modification du produit : " . $e->getMessage();
        }
    }

    // Suppression d'un produit
    if ($action === 'supprimer') {
        try {
            $sql = "DELETE FROM produits WHERE id_produit = :id_produit";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id_produit' => $_POST['id_produit']]);
            header("Location: admin_dashboard.php");
            exit;
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du produit : " . $e->getMessage();
        }
    }
}

// Récupération des produits
try {
    $sql = "SELECT * FROM produits";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des produits : " . $e->getMessage());
}
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

    <!-- Formulaire d'ajout de produit -->
    <div class="form-section">
        <h3>Ajouter un produit</h3>
        <form method="POST" action="admin_dashboard.php">
            <input type="hidden" name="action" value="ajouter">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="description">Description :</label>
            <textarea id="description" name="description" required></textarea>

            <label for="prix">Prix :</label>
            <input type="number" id="prix" name="prix" step="0.01" required>

            <label for="categorie_id">Catégorie :</label>
            <input type="number" id="categorie_id" name="categorie_id" required>

            <label for="image_url">URL de l'image :</label>
            <input type="text" id="image_url" name="image_url" required>

            <label for="quantite">Quantité :</label>
            <input type="number" id="quantite" name="quantite" required>

            <button type="submit">Ajouter</button>
        </form>
    </div>

    <!-- Liste des produits avec formulaires de modification et de suppression -->
    <h3>Liste des produits</h3>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Catégorie</th>
                <th>Quantité</th>
                <th>Actions</th>
            </tr>
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
                    <!-- Formulaire de modification de produit -->
                    <div class="product-action">
                        <form method="POST" action="admin_dashboard.php" class="product-form">
                            <input type="hidden" name="action" value="modifier">
                            <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                            <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
                            <textarea name="description" required><?php echo htmlspecialchars($produit['description']); ?></textarea>
                            <input type="number" name="prix" step="0.01" value="<?php echo htmlspecialchars($produit['prix']); ?>" required>
                            <input type="number" name="categorie_id" value="<?php echo htmlspecialchars($produit['categorie_id']); ?>" required>
                            <input type="text" name="image_url" value="<?php echo htmlspecialchars($produit['image_url']); ?>" required>
                            <input type="number" name="quantite" value="<?php echo htmlspecialchars($produit['quantite']); ?>" required>
                            <button type="submit">Modifier</button>
                        </form>
                    </div>

                    <!-- Formulaire de suppression de produit -->
                    <div class="product-action">
                        <form method="POST" action="admin_dashboard.php" class="product-form">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                            <button type="submit" class="delete-button">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
</body>
</html>
