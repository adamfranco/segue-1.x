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

echo "<pre>";


// ************************************************************************************************************************************************
// put links in media table for sections
// ************************************************************************************************************************************************

$query = "
SELECT
	FK_site, FK_createdby, url as media_tag, 'remote' as media_location, FK_updatedby, id AS section_id
FROM
	segue_et.sections
		INNER JOIN
	segue2.section
		ON id = section_id
WHERE
	type = 'url'
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	$query = "
INSERT
INTO segue2.media
SET
	FK_site = ".$a[FK_site].", 
	FK_createdby = ".$a[FK_createdby].", 
	media_tag = '".$a[media_tag]."', 
	media_location = '".$a[media_location]."', 
	FK_updatedby = ".$a[FK_updatedby];

	db_query($query);

$media_id = lastid();

	$query = "
UPDATE
	segue2.section
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
	segue_et.pages
		INNER JOIN
	segue2.page
		ON id = page_id
		INNER JOIN
	segue2.section
		ON FK_section = section.section_id
WHERE
	type = 'url'
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	$query = "
INSERT
INTO segue2.media
SET
	FK_site = ".$a[FK_site].", 
	FK_createdby = ".$a[FK_createdby].", 
	media_tag = '".$a[media_tag]."', 
	media_location = '".$a[media_location]."', 
	FK_updatedby = ".$a[FK_updatedby];

	db_query($query);

$media_id = lastid();

	$query = "
UPDATE
	segue2.page
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
	segue_et.stories
		INNER JOIN
	segue2.story
		ON id = story_id
		INNER JOIN
	segue2.page
		ON FK_page = page.page_id
		INNER JOIN
	segue2.section
		ON FK_section = section.section_id
WHERE
	type = 'link'
";

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	$query = "
INSERT
INTO segue2.media
SET
	FK_site = ".$a[FK_site].", 
	FK_createdby = ".$a[FK_createdby].", 
	media_tag = '".$a[media_tag]."', 
	media_location = '".$a[media_location]."', 
	FK_updatedby = ".$a[FK_updatedby];

	db_query($query);

$media_id = lastid();

	$query = "
UPDATE
	segue2.story
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
	segue_et.sites
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
	segue2.section
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
	segue_et.sections
ORDER BY
	sections.id
";	

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {

	$pages = decode_array($a[pages]);
	foreach ($pages as $page_order => $page_id) {
		
		$query = "
UPDATE
	segue2.page
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
	segue_et.pages
ORDER BY
	pages.id
";	

$r = db_query($query);

while ($a = db_fetch_assoc($r)) {

	$stories = decode_array($a[stories]);
	foreach ($stories as $story_order => $story_id) {
		
		$query = "
UPDATE
	segue2.story
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

//$query = "DELETE FROM segue2.discussion";
//db_query($query);

$query = "
SELECT
	id, discussions
FROM
	segue_et.stories
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
INTO segue2.discussion
	(FK_author, discussion_tstamp, discussion_subject, discussion_content , FK_story, discussion_order, FK_parent)
SELECT
	user_id, discussions.timestamp, '' as subject	, content, ".$a[id]." as story, $order as ord, NULL as parent	
FROM
	segue_et.discussions
		INNER JOIN
	segue2.user
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
	segue_et.sites
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
	segue_et.sites
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
	segue_et.sections
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
	segue_et.sites
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
	segue_et.pages
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
	segue_et.sections
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
	segue_et.stories
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
	segue_et.pages
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
