<? /* $Id$ */
/******************************************************************************
 * Segue object - basis for all other section, page, and story objects
 ******************************************************************************/

class segue {
	var $permissions = array("everyone"=>array(3=>1),"institute"=>array(3=>1));
	var $editors = array("everyone","institute");
	var $editorsToDelete = array();
	var $editorsToDeleteInScope = array();
	var $changedpermissions = 0;
	var $cachedPermissions = array();
	var $builtPermissions=0;
	
	var $id = 0;
	var $data = array();
	var $changed = array();
	
	var $fetched = array();
	var $fetcheddown = 0;
	var $fetchedup = 0;
	var $tobefetched = 0;
	
	var $owning_site; var $owningSiteObj;		// used by all types (including site for compatibility)
	var $owning_section; var $owningSectionObj;	// only used for pages and stories
	var $owning_page; var $owningPageObj;		// only used for stories
	
	var $_object_arrays = array("site"=>"sections","section"=>"pages","page"=>"stories"); // used for automatic functions like setFieldDown and setVarDown
	var $_tables = array("site"=>"sites","section"=>"sections","page"=>"pages","story"=>"stories"); // used for getField

/******************************************************************************
 * siteExists - checks if the site/slot already exists with a certain name $name
 ******************************************************************************/
	
	function siteExists($site) {
		$query = "select * from sites where name='$site'";
		if (db_num_rows(db_query($query))) return 1;
		return 0;
	}

/******************************************************************************
 * siteNameValid - checks if a user is allowed to create a site of name $name
 ******************************************************************************/
	
	function siteNameValid($user,$name) {
		return 1;
	}

/******************************************************************************
 * buildObjArrayFromSites($sites) - builds an array of objects from site names
 ******************************************************************************/

	function buildObjArrayFromSites($sites) {
		if (!is_array($sites)) return array();
		$a = array();
		foreach ($sites as $s) {
			$a[$s] = new site($s);
			$a[$s]->fetchFromDB();
		}
		return $a;
	}

/******************************************************************************
 * getAllSites - returns a list of all sites owned by $user
 ******************************************************************************/

	function getAllSites($user) {
		$sites = array();
		if (db_num_rows($r = db_query("select * from sites where addedby='$user'"))) {
			while ($a = db_fetch_assoc($r)) {
				$sites[] = $a[name];
			}
		}
		return $sites;
	}

/******************************************************************************
 * getAllSitesWhereUserIsEditor - gets all sites where $user is an editor
 ******************************************************************************/

	function getAllSitesWhereUserIsEditor($user='') {
		if ($user == '') $user = $_SESSION[auser];
		$query = "select * from permissions where user='$user'";
		$r = db_query($query);
		$ar = array();
		if (db_num_rows($r)) {
			while ($a = db_fetch_assoc($r)) {
				$ar[] = $a[site];
			}
		}
		return array_unique($ar);
	}

/******************************************************************************
 * getAllValues - returns all values of $name in $scope in the current tree
 ******************************************************************************/

	function getAllValues($scope,$name) {
		if (!$this->fetcheddown) $this->fetchDown();
		$class = get_class($this);
		$ar = $this->_object_arrays[$class];
//		print "getting all values for $name in $class ".$this->getField("title")." with scope $scope<BR>";
		if ($class==$scope) {
			if (($n = $this->getField($name)) != "")
				return array($n);
			else return array();
		}
		if ($ar) {
			$a = array();
			$oa = &$this->$ar;
			if ($oa) {
				foreach ($oa as $i=>$o) {
	//				print "doing $i in $ar...<BR>";
					$a = array_merge($a,$oa[$i]->getAllValues($scope,$name));
				}
			}
		}
		return $a;
	}
	
	function fetchData() {
		if ($fetched) return $this->data;
		else return 0;
	}
	
	function setData($data) {
		error("::setData() -- this function should not be used!");
		if (is_array($data)) {
			$this->data = $data;
			$this->changed = 1;
			$this->parseMediaTextForEdit("header");
			$this->parseMediaTextForEdit("footer");
			$this->parseMediaTextForEdit("shorttext");
			$this->parseMediaTextForEdit("longertext");
		}
	}

/******************************************************************************
 * getField - will fetch a field either from the DB or from array we have it
 ******************************************************************************/

	function getField($field) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		if ($this->tobefetched && !ereg("^l-",$field)) {	// we're supposed to fetch this field
			$_unencode = array("title","header","footer","shorttext","longertext");
			$_parse = array("header","footer","shorttext","logertext");
			$_ardecode = array("sections","pages","stories","discussions");
			if (!$this->fetched[$field]) {
				$class = get_class($this);
				$table = $this->_tables[$class]; // the table to use
				if ($class=='site') $where = "name='".$this->name."'";
				else $where = "id=".$this->id;
				$query = "select $field from $table where $where limit 1";
				db_connect($dbhost,$dbuser,$dbpass, $dbdb);
				$r = db_query($query);
				if (!db_num_rows($r)) return false;
				$a = db_fetch_assoc($r);
				$val = $a[$field];
				if (in_array($field,$_unencode)) $val = stripslashes(urldecode($val));
				if (in_array($field,$_ardecode)) $val = decode_array($val);
				$this->data[$field] = $val;
				$this->fetched[$field] = 1;
				if (in_array($field,$_parse)) $this->parseMediaTextForEdit($field);
			}
		} // done fetching
			
		return $this->data[$field];
	}
	
	function setField($name,$value) {
		$this->data[$name] = $value;
		$this->changed[$name] = 1;
		if ($name == "footer" || $name == "header" || $name == "shorttext" || $name == "longertext") {
			$this->parseMediaTextForEdit($name);
		}
	}
	
	function setFieldDown($name,$value) {
		if (!$this->fetcheddown) $this->fetchDown();
		$class=get_class($this);
		$ar = $this->_object_arrays[$class];
		$this->setField($name,$value);
		if ($ar) {
			$a = &$this->$ar;
			if ($a) {
				foreach ($a as $i=>$o) {
					$a[$i]->setFieldDown($name,$value);
				}
			}
		}
	}
	
/* 	function setSiteNameDown($name) { */
/* //		if (!$this->fetcheddown) $this->fetchDown(); */
/* 		$class=get_class($this); */
/* 		$ar = $this->_object_arrays[$class]; */
/* 		$this->owning_site = $name; */
/* 		if ($class == "site") { */
/* 			$this->name = $name; */
/* 			$this->setField("name",$name); */
/* 		} else { */
/* 			$this->setField("site_id",$name); */
/* 		} */
/* 		if ($ar) { */
/* 			$a = &$this->$ar; */
/* 			foreach ($a as $i=>$o) { */
/* 				$a[$i]->setSiteNameDown($name); */
/* 			} */
/* 		} */
/* 	} */
		
	
/******************************************************************************
 * copyObj - Copies an object to a new parent
 ******************************************************************************/
	function copyObj(&$newParent,$removeOrigional=1,$keepaddedby=0) {
		$_a = array("site"=>3,"section"=>2,"page"=>1,"story"=>0);
		// check that the newParent can be a parent
		$thisClass = get_class($this);
		$parentClass = get_class($newParent);
/* 		print $this->id."$thisClass - $parentClass<br>"; */
		if (!($_a[$parentClass]-1 == $_a[$thisClass])) return 0;
/* 		print " Before ".$this->fetcheddown."<br>"; */

/* 		print "<pre>this: "; print_r($this); print " newparent: "; print "</pre>";		 */
		$this->fetchDown(1);
/* 		print " After <br>"; */
/* 		print "title: ".$this->data[title]."<br>";		 */
/* 		print "title: ".$this->getField("title")."<br>"; */
/* 		print "<pre>this: "; print_r($this); print " newparent: "; print_r($newParent); print "</pre>"; */
/* 		return 0; */

		if ($thisClass == 'section') {
			$owning_site = $newParent->name;
			$this->insertDB(1,$owning_site,$removeOrigional,$keepaddedby);
		}
		if ($thisClass == 'page') {
			$owning_site = $newParent->owning_site;
			$owning_section = $newParent->id;
			$this->insertDB(1,$owning_site,$owning_section,$removeOrigional,$keepaddedby);
		}
		if ($thisClass == 'story') {
			$owning_site = $newParent->owning_site;
			$owning_section = $newParent->owning_section;
			$owning_page = $newParent->id;
/* 			print "insertDB: 1,$owning_site,$owning_section,$owning_page,$keepaddedby<br>"; */
			$this->insertDB(1,$owning_site,$owning_section,$owning_page,$removeOrigional,$keepaddedby);
		}

/* 		print_r($newParent); */
		return 1;
	}
	
/******************************************************************************
 * getMediaIDs - returns an array of media ids found in a string
 ******************************************************************************/

	function getMediaIDs($field) {
		$string = stripslashes($this->getField($field));
		$ids = array();
		$string =  explode("####",$string);
		for ($i=1; $i<count($string); $i=$i+2) {
			$ids[] = $string[$i];
		}
		return $ids;
	}
	
/******************************************************************************
 * replaceMediaIDs - searches for and replaces each id in the string
 ******************************************************************************/
	function replaceMediaIDs($ids,$field,$newsite) {
		$string = $this->getField($field);
		foreach ($ids as $origID) {
			$newID = copy_media($origID,$newsite);
			$string = str_replace("####$origID####","####$newID####",$string);
		}
		$this->setField($field,$string);
	}
	
/******************************************************************************
 * ACTIVATE/DEACTIVATE FUNCTIONS
 *
 * these functions handle de/activate dates in forms
 * - initFormDates() must be called upon edit session initialization
 * - outputDateForm() must be called where the HTML form data should be printed
 * - handleFormDates() must be called where all POST/GET data is processed
 ******************************************************************************/


/******************************************************************************
 * handleFormDates - checks form fields for new de/activate dates and subsequently
 *    sets the correct $_SESSION[settings][] variables
 ******************************************************************************/

	function handleFormDates() {
		// initialize the session vars.. if needed
		if (!isset($_SESSION[settings][activateyear]) || !isset($_SESSION[settings][deactivateyear])) {
			$this->initFormDates();
		}
		if ($_REQUEST[activateyear] != "") $_SESSION[settings][activateyear] = $_REQUEST[activateyear];
		if ($_REQUEST[activatemonth] != "") $_SESSION[settings][activatemonth] = $_REQUEST[activatemonth];
		if ($_REQUEST[activateday] != "") $_SESSION[settings][activateday] = $_REQUEST[activateday];
		if ($_REQUEST[deactivateyear] != "") $_SESSION[settings][deactivateyear] = $_REQUEST[deactivateyear];
		if ($_REQUEST[deactivatemonth] != "") $_SESSION[settings][deactivatemonth] = $_REQUEST[deactivatemonth];
		if ($_REQUEST[deactivateday] != "") $_SESSION[settings][deactivateday] = $_REQUEST[deactivateday];
		if ($_REQUEST[setformdates]) {
			if (/* !$_REQUEST[link] &&  */$_REQUEST[activatedate]) { 
				$_SESSION[settings][activatedate] = 1;
				$this->setActivateDate($_REQUEST[activateyear],$_REQUEST[activatemonth]+1,$_REQUEST[activateday]);
			} else {
				$_SESSION[settings][activatedate] = 0;
				$this->setActivateDate(-1);
			}
			if (/* !$_REQUEST[link] &&  */$_REQUEST[deactivatedate]) {
				$_SESSION[settings][deactivatedate] = 1;
				$this->setDeactivateDate($_REQUEST[deactivateyear],$_REQUEST[deactivatemonth]+1,$_REQUEST[deactivateday]);
			} else {
				$_SESSION[settings][deactivatedate] = 0;
				$this->setDeactivateDate(-1);
			}
		}
	}
	
/******************************************************************************
 * outputDateForm - outputs the HTML de/activate date form to be handled by above
 ******************************************************************************/

	function outputDateForm() {
		global $months;
		printc("<input type=hidden name='setformdates' value=1>");
		printc("<table>");
		printc("<tr><td align=right>");
		printc("Activate date:</td><td><input type=checkbox name='activatedate' value=1".(($_SESSION[settings][activatedate])?" checked":"")."> <select name='activateday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($_SESSION[settings][activateday] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='activatemonth'>");
		for ($i=0; $i<12; $i++) {
			printc("<option value=$i" . (($_SESSION[settings][activatemonth] == $i)?" selected":"") . ">$months[$i]\n");
		}
		printc("</select>\n<select name='activateyear'>");
		$curryear = date("Y");
		for ($i=$curryear; $i <= ($curryear+5); $i++) {
			printc("<option" . (($_SESSION[settings][activateyear] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
		
		printc("</td></tr>");
		
		printc("<tr><td align=right>");
		printc("Deactivate date:</td><td><input type=checkbox name='deactivatedate' value=1".(($_SESSION[settings][deactivatedate])?" checked":"")."> <select name='deactivateday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($_SESSION[settings][deactivateday] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='deactivatemonth'>");
		for ($i=0; $i<12; $i++) {
			printc("<option value=$i" . (($_SESSION[settings][deactivatemonth] == $i)?" selected":"") . ">$months[$i]\n");
		}
		printc("</select>\n<select name='deactivateyear'>");
		for ($i=$curryear; $i <= ($curryear+5); $i++) {
			printc("<option" . (($_SESSION[settings][deactivateyear] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
		
		printc("</tr></td></table>");
	}
	
/******************************************************************************
 * initFormDates - initializes necessary session vars for form date handling
 ******************************************************************************/

	function initFormDates() {
		$_SESSION[settings][activateyear] = "0000";
		$_SESSION[settings][activatemonth] = "00";
		$_SESSION[settings][activateday] = "00";
		$_SESSION[settings][activatedate] = 0;
		$_SESSION[settings][deactivateyear] = "0000";
		$_SESSION[settings][deactivatemonth] = "00";
		$_SESSION[settings][deactivateday] = "00";
		$_SESSION[settings][deactivatedate] = 0;
		list($_SESSION[settings][activateyear],$_SESSION[settings][activatemonth],$_SESSION[settings][activateday]) = explode("-",$this->getField("activatedate"));
		list($_SESSION[settings][deactivateyear],$_SESSION[settings][deactivatemonth],$_SESSION[settings][deactivateday]) = explode("-",$this->getField("deactivatedate"));
		$_SESSION[settings][activatemonth]-=1;
		$_SESSION[settings][deactivatemonth]-=1;
		$_SESSION[settings][activatedate]=($this->getField("activatedate")=='0000-00-00')?0:1;
		$_SESSION[settings][deactivatedate]=($this->getField("deactivatedate")=='0000-00-00')?0:1;
	}

	function setActivateDate($year,$month=0,$day=0) {
		// test to see if it's a valid date
//		print "activate: $year-$month-$day<br>";
		if ($year == -1) { // unset field
			$this->setField("activatedate","0000-00-00");
			return true;
		}
		if (!checkdate($month,$day,$year)) {
			error("The activate date you entered is invalid. It has not been set.");
			return false;
		}
		$this->setField("activatedate",$year."-".$month."-".$day);
		return true;
	}
	
	function setDeactivateDate($year,$month=0,$day=0) {
		// test to see if it's a valid date
//		print "deactivate: $year-$month-$day<br>";
		if ($year == -1) { // unset field
			$this->setField("deactivatedate","0000-00-00");
			return true;
		}
		if (!checkdate($month,$day,$year)) {
			error("The deactivate date you entered is invalid. It has not been set.");
			return false;
		}
		$this->setField("deactivatedate",$year."-".$month."-".$day);
		return true;
	}
	
/******************************************************************************
 * cropString - crops a string to an appropriate length and adds elipses if
 * 				nessisary.
 ******************************************************************************/
	function cropString ($string, $maxChars) {
		$length = strlen($string);
		if ($length > $maxChars) {
			$length = $maxChars-3;
			$string =  substr($string,0,$length)."...";
		}
		return $string;
	}
	
/******************************************************************************
 * parseMediaTextForEdit - replaces ####<id>#### with appropriate filename info
 *			-> used for inline images from the media library in text
 ******************************************************************************/

	function parseMediaTextForEdit($field) {
		if (!$this->getField("$field")) return false;
		$this->data[$field] = ereg_replace("src=('{0,1})####('{0,1})","####",$this->getField($field));
		$textarray1 = explode("####", $this->getField($field));
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

/******************************************************************************
 * parseMediaTextForDB - does the exact opposite of above
 ******************************************************************************/

	function parseMediaTextForDB($field) {
		if (!$this->getField($field)) return false;
		$textarray1 = explode("&&&&", $this->getField($field));
		if (count($textarray1) > 1) {
			for ($i=1; $i<count($textarray1); $i=$i+2) {
				$textarray2 = explode("@@@@", $textarray1[$i]);
				$id = $textarray2[1];
				$textarray1[$i] = "src='####".$id."####'";
			}		
			$this->data[$field] = implode("",$textarray1);
		}
	}
	
/******************************************************************************
 * PERMISSIONS FUNCTIONS
 *
 * these functions handle part-specific permissions
 *	isEditor($user)		checks if $user is an editor for this part
 *	addEditor($e)		adds $e as an editor with default permissions (view only)
 *	delEditor($e)		removes all of $e's site permissions (ALL OF THEM)
 *	getEditors()		returns an array of editors for the site
 *	setPermissions($p)	set permissions to $p (a permission-formatted array)
 *	getPermissions()	returns a permission-formatted array of permissions
 *	clearPermissions()	flags all editor's scope-specific permissions to be removed
 *	setUserPermissions($user,$add,$edit,$del,$view,$discuss)
 *						sets $user's permissions to values of parameters (0 or 1)
 *	setUserPermissionsFromArray($user,$p)
 *						sets $user's permissions from permission-formatted array $p
 *	buildPermissionsArray()
 *						builds a permission-formatted array from the database
 *	updatePermissionsDB()
 *						updates the permissions database to reflect changes made above
 *	canview($user)		returns true/false depending on whether $user can view
 *						this part of the site. takes into account de/activate dates
 *						and active flag
 *	hasPermission($perms,$user)
 *						takes a formatted string $perms (ex, 'add and (edit or delete)')
 *						and returns true/false if $user has those permissions
 *	hasPermissionDown($perms,$user)
 *						checks if someone has $perms anywhere down the line
 ******************************************************************************/

	function isEditor($user='') {
		if (!$this->builtPermissions) $this->buildPermissionsArray();
		if ($user=='') $user=$_SESSION[auser];
		$this->fetchUp();
		$owner = $this->owningSiteObj->getField("addedby");
/* 		print "owner: $owner"; */
		if (strtolower($user) == strtolower($owner)) return 1;
		$toCheck = array(strtolower($user));
		$toCheck = array_merge($toCheck,$this->returnEditorOverlap(getuserclasses($user)));
		foreach ($this->editors as $e) {
			if (in_array(strtolower($e),$toCheck)) return 1;
		}
		return 0;
	}

	function addEditor($e) { 
		if ($e == 'institute' || $e == 'everyone') return false;
		if ($_SESSION[auser] == $e) { error("You do not need to add yourself as an editor."); return false; }
		if (!in_array($e,$this->editors)) {
			$this->editors[]=$e;
			$this->setUserPermissions($e);
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
			$this->changedpermissions = 1;
		}
	}
	
	function getEditors() {
		$this->buildPermissionsArray(0,0);
		return $this->editors;
	}
	
	function setPermissions($p) {
		if (is_array($p)) {
			$this->permissions = $p;
			$this->editors = array_unique(array_merge(array_keys($p),$this->editors));	// add new editors from new permissions array
			$this->changedpermissions = 1;
		}
	}
	
	function clearPermissions() {
/* 		print "Editors: <pre>"; print_r($this->getEditors()); print "</pre>"; */
		$this->editorsToDeleteInScope = array_unique(array_merge(array_keys($this->permissions),$this->getEditors()));
/* 		print "To Delete: <pre>"; print_r($this->editorsToDeleteInScope); print "</pre>"; */
		$this->editors = array();
		$this->permissions = array();
		$this->changedpermissions = 1;
	}
	
	function setUserPermissions($user,$add=0,$edit=0,$del=0,$view=0,$discuss=0) {
		$this->setUserPermissionsFromArray($user,array(ADD=>$add,EDIT=>$edit,DELETE=>$del,VIEW=>$view,DISCUSS=>$discuss));
	}
	
	function setUserPermissionsFromArray($user,$p) {
		$this->permissions[$user] = $p;
		$this->changedpermissions = 1;
	}
	
	function setUserPermissionDown($perm,$user,$val=1) {
		$class=get_class($this);
		$ar = $this->_object_arrays[$class];
		$p = strtoupper($perm);
		$c = permissions::$p();
		$this->permissions[$user][$c] = $val;
		
/* 		if ($class =="site") $n = 0; */
/* 		if ($class =="section")$n =2; */
/* 		if ($class =="page")$n = 4; */
/* 		else $n=6; */
/* 		$i = 0; */
/* 		while($i <= $n) { */
/* 			print " &nbsp; "; */
/* 			$i++; */
/* 		} */
/* 		print $class.": setting ".$this->id." permissions[".$user."][".$c."] = ".$val."<br>"; */
		
		$this->changedpermissions=1;
		if ($ar) {
			$a = &$this->$ar;
			if ($a) {
				foreach ($a as $i=>$o) $a[$i]->setUserPermissionDown($perm,$user,$val);
			}
		}
	}
	
	function getPermissions() {
		// returns an html-formable permissions array based on the permissions table
		return $this->permissions;
	}
	
	function movePermission($action, $user, $origSite, $moveLevel) {
		// determines whether user can move an object here
		if ($this->getField("type") != get_class($this)) return 0;
		if ($this->owning_site == $origSite) {
			if ($action == "COPY") {
				if ($this->hasPermission("add",$user)) return 1;
				if ($moveLevel != get_class($this) && $this->hasPermissionDown("add",$user)) return 1;
			} else {
				if ($this->hasPermission("add or edit",$user)) return 1;
				if ($moveLevel != get_class($this) && $this->hasPermissionDown("add or edit",$user)) return 1;
			}
		} else {
			if ($this->hasPermission("add",$user)) return 1;
			if ($moveLevel != get_class($this) && $this->hasPermissionDown("add",$user)) return 1;
		}
		return 0;
	}
	
/******************************************************************************
 * buildPermissionsArray - builds the permissions array for current obj from DB
 ******************************************************************************/

	function buildPermissionsArray($force=0,$down=0) {
		if (!$force && $this->builtPermissions) return;
		
		$scope = get_class($this);
		$site = $this->owning_site;
		$id = $this->id;
		$query = "select * from permissions where site='$site' and scope='$scope' and scopeid='$id'";
		$r = db_query($query);
		while ($a=db_fetch_assoc($r)) {
			$this->permissions[strtolower($a[user])] = array( permissions::ADD()=>$a[a], 
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
		$this->editors = array_unique($this->editors);
		$this->builtPermissions=1;
		
		if ($down) {
			$ar = $this->_object_arrays[$scope];
			if ($ar) {
				$a = &$this->$ar;
				if ($a) {
					foreach ($a as $i=>$o) {
						$a[$i]->buildPermissionsArray($force,$down);
					}
				}
			}
		}
	}
	
/******************************************************************************
 * spiderDownLockedFlag - used for editing permissions... sets the locked flag
 * 				on children of a section with certain permissions
 *				don't try to understand this, just use it. it works
 ******************************************************************************/

	function spiderDownLockedFlag() {
		$editors = $this->getEditors();
		$p = $this->getPermissions();
		
		$_a = array("add","edit","delete","view");
		
		foreach ($editors as $e) {
			for ($i=0;$i<4;$i++) {
				$this->checkLockedFlag($e,$_a[$i]);
			}
		}
	}
	
	function checkLockedFlag($e,$perm) {
		$this->buildPermissionsArray();		// just in case
		$p = $this->getPermissions();
		$_t = strtoupper($perm);
		$pid = permissions::$_t();
		$e = strtolower($e);
		if ($p[$e][$pid]) { // set locked flag
			$this->setFieldDown("l-$e-$perm",1);
			$this->setField("l-$e-$perm",0);
		} else {
			// keep going down the line
			$ar = $this->_object_arrays[get_class($this)];
			if ($ar) {
				$a = &$this->$ar;
				if ($a) {
					foreach ($a as $i=>$o)
						$a[$i]->checkLockedFlag($e,$perm);
				}
			}
		}
	}

/******************************************************************************
 * updatePermissionsDB - updates the permissions DB based on new permissions
 ******************************************************************************/

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
				db_query($query);
				print "$query<br>";
				print mysql_error()."<br><br>";
			}
			// delete the appropriate entries from the table
			foreach ($this->editorsToDelete as $e) {
				db_query("delete from permissions where user='$e' and site='$site'");
			}
			foreach ($this->editorsToDeleteInScope as $e) {
/* 				print "<br>delete from permissions where user='$e' and site='$site' and scope='$scope' and scopeid=$id"; */
				db_query("delete from permissions where user='$e' and site='$site' and scope='$scope' and scopeid=$id");
			}
		}
	}

/******************************************************************************
 * canview - checks if part is active & within date range. if so, forwards
 *    to hasPermission() to check view permissions
 ******************************************************************************/

	function canview($user="") {
		if ($user == "") $user = $_SESSION[auser];
		if ($user == 'anyuser') $noperms=1;
		$_ignore_types = array("page"=>array("heading","divider"));
		$scope = get_class($this);
		if ($_ignore_types[$scope][$this->getField("type")]) return 1;
		$this->fetchUp();
		if ($this->owningSiteObj->getField("addedby") == $user) return 1;
		if ($scope != 'story') {
			if (!$this->getField("active")) return 0;
		}
		if (!indaterange($this->getField("activatedate"),$this->getField("deactivatedate"))) return 0;
		if (!$noperms) return $this->hasPermission("view",$user);
		return 1;
	}

/******************************************************************************
 * hasPermission - checks to see if a user has certain permissions
 * 		$perms paramater can be a complex string consisting of ()'s, 'and',
 *		'or', and permission types: 'add','edit','delete','view','discuss'
 ******************************************************************************/

	function hasPermission($perms,$ruser='') {
		global $allclasses, $_logged_in, $cfg;
		
		if (!$this->builtPermissions) $this->buildPermissionsArray();
		
		if ($ruser=='') $user=$_SESSION[auser];
		else $user = $ruser;
		
		if (isset($this->cachedPermissions[$user.$perms])) return $this->cachedPermissions[$user.$perms];
		$this->fetchUp();
		$owner = $this->owningSiteObj->getField('addedby');
		if (strtolower($user) == strtolower($owner)) return true;
		
		$_a = array('add','edit','delete','view','discuss');
		
		// check if $perms is malformed
		$a = explode(' ',ereg_replace("([()]){1}","",$perms));
//		print_r($a);
		$i=0;$j=1;
		foreach ($a as $n) {
//			print "$i: $n: ".strlen($n)."<BR>";
			if (!strlen($n)) continue;
			if (!($i%2) && !in_array($n,$_a)) $j=0;
			if (!(($i+1)%2) && $n!='and' && $n!='or' && $n!='&&' && $n!='||') $j=0;
			if (!$j) {
				print "ERROR! loop: $i: Malformed permissions string: $perms<BR><BR>";
				return 0;
			}
			$i++;
		}
		// end
		
		$permissions = $this->getPermissions();
		$toCheck = array();
		if (strlen($user)) $toCheck[] = strtolower($user);
		$toCheck[] = "everyone";
		if ($_logged_in) $toCheck[] = "institute";
		else {
			// check if our IP is in inst_ips
			$good=0;
			$ip = $_SERVER[REMOTE_ADDR];
			if (is_array($cfg[inst_ips])) {
				foreach ($cfg[inst_ips] as $i) {
					if (ereg("^$i",$ip)) $good=1;
				}
			}
			if ($good) $toCheck[]="institute";
		}
		$toCheck = array_merge($this->returnEditorOverlap($allclasses),$toCheck);
		
		foreach ($permissions as $u=>$p) $permissions[strtolower($u)] = $p;
		
		$pArray = array();
		
//		print "$perms<BR>";
		
		$perms = str_replace('and','&&',$perms);
		$perms = str_replace('or','||',$perms);

		foreach ($toCheck as $u) {
			$exec = $perms;
			foreach ($_a as $p) {
				$exec = str_replace($p,'$permissions[\''.$u.'\'][permissions::'.strtoupper($p).'()]',$exec);
			}
			$pArray[] = "(".$exec.")";
		}
		$isgood = 0;
		$condition = '$isgood = ('.implode(' || ',$pArray).')?1:0;';
		eval($condition);
		$this->cachedPermissions[$user.$perms] = $isgood;	// cache this entry
//		print $condition;
		return $isgood;
	}
	
	function hasPermissionDown($perms,$user='') {
		if (!$this->fetcheddown) $this->fetchDown();
		if ($this->hasPermission($perms,$user)) return true;
		$class = get_class($this);
		$ar = $this->_object_arrays[$class];
		if ($ar) {
			$a = &$this->$ar;
			if ($a) {
				foreach ($a as $i=>$o) {
					if($a[$i]->hasPermissionDown($perms,$user)) return true;
				}
			}
		}
		return false;
	}
	
	function returnEditorOverlap($classes) {
		$toCheck = array();
		foreach ($this->editors as $u) {
			$good=0;
			$c = array();
			if (isclass($u)) $c[] = $u;
			if ($g = isgroup($u)) $c = array_merge($c,$g);
			foreach($c as $class) {
				if (is_array($classes[$class])) $good=1;
			}
			if ($good) $toCheck[]=strtolower($u);
		}
/* 		print_r($toCheck); */
		return $toCheck;
	}
}