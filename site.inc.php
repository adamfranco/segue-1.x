<? /* $Id$ */

/* print "canview: ".$thisSite->canview()."br>"; */
/* print "hasperm: ".$thisSite->hasPermissionDown("add or edit or delete","",0,1)."<br>"; */
/* print "<pre>"; print_r($thisSite); print "</pre>"; */

// check view permissions
if (!$thisSite->canview() && !$thisSite->hasPermissionDown("add or edit or delete")) {
	error("You may not view this site. This may be due to any of the following reasons:<BR><ul><li>The site has not been activated by the owner.<li>You are not on a computer within $cfg[inst_name].<li>You are not logged in.<li>You are not part of a set of specific users or groups allowed to view this site.</ul>");
}	

if ($thisSite) $site=$thisSite->name;
if ($thisSection) $section=$thisSection->id;
if ($thisPage) $page = $thisPage->id;

do {
	// for publication sites
	if ($supplement = $_REQUEST[supplement]) {
		if ($thisSite->getField("type")=='publication' && ($supplement == 'listarticles' || $supplement == 'listissues')) {
			include("$supplement.inc.php");
			break;
		}
	}
	
	
	// check for proper instance of scripts
	if ($allowclasssites != $allowpersonalsites) {
		$type = $thisSite->getField("type");
		if ($allowclasssites && !$allowpersonalsites) {
			if ($type == 'personal')
				header("Location: $personalsitesurl/index.php?action=site&site=$site&section=$section&page=$page");
		} else if (!$allowclasssites && $allowpersonalsites) {
			if ($type != 'personal' && $type != 'system')
				header("Location: $classsitesurl/index.php?action=site&site=$site&section=$section&page=$page");
		}
	}
	
	// if we're an admin, override all errors
	if ($_SESSION[ltype] == 'admin') {
		clearerror();
	}
	
	// if we produced an error, return (don't let them view the site)
	if ($error) return;
	
	if ($thisSite) {
		// If no section is specified, select the first one that we can view.
		if (!$thisSection && count($thisSite->getField("sections"))) {
			$thisSite->fetchDown();
//			$thisSite->buildPermissionsArray();
			foreach ($thisSite->sections as $s=>$o) {
//				$o->buildPermissionsArray();
//				print_r($o);
				print "<br>hasPermission: ".$o->hasPermissionDown("add or edit or delete");
				print "<br>Canview: ".$o->canview();
//				print_r($o->permissions);
				if ($o->getField("type") == 'section' && ($o->canview() || $o->hasPermissionDown("add or edit or delete"))) { 
					$thisSection = &$thisSite->sections[$s]; 
					break; 
				}
			}
		}
		$sitetype = $thisSite->getField("type");
	}
	if ($thisSection) {
		// If no page is specified, select the first one that we can view.
		if (!$thisPage && count($thisSection->getField("pages"))) {
			$thisSection->fetchDown();
			foreach ($thisSection->pages as $p=>$o) {
				if ($o->getField("type") == 'page' && ($o->canview() || $o->hasPermissionDown("add or edit or delete"))) { $thisPage = &$thisSection->pages[$p]; break; }
			}
		}
		$st = " > " . $thisSection->getField("title");
	}
	if ($thisPage) {		// we're viewing a page
		$pt = " > " . $thisPage->getField("title");
		// check page permissions
	}
	$pagetitle = $thisSite->getField("title") . $st . $pt;
	
	
	
	$envvars = "site=".$thisSite->name;
	if ($thisSection) $envvars .= "&section=".$thisSection->id;
	if ($thisPage) $envvars .= "&page=".$thisPage->id;
	
	$site=$thisSite->name;
	$section=$thisSection->id;
	$page=$thisPage->id;
	$thisSite->fetchDown();			// just in case we haven't already
	
	// build the navbars
	include("output_modules/".$thisSite->getField("type")."/navbars.inc.php");
	
	if ($thisPage) {
		$thisPage->fetchDown();
		printc("<div class=title>".$thisPage->getField("title")."</div>");
		$i=0;
		// handle archiving -- monthly, weekly, etc
		$thisPage->handleStoryArchive();
	
		// handle ordering of stories
		$thisPage->handleStoryOrder();
	/* 	if ($pageinfo[storyorder] != 'custom' && $pageinfo[storyorder] != '') */
	/* 		$stories = handlestoryorder($stories,$pageinfo[storyorder]); */
		
		if ($thisPage->stories) {
			foreach ($thisPage->stories as $s=>$o) {
			
				if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {		
					if ((/* $thisPage->getField("showcreator") || $thisPage->getField("showdate") ||  */$thisPage->getField("showhr")) && $i!=0) 
						printc("<hr size='1' noshade style='margin-top: 5px'>");
					if ($o->getField("category")) {
						printc("<div class=contentinfo id=contentinfo2 align=right>");
						printc("Category: <b>".spchars($o->getField("category"))."</b>");
						printc("</div>");
					}
							
					printc("<div style='margin-bottom: 10px'>");
					
					$incfile = "output_modules/".$thisSite->getField("type")."/".$o->getField("type").".inc.php";
					//print $incfile; // debug
					include($incfile);
		
					if ($thisPage->getField("showcreator") || $thisPage->getField("showdate")) {
						printc("<div class=contentinfo align=right>");
						$added = timestamp2usdate($o->getField("addedtimestamp"));
						printc("added");
						if ($thisPage->getField("showcreator")) printc(" by ".$o->getField("addedby"));
						if ($thisPage->getField("showdate")) printc(" on $added");
						if ($o->getField("editedby")) {
							printc(", edited");
							if ($thisPage->getField("showcreator")) printc(" by ".$o->getField("editedby"));
							if ($thisPage->getField("showdate")) printc(" on ".timestamp2usdate($o->getField("editedtimestamp")));
						}
						printc("</div>");
						//printc("<hr size='1' noshade><br>");
					}
		
					printc("</div>");
				}
				$i++;
			}
		}
	}
} while(0);

// add the key to the footer of the page
if ($thisSite->isEditor() && !$_REQUEST[themepreview]) {
	/*$u = "$_SERVER[SCRIPT_URI]?action=viewsite&site=$site";*/
	$u = "$PHP_SELF?$sid&action=viewsite&site=$site".(($supplement)?"&supplement=$supplement":"");
	if ($section) $u .= "&section=$section";
	if ($page) $u .= "&page=$page";
	$text .= "<br> <div align=right><input type=submit class='button' value='edit this site' onClick=\"window.location='$u&$sid'\"></div>";
	$sitefooter = $sitefooter . $text;
}