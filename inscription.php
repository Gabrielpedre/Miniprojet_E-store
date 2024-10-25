<?php
// Connexion à la base de données
try {
    $conn = new PDO("mysql:host=localhost;dbname=estore", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Gestion de l'inscription
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);
    $mot_de_passe_conf = trim($_POST['mot_de_passe_conf']);

    if ($mot_de_passe !== $mot_de_passe_conf) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifie si l'e-mail existe déjà
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->rowCount() > 0) {
            $message = "Cet e-mail est déjà utilisé.";
        } else {
            // Crypte le mot de passe avant de l'enregistrer
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Ajoute l'utilisateur dans la base de données
            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':mot_de_passe' => $mot_de_passe_hash // Enregistre le mot de passe crypté
            ]);
            $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Inscription</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="connexion.php">Connexion</a>
        <a href="inscription.php">Inscription</a>
    </nav>
</header>

<section class="inscription-page">
    <div class="form-container">
        <h2>Créer un compte</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="inscription.php" class="inscription-form">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <label for="mot_de_passe_conf">Confirmez le mot de passe :</label>
            <input type="password" id="mot_de_passe_conf" name="mot_de_passe_conf" required>

            <button type="submit" class="btn">S'inscrire</button>
        </form>
    </div>
</section>

</body>
</html>
