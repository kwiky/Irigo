SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `irigo`
--

-- --------------------------------------------------------

--
-- Structure de la table `arrets`
--

DROP TABLE IF EXISTS `arrets`;
CREATE TABLE IF NOT EXISTS `arrets` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `nom` text NOT NULL,
  `lat` int(11) DEFAULT NULL,
  `long` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `arrets`
--

INSERT INTO `arrets` (`id`, `nom`, `lat`, `long`) VALUES
(1, 'Angers - Roseraie', NULL, NULL),
(2, 'Jean XXIII', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `horaires`
--

DROP TABLE IF EXISTS `horaires`;
CREATE TABLE IF NOT EXISTS `horaires` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ligne_id` smallint(5) unsigned NOT NULL,
  `sens` tinyint(3) unsigned NOT NULL,
  `arret_id` smallint(5) unsigned NOT NULL,
  `horaire` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `horaires`
--

INSERT INTO `horaires` (`id`, `ligne_id`, `sens`, `arret_id`, `horaire`) VALUES
(1, 1, 1, 1, '05:51:00'),
(2, 1, 1, 2, '05:54:00');

-- --------------------------------------------------------

--
-- Structure de la table `lignes`
--

DROP TABLE IF EXISTS `lignes`;
CREATE TABLE IF NOT EXISTS `lignes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(2) NOT NULL,
  `type_ligne_id` tinyint(3) unsigned NOT NULL,
  `nom` text NOT NULL,
  `couleur` varchar(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `lignes`
--

INSERT INTO `lignes` (`id`, `numero`, `type_ligne_id`, `nom`, `couleur`) VALUES
(1, 'A', 1, 'Angers Roseraie – Avrillé Ardenne', '#ff0000');

-- --------------------------------------------------------

--
-- Structure de la table `sens`
--

DROP TABLE IF EXISTS `sens`;
CREATE TABLE IF NOT EXISTS `sens` (
  `ligne_id` smallint(5) unsigned NOT NULL,
  `sens` tinyint(3) unsigned NOT NULL,
  `depuis` text NOT NULL,
  `vers` text NOT NULL,
  PRIMARY KEY (`ligne_id`,`sens`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `sens`
--

INSERT INTO `sens` (`ligne_id`, `sens`, `depuis`, `vers`) VALUES
(1, 1, 'Angers Roseraie', 'Avrillé Ardenne'),
(1, 2, 'Avrillé Ardenne', 'Angers Roseraie');

-- --------------------------------------------------------

--
-- Structure de la table `type_lignes`
--

DROP TABLE IF EXISTS `type_lignes`;
CREATE TABLE IF NOT EXISTS `type_lignes` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `type_lignes`
--

INSERT INTO `type_lignes` (`id`, `libelle`) VALUES
(1, 'Tramway'),
(2, 'Bus');