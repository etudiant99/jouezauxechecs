CREATE TABLE IF NOT EXISTS `partiesproposees` (
  `gidp` int(5) NOT NULL AUTO_INCREMENT,
  `prospect` int(4) DEFAULT NULL,
  `origine` int(4) NOT NULL,
  `macouleur` char(1) DEFAULT NULL,
  `cadence` int(2) NOT NULL,
  `reserve` int(2) NOT NULL,
  `commentaire` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`gidp`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;