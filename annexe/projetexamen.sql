-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  mer. 10 avr. 2019 à 16:12
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projetexamen`
--

-- --------------------------------------------------------

--
-- Structure de la table `grade`
--

CREATE TABLE `grade` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `skill_id` int(11) DEFAULT NULL,
  `grades` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `grade`
--

INSERT INTO `grade` (`id`, `user_id`, `skill_id`, `grades`) VALUES
(1, NULL, 3, NULL),
(2, NULL, 3, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20190216091345', '2019-02-28 10:13:24'),
('20190216091709', '2019-02-28 10:13:24'),
('20190221213152', '2019-02-28 10:13:24'),
('20190321091327', '2019-03-21 09:13:56'),
('20190409124819', '2019-04-09 12:48:31');

-- --------------------------------------------------------

--
-- Structure de la table `rank`
--

CREATE TABLE `rank` (
  `id` int(11) NOT NULL,
  `rank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `skill`
--

CREATE TABLE `skill` (
  `id` int(11) NOT NULL,
  `skill` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `skill`
--

INSERT INTO `skill` (`id`, `skill`) VALUES
(2, 'Francais'),
(3, 'Mathematiques'),
(4, 'Histoire'),
(5, 'Geograhie'),
(6, 'Anglais');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usercode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idrank` int(11) NOT NULL,
  `class` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `firstname`, `lastname`, `password`, `usercode`, `idrank`, `class`) VALUES
(1, 'André', 'Spilmont', 'spilmont', '0001AS', 1, 'ProfCM2'),
(2, 'Christophe', 'Spilmont', 'tof', '0004CS', 3, 'CP'),
(3, 'Sophie', 'Claisse', 'soph', '0002SC', 3, 'CE2'),
(4, 'Bryan', 'Bultot', 'darkoni', '0003BB', 3, 'CE1'),
(5, 'Gregory', 'Caron', 'greg', '0006GC', 3, 'ProfCP'),
(7, 'Stevens', 'Dorchies', 'fergus', '0007DS', 3, 'CM2'),
(8, 'Geoffrey', 'Dohen', 'lejuif', '0008GD', 1, 'ProfCM1'),
(9, 'test', 'test', 'test', 'test', 3, 'CM1'),
(10, 'Sebastien', 'Bouret', 'almalrich', '0009BS', 3, 'CP');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_595AAE34A76ED395` (`user_id`),
  ADD KEY `IDX_595AAE345585C142` (`skill_id`);

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `rank`
--
ALTER TABLE `rank`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `skill`
--
ALTER TABLE `skill`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `grade`
--
ALTER TABLE `grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `rank`
--
ALTER TABLE `rank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `skill`
--
ALTER TABLE `skill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `grade`
--
ALTER TABLE `grade`
  ADD CONSTRAINT `FK_595AAE345585C142` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`id`),
  ADD CONSTRAINT `FK_595AAE34A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
