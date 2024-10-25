<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "gabriel", "gabriel");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = :email AND mot_de_passe = :mot_de_passe");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mot_de_passe', $mot_de_passe);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur) {
        $_SESSION['utilisateur'] = $utilisateur;
        $_SESSION['role'] = $utilisateur['role'];

        // Récupérer le panier sauvegardé
        $stmt = $conn->prepare("SELECT id_produit, quantite, prix FROM sauvegarde_panier WHERE utilisateur_email = :email");
        $stmt->bindParam(':email', $utilisateur['email']);
        $stmt->execute();
        $sauvegarde_panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Charger les articles sauvegardés dans le panier de session
        $_SESSION['panier'] = [];
        foreach ($sauvegarde_panier as $article) {
            $_SESSION['panier'][] = $article;
        }

        // Redirection selon le rôle de l'utilisateur
        if ($utilisateur['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $message = "<p class='error-message'>Email ou mot de passe incorrect.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - E-Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="connexion-page">
<header>
    <h1>Connexion</h1>
</header>

<section class="login-section">
    <?php if (isset($message)) echo $message; ?>
    <form method="POST" action="connexion.php" class="login-form">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>

        <button type="submit" class="btn-login">Se connecter</button>
    </form>
</section>

<footer>
    <p>&copy; 2024 E-Store</p>
</footer>
</body>
</html>
