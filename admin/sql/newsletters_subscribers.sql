CREATE TABLE IF NOT EXISTS `newsletters_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `otapiId` bigint(20) NOT NULL,
  `login` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `middleName` varchar(100) DEFAULT NULL,
  `sex` tinyint(1) NOT NULL,
  `email` varchar(100) NOT NULL,
  `skype` varchar(100) DEFAULT NULL,
  `subscribed` datetime NOT NULL,
  `registered` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sex` (`sex`),
  KEY `email` (`email`),
  KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;