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

DROP TABLE IF EXISTS `system_configures`;
CREATE TABLE IF NOT EXISTS `system_configures` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `name` varchar(100) default NULL COMMENT '名稱',
  `value` varchar(100) default NULL COMMENT '值',
  `startDateTime` datetime NOT NULL default '2009-01-01 00:00:00' COMMENT '開始日期',
  `endDateTime` datetime NOT NULL default '9999-12-31 23:59:59' COMMENT '結束日期',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系統設定檔';

DROP TABLE IF EXISTS `system_permissions`;
CREATE TABLE IF NOT EXISTS `system_permissions` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `roleId` int(10) unsigned default NULL COMMENT '角色編號',
  `resourceId` int(10) unsigned NOT NULL COMMENT '資源編號',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  PRIMARY KEY  (`id`),
  KEY `permission` (`roleId`,`resourceId`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系統權限';

INSERT INTO `system_permissions` (`id`, `roleId`, `resourceId`, `status`) VALUES
(1, 1, 1, 'y'),
(2, 1, 2, 'y'),
(3, 1, 3, 'y'),
(4, 1, 4, 'y'),
(5, 1, 5, 'y'),
(6, 1, 6, 'y'),
(7, 1, 7, 'y'),
(8, 1, 8, 'y'),
(9, 1, 9, 'y'),
(10, 2, 5, 'y'),
(11, 2, 6, 'y'),
(12, 2, 7, 'y'),
(13, 2, 8, 'y'),
(14, 2, 9, 'y'),
(15, 1, 10, 'y'),
(16, NULL, 11, 'y');

DROP TABLE IF EXISTS `system_resources`;
CREATE TABLE IF NOT EXISTS `system_resources` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `parentId` int(10) default NULL COMMENT '父選單編號',
  `name` varchar(100) default NULL COMMENT '選單名稱',
  `module` varchar(100) NOT NULL default 'admin' COMMENT '對應 module',
  `controller` varchar(100) NOT NULL default 'index' COMMENT '對應 controller',
  `action` varchar(100) NOT NULL default 'index' COMMENT '對應 action',
  `display` enum('y','n') default 'y' COMMENT '是否顯示 (y: 是, n: 否)',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系統資源';

INSERT INTO `system_resources` (`id`, `parentId`, `name`, `module`, `controller`, `action`, `display`, `status`, `createDateTime`, `updateDateTime`) VALUES
(1, NULL, '系統設定', 'admin', 'index', 'index', 'y', 'y', NULL, NULL),
(2, 1, '權限設定', 'admin', 'index', 'permission', 'y', 'y', NULL, NULL),
(3, 1, '使用者管理', 'admin', 'index', 'user', 'y', 'y', NULL, NULL),
(4, 1, '參數設定', 'admin', 'index', 'config', 'y', 'y', NULL, NULL),
(5, NULL, '會員管理', 'admin', 'member', 'index', 'y', 'y', NULL, NULL),
(6, NULL, '商品管理', 'admin', 'product', 'index', 'y', 'y', NULL, NULL),
(7, NULL, '訂單管理', 'admin', 'order', 'index', 'y', 'y', NULL, NULL),
(8, NULL, '新聞管理', 'admin', 'news', 'index', 'y', 'y', NULL, NULL),
(9, 5, '新增會員', 'admin', 'member', 'add', 'y', 'y', NULL, NULL),
(10, NULL, '檔案上傳', 'admin', 'file', 'upload', 'y', 'y', NULL, NULL),
(11, NULL, '前台首頁', 'default', 'index', 'index', 'n', 'y', NULL, NULL);

DROP TABLE IF EXISTS `system_roles`;
CREATE TABLE IF NOT EXISTS `system_roles` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自動編號',
  `name` varchar(100) default NULL COMMENT '角色名稱',
  `status` enum('y','n','x') NOT NULL default 'y' COMMENT '狀態',
  `createDateTime` datetime default NULL COMMENT '建立時間',
  `updateDateTime` datetime default NULL COMMENT '更新時間',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系統角色';

INSERT INTO `system_roles` (`id`, `name`, `status`, `createDateTime`, `updateDateTime`) VALUES
(1, '管理員', 'y', '2009-03-05 15:55:10', NULL),
(2, '內容管理', 'y', '2009-04-02 12:15:54', NULL);

DROP TABLE IF EXISTS `system_users`;
CREATE TABLE IF NOT EXISTS `system_users` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系統後台使用者';

INSERT INTO `system_users` (`id`, `roleId`, `identity`, `password`, `nickname`, `status`, `createDateTime`, `updateDateTime`) VALUES
(1, 1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '管理員', 'y', '2008-04-10 18:37:16', '2008-04-10 18:37:16');
