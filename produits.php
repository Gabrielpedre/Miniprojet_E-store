<?php
session_start();

// Clé API Pexels (remplace par ta clé API)
$pexels_api_key = 'SjrYYmdBdPTPC0dNkOl424dcozrDGmNEiZr3XapFDRv3sMyspFpUioL0';

// Fonction pour récupérer une image à partir de l'API Pexels
function get_pexels_image($nom_produit, $api_key) {
    $query = urlencode($nom_produit . ' technology'); // Ajout du contexte "technology"
    $url = "https://api.pexels.com/v1/search?query={$query}&per_page=1";

    // Configuration de l'en-tête HTTP pour l'API Pexels
    $options = [
        "http" => [
            "header" => "Authorization: {$api_key}\r\n"
        ]
    ];
    $context = stream_context_create($options);
    
    // Effectuer une requête HTTP GET vers l'API Pexels
    $response = @file_get_contents($url, false, $context);
    if ($response === FALSE) {
        // Retourner une image par défaut en cas d'erreur
        return 'https://via.placeholder.com/300x200?text=Image+non+disponible';
    }

    $data = json_decode($response, true);

    // Si une image est trouvée, retourner l'URL
    if (!empty($data['photos'][0]['src']['medium'])) {
        return $data['photos'][0]['src']['medium'];
    } else {
        return 'https://via.placeholder.com/300x200?text=Image+non+disponible';
    }
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupérer toutes les catégories pour les afficher en haut de la page
$sql = "SELECT * FROM categories";
$stmt = $conn->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les filtres de recherche et de catégorie si définis
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
$categorie_id = isset($_GET['categorie']) ? $_GET['categorie'] : null;

if ($search && $categorie_id) {
    $sql = "SELECT * FROM produits WHERE (nom LIKE :search OR description LIKE :search) AND categorie_id = :categorie_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':search', $search);
    $stmt->bindParam(':categorie_id', $categorie_id);
} elseif ($search) {
    $sql = "SELECT * FROM produits WHERE nom LIKE :search OR description LIKE :search";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':search', $search);
} elseif ($categorie_id) {
    $sql = "SELECT * FROM produits WHERE categorie_id = :categorie_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':categorie_id', $categorie_id);
} else {
    $sql = "SELECT * FROM produits";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Store - Produits</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Nos produits</h1>
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
    <!-- Menu de catégories en boutons -->
    <div class="categories-buttons">
        <form method="GET" action="produits.php">
            <button type="submit" name="categorie" value="" <?php echo !$categorie_id ? 'class="active"' : ''; ?>>Toutes</button>
            <?php foreach ($categories as $categorie): ?>
                <button type="submit" name="categorie" value="<?php echo $categorie['id_categorie']; ?>" <?php echo ($categorie_id == $categorie['id_categorie']) ? 'class="active"' : ''; ?>>
                    <?php echo $categorie['nom']; ?>
                </button>
            <?php endforeach; ?>
        </form>
    </div>

    <!-- Barre de recherche qui se recharge après chaque frappe -->
    <form method="GET" action="produits.php" style="margin-bottom: 20px;">
        <?php if ($categorie_id): ?>
            <input type="hidden" name="categorie" value="<?php echo htmlspecialchars($categorie_id); ?>">
        <?php endif; ?>
        <input type="text" name="search" placeholder="Rechercher un produit..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" oninput="this.form.submit()">
    </form>

    <h2><?php echo $search ? 'Résultats de recherche' : 'Liste des produits'; ?></h2>
    <div class="produits">
        <?php if ($produits): ?>
            <?php foreach ($produits as $produit): ?>
                <div class="produit">
                    <?php 
                    // Utiliser la fonction pour obtenir une image via l'API Pexels
                    $image_url = get_pexels_image($produit['nom'], $pexels_api_key);
                    ?>
                    <img src="<?php echo $image_url; ?>" alt="<?php echo $produit['nom']; ?>">
                    <h3><?php echo $produit['nom']; ?></h3>
                    <p><?php echo $produit['prix']; ?> €</p>
                    <a href="produit_detail.php?id=<?php echo $produit['id_produit']; ?>">Voir le produit</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun produit trouvé.</p>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>&copy; 2024 E-Store</p>
</footer>
<div class="signature">E-Store, powered by Excellence</div>
</body>
</html>
