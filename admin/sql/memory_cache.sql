CREATE TABLE IF NOT EXISTS `memory_cache` (  
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` char(50) NOT NULL,
  `expires` int(11) NOT NULL,
  `cache_entity` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY idx_expires (`expires`) USING BTREE,
  UNIQUE KEY `unique_key` (`session_id`,`cache_entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;