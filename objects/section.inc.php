<? // object section extends segue

class section extends segue {
	var $pages;
	
	function section($insite,$id=0) {
		$this->pages = array();
		$this->data = array();
		$this->owning_site = $insite;
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->data[title] = "";
		$this->data[activatedate] = $this->data[deactivatedate] = "0000-00-00";
		$this->data[active] = 1;
		$this->data[url] = "http://";
		$this->data[locked] = 0;
		$this->data[showcreator] = 0;
		$this->data[showdate] = 0;
		$this->data[archiveby] = "none";
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
	
	function updateDB() {
		if ($this->changed) {
			$a = $this->createSQLArray();
			$a[] = "editedby='$_SESSION[auser]'";
			$a[] = "editedtimestamp = NOW()";
			$query = "update sections set ".implode(",",$a)." where id=".$this->id;
			db_query($query);
		}
		
		// update permissions
		$this->updatePermissionsDB();
		return true;
	}
	
	function insertDB() {
		$a = $this->createSQLArray();
		$a[] = "addedby='$_SESSION[auser]'";
		$a[] = "addedtimestamp = NOW()";
		$query = "insert into sections set ".implode(",",$a);
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->owningSiteObj = new site($this->owning_site);
		$this->owningSiteObj->fetchFromDB();
		$this->owningSiteObj->addSection($this->id);
		$this->owningSiteObj->updateDB();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		return true;
	}
	
	function createSQLArray() {
		$d = $this->data;
		$a = array();
		
		$a[] = "title='$d[title]'";
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