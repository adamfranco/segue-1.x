<? /* $Id$ */

header("Content-Type: text/xml");
print "<"."?xml version=\"1.0\"?".">\n";
?>
<rss version="2.0">
	<channel>
<?

//-----------------------------------
// First round of error checking
//-----------------------------------
	$error = FALSE;
	ob_start();
	
	// Do some checking to make sure that we have valid parts we are looking for.
	if (get_class($thisSite) == 'site' && slot::exists($thisSite->name))
		$site=$thisSite->name;
	else if ($thisSite) {
		unset($thisSite);
		print "The requested site does not exist. Please update your link.";
		$error = TRUE;
	}
	if (!$error) {
		if (get_class($thisSection) == 'section')
			$section=$thisSection->id;
		else if ($thisSection 
				|| ($_REQUEST['section'] 
					&& !in_array($_REQUEST['section'], $thisSite->getField('sections')))) 
		{
			unset($thisSection);
			print "The requested section does not exist. Please update your link.";
			$error = TRUE;
		}
	}
	
	if (!$error) {
		if (get_class($thisPage) == 'page')
			$page=$thisPage->id;
		else if ($thisPage
			|| ($_REQUEST['page'] 
				&& !in_array($_REQUEST['page'], $thisSection->getField('pages'))))
		{
			unset($thisPage);
			print "The requested page does not exist. Please update your link.";
			$error = TRUE;
		}
	}
	
	$errorString = ob_get_contents();
	ob_end_clean();
	// End validation

//---------------------------
// check view permissions
//---------------------------
if ($error || !$thisPage->canview()) {
	print "\t\t<title>Error</title>\n";
	ob_start();
	if ($error)
		print $errorString;
	else {
		print "You may not view this site. This may be due to any of the following reasons:<br />";
		print "<ul>";
		if ($thisSite->site_does_not_exist) {
			print "<li>This site does not exist. ";
			if (
				$_SESSION[auser] == slot::getOwner($thisSite->name)
					|| (
						$allowpersonalsites 
						&& $_SESSION[atype] != 'visitor'
						&& $thisSite->name == $_SESSION[auser]
					)
				) 
			{
				print "<br /><a href='$PHP_SELF?$sid&action=add_site&sitename=".$thisSite->name."'>Create Site</a>";
			}
			print "</li>";
		} else {
			if (!$_SESSION[auser]) {
				print "<li>You are not logged in.</li>";
				print "<li>You are not on a computer within $cfg[inst_name]</li>";
			}
			print "<li>The site has not been activated by the owner.</li>";
			print "<li>You are not part of a set of specific users or groups allowed to view this site.</li>";
		}
		print "</ul>";
	}
	
	$description = htmlentities(ob_get_contents());
	ob_end_clean();
	print "\t\t<description>";
	print $description;
	print "</description>\n";
} else {

	//---------------------------------------
	//
	// Generate the feed if we can view it.
	//
	//---------------------------------------
	
	
	// check for proper instance of scripts
	if ($allowclasssites != $allowpersonalsites) {
		$type = $thisSite->getField("type");
		if ($allowclasssites && !$allowpersonalsites) {
			if ($type == 'personal')
				header("Location: $personalsitesurl/index.php?action=rss&site=$site&section=$section&page=$page");
		} else if (!$allowclasssites && $allowpersonalsites) {
			if ($type != 'personal' && $type != 'system')
				header("Location: $classsitesurl/index.php?action=rss&site=$site&section=$section&page=$page");
		}
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
	
	
	
	if ($thisPage) {
		$thisPage->fetchDown();
		if ($thisPage->hasPermissionDown("view"))
			print "\t\t<title>".$thisPage->getField("title")."</title>\n";
			
			$pagelink = $cfg[full_uri]."/index.php?$sid&action=site";
			$pagelink .= "&site=".$thisSite->name;
			if ($thisSection) $pagelink .= "&section=".$thisSection->id;
			if ($thisPage) $pagelink .= "&page=".$thisPage->id;
			$pagelink = htmlspecialchars($pagelink);
			
			print "\t\t<link>".$pagelink."</link>\n";
			
//			<description>We have several running instances of Segue available for demostration. These instances are refreshed every two hours, so feel free to try them out without worry of breaking anything.</description>
			print "\t\t<lastBuildDate>".date("D M j, Y G:i:s T")."</lastBuildDate>\n";
			print "\t\t<generator>Segue RSS Generator</generator>\n";
			
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
// 				$o =& $thisPage->stories[$detail];
// 				include("fullstory.inc.php");
			
			//if not detail, then print all stories
			} else {
			
				foreach ($thisPage->data[stories] as $s) {
					$o =& $thisPage->stories[$s];
					
					//printc("<table><tr><td>";
					
					if ($o->canview()) {
						print "\t\t<item>\n";
						print "\t\t\t<title>".$o->getField("title")."</title>\n";
						print "\t\t\t<link>".$pagelink."</link>\n";
						print "\t\t\t<guid isPermaLink=\"true\">".$pagelink."</guid>\n";
						print "\t\t\t<pubDate>".timestamp2usdate($o->getField("addedtimestamp"))."</pubDate>\n";
						print "\t\t\t<author>".$o->getField("addedbyfull")." (".$o->getField("addedby").")</author>\n";
						
						if ($o->getField("category")) {
							print "\t\t\t<category>".$o->getField("category")."</category>\n";
						}
						
						if ($o->getField("discuss")) {
							print "\t\t\t<comments>".$pagelink.htmlspecialchars("&story=".$o->id."&detail=".$o->id)."</comments>\n";
						}
						
						$incfile = "output_modules/rss/".$o->getField("type").".inc.php";
						
						ob_start();
						include($incfile);
						$description = ob_get_contents();
						ob_end_clean();
						$description = str_replace("\n", "", $description);
						$description = str_replace("\r", "", $description);
						
						print "<description>".htmlentities($description)."</description>\n";
						
						print "\t\t</item>\n";
					}
					$i++;

				} //end foreach stories
			} //end detail conditional
		}
	}
}

?>	</channel>
</rss><?
exit;
?>