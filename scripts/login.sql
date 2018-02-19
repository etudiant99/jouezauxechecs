CREATE TABLE IF NOT EXISTS `login` (
  `uid` int(4) NOT NULL,
  `pseudo` varchar(20) NOT NULL,
  `elo` int(4) NOT NULL DEFAULT '1500',
  `coefficient` int(4) NOT NULL,
  `bidon` varchar(40) DEFAULT NULL,
  `connecte` tinyint(1) NOT NULL,
  `date_inscription` date NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
