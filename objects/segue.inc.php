<? // segue common functions for sites, sections, pages, stories

class segue {
	var $permissions = array("everyone"=>array(3=>1),"institute"=>array(3=>1));
	var $editors = array("everyone","institute");
	var $editorsToDelete = array();
	var $changedpermissions = 0;
	
	var $id = 0;
	var $data;
	var $changed = 0;
	
	var $fetched = 0;
	var $fetcheddown = 0;
	var $fetchedup = 0;
	
	var $owning_site; var $owningSiteObj;		// used by all types (including site for compatibility)
	var $owning_section; var $owningSectionObj;	// only used for pages and stories
	var $owning_page; var $owningPageObj;		// only used for stories
	
	function fetchData() {
		if ($fetched) return $this->data;
		else return 0;
	}
	
	function setData($data) {
		if (is_array($data)) {
			$this->data = $data;
			$this->changed = 1;
			$this->parseMediaTextForEdit("header");
			$this->parseMediaTextForEdit("footer");
		}
	}

	function getField($name) {
		return $this->data[$name];
	}
	
	function setField($name,$value) {
		$this->data[$name] = $value;
		$this->changed = 1;
		if ($name == "footer" || $name == "header") {
			$this->parseMediaTextForEdit($name);
		}
	}
	
	function addEditor($e) { 
		if ($e == 'institute' || $e == 'everyone') return false;
		if ($_SESSION[auser] == $e) { error("You do not need to add yourself as an editor."); return false; }
		if (!in_array($e,$this->editors)) {
			$this->editors[]=$e;
			$this->setUserPermissions($e);
/* 			print_r($this->permissions); */
			unset($_REQUEST[permissions]);
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
			unset($this->permissions[$e]);
			$this->editorsToDelete[] = $e;
			
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
	
	function setUserPermissions($user,$add=0,$edit=0,$del=0,$view=1,$discuss=0) {
		$this->setUserPermissionsFromArray($user,array(ADD=>$add,EDIT=>$edit,DELETE=>$del,VIEW=>$view,DISCUSS=>$discuss));
	}
	
	function setUserPermissionsFromArray($user,$p) {
		$this->permissions[$user] = $p;
		$this->changedpermissions = 1;
/* 		print "Setting permissions from array for $user:<BR><BR>"; */
/* 		print_r($p); */
	}
	
	function getPermissions() {
		// returns an html-formable permissions array based on the permissions table
		return $this->permissions;
	}
	
	function buildPermissionsArray() {
		// builds the permissions array from the database
		$scope = get_class($this);
		$site = $this->owning_site;
		$id = $this->id;
		$query = "select * from permissions where site='$site' and scope='$scope' and scopeid='$id'";
		$r = db_query($query);
		while ($a=db_fetch_assoc($r)) {
			$this->permissions[$a[user]] = array( permissions::ADD()=>$a[a], 
												permissions::EDIT()=>$a[e], 
												permissions::DELETE()=>$a[d], 
												permissions::VIEW()=>$a[v], 
												permissions::DISCUSS()=>$a[di]);
		}
		// build editors array
		$query = "select * from permissions where site='$site'";
		$r = db_query($query);
		$this->editors = array();
		while ($a=db_fetch_assoc($r)) {
			$this->editors[]=$a[user];
		}
		if (!in_array("everyone",$this->editors)) {
			$this->editors[] = "everyone";
			$this->setUserPermissions("everyone",0,0,0,1,0);
			$this->changedpermissions = 1;
		}
		if (!in_array("institute",$this->editors)) {
			$this->editors[] = "institute";
			$this->setUserPermissions("institute",0,0,0,1,0);
			$this->changedpermissions = 1;
		}
	}

	function updatePermissionsDB($force=0) {
		if ($this->changedpermissions || $force) {
			$scope = get_class($this);
			$id = $this->id;
			$site = $this->owning_site;

			// build a quickie array
			$a = array();
			$a[] = "site='$site'";
			$a[] = "scope='$scope'";
			$a[] = "scopeid=$id";
			
			$n = array_unique(array_merge($this->editors,array_keys($this->permissions)));
			
			foreach ($n as $user) {
				$p = $this->permissions[$user];
				$a2 = $a;
				$a2[] = "user='$user'";
				$a3 = array();
				$a3[] = "a=".(($p[ADD])?"'1'":"'0'");
				$a3[] = "e=".(($p[EDIT])?"'1'":"'0'");
				$a3[] = "d=".(($p[DELETE])?"'1'":"'0'");
				$a3[] = "v=".(($p[VIEW])?"'1'":"'0'");
				$a3[] = "di=".(($p[DISCUSS])?"'1'":"'0'");
				if (db_get_line("permissions",implode(" and ",$a2))) {
					$query = "update permissions set ".implode(",",$a3)." where ".implode(" and ",$a2);
				} else {
					$query = "insert into permissions set ".implode(",",$a2).",".implode(",",$a3);
				}
				print "$query<BR><BR>";
				db_query($query);
			}
			// delete the appropriate entries from the table
			foreach ($this->editorsToDelete as $e) {
				db_query("delete from permissions where user='$e' and site='$site'");
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
		if (!$this->data[$field]) return false;
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
		if (!$this->data[$field]) return false;
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