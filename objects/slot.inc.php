<? /* $Id$ */

class slot {
	var $owner;
	var $name;
	var $assocSite="";
	var $id=0;
	var $type;
	var $site;
	var $_allfields = array("name","owner","assocSite","id","type","site");
	var $fetched = array();

	var $_datafields = array(
		"name" => array(
			"slot",
			array("slot_name"),
			"slot_id"
		),
		"id" => array(
			"slot",
			array("slot_id"),
			"slot_id"
		),
		"assocSite" => array(
			"slot",
			array("FK_assocsite"),
			"slot_id"
		),
		"owner" => array(
			"slot
				INNER JOIN
			 user ON FK_owner = user_id
			",
			array("user_uname"),
			"slot_id"
		),
		"type" => array(
			"slot",
			array("slot_type"),
			"slot_id"
		),
		"site" => array(
			"slot",
			array("FK_site"),
			"slot_id"
		)
	);
	
	
	function slot($name,$owner="",$type="class",$assocSite="",$id=0) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		// find if this slot exists in the db, if yes, get the id
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		$q = "SELECT slot_id FROM slot WHERE slot_name = '$name'";
//		echo $q;
		$r = db_query($q);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id = $a[slot_id];
		}
		else $this->id = $id;

		$this->owner = $owner;
		$this->name = $name;
		$this->type = $type;
		$this->assocSite = $assocSite;
		$this->site = 0;
	}
	
	function getOwner($slot) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		$query = "SELECT user_uname FROM slot INNER JOIN user ON FK_owner = user_id WHERE slot_name = '$slot'";
		$r = db_query($query);
		echo mysql_error();
		if (!db_num_rows($r)) return false;
		$a = db_fetch_assoc($r);
		return $a[udrt_uname];
	}
	
	function exists($name,$checkldap=0) {
		$query = "SELECT slot_id FROM slot WHERE slot_name='$name'";
		if (db_num_rows(db_query($query)) > 0) return 1;
		// check the ldap
/* 		print "ldapfname '".ldapfname($name)."'"; */
		if ($checkldap) {
			if (is_string(ldapfname($name))) return 1;
		}
		return 0;
	}
	
	function fetchDown($force=1) {
		foreach ($this->_allfields as $field) $this->getField($field);
	}
	
	function getField ($field) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		if (!$this->fetched[$field]) {

			$query = "
				SELECT 
					".implode(",",$this->_datafields[$field][1])."
				FROM
					".$this->_datafields[$field][0]."
				WHERE
					slot_id=".$this->id."
				ORDER BY
					".$this->_datafields[$field][2]."
			";

/* 			print "<br>".$query; */
			db_connect($dbhost,$dbuser,$dbpass, $dbdb);
			$r = db_query($query);
			echo mysql_error();
			if (!db_num_rows($r)) return false;
			$a = db_fetch_assoc($r);

			$val = $a[$this->_datafields[$field][1][0]];
			$this->$field = $val;
			$this->fetched[$field] = 1;
		}
		return $this->$field;
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "DELETE FROM slot WHERE slot_id=".$this->id;
		db_query($query);
	}

	function updateDB() {
		global $error;
		if (!$this->id) error("Slot is missing an ID");
		if (!ereg("^([0-9a-zA-Z_.-]{0,})$",$this->name)) error("Your slot name is invalid. It may only contain alphanumeric characters, '-', '_' and '.'");
		if (!$error) {
		
			// get id for owner of slot
			$query = "SELECT user_id FROM user WHERE user_uname = '".$this->owner."'";
/* 			echo $query."<br>"; */
			$r = db_query($query);
			if (!db_num_rows($r)) return false;
			$a = db_fetch_assoc($r);
			$owner_id = $a[user_id];			
			
			if ($this->site)
				$site = "'".$this->site."'";
			else
				$site = "NULL";
			if ($this->assocSite) {
				// get id for assoc_site of slot
				$query = "SELECT slot_id FROM slot WHERE slot_name = '".$this->assocSite."'";
/* 				echo $query."<br>"; */
				$r = db_query($query);
				$a = db_fetch_assoc($r);
				$assocSite = $a[slot_id];
			} else
				$assocSite = "NULL";
			
			$query = "UPDATE slot SET FK_owner= $owner_id, FK_assocsite=".$assocSite.",slot_uploadlimit=".$this->uploadlimit." WHERE slot_id=".$this->id;
/* 			print $query; */
			db_query($query);
			echo mysql_error();
		}
	}
	
	function insertDB() {
		global $error;
		if (segue::siteExists($this->name) || slot::exists($this->name,0)) error("That site name, ".$this->name.", is already in use.");
		if (!ereg("^([0-9a-zA-Z_.-]{0,})$",$this->name)) error("Your slot name is invalid. It may only contain alphanumeric characters, '-', '_' and '.'");
		if (!$error) {
		
			// get id for owner of slot
			$query = "SELECT user_id FROM user WHERE user_uname = '".$this->owner."'";
/* 			echo $query."<br>"; */
			$r = db_query($query);
			if (!db_num_rows($r)) return false;
			$a = db_fetch_assoc($r);
			$owner_id = $a[user_id];			
			
			if ($this->site)
				$site = "'".$this->site."'";
			else
				$site = "NULL";
			if ($this->assocSite) {
				// get id for assoc_site of slot
				$query = "SELECT slot_id FROM slot WHERE slot_name = '".$this->assocSite."'";
/* 				echo $query."<br>"; */
				$r = db_query($query);
				$a = db_fetch_assoc($r);
				$assocSite = $a[slot_id];
			} else
				$assocSite = "NULL";
			
			$query = "INSERT INTO slot SET FK_owner= $owner_id, slot_name='".$this->name."',slot_type='".$this->type."',FK_site=".$site.",FK_assocsite=".$assocSite.",slot_uploadlimit=".$this->uploadlimit;
/* 			print $query; */
			db_query($query);
			echo mysql_error();
		}
	}
	
	function getAllSlots($owner="") {
		$allSlots = array();
		
		if ($owner != "") {
			$query = "SELECT slot_id, slot_name FROM slot INNER JOIN user ON FK_owner=user_id WHERE user_uname='$owner'";
			/* echo $query."<br>"; */
		} else {
			$query = "SELECT slot_id, slot_name FROM slot";
	/* 		echo $query; */
		}
		$r = db_query($query);
		
		while ($a = db_fetch_assoc($r)) {
			$id = $a[slot_id];
			$allSlots[$id] = $a[slot_name];
		}
		return $allSlots;
	}
	
	function getAllSlotsInfo($owner="") {
		$allSlots = array();
		
		if ($owner != "") {
			$query = "SELECT * FROM user WHERE user_uname = '".$owner."'";
			$r = db_query($query);
			$a = db_fetch_assoc($r);
			$owner_id = $a[user_id];
			$query = "
				SELECT 
					slot.slot_id,
					slot.slot_name,
					user.user_uname,
					slot.slot_type,
					assocsite.slot_name AS assocsite_name,
					slot.FK_site,
					slot.slot_uploadlimit
				FROM 
					slot
						LEFT JOIN
					user
						ON
							slot.FK_owner = user_id
						LEFT JOIN
					slot AS assocsite
						ON
							slot.FK_assocsite = assocsite.slot_id
				WHERE
					FK_owner=$owner_id
				ORDER BY
					slot.slot_name
			";
/* 			echo $query; */
		}
		else {
			$query = "
				SELECT 
					slot.slot_id,
					slot.slot_name,
					user.user_uname,
					slot.slot_type,
					assocsite.slot_name AS assocsite_name,
					slot.FK_site,
					slot.slot_uploadlimit
				FROM 
					slot
						LEFT JOIN
					user
						ON
							slot.FK_owner = user_id
						LEFT JOIN
					slot AS assocsite
						ON
							slot.FK_assocsite = assocsite.slot_id
				ORDER BY
					slot.slot_name
			";
/* 			echo $query; */
		}
		$r = db_query($query);
		
		$i=0;
		while ($a = db_fetch_assoc($r)) {			
			$allSlots[$i] = array();
			$allSlots[$i][id] = $a[slot_id];
			$allSlots[$i][name] = $a[slot_name];
			$allSlots[$i][owner] = $a[user_uname];
			$allSlots[$i][type] = $a[slot_type];
			$allSlots[$i][assocsite] = $a[assocsite_name];
			$allSlots[$i][FK_site] = $a[FK_site];
			$allSlots[$i][uploadlimit] = $a[slot_uploadlimit];
			$i++;
		}
		return $allSlots;
	}
}