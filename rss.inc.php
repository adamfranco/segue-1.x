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
				if ($o->getField("type") == 'page' && $o->canview()) { 
					$thisPage =& $thisSection->pages[$p]; 
					break; 
				}
			}
		}
	}

	//---------------------------
	// check view permissions
	//---------------------------
	if (!$thisPage || !$thisPage->canview("everyone")) {
		print "\t\t<title>Error</title>\n";
		ob_start();
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
			print "<li>The feed has not been made public by the owner.</li>";
		}
		print "</ul>";
		
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
			
			print "\t\t<lastBuildDate>".date("D, d M Y G:i:s T")."</lastBuildDate>\n";
			print "\t\t<generator>Segue RSS Generator</generator>\n";
			
		// handle archiving -- monthly, weekly, etc
		$thisPage->handleStoryArchive();
	
		// handle ordering of stories
		$thisPage->handleStoryOrder();

/******************************************************************************
 * If page has stories then print them
 ******************************************************************************/
		
		if ($thisPage->stories) {
			$i=0;
			foreach ($thisPage->data[stories] as $s) {
				$i++;
				
				if ($i > 10)
					break;
					
				$o =& $thisPage->stories[$s];
				
				//printc("<table><tr><td>";
				
				if ($o->canview()) {
					print "\t\t<item>\n";
					print "\t\t\t<title>".$o->getField("title")."</title>\n";
					$storylink = "#".$o->getField("id");
					print "\t\t\t<link>".$pagelink.$storylink."</link>\n";
					print "\t\t\t<guid isPermaLink=\"true\">".$pagelink.$storylink."</guid>\n";
					
					print "\t\t\t<pubDate>";
					print date("D, d M Y G:i:s T", strtotime(timestamp2usdate($o->getField("addedtimestamp"))));
					print "</pubDate>\n";
					
					print "\t\t\t<author>";
					print $o->getField("addedbyfull");
					$user_uname = $o->getField("addedby");
					$user_email = db_get_value("user","user_email","user_uname='$user_uname'");
					print " (".$user_email.")";
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
					if ($o->getField("type") == "file") {
						$b = db_get_line("media INNER JOIN slot ON media.FK_site=slot.FK_site","media_id=".$o->getField("longertext"));
						$filename = $b[media_tag];
						$filename = rawurlencode($filename);
						if (ereg(".mp3", $filename)) {
							$type = "audio/mpeg";
						} else {
							$type = "unknown";
						}
						/* 	print $filename; */
						$dir = $b[slot_name];
						$size = $b[media_size];
						$fileurl = "$uploadurl/$dir/$filename";
						$filepath = "$uploaddir/$dir/$filename";
						$filesize = $size;
						print "<enclosure url='$fileurl' length='$filesize' type='$type' />";					
					}

					
					print "\t\t</item>\n";
				}
			} //end foreach stories
		}
	}
}

?>	</channel>
</rss><?
exit;
?>