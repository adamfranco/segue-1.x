<? /* $Id$ */

class slot {
	var $owner;
	var $name;
	var $assocSite="";
	var $id=0;
	
	function slot($owner,$name,$assocSite="",$id=0) {
		$this->owner = $owner;
		$this->name = $name;
		$this->assocSite = $assocSite;
		$this->id = $id;
	}

	function exists($name) {
		$query = "select * from slots where name='$site'";
		if (db_num_rows(db_query($query))) return 1;
		// check the ldap
		if (ldapfname($name)) return 1;
		return 0;
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from slots where id=".$this->id;
		db_query($query);
	}
	
	function insertDB() {
		global $error;
		if (segue::siteExists($this->name) || slot::exists($this->name)) error("That site name is already in use.");
		if (!ereg("^([0-9a-zA-Z_.-]{0,})$",$this->name)) error("Your slot name is invalid. It may only contain alphanumeric characters, '-', '_' and '.'");
		if (!$error) {
			$query = "insert into slots set owner='".$this->owner."',name='".$this->name."',assocsite='".$this->assocSite."'";				
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
			$allSlots[$i][assocsite] = $a[assocsite];
			$i++;
		}
		return $allSlots;
	}
}