<? /* $Id$ */

class story extends segue {
	
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
		$this->data[discuss] = 0;
		$this->data[texttype] = "text";
		$this->data[category] = "";
		$this->data[url] = "http://";
		$this->data[locked] = 0;
		if ($this->id) $this->fetchFromDB();
		if ($formdates) $this->initFormDates();
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from stories where id=".$this->id;
		db_query($query);
		
		$this->clearPermissions();
		$this->updatePermissionsDB();
	}
	
	function addStory($id) {
		if (!is_array($this->data[stories])) $this->data[stories] = array();
		array_push($this->data[stories],$id);
		$this->changed = 1;
	}
	
	function delStory($id) {
		$d = array();
		foreach ($this->data[stories] as $s)
			if ($s != $id) $d[]=$s;
		$this->data[stories] = $d;
		$story = new story($this->owning_site,$this->owning_section,$this->owning_page,$id);
		$story->delete();
		$this->changed=1;
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
	
	function fetchDown() {
		if (!$this->fetcheddown) {
			if (!$this->fetched) $this->fetchFromDB();
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0) {
		if ($id) $this->id = $id;
		if ($this->id) {
			$query = "select * from stories where id=".$this->id." limit 1";
			$this->data = db_fetch_assoc(db_query($query));
			if (is_array($this->data)) {
				$this->fetched = 1;
				$this->buildPermissionsArray();
				$this->data[shorttext] = urldecode($this->data[shorttext]);
				$this->data[longertext] = urldecode($this->data[longertext]);
				$this->parseMediaTextForEdit("shorttext");
				$this->parseMediaTextForEdit("longertext");
				
				return true;
			}
		}
		return false;
	}
	
	function updateDB($down=0) {
		if ($this->changed) {
			$this->parseMediaTextForDB("shorttext");
			$this->parseMediaTextForDB("longertext");
			$a = $this->createSQLArray();
			$a[] = "editedby='$_SESSION[auser]'";
			$a[] = "editedtimestamp = NOW()";
			$query = "update stories set ".implode(",",$a)." where id=".$this->id;
			db_query($query);
		}
		
		// update permissions
		$this->updatePermissionsDB();
		
		// add log entry
		log_entry("edit_story",$this->owning_site,$this->owning_section,$this->owning_page,"$_SESSION[auser] edited content id ".$this->id." in site ".$this->owning_site);

		return true;
	}
	
	function insertDB($down=0,$newsite=null,$newsection=0,$newpage=0) {
		if ($newsite) $this->owning_site = $newsite;
		if ($newsection) $this->owning_section = $newsection;
		if ($newpage) $this->owning_page = $newpage;
		$this->parseMediaTextForDB("shorttext");
		$this->parseMediaTextForDB("longertext");
		$a = $this->createSQLArray();
		$a[] = "addedby='$_SESSION[auser]'";
		$a[] = "addedtimestamp = NOW()";
		$query = "insert into stories set ".implode(",",$a);
		print $query; //debug
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->fetchUp();
		$this->owningPageObj->addStory($this->id);
		$this->owningPageObj->updateDB();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
		log_entry("add_story",$this->owning_site,$this->owning_section,$this->id,"$_SESSION[auser] added content id ".$this->id." to site ".$this->owning_site);
		
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
		$a[] = "page_id=".$this->owning_page;
		$a[] = "activatedate='$d[activatedate]'";
		$a[] = "deactivatedate='$d[deactivatedate]'";
		$a[] = "active=".(($d[active])?1:0);
		$a[] = "type='$d[type]'";
		$a[] = "url='$d[url]'";
		$a[] = "discuss=$d[discuss]";
		$a[] = "texttype='$d[texttype]'";
		$a[] = "category='$d[category]'";
		$a[] = "shorttext='".urlencode($d[shorttext])."'";
		$a[] = "longertext='".urlencode($d[longertext])."'";
		$a[] = "locked=$d[locked]";
		
		return $a;
	}
}