<? /* $Id$ */

/******************************************************************************
 * page object - handles site pages
 ******************************************************************************/

class page extends segue {
	var $stories;
	
	function page($insite,$insection,$id=0) {
		$this->owning_site = $insite;
		$this->owning_section = $insection;
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->data[section_id] = $insection;
		$this->init();
		$this->data[type] = "page";
	}
	
	function init($formdates=0) {
		$this->pages = array();
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
		$this->data[storyorder] = "";
		if ($this->id) $this->fetchFromDB();
		if ($formdates) $this->initFormDates();
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from pages where id=".$this->id;
		db_query($query);
		
		// remove stories
		$this->fetchDown();
		foreach ($this->stories as $s=>$o) {
			$o->delete();
		}
		
		$this->clearPermissions();
		$this->updatePermissionsDB();
	}
	
	function fetchUp() {
		if (!$this->fetchedup) {
			$this->owningSiteObj = new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB();
			$this->owningSectionObj = new section($this->owning_site,$this->owning_section);
			$this->owningSectionObj->fetchFromDB();
			$this->fetchedup = 1;
		}
	}
	
	function fetchDown() {
		if (!$this->fetcheddown) {
			if (!$this->fetched) $this->fetchFromDB();
			foreach ($this->data[stories] as $s) {
				$this->stories[$s] = new story($this->owning_site,$this->owning_section,$this->id,$s);
				$this->stories[$s]->fetchDown();
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0) {
		if ($id) $this->id = $id;
		if ($this->id) {
			$query = "select * from pages where id=".$this->id." limit 1";
			$this->data = db_fetch_assoc(db_query($query));
			if (is_array($this->data)) {
				$this->fetched = 1;
				$this->buildPermissionsArray();
				
				$this->data[stories] = decode_array($this->data[stories]);
				
				return true;
			}
		}
		return false;
	}
	
	function updateDB($down=0) {
		if ($this->changed) {
			$a = $this->createSQLArray();
			$a[] = "editedby='$_SESSION[auser]'";
			$a[] = "editedtimestamp = NOW()";
			$query = "update pages set ".implode(",",$a)." where id=".$this->id;
			db_query($query);
		}
		
		// update permissions
		$this->updatePermissionsDB();
		
		// add log entry
		log_entry("edit_page",$this->owning_site,$this->owning_section,$this->id,"$_SESSION[auser] edited page id ".$this->id." in site ".$this->owning_site);

		// update down
		if ($down) {
			if ($this->fetcheddown) {
				foreach ($this->stories as $i=>$o) $o->updateDB(1);
			}
		}
		return true;
	}
	
	function insertDB($down=0,$newsite=null,$newsection=0) {
		if ($newsite) $this->owning_site = $newsite;
		if ($newsection) $this->owning_section = $newsection;
		$a = $this->createSQLArray();
		$a[] = "addedby='$_SESSION[auser]'";
		$a[] = "addedtimestamp = NOW()";
		$query = "insert into pages set ".implode(",",$a);
		print $query; //debug
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->fetchUp();
		$this->owningSectionObj->addPage($this->id);
		$this->owningSectionObj->updateDB();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
		log_entry("add_page",$this->owning_site,$this->owning_section,$this->id,"$_SESSION[auser] added page id ".$this->id." to site ".$this->owning_site);
		
		// insert down
		if ($down && $this->fetcheddown) {
			foreach ($this->stories as $i=>$o) $o->insertDB(1,$this->owning_site,$this->owning_section,$this->id);
		}
		return true;
	}
	
	function createSQLArray() {
		$d = $this->data;
		$a = array();
		
		$a[] = "title='".addslashes($d[title])."'";
		$a[] = "site_id='".$this->owning_site."'";
		$a[] = "section_id=".$this->owning_section;
		$a[] = "activatedate='$d[activatedate]'";
		$a[] = "deactivatedate='$d[deactivatedate]'";
		$a[] = "active=".(($d[active])?1:0);
		$a[] = "type='$d[type]'";
		$a[] = "stories='".encode_array($d[stories])."'";
		$a[] = "url='$d[url]'";
		$a[] = "ediscussion=".(($d[ediscussion])?1:0);
		$a[] = "archiveby='$d[archiveby]'";
		$a[] = "showcreator='$d[showcreator]'";
		$a[] = "showdate='$d[showdate]'";
		$a[] = "showhr='$d[showhr]'";
		$a[] = "storyorder='$d[storyorder]'";
		
		return $a;
	}
}