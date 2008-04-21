DROP TABLE IF EXISTS `empty`;
CREATE TABLE `empty` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `status` enum('y','n','x') NOT NULL default 'y',
  `createdDateTime` datetime default NULL,
  `updatedDateTime` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
