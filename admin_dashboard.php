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
            $produit_id = $_POST['id_produit'];
            $sql = "UPDATE produits SET nom = :nom, description = :description, prix = :prix, 
                    categorie_id = :categorie_id, image_url = :image_url, quantite = :quantite 
                    WHERE id_produit = :id_produit";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nom' => $_POST["nom_$produit_id"],
                ':description' => $_POST["description_$produit_id"],
                ':prix' => $_POST["prix_$produit_id"],
                ':categorie_id' => $_POST["categorie_id_$produit_id"],
                ':image_url' => $_POST["image_url_$produit_id"],
                ':quantite' => $_POST["quantite_$produit_id"],
                ':id_produit' => $produit_id
            ]);
            header("Location: admin_dashboard.php");
            exit;
        } catch (PDOException $e) {
            echo "Erreur lors de la modification du produit : " . $e->getMessage();
        }
    }

    // Suppression d'un produit
    if (strpos($action, 'supprimer_') === 0) {
        try {
            $produit_id = str_replace('supprimer_', '', $action);
            $sql = "DELETE FROM produits WHERE id_produit = :id_produit";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id_produit' => $produit_id]);
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

<section class="dashboard-section">
    <h2>Gérer les produits</h2>

    <!-- Formulaire d'ajout de produit amélioré -->
    <div class="form-container">
        <h3>Ajouter un produit</h3>
        <form method="POST" action="admin_dashboard.php" class="product-form">
            <input type="hidden" name="action" value="ajouter">

            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix :</label>
                <input type="number" id="prix" name="prix" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="categorie_id">Catégorie :</label>
                <input type="number" id="categorie_id" name="categorie_id" required>
            </div>

            <div class="form-group">
                <label for="image_url">URL de l'image :</label>
                <input type="text" id="image_url" name="image_url" required>
            </div>

            <div class="form-group">
                <label for="quantite">Quantité :</label>
                <input type="number" id="quantite" name="quantite" required>
            </div>

            <button type="submit" class="btn">Ajouter le produit</button>
        </form>
    </div>

    <!-- Liste des produits avec options de modification et de suppression -->
    <h3>Liste des produits</h3>
    <form method="POST" action="admin_dashboard.php">
        <input type="hidden" name="action" value="modifier">
        <table class="product-table">
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
                    <td><input type="text" name="nom_<?php echo $produit['id_produit']; ?>" value="<?php echo htmlspecialchars($produit['nom']); ?>"></td>
                    <td><textarea name="description_<?php echo $produit['id_produit']; ?>"><?php echo htmlspecialchars($produit['description']); ?></textarea></td>
                    <td><input type="number" step="0.01" name="prix_<?php echo $produit['id_produit']; ?>" value="<?php echo htmlspecialchars($produit['prix']); ?>"></td>
                    <td><input type="number" name="categorie_id_<?php echo $produit['id_produit']; ?>" value="<?php echo htmlspecialchars($produit['categorie_id']); ?>"></td>
                    <td><input type="number" name="quantite_<?php echo $produit['id_produit']; ?>" value="<?php echo htmlspecialchars($produit['quantite']); ?>"></td>
                    <td>
                        <button type="submit" name="id_produit" value="<?php echo $produit['id_produit']; ?>" class="btn-modify">Modifier</button>
                        <button type="submit" name="action" value="supprimer_<?php echo $produit['id_produit']; ?>" class="btn-delete" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let formModified = false;
        
        // Détecte les modifications dans les champs du formulaire
        const inputs = document.querySelectorAll("input[type='text'], input[type='number'], textarea");
        inputs.forEach(input => {
            input.addEventListener("input", () => {
                formModified = true;
            });
        });

        // Message d'avertissement avant de quitter la page
        window.addEventListener("beforeunload", (event) => {
            if (formModified) {
                event.preventDefault();
                event.returnValue = "Vous avez des modifications non enregistrées. Cliquez sur 'Modifier' pour enregistrer vos changements.";
            }
        });

        // Réinitialiser l'état de modification après l'envoi du formulaire
        const forms = document.querySelectorAll("form");
        forms.forEach(form => {
            form.addEventListener("submit", () => {
                formModified = false; // Réinitialiser après soumission
            });
        });
    });
</script>

</body>
</html>
