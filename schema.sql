CREATE TABLE IF NOT EXISTS `rnb_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `pickup_datetime` datetime NOT NULL,
  `return_datetime` datetime NOT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `room_type` varchar(255) DEFAULT NULL,
  `block_by` varchar(50) DEFAULT NULL,
  `delete_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add any additional tables or columns as needed
