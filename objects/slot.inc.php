<? /* $Id$ */

class slot {
	var $owner;
	var $name;
	var $assocSite="";
	var $id=0;
	var $_allfields = array("name","owner","assocSite","id","type");
	var $fetched = array();
	
	function slot($name,$owner="",$type="class",$assocSite="",$id=0) {
		$this->owner = $owner;
		$this->name = $name;
		$this->type = $type;
		$this->assocSite = $assocSite;
		$this->id = $id;
	}
	
	function exists($name) {
		$query = "select * from slots where name='$name'";
		if (db_num_rows(db_query($query)) > 0) return 1;
		// check the ldap
		if (ldapfname($name)) return 1;
		return 0;
	}
	
	function fetchDown($force=1) {
		foreach ($this->_allfields as $field) $this->getField($field);
	}
	
	function getField ($field) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		if (!$this->fetched[$field]) {
			$class = get_class($this);
			$table = "slots"; // the table to use
			if ($class=='site' || $class == 'slot') $where = "name='".$this->name."'";
			else $where = "id=".$this->id;
			$query = "select $field from $table where $where limit 1";
			/* print "<br>".$query; */
			db_connect($dbhost,$dbuser,$dbpass, $dbdb);
			$r = db_query($query);
			if (!db_num_rows($r)) return false;
			$a = db_fetch_assoc($r);
			$val = $a[$field];
			$this->$field = $val;
			$this->fetched[$field] = 1;
		}
		return $this->$field;
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from slots where id=".$this->id;
		db_query($query);
	}
	
	function insertDB() {
		global $error;
		if (segue::siteExists($this->name) || slot::exists($this->name)) error("That site name, ".$this->name.", is already in use.");
		if (!ereg("^([0-9a-zA-Z_.-]{0,})$",$this->name)) error("Your slot name is invalid. It may only contain alphanumeric characters, '-', '_' and '.'");
		if (!$error) {
			$query = "insert into slots set owner='".$this->owner."',name='".$this->name."',type='".$this->type."',assocsite='".$this->assocSite."'";				
//			print $query;
			return db_query($query);
		}
	}
	
	function getAllSlots($owner="") {
		$allSlots = array();
		
		if ($owner != "")
			$query = "select * from slots where owner='$owner'";
		else
			$query = "select * from slots";
		$r = db_query($query);
		
		while ($a = db_fetch_assoc($r)) {
			$id = $a[id];
			$allSlots[$id] = $a[name];
		}
		return $allSlots;
	}
	
	function getAllSlotsInfo($owner="") {
		$allSlots = array();
		
		if ($owner != "")
			$query = "select * from slots where owner='$owner'";
		else
			$query = "select * from slots";
		$r = db_query($query);
		
		$i=0;
		while ($a = db_fetch_assoc($r)) {			
			$allSlots[$i] = array();
			$allSlots[$i][id] = $a[id];
			$allSlots[$i][name] = $a[name];
			$allSlots[$i][owner] = $a[owner];
			$allSlots[$i][type] = $a[type];
			$allSlots[$i][assocsite] = $a[assocsite];
			$i++;
		}
		return $allSlots;
	}
}