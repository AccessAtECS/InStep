
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table asset
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asset`;

CREATE TABLE `asset` (
  `id` int(11) NOT NULL,
  `name` varchar(145) DEFAULT NULL,
  `level` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table asset_tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asset_tag`;

CREATE TABLE `asset_tag` (
  `asset_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`asset_id`,`tag_id`),
  KEY `fk_asset_has_tag_tag1` (`tag_id`),
  CONSTRAINT `fk_asset_has_tag_asset` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_has_tag_tag1` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table comparison
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comparison`;

CREATE TABLE `comparison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idfrom` int(11) NOT NULL,
  `idto` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `owner` int(11) NOT NULL,
  `notes` text NOT NULL,
  `learner` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_asset_has_asset_asset2` (`idto`),
  KEY `fk_asset_has_asset_asset1` (`idfrom`),
  CONSTRAINT `fk_asset_has_asset_asset1` FOREIGN KEY (`idfrom`) REFERENCES `asset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_has_asset_asset2` FOREIGN KEY (`idto`) REFERENCES `asset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `asset_id` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notes_asset1` (`asset_id`),
  CONSTRAINT `fk_notes_asset1` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tag`;

CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
