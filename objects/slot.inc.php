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

	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from slots where id=".$this->id;
		db_query($query);
	}
	
	function insertDB() {
		if (segue::siteExists($this->name)) return false;
		else {
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
			$allslots[$id] = $a[name];
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