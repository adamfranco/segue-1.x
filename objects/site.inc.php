<? /* $Id$ */

class site extends segue {
	var $sections;
	var $name;
	
	function site($name) {
		$this->name = $name;
		$this->owning_site = $name;
		$this->sections = array();
		$this->data = array();
		
		// initialize the data array
		$this->data[name] = $name;
		$this->data[type] = "personal";
		$this->data[title] = "";
		$this->data[activatedate] = "0000-00-00";
		$this->data[deactivatedate] = "0000-00-00";
		$this->data[active] = 1;
		$this->data[listed] = 1;
		$this->data[theme] = "minimal";
		$this->data[themesettings] = "";
		$this->data[header] = "";
		$this->data[footer] = "";
		$this->data[sections] = array();
	}
	
	function fetchDown() {
		if (!$this->fetcheddown) {
			if (!$this->fetched) $this->fetchFromDB();
			foreach ($this->data[sections] as $s) {
				$this->sections[$s] = new section($this->name,$s);
				$this->sections[$s]->fetchDown();
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB() {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		global $cfg;
		// take this out when appropriate & replace occurences;
		global $uploaddir;
		
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		$query = "select * from sites where name='".$this->name."' limit 1";
		$r = db_query($query);
		$this->data = db_fetch_assoc($r);
		if (is_array($this->data)) {
			$this->fetched = 1;
	//		$this->sections = unserialize(urldecode($this->data['sections']));
			$this->id = $this->data['id'];
			
			// decode appropriate info
			$this->data[sections] = decode_array($this->data[sections]);
			$this->data[header] = stripslashes(urldecode($this->data[header]));
			$this->data[footer] = stripslashes(urldecode($this->data[footer]));
			$this->parseMediaTextForEdit("header");
			$this->parseMediaTextForEdit("footer");
			$this->buildPermissionsArray();
			return 1;
		}
		return false;
	}
	
	function setSiteName($name) {
		if ($this->fetched) { // we are trying to change the name of an existing site!! bad.
			return 0;
		}
		$this->name = $this->owning_site = $name;
		$this->data[name] = $name;
		$this->changed = 1;
		return 1;
	}
	
	function updateDB($down=0) {
		if ($this->changed) {
			$a = $this->createSQLArray();
			$a[] = "editedby='$_SESSION[auser]'";
			$a[] = "editedtimestamp=NOW()";
			$query = "update sites set ".implode(",",$a)." where id=".$this->id." and name='".$this->name."'";
			print "site->updateDB: $query<BR>";
			db_query($query);
		}
		
		// now update the permissions
		$this->updatePermissionsDB();
		
		// add log entry
		log_entry("edit_site",$this->name,"","","$_SESSION[auser] edited ".$this->name);
		
		// update down
		if ($down) {
			if ($this->fetcheddown) {
				foreach ($this->sections as $i=>$o) $o->updateDB(1);
			}
		}
		return 1;
	}
	
	function insertDB($down=0) {
		$a = $this->createSQLArray();
		$a[] = "addedby='$_SESSION[auser]'";
		$a[] = "addedtimestamp=NOW()";
		$a[] = "name='".$this->name."'";
		$query = "insert into sites set ".implode(",",$a);
		print "<BR>query = $query<BR>";
		db_query($query);
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
		log_entry("add_site",$this->name,"","","$_SESSION[auser] added ".$this->name);
		
		// insert down
		if ($down && $this->fetcheddown) {
			foreach ($this->sections as $i=>$o) $o->insertDB(1,$this->name);
		}
		return 1;
	}
	
	function addSection($id) {
		if (!is_array($this->data[sections])) $this->data[sections] = array();
		print "<br>adding section $id to ".$this->name."<br>"; //debug
		array_push($this->data[sections],$id);
		$this->changed = 1;
	}
	
	function delSection($id) {
		$d = array();
		foreach ($this->data[sections] as $n)
			if ($n != $id) $d[] = $n;
		$this->data[sections] = $d;
		$section = new section($this->name,$id);
		$section->delete();
		$this->changed = 1;
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from sites where id=".$this->id;
		db_query($query);
		
		// remove sections
		$this->fetchDown();
		foreach ($this->sections as $s=>$o) {
			$o->delete();
		}
		
		$this->clearPermissions();
		$this->updatePermissionsDB();
	}
	
	function createSQLArray() {
		$this->parseMediaTextForDB("header");
		$this->parseMediaTextForDB("footer");	

		$d = $this->data;
		$a = array();
		
		$a[] = "title='".addslashes($d[title])."'";
//		$a[] = "viewpermissions='$d[viewpermissions]'";
		$a[] = "listed=".(($d[listed])?1:0);
		$a[] = "activatedate='$d[activatedate]'";
		$a[] = "deactivatedate='$d[deactivatedate]'";
		$a[] = "active=".(($d[active])?1:0);
		$a[] = "type='$d[type]'";
		$a[] = "theme='$d[theme]'";
		$a[] = "themesettings='$d[themesettings]'";
		$a[] = "editors='$d[editors]'";
//		$a[] = "permissions='$d[permissions]'";
		$a[] = "header='".urlencode($d[header])."'";
		$a[] = "footer='".urlencode($d[footer])."'";
		$a[] = "sections='".encode_array($d[sections])."'";
		return $a;
	}
}