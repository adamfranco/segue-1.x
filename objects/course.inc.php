<? /* $Id$ */

class course {
	var $id,$code,$external_id,$name,$department,$number,$section,$semester,$year,$owner,$ugroup,$classgroup;
	
	function course() {
		$this->semester = 'f';
	}
	
	function fetchCourseID($id) {
		$this->id = $id;
		$this->_fetch();
	}
	
	function _fetch() {
		$query = "
			SELECT
				class_id,
				class_external_id,
				class_department,
				class_number,
				class_section,
				class_semester,
				class_year,
				class_name,
				FK_owner,
				FK_ugroup,
				FK_classgroup
			FROM
				class
			WHERE class_id=".$this->id;
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
		} else return false;
		
		$this->code = generateCourseCode($a['class_id']);
		$this->external_id = $a['class_external_id'];
		$this->name = $a['class_name'];
		$this->department = $a['class_department'];
		$this->number = $a['class_number'];
		$this->section = $a['class_section'];
		$this->semester = $a['class_semester'];
		$this->year = $a['class_year'];
		$this->owner = $a['FK_owner'];
		$this->ugroup = $a['FK_ugroup'];
		$this->classgroup = $a['FK_classgroup'];
	}
	
	function _insert() {
		$data = "class_external_id='".$this->external_id."'";
		$data .= ",class_name='".$this->name."'";
		$data .= ",class_department='".$this->department."'";
		$data .= ",class_number='".$this->number."'";
		$data .= ",class_section='".$this->section."'";
		$data .= ",class_semester='".$this->semester."'";
		$data .= ",class_year='".$this->year."'";
		$data .= ",FK_owner='".$this->owner."'";
		$data .= ",FK_ugroup='".$this->ugroup."'";
		$data .= ",FK_classgroup='".$this->classgroup."'";
		
		if ($this->id) { // are we updating?
			$query = "
				UPDATE
					class
				SET 
					$data
				WHERE class_id=".$this->id;
		} else 
			$query = "
				INSERT INTO 
					class
				SET 
					$data";
		
//		print $query;
		return db_query($query);
	}
	
	function updateDB() { $this->_insert(); }
	function insertDB() { $this->_insert(); }
	
	function courseExists($c) {
		if (!$c) return false;
		$query = "
			SELECT
				COUNT(*) as count
			FROM
				class
			WHERE
				".generateTermsFromCode($c);
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		if ($a['count'] != 0) return true;
		return false;
	}
	
	function delCourse($id) {
		$ugroup_id = db_get_value("class","FK_ugroup","class_id=$id");
		$query = "
			DELETE FROM
				class
			WHERE
				class_id=$id";
		db_query($query);
		$query = "
			DELETE FROM
				ugroup_user
			WHERE
				FK_ugroup=$ugroup_id
		";
		db_query($query);
		$query = "
			DELETE FROM
				ugroup
			WHERE
				ugroup_id=$ugroup_id
		";
		db_query($query);
	}
		
}