<? /* $Id$ */

class section extends segue {
	var $pages;
	
	function section($insite,$id=0) {
		$this->owning_site = $insite;
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->init();
		$this->data[type] = "section";
	}
	
	function delete() {	// delete from db
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
		if (!is_array($this->data[pages])) $this->data[pages] = array();
		array_push($this->data[pages],$id);
		$this->changed = 1;
	}
	
	function delPage($id) {
		$d = array();
		foreach ($this->data[pages] as $p)
			if ($p != $id) $d[]=$p;
		$this->data[pages] = $d;
		$page = new page($this->owning_site,$this->id,$id);
		$page->delete();
		$this->changed=1;
	}
	
	function fetchDown() {
		if (!$this->fetcheddown) {
			if (!$this->fetched) $this->fetchFromDB();
			foreach ($this->data[pages] as $p) {
				$this->pages[$p] = new page($this->owning_site,$this->id,$p);
				$this->pages[$p]->fetchDown();
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0) {
		if ($id) $this->id = $id;
		if ($this->id) {
			$query = "select * from sections where id=".$this->id." limit 1";
			$this->data = db_fetch_assoc(db_query($query));
			if (is_array($this->data)) {
				$this->fetched = 1;
				$this->buildPermissionsArray();
				
				$this->data[pages] = decode_array($this->data[pages]);
				
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
	
	function insertDB($down=0,$newsite=null) {
		if ($newsite) $this->owning_site = $newsite;
		$a = $this->createSQLArray();
		$a[] = "addedby='$_SESSION[auser]'";
		$a[] = "addedtimestamp = NOW()";
		$query = "insert into sections set ".implode(",",$a);
		print $query; //debug
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->fetchUp();
		$this->owningSiteObj->addSection($this->id);
		$this->owningSiteObj->updateDB();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
		log_entry("add_section",$this->owning_site,$this->id,"","$_SESSION[auser] added section id ".$this->id." to site ".$this->owning_site);
		
		// insert down
		if ($down && $this->fetcheddown) {
			foreach ($this->pages as $i=>$o) $o->insertDB(1,$this->owning_site,$this->id);
		}
		return true;
	}
	
	function createSQLArray() {
		$d = $this->data;
		$a = array();
		
		$a[] = "title='".addslashes($d[title])."'";
		$a[] = "site_id='".$this->owning_site."'";
		$a[] = "activatedate='$d[activatedate]'";
		$a[] = "deactivatedate='$d[deactivatedate]'";
		$a[] = "active=".(($d[active])?1:0);
		$a[] = "type='$d[type]'";
		$a[] = "pages='".encode_array($this->data[pages])."'";
		$a[] = "url='$d[url]'";
		
		return $a;
	}
}