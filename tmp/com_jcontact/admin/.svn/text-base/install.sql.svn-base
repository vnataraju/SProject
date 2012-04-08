DROP TABLE IF EXISTS `#__jcontact_config`;
CREATE TABLE `#__jcontact_config` (
  `id` int(11) NOT NULL auto_increment,
  `regon` tinyint(1) NOT NULL,
  `maillist` varchar(60) NOT NULL default '',
  `maillist_text` varchar(60) NOT NULL default 'Select a list',
  `username` varchar(60) NOT NULL default '',
  `password` varchar(60) NOT NULL default '',
  `apikey` varchar(100) NOT NULL default '',
  `apiUrl` varchar(100) NOT NULL default 'https://app.icontact.com/icp',
  `secret` varchar(100) NOT NULL default '',
  `wrapperurl` varchar(150) NOT NULL default '',
  `mailsubj1` varchar(200) NOT NULL default '',
  `mailcont1` tinyblob NOT NULL,
  `mailsubj2` varchar(200) NOT NULL default '',
  `mailcont2` text NOT NULL,
  `show_optional` tinyint(1) NOT NULL,
  `show_for_all` tinyint(1) NOT NULL,
  `signup_message` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;