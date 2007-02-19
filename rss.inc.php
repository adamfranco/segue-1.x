<? /* $Id$ */

header("Content-Type: text/xml; charset=utf-8");
print "<"."?xml version=\"1.0\" encoding=\"utf-8\"  ?".">\n";
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
	$description = htmlspecialchars(ob_get_contents(), ENT_QUOTES);
	ob_end_clean();
	print "\t\t<description>";
	print $description;
	print "</description>\n";

/******************************************************************************
 * if scope then either all content blocks or all discussion posts
 ******************************************************************************/

} else if (isset($_REQUEST['scope'])) {

	$link = $cfg[full_uri];			
	$link .= "/index.php?&action=site";
	$link .= "&site=".$thisSite->name;
	$link = htmlspecialchars($link, ENT_QUOTES);
	if ($_REQUEST['scope'] == "allcontent") {
		print "\t\t<title>".htmlspecialchars($thisSite->title, ENT_QUOTE)." &gt; All Posts</title>\n";	
	} else {
		print "\t\t<title>".htmlspecialchars($thisSite->title, ENT_QUOTES)." &gt; All Discussion</title>\n";
	}
	print "\t\t<link>".$link."</link>\n";		
	print "\t\t<description>";
	// 
	print "</description>\n";		
	print "\t\t<lastBuildDate>".date("D, j M Y h:i:s O")."</lastBuildDate>\n";
	print "\t\t<generator>Segue RSS Generator</generator>\n";
			
	if ($_REQUEST['scope'] == "allcontent") {
		$recent_site_edits = recent_site_edits($thisSite->name);
		
		while ($a = db_fetch_assoc($recent_site_edits)) {
		
			$thisSection =& new section($_REQUEST[site],$a['section_id'], $thisSite);
			$thisPage =& new page($_REQUEST[site],$a['section_id'],$a['page_id'], $thisSection);
			
			if ($thisPage->canview("everyone") && $a['story_display_type'] != "rss") {
				print "\t\t<item>\n";
			//	$title = $a["story_title"];
			
				if ($a["story_title"]) {
					$title = $a["story_title"];
				} else {
					$title2 = strip_tags(urldecode($a["story_text_short"]));
					if (strlen($title2) > 25) {
						$title = substr($title2, 0, 25)."...";
					}					
				}
				
				print "\t\t\t<title>".htmlspecialchars(urldecode($title), ENT_QUOTES, 'utf-8')."</title>\n";
				
				$storylink = "&story=".$a["story_id"]."&detail=".$a["story_id"]."#".$a["discussion_id"];
				$pagelink = "&page=".$a["page_id"];
				$sectionlink = "&section=".$a["section_id"];
				$discusslink = $a["discussion_id"];	
				$linkpath = $link.htmlspecialchars($sectionlink.$pagelink.$storylink, ENT_QUOTES);
				
				print "\t\t\t<link>".$linkpath."</link>\n";
				print "\t\t\t<guid isPermaLink=\"true\">".$linkpath."</guid>\n";
				
				print "\t\t\t<pubDate>";
				print date("D, j M Y G:i:s O", strtotime(timestamp2usdate($a["story_created_tstamp"])));
				print "</pubDate>\n";
				
				print "\t\t\t<author>";
				print $a["user_fname"];
				print " ".$a['user_email']."";
				print "</author>\n";
				
				$tags = get_record_tags($a["story_id"]);
				
				if ($tags) {
					print "\t\t\t<category>";
					foreach ($tags as $urltag) {
						$tag = ereg_replace("_", " ", $urltag);
						print $tag;
						if ($urltag != end($tags)) print ", ";
					}
					print "</category>\n";
				}
				
				$spacing = htmlspecialchars("<br /><br />");
				$description = "<a href='".$link.$sectionlink.$pagelink.$storylink."'>".$a['page_title']." > ".$a['story_title']."</a><br /><br />";
				$description .= urldecode($a["story_text_short"]);
				$description = convertTagsToInteralLinks($_REQUEST[site], $description);
				//$description = str_replace("\[\[linkpath\]\]","$cfg[full_uri]", $description);
				$description = str_replace("\n", "", $description);
				$description = str_replace("\r", "", $description);
				$description = htmlspecialchars(urldecode($description), ENT_QUOTES, 'utf-8');
				
				print "<description>";
				print $description;
				print "</description>\n";

				if ($a['story_display_type'] == "file") {
					$b = db_get_line("media INNER JOIN slot ON media.FK_site=slot.FK_site","media_id='".addslashes($a["story_text_long"])."'");
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
					print "<enclosure url='$fileurl' length='$filesize' type='$type' />\n";					
				}
				
				
				print "\t\t</item>\n";
			}
								
		}
	
	} else if ($_REQUEST['scope'] == "alldiscuss") {

		$recent_discussion = recent_discussion($thisSite->name);
		
		while ($a = db_fetch_assoc($recent_discussion)) {
		
			$thisSection =& new section($_REQUEST[site],$a['section_id'], $thisSite);
			$thisPage =& new page($_REQUEST[site],$a['section_id'],$a['page_id'], $thisSection);
			
			if ($thisPage->canview("everyone")) {

				print "\t\t<item>\n";
				$title = $a["discussion_subject"];
				print "\t\t\t<title>".htmlspecialchars(urldecode($title), ENT_QUOTES, 'utf-8')."</title>\n";
				
				$storylink = "&amp;story=".$a["story_id"]."&detail=".$a["story_id"]."#".$a["discussion_id"];
				$pagelink = "&page=".$a["page_id"];
				$sectionlink = "&section=".$a["section_id"];
				$discusslink = $a["discussion_id"];	
				$linkpath = $link.htmlspecialchars($sectionlink.$pagelink.$storylink, ENT_QUOTES);
				
				print "\t\t\t<link>".$linkpath."</link>\n";
				print "\t\t\t<guid isPermaLink=\"true\">".$linkpath."</guid>\n";
				
				print "\t\t\t<pubDate>";
				print date("D, j M Y G:i:s O", strtotime(timestamp2usdate($a["discussion_tstamp"])));
				print "</pubDate>\n";
				
				print "\t\t\t<author>";
				print $a["user_fname"];
				print " ".$a['user_email']."";
				print "</author>\n";
				
				$spacing = htmlspecialchars("<br /><br />");
				$description = "<a href='".$link.$sectionlink.$pagelink.$storylink."'>".$a['story_title']." > ".$a['discussion_subject']."</a><br /><br />";						
				$description .= $a["discussion_content"];
				$description = convertTagsToInteralLinks($_REQUEST[site], $description);
				$description = str_replace("\n", "", $description);
				$description = str_replace("\r", "", $description);
				$description = htmlspecialchars(urldecode($description), ENT_QUOTES, 'utf-8');
				
				print "<description>";
				print $description;
				print "</description>\n";
				print "\t\t</item>\n";
			}

		}
	
	}
	
		
/******************************************************************************
 * if no scope then RSS of a given page or tag
 ******************************************************************************/
		
} else {
	
	// check for proper instance of scripts
	if ($allowclasssites != $allowpersonalsites) {
		$type = $thisSite->getField("type");
		if ($allowclasssites && !$allowpersonalsites) {
			if ($type == 'personal') {
				header("Location: $personalsitesurl/index.php?action=rss&site=$site&section=$section&page=$page");
				exit;
			}
		} else if (!$allowclasssites && $allowpersonalsites) {
			if ($type != 'personal' && $type != 'system') {
				header("Location: $classsitesurl/index.php?action=rss&site=$site&section=$section&page=$page");
				exit;
			}
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
				print "<br /><a href='$PHP_SELF?$sid&amp;action=add_site&amp;sitename=".$thisSite->name."'>Create Site</a>";
			}
			print "</li>";
		} else if (!$thisPage) {
			print "<li>Requested page object doesn't exist.</li>";
			
		} else {
			print "<li>The feed has not been made public by the owner.</li>";
		}
		print "</ul>";
		
		$description = htmlspecialchars(ob_get_contents(), ENT_QUOTES);
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
			
			print "\t\t<title>";
			if ($_REQUEST["tag"]) {
				print htmlspecialchars($thisSite->title." > ".$_REQUEST["tag"], ENT_QUOTES);
			} else {
				print htmlspecialchars($thisSite->title." > ".$thisSection->getField("title")." > ".$thisPage->getField("title"), ENT_QUOTES);;
			}
			print "</title>\n";
			
			$link = $cfg[full_uri];			
			$link .= "/index.php?&action=site";
			$link .= "&site=".$thisSite->name;
			
			if ($thisSection) $sectionlink = "&section=".$thisSection->id;			
			if ($thisPage) $pagelink = "&page=".$thisPage->id;
			
			$link = htmlspecialchars($link, ENT_QUOTES);
			$pagelink = htmlspecialchars($pagelink, ENT_QUOTES);
			$sectionlink = htmlspecialchars($sectionlink, ENT_QUOTES);
						
			print "\t\t<link>".$link.$sectionlink.$pagelink."</link>\n";
			
			print "\t\t<description>";
			// 
			print "</description>\n";
			print "\t\t<lastBuildDate>".date("D, j M Y h:i:s O")."</lastBuildDate>\n";
			print "\t\t<generator>Segue RSS Generator</generator>\n";
			
		// handle archiving -- monthly, weekly, etc
		// $thisPage->handleStoryArchive();
	
		// handle ordering of stories
		$thisPage->handleStoryOrder();
					

/******************************************************************************
 * If page has stories then print them
 ******************************************************************************/
		
		if ($thisPage->stories || $_REQUEST["tag"]) {
			
			if ($_REQUEST["tag"]) {
				$tagged_stories = get_tagged_stories($site,$section,$page,$_REQUEST["tag"]);
				$stories = $tagged_stories[story_id];
				//printpre($stories);
				
			} else if ($_REQUEST["detail"]) {
				$stories = array();
				$stories[] = $_REQUEST["detail"];
				
			} else {
				$stories = $thisPage->data[stories];
			}

			$i=0;
			//printpre($stories);
			
			foreach ($stories as $s) {
								
				if ($_REQUEST["tag"]) {
					$tagged_page = $tagged_stories[page_id][$i];
					$pagelink = "&page=".$tagged_page;
					$pagelink = htmlspecialchars($pagelink, ENT_QUOTES);
					$tagged_section = $tagged_stories[section_id][$i];					
					$sectionlink = "&section=".$tagged_section;
					$sectionlink = htmlspecialchars($sectionlink, ENT_QUOTES);
					$o =& new story($site,$tagged_section,$tagged_page,$s, $thisPage);
					
				} else {
					$o = & $thisPage->stories[$s];
				}

				$i++;
				if ($i > 20)
					break;
									
				//printc("<table><tr><td>";
				
				if ($o->canview()) {
					//get type of content
					$incfile = "output_modules/rss/".$o->getField("type").".inc.php";
					
					ob_start();
					include($incfile);
					$description = ob_get_contents();
					ob_end_clean();
					$description = str_replace("\n", "", $description);
					$description = str_replace("\r", "", $description);
					
					$tags = get_record_tags($o->getField("id"));

					print "\t\t<item>\n";
					if ($o->getField("title")) {
						$title = $o->getField("title");
					} else {
						$title = strip_tags($description);
						if (strlen($title) > 25) {
							$title = substr($title, 0, 25)."...";
						}					
					}
					
					print "\t\t\t<title>".htmlspecialchars($title, ENT_QUOTES)."</title>\n";
					
					$storylink = "&amp;story=".$o->getField("id")."&amp;detail=".$o->getField("id");
					print "\t\t\t<link>".$link.$sectionlink.$pagelink.$storylink."</link>\n";
					print "\t\t\t<guid isPermaLink=\"true\">".$link.$sectionlink.$pagelink.$storylink."</guid>\n";
					
					print "\t\t\t<pubDate>";
					print date("D, j M Y G:i:s O", strtotime(timestamp2usdate($o->getField("addedtimestamp"))));
					print "</pubDate>\n";
					
					print "\t\t\t<author>";
					print $o->getField("addedbyfull");
					$user_uname = $o->getField("addedby");
					$user_email = db_get_value("user","user_email","user_uname='".addslashes($user_uname)."'");
					print " ".$user_email."";
					print "</author>\n";
					
					if ($tags) {
						print "\t\t\t<category>";
						foreach ($tags as $urltag) {
							$tag = ereg_replace("_", " ", $urltag);
							print $tag;
							if ($urltag != end($tags)) print ", ";
						}
						print "</category>\n";
					}
					
					if ($o->getField("discuss")) {
						print "\t\t\t<comments>";
						print $link.$sectionlink.$pagelink.htmlspecialchars("&story=".$o->id."&detail=".$o->id, ENT_QUOTES);
						print "</comments>\n";
					}
					
					
					print "<description>";
					print htmlspecialchars($description, ENT_QUOTES, 'utf-8');
					print "</description>\n";
					if ($o->getField("type") == "file") {
						$b = db_get_line("media INNER JOIN slot ON media.FK_site=slot.FK_site","media_id='".addslashes($o->getField("longertext"))."'");
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
						print "<enclosure url='$fileurl' length='$filesize' type='$type' />\n";					
					}

					
					print "\t\t</item>\n";
					
					/******************************************************************************
					 * If detail then 1st item is story and rest is discussion...
					 ******************************************************************************/
					if ($_REQUEST["detail"]) {
						//$ds = & new discussion($o);
						$story_id = $o->getField("id");
						$query = "
							SELECT
								discussion_id,discussion_tstamp,discussion_content,
								discussion_subject,user_uname,user_fname,FK_story,
								FK_author,FK_parent,media_tag
							FROM
								discussion
							INNER JOIN
								user
							ON
								FK_author = user_id
							LEFT JOIN
								media
							ON
								FK_media = media_id
							WHERE
								FK_story='".addslashes($story_id)."'
						";
				
						$r = db_query($query);
						
						while ($a = db_fetch_assoc($r)) {
							print "\t\t<item>\n";
							$title = $a["discussion_subject"];
							print "\t\t\t<title>".htmlspecialchars(urldecode($title), ENT_QUOTES, 'utf-8')."</title>\n";
							
							$storylink = "&story=".$story_id."&detail=".$story_id."#".$a["discussion_id"];
							$storylink = $storylink = htmlspecialchars($storylink, ENT_QUOTES);
							$discusslink = $a["discussion_id"];								
							print "\t\t\t<link>".$link.$sectionlink.$pagelink.$storylink."</link>\n";
							print "\t\t\t<guid isPermaLink=\"true\">".$link.$sectionlink.$pagelink.$storylink."</guid>\n";
							
							print "\t\t\t<pubDate>";
							print date("D, j M Y G:i:s O", strtotime(timestamp2usdate($a["discussion_tstamp"])));
							print "</pubDate>\n";
							
							print "\t\t\t<author>";
							print $a["user_fname"];
							$user_uname = $a["user_uname"];
							$user_email = db_get_value("user","user_email","user_uname='".addslashes($user_uname)."'");
							print " ".$user_email."";
							print "</author>\n";
							
							print "\t\t\t<comments>";
							print $link.$sectionlink.$pagelink.$storylink;
							print "</comments>\n";
							
							$description = $a["discussion_content"];
							$description = convertTagsToInteralLinks($_REQUEST[site], $description);
							$description = str_replace("\n", "", $description);
							$description = str_replace("\r", "", $description);
							$description = htmlspecialchars(urldecode($description), ENT_QUOTES, 'utf-8');
							print "<description>";
							print $description;
							print "</description>\n";
							print "\t\t</item>\n";
														
						}													
					}					
				}
			} //end foreach stories
		}
	}
}

?>	</channel>
</rss><?
exit;
?>