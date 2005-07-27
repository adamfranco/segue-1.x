<? /* $Id$ */

class site extends segue {
	var $canview; // an array of editors that can view this site
				  // (i.e. have view permissions somewhere in the hierarchy)
	var $owner, $owneremail, $ownerfname;
	var $title;
	var $sections;
	var $name;
	var $_allfields = array("name","title","theme","themesettings","header","footer",
						"addedby","addedbyfull", "editedby","editedtimestamp","addedtimestamp",
						"activatedate","deactivatedate","active","sections",
						"listed","type");

	// fields listed in $_datafields are stored in the database.
	// the first element is the table join syntax required to pull the data.
	// the second element is an array of the database fields we will be selecting
	// the third element is the database field by which we will sort
	
	var $_datafields = array(
		"id" => array(
			"site",
			array("site_id"),
			"site_id"
		),
		"name" => array(
			"site
				INNER JOIN
			slot
				ON site_id = FK_site",
			array("slot_name"),
			"site_id"
		),
		"type" => array(
			"site
				INNER JOIN
			slot
				ON site_id = FK_site",
			array("slot_type"),
			"site_id"
		),
		"title" => array(
			"site",
			array("site_title"),
			"site_id"
		),
		"activatedate" => array(
			"site",
			array("DATE_FORMAT(site_activate_tstamp, '%Y-%m-%d')"),
			"site_id"
		),
		"deactivatedate" => array(
			"site",
			array("DATE_FORMAT(site_deactivate_tstamp, '%Y-%m-%d')"),
			"site_id"
		),
		"active" => array(
			"site",
			array("site_active"),
			"site_id"
		),
		"listed" => array(
			"site",
			array("site_listed"),
			"site_id"
		),
		"theme" => array(
			"site",
			array("site_theme"),
			"site_id"
		),
		"themesettings" => array(
			"site",
			array("site_themesettings"),
			"site_id"
		),
		"header" => array(
			"site",
			array("site_header"),
			"site_id"
		),
		"footer" => array(
			"site",
			array("site_footer"),
			"site_id"
		),
		"editedby" => array(
			"site
				INNER JOIN
			user
				ON FK_updatedby = user_id",
			array("user_uname"),
			"site_id"
		),
		"editedtimestamp" => array(
			"site",
			array("site_updated_tstamp"),
			"site_id"
		),
		"addedby" => array(
			"site
				INNER JOIN
			 user
			 	ON FK_createdby = user_id",
			array("user_uname"),
			"site_id"
		),
		"addedbyfull" => array(
			"site
				INNER JOIN
			 user
			 	ON FK_createdby = user_id",
			array("user_fname"),
			"site_id"
		),
		"addedtimestamp" => array(
			"site",
			array("site_created_tstamp"),
			"site_id"
		),
		"sections" => array(
			"site
				INNER JOIN
			section
				ON site_id = FK_site",
			array("section_id"),
			"section_order"
		)
	);
	var $_table = "site";
	
	
	function site($name) {
		// find if a site with this name already exists in the databse, and if yes, get site_id
		global $dbuser, $dbpass, $dbdb, $dbhost;
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		
		$q = "SELECT site_id, site_title, user_email, user_uname, user_fname FROM site INNER JOIN slot ON site_id = FK_site AND slot_name = '$name' INNER JOIN user ON user_id = FK_owner";
		// echo $q;
		$r = db_query($q);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id = $a[site_id];
			$this->owner = $a[user_uname];
			$this->ownerfname = $a[user_fname];
			$this->owneremail = $a[user_email];
			$this->title = $a[site_title];
		} else
			$this->site_does_not_exist = true;
			
		$this->name = $name;
		$this->owning_site = $name;
		$this->owningSiteObj =& $this;
		$this->fetchedup = 1;		
		$this->sections = array();
		$this->data = array();
		
		// initialize the data array
		$this->data[name] = $name;
		$this->data[type] = "personal";
		$this->data[title] = "";
		$this->data[activatedate] = "0000-00-00";
		$this->data[deactivatedate] = "0000-00-00";
		$this->data[active] = 1;
		$this->data[listed] = 1;
		$this->data[theme] = "minimal";
		$this->data[themesettings] = "";
		$this->data[header] = "";
		$this->data[footer] = "";
		$this->data[sections] = array();
//		$this->data[sections][] = 'FUCKFUCKFUCK';
	}
	
	// ************************************************************************************************
	// ************************************************************************************************
	// description: just look at the function name
	// THIS IS A BAD ASS FUNCTION. BUT IS IS FAST!!!
	// @param $section_id, $page_id If these are specified the function will fetch along them
	// ************************************************************************************************
	// ************************************************************************************************
	function fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen($_section_id = 0, $_page_id = 0, $quick = false) {
		if ($this->site_does_not_exist) return false;
		if ($this->fetched_forever_and_ever) return $this->id;
	
		// no $full or $force here, always fetch everything, be strong and stubborn damnit!

		// connect to db and initialize data array
		global $dbuser, $dbpass, $dbdb, $dbhost;
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		
		// delete temporary tables if they already exist
		$query = "DROP TABLE IF EXISTS t_sites";
		db_query($query);
		$query = "DROP TABLE IF EXISTS t_sections";
		db_query($query);
		$query = "DROP TABLE IF EXISTS t_pages";
		db_query($query);
		$query = "DROP TABLE IF EXISTS t_stories";
		db_query($query);
		
		// now, create the temporary tables. each table stores all siteunit ids for this site.
		
		// all stories for this site
		$query = "
CREATE TEMPORARY TABLE t_stories(
	UNIQUE uniq (site_id,section_id,page_id,story_id),
	KEY site_id (site_id),
	KEY section_id (section_id),
	KEY page_id (page_id),
	KEY story_id (story_id)
) TYPE=MyISAM
SELECT
	site_id, section_id, page_id, story_id, section_order, page_order, story_order
FROM
	site
		LEFT JOIN
	section ON FK_site = site_id
		LEFT JOIN
	page ON FK_section = section_id
		LEFT JOIN
	story ON FK_page = page_id
WHERE
	site_id = ".$this->id." 
";		
		db_query($query);

		// all pages for this site
		$query = "
CREATE TEMPORARY TABLE t_pages (
	UNIQUE uniq (site_id, section_id, page_id),
	KEY site_id (site_id),
	KEY section_id (section_id),
	KEY page_id (page_id)
)
SELECT
	DISTINCT site_id, section_id, page_id, section_order, page_order
FROM
	t_stories
";		
		db_query($query);

		// all sections for this site
		$query = "
CREATE TEMPORARY TABLE t_sections (
	UNIQUE uniq (site_id, section_id),
	KEY site_id (site_id),
	KEY section_id (section_id)
)
SELECT
	DISTINCT site_id, section_id, section_order
FROM
	t_pages
";
		db_query($query);

		// all sites for this site, i.e. just this site
		$query = "
CREATE TEMPORARY TABLE t_sites (
	UNIQUE uniq (site_id),
	KEY site_id (site_id)
)
SELECT
	DISTINCT site_id
FROM
	t_sections
";
		db_query($query);
	
		// create the object hierarchy

		$this->data = array();
		
		$query = "SELECT site_id, section_id FROM t_sections ORDER BY section_order";
		$r = db_query($query);
		while ($a = db_fetch_assoc($r))
			if ($a[section_id] != null) {
				$section =& new section($this->name,$a[section_id],$this);
				$this->sections[$a[section_id]]  =& $section;
				$this->data[sections][]  = $a[section_id];
				$this->fetched[sections] = 1;
			}
		
		$query = "SELECT site_id, section_id, page_id FROM t_pages ORDER BY	page_order";
		$r = db_query($query);
		while ($a = db_fetch_assoc($r))
			if ($a[section_id] != null && $a[page_id] != null)  {
				$section =& $this->sections[$a[section_id]];
				$page =& new page($this->name,$a[section_id],$a[page_id],$section);
				$section->pages[$a[page_id]]  =& $page;
				$section->data[pages][]  = $a[page_id];
				$section->fetched[pages]  = 1;
			}

		$query = "SELECT site_id, section_id, page_id, story_id FROM t_stories ORDER BY	story_order";
		$r = db_query($query);
		while ($a = db_fetch_assoc($r))
			if ($a[section_id] != null && $a[page_id] != null && $a[story_id] != null)  {
				$section =& $this->sections[$a[section_id]];
				$page =& $section->pages[$a[page_id]];
				$story =& new story($this->name,$a[section_id],$a[page_id],$a[story_id],$page);
				$page->stories[$a[story_id]]  =& $story;
				$page->data[stories][]  = $a[story_id];
				$page->fetched[stories]  = 1;
			}
		
		// first, fetch the site
		$query = "
SELECT  site_title AS title, DATE_FORMAT(site_activate_tstamp, '%Y-%m-%d') AS activatedate, DATE_FORMAT(site_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
		site_active AS active, site_listed AS listed, ".
		(($quick) ? "" : "site_theme AS theme, site_themesettings AS themesettings, site_header AS header, site_footer AS footer, ")
		."site_updated_tstamp AS editedtimestamp, site_created_tstamp AS addedtimestamp,
		user_createdby.user_uname AS addedby, user_updatedby.user_uname AS editedby, slot_name as name, slot_type AS type
FROM 
	t_sites
		INNER JOIN
	site
		ON t_sites.site_id = site.site_id
		INNER JOIN
	user AS user_createdby
		ON FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON FK_updatedby = user_updatedby.user_id
		INNER JOIN
	slot
		ON site.site_id = slot.FK_site
";
		
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		array_change_key_case($a); // make all keys lower case
		// for each field returned by the query
		foreach ($a as $field => $value)
			// make sure we have defined this field in the _allfields array
			if (in_array($field,$this->_allfields)) {
				// decode if necessary
				if (in_array($field,$this->_encode)) 
					$value = stripslashes(urldecode($value));
				$this->data[$field] = $value;
				$this->fetched[$field] = 1;
			}
			else
				echo "ERROR: field $field not in _allfields!!!<br />";
		$this->fetcheddown = 1;
		$this->fetched_forever_and_ever = 1;
					
		// now, create section objects and fetch them
		$query = "
SELECT  
	section.section_id AS section_id".
	(($quick) ? " " : 	
	", section_display_type AS type, section_title AS title, DATE_FORMAT(section_activate_tstamp, '%Y-%m-%d') AS activatedate, DATE_FORMAT(section_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
	section_active AS active, section_locked AS locked, section_updated_tstamp AS editedtimestamp,
	section_created_tstamp AS addedtimestamp,
	user_createdby.user_uname AS addedby, user_updatedby.user_uname AS editedby, '".$this->name."' as site_id,
	media_tag AS url ")
."FROM 
	t_sections
		INNER JOIN
	section
		ON t_sections.section_id = section.section_id
		INNER JOIN
	user AS user_createdby
		ON section.FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON section.FK_updatedby = user_updatedby.user_id
		LEFT JOIN
	media
		ON FK_media = media_id
";	
		$r = db_query($query);
		while ($a = db_fetch_assoc($r)) {
			$section =& $this->sections[$a[section_id]];
			foreach ($a as $field => $value)
				// make sure we have defined this field in the _allfields array
				if ($field == 'section_id' || in_array($field,$section->_allfields)) {
					// decode if necessary
					if (in_array($field,$section->_encode)) 
						$value = stripslashes(urldecode($value));
					$section->data[$field] = $value;
					$section->fetched[$field] = 1;
				}
				else
					echo "ERROR: field $field not in _allfields!!!<br />";
			$section->fetcheddown = 1;
			$section->fetched_forever_and_ever = 1;
		}

		// now, create page objects and fetch them
		$query = "
SELECT
	t_pages.section_id AS section_id, page.page_id AS page_id".
	(($quick) ? " " : 
	", page_display_type AS type, page_title AS title, DATE_FORMAT(page_activate_tstamp, '%Y-%m-%d') AS activatedate, DATE_FORMAT(page_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
	page_active AS active, page_story_order AS storyorder, page_show_creator AS showcreator, 
	page_show_date AS showdate, page_show_hr AS showhr,	page_archiveby AS archiveby, page_locked AS locked,
	page_updated_tstamp AS editedtimestamp, page_created_tstamp AS addedtimestamp,
	page_ediscussion AS ediscussion,
	user_createdby.user_uname AS addedby, user_updatedby.user_uname AS editedby, '".$this->name."' as site_id, media_tag AS url ")
."FROM 
	t_pages
		INNER JOIN 
	page
		ON t_pages.page_id = page.page_id
		INNER JOIN
	user AS user_createdby
		ON page.FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON page.FK_updatedby = user_updatedby.user_id
		LEFT JOIN
	media
		ON page.FK_media = media_id
";
		if ($_section_id) $query = $query." WHERE section_id = $_section_id";
	
		$r = db_query($query);
		while ($a = db_fetch_assoc($r)) {
			array_change_key_case($a); // make all keys lower case
			$page =& $this->sections[$a[section_id]]->pages[$a[page_id]];
			foreach ($a as $field => $value)
				// make sure we have defined this field in the _allfields array
				if ($field == 'page_id' || in_array($field,$page->_allfields)) {
					// decode if necessary
					if (in_array($field,$page->_encode))
						$value = stripslashes(urldecode($value));
					$page->data[$field] = $value;
					$page->fetched[$field] = 1;
				}
				else
					echo "ERROR: field $field not in _allfields!!!<br />";
			$page->fetcheddown = 1;
			$page->fetched_forever_and_ever = 1;
		}

		// now, create story objects and fetch them
		$query = "
SELECT
	t_stories.section_id AS section_id, 
	t_stories.page_id AS page_id, 
	story.story_id AS story_id".
	(($quick) ? " " : 
	", story_display_type AS type, 
	story_title AS title, 
	DATE_FORMAT(story_activate_tstamp, '%Y-%m-%d') AS activatedate, 
	DATE_FORMAT(story_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
	story_active AS active, 
	story_locked AS locked, 
	story_updated_tstamp AS editedtimestamp, 
	story_created_tstamp AS addedtimestamp,
	story_discussable AS discuss, 
	story_discussemail AS discussemail,
	story_discusslabel AS discusslabel,
	story_discussdisplay AS discussdisplay, 
	story_discussauthor AS discussauthor, 
	story_category AS category, 
	story_text_type AS texttype, 
	story_text_short AS shorttext,
	story_text_long AS longertext,
	media_tag AS url,
	user_createdby.user_uname AS addedby, 
	user_updatedby.user_uname AS editedby, 
	'".$this->name."' as site_id ")
."FROM
	t_stories
		INNER JOIN
	story
		ON t_stories.story_id = story.story_id
		INNER JOIN
	user AS user_createdby
		ON story.FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON story.FK_updatedby = user_updatedby.user_id
		LEFT JOIN
	media
		ON story.FK_media = media_id		
";
		if ($_section_id) {
			$query = $query." WHERE section_id = $_section_id";
			if ($_page_id) $query = $query." AND page_id = $_page_id";		
		}
		$r = db_query($query);
		while ($a = db_fetch_assoc($r)) {
			array_change_key_case($a); // make all keys lower case
			$story =& $this->sections[$a[section_id]]->pages[$a[page_id]]->stories[$a[story_id]];
			foreach ($a as $field => $value)
				// make sure we have defined this field in the _allfields array
				if ($field == 'story_id' || in_array($field,$story->_allfields)) {
					// decode if necessary
					if (in_array($field,$story->_encode))
						$value = stripslashes(urldecode($value));
					$story->data[$field] = $value;
					$story->fetched[$field] = 1;
				}
				else
					echo "ERROR: field $field not in _allfields!!!<br />";
			$story->fetcheddown = 1;
			$story->fetched_forever_and_ever = 1;
		}
		
		$query = "
SELECT
	user_uname as editor, ugroup_name as editor2, site_editors_type as editor_type,
	MAKE_SET(IFNULL(permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	t_sites
		INNER JOIN
	site_editors ON
		site_id = FK_site
		LEFT JOIN
	user
		ON site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup
		ON site_editors.FK_editor = ugroup_id
		LEFT JOIN
	permission ON
		site_id  = FK_scope_id
			AND
		permission_scope_type = 'site'
			AND
		permission.FK_editor <=> site_editors.FK_editor
			AND
		permission_editor_type = site_editors_type
";
		$r = db_query($query);

		$this->editors = array();
		$this->permissions = array();
		$this->cachedPermissions = array();

		// for every permisson entry, add it to the permissions array
		while ($row=db_fetch_assoc($r)) {
			// decode 'final_permissions'; 
			// 'final_permissions' is a field returned by the query and contains a string of the form "'a','vi','e'" etc.
			$a = array();
			$a[a] = (strpos($row[permissions],'a') !== false) ? 1 : 0; // look for 'a' in 'final_permissions'
			$a[e] = (strpos($row[permissions],'e') !== false) ? 1 : 0; // !== is very important here, because a position 0 is interpreted by != as FALSE
			$a[d] = (strpos($row[permissions],'d') !== false && (strpos($row[permissions],'d') !== strpos($row[permissions],'di'))) ? 1 : 0;
			$a[v] = (strpos($row[permissions],'v') !== false) ? 1 : 0;
			$a[di] = (strpos($row[permissions],'di') !== false) ? 1 : 0;
			
			// if the editor is a user then the editor's name is just the user name
			// if the editor is 'institute' or 'everyone' then set the editor's name correspondingly
			if ($row[editor_type]=='user')
				$t_editor = $row[editor];
			else if ($row[editor_type]=='ugroup')
				$t_editor = $row[editor2];
			else
				$t_editor = $row[editor_type];
			
//			echo "<br /><br />Editor: $t_editor; Add: $a[a]; Edit: $a[e]; Delete: $a[d]; View: $a[v];  Discuss: $a[di]; On the Site";
				
			// set the permissions for this editor
			$this->permissions[$t_editor] = array(
				permissions::ADD()=>$a[a], 
				permissions::EDIT()=>$a[e], 
				permissions::DELETE()=>$a[d], 
				permissions::VIEW()=>$a[v], 
				permissions::DISCUSS()=>$a[di]
			);
			if ($a[v])
				$this->canview[$t_editor] = 1;
/*			$this->cachedPermissions = array(
				$t_editor."ADD"=>$a[a], 
				$t_editor."EDIT"=>$a[e], 
				$t_editor."DELETE"=>$a[d], 
				$t_editor."VIEW"=>$a[v], 
				$t_editor."DISCUSS"=>$a[di]
			);*/
			
			// now add the editor to the editor array
			$this->editors[]=$t_editor;
			
		}

		// now, inherit the permissions to the children
		foreach (array_keys($this->sections) as $key => $section_id) {
			$this->sections[$section_id]->editors = $this->editors;
			$this->sections[$section_id]->permissions = $this->permissions;
		}

		$this->builtPermissions=1;
		
		$query = "
SELECT
	section_id, user_uname as editor, ugroup_name as editor2, site_editors_type as editor_type,
	MAKE_SET(IFNULL(permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	t_sections
		INNER JOIN
	site_editors ON
		site_id = site_editors.FK_site
		LEFT JOIN
	user ON
		site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup ON
		site_editors.FK_editor = ugroup_id
		INNER JOIN
	permission ON
		section_id  = FK_scope_id
			AND
		permission_scope_type = 'section'
			AND
		permission.FK_editor <=> site_editors.FK_editor
			AND
		permission_editor_type = site_editors_type
";
				
		$r = db_query($query);

		// for every permisson entry, add it to the permissions array
		while ($row=db_fetch_assoc($r)) {
			// decode 'final_permissions'; 
			// 'final_permissions' is a field returned by the query and contains a string of the form "'a','vi','e'" etc.
			$a = array();
			if (strpos($row[permissions],'a') !== false) $a[permissions::ADD()] = 1; // look for 'a' in 'final_permissions'
			if (strpos($row[permissions],'e') !== false) $a[permissions::EDIT()] = 1; // !== is very important here, because a position 0 is interpreted by != as FALSE
			if (strpos($row[permissions],'d') !== false && (strpos($row[permissions],'d') !== strpos($row[permissions],'di'))) $a[permissions::DELETE()] = 1;
			if (strpos($row[permissions],'v') !== false) $a[permissions::VIEW()] = 1;
			if (strpos($row[permissions],'di') !== false) $a[permissions::DISCUSS()] = 1;

			// if the editor is a user then the editor's name is just the user name
			// if the editor is 'institute' or 'everyone' then set the editor's name correspondingly
			if ($row[editor_type]=='user')
				$t_editor = $row[editor];
			else if ($row[editor_type]=='ugroup')
				$t_editor = $row[editor2];
			else
				$t_editor = $row[editor_type];
			
//			echo "<br /><br />Editor: $t_editor; Add: $a[a]; Edit: $a[e]; Delete: $a[d]; View: $a[v];  Discuss: $a[di]; on Section ".$row[section_id];

			foreach ($a as $key => $value)
				$this->sections[$row[section_id]]->permissions[$t_editor][$key] = 1;
			if ($a[permissions::VIEW()] && !$this->canview[$t_editor])
				$this->canview[$t_editor] = 1;
		}

		// now, inherit the permissions to the children
		foreach (array_keys($this->sections) as $key1 => $section_id) {
			foreach(array_keys($this->sections[$section_id]->pages) as $key2 => $page_id) {
				$this->sections[$section_id]->pages[$page_id]->editors = $this->sections[$section_id]->editors;
				$this->sections[$section_id]->pages[$page_id]->permissions = $this->sections[$section_id]->permissions;
			}
			$this->sections[$section_id]->builtPermissions=1;
		}

		$query = "
SELECT
	section_id, page_id, user_uname as editor, ugroup_name as editor2, site_editors_type as editor_type,
	MAKE_SET(IFNULL(permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	t_pages
		INNER JOIN
	site_editors ON
		site_id = site_editors.FK_site
		LEFT JOIN
	user ON
		site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup ON
		site_editors.FK_editor = ugroup_id
		INNER JOIN
	permission ON
		page_id  = FK_scope_id
			AND
		permission_scope_type = 'page'
			AND
		permission.FK_editor <=> site_editors.FK_editor
			AND
		permission_editor_type = site_editors_type
";



		$r = db_query($query);

		// for every permisson entry, add it to the permissions array
		while ($row=db_fetch_assoc($r)) {
			// decode 'final_permissions'; 
			// 'final_permissions' is a field returned by the query and contains a string of the form "'a','vi','e'" etc.
			$a = array();
			if (strpos($row[permissions],'a') !== false) $a[permissions::ADD()] = 1; // look for 'a' in 'final_permissions'
			if (strpos($row[permissions],'e') !== false) $a[permissions::EDIT()] = 1; // !== is very important here, because a position 0 is interpreted by != as FALSE
			if (strpos($row[permissions],'d') !== false && (strpos($row[permissions],'d') !== strpos($row[permissions],'di'))) $a[permissions::DELETE()] = 1;
			if (strpos($row[permissions],'v') !== false) $a[permissions::VIEW()] = 1;
			if (strpos($row[permissions],'di') !== false) $a[permissions::DISCUSS()] = 1;

			// if the editor is a user then the editor's name is just the user name
			// if the editor is 'institute' or 'everyone' then set the editor's name correspondingly
			if ($row[editor_type]=='user')
				$t_editor = $row[editor];
			else if ($row[editor_type]=='ugroup')
				$t_editor = $row[editor2];
			else
				$t_editor = $row[editor_type];
			
//			echo "<br /><br />Editor: $t_editor; Add: $a[a]; Edit: $a[e]; Delete: $a[d]; View: $a[v];  Discuss: $a[di];";

			foreach ($a as $key => $value)
				$this->sections[$row[section_id]]->pages[$row[page_id]]->permissions[$t_editor][$key] = 1;
			if ($a[permissions::VIEW()] && !$this->canview[$t_editor])
				$this->canview[$t_editor] = 1;
		}

		// now, inherit the permissions to the children
		foreach (array_keys($this->sections) as $key1 => $section_id)
			foreach(array_keys($this->sections[$section_id]->pages) as $key2 => $page_id) {
				foreach(array_keys($this->sections[$section_id]->pages[$page_id]->stories) as $key3 => $story_id) {
					$this->sections[$section_id]->pages[$page_id]->stories[$story_id]->editors = $this->sections[$section_id]->pages[$page_id]->editors;
					$this->sections[$section_id]->pages[$page_id]->stories[$story_id]->permissions = $this->sections[$section_id]->pages[$page_id]->permissions;
					$this->sections[$section_id]->pages[$page_id]->stories[$story_id]->builtPermissions=1;
				}
			$this->sections[$section_id]->pages[$page_id]->builtPermissions=1;
			}


		$query = "
SELECT
	section_id, page_id, story_id, user_uname as editor, ugroup_name as editor2,  site_editors_type as editor_type, permission_id,
	MAKE_SET(IFNULL(permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	t_stories
		INNER JOIN
	site_editors ON
		site_id = site_editors.FK_site
		LEFT JOIN
	user ON
		site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup ON
		site_editors.FK_editor = ugroup_id
		INNER JOIN
	permission ON
		story_id = FK_scope_id
			AND
		permission_scope_type = 'story'
			AND
		permission.FK_editor <=> site_editors.FK_editor
			AND
		permission_editor_type = site_editors_type
";

		$r = db_query($query);

		// for every permisson entry, add it to the permissions array
		while ($row=db_fetch_assoc($r)) {
			// decode 'final_permissions'; 
			// 'final_permissions' is a field returned by the query and contains a string of the form "'a','vi','e'" etc.
			$a = array();
//			printpre($row);

			// if the editor is a user then the editor's name is just the user name
			// if the editor is 'institute' or 'everyone' then set the editor's name correspondingly
			if ($row[editor_type]=='user')
				$t_editor = $row[editor];
			else if ($row[editor_type]=='ugroup')
				$t_editor = $row[editor2];
			else
				$t_editor = $row[editor_type];
				
			
			
			// Everyone and institute can't have add, edit, or delete permissions.
			// Somehow, these were added sometimes. If this is the case, prevent 
			// these from being set and reset those for the site.
			if ($t_editor == 'everyone' || $t_editor == 'institute') {
				// If we have a bad permission, do cleanup.
				 if ((strpos($row[permissions],'a') !== false)
				 || (strpos($row[permissions],'e') !== false)
				 ||(strpos($row[permissions],'d') !== false && (strpos($row[permissions],'d') !== strpos($row[permissions],'di')))
				) {
					printError ("Invalid add, edit, or delete permissions for $t_editor: permissionId - ".$row[permission_id]."; permission - ".$row[permissions]."\n<br />Cleaning up Database.");
					
					// Clean up the permissions
					$this->owningSiteObj->setUserPermissionDown('add', $t_editor, 0);
					$this->owningSiteObj->setUserPermissionDown('edit', $t_editor, 0);
					$this->owningSiteObj->setUserPermissionDown('delete', $t_editor, 0);
					$this->owningSiteObj->updatePermissionsDB(TRUE);
					if (is_numeric($row[permission_id])) {
						if ($row[permissions]!='d')
							$cleanupQuery = "UPDATE permission SET  permission_value='di' WHERE permission_id=".$row[permission_id];
						else
							$cleanupQuery = "DELETE FROM permission WHERE permission_id=".$row[permission_id];
						$cleanupResult = db_query($cleanupQuery);
					}
				}		
			}
			// Assign Add, edit, and delete permissions as needed.
			else {
				if (strpos($row[permissions],'a') !== false) $a[permissions::ADD()] = 1; // look for 'a' in 'final_permissions'
				if (strpos($row[permissions],'e') !== false) $a[permissions::EDIT()] = 1; // !== is very important here, because a position 0 is interpreted by != as FALSE
				if (strpos($row[permissions],'d') !== false && (strpos($row[permissions],'d') !== strpos($row[permissions],'di'))) $a[permissions::DELETE()] = 1;
			}
			if (strpos($row[permissions],'v') !== false) $a[permissions::VIEW()] = 1;
			if (strpos($row[permissions],'di') !== false) $a[permissions::DISCUSS()] = 1;
//			print_r($a); //debug
			
//			echo "<br /><br />Editor: $t_editor; Add: $a[a]; Edit: $a[e]; Delete: $a[d]; View: $a[v];  Discuss: $a[di]; On story id ".$row[story_id];
			
			foreach ($a as $key => $value)
				$this->sections[$row[section_id]]->pages[$row[page_id]]->stories[$row[story_id]]->permissions[$t_editor][$key] = 1;
			if ($a[permissions::VIEW()] && !$this->canview[$t_editor])
				$this->canview[$t_editor] = 1;
		}

		return $this->id;
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
/* 			print "site fetchdown ".$this->name."<br />"; */
			if (!$this->tobefetched) $this->fetchFromDB($full);
			foreach ($this->getField("sections") as $s) {
				$this->sections[$s] =& new section($this->name,$s,$this);
				$this->sections[$s]->fetchDown($full);
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchUp() {
		if (!$this->fetchedup) {
			$this->owningSiteObj = &$this;
			$this->fetchedup = 1;
		}
	}
	
	function fetchFromDB($force=0) {
		if ($this->site_does_not_exist) return false;

		global $dbuser, $dbpass, $dbdb, $dbhost;
		global $cfg;
		// take this out when appropriate & replace occurences;
		global $uploaddir;
		
		$this->tobefetched=1;
		
		//$this->id = $this->getField("id"); // why need to do this?
		
		if ($force) {
			// the code below is inefficient! why fetch each field separately when we can fetch all fields at same time
			// thus we can cut the number of queries significantly
/*			foreach ($this->_allfields as $f) {
				$this->getField($f);
			}
*/

			// connect to db and initialize data array
 			db_connect($dbhost,$dbuser,$dbpass, $dbdb);
			$this->data = array();

			// first fetch all fields that are not part of a 1-to-many relationship
 			$query = "
SELECT  site_title AS title, DATE_FORMAT(site_activate_tstamp, '%Y-%m-%d') AS activatedate, DATE_FORMAT(site_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
		site_active AS active, site_listed AS listed, site_theme AS theme, site_themesettings AS themesettings,
		site_header AS header, site_footer AS footer, site_updated_tstamp AS editedtimestamp, site_created_tstamp AS addedtimestamp,
		user_createdby.user_uname AS addedby, user_updatedby.user_uname AS editedby, slot_name as name, slot_type AS type
FROM 
	site
		INNER JOIN
	user AS user_createdby
		ON FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON FK_updatedby = user_updatedby.user_id
		INNER JOIN
	slot
		ON site_id = FK_site
WHERE site_id = ".$this->id;

/* 			print "<pre>"; */
/* 			print_r ($this); */
/* 			print "</pre>"; */
/* 			print "\$query=<br />$query<br />"; */
			$r = db_query($query);
/* 			print "\$r=".$r."<br />"; */

			// if the site does not exist in the database
			if (!db_num_rows($r)) return false;

			$a = db_fetch_assoc($r);
/* 			print "\$a=$a"; */
			array_change_key_case($a); // make all keys lower case
			// for each field returned by the query
			foreach ($a as $field => $value)
				// make sure we have defined this field in the _allfields array
				if (in_array($field,$this->_allfields)) {
					// decode if necessary
					if (in_array($field,$this->_encode)) 
						$value = stripslashes(urldecode($value));
					$this->data[$field] = $value;
					$this->fetched[$field] = 1;
				}
				else
					echo "ERROR: field $field not in _allfields!!!<br />";
			

			// now fetch the sections (they are part of a 1-to-many relationship and therefore
			// we cannot fetch them along with the other fields)			
			$query = "
SELECT
	section_id
FROM
	site
		INNER JOIN
	section
		ON site_id = FK_site
WHERE site_id = ".$this->id."
ORDER BY
	section_order
";

			$r = db_query($query);
			$this->data[sections] = array();
			while ($a = db_fetch_assoc($r))
				$this->data[sections][] = $a[section_id];
			$this->fetched[sections] = 1;
		}
		
		return $this->id;
	}
	


	function applyTemplate ($template) {
		$templateObj =& new site($template);
		$templateObj->fetchDown(1);	
		/* print "<pre>"; print_r($this); print_r($templateObj); print "</pre>"; */
		if (!$this->name) {
			print ("Site doesn't exist. Can't add template to it. Please contact the administrator with the steps that you did to get to this point.");
			exit;	
		}
		foreach ($templateObj->sections as $i=>$o) 
			$o->copyObj($this);
	}
	
	function setSiteName($name, $copySite=0) {
		if ($this->tobefetched && !$copySite) { // we are trying to change the name of an existing site!! bad.
			return 0;
		}
		$this->name = $this->owning_site = $name;
		$this->setField("name",$name);
		return 1;
	}
	
/******************************************************************************
 * copySite - clearPermissions currently has no effect. All permissions are cleared.
 ******************************************************************************/
	function copySite($newName, $clearPermissions=1, $copyDiscussions=FALSE) {
		if ($newName == $this->name) return FALSE;
		if ($newName == "" || !$newName) return FALSE;
		
		$oldName = $this->name;
		
		$this->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();
		
		// Make a hash array of site, section, and page ids so that
		makeSiteHash($this);
		$newSiteObj = $this;
		$newSiteObj->setSiteName($newName, 1);
		
		// Since we are specifying TRUE for the 'copy' option, each
		// part should add its new id to the global hash
		$newSiteObj->insertDB(1, 1, 0, $copyDiscussions);
		
		// Copy all the media
		$query = "
SELECT
	media_id
FROM
	media
		INNER JOIN
	slot
		ON
			media.FK_site = slot.FK_site
WHERE
	slot_name='".$oldName."'";
		$r = db_query($query);
		while ($a = db_fetch_assoc($r)) {
			copy_media($a['media_id'], $newName);
		}
		
		$newSiteObj = NULL;
		unset($newSiteObj);
		
		$newSiteObj = new site($newName);
		$newSiteObj->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();
		
		// Remove the permissions if we are clearing them.
		if ($clearPermissions) {
			$editors = $newSiteObj->getEditors();
			foreach ($editors as $editor) {
				$newSiteObj->delEditor($editor);
			}
		}
		
		// Parse through all the text for links refering to parts of the
		// old site and update them with the new ids.
		updateSiteLinksFromHash($newSiteObj, $newSiteObj);
		$newSiteObj->updateDB(1,1);
		
		// Delete any editors that we wanted to delete.
		$newSiteObj->deletePendingEditors();
	}
	
	function updateDB($down=0, $force=0, $keepEditHistory=FALSE) {
		if (count($this->changed)) {
		// the easy step: update the fields in the table
			$a = $this->createSQLArray();
			if ($keepEditHistory) {
				$a[] = $this->_datafields[editedtimestamp][1][0]."='".$this->getField("editedtimestamp")."'";
			} else
				$a[] = "FK_updatedby=".$_SESSION[aid];
				
			$query = "UPDATE site SET ".implode(",",$a)." WHERE site_id=".$this->id;
/*  			print "site->updateDB: $query<br />"; */
			db_query($query);
/* 			print mysql_error()."<br />"; */

		// the hard step: update the fields in the JOIN tables

			// first update 'slot_name' in the slot table, if the latter has changed
			if ($this->changed[name] && $this->data[name]) {
				$new_name = $this->data[name];
				$query = "UPDATE slot SET slot_name = '$new_name' WHERE FK_site=".$this->id;
				db_query($query);
			}

/* 			// now update all the section ids in the children, if the latter have changed */
/* 			if ($this->changed[sections]) { */
/* 				// first, a precautionary step: reset the parent of every section that used to have this site object as the parent */
/* 				// we do this, because we might have removed a certain section from the array of sections of a site object */
/* 				$query = "UPDATE section SET FK_site=0 WHERE FK_site=".$this->id; */
/* 				db_query($query); */
/* 				 */
/* 				// now, update all sections */
/* 				foreach ($this->data['sections'] as $k=>$v) { */
/* 					$query = "UPDATE section SET FK_site=".$this->id.", section_order=$k WHERE section_id=".$v; */
/* 					db_query($query); */
/* 				} */
/* 				 */
/* 			} */
		}
		// now update the permissions
		$this->updatePermissionsDB($force);
		
		// add log entry
/* 		log_entry("edit_site",$this->name,"","","$_SESSION[auser] edited ".$this->name); */
		
		// update down
		if ($down) {
			if ($this->fetcheddown && $this->sections) {
				foreach (array_keys($this->sections) as $k=>$i) $this->sections[$i]->updateDB($down, $force, $keepEditHistory);
			}
		}
		return 1;
	}
	
	function insertDB($down=0,$copysite=0,$importing=0, $keepDiscussions=0) {
		$a = $this->createSQLArray(1);
		if (!$importing) {
			$a[] = "FK_createdby=".$_SESSION[aid];
			$a[] = $this->_datafields[addedtimestamp][1][0]."=NOW()";
			$a[] = "FK_updatedby=".$_SESSION[aid];
		} else {
			$a[] = "FK_createdby=".db_get_value("user","user_id","user_uname='".$this->data[addedby]."'");
			$a[] = $this->_datafields[addedtimestamp][1][0]."='".$this->getField("addedtimestamp")."'";
			$a[] = "FK_updatedby=".db_get_value("user","user_id","user_uname='".$this->data[editedby]."'");
			$a[] = $this->_datafields[editedtimestamp][1][0]."='".$this->getField("editedtimestamp")."'";
		}

		// insert into the site table
		$query = "INSERT INTO site SET ".implode(",",$a).";";
/*  		print "<br />query = $query<br />"; */
		db_query($query);
		$this->id = lastid();
		
/* 		print "<H1>ID = ".$this->id."</H1>"; */
		
		// in order to insert a site, the active user must own a slot
		// update the name for that slot
		if (slot::exists($this->data[name])) {
			$query = "UPDATE slot";
			$where = " WHERE slot_name = '".$this->data[name]."' AND FK_owner = ".$_SESSION[aid];
		} else {
			$query = "INSERT INTO slot";
			$where = "";
		}
		$query .= " SET slot_name = '".$this->data[name]."',FK_owner=".$_SESSION[aid].",slot_type='".$this->data[type]."', FK_site = ".$this->id.$where;
/* 		echo $query."<br />"; */
		db_query($query);
		
		
		// See if there is a site hash (meaning that we are being copied).
		// If so, try to match our id with the hash entry for 'NEXT'.
		if ($GLOBALS['__site_hash']['site'] 
			&& $oldId = array_search('NEXT', $GLOBALS['__site_hash']['site']))
		{
			$GLOBALS['__site_hash']['site'][$oldId] = $this->name;
		}
		
		// the sections haven't been created yet, so we don't have to insert data[sections] for now

		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
/* 		log_entry("add_site",$this->name,"","","$_SESSION[auser] added ".$this->name); */
		
		// insert down (insert sections)
		if ($down && $this->fetcheddown && $this->sections) {
			foreach (array_keys($this->sections) as $id) {
				// Mark our Id as the next one to set
				if (is_array($GLOBALS['__site_hash']['sections']))
					$GLOBALS['__site_hash']['sections'][$id] = 'NEXT';
					
				$this->sections[$id]->id = 0;	// createSQLArray uses this to tell if we are inserting or updating
				$this->sections[$id]->insertDB(1,$this->name,$copysite, $importing, $keepDiscussions);
			}
		}
		return 1;
	}
	
	function addSection($id) {
		if (!is_array($this->getField("sections"))) $this->data[sections] = array();
/* 		print "<br />adding section $id to ".$this->name."<br />"; //debug */
		array_push($this->data[sections],$id);
		$this->changed[sections] = 1;
/* 		print "<pre>this: "; print_r($this->data[sections]); print "</pre>"; */
	}
	
	function delSection($id,$delete=1) {
		$d = array();
		foreach ($this->getField("sections") as $n)
			if ($n != $id) $d[] = $n;
		$this->data[sections] = $d;
		$this->changed[sections] = 1;
		if ($delete) {
			$section =& new section($this->name,$id,$this);
			$section->delete();
		}
	}
	
	function delete() {	// delete from db
		global $cfg;
		if (!$this->id) return false;
		$this->fetchDown();
		$siteName = $this->getField("name");
		$query = "DELETE FROM site WHERE site_id=".$this->id;
		db_query($query);
		$query = "DELETE FROM permission WHERE FK_scope_id=".$this->id." AND permission_scope_type='site';";
		db_query($query);
		$query = "DELETE FROM media WHERE FK_site=".$this->id;
		db_query($query);
		$query = " UPDATE slot SET FK_site=NULL WHERE FK_site=".$this->id;
		db_query($query);
		
		// remove sections
		if ($this->sections) {
			foreach ($this->sections as $s=>$o) {
				$o->delete();
			}
		}
		
/* 		print "<pre>this: "; print_r($this); print "</pre>"; */
		$this->clearPermissions();
/* 		print "<pre>this: "; print_r($this); print "</pre>"; */
		$this->updatePermissionsDB();
		// remove all editors from db
//		echo $query = "DELETE FROM site_editors WHERE FK_site = ".$this->id;
		db_query($query);
//		exit(0);

		// delete the userfiles
		if (ereg("[0-9a-zA-Z]", $siteName)) {
			$file_path = $cfg['uploaddir']."/".$siteName;
			deletePath($file_path);
		}
	}
	
	function createSQLArray($all=0) {
		$this->parseMediaTextForDB("header");
		$this->parseMediaTextForDB("footer");	

		
		$d = $this->data;
		$a = array();
		
		if ($all || $this->changed[title]) $a[] = $this->_datafields[title][1][0]."='".addslashes($d[title])."'";
		if ($all || $this->changed[listed]) $a[] = $this->_datafields[listed][1][0]."='$d[listed]'";
		if ($all || $this->changed[activatedate]) $a[] = "site_activate_tstamp ='".ereg_replace("-","",$d[activatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[deactivatedate]) $a[] = "site_deactivate_tstamp ='".ereg_replace("-","",$d[deactivatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[active]) $a[] = $this->_datafields[active][1][0]."='$d[active]'";
//		if ($all || $this->changed[type]) $a[] = $this->_datafields[type][1][0]."='$d[type]'";
		if ($all || $this->changed[theme]) $a[] = $this->_datafields[theme][1][0]."='$d[theme]'";
		if ($all || $this->changed[themesettings]) $a[] = $this->_datafields[themesettings][1][0]."='$d[themesettings]'";
		if ($all || $this->changed[header]) $a[] = $this->_datafields[header][1][0]."='".urlencode($d[header])."'";
		if ($all || $this->changed[footer]) $a[] = $this->_datafields[footer][1][0]."='".urlencode($d[footer])."'";

		return $a;
	}
}
