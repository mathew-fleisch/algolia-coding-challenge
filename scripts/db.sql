# Dump of table payment_options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_options`;

CREATE TABLE `payment_options` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table restaurants
# ------------------------------------------------------------

DROP TABLE IF EXISTS `restaurants`;

CREATE TABLE `restaurants` (
  `local_id` int(11) NOT NULL AUTO_INCREMENT,
  `objectID` int(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `alt_phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `lat` decimal(9,6) DEFAULT NULL,
  `lng` decimal(9,6) DEFAULT NULL,
  `food_type` varchar(255) DEFAULT NULL,
  `neighborhood` varchar(255) DEFAULT NULL,
  `price` int(5) DEFAULT NULL,
  `price_range` varchar(255) DEFAULT NULL,
  `stars_count` varchar(3) DEFAULT NULL,
  `reviews_count` int(100) DEFAULT NULL,
  `dining_style` varchar(255) DEFAULT NULL,
  `payment_options` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`objectID`),
  UNIQUE KEY `objectID` (`objectID`),
  KEY `local_id` (`local_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;