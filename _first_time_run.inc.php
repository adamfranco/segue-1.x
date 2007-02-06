<? /* $Id$ */

// segue first-time run stuff

/******************************************************************************
 * If tables don't exist, create them
 ******************************************************************************/
$neededTables = array (
	"class",
	"classgroup",
	"discussion",
	"log",
	"media",
	"page",
	"permission",
	"section",
	"site",
	"site_editors",
	"slot",
	"story",
	"ugroup",
	"ugroup_user",
	"user",
	"tags"
);

$query = "SHOW TABLES";
$r = db_query($query);
$existingTables = array();
if (is_resource($r)) {
	while($a = db_fetch_assoc($r)) {
		foreach($a as $k => $v) {
			$existingTables[] = $v;
		}
	}
}

$allTablesExist = true;
foreach ($neededTables as $table) {
	if (!in_array($table,$existingTables)) {
		// If this is the first missing table, print a heading.
		if ($allTablesExist)
			print "\n<h3>Auto-Configuring Segue Database Tables...</h3>";
		
		print "\nMissing Table: $table<br />";
		print "\n &nbsp; - Inserting Table: $table<br /><br />";
		$allTablesExist = false;	
	}
}

if (!$allTablesExist) {

/******************************************************************************
 * MAKE SURE THAT THIS IS UP TO DATE WITH THE TABLE STRUCTURE.
 *	DUMPED ON: 2003-06-03 
 ******************************************************************************/
	$query = "
		CREATE TABLE class (
		  class_id int(10) unsigned NOT NULL auto_increment,
		  class_external_id varchar(255) default NULL,
		  class_department varchar(255) default NULL,
		  class_number varchar(15) NOT NULL default '000',
		  class_section varchar(50) default NULL,
		  class_name varchar(255) NOT NULL default '',
		  FK_owner int(10) unsigned default NULL,
		  FK_ugroup int(10) unsigned default NULL,
		  class_semester varchar(50) NOT NULL default 'w',
		  class_year year(4) NOT NULL default '0000',
		  FK_classgroup int(10) unsigned default NULL,
		  PRIMARY KEY  (class_id),
		  UNIQUE KEY class_department (class_department,class_number,class_section,class_semester,class_year),
		  UNIQUE KEY class_external_id (class_external_id),
		  KEY class_name (class_name),
		  KEY FK_owner (FK_owner),
		  KEY FK_classgroup (FK_classgroup),
		  KEY FK_ugroup (FK_ugroup),
		  KEY class_department_2 (class_department),
		  KEY class_number (class_number),
		  KEY class_section (class_section),
		  KEY class_semester (class_semester),
		  KEY class_year (class_year)
		) TYPE=MyISAM;
		
		CREATE TABLE classgroup (
		  classgroup_id int(10) unsigned NOT NULL auto_increment,
		  FK_owner int(10) unsigned NOT NULL default '0',
		  classgroup_name varchar(255) NOT NULL default '',
		  PRIMARY KEY  (classgroup_id),
		  UNIQUE KEY classgroup_name (classgroup_name),
		  KEY FK_owner (FK_owner)
		) TYPE=MyISAM;
		
		CREATE TABLE discussion (
		  discussion_id int(10) unsigned NOT NULL auto_increment,
		  FK_author int(10) unsigned NOT NULL default '0',
		  discussion_tstamp timestamp(14) NOT NULL,
		  discussion_subject varchar(255) NOT NULL default '',
		  discussion_content mediumblob NOT NULL,
		  discussion_rate int(10) unsigned default NULL,
		  FK_story int(10) unsigned NOT NULL default '0',
		  discussion_order int(10) unsigned NOT NULL default '0',
		  FK_parent int(10) unsigned default NULL,
		  FK_media int(10) unsigned default NULL,
		  PRIMARY KEY  (discussion_id),
		  KEY FK_author (FK_author),
		  KEY FK_story (FK_story),
		  KEY discussion_order (discussion_order),
		  KEY FK_parent (FK_parent),
		  KEY discussion_tstamp (discussion_tstamp)
		) TYPE=MyISAM;
		
		CREATE TABLE log (
		  log_id int(10) unsigned NOT NULL auto_increment,
		  log_tstamp timestamp(14) NOT NULL,
		  log_type enum('login','change_auser','media_upload','media_delete','media_error','add_site','add_section','add_page','add_story','classgroups','copy_site','copy_section','copy_page','copy_story','delete_site','delete_section','delete_page','delete_story','edit_site','edit_section','edit_page','edit_story','move_site','move_section','move_page','move_story') NOT NULL default 'login',
		  FK_luser int(10) unsigned NOT NULL default '0',
		  FK_auser int(10) unsigned NOT NULL default '0',
		  FK_slot int(10) unsigned default NULL,
		  FK_siteunit int(10) unsigned default '0',
		  log_siteunit_type enum('site','section','page','story') default 'site',
		  log_desc blob,
		  PRIMARY KEY  (log_id),
		  KEY FK_luser (FK_luser),
		  KEY FK_auser (FK_auser),
		  KEY FK_siteunit (FK_siteunit),
		  KEY FK_site (FK_slot)
		) TYPE=MyISAM;
		
		CREATE TABLE media (
		  media_id int(10) unsigned NOT NULL auto_increment,
		  FK_site int(10) unsigned default NULL,
		  FK_createdby int(10) unsigned NOT NULL default '0',
		  media_tag varchar(255) NOT NULL default '',
		  media_location enum('local','remote') NOT NULL default 'local',
		  media_type enum('image','file','other') NOT NULL default 'other',
		  FK_updatedby int(10) unsigned NOT NULL default '0',
		  media_updated_tstamp timestamp(14) NOT NULL,
		  media_size int(10) unsigned default NULL,		
		  is_published TINYINT( 1 ) DEFAULT '0' NOT NULL,
		  title_whole varchar(255) default NULL,
		  title_part varchar(255) default NULL,
		  author varchar(255) default NULL,
		  pagerange varchar(255) default NULL,
		  publisher varchar(255) default NULL,
		  pubyear int(4) unsigned default NULL,
		  isbn varchar(100) default NULL,
		  PRIMARY KEY  (media_id),
		  UNIQUE KEY uniqueness (FK_site,FK_createdby,media_tag,media_location),
		  KEY FK_site (FK_site),
		  KEY FK_createdby (FK_createdby),
		  KEY media_tag (media_tag),
		  KEY media_type (media_type),
		  KEY media_location (media_location),
		  KEY media_updated_tstamp (media_updated_tstamp),
		  KEY media_size (media_size),
		  KEY FK_updatedby (FK_updatedby)
		) TYPE=MyISAM;
		
		CREATE TABLE page (
		  page_id int(10) unsigned NOT NULL auto_increment,
		  FK_section int(10) unsigned NOT NULL default '0',
		  page_order int(10) unsigned NOT NULL default '0',
		  page_location enum('left','right') default NULL,
		  page_title varchar(255) NOT NULL default '',
		  FK_updatedby int(10) unsigned NOT NULL default '0',
		  page_updated_tstamp timestamp(14) NOT NULL,
		  FK_createdby int(10) unsigned NOT NULL default '0',
		  page_created_tstamp timestamp(14) NOT NULL,
		  page_text MEDIUMBLOB,
		  page_active enum('0','1') NOT NULL default '0',
		  page_activate_tstamp timestamp(14) NOT NULL,
		  page_deactivate_tstamp timestamp(14) NOT NULL,
		  page_show_creator enum('0','1') NOT NULL default '0',
		  page_show_editor enum('0','1') default NULL,
		  page_show_date enum('0','1') NOT NULL default '0',
		  page_show_hr enum('0','1') NOT NULL default '0',
		  page_display_type enum('page','heading','divider','link','content','rss','tags','participants') NOT NULL default 'page',
		  FK_media int(10) unsigned default NULL,
		  page_story_order enum('custom','addeddesc','addedasc','editeddesc','editedasc','author','editor','category','titleasc','titledesc') NOT NULL default 'custom',
		  page_archiveby varchar(255) NOT NULL default '',
		  page_locked enum('0','1') NOT NULL default '0',
		  page_ediscussion enum('0','1') NOT NULL default '0',
		  PRIMARY KEY  (page_id),
		  KEY FK_section (FK_section),
		  KEY FK_updatedby (FK_updatedby),
		  KEY FK_createdby (FK_createdby),
		  KEY page_title (page_title),
		  KEY page_order (page_order),
		  KEY FK_media (FK_media)
		) TYPE=MyISAM;
		
		CREATE TABLE permission (
		  permission_id int(10) unsigned NOT NULL auto_increment,
		  FK_editor int(10) unsigned default NULL,
		  permission_editor_type enum('user','ugroup','everyone','institute') NOT NULL default 'user',
		  FK_scope_id int(10) unsigned NOT NULL default '0',
		  permission_scope_type enum('site','section','page','story') NOT NULL default 'site',
		  permission_value set('v','a','e','d','di') NOT NULL default '',
		  PRIMARY KEY  (permission_id),
		  UNIQUE KEY uniq (FK_editor,permission_editor_type,FK_scope_id,permission_scope_type),
		  KEY FK_editor (FK_editor,permission_editor_type),
		  KEY FK_scope_id (FK_scope_id,permission_scope_type)
		) TYPE=MyISAM;
		
		CREATE TABLE section (
		  section_id int(10) unsigned NOT NULL auto_increment,
		  FK_site int(10) unsigned NOT NULL default '0',
		  section_order int(10) unsigned NOT NULL default '0',
		  section_title varchar(255) NOT NULL default '',
		  FK_updatedby int(10) unsigned NOT NULL default '0',
		  section_updated_tstamp timestamp(14) NOT NULL,
		  FK_createdby int(10) unsigned NOT NULL default '0',
		  section_created_tstamp timestamp(14) NOT NULL,
		  section_active enum('0','1') NOT NULL default '0',
		  section_hide_sidebar enum('0','1') NOT NULL default '0',
		  section_activate_tstamp timestamp(14) NOT NULL,
		  section_deactivate_tstamp timestamp(14) NOT NULL,
		  section_locked enum('0','1') NOT NULL default '0',
		  section_display_type enum('section','heading','divider','link') NOT NULL default 'section',
		  FK_media int(10) unsigned default NULL,
		  PRIMARY KEY  (section_id),
		  KEY FK_site (FK_site),
		  KEY section_order (section_order),
		  KEY FK_updatedby (FK_updatedby),
		  KEY FK_createdby (FK_createdby),
		  KEY section_title (section_title),
		  KEY FK_media (FK_media)
		) TYPE=MyISAM;
		
		CREATE TABLE site (
		  site_id int(10) unsigned NOT NULL auto_increment,
		  site_title varchar(255) NOT NULL default '',
		  site_theme varchar(255) NOT NULL default '',
		  site_themesettings blob NOT NULL,
		  site_header blob,
		  site_footer blob,
		  FK_updatedby int(10) unsigned NOT NULL default '0',
		  site_updated_tstamp timestamp(14) NOT NULL,
		  FK_createdby int(10) unsigned NOT NULL default '0',
		  site_created_tstamp timestamp(14) NOT NULL,
		  site_active enum('0','1') NOT NULL default '0',
		  site_activate_tstamp timestamp(14) NOT NULL,
		  site_deactivate_tstamp timestamp(14) NOT NULL,
		  site_listed enum('0','1') NOT NULL default '0',
		  PRIMARY KEY  (site_id),
		  KEY site_title (site_title),
		  KEY FK_updatedby (FK_updatedby),
		  KEY FK_createdby (FK_createdby),
		  KEY site_listed (site_listed)
		) TYPE=MyISAM;
		
		CREATE TABLE site_editors (
		  FK_site int(10) unsigned NOT NULL default '0',
		  FK_editor int(10) unsigned default NULL,
		  site_editors_type enum('user','ugroup','everyone','institute') NOT NULL default 'user',
		  UNIQUE KEY FK_site (FK_site,FK_editor,site_editors_type)
		) TYPE=MyISAM;
		
		CREATE TABLE slot (
		  slot_id int(10) unsigned NOT NULL auto_increment,
		  slot_name varchar(255) default NULL,
		  FK_owner int(10) unsigned NOT NULL default '0',
		  FK_assocsite int(10) unsigned default NULL,
		  FK_site int(10) unsigned default NULL,
		  slot_type enum('class','personal','system','other','publication') NOT NULL default 'class',
		  slot_uploadlimit int(10) unsigned default NULL,
		  PRIMARY KEY  (slot_id),
		  UNIQUE KEY FK_site (FK_site),
		  UNIQUE KEY slot_name (slot_name),
		  KEY FK_owner (FK_owner),
		  KEY FK_assocsite (FK_assocsite),
		  KEY slot_type (slot_type)
		) TYPE=MyISAM;
		
		CREATE TABLE story (
		  story_id int(10) unsigned NOT NULL auto_increment,
		  FK_page int(10) unsigned NOT NULL default '0',
		  story_order int(10) unsigned NOT NULL default '0',
		  story_title varchar(255) NOT NULL default '',
		  FK_updatedby int(10) unsigned NOT NULL default '0',
		  story_updated_tstamp timestamp(14) NOT NULL,
		  FK_createdby int(10) unsigned NOT NULL default '0',
		  story_created_tstamp timestamp(14) NOT NULL,
		  story_text_short mediumblob NOT NULL,
		  story_text_long mediumblob NOT NULL,
		  story_active enum('0','1') NOT NULL default '0',
		  story_activate_tstamp timestamp(14) NOT NULL,
		  story_deactivate_tstamp timestamp(14) NOT NULL,
		  story_discussable enum('0','1') NOT NULL default '0',
		  story_discussemail ENUM('0','1') NOT NULL default '0',
		  story_discussdisplay ENUM('1','2') NOT NULL default '1',
		  story_discussauthor ENUM('1','2') NOT NULL default '1',
		  story_discusslabel varchar(128) NULL default '',
		  story_category varchar(255) NOT NULL default '',
		  story_text_type enum('text','html') NOT NULL default 'text',
		  story_display_type enum('story','image','file','link','rss') NOT NULL default 'story',
		  FK_media int(10) unsigned default NULL,
		  story_locked enum('0','1') NOT NULL default '0',
		  PRIMARY KEY  (story_id),
		  KEY FK_page (FK_page),
		  KEY story_order (story_order),
		  KEY story_title (story_title),
		  KEY FK_media (FK_media),
		  KEY FK_createdby (FK_createdby),
		  KEY FK_updatedby (FK_updatedby)
		) TYPE=MyISAM;
		
		CREATE TABLE ugroup (
		  ugroup_id int(10) unsigned NOT NULL auto_increment,
		  ugroup_name varchar(255) NOT NULL default '',
		  ugroup_type enum('class','other') NOT NULL default 'class',
		  FK_owner int(11) default NULL,
		  PRIMARY KEY  (ugroup_id),
		  UNIQUE KEY ugroup_name (ugroup_name),
		  KEY FK_owner (FK_owner)
		) TYPE=MyISAM;
		
		CREATE TABLE ugroup_user (
		  FK_ugroup int(10) unsigned NOT NULL default '0',
		  FK_user int(10) unsigned NOT NULL default '0',
		  PRIMARY KEY  (FK_ugroup,FK_user)
		) TYPE=MyISAM;
		
		CREATE TABLE user (
		  user_id int(10) unsigned NOT NULL auto_increment,
		  user_uname varchar(255) NOT NULL default '',
		  user_pass varchar(255) NOT NULL default '',
		  user_fname varchar(255) NOT NULL default '',
		  user_first_name varchar(255) default NULL,
		  user_last_name varchar(255) default NULL,
		  user_email varchar(255) NOT NULL default '',
		  user_type enum('stud','prof','staff','visitor','guest','admin') NOT NULL default 'stud',
		  user_authtype enum('ldap','db','pam') NOT NULL default 'ldap',
		  PRIMARY KEY  (user_id),
		  UNIQUE KEY user_uname (user_uname),
		  KEY user_type (user_type),
		  KEY user_fname (user_fname),
		  KEY user_last_name (user_last_name)
		) TYPE=MyISAM;
		
		CREATE TABLE tags (
		  record_type varchar(128) NOT NULL default '',
		  FK_record_id int(11) NOT NULL default '0',
		  FK_user_id int(11) NOT NULL default '0',
		  record_tag varchar(255) NOT NULL default '',
		  record_tag_added timestamp(14) NOT NULL,
		  KEY FK_record_id (FK_record_id),
		  KEY FK_user_id (FK_user_id),
		  KEY record_type (record_type(7)),
		  KEY record_tag (record_tag(10))
		) TYPE=MyISAM

	";
	$queryArray = explode(";",$query);
	foreach ($queryArray AS $query) {
//		print "<br />\"$query\"";
			db_query($query);
			if (mysql_error()) {
				print "\n<hr />";
				printpre($query);
				printpre(mysql_error());
			}
	}
}

/******************************************************************************
 * Insert Defaults into the tables
 ******************************************************************************/

$u = new user();
$u->_genDefaultAdminUser();
$u->insertDB();

// insert the template and sample sites
$query = "
	SELECT
		COUNT(*) AS numslots
	FROM
		slot
";
$r = db_query($query);
$a = db_fetch_assoc($r);

if ($a[numslots] == 0) {
	$query = "
		INSERT INTO `media` VALUES (6, 5, 1, 'http://segue.middlebury.edu/sites/segue', 'remote', 'other', 1, '2003-05-30 13:49:28', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (5, 5, 1, 'http://www.google.com', 'remote', 'other', 1, '2003-05-30 13:46:09', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (4, 5, 1, ' http://www.middlebury.edu', 'remote', 'other', 1, '2003-05-30 13:35:09', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (7, 5, 1, 'http://www.middlebury.edu', 'remote', 'other', 1, '2005-12-15 20:01:12', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (8, 1, 1, 'http://', 'remote', 'other', 1, '2005-12-15 20:38:26', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (9, 5, 1, 'http://', 'remote', 'other', 1, '2005-12-15 20:54:58', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (10, 4, 1, 'http://', 'remote', 'other', 1, '2005-12-16 00:24:24', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (11, 4, 1, '[[linkpath]]/index.php?&action=rss&site=[[site]]&section=12&page=39&', 'remote', 'other', 1, '2005-12-18 20:36:51', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (12, 4, 1, '[[linkpath]]/index.php?&action=rss&site=[[site]]&section=21&page=51&', 'remote', 'other', 1, '2005-12-18 20:36:04', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (14, 3, 1, '[[linkpath]]/index.php?&action=rss&site=[[site]]&section=5&page=64&', 'remote', 'other', 1, '2005-12-18 20:37:58', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		INSERT INTO `media` VALUES (18, 4, 1, 'http://www.middlebury.edu', 'remote', 'other', 1, '2005-12-18 12:53:55', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
		
		INSERT INTO `page` VALUES (1, 14, 0, 'left', 'Description', 1, '2005-12-15 21:16:38', 1, '2005-12-15 19:50:55', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (2, 14, 1, 'left', 'Articles', 1, '2005-12-15 19:55:21', 1, '2005-12-15 19:55:21', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (3, 14, 2, 'left', 'Links', 1, '2005-12-15 21:46:50', 1, '2005-12-15 19:55:40', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (4, 1, 0, 'left', 'Page One', 1, '2005-12-15 20:15:12', 1, '2005-12-15 20:15:12', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (5, 1, 1, 'left', 'Page Two', 1, '2005-12-15 20:15:27', 1, '2005-12-15 20:15:27', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (6, 1, 2, 'left', 'Page Three', 1, '2005-12-15 20:42:09', 1, '2005-12-15 20:15:46', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (15, 18, 1, 'left', 'Discussion', 1, '2005-12-15 21:22:58', 1, '2005-12-15 21:00:03', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (8, 17, 1, 'right', 'Sidebar Content', 1, '2005-12-15 20:48:26', 1, '2005-12-15 20:41:19', 0x0d0a496e206164646974696f6e20746f2070616765206c696e6b732c20796f752063616e2061646420616e792074657874206f722048544d4c20636f6e74656e7420796f752077616e7420746f2065697468657220746865206c656674206f722072696768742073696465626172206f6620796f757220736964650d0a, '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'content', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (9, 17, 0, 'left', 'Features', 1, '2005-12-15 20:53:50', 1, '2005-12-15 20:41:49', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (10, 17, 2, 'left', 'Categories', 1, '2005-12-15 20:48:38', 1, '2005-12-15 20:47:44', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'tags', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (11, 18, 0, 'left', 'Overview', 1, '2005-12-15 20:58:39', 1, '2005-12-15 20:54:58', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (12, 18, 4, 'right', 'Sidebar Content', 1, '2005-12-15 21:48:22', 1, '2005-12-15 20:54:58', 0x0d0a496e206164646974696f6e20746f2070616765206c696e6b732c20796f752063616e2061646420616e792074657874206f722048544d4c20636f6e74656e7420796f752077616e7420746f2065697468657220746865206c656674206f722072696768742073696465626172206f6620796f757220736974652e0d0a, '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'content', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (13, 18, 3, 'left', 'Categories', 1, '2005-12-15 21:48:22', 1, '2005-12-15 20:54:58', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'tags', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (14, 19, 0, 'left', 'A Single Page', 1, '2005-12-15 20:59:39', 1, '2005-12-15 20:59:39', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', '1', '0', '0');
		INSERT INTO `page` VALUES (17, 9, 0, 'left', 'Description', 1, '2005-12-15 21:55:07', 1, '2005-12-15 21:50:42', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (18, 9, 3, 'left', 'Requirements', 1, '2005-12-17 17:19:35', 1, '2005-12-15 21:51:25', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (19, 9, 4, 'left', 'Grading', 1, '2005-12-17 17:19:35', 1, '2005-12-15 21:51:39', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (20, 9, 5, 'left', 'Professor', 1, '2005-12-17 17:19:35', 1, '2005-12-15 21:51:51', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (21, 9, 6, 'right', 'Announcements', 1, '2005-12-17 17:19:35', 1, '2005-12-15 21:52:22', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '1', '0', 'page', NULL, 'addeddesc', '5', '0', '0');
		INSERT INTO `page` VALUES (22, 9, 2, 'left', 'Syllabus', 1, '2005-12-17 17:19:35', 1, '2005-12-15 21:54:07', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (49, 12, 2, 'right', 'Recent Posts', 1, '2005-12-16 08:01:47', 1, '2005-12-16 07:59:17', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'rss', 11, 'custom', '7', '0', '0');
		INSERT INTO `page` VALUES (50, 9, 7, 'right', 'Links', 1, '2005-12-17 17:19:35', 1, '2005-12-17 17:19:27', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (51, 21, 0, 'left', 'Topics', 1, '2005-12-17 17:44:40', 1, '2005-12-17 17:33:02', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (53, 21, 1, 'right', 'Recent Topics', 1, '2005-12-17 17:34:22', 1, '2005-12-17 17:34:22', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'rss', 12, 'custom', '5', '0', '0');
		INSERT INTO `page` VALUES (56, 20, 8, 'left', 'week 8', 1, '2005-12-18 12:50:32', 1, '2005-12-17 18:20:26', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (57, 20, 9, 'left', 'Week 9', 1, '2005-12-18 12:50:29', 1, '2005-12-17 18:20:37', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (58, 20, 10, 'left', 'Week 10', 1, '2005-12-18 12:50:26', 1, '2005-12-17 18:20:48', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (59, 20, 11, 'left', 'Week 11', 1, '2005-12-18 12:50:23', 1, '2005-12-17 18:20:59', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (60, 20, 12, 'left', 'Week 12', 1, '2005-12-18 12:50:20', 1, '2005-12-17 18:21:10', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (61, 20, 13, 'left', 'Week 13', 1, '2005-12-17 18:21:23', 1, '2005-12-17 18:21:23', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (63, 5, 0, 'right', 'Categories', 1, '2005-12-17 20:33:07', 1, '2005-12-17 20:33:07', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'tags', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (64, 5, 1, 'left', 'Blog', 1, '2005-12-17 20:36:51', 1, '2005-12-17 20:33:17', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '1', '1', 'page', NULL, 'custom', '20', '0', '0');
		INSERT INTO `page` VALUES (37, 9, 8, 'right', 'Participants', 1, '2005-12-17 17:19:35', 1, '2005-12-15 22:30:09', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'participants', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (38, 12, 1, 'left', 'Categories', 1, '2005-12-15 22:56:40', 1, '2005-12-15 22:31:46', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'tags', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (39, 12, 0, 'left', 'Blog', 1, '2005-12-16 00:19:36', 1, '2005-12-15 22:32:45', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1', '1', '1', 'page', NULL, 'addeddesc', '20', '0', '0');
		INSERT INTO `page` VALUES (54, 22, 0, 'left', 'Presentation', 1, '2005-12-17 17:50:37', 1, '2005-12-17 17:50:02', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', '1', '0', '0');
		INSERT INTO `page` VALUES (73, 20, 0, 'left', 'Assignments', 1, '2005-12-18 12:50:54', 1, '2005-12-18 12:50:12', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (42, 20, 1, 'left', 'Week 1', 1, '2005-12-18 12:50:54', 1, '2005-12-16 00:25:12', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (43, 20, 2, 'left', 'Week 2', 1, '2005-12-18 12:50:50', 1, '2005-12-16 00:25:24', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (44, 20, 3, 'left', 'Week 3', 1, '2005-12-18 12:50:49', 1, '2005-12-16 00:25:36', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (45, 20, 4, 'left', 'Week 4', 1, '2005-12-18 12:50:45', 1, '2005-12-16 00:25:48', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (46, 20, 5, 'left', 'Week 5', 1, '2005-12-18 12:50:42', 1, '2005-12-16 00:26:01', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (47, 20, 6, 'left', 'Week 6', 1, '2005-12-18 12:50:39', 1, '2005-12-16 00:26:11', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (48, 20, 7, 'left', 'Week 7', 1, '2005-12-18 12:50:36', 1, '2005-12-16 00:26:22', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (65, 5, 2, 'right', 'Recent Posts', 1, '2005-12-17 20:34:11', 1, '2005-12-17 20:34:11', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'rss', 14, 'custom', '5', '0', '0');
		INSERT INTO `page` VALUES (66, 5, 3, 'left', 'About Me', 1, '2005-12-17 20:37:59', 1, '2005-12-17 20:37:59', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (67, 3, 0, 'left', 'Description', 1, '2005-12-17 22:04:17', 1, '2005-12-17 22:04:17', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (68, 3, 1, 'left', 'Syllabus', 1, '2005-12-17 22:11:14', 1, '2005-12-17 22:04:51', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (69, 3, 2, 'left', 'Professor', 1, '2005-12-17 22:11:55', 1, '2005-12-17 22:11:55', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO `page` VALUES (75, 5, 4, 'right', 'Recent Discussion', 1, '2006-06-08 12:22:44', 1, '2006-06-08 12:22:44', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '0', 'rss', 20, 'custom', '5', '0', '0');
		
		INSERT INTO `permission` VALUES (1, NULL, 'everyone', 1, 'site', 'v');
		INSERT INTO `permission` VALUES (2, NULL, 'everyone', 2, 'site', 'v');
		INSERT INTO `permission` VALUES (3, NULL, 'everyone', 3, 'site', 'v');
		INSERT INTO `permission` VALUES (8, NULL, 'everyone', 4, 'site', 'v');
		INSERT INTO `permission` VALUES (11, NULL, 'everyone', 5, 'site', 'v');
		INSERT INTO `permission` VALUES (67, NULL, 'everyone', 45, 'story', 'v');
		INSERT INTO `permission` VALUES (65, NULL, 'everyone', 43, 'story', 'v');
		INSERT INTO `permission` VALUES (25, NULL, 'everyone', 7, 'site', 'v');
		INSERT INTO `permission` VALUES (26, NULL, 'everyone', 8, 'site', 'v');
		INSERT INTO `permission` VALUES (61, NULL, 'everyone', 39, 'story', 'v');
		INSERT INTO `permission` VALUES (62, NULL, 'everyone', 40, 'story', 'v');
		INSERT INTO `permission` VALUES (53, NULL, 'everyone', 31, 'story', 'v');
		INSERT INTO `permission` VALUES (57, NULL, 'everyone', 35, 'story', 'v');
		INSERT INTO `permission` VALUES (60, NULL, 'everyone', 38, 'story', 'v');
		INSERT INTO `permission` VALUES (50, NULL, 'everyone', 28, 'story', 'v,di');
		INSERT INTO `permission` VALUES (49, NULL, 'institute', 27, 'story', 'di');
		INSERT INTO `permission` VALUES (48, NULL, 'everyone', 27, 'story', 'v');
		INSERT INTO `permission` VALUES (47, NULL, 'everyone', 26, 'story', 'v');
		INSERT INTO `permission` VALUES (38, NULL, 'everyone', 12, 'story', 'v');
		INSERT INTO `permission` VALUES (39, NULL, 'everyone', 13, 'story', 'v');
		INSERT INTO `permission` VALUES (66, NULL, 'everyone', 44, 'story', 'v');
		INSERT INTO `permission` VALUES (46, NULL, 'everyone', 25, 'story', 'v');
		
		INSERT INTO `section` VALUES (1, 1, 0, 'Section One', 1, '2003-05-29 16:18:01', 1, '2003-05-29 16:18:01', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', 1);
		INSERT INTO `section` VALUES (22, 4, 4, 'Presentations', 1, '2005-12-18 10:00:50', 1, '2005-12-17 17:49:40', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', 10);
		INSERT INTO `section` VALUES (3, 2, 0, 'Introduction', 1, '2003-05-30 11:11:24', 1, '2003-05-30 11:08:50', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', NULL);
		INSERT INTO `section` VALUES (5, 3, 0, 'Blog', 1, '2005-12-17 20:32:21', 1, '2003-05-30 13:06:57', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', NULL);
		INSERT INTO `section` VALUES (9, 4, 0, 'Introduction', 1, '2003-05-30 13:15:21', 1, '2003-05-30 13:15:21', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', NULL);
		INSERT INTO `section` VALUES (21, 4, 2, 'Discussions', 1, '2005-12-17 17:17:39', 1, '2005-12-17 17:16:32', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', 10);
		INSERT INTO `section` VALUES (12, 4, 3, 'Blog', 1, '2005-12-17 17:17:39', 1, '2003-05-30 13:15:22', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', NULL);
		INSERT INTO `section` VALUES (20, 4, 1, 'Assignments', 1, '2005-12-16 00:24:28', 1, '2005-12-16 00:24:24', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', 10);
		INSERT INTO `section` VALUES (14, 5, 0, 'Introduction', 1, '2003-05-30 13:21:05', 1, '2003-05-30 13:20:52', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', 3);
		INSERT INTO `section` VALUES (16, 7, 0, 'Section One', 1, '2003-11-10 16:11:33', 1, '2003-11-10 16:11:33', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', NULL);
		INSERT INTO `section` VALUES (17, 1, 1, 'Section Two', 1, '2005-12-15 20:38:39', 1, '2005-12-15 20:38:26', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', 8);
		INSERT INTO `section` VALUES (18, 5, 1, 'More Features', 1, '2005-12-15 21:32:46', 1, '2005-12-15 20:54:58', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', 9);
		INSERT INTO `section` VALUES (19, 5, 2, 'Presentation', 1, '2005-12-15 20:59:39', 1, '2005-12-15 20:59:39', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 'section', NULL);
		
				
		INSERT INTO `site` VALUES (1, 'Simple Site', 'shadowbox', '', '', '', 1, '2005-12-18 13:02:15', 1, '2003-05-29 16:17:51', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1');
		INSERT INTO `site` VALUES (2, 'Brief Course Site', 'shadowbox', '', '', '', 1, '2005-12-17 22:03:57', 1, '2003-05-30 11:08:50', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1');
		INSERT INTO `site` VALUES (3, 'Weblog', 'shadowbox', '', '', '', 1, '2005-12-18 12:56:29', 1, '2003-05-30 13:06:57', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1');
		INSERT INTO `site` VALUES (4, 'Extensive Course Site', 'shadowbox', '', '', '', 1, '2005-12-17 17:15:17', 1, '2003-05-30 13:15:21', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1');
		INSERT INTO `site` VALUES (5, 'Segue Sample Site', 'shadowbox', 0x61253341313125334125374273253341352533412532327468656d652532322533427325334139253341253232736861646f77626f7825323225334273253341372533412532326267636f6c6f72253232253342732533413625334125323279656c6c6f77253232253342732533413131253341253232636f6c6f72736368656d6525323225334273253341352533412532327768697465253232253342732533413131253341253232626f726465727374796c652532322533427325334136253341253232646f74746564253232253342732533413131253341253232626f72646572636f6c6f722532322533427325334133253341253232726564253232253342732533413925334125323274657874636f6c6f722532322533427325334135253341253232626c61636b25323225334273253341392533412532326c696e6b636f6c6f7225323225334273253341332533412532327265642532322533427325334131312533412532326e61765f617272616e6765253232253342732533413132253341253232546f702b53656374696f6e7325323225334273253341392533412532326e61765f77696474682532322533427325334131302533412532323130302b706978656c7325323225334273253341313525334125323273656374696f6e6e61765f73697a65253232253342732533413925334125323231322b706978656c7325323225334273253341382533412532326e61765f73697a65253232253342732533413925334125323231302b706978656c73253232253342253744, '', '', 1, '2005-12-15 21:40:23', 1, '2003-05-30 13:20:52', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1');
		INSERT INTO `site` VALUES (7, 'Advanced: Single Section', 'shadowbox', '', '', '', 1, '2005-12-18 13:02:54', 1, '2003-11-10 16:11:33', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');
		INSERT INTO `site` VALUES (8, 'Advanced: Blank', 'minimal', '', '', '', 1, '2003-11-10 16:13:24', 1, '2003-11-10 16:13:24', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');
				
		INSERT INTO `site_editors` VALUES (1, NULL, 'everyone');
		INSERT INTO `site_editors` VALUES (1, NULL, 'institute');
		INSERT INTO `site_editors` VALUES (2, NULL, 'everyone');
		INSERT INTO `site_editors` VALUES (2, NULL, 'institute');
		INSERT INTO `site_editors` VALUES (3, NULL, 'everyone');
		INSERT INTO `site_editors` VALUES (3, NULL, 'institute');
		INSERT INTO `site_editors` VALUES (4, NULL, 'everyone');
		INSERT INTO `site_editors` VALUES (4, NULL, 'institute');
		INSERT INTO `site_editors` VALUES (5, NULL, 'everyone');
		INSERT INTO `site_editors` VALUES (5, NULL, 'institute');
		INSERT INTO `site_editors` VALUES (7, NULL, 'everyone');
		INSERT INTO `site_editors` VALUES (7, NULL, 'institute');
		INSERT INTO `site_editors` VALUES (8, NULL, 'everyone');
		INSERT INTO `site_editors` VALUES (8, NULL, 'institute');
		
		INSERT INTO `slot` VALUES (1, 'template0', 1, NULL, 1, 'system', NULL);
		INSERT INTO `slot` VALUES (2, 'template1', 1, NULL, 4, 'system', NULL);
		INSERT INTO `slot` VALUES (3, 'template2', 1, NULL, 3, 'system', NULL);
		INSERT INTO `slot` VALUES (4, 'template3', 1, NULL, 2, 'system', NULL);
		INSERT INTO `slot` VALUES (5, 'sample', 1, NULL, 5, 'system', NULL);
		INSERT INTO `slot` VALUES (6, 'template4', 1, NULL, 7, 'system', 0);
		INSERT INTO `slot` VALUES (7, 'template5', 1, NULL, 8, 'system', 0);
				
		INSERT INTO `story` VALUES (1, 1, 0, 'General Information', 1, '2005-12-15 19:52:57', 1, '2005-12-15 19:52:57', 0x253044253041496e2b253343622533455365677565253343253246622533452b796f752b63616e2b6164642b73656374696f6e732b25323861626f76652532432b6c696b652b746869732b6f6e65253243253044253041496e74726f64756374696f6e2532392e2b456163682b73656374696f6e2b636f6e7461696e732b6f6e652b6f722b6d756c7469706c652b70616765732b2532386f6e2b7468652530442530416c6566742532432b4465736372697074696f6e2b666f722b6578616d706c652532392e2b4f6e2b65766572792b706167652532432b796f752b63616e2b6164642b636f6e74656e742530442530412532386c696b652b746869732532392e2b436f6e74656e742b63616e2b72616e67652b66726f6d2b706c61696e2b746578742b746f2b696d616765732b746f2b66696c65732b746f25324325304425304177656c6c2532432b77686174657665722b796f752b77616e742e2b50616765732b63616e2b636f6e7461696e2b61732b6d616e792b746578742b626c6f636b73253044253041253238656e7469746965732b776974682b616e2b6f7074696f6e616c2b7469746c652532432b636f6e74656e742532432b616e642b6f7074696f6e616c2b64697363757373696f6e732532392b6173253044253041796f752b77616e742e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (29, 17, 0, '', 1, '2005-12-17 22:17:32', 1, '2005-12-15 21:51:00', 0x253044253041546869732b74656d706c6174652b69732b64657369676e65642b666f722b612b636f757273652b746861742b6d616b65732b657874656e736976652b7573652b6f662b7468652b7765622e2b2b49742b696e636c756465732b7468652b666f6c6c6f77696e672b73656374696f6e7325334125334362722b253246253345253343756c2533452533436c692533452533437370616e2b7374796c65253344253232666f6e742d7374796c652533412b6974616c6963253342253232253345496e74726f64756374696f6e2533432532467370616e2533452533412b696e636c756465732b70616765732b666f722b636f757273652b6465736372697074696f6e2b253238746869732b706167652532392532432b73796c6c616275732532432b726571756972656d656e74732532432b67726164696e672b616e642b70726f666573736f722532432b61732b77656c6c2b61732b616e6e6f756e636d656e74732532432b6c696e6b732b616e642b612b6c6973742b6f662b7061727469636970616e747325334362722b2532462533452533432532466c692533452533436c692533452533437370616e2b7374796c65253344253232666f6e742d7374796c652533412b6974616c696325334225323225334541737369676e6d656e74732533432532467370616e2533452533412b696e636c756465732b70616765732b666f722b656163682b7765656b2b6f662b7468652b73656d65737465722533432532466c692533452533436c692533452533437370616e2b7374796c65253344253232666f6e742d7374796c652533412b6974616c696325334225323225334544697363757373696f6e2533432532467370616e2533452533412b696e636c756465732b612b706167652b666f722b64697363757373696f6e2b746f706963732533432532466c692533452533436c692533452533437370616e2b7374796c65253344253232666f6e742d7374796c652533412b6974616c6963253342253232253345426c6f672533432532467370616e2533452533412b696e636c756465732b612b706167652b666f722b612b636f757273652b626c6f672b776974682b6c697374732b6f662b626c6f672b63617465676f726965732b616e642b726563656e742b706f7374732533432532466c692533452533436c692533452533437370616e2b7374796c65253344253232666f6e742d7374796c652533412b6974616c696325334225323225334550726573656e746174696f6e732533432532467370616e2533452533412b696e636c756465732b612b73616d706c652b736c6964652b73686f772533432532466c69253345253343253246756c2533452530442530412530442530412533437370616e2b7374796c65253344253232636f6c6f722533412b72676225323835312532432b35312532432b3531253239253342253232253345253238416e792b6f662b7468652b73656374696f6e732b616e642b70616765732b696e2b746869732b736974652b63616e2b62652b6564697465642b616e642b64656c657465642b61732b6e65656465642e2e2e2b41732b77656c6c2b6164646974696f6e616c2b73656374696f6e732b616e642b70616765732b63616e2b62652b61646465642532392533432532467370616e25334525334362722b253246253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (3, 1, 2, 'Another Example', 1, '2005-12-15 19:54:26', 1, '2005-12-15 19:54:26', 0x546869732b69732b7965742b616e6f746865722b6578616d706c652b6f662b612b746578742b626c6f636b2e2b546869732b746578742b626c6f636b2b6861732b612530442530417469746c652532432b25323671756f74253342416e6f746865722b4578616d706c6525323671756f742533422e2b5468652b746578742b626c6f636b2b62656c6f772b646f65732b6e6f742b686176652b612b7469746c652530442530416173736f6369617465642b776974682b69742e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (4, 1, 3, '', 1, '2005-12-15 19:54:53', 1, '2005-12-15 19:54:53', 0x546869732b746578742b626c6f636b2b646f65732b6e6f742b686176652b616e2b6173736f6369617465642b7469746c652e, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (5, 2, 0, 'Journal Article Excerpts', 1, '2005-12-15 19:56:19', 1, '2005-12-15 19:56:19', 0x42656c6f772b6172652b612b6665772b6578616d706c65732b6f662b6372656174696e672b746578742b626c6f636b732b776974682b616e2b61627269646765642b76657273696f6e2b6f662b7468652b636f6e74656e742b616e642b616e2b6173736f6369617465642b66756c6c2b636f6e74656e742e2b54686573652b6172652b65786365727074732b66726f6d2b6a6f75726e616c732532466e6577732b74616b656e2b66726f6d2b7468652b7765622b6f6e2b4a756c792b313974682532432b323030322e2b5468652b66756c6c2b636f6e74656e742b666f722b7468652b61727469636c652b69732b6e6f742b7468652b66756c6c2b61727469636c652b74616b656e2b66726f6d2b7468652b7765622532432b74686f7567682e2b4c696e6b732b6172652b70726f76696465642b746f2b7468652b6a6f75726e616c253237732b736974652e, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (6, 2, 1, 'A Space in Time', 1, '2005-12-15 19:57:30', 1, '2005-12-15 19:57:30', 0x496e2b7468652b6576656e696e67732532432b7768656e2b6d792b706172746963756c61722b70696563652b6f662b45617274682b6861732b7475726e65642b617761792b66726f6d2b7468652b53756e2532432b616e642b69732b6578706f7365642b696e73746561642b746f2b7468652b726573742b6f662b7468652b636f736d6f732532432b492b7369742b696e2b66726f6e742b6f662b612b6b6579626f6172642532432b6c6f672b6f6e2532432b616e642b7365656b2b6f75742b7468652b77696e646f77732b746861742b6c6f6f6b2b646f776e2b61742b7468652b706c616e6574732b616e642b6f75742b61742b7468652b73746172732e2b4974253237732b612b6d61726b65646c792b646966666572656e742b657870657269656e63652b66726f6d2b6c6f6f6b696e672b61742b726570726f64756374696f6e732b6f6e2b70617065722e2b576861742b492b7365652b69732b636c6f7365722b746f2b7468652b736f757263652e2b496e2b666163742532432b6974253237732b696e64697374696e677569736861626c652b66726f6d2b7468652b736f757263652e2b54686573652b6172652b696d616765732b746861742b686176652b6e657665722b726567697374657265642b6f6e2b612b6e656761746976652e2b4c696b652b7468652b496e7465726e65742b697473656c662532432b746865792b6172652b70726f64756374732b6f662b612b6469676974697a65642b6572612e2b4f7665722b7468652b706173742b636f75706c652b6f662b79656172732b4925323776652b6265656e2b6d6f6e69746f72696e672b7468652b6c6f6e672b72656374616e67756c61722b7374726970732b6f662b4d61727469616e2b737572666163652b6265696e672b6265616d65642b6163726f73732b7468652b766f69642532432b696e2b612b7374656164792b73747265616d2b6f662b7a65726f65732b616e642b6f6e65732532432b66726f6d2b7468652b756d6272656c6c612d7368617065642b686967682d6761696e2b616e74656e6e612b6f662b7468652b4d6172732b476c6f62616c2b5375727665796f722b737061636563726166742e2b54686573652b70696374757265732b6172652b736f2b66726573682b746861742b74686569722b696d6d6564696163792b70726163746963616c6c792b637261636b6c65732e2b43616c6c2b69742b2532326368726f6e6f2d636c61726974792e2532322b546861742b626c756973682b77697370792b636c6f7564, 0x496e2b7468652b6576656e696e67732532432b7768656e2b6d792b706172746963756c61722b70696563652b6f662b45617274682b6861732b7475726e65642b617761792b66726f6d2530442530417468652b53756e2532432b616e642b69732b6578706f7365642b696e73746561642b746f2b7468652b726573742b6f662b7468652b636f736d6f732532432b492b7369742b696e25304425304166726f6e742b6f662b612b6b6579626f6172642532432b6c6f672b6f6e2532432b616e642b7365656b2b6f75742b7468652b77696e646f77732b746861742b6c6f6f6b2b646f776e2b61742530442530417468652b706c616e6574732b616e642b6f75742b61742b7468652b73746172732e2b4974253237732b612b6d61726b65646c792b646966666572656e742b657870657269656e636525304425304166726f6d2b6c6f6f6b696e672b61742b726570726f64756374696f6e732b6f6e2b70617065722e2b576861742b492b7365652b69732b636c6f7365722b746f2b746865253044253041736f757263652e2b496e2b666163742532432b6974253237732b696e64697374696e677569736861626c652b66726f6d2b7468652b736f757263652e2b54686573652b617265253044253041696d616765732b746861742b686176652b6e657665722b726567697374657265642b6f6e2b612b6e656761746976652e2b4c696b652b7468652b496e7465726e6574253044253041697473656c662532432b746865792b6172652b70726f64756374732b6f662b612b6469676974697a65642b6572612e2b4f7665722b7468652b706173742b636f75706c652b6f6625304425304179656172732b4925323776652b6265656e2b6d6f6e69746f72696e672b7468652b6c6f6e672b72656374616e67756c61722b7374726970732b6f662b4d61727469616e253044253041737572666163652b6265696e672b6265616d65642b6163726f73732b7468652b766f69642532432b696e2b612b7374656164792b73747265616d2b6f662b7a65726f65732b616e642530442530416f6e65732532432b66726f6d2b7468652b756d6272656c6c612d7368617065642b686967682d6761696e2b616e74656e6e612b6f662b7468652b4d6172732b476c6f62616c2530442530415375727665796f722b737061636563726166742e2b54686573652b70696374757265732b6172652b736f2b66726573682b746861742b74686569722b696d6d65646961637925304425304170726163746963616c6c792b637261636b6c65732e2b43616c6c2b69742b25323671756f742533426368726f6e6f2d636c61726974792e25323671756f742533422b546861742b626c756973682b7769737079253044253041636c6f75642532432b666f722b6578616d706c652532432b686f766572696e672b6f7665722b7468652b486563617465732b54686f6c75732b766f6c63616e6f2532432b776869636825304425304172656172732b61626f76652b7468652b706f636b6d61726b65642b737572666163652b6f662b7468652b456c797369756d2b566f6c63616e69632b526567696f6e2b696e2530442530417468652b4d61727469616e2b6561737465726e2b68656d6973706865726525453225383025393469742b6861732b626172656c792b6861642b74696d652b746f2b64697370657273652530442530416265666f72652b492532432b6f722b616e796f6e652b776974682b496e7465726e65742b6163636573732532432b63616e2b7365652b69742b696e2b616c6c2b6974732b73706f6f6b792530442530416265617574792e2b5468652b766f6c63616e6f2b656d65726765732b66726f6d2b7468652b70696e6b2b4d61727469616e2b6465736572742532432b77686963682b6c6f6f6b732530442530416f7267616e69632b616e642b696d7072657373696f6e61626c652545322538302539346c696b652b68756d616e2b736b696e2532432b6f722b7468652b737572666163652b6f662b612b636c6179253044253041706f742b6265666f72652b666972696e672e2b5468652b74656e756f75732b636c6f75642b666c6f6174732b6e6561722b7468652b766f6c63616e6f253237732b6d6f75746825324325304425304161732b69662b696e2b7072656c7564652b746f2b616e2b6572757074696f6e2e2b4974253237732b612b706963747572652b636f6d706f7365642b6f662b6d696c6c696f6e732b6f66253044253041646f74732b616e642b6461736865732b6f662b646174612532432b70726f64756365642b62792b612b7472616e736d697373696f6e2b746563686e697175652b6a7573742b612530442530416665772b73746570732b72656d6f7665642b66726f6d2b4d6f7273652b636f64652533422b6275742b69742b72657665616c732b612b6c616e6473636170652b7468652b6c696b65732530442530416f662b77686963682b53616d75656c2b4d6f7273652532432b6c65742b616c6f6e652b7468652b72616e6b732b6f662b45617274682d62617365642b617374726f6e6f6d65727325304425304177686f2b686176652b73757276657965642b7468652b706c616e6574732b73696e63652b77656c6c2b6265666f72652b426162796c6f6e69616e2b74696d65732532432b636f756c642530442530417363617263656c792b686176652b656e766973696f6e65642e25334362722b25324625334525334362722b253246253345496e2b636173652b74686572652b7761732b616e792b646f7562742532432b6d616e792b6f6625304425304174686f73652b676f6f642b6f6c642b736369656e63652d66696374696f6e2b70726564696374696f6e732b66726f6d2b7468652b31393530732b616e642b7468652b31393630732530442530416172652b636f6d696e672b747275652e2b25323671756f742533424e45572b53515541442b4f462b524f424f54532b52454144592b544f2b41535341554c542b4d41525325323671756f742533422b726561642b61253044253041313939382b686561646c696e652b696e2b7468652b6f6e6c696e652b486f7573746f6e2b4368726f6e69636c652532432b7374697272696e672b7375626d65726765642530442530416d656d6f726965732b6f662b6d792b61646f6c657363656e742b72656164696e67732b6f662b49736161632b4173696d6f76253237732b492532432b526f626f742b73746f726965732e2530442530414275742b4173696d6f76253237732b73656e7469656e742b726f626f74732b776572652b6672657175656e746c792b636f6e66757365642e2b536f6d657468696e672b616c776179732530442530417365656d65642b746f2b62652b676f696e672b77726f6e672b776974682b7468656d2532432b616e642b7468652b6d617968656d2b746861742b666f6c6c6f7765642b636f756c64253044253041696e6576697461626c792b62652b7472616365642b6261636b2b746f2b612b70726f6772616d6d696e672b6572726f722b62792b74686569722b68756d616e25304425304168616e646c657273254532253830253934612b736974756174696f6e2b6e6f742b756e66616d696c6961722b746f2b74686f73652b72756e6e696e672b4e415341253237732b4d61727325304425304170726f6772616d2532432b77686963682b7761732b74656d706f726172696c792b67726f756e6465642b61667465722b612b636174617374726f706869632b706169722b6f662530442530416661696c757265732b696e2b6c6174652b313939392e2b2532385468652b4d6172732b436c696d6174652b4f7262697465722b7761732b6c6f73742b6f77696e672b746f2b746865253044253041737461726b2b6661696c7572652b62792b6f6e652b67726f75702b6f662b656e67696e656572732b746f2b7472616e736c6174652b616e6f746865722b67726f757025323773253044253041666967757265732b696e746f2b6d65747269632b756e6974732b6f662b6d6561737572656d656e742532432b616e642b7468652b4d6172732b506f6c61722b4c616e646572253044253041626563617573652b666f722b736f6d652b756e666174686f6d61626c652b726561736f6e2b6974732b6c616e64696e672b676561722b6861646e253237742b6265656e25304425304161646571756174656c792b7465737465642e25323925334362722b25324625334525334362722b253246253345546f2b726561642b7468652b66756c6c2b61727469636c652b66726f6d2b253343692533455468652b41746c616e746963253343253246692533452532432b253343612b68726566253344253232687474702533412532462532467777772e74686561746c616e7469632e636f6d25324669737375657325324632303032253246303725324662656e736f6e2e68746d253232253345636c69636b2b68657265253343253246612533452e253044, '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (7, 2, 2, 'Critics lament the state of stem cell research', 1, '2005-12-15 19:59:38', 1, '2005-12-15 19:59:38', 0x2533436469762b636c6173732533442532326c6566746d617267696e2532322533452530442530412533437374726f6e67253345253343612b6872656625334425323268747470253341253246253246736c75672e6d6964646c65627572792e65647525324625323537456163686170696e2532467365677565315f35253246696e6465782e706870253346253236616374696f6e25334473697465253236736974652533442535432535422535432535427369746525354325354425354325354425323673656374696f6e253344313425323670616765253344353225323673746f7279253344333525323664657461696c25334433352532322b6e616d652533442532323335253232253345253343253246612533452533432532467374726f6e67253345496e2b612530442530416d6f76652b746f2b617070656173652b74686f73652b77686f2b737570706f72742b7468652b696465612532432b616e642b74686f73652b77686f2b62656c696576652530442530416372656174696e672b68756d616e2b656d6272796f6e69632b7374656d2b63656c6c732b666f722b72657365617263682b69732b77726f6e672532432b427573682b6465636964656425304425304166756e64696e672b776f756c642b6f6e6c792b737570706f72742b72657365617263682b6f6e2b612b736d616c6c2b6e756d6265722b6f662b6578697374696e672b63656c6c2530442530416c696e65732b68656c642b62792b6f6e6c792b612b6665772b6c6162732e25334362722b25324625334525334325324664697625334525304425304125334362722b2532462533452530442530414e6f772532432b7374656d2b63656c6c2b72657365617263682b69732b61742b7669727475616c2b7374616e647374696c6c2532432b637269746963732b7361792e25304425304152657365617263686572732b636f6d706c61696e2b746865792b63616e253237742b6765742b7468652b63656c6c732b746865792b6e6565642b62656361757365253044253041737570706c6965732b6172652b736f2b7363617263652e25334362722b25324625334525304425304125334362722b2532462533452530442530414f6e652b6f662b74686f73652b637269746963732b69732b5374657068656e2b57616b656669656c642b66726f6d2b41746c616e74612532432b47656f726769612e2b48652b757365642b746f2b736b692b616e642b72756e2b6d61726174686f6e732b2d2d2b6275742b6e6f742b616e796d6f72652e25334362722b25324625334525304425304125334362722b2532462533452530442530415369782b79656172732b61676f2532432b68652b7761732b646961676e6f7365642b776974682b612b6e6575726f6d757363756c61722b6469736f726465722b73696d696c6172253044253041746f2b4c6f752b476568726967253237732b646973656173652e2b5468657265253237732b6e6f2b637572652b616e642b69742b77696c6c2b6f6e6c792b6765742b776f7273652e253044, 0x4e6f772532432b7374656d2b63656c6c25304425304172657365617263682b69732b61742b7669727475616c2b7374616e647374696c6c2532432b637269746963732b7361792e2b52657365617263686572732b636f6d706c61696e253044253041746865792b63616e253237742b6765742b7468652b63656c6c732b746865792b6e6565642b626563617573652b737570706c6965732b6172652b736f2b7363617263652e25334362722b25324625334525334362722b2532462533454f6e652b6f662b74686f73652b637269746963732b69732b5374657068656e2b57616b656669656c642b66726f6d2b41746c616e74612532432b47656f726769612e2b48652b757365642b746f2b736b692b616e642b72756e2b6d61726174686f6e732b2d2d2b6275742b6e6f742b616e796d6f72652e25334362722b25324625334525334362722b25324625334553697825304425304179656172732b61676f2532432b68652b7761732b646961676e6f7365642b776974682b612b6e6575726f6d757363756c61722b6469736f726465722b73696d696c61722b746f2530442530414c6f752b476568726967253237732b646973656173652e2b5468657265253237732b6e6f2b637572652b616e642b69742b77696c6c2b6f6e6c792b6765742b776f7273652e253044253041496e2b612b6d6f76652b746f253044253041617070656173652b74686f73652b77686f2b737570706f72742b7468652b696465612532432b616e642b74686f73652b77686f2b62656c696576652b6372656174696e6725304425304168756d616e2b656d6272796f6e69632b7374656d2b63656c6c732b666f722b72657365617263682b69732b77726f6e672532432b427573682b646563696465642b66756e64696e67253044253041776f756c642b6f6e6c792b737570706f72742b72657365617263682b6f6e2b612b736d616c6c2b6e756d6265722b6f662b6578697374696e672b63656c6c2b6c696e657325304425304168656c642b62792b6f6e6c792b612b6665772b6c6162732e25334362722b25324625334525334362722b2532462533454e6f772532432b7374656d2b63656c6c2b72657365617263682b69732b61742b7669727475616c2530442530417374616e647374696c6c2532432b637269746963732b7361792e2b52657365617263686572732b636f6d706c61696e2b746865792b63616e253237742b6765742b7468652b63656c6c73253044253041746865792b6e6565642b626563617573652b737570706c6965732b6172652b736f2b7363617263652e25334362722b25324625334525334362722b2532462533454f6e652b6f662b74686f73652b637269746963732b69732b5374657068656e2b57616b656669656c642b66726f6d2b41746c616e74612532432b47656f726769612e2b48652b757365642b746f2b736b692b616e642b72756e2b6d61726174686f6e732b2d2d2b6275742b6e6f742b616e796d6f72652e25334362722b25324625334525334362722b25324625334553697825304425304179656172732b61676f2532432b68652b7761732b646961676e6f7365642b776974682b612b6e6575726f6d757363756c61722b6469736f726465722b73696d696c61722b746f2530442530414c6f752b476568726967253237732b646973656173652e2b5468657265253237732b6e6f2b637572652b616e642b69742b77696c6c2b6f6e6c792b6765742b776f7273652e25334362722b25324625334525334362722b25324625334541742b66697273742532432b68652b7761732b646576617374617465642e2b4275742b7468656e2b2d2d6c696b652b6d6f73742b6f662b75732b2d2d2b68652b68656172642b61626f75742b656d6272796f6e69632b7374656d2b63656c6c732e25334362722b25324625334525334362722b253246253345536369656e746973747325304425304162656c696576652b7374656d2b63656c6c732b63616e2b62652b7475726e65642b696e746f2b616e797468696e672532432b696e636c7564696e672b7468652b6e6572766525304425304163656c6c732b53746576652b6e656564732b746f2b7265706c6163652b7468652b6f6e65732b6479696e672b696e2b6869732b626f64792e25334362722b25324625334525334362722b25324625334525323671756f742533424925304425304162656c696576652b69742b69732b7468652b6f6e6c792b7468696e672b6f6e2b7468652b686f72697a6f6e2b746861742b77696c6c2b70726f766964652b6d652b776974682b612530442530416375726525324325323671756f742533422b57616b656669656c642b736179732b7468726f7567682b6869732b776966652532432b50616d2532432b77686f2b6d7573742b7472616e736c6174652b666f722b68696d2e25334362722b25324625334525334362722b253246253345536f2b776861742b646f2b53746576652b616e642b50616d2b7468696e6b2b6f662b507265736964656e742b42757368253237732b6465636973696f6e2b612b796561722b61676f25334625334362722b25324625334525334362722b2532462533455468617425323773253044253041612b747269636b792b7175657374696f6e2e2b5468657925323772652b636c6f73652b746f2b7468652b4275736865732b616e642b63616d706169676e65642b666f722b7468656d253044253041666f722b32352b79656172732e2b412b6c61777965722532432b53746576652b6576656e2b7365727665642b696e2b7468652b66697273742b4275736825304425304161646d696e697374726174696f6e2b61732b67656e6572616c2b636f756e73656c2b666f722b7468652b4465706172746d656e742b6f662b456e657267792e25334362722b25324625334525334362722b2532462533454275742b746865792b7468696e6b2b7468652b707265736964656e742b6d6164652b7468652b77726f6e672b6465636973696f6e2b616e642b6e6f772b6665656c2b6469736170706f696e7465642b616e642b667275737472617465642e25334362722b25324625334525334362722b25324625334525323671756f7425334249742530442530416a7573742b7365656d732b6c696b652b6974253237732b6a7573742b736f2b6f6276696f75732b746f2b736176652b612b6c6966652532432b746f2b736176652b6d616e792b6c69766573253044253041746861742b427573682b616e642b6869732b61646d696e697374726174696f6e2b776f756c642b77616e742b746f2b676f2b666f72776172642b6269672b74696d652b77697468253044253041746869732b7374656d2b63656c6c2b726573656172636825324325323671756f742533422b736179732b50616d2e25334362722b25324625334525334362722b253246253345546865792b7361792b626563617573652b6f662b42757368253237732b6465636973696f6e2532432b7374656d2b63656c6c2b72657365617263682b6861736e253237742b6d6164652b6d7563682b686561647761792e2b416e642b74696d652b69732b6e6f742b6f6e2b5374657665253237732b736964652e25334362722b25324625334525334362722b253246253345496e2b7468652b74696d652b7468652b6465636973696f6e2b7761732b616e6e6f756e6365642532432b25323671756f74253342596f752b686176652b676f7474656e2b776f72736525324325323671756f742533422b50616d2b736179732b746f2b53746576652e2b48652b6e6f64732e25334362722b25324625334525334362722b253246253345434e4e25304425304173706f6b652b776974682b7365766572616c2b7374656d2b63656c6c2b72657365617263686572732b77686f2b736169642b7468657925323776652b74726965642532432b627574253044253041666f722b766172696f75732b6c6567616c2b616e642b736369656e74696669632b726561736f6e732532432b63616e253237742b6765742b74686569722b68616e64732b6f6e2b74686525304425304163656c6c732b66726f6d2b7468652b31312b6c6162732b746861742b6172652b617070726f7665642b736f75726365732e25334362722b25324625334525334362722b25324625334544722e2b43757274253044253041436976696e2532432b656469746f722b6f662b7468652b6a6f75726e616c2b5374656d2b43656c6c732b616e642b612b7065646961747269632b63616e6365722530442530417370656369616c6973742532432b77616e74732b7374656d2b63656c6c732b746f2b72656275696c642b626f6e652b6d6172726f772b666f722b796f756e672b63616e63657225304425304170617469656e74732e25334362722b25324625334525334362722b2532462533454275742532432b68652b736179732532432b656d6272796f6e69632b7374656d2b63656c6c2b72657365617263682b69732b61742b612b7669727475616c2b7374616e647374696c6c2e25334362722b25324625334525334362722b25324625334525323671756f742533424365727461696e6c79253044253041696e2b6f75722b6c61622b77652b686176656e253237742b6265656e2b61626c652b746f2b6765742b676f696e672532432b6576656e2b6f6e2b7374756479696e672b746865253044253041656d6272796f6e69632b7374656d2b63656c6c732532432b626563617573652b77652b63616e253237742b6765742b6f75722b68616e64732b6f6e2b7468656d25324325323671756f742533422b73617973253044253041436976696e2e25334362722b25324625334525334362722b2532462533455468652b4465706172746d656e742b6f662b4865616c74682b616e642b48756d616e2b53657276696365732b736179732b6974253237732b747279696e672b746f2b6d616b652b69742b6561736965722b666f722b72657365617263686572732b6c696b652b436976696e2e25334362722b25324625334525334362722b25324625334525323671756f742533424974253237732b676f696e672b746f2b636f6e74696e75652b746f2b72616d702b75702532432b7374617274696e672b736c6f772532432b6275742b636f6e74696e75652b746f2b6d6f76652b666f727761726425324325323671756f742533422b736179732b546f6d6d792b54686f6d70736f6e2532432b4848532b5365637265746172792e25334362722b25324625334525334362722b253246253345496e2b666163742532432b7468652b676f7665726e6d656e742b6a7573742b726563656e746c792b737461727465642b746f2b6d616b652b617272616e67656d656e74732b746f2b646973747269627574652b7374656d2b63656c6c732b746f2b72657365617263686572732e25334362722b25324625334525334362722b253246253345416e642b436976696e2b736179732b68652b686f7065732b746f2b6765742b736f6d652b6f662b74686f73652b7374656d2b63656c6c732b696e746f2b6869732b6c61622b61742b4a6f686e732b486f706b696e732b556e69766572736974792b696e2b42616c74696d6f72652532432b4d6172796c616e642e25334362722b25324625334525334362722b2532462533454275742b7468652b57616b656669656c64732b63616e253237742b68656c702b6275742b7468696e6b2b746869732b69732b616c6c2b6d6f76696e672b746f6f2b736c6f776c792e25334362722b25324625334525334362722b25324625334525323671756f7425334252656167616e253044253041736169642532432b2532374d722e2b476f726261636865762532432b746561722b646f776e2b74686573652b77616c6c732532372b616e642b427573682b63616e2b7361792532432b25323774656172253044253041646f776e2b74686f73652b77616c6c732b6f662b646973656173652532372e2b416e642b68652b636f756c642b62652b737563682b612b6865726f2b666f722b616c6c253044253041657465726e69747925324325323671756f742533422b736179732b50616d2e25334362722b25324625334525334362722b253246253345486572652b69732b612b6c696e6b2b746f2b7468652b253343612b68726566253344253232687474702533412532462532467777772e636e6e2e636f6d253246323030322532464845414c5448253246303825324630392532467374656d2e63656c6c2e70726f6d697365253246696e6465782e68746d6c2532322533456f726967696e616c2b61727469636c65253343253246612533452b6f6e2b25334369253345434e4e2e636f6d253343253246692533452e, '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (8, 3, 0, '', 1, '2005-12-15 20:00:16', 1, '2005-12-15 20:00:16', 0x4f6e2b746869732b706167652532432b796f752b77696c6c2b66696e642b736f6d652b6c696e6b732b746f2b72616e646f6d2b706c616365732b6f6e2b7468652b696e7465726e65742e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (9, 3, 1, 'Middlebury College', 1, '2005-12-15 20:01:12', 1, '2005-12-15 20:01:12', 0x546869732b69732b612b6c696e6b2b746f2b4d6964646c65627572792b436f6c6c656765253237732b7765622b736974652e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'link', 7, '0');
		INSERT INTO `story` VALUES (10, 3, 2, 'Google', 1, '2005-12-15 20:06:30', 1, '2005-12-15 20:06:30', 0x416e2b657863656c6c656e742b7365617263682b656e67696e65253231253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'link', 5, '0');
		INSERT INTO `story` VALUES (11, 3, 3, 'Segue Project Page', 1, '2005-12-15 20:07:13', 1, '2005-12-15 20:07:13', 0x4e6577732b616e642b696e666f726d6174696f6e2b61626f75742b7468652b646576656c6f70656d656e742b253343622533455365677565253343253246622533452e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'link', 6, '0');
		INSERT INTO `story` VALUES (12, 4, 0, 'Welcome to your new site!', 1, '2005-12-15 20:16:36', 1, '2005-12-15 20:16:36', 0x2533436469762b636c6173732533442532326c6566746d617267696e2532322533452530442530412533437374726f6e67253345253343612b6872656625334425323268747470253341253246253246736c75672e6d6964646c65627572792e65647525324625323537456163686170696e2532467365677565315f35253246696e6465782e706870253346253236616374696f6e25334473697465253236736974652533442535432535422535432535427369746525354325354425354325354425323673656374696f6e25334431253236706167652533443125323673746f72792533443125323664657461696c253344312532322b6e616d6525334425323231253232253345253343253246612533452533432532467374726f6e672533452d2b546f2b6164642b616e6f746865722b626c6f636b2b6f662b746578742b62656c6f772b746869732b746578742532432b636c69636b2b74686525334362722b25324625334525334325324664697625334525304425304125323671756f74253342253343622533452532426164642b636f6e74656e742533432532466225334525323671756f742533422b627574746f6e2b62656c6f772e25334362722b25324625334525304425304125334362722b2532462533452530442530412d2b546f2b656469742b746869732b746578742532432b636c69636b2b7468652b25323671756f7425334225334362253345656469742533432532466225334525323671756f742533422b627574746f6e2b62656c6f772e25334362722b25324625334525304425304125334362722b2532462533452530442530412d2b546f2b6164642b616e6f746865722b706167652b6f722b6f746865722b6974656d2b746f2b746869732b73656374696f6e2532432b636c69636b2b6f6e2b74686525334362722b25324625334525304425304125323671756f74253342253343622533452532426164642b6974656d2533432532466225334525323671756f742533422b627574746f6e2b746f2b7468652b6c6566742e25334362722b25324625334525304425304125334362722b2532462533452530442530412d2b436c69636b696e672b6f6e2b7468652b25323671756f7425334225334362253345656469742533432532466225334525323671756f742533422b627574746f6e2b6e6578742b746f2b612b706167652b6f722b73656374696f6e25334362722b25324625334525304425304177696c6c2b616c6c6f772b796f752b746f2b72656e616d652b69742e25334362722b25324625334525304425304125334362722b2532462533452530442530412d2b546f2b6164642b612b6e65772b73656374696f6e2532432b636c69636b2b7468652b25323671756f74253342253343622533452532426164642b73656374696f6e2533432532466225334525323671756f742533422b627574746f6e2b61626f76652e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (13, 5, 0, 'Sample Content', 1, '2005-12-15 20:17:26', 1, '2005-12-15 20:17:26', 0x2533436469762b636c6173732533442532326c6566746d617267696e2532322533452530442530412533437374726f6e67253345253343612b6872656625334425323268747470253341253246253246736c75672e6d6964646c65627572792e65647525324625323537456163686170696e2532467365677565315f35253246696e6465782e706870253346253236616374696f6e25334473697465253236736974652533442535432535422535432535427369746525354325354425354325354425323673656374696f6e25334431253236706167652533443225323673746f72792533443225323664657461696c253344322532322b6e616d6525334425323232253232253345253343253246612533452533432532467374726f6e672533452533432532466469762533452533437461626c652b77696474682533442532323130302532352532322b63656c6c73706163696e67253344253232302532322b63656c6c70616464696e672533442532323025323225334525334374626f647925334525334374722533452533437464253345546869732b69732b736f6d652b73616d706c652b636f6e74656e742b6f6e2b7468652b5365636f6e642b506167652e2533432532467464253345253343253246747225334525334325324674626f64792533452533432532467461626c65253345536f6d652b6d6f72652b73616d706c652b636f6e74656e742e2e2e2e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (14, 6, 0, 'More Sample Content', 1, '2005-12-15 20:42:22', 1, '2005-12-15 20:20:07', 0x253044253041486572652b69732b736f6d652b6d6f72652b636f6e74656e742e2e2e2e25334362722b25324625334525334362722b253246253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (27, 15, 1, 'Life, The Universe, and Everything', 1, '2005-12-15 21:25:01', 1, '2005-12-15 21:25:01', 0x546869732b71756f74652532432b66726f6d2b612b626f6f6b2b62792b446f75676c61732b4164616d732532432b61736b732b75732b776861742b77652b7468696e6b2b61626f75742b6c6966652e2b576861742b646f2b796f752b7468696e6b2b61626f75742b6c696665253346, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '1', '1', 'Discuss', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (28, 15, 2, 'Climates', 1, '2005-12-15 21:25:57', 1, '2005-12-15 21:25:57', 0x576973652b6d656e2b686176652b736169642b746861742b6d616e792b70656f706c652b7072656665722b7761726d2b636c696d617465732532432b61732b6f70706f7365642b746f2b7468652b636c696d6174652b666f756e642b696e2b5665726d6f6e742e2b5665726d6f6e742b6d61792b686176652b6974732b6f776e2b6265617574792532432b6275742b6d616e792b646f6e253237742b74616b652b746869732b696e746f2b6163636f756e742e2b576861742b646f2b796f752b7468696e6b2b6f662b74686973253346, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '1', '1', 'Discuss', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (25, 12, 0, '', 1, '2005-12-15 21:22:26', 1, '2005-12-15 21:22:26', 0x546869732b706167652b636f6e7461696e732b6d756c7469706c652b64697363757373696f6e2b746f706963732532432b656163682b6f662b77686963682b63616e2b62652530442530416469736375737365642b62792b4d6964646c65627572792b436f6c6c6567652b75736572732b253238796f752b686176652b7468652b6f7074696f6e2b6f662b616c6c6f77696e67253044253041616e796f6e652b746f2b646973637573732532432b6f722b6f6e6c792b73747564656e74732b696e2b796f75722b636c6173732532432b666f722b636c6173732b77656273697465732532392e253044253041436c69636b2b7468652b2533436225334564697363757373696f6e73253343253246622533452b6c696e6b2b62656c6f772b656163682b746f7069632b746f2b766965772b7468652b64697363757373696f6e2532432b6f722b6164642b796f75722b6f776e2b74776f2b63656e74732e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (26, 15, 0, '', 1, '2005-12-15 21:23:32', 1, '2005-12-15 21:23:32', 0x546869732b706167652b636f6e7461696e732b6d756c7469706c652b64697363757373696f6e2b746f706963732532432b656163682b6f662b77686963682b63616e2b62652530442530416469736375737365642b62792b4d6964646c65627572792b436f6c6c6567652b75736572732b253238796f752b686176652b7468652b6f7074696f6e2b6f662b616c6c6f77696e67253044253041616e796f6e652b746f2b646973637573732532432b6f722b6f6e6c792b73747564656e74732b696e2b796f75722b636c6173732532432b666f722b636c6173732b77656273697465732532392e253044253041436c69636b2b7468652b2533436225334564697363757373696f6e73253343253246622533452b6c696e6b2b62656c6f772b656163682b746f7069632b746f2b766965772b7468652b64697363757373696f6e2532432b6f722b6164642b796f75722b6f776e2b74776f2b63656e74732e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (18, 9, 0, 'Content blocks', 1, '2005-12-18 09:58:18', 1, '2005-12-15 20:47:16', 0x25304425304153656775652b70616765732b63616e2b686176652b616e792b6e756d6265722b6f662b636f6e74656e742b626c6f636b732b746861742b63616e2b62652b6f7264657265642b696e2b6368726f6e6f6c6f676963616c6c792b6c696b652b626c6f67732b6f722b696e2b616e792b637573746f6d2b6f726465722e25334362722b25324625334525334362722b25324625334553656775652b636f6e74656e742b626c6f636b732b63616e2b62652b63617465676f72697a65642b7573696e672b6d756c7469706c652b746167732e2b2b456469742b746869732b706167652b616e642b636c69636b2b6f6e2b7468652b6c696e6b2b746f2b41637469766174696f6e2b616e642b43617465676f72792b746f2b66696e642b6f75742b6d6f72652e25334362722b25324625334525334362722b253246253345416e2b6167677265676174696f6e2b6f662b74686573652b63617465676f726965732b69732b617661696c61626c652b62792b616464696e672b612b43617465676f72792b506167652b747970652e25334362722b25324625334525334362722b253246253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (19, 9, 1, 'Help', 1, '2005-12-15 20:52:57', 1, '2005-12-15 20:51:54', 0x2530442530414c6f6f6b2b666f722b253343612b7461726765742533442532325f626c616e6b2532322b687265662533442532322535432535422535432535426c696e6b7061746825354325354425354325354425324668656c7025324668656c702e70687025334625323668656c70746f706963253344696e64657825323225334548656c70253343253246612533452b6c696e6b732b7468726f7567686f75742b5365677565253237732b65646974696e672b696e746572666163652e2b2b253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (20, 11, 0, 'Content blocks', 1, '2005-12-15 20:57:47', 1, '2005-12-15 20:54:58', 0x25304425304153656775652b70616765732b63616e2b686176652b616e792b6e756d6265722b6f662b636f6e74656e742b626c6f636b732b746861742b63616e2b62652b6f7264657265642b696e2b6368726f6e6f6c6f676963616c6c792b6c696b652b626c6f67732b6f722b696e2b616e792b637573746f6d2b6f726465722e25334362722b25324625334525334362722b25324625334553656775652b636f6e74656e742b626c6f636b732b63616e2b62652b63617465676f72697a65642b7573696e672b6d756c7469706c652b746167732e2b2b456469742b746869732b706167652b616e642b636c69636b2b6f6e2b7468652b6c696e6b2b746f2b41637469766174696f6e2b616e642b43617465676f72792b746f2b66696e642b6f75742b6d6f72652e25334362722b25324625334525334362722b253246253345416e2b6167677265676174696f6e2b6f662b74686573652b63617465676f726965732b69732b617661696c61626c652b62792b616464696e672b612b43617465676f72792b506167652b747970652e25334362722b25324625334525334362722b253246253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (21, 11, 1, 'Help', 1, '2005-12-15 20:57:14', 1, '2005-12-15 20:54:58', 0x2530442530414c6f6f6b2b666f722b253343612b687265662533442532322535432535422535432535426c696e6b7061746825354325354425354325354425324668656c7025324668656c702e70687025334625323668656c70746f706963253344696e6465782532322b7461726765742533442532325f626c616e6b25323225334548656c70253343253246612533452b6c696e6b732b7468726f7567686f75742b5365677565253237732b65646974696e672b696e746572666163652e2b2b253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (22, 14, 0, 'Presentation', 1, '2005-12-15 20:59:39', 1, '2005-12-15 20:59:39', 0x253044253041546869732b69732b736f6d652b73616d706c652b636f6e74656e742b6f6e2b7468652b6f6e6c792b706167652b696e2b7468652b7365636f6e642b73656374696f6e2e2b2b25334362722b25324625334525334362722b2532462533454e6f746963652b746861742b69662b612b73656374696f6e2b6f6e6c792b6861732b6f6e652b706167652532432b6e6f2b706167652b6c696e6b2b69732b646973706c617965642b616c6c6f77696e672b796f752b746f2b6d616b652b7573652b6f662b7468652b77686f6c652b77696474682b6f662b7468652b736974652e2e2e25334362722b25324625334525334362722b253246253345496e2b6164646974696f6e2532432b746869732b706167652b6861732b6265656e2b636f6e666967757265642b746f2b646973706c61792b6f6e6c792b6f6e652b636f6e74656e742b626c6f636b2b61742b612b74696d652532432b616c6c6f77696e672b796f752b746f2b6372656174652b612b73657175656e63652e2e2e25334362722b25324625334525334362722b25324625334546696e616c6c792532432b69662b6f6e6c792b6f6e652b636f6e74656e742b626c6f636b2b69732b646973706c617965642b61742b612b74696d652532432b7468656e2b616e2b6164646974696f6e616c2b6e617669676174696f6e2b6d656e752b69732b70726f76696465642b746861742b616c6c6f77732b796f752b746f2b6a756d702b746f2b616e792b6f746865722b636f6e74656e742b626c6f636b2b6f6e2b746869732b706167652e2e2e25334362722b25324625334525334362722b253246253345416c6c2b6f662b746869732b616c6c6f77732b796f752b746f2b6372656174652b736c696465732b6c696b652b506f776572506f696e742b6f722b4b65796e6f74652e25334362722b253246253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (23, 14, 1, 'Slide 1: Introduction to Sequencing', 1, '2005-12-15 20:59:39', 1, '2005-12-15 20:59:39', 0x253343666f6e742b73697a652533442532323425323225334553656775652b616c6c6f77732b796f752b746f2b6372656174652b736c6964652b73686f77732b62792b73657474696e672b612b676976656e2b70616765253237732b646973706c61792b6f7074696f6e2b746f2b646973706c61792b6f6e6c792b6f6e652b636f6e74656e742b626c6f636b2b61742b612b74696d652e253343253246666f6e74253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (24, 14, 2, 'Slide 2: Creating a Sequence', 1, '2005-12-15 20:59:39', 1, '2005-12-15 20:59:39', 0x253044253041253343666f6e742b73697a6525334425323234253232253345546f2b6372656174652b612b73657175656e63652b696e2b53656775652532432b646f2b7468652b666f6c6c6f77696e6725334125334362722b253246253345253343253246666f6e742533452533436f6c2533452533436c69253345253343666f6e742b73697a6525334425323234253232253345436c69636b2b6f6e2b7468652b656469742b6c696e6b2b62656c6f772b7468652b7469746c652b6f662b796f75722b63757272656e742b706167652e253343253246666f6e742533452533432532466c692533452533436c69253345253343666f6e742b73697a652533442532323425323225334543686f73652b446973706c61792b6f7074696f6e732e253343253246666f6e742533452533432532466c692533452533436c69253345253343666f6e742b73697a65253344253232342532322533455365742b7468652b6e756d6265722b6f662b636f6e74656e742b626c6f636b732b746f2b646973706c61792b746f2b312e25334362722b253246253345253343253246666f6e742533452533432532466c692533452533432532466f6c253345253343666f6e742b73697a652533442532323425323225334525334362722b253246253345253343253246666f6e74253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (31, 21, 0, 'News', 1, '2005-12-15 22:40:36', 1, '2005-12-15 22:40:36', 0x4164642b616e6e6f756e63656d656e74732b746f2b746869732b706167652b6f662b7468652b736974652e2543322541302b4d6f73742b726563656e742b616e6e6f756e63656d656e74732b77696c6c2b62652b646973706c617965642b61742b7468652b746f702b6f662b7468652b706167652e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (32, 39, 0, 'New Post', 1, '2005-12-15 22:57:22', 1, '2005-12-15 22:44:34', 0x253044253041546f2b656e61626c652b746869732b626c6f672b706f73742b666f722b64697363757373696f6e2532432b646f2b7468652b666f6c6c6f77696e6725334125334362722b2532462533452533436f6c2533452533436c69253345436c69636b2b6f6e2b7468652b456469742b546869732b536974652b627574746f6e2b62656c6f772b25323869662b796f752b6172652b6e6f742b696e2b456469742b6d6f646525323925334362722b2532462533452533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b656469742b6c696e6b2b62656c6f772b746869732b636f6e74656e742b626c6f636b2b25334362722b2532462533452533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b446973637573732532464173736573732b6c696e6b2533432532466c692533452533436c69253345436865636b2b7468652b626f782b666f722b456e61626c652b44697363757373696f6e2533432532466c692533452533436c69253345436865636b2b626f7865732b746f2b737065636966792b77686f2b63616e2b7061727469636970616e742b696e2b746869732b64697363757373696f6e2533432532466c692533452533432532466f6c253345546f2b616c6c6f772b6f74686572732b746f2b6d616b652b626c6f672b706f7374696e67732b746f2b746869732b73656374696f6e2b6f662b796f75722b736974652532432b646f2b7468652b666f6c6c6f77696e6725334125334362722b2532462533452533436f6c2533452533436c69253345436c69636b2b6f6e2b7468652b456469742b546869732b536974652b627574746f6e2b62656c6f772b25323869662b796f752b6172652b6e6f742b696e2b456469742b6d6f64652532392533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b5065726d697373696f6e732b627574746f6e2b62656c6f772533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b4164642b456469746f722b627574746f6e2b616e642b6c6f6f6b2b75702b70656f706c652b746f2b6164642b61732b656469746f72732533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b456469742b5065726d697373696f6e732b6f662b436865636b65642b627574746f6e2b616e642b61737369676e2b4164642532432b456469742b616e642b44656c6574652b7065726d697373696f6e732b746f2b7468652b626c6f672b73656374696f6e2b6f662b746869732b736974652533432532466c692533452533432532466f6c2533455768656e2b706f7374696e672b626c6f672b656e74726965732532432b69742b69732b736f6d6574696d65732b75736566756c2b746f2b63617465676f72697a652b656163682b656e7472792e2b2b5365652b7468652b41637469766174696f6e2b253236616d702533422b43617465676f72792b6c696e6b2b7768656e2b61646465642b612b6e65772b706f73742e2b2b416c6c2b63617465676f726965732b77696c6c2b62652b6c69737465642b696e2b7468652b43617465676f72792b4c6973742b696e2b7468652b6c6566742b736964656261722e25334362722b253246253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (36, 54, 0, 'Slide 1: Introduction to Sequencing', 1, '2005-12-17 18:05:47', 1, '2005-12-17 17:51:06', 0x253044253041253343666f6e742b73697a65253344253232342532322533452532366e627370253342253343253246666f6e742533452533436469762b7374796c65253344253232746578742d616c69676e2533412b63656e746572253342253232253345253044253041253343666f6e742b73697a652533442532323425323225334553656775652b536c6964652b53686f7725334362722b253246253345253343253246666f6e742533452533432532466469762533452530442530412533436469762b7374796c65253344253232746578742d616c69676e2533412b6c656674253342253232253345253044253041253343666f6e742b73697a6525334425323234253232253345546f2b6372656174652b612b736c6964652b73686f772b696e2b53656775652b646f2b7468652b666f6c6c6f77696e6725334125334362722b253246253345253044253041253343253246666f6e742533452533436f6c253345253343666f6e742b73697a65253344253232342532322533452533436c692533454372656174652b612b6e65772b706167652b2532382532422b6164642b6974656d2532392b6f722b7573652b746869732b706167652533432532466c692533452530442530412533436c69253345436c69636b2b6f6e2b6c696e6b2b746f2b446973706c61792b4f7074696f6e732533432532466c692533452533436c692533455365742b7468652b6e756d6265722b6f662b636f6e74656e742b626c6f636b732b746f2b646973706c61792b746f2b312e2533432532466c692533452530442530412533436c692533454164642b636f6e74656e742b626c6f636b732b2532382532422b6164642b636f6e74656e742532392b746f2b7468652b706167652e2533432532466c69253345253343253246666f6e742533452533432532466f6c253345253044253041253044253041253044253041253343666f6e742b73697a65253344253232332532322533454c696d69742b796f75722b73656374696f6e2b746f2b6f6e652b706167652b69662b796f752b77616e742b746f2b7573652b7468652b77686f6c652b77696474682b6f662b796f75722b736974652b746f2b646973706c61792b796f75722b736c6964652b73686f772b25323873656374696f6e732b776974682b6f6e6c792b6f6e652b706167652b646f2b6e6f742b646973706c61792b706167652b6c696e6b732b696e2b7468652b73696465626172253239253343253246666f6e7425334525334362722b253246253345253044253041253343253246646976253345, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (37, 54, 1, 'Slide 2: Creating a Sequence', 1, '2005-12-17 18:06:18', 1, '2005-12-17 17:51:32', 0x2530442530412533436469762b7374796c65253344253232746578742d616c69676e2533412b63656e746572253342253232253345253343666f6e742b73697a652533442532323425323225334525334362722b2532462533452b536c6964652b3225334362722b2532462533452e2e2e25334362722b253246253345253343253246666f6e74253345253343253246646976253345, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (33, 51, 1, 'Topic 1', 1, '2005-12-17 17:38:47', 1, '2005-12-17 17:35:59', 0x2530442530412533436469762b636c6173732533442532326c6566746d617267696e2532322533452530442530412533437374726f6e67253345253343612b6e616d6525334425323233302532322b687265662533442532322535432535422535432535426c696e6b70617468253543253544253543253544253246696e6465782e706870253346253236616374696f6e25334473697465253236736974652533442535432535422535432535427369746525354325354425354325354425323673656374696f6e2533443925323670616765253344343025323673746f7279253344333025323664657461696c2533443330253232253345253343253246612533452533432532467374726f6e67253345546f2b656e61626c652b612b746f7069632b666f722b64697363757373696f6e2532432b646f2b7468652b666f6c6c6f77696e6725334125334362722b2532462533452533432532466469762533452533436f6c2533452533436c69253345436c69636b2b6f6e2b7468652b456469742b746869732b536974652b627574746f6e2b62656c6f772b25323869662b796f752b6172652b6e6f742b616c72656164792b696e2b456469742b6d6f646525323925334362722b2532462533452533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b656469742b6c696e6b2b62656c6f772b746869732b636f6e74656e742b626c6f636b2533432532466c692533452533436c692533455265706c6163652b746869732b746578742b776974682b2b796f75722b746f7069632b6f662b64697363757373696f6e2b25334362722b2532462533452533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b446973637573732532464173736573732b6c696e6b2533432532466c692533452533436c69253345436865636b2b7468652b626f782b666f722b456e61626c652b44697363757373696f6e2533432532466c692533452533436c69253345436865636b2b626f7865732b746f2b737065636966792b77686f2b63616e2b7061727469636970616e742b696e2b746869732b64697363757373696f6e2533432532466c692533452533432532466f6c253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (34, 51, 0, '', 1, '2005-12-17 17:37:44', 1, '2005-12-17 17:36:26', 0x253044253041416e79253044253041626c6f636b2b6f662b636f6e74656e742b746861742b69732b61646465642b746f2b612b53656775652b736974652b63616e2b62652b6d6164652b7468652b6f626a6563742530442530416f662b64697363757373696f6e2b6f722b7468652b736f757263652b6f662b616e2b6173736573736d656e742b73696d706c792b62792b656e61626c696e6725304425304164697363757373696f6e2532466173736573736d656e742b7768656e2b616464696e672b6f722b65646974696e672b69742e25334362722b25324625334525334362722b25324625334554776f2b636f6e74656e74253044253041626c6f636b732b253238546f7069632b492b616e642b4173736573736d656e742b492532392b686176652b6265656e2b61646465642b686572652b62656c6f772b61732530442530416578616d706c65732e25334362722b25324625334525334362722b2532462533452530442530412533436469762b7374796c65253344253232666f6e742d73697a652533412b313070782533422b636f6c6f722533412b7267622532383135332532432b3135332532432b313533253239253342253232253345253238796f752b63616e2b64656c6574652b6f722b656469742b616e792b6f662b746869732b6d6174657269616c2b61732b6e65656465642e2e2e253239253343253246646976253345253044253041253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (35, 51, 2, 'Assessment I', 1, '2005-12-17 17:44:13', 1, '2005-12-17 17:44:13', 0x416e2b6173736573736d656e742b69732b616e792b636f6e74656e742b626c6f636b2b746861742b69732b656e61626c65642b666f722b64697363757373696f6e2b77686572652b7468652b6173736573736d656e742b6f7074696f6e2b69732b63686f73656e2e2543322541302b5768656e2b706f7374696e672b746f2b616e2b6173736573736d656e742532432b7061727469636970616e74732b77696c6c2b7365652b6f6e6c792b74686569722b6f776e2b706f7374732b616e642b616e792b7265706c6965732b746f2b74686569722b706f7374732b6d6164652b62792b7468652b736974652b6f776e65722e2543322541302b25334362722b25324625334525334362722b2532462533455468652b736974652b6f776e65722b77696c6c2b7365652b616c6c2b706f7374732b616e642b69732b61626c652b746f2b726174652b656163682b706f73742b616e642b7265706c792b746f2b656163682b7061727469636970616e742b696e646976696475616c6c792b2532387061727469636970616e74732b77696c6c2b7365652b6f6e6c792b74686569722b6f776e2b706f7374732b616e642b7265706c6965732b746f2b74686569722b706f7374732b6d6164652b62792b7468652b736974652b6f776e65722e25334362722b25324625334525334362722b253246253345, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (38, 42, 0, '', 1, '2005-12-17 20:30:02', 1, '2005-12-17 20:30:02', 0x466f722b746869732b706167652532432b6164642b7468652b66697273742b7765656b253237732b61737369676e6d656e74732e2b446f2b7468652b73616d652b666f722b7468652b73756273657175656e742b7765656b732b6f6e2b74686569722b726573706563746976652b70616765732e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (39, 64, 0, 'New Post', 1, '2005-12-17 20:36:26', 1, '2005-12-17 20:36:26', 0x546f2b656e61626c652b746869732b626c6f672b706f73742b666f722b64697363757373696f6e2532432b646f2b7468652b666f6c6c6f77696e6725334125334362722b2532462533452533436f6c2533452533436c69253345436c69636b2b6f6e2b7468652b456469742b546869732b536974652b627574746f6e2b62656c6f772b25323869662b796f752b6172652b6e6f742b696e2b456469742b6d6f646525323925334362722b2532462533452533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b656469742b6c696e6b2b62656c6f772b746869732b636f6e74656e742b626c6f636b2b25334362722b2532462533452533432532466c692533452533436c69253345436c69636b2b6f6e2b7468652b446973637573732532464173736573732b6c696e6b2533432532466c692533452533436c69253345436865636b2b7468652b626f782b666f722b456e61626c652b44697363757373696f6e2533432532466c692533452533436c69253345436865636b2b626f7865732b746f2b737065636966792b77686f2b63616e2b7061727469636970616e742b696e2b746869732b64697363757373696f6e2533432532466c692533452533432532466f6c253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '1', '1', 'Discuss', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (40, 67, 0, '', 1, '2005-12-17 22:05:13', 1, '2005-12-17 22:05:13', 0x4164642b686572652b612b6465736372697074696f6e2b666f722b796f75722b636f757273652e2e2e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (41, 68, 0, 'Syllabus', 1, '2005-12-17 22:05:59', 1, '2005-12-17 22:05:42', 0x4164642b686572652b796f75722b73796c6c616275732e2e2e2b25334362722b253246253345, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (42, 68, 1, 'Download Syllabus', 1, '2005-12-17 22:11:02', 1, '2005-12-17 22:10:13', 0x546f2530442530416d616b652b796f75722b73796c6c616275732b617661696c61626c652b666f722b646f776e6c6f61642532432b646f2b7468652b666f6c6c6f77696e6725334125334362722b2532462533452533436f6c2533452533436c69253345436c69636b2b7468652b25323671756f742533426164642b636f6e74656e7425323671756f74253342253044253041627574746f6e2b62656c6f772e25334362722b2532462533452b2533432532466c692533452533436c6925334543686f6f73652b25323671756f7425334246696c652b666f722b446f776e6c6f61642e25323671756f742533422b2533432532466c692533452533436c69253345436c69636b2b7468652b4d656469612b4c6962726172792b627574746f6e2b616e642b6974732b62726f7773652b627574746f6e2b746f2b6c6f636174652b796f75722b73796c6c616275732b6f6e2b796f75722b636f6d70757465722b616e642b6164642b746f2b796f75722b73697465253237732b4d656469612b4c6962726172792e2533432532466c692533452533436c69253345436c69636b2b7468652b7573652b627574746f6e2b746f2b696e636c7564652b696e2b7468652b53796c6c616275732b706167652b6f662b746869732b736974652e2533432532466c692533452533436c6925334544656c6574652b746869732b636f6e74656e742b626c6f636b2e25334362722b2532462533452533432532466c692533452533432532466f6c25334525334362722b253246253345253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (43, 69, 0, '', 1, '2005-12-17 22:12:31', 1, '2005-12-17 22:12:31', 0x4164642b746f2b746869732b706167652b616e792b696e666f726d6174696f6e2b61626f75742b796f752b746861742b796f752b776f756c642b6c696b652b746f2b73686172652b776974682b7468652b7061727469636970616e74732b6f662b746869732b636f757273652e2e2e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (44, 73, 0, '', 1, '2005-12-18 12:51:40', 1, '2005-12-18 12:51:40', 0x546869732b73656374696f6e2b6f662b636f757273652b736974652b636f6e7461696e732b616c6c2b7468652b61737369676e6d656e74732b666f722b7468652b73656d65737465722e2e2e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'story', NULL, '0');
		INSERT INTO `story` VALUES (45, 50, 0, 'Middlebury College', 1, '2005-12-18 12:53:55', 1, '2005-12-18 12:53:55', 0x546869732b69732b4d6964646c65627572792b436f6c6c656765253237732b776562736974652e253044, '', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '', '', '', '', 'html', 'link', 18, '0');
		
		INSERT INTO `tags` VALUES ('story', 18, 1, 'Instructions', '2005-12-15 20:47:16');
		INSERT INTO `tags` VALUES ('story', 19, 1, 'Instructions', '2005-12-15 20:51:54');
		INSERT INTO `tags` VALUES ('story', 19, 1, 'Help', '2005-12-15 20:51:54');
		INSERT INTO `tags` VALUES ('story', 20, 1, 'Instructions', '2005-12-15 20:56:56');
		INSERT INTO `tags` VALUES ('story', 20, 1, 'Features', '2005-12-15 20:56:56');
		INSERT INTO `tags` VALUES ('story', 21, 1, 'Instructions', '2005-12-15 20:57:14');
		INSERT INTO `tags` VALUES ('story', 32, 1, 'new', '2005-12-15 22:54:44');
		INSERT INTO `tags` VALUES ('story', 39, 1, 'New', '2005-12-17 20:36:26');
	";
	$queryArray = explode(";",$query);
	foreach ($queryArray AS $query) {
			$query = trim($query);
			if (!$query)
				continue;
				
			db_query($query);
			if (mysql_error()) {
				print "\n<hr />";
				printpre($query);
				printpre(mysql_error());
			}
	}
	
	/******************************************************************************
	 * Add in Segue 1.7.0 database updates
	 ******************************************************************************/
	$query = "
		CREATE TABLE version (
		  version_id int(10) unsigned NOT NULL auto_increment,
		  FK_parent int(10) unsigned NOT NULL default '0',
		  FK_createdby int(10) unsigned NOT NULL default '0',
		  version_order INT( 10 ) unsigned NOT NULL  default '0',
		  version_created_tstamp timestamp(14) NOT NULL,
		  version_text_short mediumblob NOT NULL,
		  version_text_long mediumblob NOT NULL,
		  version_comments mediumblob NOT NULL,
		  PRIMARY KEY  (version_id)
		) TYPE=MyISAM;
	";
	
	db_query($query);
	if (mysql_error()) {
		print "\n<hr />";
		printpre($query);
		printpre(mysql_error());
	}

	$query = "
		ALTER TABLE 
			page
		ADD 
			page_show_versions enum('0','1') NOT NULL default '0' AFTER page_show_date
	";

	db_query($query);
	if (mysql_error()) {
		print "\n<hr />";
		printpre($query);
		printpre(mysql_error());
	}
	
	
	$path = realpath($cfg[uploaddir]);
		
	if (!file_exists($path."/RSScache/")) {
		mkdir ($path."/RSScache/", 0770);
	}
	
	if (!file_exists($path."/RSScache/autocache/")) {
		mkdir ($path."/RSScache/autocache/", 0770);
	}
	
	print "\n<h3>...Database Auto-Configuration Complete. You will not see this message again.</h3>";
}

