<? /* $Id$ */

class group {
	var $owner;
	var $classes=array();;
	var $name;
	var $id=0;
	
	function group($name,$owner='',$classes='') {
		$this->owner=$owner;
		$this->name=$name;
		if ($classes!='') $this->classes=$classes;
	}
	
	function fetchFromDB() {
		$query = "select * from classgroups where name='".$this->name."'";
		if ($this->owner) $query .= " and owner='".$this->owner."'"
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id=$a[id];
			$this->classes=explode(',',$a[classes]);
			if (!$this->owner) $this->owner = $a[owner];
			return true;
		} else return false;
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
	
	function addClasses($classes) {
		if (is_array($classes)) {
			$this->classes = array_unique(array_merge($this->classes,$classes));
		}
	}
	
	function delete() {
		db_query("delete from classgroups where name='".$this->name."' and owner='".$this->owner."'");
	}	
}