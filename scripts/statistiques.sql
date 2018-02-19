CREATE TABLE IF NOT EXISTS `statistiques` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `uid` int(4) NOT NULL,
  `gains_b` int(3) DEFAULT '0',
  `pertes_b` int(3) DEFAULT '0',
  `nulles_b` int(3) DEFAULT '0',
  `gains_n` int(3) DEFAULT '0',
  `pertes_n` int(3) DEFAULT '0',
  `nulles_n` int(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;