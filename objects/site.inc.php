<? // site object

class site {
	var $id;
	var $data;
	var $changed;
	var $sections;
	var $name;
	var $fetched;
	
	function site($name) {
		$this->id = 0;
		$this->name = $name;
		$this->changed = 0;
		$this->sections = array();
		$this->data = array();
	}
	
	function fetchFromDB() {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		$query = "select * from sites where name='".$this->name."' limit 1";
		$r = db_query($query);
		$this->data = db_fetch_assoc($r);
		$this->fetched = 1;
//		$this->sections = unserialize(urldecode($this->data['sections']));
		$this->id = $this->data['id'];
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
		}
	}
	
	function getField($name) {
		return $this->data[$name];
	}
	
	function setField($name,$value) {
		$this->data[$name] = $value;
		$this->changed = 1;
	}
	
	function updateDB() {
		global $auser;
		if (!$this->changed) return 0;
		$a = $this->createSQLArray();
		$a[] = "editedby='$auser'";
		$a[] = "editedtimestamp=NOW()";
		$query = "update sites set ".implode(",",$a)." where id=".$this->id." and name='".$this->name."'";
		db_query($query);
		return 1;
	}
	
	function insertDB() {
		global $auser;
		$a = $this->createSQLArray();
		$a[] = "addedby='$auser'";
		$a[] = "addedtimestamp=NOW()";
		$a[] = "name='".$this->name."'";
		$query = "insert into sites set ".implode(",",$a);
		print "<BR>query = $query<BR>";
		db_query($query);
		return 1;
	}
	
	function createSQLArray() {
		$d = $this->data;
		$a = array();
		$a[] = "title='$d[title]'";
		$a[] = "viewpermissions='$d[viewpermissions]'";
		$a[] = "listed=".(($d[listed])?1:0);
		$a[] = "activatedate='$d[activatedate]'";
		$a[] = "deactivatedate='$d[deactivatedate]'";
		$a[] = "active=".(($d[active])?1:0);
		$a[] = "type='$d[type]'";
		$a[] = "theme='$d[theme]'";
		$a[] = "themesettings='$d[themesettings]'";
		$a[] = "editors='$d[editors]'";
		$a[] = "permissions='$d[permissions]'";
		$a[] = "header='$d[header]'";
		$a[] = "footer='$d[footer]'";
		$a[] = "sections='$d[sections]'";
		return $a;
	}
}