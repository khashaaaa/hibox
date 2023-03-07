INSERT INTO `delivery` (`id`, `name`, `formula`) VALUES
(1, 'EMS', '$start + (Math.ceil ($weight/1000 * 2) - 1) * $step'),
(2, 'ChinPost', '$start + Math.ceil($weight /1000- 1) * $step'),
(3, 'ChinAir', '$start +Math.ceil ($weight /1000* 10 - 1) * $step');