<? /* $Id$ */

class story extends segue {
	var $_allfields = array("page_id","section_id","site_id","title","addedby","addedtimestamp",
							"editedby","editedtimestamp","shorttext","longertext",
							"activatedate","deactivatedate","discuss",
							"locked","category","discussions","texttype","type","url");
	
	// fields listed in $_datafields are stored in the database.
	// the first element is the table join syntax required to pull the data.
	// the second element is an array of the database fields we will be selecting
	// the third element is the database field by which we will sort
	
	var $_datafields = array(
		"id" => array(
			"story",
			array("story_id"),
			"story_id"
		),
		"site_id" => array(
			"story
				INNER JOIN
			 page
			 	ON FK_page = page_id
				INNER JOIN
			 section
			 	ON FK_section = section_id
			",
			array("FK_site"),
			"story_id"
		),
		"section_id" => array(
			"story
				INNER JOIN
			 page
			 	ON FK_page = page_id
			",
			array("FK_section"),
			"story_id"
		),
		"page_id" => array(
			"story",
			array("FK_page"),
			"story_id"
		),
		"type" => array(
			"story",
			array("story_type"),
			"story_id"
		),
		"title" => array(
			"story",
			array("story_title"),
			"story_id"
		),
		"activatedate" => array(
			"story",
			array("story_activate_tstamp"),
			"story_id"
		),
		"deactivatedate" => array(
			"story",
			array("story_deactivate_tstamp"),
			"story_id"
		),
		"active" => array(
			"story",
			array("story_active"),
			"story_id"
		),
		
		"??????" => array(
			"story",
			array("story_order"),
			"story_id"
		),
		"url" => array(
			"story
				INNER JOIN
			media
				ON FK_media = media_id",
			array("media_id"),
			"story_id"
		),
		"locked" => array(
			"story",
			array("story_locked"),
			"story_id"
		),
		"editedby" => array(
			"story",
			array("FK_updatedby"),
			"story_id"
		),
		"editedtimestamp" => array(
			"story",
			array("story_updated_tstamp"),
			"story_id"
		),
		"addedby" => array(
			"story",
			array("FK_createdby"),
			"story_id"
		),
		"addedtimestamp" => array(
			"story",
			array("story_created_tstamp"),
			"story_id"
		),
		"discuss" => array(
			"story",
			array("story_discussable"),
			"story_id"
		),
		"category" => array(
			"story",
			array("story_category"),
			"story_id"
		),
		"texttype" => array(
			"story",
			array("story_text_type"),
			"story_id"
		),

		"discussions" => array(
			"story
				INNER JOIN
			 discussion
			 	ON FK_story = story_id
			",
			array("discussion_id"),
			"story_id"
		),
		"shorttext" => array(
			"story",
			array("story_text_short"),
			"story_id"
		),
		"longertext" => array(
			"story",
			array("story_text_long"),
			"story_id"
		)

	);

	var $_table = "story";
	
	
	function story($insite,$insection,$inpage,$id=0) {
		$this->owning_site = $insite;
		$this->owning_section = $insection;
		$this->owning_page = $inpage;
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->data[section_id] = $insection;
		$this->data[page_id] = $inpage;
		$this->init();
		$this->data[type] = "story";
	}
	
	function init($formdates=0) {
		if (!is_array($this->data)) $this->data = array();
		$this->data[title] = "";
		$this->data[activatedate] = $this->data[deactivatedate] = "0000-00-00";
		$this->data[shorttext] = $this->data[longertext] = "";
/* 		$this->data[discuss] = 0; */
		$this->data[texttype] = "text";
		$this->data[category] = "";
		$this->data[url] = "http://";
		$this->data[locked] = 0;
		if ($this->id) $this->fetchFromDB();
		if ($formdates) $this->initFormDates();
	}
	
	function getFirst($length) {
		if ($this->getField("type") == "image" && $this->getField("shorttext") == "")
			return "Image";
		else {
			$text = $this->getField("shorttext");
			$text = strip_tags($text);
			if (strlen($text) <= $length) return $text;
		
			$text = substr($text,0,$length)."...";
			return $text;
		}
	}


	function addDiscussion($id) {
		if (!$this->getField("discussions")) $this->data[discussions] = array();
		array_push($this->data[discussions],$id);
		$this->changed[discussions] = 1;
	}
	
/******************************************************************************
 * doesn't seem to acctually delete discussions! look into this!!!!!!
 ******************************************************************************/
	function delDiscussion($id) {
		if (!$this->getField("discussions")) return;
		$d = array();
		foreach ($this->data[discussions] as $i) {
			if ($i != $id) $d[] = $i;
		}
		$this->data[discussions] = $d;
		$this->changed[discussiosn] = 1;
	}
	
	function delete($deleteFromParent=0) {	// delete from db
		if (!$this->id) return false;
		if ($deleteFromParent) {
			$parentObj = new page ($this->owning_site,$this->owning_section,$this->owning_page);
			$parentObj->fetchDown();
			/* print "<br>delStory - ".$this->id."<br>"; */
			$parentObj->delStory($this->id);
			$parentObj->updateDB();
		} else {
			/******************************************************************************
			 * should probably delete discussions too! Look into this!!!!
			 ******************************************************************************/
			$query = "DELETE FROM story WHERE id=".$this->id;
			db_query($query);

			$query = "DELETE FROM story WHERE id=".$this->id."; ";
			db_query($query);
			$query = "DELETE FROM permission WHERE FK_scope_id=".$this->id." AND permission_scope_type='story';";
			db_query($query);
			
			$this->clearPermissions();
			$this->updatePermissionsDB();
		}
	}
	
	function fetchUp() {
		if (!$this->fetchedup) {
			$this->owningSiteObj = new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB();
			$this->owningSectionObj = new section($this->owning_site,$this->owning_section);
			$this->owningSectionObj->fetchFromDB();
			$this->owningPageObj = new page($this->owning_site,$this->owning_section,$this->owning_page);
			$this->owningPageObj->fetchFromDB();
			$this->fetchedup = 1;
		}
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
			if (!$this->tobefetched || $full) $this->fetchFromDB(0,$full);
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0,$force=0) {
		if ($id) $this->id = $id;
		if ($this->id) {
			$this->tobefetched=1;
			$this->id = $this->getField("id");
			if ($force) {
				foreach ($this->_allfields as $f) $this->getField($f);
			}
/* 			$query = "select * from stories where id=".$this->id." limit 1"; */
/* 			$this->data = db_fetch_assoc(db_query($query)); */
/* 			if (is_array($this->data)) { */
/* 				$this->fetched = 1; */
/* 				$this->buildPermissionsArray(); */
/* 				$this->data[shorttext] = urldecode($this->data[shorttext]); */
/* 				$this->data[longertext] = urldecode($this->data[longertext]); */
/* 				$this->parseMediaTextForEdit("shorttext"); */
/* 				$this->parseMediaTextForEdit("longertext"); */
/* 				 */
/* 				return true; */
/* 			} */
		}
		return $this->id;
	}
	
	function updateDB($down=0) {
		if ($this->changed) {
			$this->parseMediaTextForDB("shorttext");
			$this->parseMediaTextForDB("longertext");
			$a = $this->createSQLArray();
			$a[] = $this->_datafields[editedby][1][0]."=".$_SESSION[aid];
//			$a[] = "editedtimestamp=NOW()";  // no need to do this anymore, MySQL will update the timestamp automatically
			$query = "UPDATE page SET ".implode(",",$a)." WHERE page_id=".$this->id;
			print "<pre>Page->UpdateDB: $query<br>";
			db_query($query);
			print mysql_error()."<br>";
			print_r($this->data['stories']);
			print "</pre>";
			
			// the hard step: update the fields in the JOIN tables
			
			// Urls are now stored in the media table
			if ($this->changed[url]) {
				 
			}
		}
		
// REVISE THIS =================================================================
// REVISE THIS =================================================================
// REVISE THIS =================================================================
		// update permissions
//		$this->updatePermissionsDB();
// REVISE THIS =================================================================
// REVISE THIS =================================================================
// REVISE THIS =================================================================
		
		// add log entry, now handled elsewhere
/* 		log_entry("edit_story",$this->owning_site,$this->owning_section,$this->owning_page,"$_SESSION[auser] edited content id ".$this->id." in site ".$this->owning_site); */

		return true;
	}
	
	function insertDB($down=0,$newsite=null,$newsection=0,$newpage=0,$removeOrigional=0,$keepaddedby=0) {
		$origsite = $this->owning_site;
		$origid = $this->id;
		if ($newsite) $this->owning_site = $newsite;
		if ($newsection) $this->owning_section = $newsection;
		if ($newpage) $this->owning_page = $newpage;
		
		if (!isset($this->owningSiteObj)) $this->owningSiteObj = new site($this->owning_site);
		if (!isset($this->owningSectionObj)) $this->owningSectionObj = new section($this->owning_site,$this->owning_section);
		if (!isset($this->owningPageObj)) $this->owningPageObj = new page($this->owning_site,$this->owning_section,$this->owning_page);
		
		// if moving to a new site, copy the media
		if ($origsite != $this->owning_site && $down) {
			$images = array();
			if ($this->getField("type") == "image" || $this->getField("type") == "file") {
				$media_id = $this->getField("longertext");
				$this->setField("longertext",copy_media($media_id,$newsite));
			} else if ($this->getField("type") == "story") {
				$ids = segue::getMediaIDs("shorttext");
				segue::replaceMediaIDs($ids,"shorttext",$newsite);
				$ids = segue::getMediaIDs("longertext");
				segue::replaceMediaIDs($ids,"longertext",$newsite);
			}
		}
		
		$this->parseMediaTextForDB("shorttext");
		$this->parseMediaTextForDB("longertext");
		
		$a = $this->createSQLArray(1);
		if (!$keepaddedby) {
			$a[] = $this->_datafields[addedby][1][0]."=".$_SESSION[aid];
			$a[] = $this->_datafields[addedtimestamp][1][0]."=NOW()";
		} else {
			$a[] = $this->_datafields[addedby][1][0]."=".$this->getField('addeby');
			$a[] = $this->_datafields[addedtimestamp][1][0]."='".$this->getField("addedtimestamp")."'";
		}

		$query = "INSERT INTO stories SET ".implode(",",$a);
/* 		print $query."<br>"; //debug */
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->fetchUp();
/* 		$this->owningPageObj->addStory($this->id); */
		if ($removeOrigional) $this->owningPageObj->delStory($origid,0);
		$this->owningPageObj->updateDB();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
/* 		log_entry("add_story",$this->owning_site,$this->owning_section,$this->id,"$_SESSION[auser] added content id ".$this->id." to site ".$this->owning_site); */
		
		// insert down
/* 		if ($down && $this->fetcheddown) { */
/* 			foreach ($this->stories as $i=>$o) $o->insertDB(1,$this->owning_site,$this->owning_section,$this->id,$keepaddedby); */
/* 		} */
		return true;
	}
	
	function createSQLArray($all=0) {
		$d = $this->data;
		$a = array();

		if (!isset($this->owningSiteObj)) $this->owningSiteObj = new site($this->owning_site);
		if ($all) $a[] = $this->_datafields[site_id][1][0]."='".$this->owningSiteObj->getField("id")."'";
		if (!isset($this->owningSectionObj)) $this->owningSectionObj = new section($this->owning_site,$this->owning_section);
		if ($all) $a[] = $this->_datafields[section_id][1][0]."='".$this->owningSectionObj->getField("id")."'";
		if (!isset($this->owningPageObj)) $this->owningPageObj = new page($this->owning_site,$this->owning_section,$this->owning_page);
		if ($all) $a[] = $this->_datafields[page_id][1][0]."='".$this->owningPageObj->getField("id")."'";
		
//		if ($this->id && ($all || $this->changed[pages])) { //I belive we may always need to fix the order.
		if ($this->id) {
			$orderkeys = array_keys($this->owningPageObj->getField("stories"),$this->id);
			$a[] = "story_order=".$orderkeys[0];
		} else {
			$a[] = "story_order=".count($this->owningPageObj->getField("stories"));
		}
		
		if ($all || $this->changed[title]) $a[] = $this->_datafields[title][1][0]."='".addslashes($d[title])."'";
		if ($all || $this->changed[activatedate]) $a[] = $this->_datafields[activatedate][1][0]."='".ereg_replace("-","",$d[activatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[deactivatedate]) $a[] = $this->_datafields[deactivatedate][1][0]."='".ereg_replace("-","",$d[deactivatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[active]) $a[] = $this->_datafields[active][1][0]."='".(($d[active])?1:0)."'";
		if ($all || $this->changed[type]) $a[] = $this->_datafields[type][1][0]."='$d[type]'";
		if ($all || $this->changed[locked]) $a[] = $this->_datafields[locked][1][0]."='".(($d[locked])?1:0)."'";
//		if ($all || $this->changed[stories]) $a[] = "stories='".encode_array($d[stories])."'";
//		if ($all || $this->changed[url]) $a[] = "url='$d[url]'";
		if ($all || $this->changed[discuss]) $a[] = $this->_datafields[discuss][1][0]."='".(($d[discuss])?1:0)."'";
		if ($all || $this->changed[texttype]) $a[] = $this->_datafields[texttype][1][0]."='$d[texttype]'";
		if ($all || $this->changed[category]) $a[] = $this->_datafields[category][1][0]."='$d[category]'";
		if ($all || $this->changed[shorttext]) $a[] = $this->_datafields[shorttext][1][0]."='".urlencode($d[shorttext])."'";
		if ($all || $this->changed[longertext]) $a[] = $this->_datafields[longertext][1][0]."='".urlencode($d[longertext])."'";
//		if ($all || $this->changed[discussions]) $a[] = "discussions='".encode_array($d[discussions])."'";
		
		return $a;
	}
}
