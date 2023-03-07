CREATE TABLE IF NOT EXISTS `site_arca_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,

  `hostID` varchar(255) DEFAULT NULL,
  `tid` varchar(255) DEFAULT NULL,
  `mid` varchar(255) DEFAULT NULL,
  `orderID` varchar(255) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `opaque` varchar(255) DEFAULT NULL,
  `additionalURL` varchar(255) DEFAULT NULL,
  `rrn` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `user_paid` tinyint(1) DEFAULT NULL,
  `checked` tinyint(1) DEFAULT NULL,
  `confirmed` tinyint(1) DEFAULT NULL,
  `notified` tinyint(1) DEFAULT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
