CREATE TABLE IF NOT EXISTS `#__s3_linkcache` (
	`bucket` varchar(255) NOT NULL,
	`object` varchar(255) NOT NULL,
	`expires` int(11) NOT NULL,
	`link` text NOT NULL,
	PRIMARY KEY  (`bucket`,`object`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
