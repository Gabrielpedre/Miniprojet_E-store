<?php
session_start();

// Sauvegarder le panier de l'utilisateur avant la déconnexion
if (isset($_SESSION['utilisateur'])) {
    $utilisateur = $_SESSION['utilisateur']['email'];
    echo "<script>
        const panier = JSON.parse(localStorage.getItem('panier')) || [];
        localStorage.setItem('panier_' + '$utilisateur', JSON.stringify(panier));
    </script>";
}

// Détruire la session pour déconnecter l'utilisateur
session_destroy();

// Vider le panier après déconnexion
echo "<script>
    localStorage.removeItem('panier');
    window.location.href = 'connexion.php';
</script>";
exit;
?>
