-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 25 oct. 2024 à 11:45
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `estore`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom`) VALUES
(1, 'Ordinateurs'),
(2, 'Peripheriques'),
(3, 'Composants PC'),
(4, 'Accessoires'),
(5, 'Logiciels');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id_commande` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_commande`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `utilisateur_id`, `date_commande`, `total`) VALUES
(1, 1, '2024-10-24 22:19:25', '2299.98');

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

DROP TABLE IF EXISTS `details_commande`;
CREATE TABLE IF NOT EXISTS `details_commande` (
  `id_detail_commande` int(11) NOT NULL AUTO_INCREMENT,
  `commande_id` int(11) DEFAULT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail_commande`),
  KEY `commande_id` (`commande_id`),
  KEY `produit_id` (`produit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_detail_commande`, `commande_id`, `produit_id`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 1, 1, '899.99'),
(2, 1, 15, 1, '1399.99');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_email` varchar(255) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT '1',
  `prix` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_produit` (`id_produit`),
  KEY `utilisateur_email` (`utilisateur_email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `id_produit` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `quantite` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  KEY `categorie_id` (`categorie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom`, `description`, `prix`, `quantite`, `image_url`, `categorie_id`) VALUES
(1, 'Ordinateur portable HP Pavilion', 'Ordinateur portable avec processeur Intel i7 et 16 Go de RAM\r\n', '899.99', 15, NULL, 1),
(2, 'Souris gaming Logitech G502', 'Souris ergonomique avec 11 boutons programmables.', '59.99', 40, 'images/logitech_g502.jpg', 2),
(3, 'Processeur Intel Core i9-12900K', 'Processeur de dernière génération pour les gamers et créateurs.', '599.99', 25, 'images/intel_i9.jpg', 3),
(5, 'Carte graphique NVIDIA RTX 3080', 'Carte graphique puissante pour le gaming et la création 3D.', '1199.99', 10, 'images/rtx_3080.jpg', 3),
(6, 'Casque audio HyperX Cloud II', 'Casque audio avec son surround 7.1 pour gamers.', '99.99', 50, 'images/hyperx_cloud.jpg', 2),
(7, 'Windows 11 Pro', 'Licence officielle de Windows 11 Pro pour une utilisation professionnelle.', '199.99', 100, 'images/windows_11.jpg', 5),
(8, 'Alimentation Corsair RM850x', 'Alimentation modulaire de 850W pour PC.', '129.99', 20, 'images/corsair_rm850x.jpg', 3),
(9, 'Ordinateur Portable Asus ZenBook 14', 'Ordinateur portable léger et puissant avec écran NanoEdge 14 pouces', '1199.99', 40, 'images/asus_zenbook.jpg', 1),
(10, 'Souris Gaming Corsair Dark Core', 'Souris sans fil avec éclairage RGB et capteur optique ultra précis', '89.99', 120, 'images/corsair_dark_core.jpg', 2),
(11, 'Clavier Mécanique Razer BlackWidow Elite', 'Clavier de jeu mécanique avec rétroéclairage RGB', '179.99', 55, 'images/razer_blackwidow.jpg', 2),
(12, 'Processeur Intel Core i7-12700K', 'Processeur de 12ème génération pour des performances optimales', '429.99', 35, 'images/intel_i7_12700k.jpg', 3),
(13, 'Carte Graphique NVIDIA RTX 3090', 'La carte graphique ultime pour le jeu et la création 3D', '1999.99', 20, 'images/rtx_3090.jpg', 3),
(14, 'Mémoire RAM G.Skill Ripjaws 16GB', 'Kit de RAM DDR4 haute performance pour gamers', '129.99', 80, 'images/gskill_ripjaws.jpg', 3),
(15, 'Écran LG UltraFine 5K', 'Écran professionnel 5K avec une excellente fidélité des couleurs', '1399.99', 15, 'images/lg_ultrafine_5k.jpg', 1),
(16, 'Logiciel Adobe Creative Cloud', 'Accès complet à la suite Adobe pour la création graphique et vidéo', '249.99', 300, 'images/adobe_cc.jpg', 5),
(17, 'Alimentation Cooler Master 750W', 'Alimentation semi-modulaire certifiée 80 Plus Bronze', '99.99', 45, 'images/cooler_master_750w.jpg', 3),
(18, 'Casque Audio Sennheiser HD 660S', 'Casque audiophile haute fidélité avec un son exceptionnel', '499.99', 25, 'images/sennheiser_hd660s.jpg', 2),
(19, 'Clé USB SanDisk Ultra 256GB', 'Clé USB rapide avec une capacité de 256 Go pour le stockage de fichiers', '49.99', 180, 'images/sandisk_usb.jpg', 2),
(20, 'Tablette Microsoft Surface Pro 7', 'Tablette puissante avec clavier détachable pour la productivité', '1599.99', 25, 'images/surface_pro7.jpg', 1),
(21, 'Souris sans fil Apple Magic Mouse', 'Souris sans fil élégante avec surface multi-touch', '79.99', 100, 'images/apple_magic_mouse.jpg', 2),
(22, 'Webcam Razer Kiyo Pro', 'Webcam professionnelle avec capteur de lumière adaptative pour streaming', '199.99', 70, 'images/razer_kiyo_pro.jpg', 2),
(23, 'Disque SSD Crucial MX500 2TB', 'Disque SSD rapide et fiable pour un stockage énorme', '259.99', 60, 'images/crucial_mx500.jpg', 3),
(24, 'Switch Cisco SG350-28', 'Switch réseau professionnel 28 ports Gigabit pour entreprises', '399.99', 20, 'images/cisco_sg350.jpg', 2),
(25, 'Enceinte Sonos One', 'Enceinte intelligente avec commande vocale et un son cristallin', '199.99', 100, 'images/sonos_one.jpg', 2),
(26, 'Imprimante Brother DCP-L5500DN', 'Imprimante laser multifonction avec réseau intégré', '299.99', 30, 'images/brother_l5500dn.jpg', 1),
(27, 'Clé de licence MacOS Big Sur', 'Licence officielle pour installer et utiliser macOS Big Sur', '129.99', 150, 'images/macos_bigsur.jpg', 5),
(28, 'Sacoche HP Executive 15.6\"', 'Sacoche élégante et résistante pour ordinateurs portables de 15,6 pouces', '89.99', 150, 'images/hp_executive_bag.jpg', 4),
(29, 'Ordinateur Portable Lenovo Legion 5', 'Ordinateur portable gamer avec processeur Ryzen 7 et RTX 3060', '1599.99', 50, 'images/lenovo_legion5.jpg', 1),
(30, 'Souris SteelSeries Rival 600', 'Souris gaming avec double capteur pour une précision accrue', '89.99', 80, 'images/steelseries_rival600.jpg', 2),
(31, 'Clavier Mécanique HyperX Alloy Elite 2', 'Clavier gaming mécanique avec rétroéclairage dynamique RGB', '149.99', 70, 'images/hyperx_alloy_elite2.jpg', 2),
(32, 'Processeur AMD Ryzen 7 5800X', 'Processeur puissant pour le jeu et la création de contenu', '399.99', 35, 'images/ryzen_5800x.jpg', 3),
(33, 'Carte Graphique AMD Radeon RX 6800 XT', 'Carte graphique haute performance pour les gamers et créateurs', '1199.99', 40, 'images/radeon_6800xt.jpg', 3),
(34, 'Mémoire RAM Corsair Dominator Platinum 64GB', 'Kit de RAM DDR4 haut de gamme avec éclairage RGB', '499.99', 20, 'images/corsair_dominator.jpg', 3),
(35, 'Écran Dell UltraSharp 32 4K', 'Écran 32 pouces 4K avec un rendu de couleurs ultra précis', '1099.99', 25, 'images/dell_ultrasharp_4k.jpg', 1),
(36, 'Logiciel Final Cut Pro X', 'Logiciel professionnel de montage vidéo pour Mac', '299.99', 150, 'images/final_cut_pro_x.jpg', 5),
(37, 'Alimentation Seasonic Focus GX-850', 'Alimentation modulaire certifiée 80 Plus Gold', '149.99', 50, 'images/seasonic_gx850.jpg', 3),
(38, 'Casque Audio HyperX Cloud Alpha', 'Casque de jeu confortable avec son surround 7.1', '129.99', 120, 'images/hyperx_cloud_alpha.jpg', 2),
(39, 'Clé USB Samsung 512GB', 'Clé USB ultra compacte avec une capacité de stockage élevée', '69.99', 120, 'images/samsung_usb_512gb.jpg', 2),
(40, 'Tablette Samsung Galaxy Tab S7+', 'Tablette Android avec écran AMOLED de 12.4 pouces', '1199.99', 30, 'images/galaxy_tab_s7.jpg', 1),
(41, 'Souris Logitech G Pro Wireless', 'Souris sans fil avec capteur HERO pour une performance maximale', '149.99', 75, 'images/logitech_g_pro_wireless.jpg', 2),
(42, 'Webcam Microsoft LifeCam Studio', 'Webcam HD pour les conférences et le streaming', '99.99', 90, 'images/microsoft_lifecam.jpg', 2),
(43, 'Disque SSD Western Digital 2TB Black SN850', 'SSD NVMe haute vitesse pour les gamers et créateurs', '349.99', 40, 'images/wd_black_sn850.jpg', 3),
(44, 'Switch TP-Link TL-SG108', 'Switch réseau 8 ports Gigabit pour petites entreprises et bureaux', '49.99', 90, 'images/tplink_switch.jpg', 2),
(45, 'Enceinte Bluetooth Marshall Kilburn II', 'Enceinte portable avec un design rétro et un son puissant', '299.99', 50, 'images/marshall_kilburn2.jpg', 2),
(46, 'Imprimante Canon PIXMA TS8350', 'Imprimante jet d’encre multifonction avec connectivité Wi-Fi', '179.99', 40, 'images/canon_pixma.jpg', 1),
(47, 'Clé Windows Server 2019', 'Licence officielle pour Windows Server 2019', '499.99', 60, 'images/windows_server_2019.jpg', 5),
(48, 'Sac à dos Targus CitySmart', 'Sac à dos élégant et pratique pour ordinateurs portables et accessoires', '79.99', 130, 'images/targus_backpack.jpg', 4);

-- --------------------------------------------------------

--
-- Structure de la table `sauvegarde_panier`
--

DROP TABLE IF EXISTS `sauvegarde_panier`;
CREATE TABLE IF NOT EXISTS `sauvegarde_panier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_email` varchar(255) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_email` (`utilisateur_email`),
  KEY `id_produit` (`id_produit`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `email`, `mot_de_passe`, `role`) VALUES
(1, 'Gregory', 'gregory@gmail.com', 'gregory', 'client'),
(2, 'Gabriel', 'gabriel@gmail.com', 'gabriel', 'admin'),
(3, 'Nathael', 'nathael@gmail.com', 'nathael', 'client'),
(4, 'isabelle', 'isabelle@gmail.com', '$2y$10$ruXhGxih5bgtWr2bCLw4q.IRj/0pzr2//56WyiEiRU8pVK.3QCId.', 'client'),
(5, 'michel', 'michel@gmail.com', '$2y$10$m2Ko2RIBpwde2esp4nm4bex84.sIVg34/LAQr.RLu5VCdVhXOU5PC', 'client');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
