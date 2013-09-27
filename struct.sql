-- Freetag Structure v2.02
--
-- Table structure for table `freetags`
--

CREATE TABLE IF NOT EXISTS /*TABLE_PREFIX*/t_tagger_tags (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  tag varchar(30) NOT NULL DEFAULT '',
  raw_tag varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

--
-- Table structure for table `freetagged_objects`
--
CREATE TABLE IF NOT EXISTS /*TABLE_PREFIX*/t_tagger_tagged_objects (
  tag_id int(10) UNSIGNED NOT NULL DEFAULT '0',
  tagger_id int(10) UNSIGNED NOT NULL DEFAULT '0',
  object_id int(10) UNSIGNED NOT NULL DEFAULT '0',
  tagged_on datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`tag_id`,`tagger_id`,`object_id`),
  KEY `tag_id_index` (`tag_id`),
  KEY `tagger_id_index` (`tagger_id`),
  KEY `object_id_index` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';