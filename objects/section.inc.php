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
			"section",
			array("FK_site"),
			"section_id"
		),
		"type" => array(
			"section",
			array("section_type"),
			"section_id"
		),
		"title" => array(
			"section",
			array("section_title"),
			"section_id"
		),
		"activatedate" => array(
			"section",
			array("section_activate_tstamp"),
			"section_id"
		),
		"deactivatedate" => array(
			"section",
			array("section_deactivate_tstamp"),
			"section_id"
		),
		"active" => array(
			"section",
			array("section_active"),
			"section_id"
		),		
		"??????" => array(
			"section",
			array("section_order"),
			"section_id"
		),
		"url" => array(
			"section
				INNER JOIN
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
				ON FK_createdby = user_id",
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
	
						


	function section($insite,$id=0) {
		$this->owning_site = $insite;
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->init();
		$this->data[type] = "section";
	}
	
	function delete($deleteFromParent=0) {	// delete from db
		if (!$this->id) return false;
		if ($deleteFromParent) {
			$parentObj = new site ($this->owning_site);
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

			$query = "DELETE FROM section WHERE id=".$this->id."; ";
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
			$this->owningSiteObj = new site($this->owning_site);
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
			$page = new page($this->owning_site,$this->id,$id);
			$page->delete();
		}
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
/* 			print "-->section fetchdown ".$this->id."<BR>"; */
			if (!$this->tobefetched || $full) $this->fetchFromDB(0,$full);
			foreach ($this->getField("pages") as $p) {
				$this->pages[$p] = new page($this->owning_site,$this->id,$p);
				$this->pages[$p]->fetchDown($full);
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0,$force=0) {
		if ($id) $this->id = $id;
		$this->tobefetched=1;
		$this->id = $this->getField("id");
/* 		if ($this->id) { */
/* 			$query = "select * from sections where id=".$this->id." limit 1"; */
/* 			$this->data = db_fetch_assoc(db_query($query)); */
/* 			if (is_array($this->data)) { */
/* 				$this->fetched = 1; */
/* 				$this->buildPermissionsArray(); */
/* 				 */
/* 				$this->data[pages] = decode_array($this->data[pages]); */
/* 				 */
/* 				return true; */
/* 			} */
/* 		} */
		if ($force && $this->id) {
			foreach ($this->_allfields as $f) $this->getField($f);
		}	
		return $this->id;
	}
	
	function updateDB($down=0) {
		if (count($this->changed)) {
			$a = $this->createSQLArray();
			$a[] = $this->_datafields[editedby][1][0]."=".$_SESSION[aid];
//			$a[] = "editedtimestamp=NOW()";  // no need to do this anymore, MySQL will update the timestamp automatically
			$query = "UPDATE section SET ".implode(",",$a)." WHERE section_id=".$this->id;
			print "<pre>Section->UpdateDB: $query<br>";
			db_query($query);
			print mysql_error()."<br>";
			print_r($this->data['pages']);
			print "</pre>";
			
			// the hard step: update the fields in the JOIN tables
			
			// Urls are now stored in the media table
			if ($this->changed[url]) {
				 
			}
						
			// now update all the page ids in the children, if the latter have changed
			if ($this->changed[pages]) {
				// first, a precautionary step: reset the parent of every section that used to have this site object as the parent
				// we do this, because we might have removed a certain section from the array of sections of a site object
				$query = "UPDATE page SET FK_section=0 WHERE FK_section=".$this->id;
				db_query($query);
				
				// now, update all pages
				foreach ($this->data['pages'] as $k => $v) {
					$query = "UPDATE page SET FK_section=".$this->id.", page_order=$k WHERE page_id=".$v;
					db_query($query);
				}
				
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

		// add log entry
/* 		log_entry("edit_section",$this->owning_site,$this->id,"","$_SESSION[auser] edited section id ".$this->id." in site ".$this->owning_site); */
		
		// update down
		if ($down) {
			if ($this->fetcheddown && $this->pages) {
				foreach ($this->pages as $i=>$o) $o->updateDB(1);
			}
		}
		return true;
	}
	
	function insertDB($down=0,$newsite=null,$removeOrigional=0,$keepaddedby=0) {
		$origsite = $this->owning_site;
		$origid = $this->id;
		if ($newsite) {
			$this->owning_site = $newsite;
		}
		
		if (!isset($this->owningSiteObj)) $this->owningSiteObj = new site($this->owning_site);
		
		$a = $this->createSQLArray(1);
		if (!$keepaddedby) {
			$a[] = $this->_datafields[addedby][1][0]."=".$_SESSION[aid];
			$a[] = $this->_datafields[addedtimestamp][1][0]."=NOW()";
		} else {
			$a[] = $this->_datafields[addedby][1][0]."=".$this->getField('addeby');
			$a[] = $this->_datafields[addedtimestamp][1][0]."='".$this->getField("addedtimestamp")."'";
		}

		$query = "INSERT INTO section SET ".implode(",",$a);
		print "<BR>query = $query<BR>";
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->fetchUp(1);

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
			foreach ($this->pages as $i=>$o) $o->insertDB(1,$this->owning_site,$this->id,1,$keepaddedby);
		}
		return true;
	}
	
	function createSQLArray($all=0) {
		$d = $this->data;
		$a = array();
		
		if (!isset($this->owningSiteObj)) $this->owningSiteObj = new site($this->owning_site);
		if ($all) $a[] = $this->_datafields[site_id][1][0]."='".$this->owningSiteObj->getField("id")."'";
		
//		if ($this->id && ($all || $this->changed[sections])) { //I belive we may always need to fix the order.
		if ($this->id) {
			$orderkeys = array_keys($this->owningSiteObj->getField("sections"),$this->id);
			$a[] = "section_order=".$orderkeys[0];
		} else {
			$a[] = "section_order=".count($this->owningSiteObj->getField("sections"));
		}
		
		if ($all || $this->changed[title]) $a[] = $this->_datafields[title][1][0]."='".addslashes($d[title])."'";
		if ($all || $this->changed[activatedate]) $a[] = $this->_datafields[activatedate][1][0]."='".ereg_replace("-","",$d[activatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[deactivatedate]) $a[] = $this->_datafields[deactivatedate][1][0]."='".ereg_replace("-","",$d[deactivatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[active]) $a[] = $this->_datafields[active][1][0]."='".(($d[active])?1:0)."'";
		if ($all || $this->changed[type]) $a[] = $this->_datafields[type][1][0]."='$d[type]'";
//		if ($all || $this->changed[pages]) $a[] = "pages='".encode_array($this->getField("pages"))."'";
//		if ($all || $this->changed[url]) $a[] = $this->_datafields[url][1][0]."='$d[url]'";
		if ($all || $this->changed[locked]) $a[] = $this->_datafields[locked][1][0]."='".(($d[locked])?1:0)."'";
		
		return $a;
	}
}