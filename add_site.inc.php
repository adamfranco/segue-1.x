<? // add_site.inc.php -- add/edit a site (passed $sitename)

if (isset($_SESSION[settings])) {
	// if we have already started editing...

	// ---- Editor actions ----
	if ($edaction == 'add') {
		if ($settings[editors])
			$edlist = explode(",",$settings[editors]);
		else $edlist = array();
		if (!in_array($edname,$edlist) && $edname != $auser) $edlist[]=$edname;
		$editors = implode(",",$edlist);
		if ($edname == $auser) error("You do not need to add yourself as an editor.");
		
		// eventually -- something like $_SESSION[siteObj]->addEditor($edname)
		// same for below
	}
	
	if ($edaction == 'del') {
		if ($settings[editors])
			$edlist = explode(",",$settings[editors]);
		else $edlist = array();
		$nlist = array();
		foreach ($edlist as $e) {
			if ($e != $edname) $nlist[]=$e;
		}
		$editors = implode(",",$nlist);
	}

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($settings[step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
	if ($_REQUEST[sitename] != ""  && $_SESSION[ltype] == "admin") { $_SESSION[siteObj]->setSiteName($_REQUEST[sitename]); $_SESSION[settings][sitename] = $_REQUEST[sitename]; }
	if ($_REQUEST[type] && $_SESSION[ltype]=='admin') $_SESSION[siteObj]->setField("type",$_REQUEST[type]);
	if ($_SESSION[settings][step] == 1 && $_REQUEST[title] != "") $_SESSION[siteObj]->setField("title",$_REQUEST[title]);
	if ($_REQUEST[activateyear] != "") $_SESSION[settings][activateyear] = $_REQUEST[activateyear];
	if ($_REQUEST[activatemonth] != "") $_SESSION[settings][activatemonth] = $_REQUEST[activatemonth];
	if ($_REQUEST[activateday] != "") $_SESSION[settings][activateday] = $_REQUEST[activateday];
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link] && $_SESSION[settings][activatedate]) $_SESSION[siteObj]->setActivateDate($_REQUEST[activateyear],$_REQUEST[activatemonth],$_REQUEST[activateday]);
	if ($_REQUEST[deactivateyear] != "") $_SESSION[settings][deactivateyear] = $_REQUEST[deactivateyear];
	if ($_REQUEST[deactivatemonth] != "") $_SESSION[settings][deactivatemonth] = $_REQUEST[deactivatemonth];
	if ($_REQUEST[deactivateday] != "") $_SESSION[settings][deactivateday] = $_REQUEST[deactivateday];
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link] && $_SESSION[settings][deactivatedate]) $_SESSION[siteObj]->setDeactivateDate($_REQUEST[deactivateyear],$_REQUEST[deactivatemonth],$_REQUEST[deactivateday]);
	if ($_REQUEST[active] != "") $_SESSION[siteOjb]->setField("active",$_REQUEST[active]);
//	if ($_REQUEST[viewpermissions] != "") $_SESSION[settings][viewpermissions] = $_REQUEST[viewpermissions];
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[siteObj]->setField("listed",$_REQUEST[listed]);
	if ($_REQUEST[theme] != "") $_SESSION[siteObj]->setField("theme",$_REQUEST[theme]);
	if ($_REQUEST[theme] != "") $_SESSION[siteObj]->setField("themesettings",$_REQUEST[themesettings]);
	if ($_SESSION[settings][step] == 3) $_SESSION[siteObj]->setField("template",$_REQUEST[template]);
//	if ($settings[step] == 4 && !$_REQUEST[link]) $_SESSION[settings][editors] = strtolower($_REQUEST[editors]);
//	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[settings][permissions] = $_REQUEST[permissions];
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[settings][recursiveenable] = $_REQUEST[recursiveenable];
	if ($_REQUEST[copydownpermissions] != "") $_SESSION[settings][copydownpermissions] = $_REQUEST[copydownpermissions];
	if ($_REQUEST[copyfooter]) $_SESSION[siteObj]->setField("header",$_SESSION[siteObj]->getField("footer"));
	if ($_REQUEST[copyheader]) $_SESSION[siteObj]->setField("footer",$_SESSION[siteObj]->getField("header"));	
	
/* 	if ($settings[step] == 5) { */
/* 		$settings[header] = str_replace("src='####","####",$header); */
/* 		$settings[header] = str_replace("src=####","####",$settings[header]); */
/* 		$settings[header] = str_replace("####'","####",$settings[header]); */
/* 		$textarray1 = explode("####", $settings[header]); */
/* 		if (count($textarray1) > 1) { */
/* 			for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 				$id = $textarray1[$i]; */
/* 				$filename = db_get_value("media","name","id=$id"); */
/* 				$dir = db_get_value("media","site_id","id=$id"); */
/* 				$filepath = $uploadurl."/".$dir."/".$filename; */
/* 				$textarray1[$i] = "&&&& src='".$filepath."' @@@@".$id."@@@@ &&&&"; */
/* 			}		 */
/* 			$settings[header] = implode("",$textarray1); */
/* 		} */
/* 	} */
/* 	if ($settings[step] == 6) { */
/* 		$settings[footer] = str_replace("src='####","####",$footer); */
/* 		$settings[footer] = str_replace("src=####","####",$settings[footer]); */
/* 		$settings[footer] = str_replace("####'","####",$settings[footer]); */
/* 		$textarray1 = explode("####", $settings[footer]); */
/* 		if (count($textarray1) > 1) { */
/* 			for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 				$id = $textarray1[$i]; */
/* 				$dir = db_get_value("media","site_id","id=$id"); */
/* 				$filepath = $uploadurl."/".$dir."/".$filename; */
/* 				$textarray1[$i] = "&&&& src='".$filepath."' @@@@".$id."@@@@ &&&&"; */
/* 			}		 */
/* 			$settings[footer] = implode("",$textarray1); */
/* 		} */
/* 	} */
}

if (!isset($_SESSION["settings"]) || !isset($_SESSION["siteObj"])) {
	// create the settings array with default values. $settings must be passed along with each link.
	// The array will be saved on clicking a save button.
	$_SESSION[settings] = array(
		"sitename" => $_REQUEST[sitename],
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"activateyear" => "0000",
		"activatemonth" => "00",
		"activateday" => "00",
		"deactivateyear" => "0000",
		"deactivatemonth" => "00",
		"deactivateday" => "00",
		"recursiveenable" => "",
		"copydownpermissions" => "",
		"commingFrom" => $_REQUEST[commingFrom]
	);
	$_SESSION[siteObj] = new site($_REQUEST[sitename]);
	
	if (isclass($_REQUEST[sitename])) $_SESSION[siteObj]->setField("type","class");
	
	if ($_REQUEST[action] == 'add_site') {
		$_SESSION[settings][add]=1;
		$_SESSION[settings][edit]=0;
	}	
	if ($_REQUEST[action] == 'edit_site') { 
		$_SESSION[settings][add]=0;
		$_SESSION[settings][edit]=1;		
	}
	
	if ($_SESSION[settings][edit]) {
		$_SESSION[siteObj]->fetchFromDB();
		list($_SESSION[settings][activateyear],$_SESSION[settings][activatemonth],$_SESSION[settings][activateday]) = explode("-",$_SESSION[siteObj]->getField("activatedate"));
		list($_SESSION[settings][deactivateyear],$s_SESSION[ettings][deactivatemonth],$s_SESSION[ettings][deactivateday]) = explode("-",$_SESSION[siteObj]->getField("deactivatedate"));
		$_SESSION[settings][activatemonth]-=1;
		$_SESSION[settings][deactivatemonth]-=1;
		$_SESSION[settings][activatedate]=($_SESSION[settings][activatedate]=='0000-00-00')?0:1;
		$_SESSION[settings][deactivatedate]=($_SESSION[settings][deactivatedate]=='0000-00-00')?0:1;
//		$settings[permissions] = decode_array($settings[permissions]);
		$_SESSION[settings][copydownpermissions] = decode_array($_SESSION[settings][copydownpermissions]);
		$_SESSION[settings][site_owner] = $_SESSION[siteObj]->getField("addedby");	
/*  */
/* 		$settings[header] = str_replace("src='####","####",$settings[header]); */
/* 		$settings[header] = str_replace("src=####","####",$settings[header]); */
/* 		$settings[header] = str_replace("####'","####",$settings[header]); */
/* 		$textarray1 = explode("####", $settings[header]); */
/* 		if (count($textarray1) > 1) { */
/* 			for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 				$id = $textarray1[$i]; */
/* 				$filename = db_get_value("media","name","id=$id"); */
/* 				$dir = db_get_value("media","site_id","id=$id"); */
/* 				$filepath = $uploadurl."/".$dir."/".$filename; */
/* 				$textarray1[$i] = "&&&& src='".$filepath."' @@@@".$id."@@@@ &&&&"; */
/* 			}		 */
/* 			$settings[header] = implode("",$textarray1); */
/* 		} */
/* 		$settings[footer] = str_replace("src='####","####",$settings[footer]); */
/* 		$settings[footer] = str_replace("src=####","####",$settings[footer]); */
/* 		$settings[footer] = str_replace("####'","####",$settings[footer]); */
/* 		$textarray1 = explode("####", $settings[footer]); */
/* 		if (count($textarray1) > 1) { */
/* 			for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 				$id = $textarray1[$i]; */
/* 				$dir = db_get_value("media","site_id","id=$id"); */
/* 				$filepath = $uploadurl."/".$dir."/".$filename; */
/* 				$textarray1[$i] = "&&&& src='".$filepath."' @@@@".$id."@@@@ &&&&"; */
/* 			}		 */
/* 			$settings[footer] = implode("",$textarray1); */
/* 		} */
	}
}

if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] - 1;
if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] + 1; 
if ($_REQUEST[step] != "") $_SESSION[settings][step] = $_REQUEST[step];


// error checking
if (($_SESSION[settings][step] != 1) && (!$_SESSION[settings][title] || $_SESSION[settings][title] == '')) {
	error("You must enter a site title.");
	$_SESSION[settings][step] = 1;
}


if ($_SESSION[settings][add]) $pagetitle="Add Site";
if ($_SESSION[settings][edit]) $pagetitle="Edit Site";

/*
// ---  variables for debugging ---
$variables = "<br><br>active = $active<br>";
$variables .= "action = $action <br> auser = $auser <br> settings = $settings";
foreach ($settings as $n => $v) {
	$variables .= "$n = $v <br>";	
}
add_link(leftnav,'','',"$variables");
//------------------------------------
*/

if (!sitenamevalid($_SESSION[siteObj]->getField("name"))) {// check if the site name is valid
	error("You are not allowed to edit this site. Nice try.");
	return;
}
if ($_REQUEST[cancel]) {
	$_REQUEST[commingFrom] = $_SESSION[settings][commingFrom];
	$_REQUEST[sitename] = $_SESSION[settings][sitename];
	unset($_SESSION["settings"]);
	unset($_SESSION["siteObj"]);
	if ($_REQUEST[commingFrom]) header("Location: index.php?$sid&action=$_REQUEST[commingFrom]&site=".$_SESSION[siteObj]->getField("name"));
	else header("Location: index.php?$sid");
}


if ($_REQUEST[save]) {
	if (!$error) { // save it to the database
		
/* 		if ($settings[activatedate]) $settings[activatedate] = $settings[activateyear] . "-" . ($settings[activatemonth]+1) . "-" . $settings[activateday]; */
/* 		else $settings[activatedate] = "0000-00-00"; */
/* 	 */
/* 		if ($settings[deactivatedate]) $settings[deactivatedate] = $settings[deactivateyear] . "-" . ($settings[deactivatemonth]+1) . "-" . $settings[deactivateday]; */
/* 		else $settings[deactivatedate] = "0000-00-00"; */
/* 		 */
/* 		if ($settings[editors]) { */
/* 			$edlist = explode(",",$settings[editors]); */
/* 			foreach ($edlist as $e) { */
/* 				for ($i=0;$i<3;$i++) { */
/* 					$settings[permissions][$e][$i] = ($settings[permissions][$e][$i])?1:0; */
/* 				} */
/* 			} */
/* 		} else $settings[permissions] = ''; */
/* 		print_r($settings[permissions]); */
/* 		$settings[permissions]=encode_array($settings[permissions]); */
/*  */
/* 		// Break up the short and long text and properly configure image urls */
/* 		$textarray1 = explode("&&&&", $settings[header]); */
/* 		if (count($textarray1) > 1) { */
/* 			for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 				$textarray2 = explode("@@@@", $textarray1[$i]); */
/* 				$id = $textarray2[1]; */
/* 				$textarray1[$i] = "src='####".$id."####'"; */
/* 			}		 */
/* 			$settings[header] = implode("",$textarray1); */
/* 		} */
/* 		 */
/* 		$textarray1 = explode("&&&&", $settings[footer]); */
/* 		if (count($textarray1) > 1) { */
/* 			for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 				$textarray2 = explode("@@@@", $textarray1[$i]); */
/* 				$id = $textarray2[1]; */
/* 				$textarray1[$i] = "src='####".$id."####'"; */
/* 			}		 */
/* 			$settings[footer] = implode("",$textarray1); */
/* 		} */
/*  */
/* 		$_SESSION[settings][header] = urlencode($_SESSION[settings][header]); */
/* 		$query .= ", header='$_SESSION[settings][header]'"; */
/*  */
/* 		$_SESSION[settings][footer] = urlencode($_SESSION[settings][footer]); */
/* 		$query .= ", footer='$_SESSION[settings][footer]'"; */
		
		//db_query($query.$where);
		print "<BR><BR>$_SESSION[settings][sitename]<BR><BR>";
		if ($_SESSION[settings][add]) $_SESSION[siteObj]->insertDB();
		if ($_SESSION[settings][edit]) $_SESSION[siteObj]->updateDB();
		
		log_entry($_REQUEST[action],$_SESSION[siteObj]->getField("name"),"","","$auser ".(($_SESSION[settings][edit])?"edited":"added")." ".$_SESSION[siteObj]->getField("name"));
		
		/* ----------------------------------------------------- */
		/*   will have to update this to use object-related site copy functions */
		
		// --- Copy the Template on add ---
		if ($_SESSION[settings][add] && $_SESSION[settings][template] != "") {
			copySite($_SESSION[settings][template],$_SESSION[settings][sitename]);
		} else if ($_SESSION[settings][add]) {
			copySite("template0",$_SESSION[settings][sitename]);
		}
	
		// --- do the copy down and recursive changes for sections & pages --- 
		print "count for copy down: " . count($_SESSION[settings][copydownpermissions]) . "<BR>";
		
		$_SESSION[settings][permissions] = decode_array($_SESSION[settings][permissions]);
		$site_owner = $auser;
		if (/*$_SESSION[settings][edit] && */($_SESSION[settings][recursiveenable] || count($_SESSION[settings][copydownpermissions]))) {
			// recursively change the $active or $permissions field for all parts of the site
			$sections = decode_array(db_get_value("sites","sections","name='$_SESSION[settings][sitename]'"));
			foreach ($sections as $sn) {
				$sna = db_get_line("sections","id=$sn");
				$chg = array();
				if ($_SESSION[settings][recursiveenable] && permission($auser,SITE,EDIT,$_SESSION[settings][sitename])) $chg[] = "active=$_SESSION[settings][active]";
				if (count($_SESSION[settings][copydownpermissions]) && $auser == $_SESSION[settings][site_owner]) {
					$snp = decode_array($sna['permissions']);
					foreach ($_SESSION[settings][copydownpermissions] as $e) $snp[$e] = $_SESSION[settings][permissions][$e];
					print_r($snp);
					$snp = encode_array($snp);
					$chg[] = "permissions='$snp'";
				}
				$query = "update sections set " . implode(",",$chg) . " where id=$sn";
				print $query . "<BR>";
				if (count($chg)) db_query($query);
				
				$pages = decode_array($sna['pages']);
				foreach ($pages as $p) {
					$pa = db_get_line("pages","id=$p");
					$chg = array();
					if ($_SESSION[settings][recursiveenable] && permission($auser,SECTION,EDIT,$sn)) $chg[] = "active=$_SESSION[settings][active]";
					if (count($_SESSION[settings][copydownpermissions]) && $auser == $_SESSION[settings][site_owner]) {
						$pp = decode_array($pa['permissions']);
						foreach ($_SESSION[settings][copydownpermissions] as $e) $pp[$e] = $_SESSION[settings][permissions][$e];
						$pp = encode_array($pp);
						$chg[] = "permissions='$pp'";
					}
					$query = "update pages set " . implode(",",$chg) . " where id=$p";
					print "--> ".$query . "<BR>";
					if (count($chg)) db_query($query);
	
					$stories = decode_array(db_get_value("pages","stories","id=$p"));
					foreach ($stories as $s) {
						$sa = db_get_line("stories","id=$s");
						$chg = array();
						if ($recursiveenable && permission($auser,PAGE,EDIT,$p)) $chg[] = "active=$_SESSION[settings][active]";
						if (count($_SESSION[settings][copydownpermissions]) && $auser == $_SESSION[settings][site_owner]) {
							$sp = decode_array($sa['permissions']);
							foreach ($_SESSION[settings][copydownpermissions] as $e) $sp[$e] = $_SESSION[settings][permissions][$e];
							$sp = encode_array($sp);
							$chg[] = "permissions='$sp'";
						}
						$query = "update stories set " . implode(",",$chg) . " where id=$s";
						print "--> ".$query . "<BR>";
						if (count($chg)) db_query($query);
					}
				}
			}
		}
		
		if ($_SESSION[settings][add]) {
			header("Location: index.php?$sid&action=viewsite&site=$_SESSION[settings][sitename]");
			unset($_SESSION["settings"]);
			unset($_SESSION["siteObj"]);
		} else {
			$commingFrom = $_SESSION[settings][commingFrom];
			$sitename = $_SESSION[settings][sitename];
			unset($_SESSION["settings"]);
			unset($_SESSION["siteObj"]);
			if ($commingFrom) header("Location: index.php?$sid&action=$commingFrom&site=$sitename");
			else header("Location: index.php?$sid");
		}
		
	} else {
		printc ("<br>There was an error");
	}
	
}

if ($_SESSION[settings][edit] && $_SESSION[settings][step] == 3 && $prevbutton) $_SESSION[settings][step] = 2;
else if ($_SESSION[settings][edit] && $_SESSION[settings][step] == 3) $_SESSION[settings][step] = 4;

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "________________<br><table>";
$leftlinks .= "<tr><td>";
if ($_SESSION[settings][step] == 1) $leftlinks .= "<span class=editnote>&rArr;</span>";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 1) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$_SESSION[settings][sitename]&step=1&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Title & Availability";
if ($_SESSION[settings][step] != 1 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 2) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 2) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$_SESSION[settings][sitename]&step=2&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Appearance";
if ($_SESSION[settings][step] != 2 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

if ($_SESSION[settings][add]) $leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 3 && $_SESSION[settings][add]) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 3) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$_SESSION[settings][sitename]&step=3&link=1 onClick=\"submitForm()\">";
if ($_SESSION[settings][add]) $leftlinks .= "Template";
if ($_SESSION[settings][step] != 3 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 4) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 4) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$_SESSION[settings][sitename]&step=4&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Editing Permissions";
if ($_SESSION[settings][step] != 4 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 5) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 5) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$_SESSION[settings][sitename]&step=5&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Custom Header";
if ($_SESSION[settings][step] != 5 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 6) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 6) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$_SESSION[settings][sitename]&step=6&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Custom Footer";
if ($_SESSION[settings][step] != 6 && $_SESSION[settings][edit]) $leftlinks .= "</a>";
$leftlinks .= "</td></tr></table>________________<br><a href=$PHP_SELF?$sid&action=add_site&cancel=1>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($_SESSION[settings][step] == 1) {
	include("add_site_form_1_title.inc");
}
if ($_SESSION[settings][step] == 2) {
	include("add_site_form_2_theme.inc");
}
if ($_SESSION[settings][step] == 3) {
	if ($_SESSION[settings][add]) include("add_site_form_3_template.inc");
}
if ($_SESSION[settings][step] == 4) {
	include("add_site_form_4_permissions.inc");
}
if ($_SESSION[settings][step] == 5) {
	include("add_site_form_5_header.inc");
}
if ($_SESSION[settings][step] == 6) {
	include("add_site_form_6_footer.inc");	
}




// ---  variables for debugging ---
$variables = "<br>";
$variables .= "action = $action <br> auser = $auser <br> settings = $settings<br>";
foreach ($settings as $n =>$v) {
	$variables .= "$n = $v <br>";
}
//printc("$variables");
//------------------------------------

