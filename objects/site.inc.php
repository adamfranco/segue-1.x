<? /* $Id$ */

class site extends segue {
	var $sections;
	var $name;
	var $_allfields = array("name","title","theme","themesettings","header","footer",
						"addedby","editedby","editedtimestamp","addedtimestamp",
						"activatedate","deactivatedate","active","sections",
						"listed","type");

	// fields listed in $_datafields are stored in the database.
	// the first element is the table join syntax required to pull the data.
	// the second element is an array of the database fields we will be selecting
	// the third element is the database field by which we will sort
	
	var $_datafields = array(
		"id" => array(
			"site",
			array("site_id"),
			"site_id"
		),
		"name" => array(
			"site
				INNER JOIN
			slot
				ON site_id = FK_site",
			array("slot_name"),
			"site_id"
		),
		"type" => array(
			"site
				INNER JOIN
			slot
				ON site_id = FK_site",
			array("slot_type"),
			"site_id"
		),
		"title" => array(
			"site",
			array("site_title"),
			"site_id"
		),
		"activatedate" => array(
			"site",
			array("site_activate_tstamp"),
			"site_id"
		),
		"deactivatedate" => array(
			"site",
			array("site_deactivate_tstamp"),
			"site_id"
		),
		"active" => array(
			"site",
			array("site_active"),
			"site_id"
		),
		"listed" => array(
			"site",
			array("site_listed"),
			"site_id"
		),
		"theme" => array(
			"site",
			array("site_theme"),
			"site_id"
		),
		"themesettings" => array(
			"site",
			array("site_themesettings"),
			"site_id"
		),
		"header" => array(
			"site",
			array("site_header"),
			"site_id"
		),
		"footer" => array(
			"site",
			array("site_footer"),
			"site_id"
		),
		"editedby" => array(
			"site
				INNER JOIN
			user
				ON FK_updatedby = user_id",
			array("user_uname"),
			"site_id"
		),
		"editedtimestamp" => array(
			"site",
			array("site_updated_tstamp"),
			"site_id"
		),
		"addedby" => array(
			"site
				INNER JOIN
			 user
			 	ON FK_createdby = user_id",
			array("user_uname"),
			"site_id"
		),
		"addedtimestamp" => array(
			"site",
			array("site_created_tstamp"),
			"site_id"
		),
		"sections" => array(
			"site
				INNER JOIN
			section
				ON site_id = FK_site",
			array("section_id"),
			"section_order"
		)
	);
	var $_table = "site";
	
	
	function site($name) {
		// find if a site with this name already exists in the databse, and if yes, get site_id
		global $dbuser, $dbpass, $dbdb, $dbhost;
		db_connect($dbhost,$dbuser,$dbpass, $dbdb);
		$q = "SELECT site_id FROM site INNER JOIN slot ON site_id = FK_site AND slot_name = '$name'";
		// echo $q;
		$r = db_query($q);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			$this->id = $a[site_id];
		}

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
		if (!$this->fetchedup) {
			$this->owningSiteObj = &$this;
			$this->fetchedup = 1;
		}
	}
	
	function fetchFromDB($force=0) {
		global $dbuser, $dbpass, $dbdb, $dbhost;
		global $cfg;
		// take this out when appropriate & replace occurences;
		global $uploaddir;
		
		$this->tobefetched=1;
		
		//$this->id = $this->getField("id"); // why need to do this?
		
		if ($force) {
			// the code below is inefficient! why fetch each field separately when we can fetch all fields at same time
			// thus we can cut the number of queries significantly
/*			foreach ($this->_allfields as $f) {
				$this->getField($f);
			}
*/

			// connect to db and initialize data array
 			db_connect($dbhost,$dbuser,$dbpass, $dbdb);
			$this->data = array();

			// first fetch all fields that are not part of a 1-to-many relationship
 			$query = "
SELECT  site_title AS title, site_activate_tstamp AS activatedate, site_deactivate_tstamp AS deactivatedate,
		site_active AS active, site_listed AS listed, site_theme AS theme, site_themesettings AS themesettings,
		site_header AS header, site_footer AS footer, site_updated_tstamp AS editedtimestamp, site_created_tstamp AS addedtimestamp,
		user_createdby.user_uname AS addedby, user_updatedby.user_uname AS editedby, slot_name as name, slot_type AS type

FROM 
	site
		INNER JOIN
	user AS user_createdby
		ON FK_createdby = user_createdby.user_id
		INNER JOIN
	user AS user_updatedby
		ON FK_updatedby = user_updatedby.user_id
		INNER JOIN
	slot
		ON site_id = FK_site
WHERE site_id = ".$this->id;

/* 			print "<pre>"; */
/* 			print_r ($this); */
/* 			print "</pre>"; */
/* 			print "\$query=<br>$query<br>"; */
			$r = db_query($query);
/* 			print "\$r=".$r."<br>"; */
			$a = db_fetch_assoc($r);
/* 			print "\$a=$a"; */
			array_change_key_case($a); // make all keys lower case
			// for each field returned by the query
			foreach ($a as $field => $value)
				// make sure we have defined this field in the _allfields array
				if (in_array($field,$this->_allfields)) {
					// decode if necessary
					if (in_array($field,$this->_encode)) 
						$value = stripslashes(urldecode($value));
	// UPDATE parseMediaTextForEdit *********************************************************************
	// UPDATE parseMediaTextForEdit *********************************************************************
	// UPDATE parseMediaTextForEdit *********************************************************************
	//				if (in_array($field,$this->_parse)) 
	//					$value = $this->parseMediaTextForEdit($value);
					$this->data[$field] = $value;
					$this->fetched[$field] = 1;
				}
				else
					echo "ERROR: field $field not in _allfields!!!<br>";
			

			// now fetch the sections (they are part of a 1-to-many relationship and therefore
			// we cannot fetch them along with the other fields)			
			$query = "
SELECT
	section_id
FROM
	site
		INNER JOIN
	section
		ON site_id = FK_site
WHERE site_id = ".$this->id."
ORDER BY
	section_order
";

			$r = db_query($query);
			$this->data[sections] = array();
			while ($a = db_fetch_assoc($r))
				$this->data[sections][] = $a[section_id];
			$this->fetched[sections] = 1;
		}
		
		return $this->id;
	}
	


	function applyTemplate ($template) {
		$templateObj = new site($template);
		$templateObj->fetchDown(1);	
		/* print "<pre>"; print_r($this); print_r($templateObj); print "</pre>"; */
		foreach ($templateObj->sections as $i=>$o) 
			$o->copyObj($this);
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
		// the easy step: update the fields in the table
			$a = $this->createSQLArray();
			$a[] = "FK_updatedby=".$_SESSION[aid];
//			$a[] = "editedtimestamp=NOW()";  // no need to do this anymore, MySQL will update the timestamp automatically
			$query = "UPDATE site SET ".implode(",",$a)." WHERE site_id=".$this->id;
/*  			print "site->updateDB: $query<BR>"; */
			db_query($query);
			print mysql_error()."<br>";

		// the hard step: update the fields in the JOIN tables

			// first update 'slot_name' in the slot table, if the latter has changed
			if ($this->changed[name]) {
				$new_name = $this->data[name];
				$query = "UPDATE slot SET slot_name = '$new_name' WHERE FK_site=".$this->id;
				db_query($query);
			}

/* 			// now update all the section ids in the children, if the latter have changed */
/* 			if ($this->changed[sections]) { */
/* 				// first, a precautionary step: reset the parent of every section that used to have this site object as the parent */
/* 				// we do this, because we might have removed a certain section from the array of sections of a site object */
/* 				$query = "UPDATE section SET FK_site=0 WHERE FK_site=".$this->id; */
/* 				db_query($query); */
/* 				 */
/* 				// now, update all sections */
/* 				foreach ($this->data['sections'] as $k=>$v) { */
/* 					$query = "UPDATE section SET FK_site=".$this->id.", section_order=$k WHERE section_id=".$v; */
/* 					db_query($query); */
/* 				} */
/* 				 */
/* 			} */
		}
		


// REVISE THIS =================================================================
// REVISE THIS =================================================================
// REVISE THIS =================================================================
		// now update the permissions
		$this->updatePermissionsDB();
// REVISE THIS =================================================================
// REVISE THIS =================================================================
// REVISE THIS =================================================================
		
		// add log entry
/* 		log_entry("edit_site",$this->name,"","","$_SESSION[auser] edited ".$this->name); */
		
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
		$a[] = "FK_createdby=".$_SESSION[aid];
		$a[] = $this->_datafields[addedtimestamp][1][0]."=NOW()";
		$a[] = "FK_updatedby=".$_SESSION[aid];

		// insert into the site table
		$query = "INSERT INTO site SET ".implode(",",$a).";";
/*  		print "<BR>query = $query<BR>"; */
		db_query($query);
		$this->id = mysql_insert_id();
		
/* 		print "<H1>ID = ".$this->id."</H1>"; */
		
		// in order to insert a site, the active user must own a slot
		// update the name for that slot
		if (slot::exists($this->data[name])) {
			$query = "UPDATE slot";
			$where = " WHERE slot_name = '".$this->data[name]."' AND FK_owner = ".$_SESSION[aid];
		} else {
			$query = "INSERT INTO slot";
			$where = "";
		}
		$query .= " SET slot_name = '".$this->data[name]."',FK_owner=".$_SESSION[aid].",slot_type='".$this->data[type]."', FK_site = ".$this->id.$where;
		echo $query."<br>";
		db_query($query);
		
		// the sections haven't been created yet, so we don't have to insert data[sections] for now

		// add new permissions entry.. force update
// REVISE THIS =================================================================
// REVISE THIS =================================================================
// REVISE THIS =================================================================
		$this->updatePermissionsDB(1);
// REVISE THIS =================================================================
// REVISE THIS =================================================================
// REVISE THIS =================================================================
		
		// add log entry
/* 		log_entry("add_site",$this->name,"","","$_SESSION[auser] added ".$this->name); */
		
		// insert down (insert sections)
		if ($down && $this->fetcheddown && $this->sections) {
			foreach ($this->sections as $i=>$o) {
				$o->id = 0;	// createSQLArray uses this to tell if we are inserting or updating
				$o->insertDB(1,$this->name,$copysite);
			}
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
		$this->fetchDown();
		$query = "DELETE FROM site WHERE site_id=".$this->id;
		db_query($query);
		$query = " UPDATE slot SET FK_site=NULL WHERE FK_site=".$this->id;
		db_query($query);
		
		// remove sections
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
		
		if ($all || $this->changed[title]) $a[] = $this->_datafields[title][1][0]."='".addslashes($d[title])."'";
		if ($all || $this->changed[listed]) $a[] = $this->_datafields[listed][1][0]."='$d[listed]'";
		if ($all || $this->changed[activatedate]) $a[] = $this->_datafields[activatedate][1][0]."='".ereg_replace("-","",$d[activatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[deactivatedate]) $a[] = $this->_datafields[deactivatedate][1][0]."='".ereg_replace("-","",$d[deactivatedate])."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[active]) $a[] = $this->_datafields[active][1][0]."='$d[active]'";
//		if ($all || $this->changed[type]) $a[] = $this->_datafields[type][1][0]."='$d[type]'";
		if ($all || $this->changed[theme]) $a[] = $this->_datafields[theme][1][0]."='$d[theme]'";
		if ($all || $this->changed[themesettings]) $a[] = $this->_datafields[themesettings][1][0]."='$d[themesettings]'";
		if ($all || $this->changed[header]) $a[] = $this->_datafields[header][1][0]."='".urlencode($d[header])."'";
		if ($all || $this->changed[footer]) $a[] = $this->_datafields[footer][1][0]."='".urlencode($d[footer])."'";

		return $a;
	}
}