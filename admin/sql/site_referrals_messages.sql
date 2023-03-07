CREATE TABLE IF NOT EXISTS `site_referrals_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `parent` int(11) NOT NULL,
  `login` varchar(200) NOT NULL,
  `direction` varchar(10) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY idx_login (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
