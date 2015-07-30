CREATE TABLE IF NOT EXISTS `appeals` (
  `case_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `radius` float NOT NULL,
  `location` text NOT NULL,
  `crime_type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `police_force_id` int(11) NOT NULL,
  `block_id` varchar(6) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`case_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
