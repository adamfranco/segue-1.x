<? // site object

class site extends segue {
	var $id;
	var $data;
	var $changed;
	var $sections;
	var $name;
	var $fetched;
	var $fetcheddown;
	var $fetchedup;
	
	function site($name) {
		$this->id = 0;
		$this->name = $name;
		$this->changed = 0;
		$this->sections = array();
		$this->data = array();
		$this->fetched = $this->fetcheddown = $this->fetchedup = 0;
		
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
		$this->fetched = 1;
//		$this->sections = unserialize(urldecode($this->data['sections']));
		$this->id = $this->data['id'];
		
		// decode appropriate info
		$this->data[header] = stripslashes(urldecode($this->data[header]));
		$this->data[footer] = stripslashes(urldecode($this->data[footer]));
		$this->parseMediaTextForEdit("header");
		$this->parseMediaTextForEdit("footer");
		return 1;
	}
	
	function fetchData() {
		if ($fetched) return $this->data;
		else return 0;
	}
	
	function setData($data) {
		if (is_array($data)) {
			$this->data = $data;
			$this->changed = 1;
			$this->parseMediaTextForEdit("header");
			$this->parseMediaTextForEdit("footer");
		}
	}
	
	function getField($name) {
		return $this->data[$name];
	}
	
	function setField($name,$value) {
		$this->data[$name] = $value;
		$this->changed = 1;
		if ($name == "footer" || $name == "header") {
			$this->parseMediaTextForEdit($name);
		}
	}
	
	function setSiteName($name) {
		if ($this->fetched) { // we are trying to change the name of an existing site!! bad.
			return 0;
		}
		$this->name = $name;
		$this->data[name] = $name;
		$this->changed = 1;
		return 1;
	}
	
	function updateDB() {
		if (!$this->changed) return 0;
		$a = $this->createSQLArray();
		$a[] = "editedby='$_SESSION[auser]'";
		$a[] = "editedtimestamp=NOW()";
		$query = "update sites set ".implode(",",$a)." where id=".$this->id." and name='".$this->name."'";
		db_query($query);
		return 1;
	}
	
	function insertDB() {
		$a = $this->createSQLArray();
		$a[] = "addedby='$_SESSION[auser]'";
		$a[] = "addedtimestamp=NOW()";
		$a[] = "name='".$this->name."'";
		$query = "insert into sites set ".implode(",",$a);
		print "<BR>query = $query<BR>";
		db_query($query);
		return 1;
	}
	
	function createSQLArray() {
		$this->parseMediaTextForDB("header");
		$this->parseMediaTextForDB("footer");	

		$d = $this->data;
		$a = array();
		
		$a[] = "title='$d[title]'";
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
		$a[] = "header='$d[header]'";
		$a[] = "footer='$d[footer]'";
		$a[] = "sections='$d[sections]'";
		return $a;
	}
}