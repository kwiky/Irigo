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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=667 ;

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
