<? // segue common functions for sites, sections, pages, stories

class segue {
	var $permissions = array();
	var $editors = array();
	var $changedpermissions = 0;
	
	function addEditor($e) { 
		if ($e == 'institute' || $e == 'everyone') return false;
		if ($_SESSION[auser] == $e) { error("You do not need to add yourself as an editor."); return false; }
		if (!in_array($e,$this->editors)) {
			$this->editors[]=$e;
			$this->changedpermissions = 1;
		}
	}

	function delEditor($e) {
		if ($e == 'institute' || $e == 'everyone') return false;
		if (in_array($e,$this->editors)) {
			$n = array();
			foreach($this->editors as $v) {
				if ($v != $e) $n[]=$v;
			}
			$this->editors = $n;
			
			// remove any pertinent entries from the permissions table
			// to be added later -- not sure exactly how to handle this yet.
			// -- the higher power will enlighten me :)
		}
	}
	
	function getEditors() {
		return $this->editors;
	}
	
	function setPermissions($p) {
		// set the permissions array
		if (is_array($p)) {
			$this->permissions = $p;
			$this->changedpermissions = 1;
		}
	}
	
	function setUserPermissions($user,$add,$edit,$del,$view,$discuss) {
		$this->setUserPermissionsFromArray($user,array(ADD=>$add,EDIT=>$edit,DELETE=>$del,VIEW=>$view,DISCUSS=>$discuss));
	}
	
	function setUserPermissionsFromArray($user,$p) {
		$this->permissions[$user] = $p;
		$this->changedpermissions = 1;
	}
	
	function getPermissions() {
		// returns an html-formable permissions array based on the permissions table
		return $this->permissions;
	}
	
	function buildPermissionsArray() {
		// builds the permissions array from the database
		$scope = get_class($this);
		if ($scope == 'site') $site = $this->name;
		else $site = $this->owning_site;
		$id = $this->id;
		$query = "select * from permissions where site='$site' and scope='$scope' and scopeid='$id'";
		$r = db_query($query);
		while ($a=db_fetch_assoc($r)) {
			$this->permissions[$a[user]] = array( ADD=>$a[a], EDIT=>$a[e], DELETE=>$a[d], VIEW=>$a[v], DISCUSS=>$a[di]);
		}
		// build editors array
		$query = "select * from permissions where site='$site'";
		$r = db_query($query);
		$this->editors = array();
		while ($a=db_fetch_assoc($r)) {
			$this->editors[]=$a[user];
		}
	}

	function updatePermissionsDB() {
		if ($this->changedpermissions) {
			$scope = get_class($this);
			$id = $this->id;
			if ($scope == 'site') $site = $this->name;
			else $site = $this->owning_site;

			// build a quickie array
			$a = array();
			$a[] = "site='$site'";
			$a[] = "scope='$scope'";
			$a[] = "scopeid=$id";
			
			foreach ($this->permissions as $user=>$p) {
				$a2 = $a;
				$a2[] = "user='$user'";
				$a3 = array();
				$a3[] = "a=".(($p[ADD])?'1':'0');
				$a3[] = "e=".(($p[EDIT])?'1':'0');
				$a3[] = "d=".(($p[DELETE])?'1':'0');
				$a3[] = "v=".(($p[VIEW])?'1':'0');
				$a3[] = "di=".(($p[DISCUSS])?'1':'0');
				if (db_get_line("permissions",implode(" and ",$a2))) {
					$query = "update permissions set ".implode(",",$a3)." where ".implode(" and ",$a2);
				} else {
					$query = "insert into permissions set ".implode(",",$a2).",".implode(",",$a3);
				}
				print "$query<BR><BR>";
				db_query($query);
			}
		}
	}

	function setActivateDate($year,$month,$day) {
		// test to see if it's a valid date
		if (!checkdate($month,$day,$year)) {
			error("The activate date you entered is invalid. It has not been set.");
			return false;
		}
		$this->setField("activatedate",$year."-".$month."-".$day);
		return true;
	}
	
	function setDeactivateDate($year,$month,$day) {
		// test to see if it's a valid date
		if (!checkdate($month,$day,$year)) {
			error("The deactivate date you entered is invalid. It has not been set.");
			return false;
		}
		$this->setField("deactivatedate",$year."-".$month."-".$day);
		return true;
	}
	
	function parseMediaTextForEdit($field) {
		$this->data[$field] = ereg_replace("src=('{0,1})####('{0,1})","####",$this->data[$field]);
		$textarray1 = explode("####", $this->data[$field]);
		if (count($textarray1) > 1) {
			for ($i=1; $i<count($textarray1); $i+=2) {
				$id = $textarray1[$i];
				$filename = db_get_value("media","name","id=$id");
				$dir = db_get_value("media","site_id","id=$id");
				$filepath = $uploadurl."/".$dir."/".$filename;
				$textarray1[$i] = "&&&& src='".$filepath."' @@@@".$id."@@@@ &&&&";
			}		
			$this->data[$field] = implode("",$textarray1);
		}
	}
	
	function parseMediaTextForDB($field) {
		$textarray1 = explode("&&&&", $this->data[$field]);
		if (count($textarray1) > 1) {
			for ($i=1; $i<count($textarray1); $i=$i+2) {
				$textarray2 = explode("@@@@", $textarray1[$i]);
				$id = $textarray2[1];
				$textarray1[$i] = "src='####".$id."####'";
			}		
			$this->data[$field] = implode("",$textarray1);
		}
	}
}