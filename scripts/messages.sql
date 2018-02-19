CREATE TABLE IF NOT EXISTS `messages` (
  `mid` int(5) NOT NULL AUTO_INCREMENT,
  `envoye` date DEFAULT NULL,
  `destinataire` int(4) DEFAULT NULL,
  `origine` int(4) DEFAULT NULL,
  `titre` varchar(20) DEFAULT NULL,
  `contenu` text,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;
