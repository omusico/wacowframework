DROP TABLE IF EXISTS `empty`;
CREATE TABLE IF NOT EXISTS `empty` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `session_store`;
CREATE TABLE IF NOT EXISTS `session_store` (
  `session_id` varchar(32) NOT NULL default '' COMMENT 'Session Id',
  `expire` bigint(20) NOT NULL default '0' COMMENT '過期日',
  `data` longtext NOT NULL COMMENT 'Session 資料',
  PRIMARY KEY  (`session_id`),
  KEY `expire` (`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Session 資料表';
