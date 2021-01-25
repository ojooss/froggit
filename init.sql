CREATE TABLE IF NOT EXISTS `log` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     `device` varchar(128) NOT NULL,
     `attribute` varchar(128) NOT NULL,
     `value` decimal(8,2) NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `log_uindex` (`device`,`attribute`,`timestamp`,`value`),
     KEY `log_timestamp_index` (`timestamp`),
     KEY `log_device_index` (`device`),
     KEY `log_attribute_index` (`attribute`),
     KEY `log_value_index` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
