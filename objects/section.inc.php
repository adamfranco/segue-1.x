<? /* $Id$ */

class section extends segue {
	var $pages;
	var $_allfields = array("site_id","title","editedtimestamp","addedby","editedby","addedtimestamp",
						"activatedate","deactivatedate","active","pages","type",
						"url","locked");
	
	// fields listed in $_datafields are stored in the database.
	// the first element is the table join syntax required to pull the data.
	// the second element is an array of the database fields we will be selecting
	// the third element is the database field by which we will sort
	
	var $_datafields = array(
		"id" => array(
			"section",
			array("section_id"),
			"section_id"
		),
		"site_id" => array(
			"section
				INNER JOIN
			 site
			 	ON section.FK_site = site.site_id
			 	INNER JOIN
			 slot
				ON site.site_id = slot.FK_site",
			array("slot_name"),
			"section_id"
		),
		"type" => array(
			"section",
			array("section_display_type"),
			"section_id"
		),
		"title" => array(
			"section",
			array("section_title"),
			"section_id"
		),
		"activatedate" => array(
			"section",
			array("DATE_FORMAT(section_activate_tstamp, '%Y-%m-%d')"),
			"section_id"
		),
		"deactivatedate" => array(
			"section",
			array("DATE_FORMAT(section_deactivate_tstamp, '%Y-%m-%d')"),
			"section_id"
		),
		"active" => array(
			"section",
			array("section_active"),
			"section_id"
		),		
		"url" => array(
			"section
				LEFT JOIN
			 media
				ON FK_media = media_id",
			array("media_tag"),
			"section_id"
		),
		"locked" => array(
			"section",
			array("section_locked"),
			"section_id"
		),
		"editedby" => array(
			"section
				INNER JOIN
			user
				ON FK_updatedby = user_id",
			array("user_uname"),
			"section_id"
		),
		"editedtimestamp" => array(
			"section",
			array("section_updated_tstamp"),
			"section_id"
		),
		"addedby" => array(
			"section
				INNER JOIN
			user
				ON FK_createdby = user_id",
			array("user_uname"),
			"section_id"
		),
		"addedtimestamp" => array(
			"section",
			array("section_created_tstamp"),
			"section_id"
		),
		"pages" => array(
			"section
				INNER JOIN
			page
				ON section_id = FK_section",
			array("page_id"),
			"page_order"
		)
	);

	var $_table = "section";
	
						


	function section($insite,$id=0,&$siteObj) {
		$this->owning_site = $insite;
		$this->owningSiteObj = &$siteObj;
		$this->fetchedup = 1;
		
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->init();
		$this->data[type] = "section";
		$this->data[id] = $id;
	}
	
	function delete($deleteFromParent=0) {	// delete from db
		if (!$this->id) return false;
		if ($deleteFromParent) {
			$parentObj =& new site ($this->owning_site);
			$parentObj->fetchDown();
			$parentObj->delSection($this->id);
			$parentObj->updateDB();
		} else {
			// remove pages
			$this->fetchDown();
			if ($this->pages) {
				foreach ($this->pages as $p=>$o) {
					$o->delete();
				}
			}

			$query = "DELETE FROM section WHERE section_id=".$this->id;
			db_query($query);
			$query = "DELETE FROM permission WHERE FK_scope_id=".$this->id." AND permission_scope_type='section';";
			db_query($query);
			
			
			$this->clearPermissions();
			$this->updatePermissionsDB();
		}
	}
	
	function init($formdates=0) {
		$this->pages = array();
		if (!is_array($this->data)) $this->data = array();
		$this->data[title] = "";
		$this->data[activatedate] = $this->data[deactivatedate] = "0000-00-00";
		$this->data[active] = 1;
		$this->data[url] = "http://";
		$this->data[locked] = 0;
		if ($this->id) $this->fetchFromDB();
		if ($formdates) $this->initFormDates();
	}
	
	function fetchUp($full=0) {
		if (!$this->fetchedup || $full) {
/* 			print "<br>Fetching Up<br>"; */
			$this->owningSiteObj =& new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB(1);
//			$this->owningSiteObj->buildPermissionsArray(1);
			$this->fetchedup = 1;
		}
	}
	
	function addPage($id) {
		if (!is_array($this->getField("pages"))) $this->data[pages] = array();
		array_push($this->data["pages"],$id);
		$this->changed[pages] = 1;
	}
	
	function delPage($id,$delete=1) {
		$d = array();
		foreach ($this->getField("pages") as $p)
			if ($p != $id) $d[]=$p;
		$this->data[pages] = $d;
		$this->changed[pages]=1;
		if ($delete) {
			$page =& new page($this->owning_site,$this->id,$id,&$this);
			$page->delete();
		}
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
/* 			print "-->section fetchdown ".$this->id."<BR>"; */
			if (!$this->tobefetched || $full) $this->fetchFromDB(0,$full);
			foreach ($this->getField("pages") as $p) {
				$this->pages[$p] =& new page($this->owning_site,$this->id,$p,&$this);
				$this->pages[$p]->fetchDown($full);
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0,$force=0) {
		if ($id) $this->id = $id;
		$this->tobefetched=1;

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
	section_display_type AS type, section_title AS title, DATE_FORMAT(section_activate_tstamp, '%Y-%m-%d') AS activatedate, DATE_FORMAT(section_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
	section_active AS active, section_locked AS locked, section_updated_tstamp AS editedtimestamp,
	section_created_tstamp AS addedtimestamp,
	user_createdby.user_uname AS addedby, user_updatedby.user_uname AS editedby, slot_name as site_id,
	media_tag AS url
FROM 
	section
		INNER JOIN
	user AS user_createdby
		ON section.FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON section.FK_updatedby = user_updatedby.user_id
		INNER JOIN
	 site
		ON section.FK_site = site.site_id
		INNER JOIN
	 slot
		ON site.site_id = slot.FK_site
		LEFT JOIN
	media
		ON FK_media = media_id
WHERE section_id = ".$this->id;

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
	page_id
FROM
	section
		INNER JOIN
	page
		ON section_id = FK_section
WHERE section_id = ".$this->id."
ORDER BY
	page_order
";

			$r = db_query($query);
			$this->data[pages] = array();
			while ($a = db_fetch_assoc($r))
				$this->data[pages][] = $a[page_id];

			$this->fetched[pages] = 1;
		}
		
		return $this->id;

	}
	
	function updateDB($down=0, $force=0, $keepEditHistory = FALSE) {
		if (count($this->changed)) {
			$a = $this->createSQLArray();
			
			if ($keepEditHistory) {
				$a[] = $this->_datafields[editedtimestamp][1][0]."='".$this->getField("editedtimestamp")."'";
			} else
				$a[] = "FK_updatedby=".$_SESSION[aid];
			
			$query = "UPDATE section SET ".implode(",",$a)." WHERE section_id=".$this->id;
/* 			print "<pre>Section->UpdateDB: $query<br>"; */
			db_query($query);
			
/* 			print mysql_error()."<br>"; */
/* 			print_r($this->data['pages']); */
/* 			print "</pre>"; */
			
			// the hard step: update the fields in the JOIN tables
			
			if ($this->changed[url]) {
				// Urls are now stored in the media table
				// get id of media item
				$query = "
SELECT
	FK_media
FROM
	section
WHERE
	section_id = ".$this->id;

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
						
/* 			// now update all the page ids in the children, if the latter have changed */
/* 			if ($this->changed[pages]) { */
/* 				// first, a precautionary step: reset the parent of every section that used to have this site object as the parent */
/* 				// we do this, because we might have removed a certain section from the array of sections of a site object */
/* 				$query = "UPDATE page SET FK_section=0 WHERE FK_section=".$this->id; */
/* 				db_query($query); */
/* 				 */
/* 				// now, update all pages */
/* 				foreach ($this->data['pages'] as $k => $v) { */
/* 					$query = "UPDATE page SET FK_section=".$this->id.", page_order=$k WHERE page_id=".$v; */
/* 					db_query($query); */
/* 				} */
/* 				 */
/* 			} */			
		}
		
		// update permissions
		$this->updatePermissionsDB($force);

		// add log entry
/* 		log_entry("edit_section",$this->owning_site,$this->id,"","$_SESSION[auser] edited section id ".$this->id." in site ".$this->owning_site); */
		
		// update down
		if ($down) {
			if ($this->fetcheddown && $this->pages) {
				foreach (array_keys($this->pages) as $k=>$i) $this->pages[$i]->updateDB($down, $force, $keepEditHistory);
			}
		}
		return true;
	}
	
	function insertDB($down=0,$newsite=null,$removeOrigional=0,$keepaddedby=0) {
		$origsite = $this->owning_site;
		$origid = $this->id;
		if ($newsite) {
			$this->owning_site = $newsite;
			unset($this->owningSiteObj);
		}
		
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
		
		$query = "INSERT INTO section SET ".implode(",",$a);
		db_query($query);
		
		$this->id = lastid();
		
		// See if there is a site hash (meaning that we are being copied).
		// If so, try to match our id with the hash entry for 'NEXT'.
		if ($GLOBALS['__site_hash']['sections'] 
			&& $oldId = array_search('NEXT', $GLOBALS['__site_hash']['sections']))
		{
			$GLOBALS['__site_hash']['sections'][$oldId] = $this->id;
		}
		
//		$this->fetchUp(1);

/* 		print "<br>remove origionl: $removeOrigional<br>"; */
		if ($removeOrigional) $this->owningSiteObj->delSection($origid,0);
/* 		print "<pre>this->owningsiteobject: "; print_r($this->owningSiteObj); print "</pre>"; */
		
		$this->owningSiteObj->updateDB();
		
		// add new permissions entry.. force update
//		$this->updatePermissionsDB(1);	// We shouldn't need this because new sections will just
										//inherit the permissions of their parent sites
		
		// add log entry
/* 		log_entry("add_section",$this->owning_site,$this->id,"","$_SESSION[auser] added section id ".$this->id." to site ".$this->owning_site); */
		
		// insert down
		if ($down && $this->fetcheddown && $this->pages) {
			foreach (array_keys($this->pages) as $k=>$i) {
				// Mark our Id as the next one to set
				if (is_array($GLOBALS['__site_hash']['pages']))
					$GLOBALS['__site_hash']['pages'][$i] = 'NEXT';
					
				$this->pages[$i]->id = 0;	// createSQLArray uses this to tell if we are inserting or updating
				$this->pages[$i]->insertDB(1,$this->owning_site,$this->id,1,$keepaddedby);
			}
		}
		return true;
	}
	
	function createSQLArray($all=0) {
		$d = $this->data;
		$a = array();
		
		$this->fetchUp();
		
		if ($all) $a[] = "FK_site='".$this->owningSiteObj->id."'";
		
/* 		print "<pre>\n\nXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\n"; */
/* 		print "owning_site=".$this->owning_site."\nOwningSiteObj: "; */
/* 		print_r ($this->owningSiteObj); */
/* 		print "\nXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\n\n</pre>"; */
/* 		print "<br>Sections = <pre>"; */
/* 		print_r($this->owningSiteObj->getField("sections")); */
/* 		print "</pre>"; */
		
//		if ($this->id && ($all || $this->changed[sections])) { //I belive we may always need to fix the order.

		
		if ($this->id) {
			$orderkeys = array_keys($this->owningSiteObj->getField("sections"),$this->id);
			$a[] = "section_order=".$orderkeys[0];
		} else {
/* 			print "<br>No id, inserting at end of other sections. Count=".count($this->owningSiteObj->getField("sections")); */
			$a[] = "section_order=".count($this->owningSiteObj->getField("sections"));
		}
		
		if ($all || $this->changed[title]) $a[] = $this->_datafields[title][1][0]."='".addslashes($d[title])."'";
		if ($all || $this->changed[activatedate]) $a[] = "section_activate_tstamp ='".ereg_replace("-","",$d[activatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[deactivatedate]) $a[] = "section_deactivate_tstamp ='".ereg_replace("-","",$d[deactivatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[active]) $a[] = $this->_datafields[active][1][0]."='".(($d[active])?1:0)."'";
		if ($all || $this->changed[type]) $a[] = $this->_datafields[type][1][0]."='$d[type]'";
//		if ($all || $this->changed[pages]) $a[] = "pages='".encode_array($this->getField("pages"))."'";
//		if ($all || $this->changed[url]) $a[] = $this->_datafields[url][1][0]."='$d[url]'";
		if ($all || $this->changed[locked]) $a[] = $this->_datafields[locked][1][0]."='".(($d[locked])?1:0)."'";
		
		return $a;
	}
}
