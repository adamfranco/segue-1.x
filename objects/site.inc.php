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
		
	}
	
	function createSQLArray() {
		$d = $this->data;
		$a = array();
		$a[] = "title='$settings[title]'";
		$a[] = "viewpermissions='$settings[viewpermissions]'";
		$a[] = "listed='$settings[listed]'";
		$a[] = "activatedate='$settings[activatedate]'";
		$a[] = "deactivatedate='$settings[deactivatedate]'";
		$a[] = "active=$settings[active]";
		$a[] = "type='$settings[type]'";
		$a[] = "theme='$settings[theme]'";
		$a[] = "themesettings='$settings[themesettings]'";
		$a[] = "editors='$settings[editors]'";