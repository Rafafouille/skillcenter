-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1build0.15.04.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 09 Juin 2016 à 23:48
-- Version du serveur :  5.6.28-0ubuntu0.15.04.1
-- Version de PHP :  5.6.4-4ubuntu6.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `competences`
--

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE IF NOT EXISTS `competences` (
`id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `groupe` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `competences`
--

INSERT INTO `competences` (`id`, `nom`, `groupe`, `position`) VALUES
(1, 'Renseigner un logiciel de simulation du comportement énergétique avec les caractéristiques du système et les paramètres externes ', 1, 0),
(2, 'Interpréter les résultats d''une simulation afin de valider une solution ou l''optimiser', 1, 0),
(3, 'Comparer et interpréter le résultat d''une simulation d''un comportement d''un système avec un comportement réel', 2, 0),
(4, 'Mettre en œuvre un protocole d''essais et de mesures sur le prototype d''une chaîne d''énergie, interpréter les résultats', 2, 0),
(5, 'Décrire un état d’avancement d’une idée, d’un principe, d’une solution, d’un projet en utilisant des outils de représentation adaptés', 4, 0),
(6, 'Présenter des résultats finalisés d’expérimentation, de démarches de réflexion, de recherche d’informations', 2, 0),
(7, 'Interagir avec le jury en vue de le convaincre', 4, 0),
(8, 'Produire des documents visuels de qualité du point de vue scientifique et technique', 3, 0),
(9, 'Veiller à la correction linguistique (grammaire, lexique, orthographe) et richesse linguistique (variété des structures, richesse et précision lexicale)', 3, 0);

-- --------------------------------------------------------

--
-- Structure de la table `groupes_competences`
--

CREATE TABLE IF NOT EXISTS `groupes_competences` (
`id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `groupes_competences`
--

INSERT INTO `groupes_competences` (`id`, `nom`, `position`) VALUES
(1, 'Modélisation', 1),
(2, 'Mesure des écarts', 2),
(3, 'support de communication', 3),
(4, 'Communication orale', 4);

-- --------------------------------------------------------

--
-- Structure de la table `indicateurs`
--

CREATE TABLE IF NOT EXISTS `indicateurs` (
`id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `details` text NOT NULL,
  `niveaux` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `competence` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `indicateurs`
--

INSERT INTO `indicateurs` (`id`, `nom`, `details`, `niveaux`, `position`, `competence`) VALUES
(1, 'Les variables du modèle sont identifiés', '', 4, 0, 1),
(13, 'Leurs influences respectives sont identifiées', '', 4, 0, 1),
(14, 'Les paramètres saisis sont réalistes', '', 4, 0, 1),
(15, 'Les scénarios de simulation sont identifiés', '', 4, 0, 2),
(16, 'Les paramètres influents sont identifiés', '', 4, 0, 2),
(17, 'Les conséquences sur le système sont identifiées', '', 4, 0, 2),
(18, 'Les modifications proposées sont pertinentes', '', 4, 0, 2),
(19, 'Les résultats de la simulation et les mesures sont corrélés', '', 4, 0, 3),
(20, 'L''analyse des écarts est méthodique', '', 4, 0, 3),
(21, 'L''interprétation des résultats est cohérente et pertinente', '', 4, 0, 3),
(22, 'L''interprétation des résultats est Les condition de l''essai sont identifiées et justifiées', '', 4, 0, 4),
(23, 'Le protocole est adapté à l''objectif', '', 4, 0, 4),
(24, 'Les observations et mesures sont méthodiquement menés', '', 4, 0, 4),
(25, 'Les incertitudes sont estimées', '', 4, 0, 4),
(26, 'L''interprétation des résultats est cohérente et pertinente', '', 4, 0, 4),
(27, 'L''étudiant a su choisir l''outil de communication adapté pour présenter un principe de solution (schémas, modèle numérique, etc.)', '', 4, 0, 5),
(28, 'L''étudiant a su présenter et justifier au moins un protocole d''expérimentation, et/ou une démarche de recherche d''informations (brevets, etc.) ', '', 4, 0, 6),
(29, 'Peut intervenir simplement, mais la communication repose sur la répétition et la reformulation ', '', 4, 0, 7),
(30, 'L''étudiant répond et réagit de façon simple.', '', 4, 0, 7),
(31, 'L''étudiant argumente, cherche à convaincre, réagit avec pertinence', '', 4, 0, 7),
(32, 'L''étudiant a su produire des documents visuels exempts de défauts scientifiques et techniques', '', 4, 0, 9),
(33, 'L''étudiant a su enrichir les documents visuels d''un vocabulaire technique en respectant la correction linguistique ', '', 4, 0, 8);

-- --------------------------------------------------------

--
-- Structure de la table `liensClassesIndicateurs`
--

CREATE TABLE IF NOT EXISTS `liensClassesIndicateurs` (
`idLien` int(11) NOT NULL,
  `indicateur` int(11) NOT NULL,
  `classe` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `liensClassesIndicateurs`
--

INSERT INTO `liensClassesIndicateurs` (`idLien`, `indicateur`, `classe`) VALUES
(3, 32, 'PT'),
(4, 1, 'PT'),
(6, 1, 'PT');

-- --------------------------------------------------------

--
-- Structure de la table `notation`
--

CREATE TABLE IF NOT EXISTS `notation` (
`id` int(11) NOT NULL,
  `note` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `eleve` int(11) NOT NULL,
  `indicateur` int(11) NOT NULL,
  `examinateur` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notation`
--

INSERT INTO `notation` (`id`, `note`, `date`, `eleve`, `indicateur`, `examinateur`) VALUES
(83, 1, '2016-04-08 09:32:45', 43, 15, 28),
(84, 3, '2016-04-08 09:32:49', 43, 17, 28);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE IF NOT EXISTS `utilisateurs` (
`id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `prenom` text NOT NULL,
  `login` text NOT NULL,
  `mdp` text NOT NULL,
  `classe` text NOT NULL,
  `statut` text NOT NULL,
  `mail` text NOT NULL,
  `notifieMail` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `login`, `mdp`, `classe`, `statut`, `mail`, `notifieMail`) VALUES
(1, 'ALLAIS', 'Raphaël', 'allais', '954z8e', '', 'admin', '', 0),
(27, 'eChaillot', 'Francois', 'chaillot', '123', 'PT', '', '', 0),
(28, 'GUERIN', 'Denis', 'guerind', 'pcxd3', '', 'admin', '', 0),
(29, 'groupe_1', '', 'gr_1', 'fl', 'TSI1', '', '', 0),
(43, 'groupe_2', '', 'gr_2', 'fl', 'TSI1', '', '', 0),
(45, 'groupe_4', '', 'gr_4', 'fl', 'TSI1', '', '', 0),
(46, 'groupe_5', '', 'gr_5', 'fl', 'TSI1', '', '', 0),
(47, 'groupe_6', '', 'gr_6', 'fl', 'TSI1', '', '', 0),
(48, 'groupe_7', '', 'gr_7', 'fl', 'TSI1', '', '', 0),
(49, 'groupe_8', '', 'gr_8', 'fl', 'TSI1', '', '', 0),
(50, 'groupe_9', '', 'gr_9', 'fl', 'TSI1', '', '', 0),
(51, 'groupe_10', '', 'gr_10', 'fl', 'TSI1', '', '', 0),
(52, 'POULET', 'Fréderic', 'fp', 'fl021', '', 'admin', '', 0),
(54, 'A_TEST', 'toto', 'tesst', '123', 'PT', '', '', 0);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `groupes_competences`
--
ALTER TABLE `groupes_competences`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `indicateurs`
--
ALTER TABLE `indicateurs`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `liensClassesIndicateurs`
--
ALTER TABLE `liensClassesIndicateurs`
 ADD PRIMARY KEY (`idLien`);

--
-- Index pour la table `notation`
--
ALTER TABLE `notation`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `groupes_competences`
--
ALTER TABLE `groupes_competences`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `indicateurs`
--
ALTER TABLE `indicateurs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT pour la table `liensClassesIndicateurs`
--
ALTER TABLE `liensClassesIndicateurs`
MODIFY `idLien` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `notation`
--
ALTER TABLE `notation`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=85;
--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=55;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
