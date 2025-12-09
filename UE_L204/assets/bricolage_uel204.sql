-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 04 déc. 2025 à 10:55
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bricolage_uel204`
--

-- --------------------------------------------------------

--
-- Structure de la table `outil`
--

CREATE TABLE `outil` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL,
  `tarif_journee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `outil`
--

INSERT INTO `outil` (`id`, `nom`, `quantite`, `tarif_journee`) VALUES
(1, 'ponceuse parquet', 10, 50),
(2, 'shampouineuse', 5, 30),
(3, 'perforateur', 10, 20),
(4, 'tronçonneuse', 10, 30),
(5, 'diable', 15, 10),
(6, 'scie circulaire', 10, 20),
(7, 'perceuse visseuse', 15, 15),
(8, 'tondeuse', 7, 10),
(9, 'scie sauteuse', 12, 10),
(10, 'niveau laser', 15, 10),
(11, 'rainureuse', 5, 30),
(12, 'taille-haie', 10, 25),
(13, 'serre-joint', 10, 10),
(14, 'caisse à outils', 15, 10),
(15, 'décapeur', 5, 15),
(16, 'pistolet à colle', 10, 15),
(17, 'débroussailleuse', 13, 20),
(18, 'coupe-bordure', 8, 15),
(19, 'nettoyeur haute pression', 15, 20),
(20, 'échelle', 20, 10),
(21, 'compresseur', 6, 30),
(22, 'aspirateur de chantier', 10, 15);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `outil_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `identifiant` varchar(255) NOT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `identifiant`, `motdepasse`, `role`) VALUES
(1, 'Robert', 'bricoleur_du_87', 'user'),
(2, 'Admin', 'admin123', 'admin'),
(3, 'Julie', 'mdp456', 'user'),
(4, 'Mathilde', 'mdp789', 'user'),
(5, 'Pascal', 'mdp123', 'user'),
(6, 'Marie', 'mdp456', 'user'),
(7, 'Taslim', 'mdp789', 'user'),
(8, 'Safiya', 'mdp123', 'user'),
(9, 'Lucie', 'mdp123', 'user'),
(10, 'Stéphane', 'mdp456', 'user'),
(11, 'Alexis', 'mdp789', 'user'),
(12, 'Martin', 'mdp123', 'user'),
(13, 'Enzo', 'mdp123', 'user'),
(14, 'Patrick', 'mdp456', 'user'),
(15, 'Laurence', 'mdp789', 'user'),
(16, 'Paul', 'mdp123', 'user'),
(17, 'Jacques', 'mdp789', 'user'),
(18, 'Laure', 'mdp123', 'user'),
(19, 'Michel', 'mdp456', 'user'),
(20, 'Annick', 'mdp789', 'user');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `outil`
--
ALTER TABLE `outil`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservation_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_reservation_outil` (`outil_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `outil`
--
ALTER TABLE `outil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_reservation_outil` FOREIGN KEY (`outil_id`) REFERENCES `outil` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservation_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
