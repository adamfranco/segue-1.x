<? /* $Id$ */

class group {
	var $owner;
	var $classes=array();
	var $name;
	var $id=0;
	
	function group($name,$owner='',$classes='') {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		// find if this classgroup exists in the db, if yes, get the id
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		$q = "SELECT classgroup_id FROM classgroup WHERE classgroup_name = '$name'";
		echo $q."<br>";
		$r = db_query($q);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id = $a[classgroup_id];
		}
		else $this->id = 0;
		$this->name=$name;
		$this->owner=$owner;
		if ($classes!='') $this->classes=$classes;
	}
	
	function fetchFromDB() {
		$query = "
SELECT
	classgroup_id as id, classgroup_name as name
FROM classgroup WHERE classgroup_id=".$this->id;
		echo $query."<br>";
			
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id=$a[id];
		} else return false;
		
		$query = "
SELECT
	user_uname
FROM
	classgroup
		INNER JOIN
	user ON FK_owner = user_id AND classgroup_id = ".$this->id;
		echo $query."<br>";

		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->owner=$a[user_uname];
		} else return false;
			
		$query = "SELECT class_code FROM class INNER JOIN classgroup ON FK_classgroup = classgroup_id AND classgroup_id=".$this->id;
		echo $query."<br>";

		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->classes=array();
			while ($a = db_fetch_assoc($r))
				$this->classes[] = $a[class_code];
		} else return false;
	}
	
	
	// UPDATE THIS FUNCTION	
	function updateDB() {
		if ($this->exists($this->name)) {
			$query = "update classgroups set name='".$this->name."',owner='".$this->owner."'";
			$query .= ",classes='".implode(",",$this->classes)."' where id=".$this->id;
			db_query($query);
		} else {
			// insert in the db
			$query = "insert into classgroups set name='".$this->name."',owner='".$this->owner."'";
			$query .= ",classes='".implode(",",$this->classes)."'";
			db_query($query);
		}
	}
	
	function exists($name) {
		$query = "SELECT * FROM classgroup WHERE classgroup_name='$name'";
		echo $query."<br>";
		if (db_num_rows(db_query($query))) return true;
		return false;
	}
	
	function getClassesFromName($name) {
		if (group::exists($name)) {
			$query = "SELECT class_code FROM class INNER JOIN classgroup ON FK_classgroup = classgroup_id AND classgroup_name='$name'";
			echo $query."<br>";
			$r = db_query($query);
			$classes = array();
			while ($a = db_fetch_assoc($r))
				$classes[] = $a[class_code];
			return $classes;
		}
		return false;
	}
	
	function getNameFromClass($class) {
		$query = "SELECT classgroup_name FROM class INNER JOIN classgroup ON FK_classgroup = classgroup_id AND class_code = '$class'";
		echo $query."<br>";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			return $a[classgroup_name]; 
		}
		return false;
	}
	
	function getGroupsOwnedBy($owner) {
		$query = "SELECT * FROM user WHERE user_uname = '".$owner."'";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		$owner_id = $a[user_id];

		$query = "SELECT classgroup_name FROM classgroup WHERE FK_owner=$owner_id";
		$a = array();
		$r = db_query($query);
		while ($x = db_fetch_assoc($r)) {
			$a[] = $x[classgroup_name];
		}
		return $a;
	}
	
	function addClasses($classes) {
		if (is_array($classes)) {
			$classes2 = array();
			foreach ($classes as $n=>$class) {
				if (segue::siteExists($class)) {
					if (!segue::siteExists($this->name)) {
						$siteObj = new site ($class);
						$siteObj->fetchDown(1);
						$siteObj->copySite($this->name);
						$siteObj = new site ($class);
						$siteObj->fetchDown(1);
						$siteObj->delete();
						$classes2[] = $class;
					} else {
						error("You can not add an existing site to a group that already has a site created");
					}
				} else {
					$classes2[] = $class;
				}
			}
		
			$this->classes = array_unique(array_merge($this->classes,$classes2));
		}
	}
	
	function delClass($class) {
		$c = array();
		foreach ($this->classes as $cl) {
			if ($cl != $class) $c[] = $cl;
		}
		$this->classes = $c;
	}
	
	function delete() {
		db_query("DELETE FROM classgroup WHERE classgroup_name='".$this->name."'");
	}	
}