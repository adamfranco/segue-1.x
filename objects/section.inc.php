<? /* $Id$ */

class section extends segue {
	var $pages;
	var $_allfields = array("site_id","title","editedtimestamp","addedby","editedby","addedtimestamp",
						"activatedate","deactivatedate","active","pages","type",
						"url","locked");
	
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
			$query = "delete from sections where id=".$this->id;
			db_query($query);
			
			// remove pages
			$this->fetchDown();
			foreach ($this->pages as $p=>$o) {
				$o->delete();
			}
			
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
	
	function fetchUp() {
		if (!$this->fetchedup) {
			$this->owningSiteObj = new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB();
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
			foreach ($_allfields as $f) $this->getField($f);
		}	
		return $this->id;
	}
	
	function updateDB($down=0) {
		if (count($this->changed)) {
			$a = $this->createSQLArray();
			$a[] = "editedby='$_SESSION[auser]'";
			$a[] = "editedtimestamp = NOW()";
			$query = "update sections set ".implode(",",$a)." where id=".$this->id;
			db_query($query);
		}
		
		// update permissions
		$this->updatePermissionsDB();
		
		// add log entry
		log_entry("edit_section",$this->owning_site,$this->id,"","$_SESSION[auser] edited section id ".$this->id." in site ".$this->owning_site);
		
		// update down
		if ($down) {
			if ($this->fetcheddown) {
				foreach ($this->pages as $i=>$o) $o->updateDB(1);
			}
		}
		return true;
	}
	
	function insertDB($down=0,$newsite=null,$keepaddedby=0) {
		$origsite = $this->owning_site;
		$origid = $this->id;
		if ($newsite) {
			$this->owning_site = $newsite;
			$this->owningSiteObj = new site($newsite);
		}
		
		$a = $this->createSQLArray(1);
		if (!$keepaddedby) {
			$a[] = "addedby='$_SESSION[auser]'";
			$a[] = "addedtimestamp = NOW()";
		} else {
			$a[] = "addedby='".$this->getField("addedby")."'";
			$a[] = "addedtimestamp='".$this->getField("addedtimestamp")."'";
		}

		$query = "insert into sections set ".implode(",",$a);
		print $query; //debug
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->fetchUp();
		$this->owningSiteObj->addSection($this->id);
		if ($down) $this->owningSiteObj->delSection($origid,0);
		$this->owningSiteObj->updateDB();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
		log_entry("add_section",$this->owning_site,$this->id,"","$_SESSION[auser] added section id ".$this->id." to site ".$this->owning_site);
		
		// insert down
		if ($down && $this->fetcheddown) {
			foreach ($this->pages as $i=>$o) $o->insertDB(1,$this->owning_site,$this->id,$keepaddedby);
		}
		return true;
	}
	
	function createSQLArray($all=0) {
		$d = $this->data;
		$a = array();
		
		if ($all || $this->changed[title]) $a[] = "title='".addslashes($d[title])."'";
		if ($all) $a[] = "site_id='".$this->owning_site."'";
		if ($all || $this->changed[activatedate]) $a[] = "activatedate='$d[activatedate]'";
		if ($all || $this->changed[deactivatedate]) $a[] = "deactivatedate='$d[deactivatedate]'";
		if ($all || $this->changed[active]) $a[] = "active=".(($d[active])?1:0);
		if ($all || $this->changed[type]) $a[] = "type='$d[type]'";
		if ($all || $this->changed[pages]) $a[] = "pages='".encode_array($this->getField("pages"))."'";
		if ($all || $this->changed[url]) $a[] = "url='$d[url]'";
		
		return $a;
	}
}