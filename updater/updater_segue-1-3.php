<?

// ****************************************************
// run this script after runnig the SQL update queries!
// ****************************************************


include("config.inc.php");
include("functions.inc.php");
include("objects/objects.inc.php");
include("dbwrapper.inc.php");
include("permissions.inc.php");

global $dbuser, $dbpass, $dbdb, $dbhost;
db_connect($dbhost,$dbuser,$dbpass,$dbdb);

$destinationDB = $dbdb;
$sourceDB = "segue";
echo "<pre>";

// Set up the tables and copy most of the data
$query = "
		CREATE TABLE class (
		  class_id int(10) unsigned NOT NULL auto_increment,
		  class_external_id varchar(255) default NULL,
		  class_department varchar(255) default NULL,
		  class_number int(11) NOT NULL default '0',
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
		) TYPE=MyISAM;

";

    



/*############################################################################################################

############################################################################################################

# Now insert data from old database into new database

############################################################################################################

############################################################################################################*/





/*############################################################################################################

# user table

############################################################################################################*/

$query .= "
INSERT 

INTO ".$destinationDB.".user 

	(user_id, user_uname, user_pass, user_fname,

	 user_email, user_type, user_authtype)

SELECT 

	id, uname, pass, fname, email, type, status 

FROM 

	".$sourceDB.".users 

ORDER BY uname;

";





/*############################################################################################################

# slot table

############################################################################################################*/

$query .= "
INSERT 

INTO ".$destinationDB.".slot 

	(slot_name, FK_owner, FK_site, slot_type)

SELECT 

	name, user_id, sites.id, sites.type 

FROM 

	".$sourceDB.".sites 

		LEFT JOIN

	".$destinationDB.".user

		ON addedby = user_uname

ORDER BY name;
";





/*############################################################################################################

# site table

############################################################################################################*/

$query .= "
INSERT

INTO ".$destinationDB.".site

	(site_id, site_title, site_theme, site_themesettings, site_header, 

	 site_footer, FK_updatedby, site_updated_tstamp, FK_createdby, site_created_tstamp, site_active,     	 site_activate_tstamp, site_deactivate_tstamp, site_listed)

SELECT 

	".$sourceDB.".sites.id, title, theme, themesettings, header, footer, u1.user_id, 

	editedtimestamp, u2.user_id, addedtimestamp, CONV(active,2,2), 

	FROM_UNIXTIME(UNIX_TIMESTAMP(activatedate)), 

	FROM_UNIXTIME(UNIX_TIMESTAMP(deactivatedate)), CONV(listed,2,2)

FROM 

	".$sourceDB.".sites

		LEFT JOIN

	".$destinationDB.".user AS u1

		ON editedby = u1.user_uname

		LEFT JOIN

	".$destinationDB.".user AS u2

		ON addedby = u2.user_uname

ORDER BY

	".$sourceDB.".sites.name;



UPDATE

	".$destinationDB.".site

SET

	FK_updatedby = FK_createdby

WHERE

	FK_updatedby = 0;

";



/*############################################################################################################

# section table

############################################################################################################*/

$query .= "
INSERT

INTO ".$destinationDB.".section

	(section_id, FK_site, section_order, section_title, FK_updatedby, section_updated_tstamp,

	 FK_createdby, section_created_tstamp, section_active, section_activate_tstamp, 

	 section_deactivate_tstamp, section_locked, section_display_type)

SELECT 

	sections.id, slot.FK_site, sections.id, title,   u1.user_id, editedtimestamp, u2.user_id,

	addedtimestamp, CONV(active,2,2), FROM_UNIXTIME(UNIX_TIMESTAMP(activatedate)), 

	FROM_UNIXTIME(UNIX_TIMESTAMP(deactivatedate)), CONV(locked,2,2), IF(sections.type = 'url', 'link', sections.type)

FROM 

	".$sourceDB.".sections

		INNER JOIN

	".$destinationDB.".slot

		ON slot.slot_name = sections.site_id

		LEFT JOIN

	".$destinationDB.".user AS u1

		ON editedby = u1.user_uname

		LEFT JOIN

	".$destinationDB.".user AS u2

		ON addedby = u2.user_uname

ORDER BY

	".$sourceDB.".sections.site_id;



UPDATE

	".$destinationDB.".section

SET

	FK_updatedby = FK_createdby

WHERE

	FK_updatedby = 0;

";



/*############################################################################################################

# page table

############################################################################################################*/

$query .= "
INSERT

INTO ".$destinationDB.".page

	(page_id, FK_section, page_order, page_title, FK_updatedby, page_updated_tstamp, FK_createdby, 		 		 page_created_tstamp, page_active, page_activate_tstamp, page_deactivate_tstamp, page_show_creator, 	 		 page_show_date, page_show_hr, page_display_type, page_story_order, page_archiveby, page_locked, page_ediscussion)

SELECT 

	pages.id, section_id, pages.id, title,  u1.user_id, editedtimestamp, u2.user_id,

	addedtimestamp, CONV(active,2,2), 

	FROM_UNIXTIME(UNIX_TIMESTAMP(activatedate)), FROM_UNIXTIME(UNIX_TIMESTAMP(deactivatedate)),

	CONV(showcreator,2,2), CONV(showdate,2,2), CONV(showhr,2,2), IF(pages.type = 'url', 'link', pages.type), storyorder, archiveby,

	CONV(locked,2,2), CONV(ediscussion,2,2)

FROM 

	".$sourceDB.".pages

		LEFT JOIN

	".$destinationDB.".user AS u1

		ON editedby = u1.user_uname

		LEFT JOIN

	".$destinationDB.".user AS u2

		ON addedby = u2.user_uname

ORDER BY

	".$sourceDB.".pages.section_id;



UPDATE

	".$destinationDB.".page

SET

	FK_updatedby = FK_createdby

WHERE

	FK_updatedby = 0;
";




/*############################################################################################################

# story table

############################################################################################################*/

$query .= "
INSERT

INTO ".$destinationDB.".story

	(story_id, FK_page, story_order, story_title, FK_updatedby, story_updated_tstamp, FK_createdby, 

	 story_created_tstamp, story_text_short, story_text_long, story_active, story_activate_tstamp, 

	 story_deactivate_tstamp, story_discussable, story_category, story_text_type, story_display_type, story_locked)

SELECT 

	stories.id, page_id, stories.id, title, u1.user_id, editedtimestamp, u2.user_id, addedtimestamp, shorttext,

	longertext, CONV(active,2,2), FROM_UNIXTIME(UNIX_TIMESTAMP(activatedate)), 

	FROM_UNIXTIME(UNIX_TIMESTAMP(deactivatedate)), CONV(discuss,2,2), category, texttype, IF(stories.type = 'url', 'link', stories.type), CONV(locked,2,2)

FROM 

	".$sourceDB.".stories

		LEFT JOIN

	".$destinationDB.".user AS u1

		ON editedby = u1.user_uname

		LEFT JOIN

	".$destinationDB.".user AS u2

		ON addedby = u2.user_uname

ORDER BY

	".$sourceDB.".stories.page_id;



UPDATE

	".$destinationDB.".story

SET

	FK_updatedby = FK_createdby

WHERE

	FK_updatedby = 0;

";



/*############################################################################################################

# permissions & site editors

############################################################################################################*/

$query .= "
INSERT

INTO ".$destinationDB.".site_editors

	(FK_site, FK_editor, site_editors_type)

SELECT

	id, NULL, 'everyone'

FROM

	".$sourceDB.".sites

ORDER BY

	id;



INSERT

INTO ".$destinationDB.".site_editors

	(FK_site, FK_editor, site_editors_type)

SELECT

	id, NULL, 'institute'

FROM

	".$sourceDB.".sites

ORDER BY

	id;



INSERT

INTO ".$destinationDB.".permission

	(FK_editor, permission_editor_type, FK_scope_id, permission_scope_type, permission_value)

SELECT

	NULL, 'everyone', id, 'site', 'v'

FROM

	".$sourceDB.".sites

WHERE

	viewpermissions = 'anyone'

ORDER BY

	id;



INSERT

INTO ".$destinationDB.".permission

	(FK_editor, permission_editor_type, FK_scope_id, permission_scope_type, permission_value)

SELECT

	NULL, 'institute', id, 'site', 'v'

FROM

	".$sourceDB.".sites

WHERE

	viewpermissions = 'midd'

ORDER BY

	id;

";





/*############################################################################################################

# media items: images & files

############################################################################################################*/

$query .= " 

INSERT

INTO ".$destinationDB.".media

	(media_id, FK_site, FK_createdby, media_tag, media_location, media_type, FK_updatedby, media_size)

SELECT

	id, FK_site, user_id, name, 'local', type, user_id, size

FROM

	".$sourceDB.".media

		INNER JOIN

	".$destinationDB.".slot

		ON slot_name = site_id

		LEFT JOIN

	".$destinationDB.".user

		ON addedby = user_uname
";

	$queryArray = explode(";",$query);
	foreach ($queryArray AS $query) {
//		print "<br>\"$query\"";
			db_query($query);
	}



// ************************************************************************************************************************************************
// put links in media table for sections
// ************************************************************************************************************************************************

$query = "
SELECT
	FK_site, FK_createdby, url as media_tag, 'remote' as media_location, FK_updatedby, id AS section_id
FROM
	".$sourceDB.".sections
		INNER JOIN
	".$destinationDB.".section
		ON id = section_id
WHERE
	type = 'url'
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	// first see if the link is already in the media table
	$query = "
SELECT
	media_id
FROM ".$destinationDB.".media
WHERE
	FK_site = ".$a[FK_site]." AND
	FK_createdby = ".$a[FK_createdby]." AND
	media_tag = '".$a[media_tag]."' AND
	media_location = '".$a[media_location]."'";

	$r1 = db_query($query);
	
	if ($a1 = db_fetch_assoc($r1)) {
		$media_id = $a1[media_id];
	}
	else {
		$query = "
	INSERT
	INTO ".$destinationDB.".media
	SET
		FK_site = ".$a[FK_site].", 
		FK_createdby = ".$a[FK_createdby].", 
		media_tag = '".$a[media_tag]."', 
		media_location = '".$a[media_location]."', 
		FK_updatedby = ".$a[FK_updatedby];
	
		db_query($query);
	
		$media_id = lastid();
	}

	$query = "
UPDATE
	".$destinationDB.".section
SET
	FK_media = $media_id
WHERE
	section_id = ".$a[section_id];

	db_query($query);
}



// ************************************************************************************************************************************************
// put links in media table for pages
// ************************************************************************************************************************************************

$query = "
SELECT
	FK_site, page.FK_createdby, url as media_tag, 'remote' as media_location, page.FK_updatedby, id AS page_id
FROM
	".$sourceDB.".pages
		INNER JOIN
	".$destinationDB.".page
		ON id = page_id
		INNER JOIN
	".$destinationDB.".section
		ON FK_section = section.section_id
WHERE
	type = 'url'
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	// first see if the link is already in the media table
	$query = "
SELECT
	media_id
FROM ".$destinationDB.".media
WHERE
	FK_site = ".$a[FK_site]." AND
	FK_createdby = ".$a[FK_createdby]." AND
	media_tag = '".$a[media_tag]."' AND
	media_location = '".$a[media_location]."'";

	$r1 = db_query($query);
	
	if ($a1 = db_fetch_assoc($r1)) {
		$media_id = $a1[media_id];
	}
	else {
		$query = "
	INSERT
	INTO ".$destinationDB.".media
	SET
		FK_site = ".$a[FK_site].", 
		FK_createdby = ".$a[FK_createdby].", 
		media_tag = '".$a[media_tag]."', 
		media_location = '".$a[media_location]."', 
		FK_updatedby = ".$a[FK_updatedby];
	
		db_query($query);
	
		$media_id = lastid();
	}


	$query = "
UPDATE
	".$destinationDB.".page
SET
	FK_media = $media_id
WHERE
	page_id = ".$a[page_id];

	db_query($query);
}


// ************************************************************************************************************************************************
// put links in media table for stories
// ************************************************************************************************************************************************

$query = "
SELECT
	FK_site, story.FK_createdby, url as media_tag, 'remote' as media_location, story.FK_updatedby, id AS story_id
FROM
	".$sourceDB.".stories
		INNER JOIN
	".$destinationDB.".story
		ON id = story_id
		INNER JOIN
	".$destinationDB.".page
		ON FK_page = page.page_id
		INNER JOIN
	".$destinationDB.".section
		ON FK_section = section.section_id
WHERE
	type = 'link'
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	// first see if the link is already in the media table
	$query = "
SELECT
	media_id
FROM ".$destinationDB.".media
WHERE
	FK_site = ".$a[FK_site]." AND
	FK_createdby = ".$a[FK_createdby]." AND
	media_tag = '".$a[media_tag]."' AND
	media_location = '".$a[media_location]."'";

	$r1 = db_query($query);
	
	if ($a1 = db_fetch_assoc($r1)) {
		$media_id = $a1[media_id];
	}
	else {
		$query = "
	INSERT
	INTO ".$destinationDB.".media
	SET
		FK_site = ".$a[FK_site].", 
		FK_createdby = ".$a[FK_createdby].", 
		media_tag = '".$a[media_tag]."', 
		media_location = '".$a[media_location]."', 
		FK_updatedby = ".$a[FK_updatedby];
	
		db_query($query);
	
		$media_id = lastid();
	}


	$query = "
UPDATE
	".$destinationDB.".story
SET
	FK_media = $media_id
WHERE
	story_id = ".$a[story_id];

	db_query($query);
}



// ************************************************************************************************************************************************
// fix order of sections
// ************************************************************************************************************************************************

$query = "
SELECT
	id, sections
FROM
	".$sourceDB.".sites
ORDER BY
	sites.id
";	

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {

	$site_id = $a[id];
	$sections = decode_array($a[sections]);
	foreach ($sections as $section_order => $section_id) {
		$query = "
UPDATE
	".$destinationDB.".section
SET
	section_order = $section_order
WHERE
	section_id = $section_id
";
		db_query($query);
		echo "<br><b>Site: $site_id, Section: $section_id, Order: $section_order</b>";	
	}
}

// ************************************************************************************************************************************************
// fix order of pages
// ************************************************************************************************************************************************

$query = "
SELECT
	pages
FROM
	".$sourceDB.".sections
ORDER BY
	sections.id
";	

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {

	$pages = decode_array($a[pages]);
	foreach ($pages as $page_order => $page_id) {
		
		$query = "
UPDATE
	".$destinationDB.".page
SET
	page_order = $page_order
WHERE
	page_id = $page_id
";
		db_query($query);
		echo "<br>Page: $page_id, Order: $page_order";	
	}
}

	

// ************************************************************************************************************************************************
// fix order of stories
// ************************************************************************************************************************************************

$query = "
SELECT
	stories
FROM
	".$sourceDB.".pages
ORDER BY
	pages.id
";	

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {

	$stories = decode_array($a[stories]);
	foreach ($stories as $story_order => $story_id) {
		
		$query = "
UPDATE
	".$destinationDB.".story
SET
	story_order = $story_order
WHERE
	story_id = $story_id
";
		db_query($query);
		echo "<br>Story: $story_id, Order: $story_order";	
	}
}

// ************************************************************************************************************************************************
// import discussions
// ************************************************************************************************************************************************

//$query = "DELETE FROM ".$destinationDB.".discussion";
//db_query($query);

$query = "
SELECT
	id, discussions
FROM
	".$sourceDB.".stories
ORDER BY
	id
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	
	$discussions = decode_array($a[discussions]);
	if (count($discussions) != 0) {
//		echo "<br> Story: ".$a[id].", Discussions: <br> ";
//		print_r($discussions);
		foreach($discussions as $order => $id) {
			$query = "
INSERT
INTO ".$destinationDB.".discussion
	(FK_author, discussion_tstamp, discussion_subject, discussion_content , FK_story, discussion_order, FK_parent)
SELECT
	user_id, discussions.timestamp, '' as subject	, content, ".$a[id]." as story, $order as ord, NULL as parent	
FROM
	".$sourceDB.".discussions
		INNER JOIN
	".$destinationDB.".user
		ON discussions.author = user_uname
WHERE
	id = $id
";
		
//			echo $query;
			db_query($query);
			echo "<br>In story #".$a[id].", discussion $order -> $id<br>";
		}
	}	
}





// ************************************************************************************************************************************************
// import site editors
// ************************************************************************************************************************************************

$query = "
SELECT
	id, permissions
FROM
	".$sourceDB.".sites
ORDER BY
	id
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	$permissions = decode_array($a[permissions]);
//	if (count($permissions)) print_r($permissions);
	foreach ($permissions as $editor => $perms) {
		// put editors

		$query = "
INSERT
INTO site_editors
	(FK_site, FK_editor, site_editors_type)
SELECT
	".$a[id]." AS site_id, user_id, 'user' as editor_type
FROM
	user
WHERE
	user_uname = '$editor'	
";
		db_query($query);

	}

}
	



// ************************************************************************************************************************************************
// now import old permissions. I should get a raise for this stuff, it's pretty complicated ya know ;)
// ************************************************************************************************************************************************

// ************************************************************************************************************************************************
// process site permissions
// ************************************************************************************************************************************************

		
		$query = "
SELECT
	id, permissions
FROM
	".$sourceDB.".sites
";

		$r = db_query($query);
		
		while ($a = db_fetch_assoc($r)) {
			$site_id = $a[id];
			$permissions = decode_array($a[permissions]);
			if (is_array($permissions)) foreach($permissions as $editor => $perms) {
				// build a permission string for $perms, i.e. smth in the form of "'a','e','d'"
				$p = "";
				if ($perms[ADD]) $p.="a,";
				if ($perms[EDIT]) $p.="e,";
				if ($perms[DELETE]) $p.="d,";
				
				if ($p) $p = substr($p, 0, strlen($p)-1); // strip last comma from the end of a string 

				$query ="
INSERT
INTO permission
	(FK_editor , permission_editor_type , FK_scope_id , permission_scope_type , permission_value)
SELECT
	user_id, 'user', $site_id, 'site', '$p'
FROM
	user
WHERE
	user_uname = '$editor'
";

				echo "Editor: $editor, Permissions: $p<br>";
				if ($p) db_query($query);
			
			}
		}


// ************************************************************************************************************************************************
// process section permissions
// ************************************************************************************************************************************************
		
		$query = "
SELECT
	id, site_id, permissions
FROM
	".$sourceDB.".sections
";

		$r = db_query($query);
		
		while ($a = db_fetch_assoc($r)) {
			$section_id = $a[id];
			$permissions2 = decode_array($a[permissions]);
				// now get permissions of the parent and see if we are just inheriting or are actually introducing smth new
				$query = "
SELECT
	id, permissions
FROM
	".$sourceDB.".sites
WHERE
	name = '".$a[site_id]."'";
			$r1 = db_query($query);
			$a1 = db_fetch_assoc($r1);
			$permissions1 = decode_array($a1[permissions]);

			if (is_array($permissions2)) foreach($permissions2 as $editor => $p2) {
	
				if (is_array($permissions1[$editor])) 
					$p1 = $permissions1[$editor];
				// for some reason the editor is in the the child but not in the parent!!! put them in site_editors damnit!
				else {
					$query = "
INSERT
INTO site_editors
	(FK_site, FK_editor, site_editors_type)
SELECT
	".$a1[id]." AS site_id, user_id, 'user' as editor_type
FROM
	user
WHERE
	user_uname = '$editor'	
";
					db_query($query);
					$p1 = array();
					$p1[ADD] = 0;
					$p1[EDIT] = 0;
					$p1[DELETE] = 0;
				}
					
				// note that if a certain permission is set in $p1, it is impossible that the same permission is not set in $p2 (because $p2 inherits $p1's permissions)
				// thus, there are 3 possibilities:
				// 1) $p1 - SET,   $p2 - SET   
				// 2) $p1 - UNSET, $p2 - SET
				// 3) $p1 - UNSET, $p2 - UNSET

				$p_new = array();

				foreach ($p1 as $key => $value)
					// in case 1) and 3) $p2 inherits $p1's permission
					if ($p1[$key] || (!$p1[$key] && !$p2[$key])) {
						$p_new[$key] = 0;
					}
					// in case 2), $p2 adds a new permission
					else {
						$p_new[$key] = 1;
					}

				// convert $p_new to a "'a','v',..." format.
				$p_new_str = "";
				if ($p_new[ADD]) $p_new_str.="a,";
				if ($p_new[EDIT]) $p_new_str.="e,";
				if ($p_new[DELETE]) $p_new_str.="d,";
				
				if ($p_new_str) $p_new_str = substr($p_new_str, 0, strlen($p_new_str)-1); // strip last comma from the end of a string 

				$query ="
INSERT
INTO permission
	(FK_editor , permission_editor_type , FK_scope_id , permission_scope_type , permission_value)
SELECT
	user_id, 'user', $section_id, 'section', '$p_new_str'
FROM
	user
WHERE
	user_uname = '$editor'
";

				echo "<br><b>Section: $section_id, Editor: $editor, Permissions: $p_new_str</b><br>";
				if ($p_new_str) db_query($query);
			
			}
		}




// ************************************************************************************************************************************************
// process page permissions
// ************************************************************************************************************************************************


		$query = "
SELECT
	id, section_id, permissions
FROM
	".$sourceDB.".pages
";

		$r = db_query($query);
		
		while ($a = db_fetch_assoc($r)) {
			$page_id = $a[id];
			$permissions2 = decode_array($a[permissions]);
				// now get permissions of the parent and see if we are just inheriting or are actually introducing smth new
				$query = "
SELECT
	site_id, id, permissions
FROM
	".$sourceDB.".sections
WHERE
	id = ".$a[section_id];
	
			$r1 = db_query($query);
			$a1 = db_fetch_assoc($r1);
			$permissions1 = decode_array($a1[permissions]);

			if (is_array($permissions2)) foreach($permissions2 as $editor => $p2) {
	
				if (is_array($permissions1[$editor])) 
					$p1 = $permissions1[$editor];
				// for some reason the editor is in the the child but not in the parent!!! put them in site_editors damnit!
				else {
					$query = "
INSERT
INTO site_editors
	(FK_site, FK_editor, site_editors_type)
SELECT
	FK_site AS site_id, user_id, 'user' as editor_type
FROM
	user, slot
WHERE
	user_uname = '$editor' AND slot_name = '".$a1[site_id]."'
";
					db_query($query);
					$p1 = array();
					$p1[ADD] = 0;
					$p1[EDIT] = 0;
					$p1[DELETE] = 0;
				}
					
				// note that if a certain permission is set in $p1, it is impossible that the same permission is not set in $p2 (because $p2 inherits $p1's permissions)
				// thus, there are 3 possibilities:
				// 1) $p1 - SET,   $p2 - SET   
				// 2) $p1 - UNSET, $p2 - SET
				// 3) $p1 - UNSET, $p2 - UNSET

				$p_new = array();

				foreach ($p1 as $key => $value)
					// in case 1) and 3) $p2 inherits $p1's permission
					if ($p1[$key] || (!$p1[$key] && !$p2[$key])) {
						$p_new[$key] = 0;
					}
					// in case 2), $p2 adds a new permission
					else {
						$p_new[$key] = 1;
					}

				// convert $p_new to a "'a','v',..." format.
				$p_new_str = "";
				if ($p_new[ADD]) $p_new_str.="a,";
				if ($p_new[EDIT]) $p_new_str.="e,";
				if ($p_new[DELETE]) $p_new_str.="d,";
				
				if ($p_new_str) $p_new_str = substr($p_new_str, 0, strlen($p_new_str)-1); // strip last comma from the end of a string 

				$query ="
INSERT
INTO permission
	(FK_editor , permission_editor_type , FK_scope_id , permission_scope_type , permission_value)
SELECT
	user_id, 'user', $page_id, 'page', '$p_new_str'
FROM
	user
WHERE
	user_uname = '$editor'
";

				echo "<br><b>Page: $page_id, Editor: $editor, Permissions: $p_new_str</b><br>";
				if ($p_new_str) db_query($query);
			
			}
		}




// ************************************************************************************************************************************************
// process story permissions
// ************************************************************************************************************************************************


		$query = "
SELECT
	id, page_id, permissions
FROM
	".$sourceDB.".stories
";

		$r = db_query($query);
		
		while ($a = db_fetch_assoc($r)) {
			$story_id = $a[id];
			$permissions2 = decode_array($a[permissions]);
				// now get permissions of the parent and see if we are just inheriting or are actually introducing smth new
				$query = "
SELECT
	site_id, id, permissions
FROM
	".$sourceDB.".pages
WHERE
	id = ".$a[page_id];
	
			$r1 = db_query($query);
			$a1 = db_fetch_assoc($r1);
			$permissions1 = decode_array($a1[permissions]);

			if (is_array($permissions2)) foreach($permissions2 as $editor => $p2) {
	
				if (is_array($permissions1[$editor])) 
					$p1 = $permissions1[$editor];
				// for some reason the editor is in the the child but not in the parent!!! put them in site_editors damnit!
				else {
					$query = "
INSERT
INTO site_editors
	(FK_site, FK_editor, site_editors_type)
SELECT
	FK_site AS site_id, user_id, 'user' as editor_type
FROM
	user, slot
WHERE
	user_uname = '$editor' AND slot_name = '".$a1[site_id]."'
";
					db_query($query);
					$p1 = array();
					$p1[ADD] = 0;
					$p1[EDIT] = 0;
					$p1[DELETE] = 0;
				}
					
				// note that if a certain permission is set in $p1, it is impossible that the same permission is not set in $p2 (because $p2 inherits $p1's permissions)
				// thus, there are 3 possibilities:
				// 1) $p1 - SET,   $p2 - SET   
				// 2) $p1 - UNSET, $p2 - SET
				// 3) $p1 - UNSET, $p2 - UNSET

				$p_new = array();

				foreach ($p1 as $key => $value)
					// in case 1) and 3) $p2 inherits $p1's permission
					if ($p1[$key] || (!$p1[$key] && !$p2[$key])) {
						$p_new[$key] = 0;
					}
					// in case 2), $p2 adds a new permission
					else {
						$p_new[$key] = 1;
					}

				// convert $p_new to a "'a','v',..." format.
				$p_new_str = "";
				if ($p_new[ADD]) $p_new_str.="a,";
				if ($p_new[EDIT]) $p_new_str.="e,";
				if ($p_new[DELETE]) $p_new_str.="d,";
				
				if ($p_new_str) $p_new_str = substr($p_new_str, 0, strlen($p_new_str)-1); // strip last comma from the end of a string 

				$query ="
INSERT
INTO permission
	(FK_editor , permission_editor_type , FK_scope_id , permission_scope_type , permission_value)
SELECT
	user_id, 'user', $story_id, 'story', '$p_new_str'
FROM
	user
WHERE
	user_uname = '$editor'
";

				echo "<br><b>Story: $story_id, Editor: $editor, Permissions: $p_new_str</b><br>";
				if ($p_new_str) db_query($query);
			
			}
		}






echo "</pre>";
?>
