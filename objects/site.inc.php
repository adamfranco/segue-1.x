<? /* $Id$ */

class site extends segue {
	var $sections;
	var $name;
	var $_allfields = array("title","theme","themesettings","header","footer",
						"addedby","editedby","editedtimestamp","addedtimestamp",
						"activatedate","deactivatedate","active","sections",
						"listed","type");
	
	function site($name) {
		$this->name = $name;
		$this->owning_site = $name;
		$this->sections = array();
		$this->data = array();
		
		// initialize the data array
		$this->data[name] = $name;
		$this->data[type] = "personal";
		$this->data[title] = "";
		$this->data[activatedate] = "0000-00-00";
		$this->data[deactivatedate] = "0000-00-00";
		$this->data[active] = 1;
		$this->data[listed] = 1;
		$this->data[theme] = "minimal";
		$this->data[themesettings] = "";
		$this->data[header] = "";
		$this->data[footer] = "";
		$this->data[sections] = array();
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
/* 			print "site fetchdown ".$this->name."<BR>"; */
			if (!$this->tobefetched) $this->fetchFromDB($full);
			foreach ($this->getField("sections") as $s) {
				$this->sections[$s] = new section($this->name,$s);
				$this->sections[$s]->fetchDown($full);
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchUp() {
		if (get_class($this) == 'site') $this->owningSiteObj = &$this;
		else {
			$this->owningSiteObj = new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB();
		}
	}
	
	function fetchFromDB($force=0) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		global $cfg;
		// take this out when appropriate & replace occurences;
		global $uploaddir;
		
		$this->tobefetched=1;
		
		$this->id = $this->getField("id");
		
		if ($force) {
/* 			db_connect($dbhost,$dbuser,$dbpass, $dbdb); */
/* 			$query = "select * from sites where name='".$this->name."' limit 1"; */
/* 			$r = db_query($query); */
/* 			if (db_num_rows($r)) { */
/* 				$this->data = db_fetch_assoc($r); */
/* 				$this->fetched = 1; */
/* 		//		$this->sections = unserialize(urldecode($this->getField("sections"))); */
/* 				$this->id = $this->getField("id"); */
/* 				 */
/* 				// decode appropriate info */
/* 				$this->data[sections] = decode_array($this->getField("sections")); */
/* 				$this->data[header] = stripslashes(urldecode($this->data[header])); */
/* 				$this->data[footer] = stripslashes(urldecode($this->data[footer])); */
/* 				$this->parseMediaTextForEdit("header"); */
/* 				$this->parseMediaTextForEdit("footer"); */
/* 				$this->buildPermissionsArray(); */
/* 				if (strlen($this->data[type])) $this->data[type] = 'personal'; */
/* 				return true; */
/* 			} */
/* 			print "<pre>allFields: "; print_r($this->_allfields); print "</pre>"; */
			foreach ($this->_allfields as $f) {
				$this->getField($f);
			}
		}
		return $this->id;
	}
	
	function applyTemplate ($template) {
		$templateObj = new site($template);
		$templateObj->fetchDown(1);
		print "<pre>templateObj: "; print_r($templateObj); print "</pre>";
		foreach ($templateObj->sections as $i=>$o) $o->copyObj($this);
	}
	
	function setSiteName($name, $copySite=0) {
		if ($this->tobefetched && !$copySite) { // we are trying to change the name of an existing site!! bad.
			return 0;
		}
		$this->name = $this->owning_site = $name;
		$this->setField("name",$name);
		return 1;
	}
	
/******************************************************************************
 * copySite - clearPermissions currently has no effect. All permissions are cleared.
 ******************************************************************************/
	function copySite($newName, $clearPermissions=1) {
		$newSiteObj = $this;
		$newSiteObj->setSiteName($newName, 1);
		$newSiteObj->insertDB(1,1);
	}
	
	function updateDB($down=0) {
		if (count($this->changed)) {
			$a = $this->createSQLArray();
			$a[] = "editedby='$_SESSION[auser]'";
			$a[] = "editedtimestamp=NOW()";
			$query = "update sites set ".implode(",",$a)." where id=".$this->id." and name='".$this->name."'";
/* 			print "site->updateDB: $query<BR>"; */
			db_query($query);
		}
		
		// now update the permissions
		$this->updatePermissionsDB();
		
		// add log entry
		log_entry("edit_site",$this->name,"","","$_SESSION[auser] edited ".$this->name);
		
		// update down
		if ($down) {
			if ($this->fetcheddown && $this->sections) {
				foreach ($this->sections as $i=>$o) $o->updateDB(1);
			}
		}
		return 1;
	}
	
	function insertDB($down=0,$copysite=0) {
		$a = $this->createSQLArray(1);
		$a[] = "addedby='$_SESSION[auser]'";
		$a[] = "addedtimestamp=NOW()";
		$a[] = "name='".$this->name."'";
		$query = "insert into sites set ".implode(",",$a);
/* 		print "<BR>query = $query<BR>"; */
		db_query($query);
		$this->id = mysql_insert_id();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
		log_entry("add_site",$this->name,"","","$_SESSION[auser] added ".$this->name);
		
		// insert down
		if ($down && $this->fetcheddown && $this->sections) {
			foreach ($this->sections as $i=>$o) $o->insertDB(1,$this->name,$copysite);
		}
		return 1;
	}
	
	function addSection($id) {
		if (!is_array($this->getField("sections"))) $this->data[sections] = array();
/* 		print "<br>adding section $id to ".$this->name."<br>"; //debug */
		array_push($this->data[sections],$id);
		$this->changed[sections] = 1;
/* 		print "<pre>this: "; print_r($this->data[sections]); print "</pre>"; */
	}
	
	function delSection($id,$delete=1) {
		$d = array();
		foreach ($this->getField("sections") as $n)
			if ($n != $id) $d[] = $n;
		$this->data[sections] = $d;
		$this->changed[sections] = 1;
		if ($delete) {
			$section = new section($this->name,$id);
			$section->delete();
		}
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from sites where id=".$this->id;
		db_query($query);
		
		// remove sections
		$this->fetchDown();
		if ($this->sections) {
			foreach ($this->sections as $s=>$o) {
				$o->delete();
			}
		}
		
/* 		print "<pre>this: "; print_r($this); print "</pre>"; */
		$this->clearPermissions();
/* 		print "<pre>this: "; print_r($this); print "</pre>"; */
		$this->updatePermissionsDB();
	}
	
	function createSQLArray($all=0) {
		$this->parseMediaTextForDB("header");
		$this->parseMediaTextForDB("footer");	

		$d = $this->data;
		$a = array();
		
		if ($all || $this->changed[title]) $a[] = "title='".addslashes($d[title])."'";
//		$a[] = "viewpermissions='$d[viewpermissions]'";
		if ($all || $this->changed[listed]) $a[] = "listed=".(($d[listed])?1:0);
		if ($all || $this->changed[activatedate]) $a[] = "activatedate='$d[activatedate]'";
		if ($all || $this->changed[deactivatedate]) $a[] = "deactivatedate='$d[deactivatedate]'";
		if ($all || $this->changed[active]) $a[] = "active=".(($d[active])?1:0);
		if ($all || $this->changed[type]) $a[] = "type='$d[type]'";
		if ($all || $this->changed[theme]) $a[] = "theme='$d[theme]'";
		if ($all || $this->changed[themesettings]) $a[] = "themesettings='$d[themesettings]'";
/* 		if ($this->changed[editors]) $a[] = "editors='$d[editors]'"; */
//		$a[] = "permissions='$d[permissions]'";
		if ($all || $this->changed[header]) $a[] = "header='".urlencode($d[header])."'";
		if ($all || $this->changed[footer]) $a[] = "footer='".urlencode($d[footer])."'";
		if ($all || $this->changed[sections]) $a[] = "sections='".encode_array($d[sections])."'";
		return $a;
	}
}