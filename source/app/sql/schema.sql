DROP TABLE IF EXISTS `empty`;
CREATE TABLE IF NOT EXISTS `empty` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `functions`;
CREATE TABLE IF NOT EXISTS `functions` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `moduleId` int(10) unsigned NOT NULL COMMENT '模組編號',
  `functionId` int(10) unsigned default NULL COMMENT '功能編號',
  `name` varchar(20) NOT NULL COMMENT '名稱',
  `link` varchar(200) default NULL COMMENT '連結',
  `description` varchar(200) default NULL COMMENT '描述',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `moduleId` (`moduleId`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='功能';


DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `name` varchar(20) NOT NULL COMMENT '名稱',
  `description` varchar(200) default NULL COMMENT '描述',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模組';


DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `roleId` int(10) unsigned NOT NULL COMMENT '角色編號',
  `functionId` int(10) unsigned NOT NULL COMMENT '功能編號',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  PRIMARY KEY  (`id`),
  KEY `permission` (`roleId`,`functionId`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='權限';


DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色';


DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `roleId` int(10) unsigned NOT NULL default '1' COMMENT '角色編號',
  `identity` varchar(20) NOT NULL COMMENT '帳號',
  `password` varchar(32) NOT NULL COMMENT '密碼',
  `nickname` varchar(50) default NULL COMMENT '暱稱',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `auth` (`identity`,`password`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='後台使用者';

INSERT INTO `users` (`id`, `roleId`, `identity`, `password`, `nickname`, `status`, `createDateTime`, `updateDateTime`) VALUES
(1, 1, 'admin', '12cac4a6a8173c46b3ba9ddea00fefd3', '管理員', 'y', '2008-04-10 18:37:16', '2008-04-10 18:37:16');
