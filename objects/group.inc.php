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
/* 		echo $q."<br>"; */
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
		// get id
		$query = "
SELECT
	classgroup_id AS id, classgroup_name AS name
FROM classgroup WHERE classgroup_id=".$this->id;
//		echo $query."<br>";
			
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id=$a[id];
		} else return false;

		// get owner		
		$query = "
SELECT
	user_uname
FROM
	classgroup
		INNER JOIN
	user ON FK_owner = user_id AND classgroup_id = ".$this->id;
//		echo $query."<br>";

		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->owner=$a[user_uname];
		} else return false;
			
		// get classes for that group<br>
		$query = "SELECT class_id FROM class INNER JOIN classgroup ON FK_classgroup = classgroup_id AND classgroup_id=".$this->id;
//		echo $query."<br>";

		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->classes=array();
			while ($a = db_fetch_assoc($r))
				$this->classes[] = generateCourseCode($a[class_id]);
		} else return false;
		
		return true;
	}
	
	function updateDB() {
		// get owner id
		$query = "SELECT user_id FROM user WHERE user_uname = '".$this->owner."'";
//		echo $query."<br>";
		$r = db_query($query);
		if (db_num_rows($r)==0) return false;
		else {
			$a = db_fetch_assoc($r);
			$owner_id = $a[user_id];
		}	

		// if this classgroup has not been inserted into the db yet, do it!
		if (!$this->exists($this->name))
        {
			$query = "INSERT INTO classgroup SET FK_owner = $owner_id, classgroup_name = '".$this->name."'";
//			echo $query."<br>";
			$r = db_query($query);
    		$this->id = lastid();
		}
		// else just update it
		else {
			$query = "UPDATE classgroup SET FK_owner = $owner_id, classgroup_name = '".$this->name."'";
//			echo $query."<br>";
		}

		// now that the group is in the db, update the foreign key for the classes

		// first, reset classes that used to be part of this classgroup
		$query = "UPDATE class SET FK_classgroup = NULL WHERE FK_classgroup = ".$this->id;
//		echo $query."<br>";
		$r = db_query($query);
		
		// then, set new forign key		
		if (count($this->classes)>0) {
//			$classes = "'".implode("','",$this->classes)."'";
//			$query = "UPDATE class SET FK_classgroup = ".$this->id." WHERE class_code IN ($classes)";
			foreach ($this->classes as $class_code) {
				$query = "
					UPDATE
						class
					SET
						FK_classgroup = ".$this->id."
					WHERE
						".generateTermsFromCode($class_code)."
				";		
//				echo $query."<br>";
				$r = db_query($query);
			}
		}
	}
	
	function exists($name) {
		$query = "SELECT classgroup_id FROM classgroup WHERE classgroup_name='$name'";
/* 		echo $query."<br>"; */
		if (db_num_rows(db_query($query))) return true;
		return false;
	}
	
	function getClassesFromName($name) {
		if (group::exists($name)) {
			$query = "SELECT class_id FROM class INNER JOIN classgroup ON FK_classgroup = classgroup_id AND classgroup_name='$name'";
//			echo $query."<br>";
			$r = db_query($query);
			$classes = array();
			while ($a = db_fetch_assoc($r))
				$classes[] = generateCourseCode($a[class_id]);
			return $classes;
		}
		return false;
	}
	
	function getNameFromClass($class) {
		$query = "SELECT classgroup_name FROM class INNER JOIN classgroup ON FK_classgroup = classgroup_id AND ".generateTermsFromCode($class);
//		echo $query."<br>";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			return $a[classgroup_name]; 
		}
		return false;
	}
	
	function getGroupsOwnedBy($owner) {
		$query = "SELECT user_id FROM user WHERE user_uname = '".$owner."'";
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
						$siteObj =& new site ($class);
						$siteObj->fetchDown(1);
						$siteObj->copySite($this->name);
						$siteObj =& new site ($class);
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