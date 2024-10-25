<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Mon Panier</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="produits.php">Nos produits</a>

            <!-- Vérification de la session utilisateur -->
            <?php if (isset($_SESSION['utilisateur'])): ?>
                <span>Connecté en tant que : <?php echo $_SESSION['utilisateur']['email']; ?></span>
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <section id="panier">
        <h2>Vos articles :</h2>
        <div id="liste-panier">
            <!-- Les articles seront affichés ici via JavaScript -->
        </div>
        <p>Total : <span id="total-panier">0</span> €</p>
        <button onclick="validerCommande()">Valider la commande</button>
    </section>

    <footer>
        <p>&copy; 2024 E-Store</p>
    </footer>

    <div class="signature">E-Store, powered by Excellence</div>

    <script>
        let panier = JSON.parse(localStorage.getItem('panier')) || [];

        // Fonction pour afficher le panier
        function afficherPanier() {
            let total = 0;
            const listePanier = document.getElementById('liste-panier');
            listePanier.innerHTML = '';

            panier.forEach((produit, index) => {
                total += produit.prix * produit.quantite;
                listePanier.innerHTML += `
                    <div class="produit-panier">
                        <h3>${produit.nom}</h3>
                        <p>
                            <label for="quantite-${index}">Quantité : </label>
                            <input type="number" id="quantite-${index}" value="${produit.quantite}" min="1" onchange="modifierQuantite(${index})">
                            x ${produit.prix} €
                        </p>
                        <button onclick="supprimerDuPanier(${index})">Supprimer</button>
                    </div>
                `;
            });

            document.getElementById('total-panier').innerText = total.toFixed(2);
        }

        // Fonction pour modifier la quantité d'un produit dans le panier
        function modifierQuantite(index) {
            const nouvelleQuantite = document.getElementById(`quantite-${index}`).value;
            panier[index].quantite = parseInt(nouvelleQuantite);
            localStorage.setItem('panier', JSON.stringify(panier)); // Mettre à jour le localStorage
            afficherPanier(); // Réafficher le panier pour mettre à jour le total
        }

        // Fonction pour supprimer un article du panier
        function supprimerDuPanier(index) {
            panier.splice(index, 1); // Supprime l'article du tableau
            localStorage.setItem('panier', JSON.stringify(panier)); // Mettre à jour le localStorage
            afficherPanier(); // Mettre à jour l'affichage du panier
        }

        // Fonction pour valider la commande
        function validerCommande() {
            if (panier.length > 0) {
                fetch('validerCommande.php', {
                    method: 'POST',
                    body: JSON.stringify(panier),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    alert('Commande validée !');
                    localStorage.removeItem('panier');
                    afficherPanier();
                });
            } else {
                alert('Votre panier est vide !');
            }
        }

        // Afficher le panier lors du chargement de la page
        afficherPanier();
    </script>
</body>
</html>
