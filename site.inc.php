<? /* $Id$ */

// check view permissions
if (!$thisSite->canview()) {
	ob_start();
	print "You may not view this site. This may be due to any of the following reasons:<BR>";
	print "\n<ul>";
	if ($thisSite->site_does_not_exist) {
		print "\n		<li>This site does not exist. ";
		if (
			$_SESSION[auser] == slot::getOwner($thisSite->name)
				|| (
					$allowpersonalsites 
					&& $_SESSION[atype] != 'visitor'
					&& $thisSite->name == $_SESSION[auser]
				)
			) 
		{
			print "\n<br /><a href='$PHP_SELF?$sid&action=add_site&sitename=".$thisSite->name."'>Create Site</a>";
		}
		print "</li>";
	} else {
		if (!$_SESSION[auser]) {
			print "\n		<li>You are not logged in.</li>";
			print "\n		<li>You are not on a computer within $cfg[inst_name]</li>";
		}
		print "\n		<li>The site has not been activated by the owner.</li>";
		print "\n		<li>You are not part of a set of specific users or groups allowed to view this site.</li>";
	}
	print "\n</ul>";
	error(ob_get_contents());
	ob_end_clean();
	printc("<a href='index.php?$sid'>Home</a>");
	return;
}	

// Do some checking to make sure that we have valid parts we are looking for.
if (get_class($thisSite) == 'site')
	$site=$thisSite->name;
else if ($thisSite) {
	unset($thisSite);
	error("The requested site does not exist. Please update your link.");
	return;
}
if (get_class($thisSection) == 'section')
	$section=$thisSection->id;
else if ($thisSection 
		|| ($_REQUEST['section'] 
			&& !in_array($_REQUEST['section'], $thisSite->getField('sections')))) 
{
	unset($thisSection);
	error("The requested section does not exist. Please update your link.");
	return;
}

if (get_class($thisPage) == 'page')
	$page=$thisPage->id;
else if ($thisPage
	|| ($_REQUEST['page'] 
		&& !in_array($_REQUEST['page'], $thisSection->getField('pages'))))
{
	unset($thisPage);
	error("The requested page does not exist. Please update your link.");
	return;
}
// End validation


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
	if ($_SESSION[ltype] == 'admin' && $_SESSION[luser]==$_SESSION[auser]) {
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
				if ($o->getField("type") == 'section' && $o->canview()) { 
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
				if ($o->getField("type") == 'page' && $o->canview()) { $thisPage = &$thisSection->pages[$p]; break; }
			}
		}
		$st = " > " . $thisSection->getField("title");
	}
	if ($thisPage) {		// we're viewing a page
		$pt = " > " . $thisPage->getField("title");
		// check page permissions
	}
	$pagetitle = $previewTitle.$thisSite->getField("title") . $st . $pt;
	
	
	
	$envvars = "site=".$thisSite->name;
	if ($thisSection) $envvars .= "&section=".$thisSection->id;
	if ($thisPage) $envvars .= "&page=".$thisPage->id;
	
	$site=$thisSite->name;
	$section=$thisSection->id;
	$page=$thisPage->id;
	$thisSite->fetchDown();			// just in case we haven't already
	
	$topsections = !ereg("Side\+Sections",$thisSite->getField("themesettings"));
	
	// build the navbars
	include("output_modules/".$thisSite->getField("type")."/navbars.inc.php");
	
	if ($thisPage) {
		$thisPage->fetchDown();
		if ($thisPage->hasPermissionDown("view"))
			printc("<div class='title'>".$thisPage->getField("title")."</div>");
			
		$i=0;
		// handle archiving -- monthly, weekly, etc
		$thisPage->handleStoryArchive();
	
		// handle ordering of stories
		$thisPage->handleStoryOrder();
	/* 	if ($pageinfo[storyorder] != 'custom' && $pageinfo[storyorder] != '') */
	/* 		$stories = handlestoryorder($stories,$pageinfo[storyorder]); */

/******************************************************************************
 * If page has stories then print them
 ******************************************************************************/
		
		if ($thisPage->stories) {
			//if detail then print only story detail ie full text/discussion
			if ($detail) {
				$o =& $thisPage->stories[$detail];
				include("fullstory.inc.php");
			
			//if not detail, then print all stories
			} else {
			
				foreach ($thisPage->data[stories] as $s) {
					$o =& $thisPage->stories[$s];
					
					//printc("<table><tr><td>";
					
					if ($o->canview()) {
						if ((/* $thisPage->getField("showcreator") || $thisPage->getField("showdate") ||  */$thisPage->getField("showhr")) && $i!=0) {
							printc("<hr size='1' noshade style='margin-top: 5px'>");
							//printc($detail);
						}
							

						if ($o->getField("category")) {
							printc("<div class=contentinfo id=contentinfo2 align=right>");
							printc("Category: <b>".spchars($o->getField("category"))."</b>");
							printc("</div>");
						}
								
						printc("<div style='margin-bottom: 10px'>");
						
						$incfile = "output_modules/".$thisSite->getField("type")."/".$o->getField("type").".inc.php";
	//					print $incfile; // debug
						include($incfile);
						
						
						if ($thisPage->getField("showcreator") || $thisPage->getField("showdate")) {
							printc("<div class=contentinfo align=right>");
							$added = timestamp2usdate($o->getField("addedtimestamp"));
							printc("added");
							if ($thisPage->getField("addedby")) printc(" by ".$o->getField("addedbyfull"));
							if ($thisPage->getField("showdate")) printc(" on $added");
							if ($o->getField("editedby") && !($o->getField("editedby") == $o->getField("addedby") && $o->getField("addedtimestamp") == $o->getField("editedtimestamp"))) {
								printc(", edited");
								if ($thisPage->getField("showcreator")) printc(" by ".$o->getField("editedbyfull"));
								if ($thisPage->getField("showdate")) printc(" on ".timestamp2usdate($o->getField("editedtimestamp")));
							}
							
							printc("</div>");
							printc("</div>");
							//printc("<hr size='1' noshade><br>");
						}
						
					}
					$i++;
					//printc("</div>");
					
				//printc("</td></tr></table>");
				} //end foreach stories
			} //end detail conditional
		}
	}
} while(0);


// add the key to the footer of the page
if ($thisSite->isEditor()
	&& $thisSite->hasPermissionDown("add || edit || delete")
	&& !$_REQUEST[themepreview]) 
{
	$text .= "\n<br> \n\n<div align=right>";
	if (ereg('preview_as', $_REQUEST['action'])) {
		$editAction = ereg_replace('preview_as', '&action=preview_edit_as', $_REQUEST['action']);
	 } else {
		$editAction = '&action=viewsite';
	}

	if ($_SESSION[auser] == $thisSite->owningSiteObj->owner) {
		$text .= "\n\t<input type=button style='width: $btnw' class='button' name='preview_as' value=' &nbsp; Preview Site As... &nbsp;' onClick='sendWindow(\"preview_as\",400,300,\"preview.php?$sid&site=$site&query=".urlencode($_SERVER[QUERY_STRING])."\")' target='preview_as' style='text-decoration: none'>";
	}

	$u = "$PHP_SELF?$sid".$editAction."&site=$site".(($supplement)?"&supplement=$supplement":"");
	if ($section) $u .= "&section=$section";
	if ($page) $u .= "&page=$page";
	$text .= "\n<input type=submit class='button' value='Edit This Site' onClick=\"window.location='$u&$sid'\">\n</div>";
} else {
	$text = "";
}
$text .= "\n<br>\n<div align=right>\n<div style='font-size: 0px;'>powered by segue</div>\n<a href='http://segue.sourceforge.net' target='_blank'>\n<img border=0 src=$cfg[themesdir]/common/images/segue_logo_trans_solid.gif>\n</a>\n</div>";
$sitefooter = $sitefooter . $text;
