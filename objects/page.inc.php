<? /* $Id$ */

/******************************************************************************
 * page object - handles site pages
 ******************************************************************************/

class page extends segue {
	var $stories;
	var $_allfields = array("section_id","site_id","title","addedtimestamp","addedby",
						"editedby","editedtimestamp","activatedate","deactivatedate",
						"active","locked","showcreator","showdate","showhr","stories",
						"storyorder","type","url","ediscussion","archiveby");
						
	// fields listed in $_datafields are stored in the database.
	// the first element is the table join syntax required to pull the data.
	// the second element is an array of the database fields we will be selecting
	// the third element is the database field by which we will sort
	
	var $_datafields = array(
		"id" => array(
			"page",
			array("page_id"),
			"page_id"
		),
		"section_id" => array(
			"page",
			array("FK_section"),
			"page_id"
		),
		"site_id" => array(
			"page
				INNER JOIN
			 section
			 	ON FK_section = section_id
				INNER JOIN
			 site
			 	ON section.FK_site = site.site_id
			 	INNER JOIN
			 slot
				ON site.site_id = slot.FK_site
			",
			array("slot_name"),
			"page_id"
		),
		"type" => array(
			"page",
			array("page_display_type"),
			"page_id"
		),
		"title" => array(
			"page",
			array("page_title"),
			"page_id"
		),
		"activatedate" => array(
			"page",
			array("DATE_FORMAT(page_activate_tstamp, '%Y-%m-%d')"),
			"page_id"
		),
		"deactivatedate" => array(
			"page",
			array("DATE_FORMAT(page_deactivate_tstamp, '%Y-%m-%d')"),
			"page_id"
		),
		"active" => array(
			"page",
			array("page_active"),
			"page_id"
		),		
		"storyorder" => array(
			"page",
			array("page_story_order"),
			"page_id"
		),
		"showcreator" => array(
			"page",
			array("page_show_creator"),
			"page_id"
		),
		"showdate" => array(
			"page",
			array("page_show_date"),
			"page_id"
		),
		"showhr" => array(
			"page",
			array("page_show_hr"),
			"page_id"
		),
		"url" => array(
			"page
				LEFT JOIN
			media
				ON FK_media = media_id",
			array("media_tag"),
			"page_id"
		),
		"archiveby" => array(
			"page",
			array("page_archiveby"),
			"page_id"
		),
		"locked" => array(
			"page",
			array("page_locked"),
			"page_id"
		),
		"editedby" => array(
			"page
				INNER JOIN
			user
				ON FK_updatedby = user_id",
			array("user_uname"),
			"page_id"
		),
		"editedtimestamp" => array(
			"page",
			array("page_updated_tstamp"),
			"page_id"
		),
		"addedby" => array(
			"page
				INNER JOIN
			user
				ON FK_createdby = user_id",
			array("user_uname"),
			"page_id"
		),
		"addedtimestamp" => array(
			"page",
			array("page_created_tstamp"),
			"page_id"
		),
		"ediscussion" => array(
			"page",
			array("page_ediscussion"),
			"page_id"
		),
		"stories" => array(
			"page
				INNER JOIN
			story
				ON page_id = FK_page",
			array("story_id"),
			"story_order"
		)
	);

	var $_table = "page";
	
						
	
	function page($insite,$insection,$id=0,&$sectionObj) {
		$this->owning_site = $insite;
		$this->owning_section = $insection;
		$this->owningSectionObj = &$sectionObj;
		$this->owningSiteObj = &$this->owningSectionObj->owningSiteObj;
		$this->fetchedup = 1;
		
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->data[section_id] = $insection;
		$this->init();
		$this->data[type] = "page";
	}
	
	function init($formdates=0) {
		$this->stories = array();
		if (!is_array($this->data)) $this->data = array();
		$this->data[title] = "";
		$this->data[activatedate] = $this->data[deactivatedate] = "0000-00-00";
		$this->data[active] = 1;
		$this->data[url] = "http://";
		$this->data[locked] = 0;
		$this->data[showcreator] = 0;
		$this->data[showdate] = 0;
		$this->data[archiveby] = "none";
		$this->data[ediscussion] = 0;
		$this->data[showcreator] = 0;
		$this->data[showdate] = 0;
		$this->data[showhr] = 0;
		$this->data[archiveby] = "none";
		$this->data[stories] = array();
		$this->data[storyorder] = "custom";
		if ($this->id) $this->fetchFromDB();
		if ($formdates) $this->initFormDates();
	}
	
	function delete($deleteFromParent=0) {	// delete from db
//		print "<br><BR> Deleting Page<bR><BR>";
		if (!$this->id) return false;
		if ($deleteFromParent) {
			$parentObj =& new section($this->owning_site,$this->owning_section,&$this->owningSectionObj->owningSiteObj);
			$parentObj->fetchDown();
			$parentObj->delPage($this->id);
			$parentObj->updateDB();
		} else {
			// remove stories
			$this->fetchDown();
			if ($this->stories) {
				foreach ($this->stories as $s=>$o) {
					$o->delete();
				}
			}
			
			$query = "DELETE FROM page WHERE page_id=".$this->id;
			db_query($query);
			$query = "DELETE FROM permission WHERE FK_scope_id=".$this->id." AND permission_scope_type='page';";
			db_query($query);
			
			$this->clearPermissions();
			$this->updatePermissionsDB();
		}
	}
	
	function addStory($id) {
		if (!is_array($this->getField("stories"))) $this->data[stories] = array();
		array_push($this->data["stories"],$id);
		$this->changed[stories]=1;
	}
	
	function delStory($id,$delete=1) {
/* 		print "<br> deleting - $id - $delete<br>"; */
/* 		print "<pre>"; print_r($this); print "</pre>"; */
		$d = array();
		foreach ($this->getField("stories") as $s) {
			if ($s != $id) $d[]=$s;
		}
		$this->data[stories] = $d;
		$this->changed[stories]=1;
/* 		print "------------------------ <br><pre>"; print_r($this); print "</pre>"; */
		if ($delete) {
			$story =& new story($this->owning_site,$this->owning_section,$this->owning_page,$id,&$this);
			$story->delete();
		}
	}
	
	function fetchUp($full = 0) {
		if (!$this->fetchedup || $full) {
			$this->owningSiteObj =& new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB();
//			$this->owningSiteObj->buildPermissionsArray(1);
			$this->owningSectionObj =& new section($this->owning_site,$this->owning_section,&$this->owningSiteObj);
			$this->owningSectionObj->fetchFromDB();
//			$this->owningSectionObj->buildPermissionsArray(1);
			$this->fetchedup = 1;
		}
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
/* 			print "---->page fetchdown".$this->id." full = $full<BR>"; */
			if (!$this->tobefetched || $full) 
				$this->fetchFromDB(0,$full);
			foreach ($this->getField("stories") as $s) {
				$this->stories[$s] =& new story($this->owning_site,$this->owning_section,$this->id,$s,&$this);
				$this->stories[$s]->fetchDown($full);
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0,$force=0) {
		if ($id) $this->id = $id;
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
SELECT  
	page_display_type AS type, page_title AS title, DATE_FORMAT(page_activate_tstamp, '%Y-%m-%d') AS activatedate, DATE_FORMAT(page_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
	page_active AS active, page_story_order AS storyorder, page_show_creator AS showcreator, 
	page_show_date AS showdate, page_show_hr AS showhr,	page_archiveby AS archiveby, page_locked AS locked,
	page_updated_tstamp AS editedtimestamp, page_created_tstamp AS addedtimestamp,
	page_ediscussion AS ediscussion,
	user_createdby.user_uname AS addedby, user_updatedby.user_uname AS editedby, slot_name as site_id,
	FK_section AS section_id, media_tag AS url
FROM 
	page
		INNER JOIN
	 section
		ON FK_section = section_id
		INNER JOIN
	user AS user_createdby
		ON page.FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON page.FK_updatedby = user_updatedby.user_id
		INNER JOIN
	 site
		ON section.FK_site = site.site_id
		INNER JOIN
	 slot
		ON site.site_id = slot.FK_site
		LEFT JOIN
	media
		ON page.FK_media = media_id
WHERE 
	page_id = ".$this->id;

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
	// UPDATE parseMediaTextForEdit *********************************************************************
	// UPDATE parseMediaTextForEdit *********************************************************************
	// UPDATE parseMediaTextForEdit *********************************************************************
	//				if (in_array($field,$this->_parse)) 
	//					$value = $this->parseMediaTextForEdit($value);
					$this->data[$field] = $value;
					$this->fetched[$field] = 1;
				}
				else
					echo "ERROR: field $field not in _allfields!!!<br>";
			

			// now fetch the sections (they are part of a 1-to-many relationship and therefore
			// we cannot fetch them along with the other fields)			
			$query = "
SELECT
	story_id
FROM
	page
		INNER JOIN
	story
		ON page_id = FK_page
WHERE page_id = ".$this->id."
ORDER BY
	page_order
";


			$r = db_query($query);
			$this->data[stories] = array();
			while ($a = db_fetch_assoc($r))
				$this->data[stories][] = $a[story_id];

			$this->fetched[stories] = 1;
		}
		
		return $this->id;

	}
	
	function updateDB($down=0, $force=0) {
		if (count($this->changed)) {
			$a = $this->createSQLArray();
			$a[] = "FK_updatedby=".$_SESSION[aid];
//			$a[] = "editedtimestamp=NOW()";  // no need to do this anymore, MySQL will update the timestamp automatically
			$query = "UPDATE page SET ".implode(",",$a)." WHERE page_id=".$this->id;
/* 			print "<pre>Page->UpdateDB: $query<br>"; */
			db_query($query);
/* 			print mysql_error()."<br>"; */
/* 			print_r($this->data['stories']); */
/* 			print "</pre>"; */
			
			// the hard step: update the fields in the JOIN tables
			
			// Urls are now stored in the media table
			if ($this->changed[url]) {
				// Urls are now stored in the media table
				// get id of media item
				$query = "
SELECT
	FK_media
FROM
	page
WHERE
	page_id = ".$this->id;

				$a = db_fetch_assoc(db_query($query));
				$media_id = $a[FK_media];
							
				$query = "
UPDATE
	media
SET
	media_tag = '".$this->data[url]."',
	FK_updatedby = ".$_SESSION[aid]."
WHERE
	media_id = $media_id
";

				db_query($query);

				 
			}	
		}
		
		// update permissions
		$this->updatePermissionsDB($force);

		// add log entry
/* 		log_entry("edit_section",$this->owning_site,$this->id,"","$_SESSION[auser] edited section id ".$this->id." in site ".$this->owning_site); */
		
		// update down
		if ($down) {
			if ($this->fetcheddown && $this->stories) {
				foreach (array_keys($this->stories) as $i) $this->stories[$i]->updateDB($down, $force);
			}
		}
		return true;
	}
	
	function insertDB($down=0,$newsite=null,$newsection=0,$removeOrigional=0,$keepaddedby=0) {
		$origsite = $this->owning_site;
		$origid = $this->id;
		if ($newsite) {
			$this->owning_site = $newsite;
			unset($this->owningSiteObj);
		}
		if ($newsection) {
			$this->owning_section = $newsection;
			unset($this->owningSectionObj);
		}
		
/* 		print "<pre>\n\nXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\n"; */
/* 		print "owning_site=".$this->owning_site."\nowning section=".$this->owning_section."\nOwningSiteObj: "; */
/* //		print_r ($this->owningSiteObj); */
/* 		print "\nOwningSectionObj: "; */
/* 		print_r ($this->owningSectionObj); */
/* 		print "\nXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\n\n</pre>"; */
		
		$this->fetchUp(1);

		$a = $this->createSQLArray(1);
		if (!$keepaddedby) {
			$a[] = "FK_createdby=".$_SESSION[aid];
			$a[] = $this->_datafields[addedtimestamp][1][0]."=NOW()";
			$a[] = "FK_updatedby=".$_SESSION[aid];
		} else {
			$a[] = "FK_createdby=".db_get_value("user","user_id","user_uname='".$this->getField("addedby")."'");
			$a[] = $this->_datafields[addedtimestamp][1][0]."='".$this->getField("addedtimestamp")."'";
			$a[] = "FK_updatedby=".db_get_value("user","user_id","user_uname='".$this->getField("editedby")."'");
			$a[] = $this->_datafields[editedtimestamp][1][0]."='".$this->getField("editedtimestamp")."'";
		}

		// insert media (url)
		if ($this->data[url]) {
			// first see, if media item already exists in media table
			$query = "
SELECT
	media_id
FROM
	media
WHERE
	FK_site = ".$this->owningSiteObj->id." AND
	FK_createdby = ".$_SESSION[aid]." AND
	media_tag = '".$this->data[url]."' AND
	media_location = 'remote'";
			$r = db_query($query);
			
			// if not in media table insert it
			if (!db_num_rows($r)) {
				$query = "
INSERT
INTO media
SET
	FK_site = ".$this->owningSiteObj->id.",
	FK_createdby = ".$_SESSION[aid].",
	media_tag = '".$this->data[url]."',
	media_location = 'remote',
	FK_updatedby = ".$_SESSION[aid]."
";
				db_query($query);
				$a[] = "FK_media=".lastid();
			}
			// if in media table, assign the media id
			else {
				$arr = db_fetch_assoc($r);
				$a[] = "FK_media=".$arr[media_id];
			}
		}

		$query = "INSERT INTO page SET ".implode(",",$a);
/* 		print $query."<br>"; //debug */
		db_query($query);
		
		$this->id = lastid();
		
		// See if there is a site hash (meaning that we are being copied).
		// If so, try to match our id with the hash entry for 'NEXT'.
		if ($GLOBALS['__site_hash']['pages'] 
			&& $oldId = array_search('NEXT', $GLOBALS['__site_hash']['pages']))
		{
			$GLOBALS['__site_hash']['pages'][$oldId] = $this->id;
		}
		
		$this->fetchUp();
/* 		$this->owningSectionObj->addPage($this->id); */
		if ($removeOrigional) $this->owningSectionObj->delPage($origid,0);
		$this->owningSectionObj->updateDB();
		
		// add new permissions entry.. force update
//		$this->updatePermissionsDB(1);	// We shouldn't need this because new sections will just
										//inherit the permissions of their parent sites
		
		// add log entry
/* 		log_entry("add_page",$this->owning_site,$this->owning_section,$this->id,"$_SESSION[auser] added page id ".$this->id." to site ".$this->owning_site); */
		
		// insert down
		if ($down && $this->fetcheddown && $this->stories) {
			foreach (array_keys($this->stories) as $i) {
				// Mark our Id as the next one to set
				if (is_array($GLOBALS['__site_hash']['stories']))
					$GLOBALS['__site_hash']['stories'][$i] = 'NEXT';
					
				$this->stories[$i]->id = 0;	// createSQLArray uses this to tell if we are inserting or updating
				$this->stories[$i]->insertDB(1,$this->owning_site,$this->owning_section,$this->id,1,$keepaddedby);
			}
		}
		return true;
	}
	
	function createSQLArray($all=0) {
		$d = $this->data;
		$a = array();
		
		$this->fetchUp();

/* 		print "<pre>OwningSite: ".$this->owning_site."\nOwning_section: ".$this->owning_section."\nOwningSiteObj for page".$this->id.":\n"; */
/* 		print_r($this->owningSiteObj); */
/* 		print "</pre>"; */
		if (!isset($this->owningSectionObj)) {
			$this->owningSectionObj = &$this->owningSiteObj->sections[$this->owning_section];
		}
		
		if ($all) $a[] = $this->_datafields[section_id][1][0]."='".$this->owningSectionObj->id."'";
		
//		if ($this->id && ($all || $this->changed[pages])) { //I belive we may always need to fix the order.
		if ($this->id) {
			$orderkeys = array_keys($this->owningSectionObj->getField("pages"),$this->id);
			$a[] = "page_order=".$orderkeys[0];
		} else {
			$a[] = "page_order=".count($this->owningSectionObj->getField("pages"));
		}
		
/* 		print "\nXXXXXXX\n</pre>"; */
		
		if ($all || $this->changed[title]) $a[] = $this->_datafields[title][1][0]."='".addslashes($d[title])."'";
		if ($all || $this->changed[activatedate]) $a[] = "page_activate_tstamp ='".ereg_replace("-","",$d[activatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[deactivatedate]) $a[] = "page_deactivate_tstamp ='".ereg_replace("-","",$d[deactivatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[active]) $a[] = $this->_datafields[active][1][0]."='".(($d[active])?1:0)."'";
		if ($all || $this->changed[type]) $a[] = $this->_datafields[type][1][0]."='$d[type]'";
		if ($all || $this->changed[locked]) $a[] = $this->_datafields[locked][1][0]."='".(($d[locked])?1:0)."'";
//		if ($all || $this->changed[stories]) $a[] = "stories='".encode_array($d[stories])."'";
//		if ($all || $this->changed[url]) $a[] = "url='$d[url]'";
		if ($all || $this->changed[ediscussion]) $a[] = $this->_datafields[ediscussion][1][0]."='".(($d[ediscussion])?1:0)."'";
		if ($all || $this->changed[archiveby]) $a[] = $this->_datafields[archiveby][1][0]."='$d[archiveby]'";
		if ($all || $this->changed[showcreator]) $a[] = $this->_datafields[showcreator][1][0]."='".(($d[showcreator])?1:0)."'";
		if ($all || $this->changed[showdate]) $a[] = $this->_datafields[showdate][1][0]."='".(($d[showdate])?1:0)."'";
		if ($all || $this->changed[showhr]) $a[] = $this->_datafields[showhr][1][0]."='".(($d[showhr])?1:0)."'";
		if ($all || $this->changed[storyorder]) $a[] = $this->_datafields[storyorder][1][0]."='".$d[storyorder]."'";
		
		return $a;
	}
	
	
	function handleStoryArchive() {
		global $months;
/* 		global $usesearch; */
/* 		global $site,$section,$page; */
		$site = $this->owning_site;
		$section = $this->owning_section;
		$page=$this->id;
/* 		$stories = $this->getField("stories"); */
		$newstories = array();
		$pa = $this->getField("archiveby");
		
		if ($pa == 'none' || $pa == '') return;
		
		// if we're looking at a discussion, don't do the archiving
		if ($_REQUEST['detail']) return;
		
		$_a = array("startday","startmonth","startyear","endday","endmonth","endyear","usestart","useend","usesearch");
		foreach ($_a as $a) $$a = $_REQUEST[$a];
		
		if (!$usesearch) {
			$endyear = date("Y");
			$endmonth = date("n");
			$endday = date("j");
		}
		printc("<div>");
	//	printc("<b>Search:</b> ");
		printc("Display content in date rage: ");
		printc("<form action='$PHP_SELF?$sid&action=site&site=$site&section=$section&page=$page' method=post>");
		printc("<input type=hidden name=usesearch value=1>");
	
		printc("<select name='startday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($startday == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='startmonth'>");
		for ($i=0; $i<12; $i++)
			printc("<option value=".($i+1). (($startmonth == $i+1)?" selected":"") . ">$months[$i]\n");
		
		printc("</select>\n<select name='startyear'>");
		$curryear = date("Y");
		for ($i=$curryear-10; $i <= ($curryear); $i++) {
			printc("<option" . (($startyear == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
	//	printc("<br>");
		printc(" to <select name='endday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($endday == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='endmonth'>");
		for ($i=0; $i<12; $i++) {
			printc("<option value=".($i+1) . (($endmonth == $i+1)?" selected":"") . ">$months[$i]\n");
		}
		printc("</select>\n<select name='endyear'>");
		for ($i=$curryear; $i <= ($curryear+5); $i++) {
			printc("<option" . (($endyear == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
		printc(" <input type=submit class=button value='go'>");
		printc("</form></div>");
	
		$start = mktime(1,1,1,$startmonth,$startday,$startyear);
		$end = mktime(1,1,1,$endmonth,$endday,$endyear);
		if ($pa == 'week') {
			if (!$usesearch) {
				$start = mktime(0,0,0,date("n"),date('j')-7,date('Y'));
				$end = time();
			}
		}
		if ($pa == 'month') {
			if (!$usesearch) {
				$start = mktime(0,0,0,date("n")-1,date('j'),date("Y"));
				$end = time();
			}
		}
		if ($pa == 'year') {
			if (!$usesearch) {
				$start = mktime(0,0,0,date("n"),date('j'),date("Y")-1);
				$end = time();
			}
		}
		$txtstart = date("n/j/y",$start);
		$txtend = date("n/j/y",$end);
		
//		print "start $start, end: $end<BR>";
		
		$this->fetchDown();
		foreach ($this->stories as $s=>$o) {
			$added = $o->getField("addedtimestamp");
//			print $added."<br>";
			
			ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})",$added,$regs);
//			print_r($regs);
			$year = (integer)$regs[1];
			$month = (integer)$regs[2];
			$day = (integer)$regs[3];
			$t = mktime(0,0,0,$month,$day,$year);
//			print $t."<br>";
	// 			$week = date("W",$t-(date("w",$t)*86400)); 
	//
	// 			if ($startyear == $year && $startweek == $week) 
	// 				$newstories[] = $s; 
	// 		if ((!$usestart || $start < $t) && (!$useend || $t < $end)) { 
			if (($start < $t) && ($t < $end) || false) {
				$newstories[$s] = $t;
			}
		
		}
	// 	print_r($newstories); 
	//	arsort($newstories,SORT_NUMERIC);
	// 	print_r($newstories);
		$this->setField("stories",array_keys($newstories));
		$a = array();
		foreach (array_keys($newstories) as $s)
			$a[$s] = &$this->stories[$s];

		$this->stories = &$a;
/* 		$this->fetcheddown=0;$this->fetchDown(); */
		printc("<b>Content ranging from $txtstart to $txtend.</b><br><BR>");
	}

	function handleStoryOrder() {
		// reorders the stories array passed to it depending on the order specified.
		// Orders: addedesc, addedasc, editeddesc, editedasc, author, editor, category, titledesc, titleasc
		$newstories = array();
		$order = $this->getField("storyorder");
		if ($order == '' || $order=='custom') return;
		$this->fetchDown();
		foreach ($this->stories as $s=>$o) {
			$added = ereg_replace("[: -]","",$o->getField("addedtimestamp"));
/* 			$added = str_replace("-","",$added); */
/* 			$added = str_replace(" ","",$added); */
	
			if ($order == "addeddesc" || $order == "addedasc") 
				$newstories[$s] = $added;
			else if ($order == "editeddesc" || $order == "editedasc") 
				$newstories[$s] = $o->getField("editedtimestamp");
			else if ($order == "author") 
				$newstories[$s] = $o->getField("addedby");
			else if ($order == "editor") 
				$newstories[$s] = $o->getField("editedby");
			else if ($order == "category") 
				$newstories[$s] = $o->getField("category");
			else if ($order == "titledesc" || $order == "titleasc") 
				$newstories[$s] = strtolower($o->getField("title"));
		}
		
		if ($order == "addeddesc" || $order == "editeddesc")
			arsort($newstories,SORT_NUMERIC);
		else if ($order == "addedasc" || $order == "editedasc")
			asort($newstories,SORT_NUMERIC);
		else if ($order == "titledesc")
			arsort($newstories);
		else
			asort($newstories);
		
		foreach ($newstories as $id=>$n) {
			$newstories[$id] = $this->stories[$id];
		}
		$this->stories = $newstories;
		$this->setField("stories",array_keys($newstories));
	}
}
