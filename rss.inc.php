<? /* $Id$ */

header("Content-Type: text/xml; charset=utf-8");
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
	
if ($error) {
	print "\t\t<title>Error</title>\n";
	ob_start();
	print $errorString;
	$description = htmlspecialchars(ob_get_contents());
	ob_end_clean();
	print "\t\t<description>";
	print $description;
	print "</description>\n";

} else {
	
	
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
	
	if ($thisSite) {
		// If no section is specified, select the first one that we can view.
		if (!$thisSection && count($thisSite->getField("sections"))) {
			$thisSite->fetchDown();
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
	}

	//---------------------------
	// check view permissions
	//---------------------------
	if (!$thisPage || !$thisPage->canview()) {
		print "\t\t<title>Error</title>\n";
		ob_start();
		if ($error)
			print $errorString;
		else {
			print "You may not view this RSS Feed. This may be due to any of the following reasons:<br />";
			print "<ul>";
			if ($thisSite->site_does_not_exist) {
				print "<li>This feed does not exist. ";
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
			} else if (!$thisPage) {
				print "<li>Requested page object doesn't exist.</li>";
				
			} else {
				if (!$_SESSION[auser]) {
					print "<li>You are not logged in.</li>";
					print "<li>You are not on a computer within $cfg[inst_name]</li>";
				}
				print "<li>The feed has not been activated by the owner.</li>";
				print "<li>You are not part of a set of specific users or groups allowed to view this feed.</li>";
			}
			print "</ul>";
		}
		
		$description = htmlspecialchars(ob_get_contents());
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
	
	
		$thisPage->fetchDown();
		if ($thisPage->hasPermissionDown("view"))
			print "\t\t<title>".$thisPage->getField("title")."</title>\n";
			
			$pagelink = $cfg[full_uri]."/index.php?$sid&action=site";
			$pagelink .= "&site=".$thisSite->name;
			if ($thisSection) $pagelink .= "&section=".$thisSection->id;
			if ($thisPage) $pagelink .= "&page=".$thisPage->id;
			$pagelink = htmlspecialchars($pagelink);
			
			print "\t\t<link>".$pagelink."</link>\n";
			
			print "\t\t<description>";
			// 
			print "</description>\n";
			
			print "\t\t<lastBuildDate>".date("D M j, Y G:i:s T")."</lastBuildDate>\n";
			print "\t\t<generator>Segue RSS Generator</generator>\n";
			
		$i=0;
		// handle archiving -- monthly, weekly, etc
		$thisPage->handleStoryArchive();
	
		// handle ordering of stories
		$thisPage->handleStoryOrder();

/******************************************************************************
 * If page has stories then print them
 ******************************************************************************/
		
		if ($thisPage->stories) {
			foreach ($thisPage->data[stories] as $s) {
				$o =& $thisPage->stories[$s];
				
				//printc("<table><tr><td>";
				
				if ($o->canview()) {
					print "\t\t<item>\n";
					print "\t\t\t<title>".$o->getField("title")."</title>\n";
					print "\t\t\t<link>".$pagelink."</link>\n";
					print "\t\t\t<guid isPermaLink=\"true\">".$pagelink."</guid>\n";
					
					print "\t\t\t<pubDate>";
					print timestamp2usdate($o->getField("addedtimestamp"));
					print "</pubDate>\n";
					
					print "\t\t\t<author>";
					print $o->getField("addedbyfull");
					print " (".$o->getField("addedby").")";
					print "</author>\n";
					
					if ($o->getField("category")) {
						print "\t\t\t<category>";
						print $o->getField("category");
						print "</category>\n";
					}
					
					if ($o->getField("discuss")) {
						print "\t\t\t<comments>";
						print $pagelink.htmlspecialchars("&story=".$o->id."&detail=".$o->id);
						print "</comments>\n";
					}
					
					$incfile = "output_modules/rss/".$o->getField("type").".inc.php";
					
					ob_start();
					include($incfile);
					$description = ob_get_contents();
					ob_end_clean();
					$description = str_replace("\n", "", $description);
					$description = str_replace("\r", "", $description);
					
					print "<description>";
					print htmlspecialchars($description, ENT_COMPAT, 'utf-8');
					print "</description>\n";
					
					print "\t\t</item>\n";
				}
				$i++;

			} //end foreach stories
		}
	}
}

?>	</channel>
</rss><?
exit;
?>