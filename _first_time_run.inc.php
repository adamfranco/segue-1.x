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
	"user"
);

$query = "SHOW TABLES";
$r = db_query($query);
$existingTables = array();
while($a = db_fetch_assoc($r)) {
	foreach($a as $k => $v) {
		$existingTables[] = $v;
	}
}

print_r($tables);

$allTablesExist = true;
foreach ($neededTables as $table) {
	if (!in_array($table,$existingTables)) {
		print "<br>Missing Table: $table";
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
		  class_section varchar(25) default NULL,
		  class_name varchar(255) NOT NULL default '',
		  FK_owner int(10) unsigned default NULL,
		  FK_ugroup int(10) unsigned default NULL,
		  class_semester enum('w','s','f','l') NOT NULL default 'w',
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
		  FK_story int(10) unsigned NOT NULL default '0',
		  discussion_order int(10) unsigned NOT NULL default '0',
		  FK_parent int(10) unsigned default NULL,
		  PRIMARY KEY  (discussion_id),
		  KEY FK_author (FK_author),
		  KEY FK_story (FK_story),
		  KEY discussion_order (discussion_order),
		  KEY FK_parent (FK_parent),
		  KEY FK_media (FK_media),
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
		  page_title varchar(255) NOT NULL default '',
		  FK_updatedby int(10) unsigned NOT NULL default '0',
		  page_updated_tstamp timestamp(14) NOT NULL,
		  FK_createdby int(10) unsigned NOT NULL default '0',
		  page_created_tstamp timestamp(14) NOT NULL,
		  page_active enum('0','1') NOT NULL default '0',
		  page_activate_tstamp timestamp(14) NOT NULL,
		  page_deactivate_tstamp timestamp(14) NOT NULL,
		  page_show_creator enum('0','1') NOT NULL default '0',
		  page_show_date enum('0','1') NOT NULL default '0',
		  page_show_hr enum('0','1') NOT NULL default '0',
		  page_display_type enum('page','heading','divider','link') NOT NULL default 'page',
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
		  story_category varchar(255) NOT NULL default '',
		  story_text_type enum('text','html') NOT NULL default 'text',
		  story_display_type enum('story','image','file','link') NOT NULL default 'story',
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
		  user_type enum('stud','prof','staff','admin') NOT NULL default 'stud',
		  user_authtype enum('ldap','db','pam') NOT NULL default 'ldap',
		  PRIMARY KEY  (user_id),
		  UNIQUE KEY user_uname (user_uname),
		  KEY user_type (user_type),
		  KEY user_fname (user_fname),
		  KEY user_last_name (user_last_name)
		) TYPE=MyISAM
	";
	$queryArray = explode(";",$query);
	foreach ($queryArray AS $query) {
//		print "<br>\"$query\"";
			db_query($query);
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
		INSERT INTO media VALUES (6, 5, 1, 'http://segue.middlebury.edu/sites/segue', 'remote', 'other', 1, 20030530134928, NULL);
		INSERT INTO media VALUES (5, 5, 1, 'http://www.google.com', 'remote', 'other', 1, 20030530134609, NULL);
		INSERT INTO media VALUES (4, 5, 1, ' http://www.middlebury.edu', 'remote', 'other', 1, 20030530133509, NULL);
		
		INSERT INTO page VALUES (1, 1, 0, 'Page One', 1, 20030529161839, 1, 20030529161839, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (2, 1, 1, 'The Second Page', 1, 20030529161851, 1, 20030529161851, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (3, 1, 2, 'Page Three', 1, 20030529161905, 1, 20030529161905, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (4, 2, 0, 'Another Page', 1, 20030529161923, 1, 20030529161923, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (5, 3, 0, 'Description', 1, 20030530111611, 1, 20030530110850, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (6, 3, 1, 'Professor', 1, 20030530112135, 1, 20030530110850, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (8, 4, 1, 'Download Syllabus', 1, 20030530112722, 1, 20030530112418, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 1, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (7, 4, 0, 'View Syllabus', 1, 20030530112655, 1, 20030530110850, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (9, 5, 0, 'Description', 1, 20030530130657, 1, 20030530130657, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (10, 5, 3, 'Professor', 1, 20030530130748, 1, 20030530130657, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (11, 6, 0, 'View Syllabus', 1, 20030530130657, 1, 20030530130657, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (12, 6, 1, 'Download Syllabus', 1, 20030530130657, 1, 20030530130657, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (13, 5, 1, 'Requirements', 1, 20030530130725, 1, 20030530130721, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 1, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (14, 5, 2, 'Grading', 1, 20030530130748, 1, 20030530130745, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (15, 7, 0, 'Week 1', 1, 20030530131047, 1, 20030530131047, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (16, 7, 1, 'Week 2', 1, 20030530131054, 1, 20030530131054, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (17, 7, 2, 'Week 3', 1, 20030530131102, 1, 20030530131102, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (18, 7, 3, 'Week 4', 1, 20030530131110, 1, 20030530131110, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (19, 7, 4, 'Week 5', 1, 20030530131118, 1, 20030530131118, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (20, 7, 5, 'Week 6', 1, 20030530131129, 1, 20030530131129, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (21, 7, 6, 'Week 7', 1, 20030530131136, 1, 20030530131136, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (22, 7, 7, 'Week 8', 1, 20030530131144, 1, 20030530131144, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (23, 7, 8, 'Week 9', 1, 20030530131149, 1, 20030530131149, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (24, 7, 9, 'Week 10', 1, 20030530131157, 1, 20030530131157, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (25, 7, 10, 'Week 11', 1, 20030530131202, 1, 20030530131202, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (26, 7, 11, 'Week 12', 1, 20030530131210, 1, 20030530131210, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (27, 7, 12, 'Week 13', 1, 20030530131216, 1, 20030530131216, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (28, 8, 0, 'Links', 1, 20030530131313, 1, 20030530131313, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (29, 9, 0, 'Description', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (30, 9, 1, 'Requirements', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 2, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (31, 9, 2, 'Grading', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (32, 9, 3, 'Professor', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (33, 10, 0, 'Week 1', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (34, 10, 1, 'Week 2', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (35, 10, 2, 'Week 3', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (36, 10, 3, 'Week 4', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (37, 10, 4, 'Week 5', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (38, 10, 5, 'Week 6', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (39, 10, 6, 'Week 7', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (40, 10, 7, 'Week 8', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (41, 10, 8, 'Week 9', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (42, 10, 9, 'Week 10', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (43, 10, 10, 'Week 11', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (44, 10, 11, 'Week 12', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (45, 10, 12, 'Week 13', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (46, 11, 0, 'View Syllabus', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (47, 11, 1, 'Download Syllabus', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (48, 12, 0, 'Links', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (49, 9, 4, 'Announcements', 1, 20030530131641, 1, 20030530131539, '1', 00000000000000, 00000000000000, '0', '1', '1', 'page', 0, 'addeddesc', 'week', '0', '0');
		INSERT INTO page VALUES (50, 13, 0, 'Topics', 1, 20030530131857, 1, 20030530131813, '1', 00000000000000, 00000000000000, '0', '1', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (51, 14, 0, 'Description', 1, 20030530132119, 1, 20030530132052, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (52, 14, 1, 'Articles', 1, 20030530132514, 1, 20030530132052, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (53, 14, 2, 'Links', 1, 20030530133327, 1, 20030530132052, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (54, 15, 0, 'Discussion', 1, 20030530155920, 1, 20030530132052, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (55, 15, 1, 'Collaboration', 1, 20030530161605, 1, 20030530161605, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (56, 15, 2, 'File Downloads', 1, 20030530161641, 1, 20030530161641, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', 0, 'custom', 'none', '0', '0');
		INSERT INTO page VALUES (57, 16, 0, 'Page One', 1, 20031110161133, 1, 20031110161133, '1', 00000000000000, 00000000000000, '0', '0', '0', 'page', NULL, 'custom', 'none', '0', '0');
		
		INSERT INTO permission VALUES (1, NULL, 'everyone', 1, 'site', 'v');
		INSERT INTO permission VALUES (2, NULL, 'everyone', 2, 'site', 'v');
		INSERT INTO permission VALUES (3, NULL, 'everyone', 3, 'site', 'v');
		INSERT INTO permission VALUES (8, NULL, 'everyone', 4, 'site', 'v');
		INSERT INTO permission VALUES (11, NULL, 'everyone', 5, 'site', 'v');
		INSERT INTO permission VALUES (23, NULL, 'institute', 41, 'story', 'di');
		INSERT INTO permission VALUES (21, NULL, 'institute', 40, 'story', 'di');
		INSERT INTO permission VALUES (25, NULL, 'everyone', 7, 'site', 'v');
		INSERT INTO permission VALUES (26, NULL, 'everyone', 8, 'site', 'v');
		
		INSERT INTO section VALUES (1, 1, 0, 'Section One', 1, 20030529161801, 1, 20030529161801, '1', 00000000000000, 00000000000000, '0', 'section', 1);
		INSERT INTO section VALUES (2, 1, 1, 'Section Two', 1, 20030529161811, 1, 20030529161811, '1', 00000000000000, 00000000000000, '0', 'section', 0);
		INSERT INTO section VALUES (3, 2, 0, 'Introduction', 1, 20030530111124, 1, 20030530110850, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (4, 2, 1, 'Syllabus', 1, 20030530111139, 1, 20030530110850, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (5, 3, 0, 'Introduction', 1, 20030530130657, 1, 20030530130657, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (6, 3, 2, 'Syllabus', 1, 20030530130945, 1, 20030530130657, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (7, 3, 1, 'Assignments', 1, 20030530130945, 1, 20030530130942, '1', 00000000000000, 00000000000000, '0', 'section', 0);
		INSERT INTO section VALUES (8, 3, 3, 'Links', 1, 20030530131023, 1, 20030530131023, '1', 00000000000000, 00000000000000, '0', 'section', 0);
		INSERT INTO section VALUES (9, 4, 0, 'Introduction', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (10, 4, 1, 'Assignments', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (11, 4, 2, 'Syllabus', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (12, 4, 3, 'Links', 1, 20030530131522, 1, 20030530131522, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (13, 4, 4, 'Discussions', 1, 20030530131802, 1, 20030530131802, '1', 00000000000000, 00000000000000, '0', 'section', 0);
		INSERT INTO section VALUES (14, 5, 0, 'Introduction', 1, 20030530132105, 1, 20030530132052, '1', 00000000000000, 00000000000000, '0', 'section', 3);
		INSERT INTO section VALUES (15, 5, 1, 'Advanced', 1, 20030530155807, 1, 20030530132052, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		INSERT INTO section VALUES (16, 7, 0, 'Section One', 1, 20031110161133, 1, 20031110161133, '1', 00000000000000, 00000000000000, '0', 'section', NULL);
		
		INSERT INTO site VALUES (1, 'Simple Site', 'minimal', '', '', '', 1, 20030529161751, 1, 20030529161751, '1', 00000000000000, 00000000000000, '1');
		INSERT INTO site VALUES (2, 'Brief Course Site', 'minimal', '', '', '', 1, 20030530110850, 1, 20030530110850, '1', 00000000000000, 00000000000000, '1');
		INSERT INTO site VALUES (3, 'Standard Course Site', 'minimal', '', '', '', 1, 20030530130657, 1, 20030530130657, '1', 00000000000000, 00000000000000, '1');
		INSERT INTO site VALUES (4, 'Extensive Course Site', 'minimal', '', '', '', 1, 20030530131521, 1, 20030530131521, '1', 00000000000000, 00000000000000, '1');
		INSERT INTO site VALUES (5, 'Segue Sample Site', 'minimal', '', '', '', 1, 20030530132052, 1, 20030530132052, '1', 00000000000000, 00000000000000, '1');
		INSERT INTO site VALUES (7, 'Advanced: Single Section', 'minimal', '', '', '', 1, 20031110161133, 1, 20031110161133, '1', 00000000000000, 00000000000000, '');
		INSERT INTO site VALUES (8, 'Advanced: Blank', 'minimal', '', '', '', 1, 20031110161324, 1, 20031110161324, '1', 00000000000000, 00000000000000, '');
		
		INSERT INTO site_editors VALUES (1, NULL, 'everyone');
		INSERT INTO site_editors VALUES (1, NULL, 'institute');
		INSERT INTO site_editors VALUES (2, NULL, 'everyone');
		INSERT INTO site_editors VALUES (2, NULL, 'institute');
		INSERT INTO site_editors VALUES (3, NULL, 'everyone');
		INSERT INTO site_editors VALUES (3, NULL, 'institute');
		INSERT INTO site_editors VALUES (4, NULL, 'everyone');
		INSERT INTO site_editors VALUES (4, NULL, 'institute');
		INSERT INTO site_editors VALUES (5, NULL, 'everyone');
		INSERT INTO site_editors VALUES (5, NULL, 'institute');
		INSERT INTO site_editors VALUES (7, NULL, 'everyone');
		INSERT INTO site_editors VALUES (7, NULL, 'institute');
		INSERT INTO site_editors VALUES (8, NULL, 'everyone');
		INSERT INTO site_editors VALUES (8, NULL, 'institute');
		
		INSERT INTO slot VALUES (1, 'template0', 1, NULL, 1, 'system', NULL);
		INSERT INTO slot VALUES (2, 'template1', 1, NULL, 4, 'system', NULL);
		INSERT INTO slot VALUES (3, 'template2', 1, NULL, 3, 'system', NULL);
		INSERT INTO slot VALUES (4, 'template3', 1, NULL, 2, 'system', NULL);
		INSERT INTO slot VALUES (5, 'sample', 1, NULL, 5, 'system', NULL);
		INSERT INTO slot VALUES (6, 'template4', 1, NULL, 7, 'system', 0);
		INSERT INTO slot VALUES (7, 'template5', 1, NULL, 8, 'system', 0);
		
		INSERT INTO story VALUES (1, 1, 0, 'Welcome to your new site!', 1, 20030529162308, 1, 20030529162257, '%0D%0A-+To+add+another+block+of+text+below+this+text%2C+click+the%0D%0A%22%3Cb%3E%2Badd+content%3C%2Fb%3E%22+button+below.%0D%0A%0D%0A-+To+edit+this+text%2C+click+the+%22%3Cb%3Eedit%3C%2Fb%3E%22+button+below.%0D%0A%0D%0A-+To+add+another+page+or+other+item+to+this+section%2C+click+on+the%0D%0A%22%3Cb%3E%2Badd+item%3C%2Fb%3E%22+button+to+the+left.%0D%0A%0D%0A-+Clicking+on+the+%22%3Cb%3Eedit%3C%2Fb%3E%22+button+next+to+a+page+or+section%0D%0Awill+allow+you+to+rename+it.%0D%0A%0D%0A-+To+add+a+new+section%2C+click+the+%22%3Cb%3E%2Badd+section%3C%2Fb%3E%22+button+above.+', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (2, 2, 0, 'Sample Content', 1, 20030529162412, 1, 20030529162412, 'This+is+some+sample+content+on+the+Second+Page.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (3, 2, 1, '', 1, 20030529162534, 1, 20030529162534, 'Some+more+sample+content....', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (4, 4, 0, '', 1, 20030529162636, 1, 20030529162636, 'This+is+some+sample+content+on+the+only+page+in+the+second+section.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (5, 5, 0, '', 1, 20030530112233, 1, 20030530110850, 'Add+here+a+description+for+your+course...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (6, 6, 0, '', 1, 20030530112308, 1, 20030530110850, 'Put+here+information+about+you%2C+the+professor.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (9, 8, 0, '', 1, 20030530112752, 1, 20030530112506, 'To+make+your+syllabus+avialable+for+download%2C+click+the+%22add+content%22+button+and+choose+%22File+for+Download%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%22Add%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+PDF%2C+RTF%2C+and+Microsoft+Word.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (8, 7, 0, '', 1, 20030530112809, 1, 20030530110850, 'Add+here+a+text+version+of+your+syllabus.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (10, 9, 0, '', 1, 20030530130657, 1, 20030530130657, 'Add+here+a+description+for+your+course...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (11, 10, 0, '', 1, 20030530130657, 1, 20030530130657, 'Put+here+information+about+you%2C+the+professor.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (12, 11, 0, '', 1, 20030530130657, 1, 20030530130657, 'Add+here+a+text+version+of+your+syllabus.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (13, 12, 0, '', 1, 20030530130657, 1, 20030530130657, 'To+make+your+syllabus+avialable+for+download%2C+click+the+%22add+content%22+button+and+choose+%22File+for+Download%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%22Add%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+PDF%2C+RTF%2C+and+Microsoft+Word.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (14, 13, 0, '', 1, 20030530130818, 1, 20030530130818, 'Add+here+content+such+as+requirements+for+your+course...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (15, 14, 0, '', 1, 20030530130850, 1, 20030530130850, 'Add+here+grading+policies+for+your+course...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (16, 15, 0, '', 1, 20030530131246, 1, 20030530131246, 'For+this+page%2C+add+the+first+week%27s+assignments.+Do+the+same+for+the+subsequent+weeks+on+their+respective+pages.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (17, 28, 0, '', 1, 20030530131405, 1, 20030530131405, 'Add+your+links+to+this+page...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (18, 29, 0, '', 1, 20030530131521, 1, 20030530131521, 'Add+here+a+description+for+your+course...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (19, 30, 0, '', 1, 20030530131521, 1, 20030530131521, 'Add+here+content+such+as+requirements+for+your+course...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (20, 31, 0, '', 1, 20030530131521, 1, 20030530131521, 'Add+here+grading+policies+for+your+course...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (21, 32, 0, '', 1, 20030530131521, 1, 20030530131521, 'Put+here+information+about+you%2C+the+professor.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (22, 33, 0, '', 1, 20030530131521, 1, 20030530131521, 'For+this+page%2C+add+the+first+week%27s+assignments.+Do+the+same+for+the+subsequent+weeks+on+their+respective+pages.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (23, 46, 0, '', 1, 20030530131522, 1, 20030530131522, 'Add+here+a+text+version+of+your+syllabus.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (24, 47, 0, '', 1, 20030530131522, 1, 20030530131522, 'To+make+your+syllabus+avialable+for+download%2C+click+the+%22add+content%22+button+and+choose+%22File+for+Download%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%22Add%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+PDF%2C+RTF%2C+and+Microsoft+Word.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (25, 48, 0, '', 1, 20030530131522, 1, 20030530131522, 'Add+your+links+to+this+page...', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (26, 49, 0, '', 1, 20030530131727, 1, 20030530131727, 'Add+here+any+announcements+you+have+for+visitors+to+your+site.+Notice+how+all+content+displays+the+date+it+was+added%2Fedited+underneath+it.%0D%0A%0D%0AAlso+notice+how+the+content+on+this+page+is+archived+by+week+%28meaning+the+last+7+days+of+content+are+displayed+by+default%29.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (27, 50, 0, '', 1, 20030530131837, 1, 20030530131837, 'Add+here+any+topics+you+wish+to+be+discussed.+Notice+how+the+date%2Ftime+the+content+was+added+is+displayed+beneath+it.%0D%0A%0D%0ATo+enable+discussion+on+a+certain+Text+Block%2C+click+on+%27%2B+add+content%27+below%2C+and+click+the+Switch+to+Advanced+View+button.+Then+scroll+down+to+%22Discussion%22+and+check+the+enable+discussion+checkbox.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (28, 51, 0, 'General Info', 1, 20030530132213, 1, 20030530132052, 'In+%3Cb%3ESegue%3C%2Fb%3E+you+can+add+sections+%28above%2C+like+this+one%2C+Introduction%29.+Each+section+contains+one+or+multiple+pages+%28on+the+left%2C+Description+for+example%29.+On+every+page%2C+you+can+add+content+%28like+this%29.+Content+can+range+from+plain+text+to+images+to+files+to%2C+well%2C+whatever+you+want.+Pages+can+contain+as+many+text+blocks+%28entities+with+an+optional+title%2C+content%2C+and+optional+discussions%29+as+you+want.+', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (32, 51, 1, '', 1, 20030530132332, 1, 20030530132332, 'Here+are+some+ideas+for+using+this+space%3A+%0D%0A%3Cul%3E%0D%0A%3Cli%3EClass+assignments%0D%0A%3Cli%3EA+resume%0D%0A%3Cli%3EWeekly+archived+articles%0D%0A%3Cli%3EDiscussion+Topics%0D%0A%3C%2Ful%3E', '', '0', 00000000000000, 00000000000000, '0', '', 'html', 'story', 0, '0');
		INSERT INTO story VALUES (29, 52, 0, 'Journal Article Excerpts', 1, 20030530132622, 1, 20030530132052, 'Below+are+a+few+examples+of+creating+text+blocks+with+an+abridged+version+of+the+content+and+an+associated+full+content.+These+are+excerpts+from+journals%2Fnews+taken+from+the+web+on+July+19th%2C+2002.+The+full+content+for+the+article+is+not+the+full+article+taken+from+the+web%2C+though.+Links+are+provided+to+the+journal%27s+site.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (30, 52, 1, 'A Space in Time', 1, 20030530133253, 1, 20030530132052, 'In+the+evenings%2C+when+my+particular+piece+of+Earth+has+turned+away+from+the+Sun%2C+and+is+exposed+instead+to+the+rest+of+the+cosmos%2C+I+sit+in+front+of+a+keyboard%2C+log+on%2C+and+seek+out+the+windows+that+look+down+at+the+planets+and+out+at+the+stars.+It%27s+a+markedly+different+experience+from+looking+at+reproductions+on+paper.+What+I+see+is+closer+to+the+source.+In+fact%2C+it%27s+indistinguishable+from+the+source.+These+are+images+that+have+never+registered+on+a+negative.+Like+the+Internet+itself%2C+they+are+products+of+a+digitized+era.+Over+the+past+couple+of+years+I%27ve+been+monitoring+the+long+rectangular+strips+of+Martian+surface+being+beamed+across+the+void%2C+in+a+steady+stream+of+zeroes+and+ones%2C+from+the+umbrella-shaped+high-gain+antenna+of+the+Mars+Global+Surveyor+spacecraft.+These+pictures+are+so+fresh+that+their+immediacy+practically+crackles.+Call+it+%22chrono-clarity.%22+That+bluish+wispy+cloud...', 'In+the+evenings%2C+when+my+particular+piece+of+Earth+has+turned+away+from+the+Sun%2C+and+is+exposed+instead+to+the+rest+of+the+cosmos%2C+I+sit+in+front+of+a+keyboard%2C+log+on%2C+and+seek+out+the+windows+that+look+down+at+the+planets+and+out+at+the+stars.+It%27s+a+markedly+different+experience+from+looking+at+reproductions+on+paper.+What+I+see+is+closer+to+the+source.+In+fact%2C+it%27s+indistinguishable+from+the+source.+These+are+images+that+have+never+registered+on+a+negative.+Like+the+Internet+itself%2C+they+are+products+of+a+digitized+era.+Over+the+past+couple+of+years+I%27ve+been+monitoring+the+long+rectangular+strips+of+Martian+surface+being+beamed+across+the+void%2C+in+a+steady+stream+of+zeroes+and+ones%2C+from+the+umbrella-shaped+high-gain+antenna+of+the+Mars+Global+Surveyor+spacecraft.+These+pictures+are+so+fresh+that+their+immediacy+practically+crackles.+Call+it+%22chrono-clarity.%22+That+bluish+wispy+cloud%2C+for+example%2C+hovering+over+the+Hecates+Tholus+volcano%2C+which+rears+above+the+pockmarked+surface+of+the+Elysium+Volcanic+Region+in+the+Martian+eastern+hemisphere%E2%80%94it+has+barely+had+time+to+disperse+before+I%2C+or+anyone+with+Internet+access%2C+can+see+it+in+all+its+spooky+beauty.+The+volcano+emerges+from+the+pink+Martian+desert%2C+which+looks+organic+and+impressionable%E2%80%94like+human+skin%2C+or+the+surface+of+a+clay+pot+before+firing.+The+tenuous+cloud+floats+near+the+volcano%27s+mouth%2C+as+if+in+prelude+to+an+eruption.+It%27s+a+picture+composed+of+millions+of+dots+and+dashes+of+data%2C+produced+by+a+transmission+technique+just+a+few+steps+removed+from+Morse+code%3B+but+it+reveals+a+landscape+the+likes+of+which+Samuel+Morse%2C+let+alone+the+ranks+of+Earth-based+astronomers+who+have+surveyed+the+planets+since+well+before+Babylonian+times%2C+could+scarcely+have+envisioned.%0D%0A%0D%0AIn+case+there+was+any+doubt%2C+many+of+those+good+old+science-fiction+predictions+from+the+1950s+and+the+1960s+are+coming+true.+%22NEW+SQUAD+OF+ROBOTS+READY+TO+ASSAULT+MARS%22+read+a+1998+headline+in+the+online+Houston+Chronicle%2C+stirring+submerged+memories+of+my+adolescent+readings+of+Isaac+Asimov%27s+I%2C+Robot+stories.+But+Asimov%27s+sentient+robots+were+frequently+confused.+Something+always+seemed+to+be+going+wrong+with+them%2C+and+the+mayhem+that+followed+could+inevitably+be+traced+back+to+a+programming+error+by+their+human+handlers%E2%80%94a+situation+not+unfamiliar+to+those+running+NASA%27s+Mars+program%2C+which+was+temporarily+grounded+after+a+catastrophic+pair+of+failures+in+late+1999.+%28The+Mars+Climate+Orbiter+was+lost+owing+to+the+stark+failure+by+one+group+of+engineers+to+translate+another+group%27s+figures+into+metric+units+of+measurement%2C+and+the+Mars+Polar+Lander+because+for+some+unfathomable+reason+its+landing+gear+hadn%27t+been+adequately+tested.%29%0D%0A%0D%0ATo+read+the+full+article+from+%3Ci%3EThe+Atlantic%3C%2Fi%3E%2C+%3Ca+href%3D%27http%3A%2F%2Fwww.theatlantic.com%2Fissues%2F2002%2F07%2Fbenson.htm%27%3Eclick+here%3C%2Fa%3E.', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (31, 54, 0, '', 1, 20030530160136, 1, 20030530132052, 'This+page+contains+multiple+discussion+topics%2C+each+of+which+can+be+discussed+by+logged+in+users+%28you+have+the+option+of+allowing+anyone+to+discuss%2C+or+only+students+in+your+class%2C+for+class+websites%29.+Click+the+discussions+link+below+each+topic+to+view+the+discussion%2C+or+add+your+own+two+cents.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');
		INSERT INTO story VALUES (33, 51, 2, 'Another Example', 1, 20030530132438, 1, 20030530132427, 'This+is+yet+another+example+of+a+text+block.+This+text+block+has+a+title%2C+%22Another+Example%22.+The+text+block+below+does+not+have+a+title+associated+with+it.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (34, 51, 3, '', 1, 20030530132453, 1, 20030530132453, 'This+text+block+does+not+have+an+associated+title.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (35, 52, 2, 'Critics lament the state of stem cell research', 1, 20030530133204, 1, 20030530133204, 'n+a+move+to+appease+those+who+support+the+idea%2C+and+those+who+believe+creating+human+embryonic+stem+cells+for+research+is+wrong%2C+Bush+decided+funding+would+only+support+research+on+a+small+number+of+existing+cell+lines+held+by+only+a+few+labs.%0D%0A%0D%0ANow%2C+stem+cell+research+is+at+virtual+standstill%2C+critics+say.+Researchers+complain+they+can%27t+get+the+cells+they+need+because+supplies+are+so+scarce.%0D%0A%0D%0AOne+of+those+critics+is+Stephen+Wakefield+from+Atlanta%2C+Georgia.+He+used+to+ski+and+run+marathons+--+but+not+anymore.%0D%0A%0D%0ASix+years+ago%2C+he+was+diagnosed+with+a+neuromuscular+disorder+similar+to+Lou+Gehrig%27s+disease.+There%27s+no+cure+and+it+will+only+get+worse.', 'In+a+move+to+appease+those+who+support+the+idea%2C+and+those+who+believe+creating+human+embryonic+stem+cells+for+research+is+wrong%2C+Bush+decided+funding+would+only+support+research+on+a+small+number+of+existing+cell+lines+held+by+only+a+few+labs.%0D%0A%0D%0ANow%2C+stem+cell+research+is+at+virtual+standstill%2C+critics+say.+Researchers+complain+they+can%27t+get+the+cells+they+need+because+supplies+are+so+scarce.%0D%0A%0D%0AOne+of+those+critics+is+Stephen+Wakefield+from+Atlanta%2C+Georgia.+He+used+to+ski+and+run+marathons+--+but+not+anymore.%0D%0A%0D%0ASix+years+ago%2C+he+was+diagnosed+with+a+neuromuscular+disorder+similar+to+Lou+Gehrig%27s+disease.+There%27s+no+cure+and+it+will+only+get+worse.%0D%0A%0D%0AAt+first%2C+he+was+devastated.+But+then+--like+most+of+us+--+he+heard+about+embryonic+stem+cells.%0D%0A%0D%0AScientists+believe+stem+cells+can+be+turned+into+anything%2C+including+the+nerve+cells+Steve+needs+to+replace+the+ones+dying+in+his+body.%0D%0A%0D%0A%22I+believe+it+is+the+only+thing+on+the+horizon+that+will+provide+me+with+a+cure%2C%22+Wakefield+says+through+his+wife%2C+Pam%2C+who+must+translate+for+him.%0D%0A%0D%0ASo+what+do+Steve+and+Pam+think+of+President+Bush%27s+decision+a+year+ago%3F%0D%0A%0D%0AThat%27s+a+tricky+question.+They%27re+close+to+the+Bushes+and+campaigned+for+them+for+25+years.+A+lawyer%2C+Steve+even+served+in+the+first+Bush+administration+as+general+counsel+for+the+Department+of+Energy.%0D%0A%0D%0ABut+they+think+the+president+made+the+wrong+decision+and+now+feel+disappointed+and+frustrated.%0D%0A%0D%0A%22It+just+seems+like+it%27s+just+so+obvious+to+save+a+life%2C+to+save+many+lives+that+Bush+and+his+administration+would+want+to+go+forward+big+time+with+this+stem+cell+research%2C%22+says+Pam.%0D%0A%0D%0AThey+say+because+of+Bush%27s+decision%2C+stem+cell+research+hasn%27t+made+much+headway.+And+time+is+not+on+Steve%27s+side.%0D%0A%0D%0AIn+the+time+the+decision+was+announced%2C+%22You+have+gotten+worse%2C%22+Pam+says+to+Steve.+He+nods.%0D%0A%0D%0ACNN+spoke+with+several+stem+cell+researchers+who+said+they%27ve+tried%2C+but+for+various+legal+and+scientific+reasons%2C+can%27t+get+their+hands+on+the+cells+from+the+11+labs+that+are+approved+sources.%0D%0A%0D%0ADr.+Curt+Civin%2C+editor+of+the+journal+Stem+Cells+and+a+pediatric+cancer+specialist%2C+wants+stem+cells+to+rebuild+bone+marrow+for+young+cancer+patients.%0D%0A%0D%0ABut%2C+he+says%2C+embryonic+stem+cell+research+is+at+a+virtual+standstill.%0D%0A%0D%0A%22Certainly+in+our+lab+we+haven%27t+been+able+to+get+going%2C+even+on+studying+the+embryonic+stem+cells%2C+because+we+can%27t+get+our+hands+on+them%2C%22+says+Civin.%0D%0A%0D%0AThe+Department+of+Health+and+Human+Services+says+it%27s+trying+to+make+it+easier+for+researchers+like+Civin.%0D%0A%0D%0A%22It%27s+going+to+continue+to+ramp+up%2C+starting+slow%2C+but+continue+to+move+forward%2C%22+says+Tommy+Thompson%2C+HHS+Secretary.%0D%0A%0D%0AIn+fact%2C+the+government+just+recently+started+to+make+arrangements+to+distribute+stem+cells+to+researchers.%0D%0A%0D%0AAnd+Civin+says+he+hopes+to+get+some+of+those+stem+cells+into+his+lab+at+Johns+Hopkins+University+in+Baltimore%2C+Maryland.%0D%0A%0D%0ABut+the+Wakefields+can%27t+help+but+think+this+is+all+moving+too+slowly.%0D%0A%0D%0A%22Reagan+said%2C+%27Mr.+Gorbachev%2C+tear+down+these+walls%27+and+Bush+can+say%2C+%27tear+down+those+walls+of+disease%27.+And+he+could+be+such+a+hero+for+all+eternity%2C%22+says+Pam.%0D%0A%0D%0AHere+is+a+link+to+the+%3Ca+href%3D%27http%3A%2F%2Fwww.cnn.com%2F2002%2FHEALTH%2F08%2F09%2Fstem.cell.promise%2Findex.html%27%3Eoriginal+article%3C%2Fa%3E+on+%3Ci%3ECNN.com%3C%2Fi%3E.', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (36, 53, 0, '', 1, 20030530133344, 1, 20030530133344, 'On+this+page%2C+you+will+find+some+links+to+random+places+on+the+internet.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (37, 53, 1, 'Middlebury College', 1, 20030530133509, 1, 20030530133509, 'This+is+a+link+to+Middlebury+College%27s+web+site.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'link', 4, '0');
		INSERT INTO story VALUES (38, 53, 2, 'Google', 1, 20030530134609, 1, 20030530134609, 'The+best+search+engine+out+there%21', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'link', 5, '0');
		INSERT INTO story VALUES (39, 53, 3, 'Segue Project Page', 1, 20030530134928, 1, 20030530134928, 'News+and+information+about+the+developement+%3Cb%3ESegue%3C%2Fb%3E.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'link', 6, '0');
		INSERT INTO story VALUES (40, 54, 1, 'Life, The Universe, and Everything', 1, 20030530160309, 1, 20030530160309, 'This+quote%2C+from+a+book+by+Douglas+Adams%2C+asks+us+what+we+think+about+life.+What+do+%3Cb%3Eyou%3C%2Fb%3E+think+about+life', '', '0', 00000000000000, 00000000000000, '1', '', 'text', 'story', 7, '0');
		INSERT INTO story VALUES (41, 54, 2, 'Climates', 1, 20030530161012, 1, 20030530161012, 'Wise+men+have+said+that+many+people+prefer+warm+climates%2C+as+opposed+to+the+climate+found+in+Vermont.+Vermont+may+have+its+own+beauty%2C+but+many+don%27t+take+this+into+account.+What+do+you+think+of+this%3F', '', '0', 00000000000000, 00000000000000, '1', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (42, 55, 0, '', 1, 20030530161629, 1, 20030530161629, 'One+of+the+most+advanced+features+of+SitesDB+is+to+allow+site+creators+to+specify+editors+to+their+sites.+Editors+can+be+assigned+permissions+to+add%2C+edit+or+delete+content+from+certain+parts+of+a+site.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (43, 56, 0, '', 1, 20030530161756, 1, 20030530161756, '%3Cb%3ESegue%3C%2Fb%3E+also+allows+you+to+upload+files+and+allow+people+to+download+them.+This+is+useful+if+you+have+a+resume+or+syllabus+in+PDF%2C+RTF%2C+or+Word+format%2C+or+some+other+file+format+that+you+would+like+to+allow+visitors+to+download.', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', 0, '0');
		INSERT INTO story VALUES (44, 57, 0, 'Welcome to your new site!', 1, 20031110161133, 1, 20031110161133, '%0D%0A-+To+add+another+block+of+text+below+this+text%2C+click+the%0D%0A%22%3Cb%3E%2Badd+content%3C%2Fb%3E%22+button+below.%0D%0A%0D%0A-+To+edit+this+text%2C+click+the+%22%3Cb%3Eedit%3C%2Fb%3E%22+button+below.%0D%0A%0D%0A-+To+add+another+page+or+other+item+to+this+section%2C+click+on+the%0D%0A%22%3Cb%3E%2Badd+item%3C%2Fb%3E%22+button+to+the+left.%0D%0A%0D%0A-+Clicking+on+the+%22%3Cb%3Eedit%3C%2Fb%3E%22+button+next+to+a+page+or+section%0D%0Awill+allow+you+to+rename+it.%0D%0A%0D%0A-+To+add+a+new+section%2C+click+the+%22%3Cb%3E%2Badd+section%3C%2Fb%3E%22+button+above.+', '', '0', 00000000000000, 00000000000000, '0', '', 'text', 'story', NULL, '0');

		INSERT INTO discussion VALUES (1, 1, 20030530164042, 'I+Love+It', 'Well%2C+to+be+perfectly+frank%2C+I+love+it.+What+could+one+enjoy+more+than+eating%2C+sleeping%2C+and+having+___%3F', 40, 0, NULL);
		INSERT INTO discussion VALUES (2, 1, 20030530164121, 'Re%3A+I+Love+It', 'Inevitably%2C+though+life+is+suffering.+We+are+born%2C+which+is+quite+painful%2C+not+knowing+how+to+be%2C+throughout+our+lives+we+experience+pain+in+infinitely+subtle+varieties+resulting+in+sickness+and+before+we+have+fully+grasping+what+it+means+to+be+alive+we+begin+to+die%2C+losing+the+freshness+and+vitality+that+accompanies+new+experiences.', 40, 1, 1);
		INSERT INTO discussion VALUES (3, 1, 20030530164220, 'Life+Rocks', 'The+key+is+living+within+your+means+so+that+you+have+the+time+and+energy+to+experience+the+joys+of+family+and+comunity%3B+its+the+experiences+that+make+life+great%2C+not+the+objects+and+posessions+that+we+attempt+to+fill+our+lives+with.', 40, 2, NULL);
		INSERT INTO discussion VALUES (4, 1, 20030530164318, 'Not+all+that+glitters...', 'Remember%3A%0D%0A%0D%0ANot+all+that+glitters+is+gold%2C+but+it+does+contain+free+electric+charge+carriers.', 40, 3, NULL);
		INSERT INTO discussion VALUES (5, 1, 20030530164341, 'Re%3A+Not+all+that+glitters...', 'Good+point.+I+think+you%27ve+shown+your+knowledge+of+physics+quite+well.', 40, 4, 4);
		INSERT INTO discussion VALUES (6, 1, 20030530164354, 'Re%3A+Not+all+that+glitters...', 'Thanks.', 40, 5, 5);
		INSERT INTO discussion VALUES (7, 1, 20030530165911, 'Vermonters+Know', 'It+sure+is+confusing...+Let%27s+just+say+that+we+who+live+in+Vermont+truly+understand%2C+well%2C+everything.+People+who+move+to+places+near+the+equator+just+don%27t.+I+actually+think+it%27s+that+simple', 41, 0, NULL);
		INSERT INTO discussion VALUES (8, 1, 20030530165927, 'Re%3A+Vermonters+Know', 'I+disagree+with+the+simple+view+of+climate+espoused+above.+Climate%2C+like+all+phenomena%2C+is+relative.+What+is+cold+and+barren+to+one+is+warm+and+bliss+to+another.', 41, 1, 7);
		INSERT INTO discussion VALUES (9, 1, 20030530165955, 'Asteroids...', 'Well%2C+a+really+large+asteroid+hitting+the+earth+could+cause+enough+dust+to+fill+the+atmosphere+to+trigger+an+ice+age.+The+only+problem+is+the+collateral+damage.%0D%0A%0D%0AHow+bad+would+it+be+if+an+asteroid+hit+the+earth%3F+We+have+no+way+to+know+for+sure%2C+but+experiments+with+a+common+laboratory+frog+and+a+sledge+hammer+sugest+that+it+would+be+pretty+bad.', 41, 2, NULL)
	";
	$queryArray = explode(";",$query);
	foreach ($queryArray AS $query) {
//		print "<br>\"$query\"";
			db_query($query);
	}
}