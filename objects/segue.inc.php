<? /* $Id$ */
/******************************************************************************
 * Segue object - basis for all other section, page, and story objects
 ******************************************************************************/

class segue {
//	var $permissions = array("everyone"=>array(3=>1),"institute"=>array(3=>1));
	var $permissions = array();
//	var $editors = array("everyone","institute");
	var $editors = array();
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

	var $_encode = array("title","header","footer","shorttext","longertext","discussions","url");
	var $_parse = array("header","footer","shorttext","longertext");

/******************************************************************************
 * siteExists - checks if the site/slot already exists with a certain name $name
 ******************************************************************************/
	
	function siteExists($site) {
		$query = "
SELECT site_id
	FROM slot INNER JOIN site
		ON FK_site = site_id AND slot_name='$site'
";

//		echo $query."<br>";
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
			$a[$s] =& new site($s);
			$a[$s]->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,0,true);
		}
		return $a;
	}

/******************************************************************************
 * getAllSites - returns a list of all sites owned by $user
 ******************************************************************************/

	function getAllSites($user) {
		$sites = array();
		$query = "
SELECT
	slot_name
FROM
	slot
		INNER JOIN 
	user
		ON FK_owner = user_id
			AND
		user_uname = '$user'
";

		if (db_num_rows($r = db_query($query)))
			while ($a = db_fetch_assoc($r)) {
					$sites[] = $a[slot_name];
				}
		return $sites;
}

/******************************************************************************
 * getAllSitesWhereUserIsEditor - gets all sites where $user is an editor
 ******************************************************************************/

	function getAllSitesWhereUserIsEditor($user='') {
		if ($user == '') $user = $_SESSION[auser];

		// first, get all sites for which the user is an editor
		$query = "
			SELECT
				slot_name
			FROM
				slot
					INNER JOIN
				site
					ON slot.FK_site = site_id
					INNER JOIN 
				site_editors ON (
					site_id = site_editors.FK_site 
						AND 
					site_editors_type = 'user'
				)
					INNER JOIN
				user ON FK_editor = user_id AND user_uname='$user'
			WHERE
				slot.FK_owner != user_id
		";
		$r = db_query($query);
		$ar = array();
		if (db_num_rows($r))
			while ($a = db_fetch_assoc($r)) {
				$ar[] = $a[slot_name];
			}

		// now, if a user is a member of any groups, get all sites for which those groups are editors
		$query = "
			SELECT
				slot_name
			FROM
				slot
					INNER JOIN
				site
					ON slot.FK_site = site_id
					INNER JOIN 
				site_editors ON (
					site_id = site_editors.FK_site 
						AND 
					site_editors_type = 'ugroup'
				)
					INNER JOIN
				ugroup ON FK_editor = ugroup_id
					INNER JOIN
				ugroup_user ON ugroup_id = FK_ugroup
					INNER JOIN
				user ON FK_user = user_id AND user_uname='$user'";
		$r = db_query($query);
		if (db_num_rows($r))
			while ($a = db_fetch_assoc($r)) {
				$ar[] = $a[slot_name];
			}
			
		// the two queries will return unique values, but their union could have non-unique entries.
		// therefore, uniquize it.
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
 * getField - Will return the value of a field in the data array.
 *			$field should be the name of the field in the object, not the database
 *			
 *			If the value of the field has not yet been fetched from the database,
 *			it is fetched from the database, otherwise it is simply returned from 
 *			the data array.
 *			
 * 			Each class that extends segue has the following properties:
 *			
 *			An associative array called _datafields that associates the object 
 *			field name to a database join syntax and a database field name or pair of names.
 *			
 *			An array called _encode that holds the names of fields that need to
 *			have slashes added and urlencoding to save them into the database
 ******************************************************************************/
	function getField ($field) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		if (ereg("^l%",$field)) 
			return $this->data[$field];
		if ($this->tobefetched && !$this->fetched[$field] && $this->id) {	// we haven't allready gotten this data
													// and this object is in the database.
// 			print "<pre>".get_class($this)."  --$field---\n"; 
// 			print_r ($this->_datafields[$field][1]); 
//			print_r($this);
// 			print "</pre>"; 
//			echo "<br>HERE: ".$field."<br>";

			$query = "
				SELECT 
					".implode(",",$this->_datafields[$field][1])."
				FROM
					".$this->_datafields[$field][0]."
				WHERE
					".$this->_table."_id=".$this->id."
				ORDER BY
					".$this->_datafields[$field][2]."
			";
/* 			print $query; */
			
			if ($debug) 
				print "-----------beginning---------$field<br><pre>".$query; 
	
			db_connect($dbhost,$dbuser,$dbpass, $dbdb);
			$r = db_query($query);
			
			if ($debug) {
				print mysql_error()."<br>Numrows = ".db_num_rows($r);
				print "\n\nresult arrays:\n";
			}
			
			if (!db_num_rows($r)) {	// if we get no results
				if (in_array($field,$this->_object_arrays)) {
					// return an empty array
					$this->data[$field] = array();
				} else {
					return false;
				}
			}
			
			$valarray = array();
			while($a = db_fetch_assoc($r)) {
			//	print_r($a);
				
				if (count($this->_datafields[$field][1]) == 1) { 
					// We just want a single value
					$val = $a[$this->_datafields[$field][1][0]];
					$key = 0;
				} else {
					// we want a pair of values
					$val = $a[$this->_datafields[$field][1][0]];
					$key = $a[$this->_datafields[$field][1][1]];
				}

				// Decode this value if it is a member of _encode
				if (in_array($field,$this->_encode)) 
					$val = stripslashes(urldecode($val));

// UPDATE parseMediaTextForEdit *********************************************************************
// UPDATE parseMediaTextForEdit *********************************************************************
// UPDATE parseMediaTextForEdit *********************************************************************
//				if (in_array($field,$this->_parse)) 
//					$val = $this->parseMediaTextForEdit($val);

				if (count($this->_datafields[$field][1]) == 1) { 
					$valarray[] = $val;
				} else {
					$valarray[$key] = $val;
				}
/* 				print "<br>key = $key \nval = $val \nvalarray =\n"; */
//				print_r($valarray);
			}
			
			// only object_arrays should really be returning arrays to the data array.
			if (count($valarray) == 1 && !in_array($field,$this->_object_arrays))
				$this->data[$field] = $valarray[0];
			else
				$this->data[$field] = $valarray;
			$this->fetched[$field] = 1;
			
			if ($debug) {
				print "Valarray: ";
				print_r($valarray);
				print "\nInArray: \n$field"; 
				print_r($_object_arrays);
				print "<br>Is object?: ".((in_array($field,$this->_object_arrays))?"TRUE":"FALSE");
				print "</pre>----------end------------$field<br>";
			}
		}

		return $this->data[$field];
	}
	
	function fetchAllFields() {
		foreach ($this->_datafields as $key => $val) {
			$this->getField($key);
		}
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
		$this->fetchDown(1);

/* 		print "<br><br>Copying $thisClass ".$this->getField("title")." <br>"; */

		if ($thisClass == 'section') {
			$owning_site = $newParent->name;
			$this->id = 0;	// createSQLArray uses this to tell if we are inserting or updating
			$this->insertDB(1,$owning_site,$removeOrigional,$keepaddedby);
		}
		if ($thisClass == 'page') {
			$owning_site = $newParent->owning_site;
			$owning_section = $newParent->id;
			$this->id = 0;	// createSQLArray uses this to tell if we are inserting or updating
			$this->insertDB(1,$owning_site,$owning_section,$removeOrigional,$keepaddedby);
		}
		if ($thisClass == 'story') {
			$owning_site = $newParent->owning_site;
			$owning_section = $newParent->owning_section;
			$owning_page = $newParent->id;
			$this->id = 0;	// createSQLArray uses this to tell if we are inserting or updating
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
				$this->setActivateDate($_REQUEST[activateyear],$_REQUEST[activatemonth],$_REQUEST[activateday]);
			} else {
				$_SESSION[settings][activatedate] = 0;
				$this->setActivateDate(-1);
			}
			if (/* !$_REQUEST[link] &&  */$_REQUEST[deactivatedate]) {
				$_SESSION[settings][deactivatedate] = 1;
				$this->setDeactivateDate($_REQUEST[deactivateyear],$_REQUEST[deactivatemonth],$_REQUEST[deactivateday]);
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
		global $months, $months_values;
	//	print_r($_SESSION[settings][activatedate]);
		printc("<input type=hidden name='setformdates' value=1>");
		printc("<table>");
		printc("<tr><td align=right>");
		printc("Activate date:</td><td><input type=checkbox name='activatedate' value=1".(($_SESSION[settings][activatedate])?" checked":"")."> <select name='activateday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($_SESSION[settings][activateday] == $i)?" selected":"") . ">".$i."\n");
		}
		
		printc("</select>");
		printc("<select name='activatemonth'>");
		for ($i=1; $i<13; $i++) {
			printc("<option value='$i'" . (($_SESSION[settings][activatemonth] == $i)?" selected":"") . ">".$months[$i-1]."\n");
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
			printc("<option" . (($_SESSION[settings][deactivateday] == $i)?" selected":"") . ">".$i."\n");
		}
		printc("</select>\n");
		printc("<select name='deactivatemonth'>");
		for ($i=1; $i<13; $i++) {
			printc("<option value='$i'" . (($_SESSION[settings][deactivatemonth] == $i)?" selected":"") . ">".$months[$i-1]."\n");
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
//		$_SESSION[settings][activatemonth]-=1;
//		$_SESSION[settings][deactivatemonth]-=1;
/* 		echo $this->getField("activatedate")."<br>"; */
		$_SESSION[settings][activatedate]=($this->getField("activatedate")=='0000-00-00')?0:1;
		$_SESSION[settings][deactivatedate]=($this->getField("deactivatedate")=='0000-00-00')?0:1;
	}

	function setActivateDate($year,$month=0,$day=0) {
		// test to see if it's a valid date
		if ($year == -1) { // unset field
			$this->setField("activatedate","0000-00-00");
			return true;
		}

		if (!checkdate($month,$day,$year)) {
			error("The activate date you entered is invalid. It has not been set.");
			return false;
		}
		
		if ($month < 10) {
			$month = "0".$month;
		}
		if ($day < 10) {
			$day = "0".$day;
		}
		
		$this->setField("activatedate",$year."-".$month."-".$day);
		return true;
	}
	
	function setDeactivateDate($year,$month=0,$day=0) {
		// test to see if it's a valid date
		if ($year == -1) { // unset field
			$this->setField("deactivatedate","0000-00-00");
			return true;
		}
		if (!checkdate($month,$day,$year)) {
			error("The deactivate date you entered is invalid. It has not been set.");
			return false;
		}

		if ($month < 10) {
			$month = "0".$month;
		}
		if ($day < 10) {
			$day = "0".$day;
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
			for ($i=1; $i < count($textarray1); $i+=2) {
				$id = $textarray1[$i];
				$filename = db_get_value("media","media_tag","media_id=$id");
				$query = "
SELECT 
	slot_name 
FROM
	media 
		INNER JOIN 
	site ON media.FK_site = site_id
		INNER JOIN
	slot ON site_id = slot.FK_site
WHERE
	media_id = $id
";
				$a = db_fetch_assoc(db_query($query));
				$dir = $a[slot_name];
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
		if (!$this->builtPermissions && $this->id) $this->buildPermissionsArray();
		if ($user=='') $user=$_SESSION[auser];
		$this->fetchUp();
		$owner = $this->owningSiteObj->owner;
/* 		print "owner: $owner"; */
		if (strtolower($user) == strtolower($owner)) return 1;
		$toCheck = array(strtolower($user));
		$toCheck = array_merge($toCheck,$this->returnEditorOverlap(getuserclasses($user,"all")));
//		print_r($toCheck);
//		print_r($this->editors);
		foreach ($this->editors as $e) {
			if (in_array($e,$toCheck)) return 1;
		}
		return 0;
	}

	function addEditor($e) { 
		/* print "<br>Adding editor $e<br>"; */
//		if ($e == 'institute' || $e == 'everyone') return false;	// With the new permissions structure, this may be unwanted.
		if ($_SESSION[auser] == $e) { error("You do not need to add yourself as an editor."); return false; }
		if (!in_array($e,$this->editors)) {
			$this->editors[]=$e;
			$this->setUserPermissions($e);
			$this->changedpermissions = 1;
		}
	}

	function delEditor($e) {
		$class=get_class($this);
		if ($e == 'institute' || $e == 'everyone') return false;
		if (in_array($e,$this->editors)) {
			$n = array();
			foreach($this->editors as $v) {
				if ($v != $e) $n[]=$v;
			}
			$this->editors = $n;
			$this->setFieldDown("l%$e%add",0);
			$this->setFieldDown("l%$e%edit",0);
			$this->setFieldDown("l%$e%delete",0);
			$this->setFieldDown("l%$e%view",0);
			$this->setFieldDown("l%$e%discuss",0);
			$this->setUserPermissionDown("ADD",$e,0);
			$this->setUserPermissionDown("VIEW",$e,0);
			$this->setUserPermissionDown("EDIT",$e,0);
			$this->setUserPermissionDown("DELETE",$e,0);
			$this->setUserPermissionDown("DISCUSS",$e,0);
			$this->editorsToDelete[] = $e;
		}
	}
	
	function getEditors() {
		if (!$this->builtPermissions && $this->id)
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
	
/* 	function setPermissionsDown($p) { */
/* 		if (!$this->fetcheddown) $this->fetchDown(); */
/* 		$class=get_class($this); */
/* 		$ar = $this->_object_arrays[$class]; */
/* 		$this->setPermissions($p); */
/* 		if ($ar) { */
/* 			$a = &$this->$ar; */
/* 			if ($a) { */
/* 				foreach ($a as $i=>$o) { */
/* 					$a[$i]->setPermissionsDown($p); */
/* 				} */
/* 			} */
/* 		} */
/* 	} */
	
	function clearPermissions($editor = '') {
/* 		print "Editors: <pre>"; print_r($this->getEditors()); print "</pre>"; */
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
		if ($this->permissions[$user][$c] != $val) {
			$this->permissions[$user][$c] = $val;
			$this->cachedPermissions[$user.$perm] = $val;	// Update the cached permissions array so that
														// hasPermission doesn't get a fscked up
			$this->changedpermissions=1;
		}
		

/* 		if ($class == "site") $n = 0; */
/* 		else if ($class == "section")$n =4; */
/* 		else if ($class == "page")$n = 8; */
/* 		else $n=12; */
/* 		$i = 0; */
/* 		while($i <= $n) { */
/* 			print " &nbsp; "; */
/* 			$i++; */
/* 		} */
/* 		print $this->permissions[$user][$c]; */
/* 		print $class.": set -- has permission= -- should be: $val<br>"; */
/* 		print $this->permissions[$user][$c]; */
/* 		print "<pre>"; print_r($this->permissions[$user]); print "</pre>"; */
		
		if ($ar) {
			$a = &$this->$ar;
			if ($a) {
				foreach (array_keys($a) as $k=>$i) {
					$a[$i]->setUserPermissionDown($perm,$user,$val);
					$a[$i]->cachedPermissions[$user.$perm] = $val;	// Update the cached permissions array so that
																	// hasPermission doesn't get a fscked up
				}
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

		// the SQL queries for obtaining the permissions vary with the scope type. Thus, we have 4 cases, 1 for each scope type.
		
		// editors can be either institute, everyone, a username or a ugroup name
		// we need two queries for any one scope
		
		
		// CASE 1: scope is SITE
		if ($scope == 'site') {
		$query = "
SELECT
	user_uname as editor, ugroup_name as editor2, site_editors_type as editor_type,
	MAKE_SET(IFNULL(permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	site
		INNER JOIN
	site_editors ON
		site_id = ".$this->id."
			AND
		site_id = FK_site
		LEFT JOIN
	user ON
		site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup ON
		site_editors.FK_editor = ugroup_id
		LEFT JOIN
	permission ON
		site_id  = FK_scope_id
			AND
		permission_scope_type = 'site'
			AND
		permission.FK_editor <=> site_editors.FK_editor
			AND
		permission_editor_type = site_editors_type
";
		}

		// CASE 2: scope is SECTION
		else if ($scope == 'section') {
		$query = "
SELECT
	user_uname as editor, ugroup_name as editor2, site_editors_type as editor_type,
	MAKE_SET(IFNULL(p1.permission_value,0) | IFNULL(p2.permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	site
		INNER JOIN
	section
		ON site_id = section.FK_site
			AND
		section_id = ".$this->id."
		INNER JOIN
	site_editors ON
		site_id = site_editors.FK_site
		LEFT JOIN
	user ON
		site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup ON
		site_editors.FK_editor = ugroup_id
		LEFT JOIN
	permission as p1 ON
		site_id  = p1.FK_scope_id
			AND
		p1.permission_scope_type = 'site'
			AND
		p1.FK_editor <=> site_editors.FK_editor
			AND
		p1.permission_editor_type = site_editors_type
		LEFT JOIN 
	permission as p2 ON
		section_id  = p2.FK_scope_id
			AND
		p2.permission_scope_type = 'section'
			AND
		p2.FK_editor <=> site_editors.FK_editor
			AND
		p2.permission_editor_type = site_editors_type
";
		}

		// CASE 3: scope is PAGE
		else if ($scope == 'page') {
		$query = "
SELECT
	user_uname as editor, ugroup_name as editor2, site_editors_type as editor_type,
	MAKE_SET(IFNULL(p1.permission_value,0) | IFNULL(p2.permission_value,0) | IFNULL(p3.permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	site
		INNER JOIN
	section
		ON site_id = section.FK_site
		INNER JOIN
	page
		ON section_id = page.FK_section
			AND
		page_id = ".$this->id."
		INNER JOIN
	site_editors ON
		site_id = site_editors.FK_site
		LEFT JOIN
	user ON
		site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup ON
		site_editors.FK_editor = ugroup_id
		LEFT JOIN
	permission as p1 ON
		site_id  = p1.FK_scope_id
			AND
		p1.permission_scope_type = 'site'
			AND
		p1.FK_editor <=> site_editors.FK_editor
			AND
		p1.permission_editor_type = site_editors_type
		LEFT JOIN 
	permission as p2 ON
		section_id  = p2.FK_scope_id
			AND
		p2.permission_scope_type = 'section'
			AND
		p2.FK_editor <=> site_editors.FK_editor
			AND
		p2.permission_editor_type = site_editors_type
		LEFT JOIN
	permission as p3 ON
		page_id  = p3.FK_scope_id
			AND
		p3.permission_scope_type = 'page'
			AND
		p3.FK_editor <=> site_editors.FK_editor
			AND
		p3.permission_editor_type = site_editors_type
";
		}

		// CASE 4: scope is PAGE
		else if ($scope == 'story') {
		$query = "
SELECT
	user_uname as editor, ugroup_name as editor2, site_editors_type as editor_type,
	MAKE_SET(IFNULL(p1.permission_value,0) | IFNULL(p2.permission_value,0) | IFNULL(p3.permission_value,0) | IFNULL(p4.permission_value,0), 'v', 'a', 'e', 'd', 'di') as permissions
FROM
	site
		INNER JOIN
	section
		ON site_id = section.FK_site
		INNER JOIN
	page
		ON section_id = page.FK_section
		INNER JOIN
	story
		ON page_id = story.FK_page
			AND
		story_id = ".$this->id."
		INNER JOIN
	site_editors ON
		site_id = site_editors.FK_site
		LEFT JOIN
	user ON
		site_editors.FK_editor = user_id
		LEFT JOIN
	ugroup ON
		site_editors.FK_editor = ugroup_id
		LEFT JOIN
	permission as p1 ON
		site_id  = p1.FK_scope_id
			AND
		p1.permission_scope_type = 'site'
			AND
		p1.FK_editor <=> site_editors.FK_editor
			AND
		p1.permission_editor_type = site_editors_type
		LEFT JOIN 
	permission as p2 ON
		section_id  = p2.FK_scope_id
			AND
		p2.permission_scope_type = 'section'
			AND
		p2.FK_editor <=> site_editors.FK_editor
			AND
		p2.permission_editor_type = site_editors_type
		LEFT JOIN
	permission as p3 ON
		page_id  = p3.FK_scope_id
			AND
		p3.permission_scope_type = 'page'
			AND
		p3.FK_editor <=> site_editors.FK_editor
			AND
		p3.permission_editor_type = site_editors_type
		LEFT JOIN
	permission as p4 ON
		story_id = p4.FK_scope_id
			AND
		p4.permission_scope_type = 'story'
			AND
		p4.FK_editor <=> site_editors.FK_editor
			AND
		p4.permission_editor_type = site_editors_type
";
		}

		// execute the query
//		echo $query;
		$r = db_query($query);
		//echo "Query result: ".$r."<br>";
		
		
		// reset the editor array		
		if ($r) {
			$this->editors = array();
			$this->permissions = array();
		}
		
		// for every permisson entry, add it to the permissions array
		while ($row=db_fetch_assoc($r)) {
			// decode 'final_permissions'; 
			// 'final_permissions' is a field returned by the query and contains a string of the form "'a','vi','e'" etc.
			$a = array();
			$a[a] = (strpos($row[permissions],'a') !== false) ? 1 : 0; // look for 'a' in 'final_permissions'
			$a[e] = (strpos($row[permissions],'e') !== false) ? 1 : 0; // !== is very important here, because a position 0 is interpreted by != as FALSE
			$a[d] = (strpos($row[permissions],'d') !== false) ? 1 : 0;
			$a[v] = (strpos($row[permissions],'v') !== false) ? 1 : 0;
			$a[di] = (strpos($row[permissions],'di') !== false) ? 1 : 0;
			
			// if the editor is a user then the editor's name is just the user name
			// if the editor is 'institute' or 'everyone' then set the editor's name correspondingly
			if ($row[editor_type]=='user')
				$t_editor = $row[editor];
			else if ($row[editor_type]=='ugroup')
				$t_editor = $row[editor2];
			else
				$t_editor = $row[editor_type];
			
//			echo "<br><br>Editor: $t_editor; Add: $a[a]; Edit: $a[e]; Delete: $a[d]; View: $a[v];  Discuss: $a[di];";

			// set the permissions for this editor
//			$this->permissions[strtolower($t_editor)] = array(
			$this->permissions[$t_editor] = array(
				permissions::ADD()=>$a[a], 
				permissions::EDIT()=>$a[e], 
				permissions::DELETE()=>$a[d], 
				permissions::VIEW()=>$a[v], 
				permissions::DISCUSS()=>$a[di]
			);
			
			// now add the editor to the editor array
//			$this->editors[]=strtolower($t_editor);
			$this->editors[]=$t_editor;
		}
		
//		print_r($this->permissions);

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
//		print_r($editors);print "<br>";print_r($p);
		$_a = array("add","edit","delete","view");
		
		foreach ($editors as $e) {
//			print "doing flags for $e<br>";
			for ($i=0;$i<4;$i++) {
				$this->checkLockedFlag($e,$_a[$i]);
			}
		}
	}
	
	function checkLockedFlag($e,$perm) {
//		if (!$this->builtPermissions && $this->id)	//might be needed. unknown.
			$this->buildPermissionsArray();		// just in case
		$p = $this->getPermissions();
		$_t = strtoupper($perm);
		$pid = permissions::$_t();
		$e = $e;
		if ($p[$e][$pid]) { // set locked flag
			$this->setFieldDown("l%$e%$perm",1);
			$this->setField("l%$e%$perm",0);
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

			$n = array_unique(array_merge($this->editors,$this->editorsToDelete,array_keys($this->permissions)));
			
//			print_r($n);

			foreach ($n as $editor) {
				$p2 = $this->permissions[$editor];
				if (!is_array($p2)) {
//					echo "p2: ************************** BE CAREFUL!!!! ********************************<BR>";
					$p2 = array();
					$p2[ADD] = 0;
					$p2[EDIT] = 0;
					$p2[DELETE] = 0;
					$p2[VIEW] = 0;
					$p2[DISCUSS] = 0;
				}
//				print_r($p);

				// now get the permissions for the parent object. We need to do this so that we can determine whether
				// the child permissions have simply inherited the parent permissions, or they have added something new.
				
				// if a section object, get permissions for the site
				if ($scope == "section")
					$p1 = $this->owningSiteObj->permissions[$editor];
				// if a page object, get permissions for the parent section
				else if ($scope == "page")
					$p1 = $this->owningSectionObj->permissions[$editor];
				// if a story object, get permissions for the parent page
				else if ($scope == "story")
					$p1 = $this->owningPageObj->permissions[$editor];
					
				if (!is_array($p1) && $scope != 'site') {
//					echo "p1: ************************** BE CAREFUL!!!! ********************************<BR>";
					$p1 = array();
					$p1[ADD] = 0;
					$p1[EDIT] = 0;
					$p1[DELETE] = 0;
					$p1[VIEW] = 0;
					$p1[DISCUSS] = 0;
				}
					
					
					// note that if a certain permission is set in $p1, it is impossible that the same permission is not set in $p2 (because $p2 inherits $p1's permissions)
					// thus, there are 3 possibilities:
					// 1) $p1 - SET,   $p2 - SET   
					// 2) $p1 - UNSET, $p2 - SET
					// 3) $p1 - UNSET, $p2 - UNSET

				// now, put the inherited permissions in $p_inherit and the new permissions in $p_new
				$p_inherit = array();
				$p_new = array();
				if ($scope != "site") {
					foreach ($p1 as $key => $value)
						// in case 1) and 3) $p2 inherits $p1's permission
						if ($p1[$key] || (!$p1[$key] && !$p2[$key])) {
							$p_inherit[$key] = 1;
							$p_new[$key] = 0;
						}
						// in case 2), $p2 adds a new permission
						else {
							$p_inherit[$key] = 0;
							$p_new[$key] = 1;
						}
				}
				// if a site object
				else {
					$p_new = $p2; // everything is new
					foreach ($p2 as $key => $value)
						$p_inherit[$key] = 0; // nothing is inherited					
				}

				// convert $p_new to a "'a','v',..." format.
				$p_new_str = "";
				if ($p_new[ADD]) $p_new_str.="a,";
				if ($p_new[EDIT]) $p_new_str.="e,";
				if ($p_new[DELETE]) $p_new_str.="d,";
				if ($p_new[VIEW]) $p_new_str.="v,";
				if ($p_new[DISCUSS]) $p_new_str.="di,";
				
				if ($p_new_str) $p_new_str = substr($p_new_str, 0, strlen($p_new_str)-1); // strip last comma from the end of a string 

				// find the id and type of this editor
				if ($editor == 'everyone' || $editor == 'institute') {
					$ed_type = $editor;
					$ed_id = 'NULL';				
				}
				else if ($ugroup_id = ugroup::getGroupID($editor)) {
					$ed_type = 'ugroup';
					$ed_id = $ugroup_id;
				}
				else {
					$ed_type = 'user';
					// need to fetch the id from the user table
					$query = "SELECT user_id FROM user WHERE user_uname = '$editor'";
					$r = db_query($query);
					if (!db_num_rows($r)) {
						echo $query."<br>";
						die("updatePermissionsDB() :: could not find an ID to associate with editor: '$editor'!!!");
					}
					
					$arr = db_fetch_assoc($r);
					$ed_id = $arr['user_id'];
				}

//				echo "<br><br><b>***** New permissions in $scope #$id with editor $editor: '".$p_new_str."'</b><br>";
//				echo "EID: $ed_id; ETYPE: $ed_type <br>";
				

				// if this is a site object, see if the editor is in the site_editors table
				if ($scope == "site") {
					$query = "
SELECT
	FK_editor
FROM
	site_editors
WHERE
	FK_editor <=> $ed_id AND
	site_editors_type = '$ed_type' AND
	FK_site = $id
";
//					echo $query."<br>";
					$r_editor = db_query($query); // this query checks to see if the editor is in the site_editors table
					// if the editor is not in the site_editors then insert him
					if (!db_num_rows($r_editor)) {
						$query = "
INSERT
INTO site_editors
	(FK_site, FK_editor, site_editors_type)
VALUES
	($id, $ed_id, '$ed_type')
";					

//					echo $query."<br>";
						db_query($query);
					}
					
				}


				// now that we have all the information pertaining to this user, check if the permission entry is already present
				// if yes, update it
				// if not, insert it
				
				$query = "
SELECT 
	permission_id 
FROM permission 
WHERE 
	permission_scope_type='$scope' AND 
	FK_scope_id=$id AND 
	FK_editor <=> $ed_id AND 
	permission_editor_type = '$ed_type'
";
				

//				echo $query."<br>";
				$r_perm = db_query($query); // this query checks to see if the entry exists in the permission table
				
				// if permission entry exists
				if (db_num_rows($r_perm)) {
					$a = db_fetch_assoc($r_perm);
					// if we are changing the permissions, update the db
					if ($p_new_str) {
						$query = "UPDATE permission SET permission_value='$p_new_str' WHERE permission_id = ".$a[permission_id];
						echo $query."<br>";
						db_query($query);
					}
					// if we are clearing the permissions, delete the entry from the db
					else {
						$query = "DELETE FROM permission WHERE permission_id = ".$a[permission_id];
						db_query($query);
					}
				}
				// if permission entry does not exist in the permission table
				else if ($p_new_str) {
					// need to insert permissions
					$query = "
INSERT
INTO permission
	(FK_editor, permission_editor_type, FK_scope_id, permission_scope_type, permission_value)
VALUES ($ed_id, '$ed_type', $id, '$scope', '$p_new_str')
";
//						echo $query."<br>";
					db_query($query);
				}
			}
		}
	}
	
/******************************************************************************
 * deletePendingEditors() - takes care of editors in editorsToDelete and
 * 		editorsToDeleteInScope. 
 * 	THIS FUNCTION MUST BE CALLED AFTER updatePermissionsDB()!!!
 ******************************************************************************/

	function deletePendingEditors() {
			// if user wants to delete editors, remove their permissions from site_editors
			foreach ($this->editorsToDelete as $e) {
					$query = "SELECT user_id FROM user WHERE user_uname = '$e'";
					$r = db_query($query);
					$arr = db_fetch_assoc($r);
					$ed_id = $arr['user_id'];
					if ($ed_id) {
						$query = "DELETE FROM site_editors WHERE FK_editor = $ed_id AND site_editors_type = 'user' AND FK_site = ".$this->id;
						$r = db_query($query);
					}
			}
			$this->editorsToDelete = array();
			
			/*
			foreach ($this->editorsToDeleteInScope as $e) {
				db_query("delete from permissions where user='$e' and site='$site' and scope='$scope' and scopeid=$id");
			}
			*/
	}


/******************************************************************************
 * canview - checks if part is active & within date range. if so, forwards
 *    to hasPermission() to check view permissions
 ******************************************************************************/

	function canview($user="") {
		if ($user == "") $user = $_SESSION[auser];
		if ($user == 'anyuser') $noperms=1;
//		$_ignore_types = array("page"=>array("heading","divider"));
		$scope = get_class($this);
//		if ($_ignore_types[$scope][$this->getField("type")]) return 1;
//		print "<br>$scope - ".$this->getField("type");
		$this->fetchUp();
//		if (slot::getOwner($this->owningSiteObj->name) == $user) { return 1;}
		if ($this->owningSiteObj->owner == $user) { return 1;}
		if ($scope != 'story' && $this->getField("type") != 'heading') {
			if (!$this->getField("active")) return 0;
		}
		if (!indaterange($this->getField("activatedate"),$this->getField("deactivatedate"))) return 0;
		
		echo "<pre>\nCANVIEW\n";
		echo "USER: $user\n";
		print_r($this->canview);
		echo "\nCANVIEW </pre>";
		
		if (!$noperms) {
			if ($this->fetched_forever_and_ever) {
				// check if our IP is in inst_ips
				$good=0;
				$ip = $_SERVER[REMOTE_ADDR];
				if (is_array($cfg[inst_ips]))
					foreach ($cfg[inst_ips] as $i)
						if (ereg("^$i",$ip)) $good=1;

				if ($good) $institute = true;
				else $institute = false;

				return ($this->canview[everyone] || $this->canview[$user] || (($institute) ? $this->canview[institute] : true));
			}
			else
				//exit;
				return $this->hasPermissionDown("view",$user,0,1); 
		}
		return 1;
	}

/******************************************************************************
 * hasPermission - checks to see if a user has certain permissions
 * 		$perms paramater can be a complex string consisting of ()'s, 'and',
 *		'or', and permission types: 'add','edit','delete','view','discuss'
 ******************************************************************************/

	function hasPermission($perms,$ruser='',$useronly=0) {
		global $allclasses, $_loggedin, $cfg;
		
			
//		if (!$this->builtPermissions && $this->id) 
			$this->buildPermissionsArray();

		
		if ($ruser=='') $user=$_SESSION[auser];
		else $user = $ruser;

		if (!is_array($allclasses)) $allclasses = getuserclasses($user,"all");
		
		/* Debuging stuff */
/* 		$class = get_class($this); */
/* 		print "checking $perms for $user on  $class ".$this->id."<br>"; */
		
//		echo "Cached Permissions: <pre>";
//		print_r($this->cachedPermissions);
		
		if (isset($this->cachedPermissions[$user.$perms]) && count($this->cachedPermissions)) 
			return $this->cachedPermissions[$user.$perms];
		$this->fetchUp();
		$owner = $this->owningSiteObj->owner;
		
		if (strtolower($user) == strtolower($owner)) 
			return true;
			
//		echo "Id: ".$this->id.", Scope: ".get_class($this).", Title: ".$this->data[title]."<br>";

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
/* 		print "<pre>Permissions: "; print_r($permissions); print "</pre>"; */

		$toCheck = array();
		if (strlen($user)) $toCheck[] = strtolower($user);
		if (!$useronly) {
			$toCheck[] = "everyone";
			if ($_loggedin) $toCheck[] = "institute";
		
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
		if (!$useronly) $toCheck = array_merge($this->returnEditorOverlap($allclasses),$toCheck);
		
/* 		print "<pre>"; print_r($toCheck); print "</pre><br>"; */
		
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
/* 		print $this->id." ".$condition." == ".$isgood."<br>"; */
		return $isgood;
	}
	
	function hasPermissionDown($perms,$user='',$useronly=0,$fetch=0) {
 		if ($fetch) {
 			if (!$this->fetcheddown) $this->fetchDown();
		}
				
		if ($this->hasPermission($perms,$user,$useronly)) {
			return true;
		}
		$class = get_class($this);
		$ar = $this->_object_arrays[$class];
		if ($ar) {
			$a = &$this->$ar;
			if ($a) {
				foreach ($a as $i=>$o) {
					if($a[$i]->hasPermissionDown($perms,$user,$useronly)) return true;
				}
			}
		}
		return false;
	}
	
	function returnEditorOverlap($classes) {
		$toCheck = array();
//		print_r($classes);
		foreach ($this->editors as $u) {
			$good=0;
//			print "$u - ";
//			if (isclass($u)) print "class";
			$c = array();
			if (isclass($u)) $c[] = $u;
			if ($g = isgroup($u)) $c = array_merge($c,$g);
			foreach($c as $class) {
				if (is_array($classes[$class])) $good=1;
			}
			if ($good) $toCheck[]=$u;
		}
/* 		print_r($toCheck); */
		return $toCheck; 
	}
}
