<? /* $Id$ */

if (isset($_SESSION[settings]) && isset($_SESSION[siteObj])) {
	// if we have already started editing...

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($settings[step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
/* 	if ($_REQUEST[sitename] != ""  && $_SESSION[ltype] == "admin") { $_SESSION[siteObj]->setSiteName($_REQUEST[sitename]); $_SESSION[settings][sitename] = $_REQUEST[sitename]; } */
/* 	if ($_REQUEST[type] && $_SESSION[ltype]=='admin') $_SESSION[siteObj]->setField("type",$_REQUEST[type]); */
	if ($_SESSION[settings][step] == 1 && $_REQUEST[title] != "") $_SESSION[siteObj]->setField("title",$_REQUEST[title]);
	// handle de/activate dates
	$_SESSION[siteObj]->handleFormDates();
	if ($_REQUEST[active] != "") $_SESSION[siteObj]->setField("active",$_REQUEST[active]);
	if ($_REQUEST[viewpermissions] == "everyone") {
		$_SESSION[siteObj]->setUserPermissionDown("view","everyone","1");
		$_SESSION[siteObj]->addEditor("institute");
//		$_SESSION[siteObj]->updatePermissionsDB();
		$_SESSION[settings][viewpermissions] = "";
	}
	if ($_REQUEST[viewpermissions] == "institute") {
		$_SESSION[siteObj]->setUserPermissionDown("view","everyone","0");
		$_SESSION[siteObj]->setUserPermissionDown("view","institute","1");
//		$_SESSION[siteObj]->updatePermissionsDB();
		$_SESSION[settings][viewpermissions] = "";
	}
	if ($_REQUEST[viewpermissions] == "class") {
		if (isgroup($_SESSION[siteObj]->getField("name"))) {
//			print "<br>".$_SESSION[siteObj]->getField("name")."is a classgroup";
			$classes = group::getClassesFromName($_SESSION[siteObj]->getField("name"));
//			print "<br>Classes contained:<pre>"; print_r($classes); print "</pre>";
			foreach ($classes as $class) {
				if (!$_SESSION[siteObj]->isEditor($class)) {
					$_SESSION[siteObj]->addEditor($class);
//					print "<br>Adding $class as editor";
				}
				$_SESSION[siteObj]->setUserPermissionDown("view",$class,"1");
//				print "<br>Setting 1 view permission for $class";
			}		
		} else {
			if (!$_SESSION[siteObj]->isEditor($_SESSION[siteObj]->getField("name"))) {
				$_SESSION[siteObj]->addEditor($_SESSION[siteObj]->getField("name"));
			}
			$_SESSION[siteObj]->setUserPermissionDown("view",$_SESSION[siteObj]->getField("name"),"1");
		}
		$_SESSION[siteObj]->setUserPermissionDown("view","everyone","0");
		$_SESSION[siteObj]->setUserPermissionDown("view","institute","0");
//		$_SESSION[siteObj]->updatePermissionsDB();
		$_SESSION[settings][viewpermissions] = "";
	}
	if ($_REQUEST[viewpermissions] == "custom") {
		$_SESSION[settings][viewpermissions] = "custom";
	}
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[siteObj]->setField("listed",$_REQUEST[listed]);
	if ($_REQUEST[theme] != "") $_SESSION[siteObj]->setField("theme",$_REQUEST[theme]);
	if ($_REQUEST[theme] != "") $_SESSION[siteObj]->setField("themesettings",$_REQUEST[themesettings]);
	if ($_SESSION[settings][step] == 3) $_SESSION[settings][template] = $_REQUEST[template];
//	if ($settings[step] == 4 && !$_REQUEST[link]) $_SESSION[settings][editors] = strtolower($_REQUEST[editors]);
//	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[siteObj]->setPermissions($_REQUEST[permissions]);
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[settings][recursiveenable] = $_REQUEST[recursiveenable];
//	if ($_REQUEST[copydownpermissions] != "") $_SESSION[settings][copydownpermissions] = $_REQUEST[copydownpermissions];
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[siteObj]->setField("header",$_REQUEST[header]);
	if ($_SESSION[settings][step] == 5 && !$_REQUEST[link]) $_SESSION[siteObj]->setField("footer",$_REQUEST[footer]);
	if ($_REQUEST[copyfooter]) $_SESSION[siteObj]->setField("header",$_SESSION[siteObj]->getField("footer"));
	if ($_REQUEST[copyheader]) $_SESSION[siteObj]->setField("footer",$_SESSION[siteObj]->getField("header"));	
	
}

if (!isset($_SESSION["settings"]) || !isset($_SESSION["siteObj"])) {
	// create the settings array with default values. $settings must be passed along with each link.
	// The array will be saved on clicking a save button.
	$_SESSION[settings] = array(
		"sitename" => $_REQUEST[sitename],
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"recursiveenable" => "",
		"copydownpermissions" => "",
		"template" => "template0",
		"comingFrom" => $_REQUEST[comingFrom]
	);
	$_SESSION[siteObj] =& new site($_REQUEST[sitename]);
	
	if (slot::exists($_REQUEST[sitename])) {
		$slotObj = new slot ($_REQUEST[sitename]);
		$_SESSION[siteObj]->setField("type",$slotObj->getField("type"));
	} else {
		if (isclass($_REQUEST[sitename])) $_SESSION[siteObj]->setField("type","class");
		else if (!$_SESSION[siteObj]->getField("type") || $_SESSION[siteObj]->getField("type") == "") $_SESSION[siteObj]->setField("type","personal");
	}
	
	if ($_REQUEST[action] == 'add_site') {
		$_SESSION[settings][add]=1;
		$_SESSION[settings][edit]=0;
	}	
	if ($_REQUEST[action] == 'edit_site') { 
		$_SESSION[settings][add]=0;
		$_SESSION[settings][edit]=1;		
	}
	
	if ($_SESSION[settings][add]) {
		$_SESSION[siteObj]->addEditor("everyone");	//We aren't storing the permissions from form 1.
		$_SESSION[siteObj]->addEditor("institute");
		$_SESSION[siteObj]->setUserPermissionDown("view","everyone","1");
	}
	
	if ($_SESSION[settings][edit]) {
		if (!$_SESSION[settings][sitename]) {
			$_SESSION[settings][sitename] = $_REQUEST[edit_site];
			$_SESSION[siteObj]->setSiteName($_REQUEST[edit_site]);
		}
		$_SESSION[siteObj]->fetchDown(1);
		$_SESSION[siteObj]->buildPermissionsArray(0,1);
/* ---------------------------------------------------- 
	 uncomment this line when permissions are set and done */
		$_SESSION[settings][copydownpermissions] = decode_array($_SESSION[settings][copydownpermissions]);
		$_SESSION[settings][site_owner] = $site_owner;	
	}
	
	$_SESSION[siteObj]->initFormDates();
	$dontCheckError = 1;
}

/* ---------------------------------------------------------------------------------------------*/
/*						ERROR CHECKING															*/
if ($_SESSION[settings][step] == 1 && !$dontCheckError) {
	if ((!$_SESSION[siteObj]->getField("title") || $_SESSION[siteObj]->getField("title") == ''))
		error("You must enter a site title.");
	if ($_SESSION[siteObj]->getField("name") == "")
		error("You must enter a name for this site. Sites without names will be broken.");
	if (!ereg("^([0-9A-Za-z_-]*)$",$_SESSION[siteObj]->getField("name")))
		error("The site name you entered is invalid. It may only contain alphanumeric characters, '_' and '-'.");
/* 	if ($error) $_SESSION[settings][step] = 1; */
}

if (!$error) {
	if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] - 1;
	if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] + 1; 
}
if ($_REQUEST[step] != "" && $_REQUEST[step]) $_SESSION[settings][step] = $_REQUEST[step];


if ($_SESSION[settings][add]) $pagetitle="Add Site";
if ($_SESSION[settings][edit]) $pagetitle="Edit Site";

if (!sitenamevalid($_SESSION[siteObj]->getField("name"))) {// check if the site name is valid
	error("You are not allowed to edit this site.");
	return;
}

if ($_REQUEST[cancel]) {
	$comingFrom = $_SESSION[settings][comingFrom];
	$site = $_SESSION[siteObj]->getField("name");
//	if (ini_get("register_globals")) { session_unregister("settings"); session_unregister("siteObj"); }
//	unset($_SESSION[siteObj],$_SESSION[setting]);
	if ($comingFrom) header("Location: index.php?$sid&action=$comingFrom&site=$site");
	else header("Location: index.php?$sid");
}


/******************************************************************************
 * Save
 ******************************************************************************/
if ($_REQUEST[save]) {
	if (!$error) { // save it to the database
		
		print "<BR><BR>".$_SESSION[settings][sitename]."<BR><BR>";
		
		/******************************************************************************
		 * replace media library urls with $mediapath/$sitename/filename
		 * replace specific url with general url
		 ******************************************************************************/
		 $mod_header = $_SESSION[siteObj]->getField("header",$_REQUEST[header]);
		 $mod_footer = $_SESSION[siteObj]->getField("footer",$_REQUEST[footer]);
		 $specfic_mediapath = $cfg[uploadurl]."/".$_SESSION[settings][sitename];
		 $general_mediapath = "\[\[mediapath\]\]";
		 $mod_header = eregi_replace($specfic_mediapath, $general_mediapath, $mod_header);
		 $mod_footer = eregi_replace($specfic_mediapath, $general_mediapath, $mod_footer);
		 
		 // Also, if we are using a plain text-field convert any linereturns to <br /> tags
		 // Make sure that we have the content formatted correctly.
		include ("sniffer.inc.php");
		// If we just have a text box, replace new lines with <br> tags
		if (!$supported) {
			$mod_header = htmlbr($mod_header);
			$mod_footer = htmlbr($mod_footer);
		}
		 
		 $_SESSION[siteObj]->setField("header",$mod_header);
		 $_SESSION[siteObj]->setField("footer",$mod_footer);

		if ($_SESSION[settings][add]) {
			$_SESSION[siteObj]->insertDB();
			log_entry("add_site","$_SESSION[auser] added ".$_SESSION[siteObj]->name,$_SESSION[siteObj]->name,$_SESSION[siteObj]->id,"site");
		}
		if ($_SESSION[settings][edit]) {
			$_SESSION[siteObj]->updateDB(1);
			log_entry("edit_site","$_SESSION[auser] edited ".$_SESSION[siteObj]->name,$_SESSION[siteObj]->name,$_SESSION[siteObj]->id,"site");
		}
		
		/* ----------------------------------------------------- */
		/*   will have to update this to use object-related site copy functions */
		// --- Copy the Template on add ---
		if ($_SESSION[settings][add] && $_SESSION[settings][template] != "") {
/* 			copySite($_SESSION[settings][template],$_SESSION[siteObj]->getField("name")); */
			$_SESSION[siteObj]->applyTemplate($_SESSION[settings][template]);
		} else if ($_SESSION[settings][add]) {
/* 			copySite("template0",$_SESSION[siteObj]->getField("name")); */
			$_SESSION[siteObj]->applyTemplate("template0");
		}
	
		// do recursive enable
		if ($_SESSION[settings][recursiveenable]) {
			$val = $_SESSION[siteObj]->getField("active");
			$_SESSION[siteObj]->setFieldDown("active",$val);
			$_SESSION[siteObj]->updateDB(1);
			// done
		}
				
		$sitename = $_SESSION[siteObj]->getField("name");
		$comingFrom = $_SESSION[settings][comingFrom];
		$add = $_SESSION[settings][add];

		if ($add) {
			header("Location: index.php?$sid&action=viewsite&site=$sitename");
		} else {
			if ($comingFrom) {
				header("Location: index.php?$sid&action=$comingFrom&site=$sitename");
			} else {
				header("Location: index.php?$sid");
			}
		}
		
	} else {
		printc ("<br>There was an error");
	}
	
}

/******************************************************************************
 * Form Stuff
 ******************************************************************************/
if ($_SESSION[settings][edit] && $_SESSION[settings][step] == 3 && $prevbutton) $_SESSION[settings][step] = 2;
else if ($_SESSION[settings][edit] && $_SESSION[settings][step] == 3) $_SESSION[settings][step] = 4;

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "________________<br><table>";
$leftlinks .= "<tr><td>";
if ($_SESSION[settings][step] == 1) $leftlinks .= "<span class=editnote>&rArr;</span>";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 1) $leftlinks .= "<a href='#' onClick=\"submitFormLink(1)\">";
$leftlinks .= "Title & Availability";
if ($_SESSION[settings][step] != 1 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 2) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 2) $leftlinks .= "<a href='#' onClick=\"submitFormLink(2)\">";
$leftlinks .= "Appearance";
if ($_SESSION[settings][step] != 2 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

if ($_SESSION[settings][add]) $leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 3 && $_SESSION[settings][add]) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 3) $leftlinks .= "<a href='#' onClick=\"submitFormLink(3)\">";
if ($_SESSION[settings][add]) $leftlinks .= "Template";
if ($_SESSION[settings][step] != 3 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 4) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 4) $leftlinks .= "<a href='#' onClick=\"submitFormLink(4)\">";
$leftlinks .= "Custom Header";
if ($_SESSION[settings][step] != 4 && $_SESSION[settings][edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($_SESSION[settings][step] == 5) $leftlinks .= "<span class=editnote>&rArr;</span> ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][edit] && $_SESSION[settings][step] != 5) $leftlinks .= "<a href='#' onClick=\"submitFormLink(5)\">";
$leftlinks .= "Custom Footer";
if ($_SESSION[settings][step] != 5 && $_SESSION[settings][edit]) $leftlinks .= "</a>";
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
	include("add_site_form_5_header.inc");
}
if ($_SESSION[settings][step] == 5) {
	include("add_site_form_6_footer.inc");	
}