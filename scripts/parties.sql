CREATE TABLE IF NOT EXISTS `parties` (
  `gid` int(5) NOT NULL AUTO_INCREMENT,
  `uidb` int(4) DEFAULT NULL,
  `uidn` int(4) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date NOT NULL,
  `date_dernier_coup` datetime DEFAULT NULL,
  `cadencep` int(2) DEFAULT NULL,
  `reservep` int(2) DEFAULT NULL,
  `reserve_uidb` float NOT NULL,
  `reserve_uidn` float NOT NULL,
  `finalisation` int(2) NOT NULL,
  `commentaire` text NOT NULL,
  `nulle` int(4) NOT NULL,
  `efface` int(11) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;