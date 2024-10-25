<?php
session_start();

// Connexion à la base de données
try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Gestion de la connexion
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);

    // Vérifier si l'utilisateur existe
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        // Le mot de passe est correct
        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'email' => $utilisateur['email'],
            'role' => $utilisateur['role']
        ];

        // Redirection en fonction du rôle
        if ($utilisateur['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $message = "Email ou mot de passe incorrect.";
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
    <h1>Connexion</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="produits.php">Produits</a>
        <a href="panier.php">Panier</a>
        <a href="connexion.php">Connexion</a>
        <a href="inscription.php">Inscription</a>
    </nav>
</header>

<section class="connexion-page">
    <div class="form-container">
        <h2>Connexion</h2>
        <?php if (!empty($message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="connexion.php" class="login-form">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <button type="submit" class="btn-login">Se connecter</button>
        </form>
    </div>
</section>

</body>
</html>
