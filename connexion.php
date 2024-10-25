<?php
session_start();

// Activer l'affichage des erreurs pour diagnostiquer les problèmes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données avec PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Requête pour vérifier l'utilisateur
    $sql = "SELECT * FROM utilisateurs WHERE email = :email AND mot_de_passe = :mot_de_passe";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mot_de_passe', $mot_de_passe);

    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur) {
        $_SESSION['utilisateur'] = $utilisateur;

        // Sauvegarder l'email de l'utilisateur pour la récupération du panier
        $email_utilisateur = $utilisateur['email'];

        // Récupérer le panier sauvegardé en localStorage
        echo "
            <script>
                const panierSauvegarde = JSON.parse(localStorage.getItem('panier_' + '$email_utilisateur')) || [];
                localStorage.setItem('panier', JSON.stringify(panierSauvegarde));
                window.location.href = 'produits.php';
            </script>";
        exit;
    } else {
        echo "<p>Identifiants incorrects. Veuillez réessayer.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Connexion à E-Store</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="produits.php">Nos produits</a>
    </nav>
</header>

<section id="connexion">
    <h2>Se connecter</h2>
    <form method="POST" action="connexion.php">
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>
        
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required>
        
        <button type="submit">Connexion</button>
    </form>
</section>

<footer>
    <p>&copy; 2024 E-Store</p>
</footer>

<div class="signature">E-Store, powered by Excellence</div>

</body>
</html>
