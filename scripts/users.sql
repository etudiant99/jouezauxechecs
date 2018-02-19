CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(4) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(15) DEFAULT NULL,
  `nom` varchar(15) DEFAULT NULL,
  `sexe` char(1) DEFAULT NULL,
  `naissance` date DEFAULT NULL,
  `pays` varchar(15) DEFAULT NULL,
  `description` text,
  `photo` varchar(1) NOT NULL DEFAULT 'n',
  `courriel` varchar(40) DEFAULT NULL,
  `date_connection` datetime DEFAULT NULL,
  `date_inscription` date NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;
