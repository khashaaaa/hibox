CREATE TABLE IF NOT EXISTS `site_support` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `orderid` varchar(255) NOT NULL,
  `categoryid` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `parent` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `direction` varchar(10) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `added` int(11) NOT NULL,
  `userlogin` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY idx_userid (`userid`),
  KEY idx_orderid (`orderid`),
  KEY idx_parent (`parent`),
  KEY idx_dir_read (`direction`, `read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;