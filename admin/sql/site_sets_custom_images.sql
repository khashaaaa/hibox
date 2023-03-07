
--
-- Структура таблицы `site_sets_custom_images`
--

CREATE TABLE IF NOT EXISTS `site_sets_custom_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeId` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `picture` text NOT NULL,
  `language` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
