<? /* $Id$ */

/******************************************************************************
 * page object - handles site pages
 ******************************************************************************/

class page extends segue {
	var $stories;
	var $_allfields = array("section_id","site_id","title","addedtimestamp","addedby",
						"editedby","editedtimestamp","activatedate","deactivatedate",
						"active","locked","showcreator","showdate","showhr","stories",
						"storyorder","type","url","ediscussion","archiveby");
	
	function page($insite,$insection,$id=0) {
		$this->owning_site = $insite;
		$this->owning_section = $insection;
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->data[section_id] = $insection;
		$this->init();
		$this->data[type] = "page";
	}
	
	function init($formdates=0) {
		$this->pages = array();
		if (!is_array($this->data)) $this->data = array();
		$this->data[title] = "";
		$this->data[activatedate] = $this->data[deactivatedate] = "0000-00-00";
		$this->data[active] = 1;
		$this->data[url] = "http://";
		$this->data[locked] = 0;
		$this->data[showcreator] = 0;
		$this->data[showdate] = 0;
		$this->data[archiveby] = "none";
		$this->data[ediscussion] = 0;
		$this->data[showcreator] = 0;
		$this->data[showdate] = 0;
		$this->data[showhr] = 0;
		$this->data[archiveby] = "none";
		$this->data[storyorder] = "";
		if ($this->id) $this->fetchFromDB();
		if ($formdates) $this->initFormDates();
	}
	
	function delete() {	// delete from db
		if (!$this->id) return false;
		$query = "delete from pages where id=".$this->id;
		db_query($query);
		
		// remove stories
		$this->fetchDown();
		foreach ($this->stories as $s=>$o) {
			$o->delete();
		}
		
		$this->clearPermissions();
		$this->updatePermissionsDB();
	}
	
	function addStory($id) {
		if (!is_array($this->getField("stories"))) $this->data[stories] = array();
		array_push($this->data["stories"],$id);
		$this->changed[stories]=1;
	}
	
	function delStory($id,$delete=1) {
		$d = array();
		foreach ($this->getField("stories") as $s) {
			if ($s != $id) $d[]=$s;
		}
		$this->data[stories] = $d;
		$this->changed[stories]=1;
		if ($delete) {
			$story = new story($this->owning_site,$this->owning_section,$this->owning_page,$id);
			$story->delete();
		}
	}
	
	function fetchUp() {
		if (!$this->fetchedup) {
			$this->owningSiteObj = new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB();
			$this->owningSectionObj = new section($this->owning_site,$this->owning_section);
			$this->owningSectionObj->fetchFromDB();
			$this->fetchedup = 1;
		}
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
/* 			print "---->page fetchdown".$this->id." full = $full<BR>"; */
			if (!$this->tobefetched || $full) $this->fetchFromDB(0,$full);
			foreach ($this->getField("stories") as $s) {
				$this->stories[$s] = new story($this->owning_site,$this->owning_section,$this->id,$s);
				$this->stories[$s]->fetchDown($full);
			}
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0,$force=0) {
		if ($id) $this->id = $id;
		if ($this->id) {
			$this->tobefetched=1;
			$this->id = $this->getField("id");
			
			if ($force) {
				foreach ($this->_allfields as $f) $this->getField($f);
			}
/* 			$query = "select * from pages where id=".$this->id." limit 1"; */
/* 			$this->data = db_fetch_assoc(db_query($query)); */
/* 			if (is_array($this->data)) { */
/* 				$this->fetched = 1; */
/* 				$this->buildPermissionsArray(); */
/* 				 */
/* 				$this->data[stories] = decode_array($this->data[stories]); */
/* 				 */
/* 				return true; */
/* 			} */
		}
		return $this->id;
	}
	
	function updateDB($down=0) {
		if ($this->changed) {
			$a = $this->createSQLArray();
			$a[] = "editedby='$_SESSION[auser]'";
			$a[] = "editedtimestamp = NOW()";
			$query = "update pages set ".implode(",",$a)." where id=".$this->id;
			db_query($query);
		}
		
		// update permissions
		$this->updatePermissionsDB();
		
		// add log entry
		log_entry("edit_page",$this->owning_site,$this->owning_section,$this->id,"$_SESSION[auser] edited page id ".$this->id." in site ".$this->owning_site);

		// update down
		if ($down) {
			if ($this->fetcheddown) {
				foreach ($this->stories as $i=>$o) $o->updateDB(1);
			}
		}
		return true;
	}
	
	function insertDB($down=0,$newsite=null,$newsection=0,$keepaddedby=0) {
		$origsite = $this->owning_site;
		$origid = $this->id;
		if ($newsite) $this->owning_site = $newsite;
		if ($newsection) {
			$this->owning_section = $newsection;
			$this->owningSectionObj = new section($newsite,$newsection);
		}
		
		$a = $this->createSQLArray(1);
		if (!$keepaddedby) {
			$a[] = "addedby='$_SESSION[auser]'";
			$a[] = "addedtimestamp = NOW()";
		} else {
			$a[] = "addedby='".$this->getField("addedby")."'";
			$a[] = "addedtimestamp='".$this->getField("addedtimestamp")."'";
		}

		$query = "insert into pages set ".implode(",",$a);
		print $query."<br>"; //debug
		db_query($query);
		
		$this->id = mysql_insert_id();
		
		$this->fetchUp();
		$this->owningSectionObj->addPage($this->id);
		$this->owningSectionObj->delPage($origid,0);
		$this->owningSectionObj->updateDB();
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		// add log entry
		log_entry("add_page",$this->owning_site,$this->owning_section,$this->id,"$_SESSION[auser] added page id ".$this->id." to site ".$this->owning_site);
		
		// insert down
		if ($down && $this->fetcheddown) {
			foreach ($this->stories as $i=>$o) $o->insertDB(1,$this->owning_site,$this->owning_section,$this->id,$keepaddedby);
		}
		return true;
	}
	
	function createSQLArray($all=0) {
		$d = $this->data;
		$a = array();
		
		if ($all || $this->changed[title]) $a[] = "title='".addslashes($d[title])."'";
		if ($all) $a[] = "site_id='".$this->owning_site."'";
		if ($all) $a[] = "section_id=".$this->owning_section;
		if ($all || $this->changed[activatedate]) $a[] = "activatedate='$d[activatedate]'";
		if ($all || $this->changed[deactivatedate]) $a[] = "deactivatedate='$d[deactivatedate]'";
		if ($all || $this->changed[active]) $a[] = "active=".(($d[active])?1:0);
		if ($all || $this->changed[type]) $a[] = "type='$d[type]'";
		if ($all || $this->changed[stories]) $a[] = "stories='".encode_array($d[stories])."'";
		if ($all || $this->changed[url]) $a[] = "url='$d[url]'";
		if ($all || $this->changed[ediscussion]) $a[] = "ediscussion=".(($d[ediscussion])?1:0);
		if ($all || $this->changed[archiveby]) $a[] = "archiveby='$d[archiveby]'";
		if ($all || $this->changed[showcreator]) $a[] = "showcreator='$d[showcreator]'";
		if ($all || $this->changed[showdate]) $a[] = "showdate='$d[showdate]'";
		if ($all || $this->changed[showhr]) $a[] = "showhr='$d[showhr]'";
		if ($all || $this->changed[storyorder]) $a[] = "storyorder='$d[storyorder]'";
		
		return $a;
	}
	
	
	function handleStoryArchive() {
		global $months;
/* 		global $usesearch; */
/* 		global $site,$section,$page; */
		$site = $this->owning_site;
		$section = $this->owning_section;
		$page=$this->id;
/* 		$stories = $this->getField("stories"); */
		$newstories = array();
		$pa = $this->getField("archiveby");
		
		if ($pa == 'none' || $pa == '') return;
		
		$_a = array("startday","startmonth","startyear","endday","endmonth","endyear","usestart","useend","usesearch");
		foreach ($_a as $a) $$a = $_REQUEST[$a];
		
		if (!$usesearch) {
			$endyear = date("Y");
			$endmonth = date("n");
			$endday = date("j");
		}
		printc("<div>");
	//	printc("<b>Search:</b> ");
		printc("Display content in date rage: ");
		printc("<form action='$PHP_SELF?$sid&action=site&site=$site&section=$section&page=$page' method=post>");
		printc("<input type=hidden name=usesearch value=1>");
	
		printc("<select name='startday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($startday == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='startmonth'>");
		for ($i=0; $i<12; $i++)
			printc("<option value=".($i+1). (($startmonth == $i+1)?" selected":"") . ">$months[$i]\n");
		
		printc("</select>\n<select name='startyear'>");
		$curryear = date("Y");
		for ($i=$curryear-10; $i <= ($curryear); $i++) {
			printc("<option" . (($startyear == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
	//	printc("<br>");
		printc(" to <select name='endday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($endday == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='endmonth'>");
		for ($i=0; $i<12; $i++) {
			printc("<option value=".($i+1) . (($endmonth == $i+1)?" selected":"") . ">$months[$i]\n");
		}
		printc("</select>\n<select name='endyear'>");
		for ($i=$curryear; $i <= ($curryear+5); $i++) {
			printc("<option" . (($endyear == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
		printc(" <input type=submit class=button value='go'>");
		printc("</form></div>");
	
		$start = mktime(1,1,1,$startmonth,$startday,$startyear);
		$end = mktime(1,1,1,$endmonth,$endday,$endyear);
		if ($pa == 'week') {
			if (!$usesearch) {
				$start = mktime(0,0,0,date("n"),date('j')-7,date('Y'));
				$end = time();
			}
		}
		if ($pa == 'month') {
			if (!$usesearch) {
				$start = mktime(0,0,0,date("n")-1,date('j'),date("Y"));
				$end = time();
			}
		}
		if ($pa == 'year') {
			if (!$usesearch) {
				$start = mktime(0,0,0,date("n"),date('j'),date("Y")-1);
				$end = time();
			}
		}
		$txtstart = date("n/j/y",$start);
		$txtend = date("n/j/y",$end);
		$this->fetchDown();
		foreach ($this->stories as $s=>$o) {
			$added = $o->getField("addedtimestamp");
			ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",$added,$regs);
			$year = (integer)$regs[1];
			$month = (integer)$regs[2];
			$day = (integer)$regs[3];
			$t = mktime(0,0,0,$month,$day,$year);
	// 			$week = date("W",$t-(date("w",$t)*86400)); 
	//
	// 			if ($startyear == $year && $startweek == $week) 
	// 				$newstories[] = $s; 
	// 		if ((!$usestart || $start < $t) && (!$useend || $t < $end)) { 
			if (($start < $t) && ($t < $end) || false) {
				$newstories[$s] = $t;
			}
		
		}
	// 	print_r($newstories); 
		arsort($newstories,SORT_NUMERIC);
	// 	print_r($newstories);
		$this->setField("stories",array_keys($newstories));
		$a = array();
		foreach ($this->getField("stories") as $s)
			$a[$s] = $this->stories[$s];
		$this->stories = $a;
/* 		$this->fetcheddown=0;$this->fetchDown(); */
		printc("<b>Content ranging from $txtstart to $txtend.</b><br><BR>");
	}

	function handleStoryOrder() {
		// reorders the stories array passed to it depending on the order specified.
		// Orders: addedesc, addedasc, editeddesc, editedasc, author, editor, category, titledesc, titleasc
		$newstories = array();
		$order = $this->getField("storyorder");
		if ($order == '' || $order=='custom') return;
		$this->fetchDown();
		foreach ($this->stories as $s=>$o) {
			$added = ereg_replace("[:- ]","",$o->getField("addedtimestamp"));
/* 			$added = str_replace("-","",$added); */
/* 			$added = str_replace(" ","",$added); */
	
			if ($order == "addeddesc" || $order == "addedasc") 
				$newstories[$s] = $added;
			else if ($order == "editeddesc" || $order == "editedasc") 
				$newstories[$s] = $o->getField("editedtimestamp");
			else if ($order == "author") 
				$newstories[$s] = $o->getField("addedby");
			else if ($order == "editor") 
				$newstories[$s] = $o->getField("editedby");
			else if ($order == "category") 
				$newstories[$s] = $o->getField("category");
			else if ($order == "titledesc" || $order == "titleasc") 
				$newstories[$s] = strtolower($o->getField("title"));
		}
		
		if ($order == "addeddesc" || $order == "editeddesc")
			arsort($newstories,SORT_NUMERIC);
		else if ($order == "addedasc" || $order == "editedasc")
			asort($newstories,SORT_NUMERIC);
		else if ($order == "titledesc")
			arsort($newstories);
		else
			asort($newstories);
		
		foreach ($newstories as $id=>$n) {
			$newstories[$id] = $this->stories[$id];
		}
		$this->stories = $newstories;
		$this->setField("stories",array_keys($newstories));
	}
}