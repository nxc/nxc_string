DROP TABLE IF EXISTS `nxc_string_limitations`;
CREATE TABLE `nxc_string_limitations` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `class_attribute_id` int(11) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `expression` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `error` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;