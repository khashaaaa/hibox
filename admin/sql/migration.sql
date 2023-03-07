CREATE TABLE IF NOT EXISTS `migration` (
  `name` varchar(255) NOT NULL,
  `apply_time` datetime NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;