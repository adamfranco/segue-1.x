<? /* $Id$ */

class group {
	var $owner;
	var $classes=array();
	var $name;
	var $id=0;
	
	function group($name,$owner='',$classes='') {
		$this->owner=$owner;
		$this->name=$name;
		if ($classes!='') $this->classes=$classes;
	}
	
	function fetchFromDB() {
		$query = "SELECT * FROM classgroup WHERE classgroup_name='".$this->name."'";
		if ($this->owner) $query .= " and owner='".$this->owner."'";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id=$a[id];
			$this->classes=explode(',',$a[classes]);
			if (!$this->owner) $this->owner = $a[owner];
			return true;
		} else return false;
	}
	
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
		$query = "select * from classgroups where name='$name'";
		if (db_num_rows(db_query($query))) return true;
		return false;
	}
	
	function getClassesFromName($name) {
		if (group::exists($name)) {
			$query = "select * from classgroups where name='$name'";
			$a = db_fetch_assoc(db_query($query));
			$classes = explode(',',$a[classes]);
			return $classes;
		}
		return false;
	}
	
	function getNameFromClass($class) {
		$query = "select * from classgroups where classes LIKE '%$class%'";
		$r = db_query($query);
		if (db_num_rows($r)) {$a = db_fetch_assoc($r); return $a[name]; }
		return false;
	}
	
	function getGroupsOwnedBy($user) {
		$query = "select * from classgroups where owner='$user'";
		$a = array();
		$r = db_query($query);
		while ($x = db_fetch_assoc($r)) {
			$a[] = $x[name];
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
		db_query("delete from classgroups where name='".$this->name."' and owner='".$this->owner."'");
	}	
}