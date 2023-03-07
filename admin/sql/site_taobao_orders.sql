CREATE TABLE IF NOT EXISTS `site_taobao_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `secret` text NOT NULL,
  `orderid` varchar(255) NOT NULL,
  `vendorid` varchar(255) NOT NULL,
  `tradeid` varchar(255) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `account` varchar(100) NOT NULL,
  `stop_sync` tinyint(1) NOT NULL,
  `last_sync` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8199 ;
