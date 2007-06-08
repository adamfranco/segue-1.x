<? /* $Id$ */

// check view permissions
if (!$thisSite->canview()) {
	ob_start();
	print "You may not view this site. This may be due to any of the following reasons:<br />";
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
			print "\n<br /><a href='$PHP_SELF?$sid&amp;action=add_site&amp;sitename=".$thisSite->name."'>Create Site</a>\n";
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

//printpre($_SESSION);
//printpre($_REQUEST);

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
	if ($supplement = preg_replace('/[^a-z0-9_-]/i', '', $_REQUEST[supplement])) {
		if ($thisSite->getField("type")=='publication' && ($supplement == 'listarticles' || $supplement == 'listissues')) {
			include("$supplement.inc.php");
			break;
		}
	}
	
	
	// check for proper instance of scripts
	if ($allowclasssites != $allowpersonalsites) {
		$type = $thisSite->getField("type");
		if ($allowclasssites && !$allowpersonalsites) {
			if ($type == 'personal') {
				header("Location: $personalsitesurl/index.php?action=site&site=$site&section=$section&page=$page");
				exit;
			}
		} else if (!$allowclasssites && $allowpersonalsites) {
			if ($type != 'personal' && $type != 'system') {
				header("Location: $classsitesurl/index.php?action=site&site=$site&section=$section&page=$page");
				exit;
			}
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
					$thisSection =& $thisSite->sections[$s]; 
					break; 
				}
			}
		}
		$sitetype = $thisSite->getField("type");
	}
	if ($thisSection) {
		// if hide_sidebars then set hide_sidebar variable
		if ($thisSection->getField("hide_sidebar") == 1) {
			$hide_sidebar = 1;
		} else {
			$hide_sidebar = 0;
		}
			
		// If no page is specified, select the first one that we can view.
		if (!$thisPage && count($thisSection->getField("pages"))) {
			$thisSection->fetchDown();
			foreach ($thisSection->pages as $p=>$o) {
				if ($o->getField("type") == 'page' && $o->canview()) { $thisPage =& $thisSection->pages[$p]; break; }
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
	if ($thisSection) $envvars .= "&amp;section=".$thisSection->id;
	
	
	if ($thisPage) {
		
		/******************************************************************************
		 * If hide sidebars then get previous and next pages
		 ******************************************************************************/
		if ($hide_sidebar) {
			
			$prevPage = "";
			$nextPage = "";
			$currentpage = "";
			
			foreach ($thisSection->pages as $p=>$o) {		
				if ($o->canview() && $nextPage =="") { 
										
					if ($o->id != $thisPage->id && $currentpage =="" && $firstpage != 1) {
						$firstpage = 1;
						$prevPage =& $thisSection->pages[$p]; 
						$prevPage_envvars .= "&amp;page=".$prevPage->id;
					} else if ($o->id == $thisPage->id) {
						$currentpage = $thisPage->id;
					} else if ($currentpage) {
						$nextPage =& $thisSection->pages[$p];
						$nextPage_envvars .= "&amp;page=".$nextPage->id;
						$currentpage = "";
					}
					$firstpage = 0;

				}
			}
		}
		
		$envvars .= "&amp;page=".$thisPage->id;
		
	}
	
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
				
			/******************************************************************************
			 * if hide sidebars then print out page pagination
			 ******************************************************************************/
			if ($hide_sidebar) {
				ob_start();
				if ($prevPage) {
					print "<div class='previous_page_link'>";
					print "<a href='$PHP_SELF?$sid&amp;site=$site&amp;page=$prevPage->id&amp;section=$section&amp;action=site'>&lt;&lt; ";
					print $prevPage->getField("title");
					print "</a></div>";
				}
				if ($nextPage) {
					print "<div class='next_page_link'>";
					print "<a href='$PHP_SELF?$sid&amp;site=$site&amp;page=$nextPage->id&amp;section=$section&amp;action=site'>";
					print $nextPage->getField("title");
					print " &gt;&gt;</a></div>";
				} else {
					print "<div>&nbsp;</div>";
				}
				$pagePagination = ob_get_clean();
				printc($pagePagination);
			}

			if ($thisPage->getField("type") == "tags" && $_REQUEST["tag"]) {
				printc("<div class='title'>".$thisPage->getField("title")." > ");
				printc(urldecode($_REQUEST["tag"])."</div>");
			} else if ($_REQUEST["tag"]) {
				printc("<div class='title'>Categories > ");
				printc(urldecode($_REQUEST["tag"])."</div>");
				
			} else if ($_REQUEST['versioning'] || $_REQUEST['version']) {
				printc("");
			} else {

				printc("\n\t\t\t\t<div class='title'>".$thisPage->getField("title")."</div>");
//				$linking_pages = getLinkingPages($site, $section, $page);
//				printc("linking pages:");
//				foreach ($linking_pages as $linktitle => $link) {
//					printc("<a href='".$link."'>".$linktitle."</a>, ");
//				
//				}
//				printc("<hr>");

			}
			
		$i=0;
		// handle archiving -- monthly, weekly, etc
	//	$thisPage->handleStoryArchive();
	
		// handle ordering of stories
		$thisPage->handleStoryOrder();
	/* 	if ($pageinfo[storyorder] != 'custom' && $pageinfo[storyorder] != '') */
	/* 		$stories = handlestoryorder($stories,$pageinfo[storyorder]); */
	
				
		/******************************************************************************
		 * if hide sidebar and page type is not page (e.g. is content or RSS or link ..etc)
		 * print content out
		 ******************************************************************************/
		
		if ($hide_sidebar && $thisPage->getField("type") != "page") {
			
			$id = $thisPage->getField("page_id");			
			if ($thisPage->getField("location") == "left") {
				$nav_source = $leftnav;
			} else {
				$nav_source = $rightnav;
			}
			// find page content in nav array
			foreach ($nav_source as $page_item) {
				if ($page_item['id'] == $id) {
					if ($page_item['type'] == "normal") {
						$page_content = "<a href='".$page_item['url']."'>".$page_item['name']."</a>";
					} else {
						$page_content = $page_item['content'];
					}
					break;
				}			
			}
			
			printc("\n\t\t\t\t\t<table width='100%' cellspacing='0' cellpadding='0'>");
			if ($page_content) printc("\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t".$page_content."\n\t\t\t\t\t
			\t\t\t<br />\n");
			printc("\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>");
		}
		
				
		/******************************************************************************
		 * If page has stories then print them
		 ******************************************************************************/
				
		if ($thisPage->stories || $thisPage->getField("type") == "tags") {
			//if detail then print only story detail ie full text/discussion
			if ($detail) {
				$o =& $thisPage->stories[$detail];
				include("fullstory.inc.php");
			} else if ($_REQUEST['versioning'] || $_REQUEST['version']) {
				$o =& $thisPage->stories[$detail];
				include("versions.inc.php");
				
			} else if ($_REQUEST['search']) {
				
				include("search.inc.php");			
			
			//if not detail, then print all stories
			
			} else {
			
				/******************************************************************************
				 * Set up story pagination variables
				 ******************************************************************************/
				 $num_per_set = 0;
				if (is_numeric($thisPage->getField("archiveby"))) {
					$num_per_set = $thisPage->getField("archiveby");
				} else {
					$num_per_set = 100000000;
				}
				
				if ($_REQUEST["story_set"] > 0) 
					$story_set = $_REQUEST["story_set"] - 1;
				else
					$story_set = 0;
					
				$start = $story_set * $num_per_set;
				$end = $start + $num_per_set;

				
				if ($_REQUEST["tag"]) {
					$tagged_stories = get_tagged_stories($site,$section,$page,$_REQUEST["tag"]);
					$stories = $tagged_stories[story_id];
					$tag = $_REQUEST["tag"];
				} else {
					$stories = $thisPage->data[stories];
				}
								
				/******************************************************************************
				 * Print out story pagination links
				 ******************************************************************************/
				$pagelinks = array();
				if (count($stories) > $num_per_set && $num_per_set != 0)  {
					printc("\n\t\t\t\t<div class='multi_page_links'>");
					
					for ($j = 0; $j < (count($stories) / $num_per_set); $j++) {
						if ($story_set == $j) {
							$pagelinks[] = "current";
							printc("<strong>".($j+1)."</strong> | ");
						} else {
						if (!$_REQUEST["tag"]) $tag = "";
							$pagelinks[] = "?$sid&amp;$envvars&amp;action=site&amp;tag=$tag&amp;story_set=".($j+1);
							printc("<a href='?$sid&amp;$envvars&amp;action=site&amp;tag=$tag&amp;story_set=".($j+1)."'>".($j+1)."</a> | ");
						}
					}
					
					/******************************************************************************
					 * If num_per_set = 1 show menu of story titles (or snippets from short text)
					 ******************************************************************************/
					 
					if ($num_per_set == 1) {
					//	printpre ($num_per_set);
						printc("\n\t\t\t\t<div class='multi_page_links'>\n\t\t\t\t<select name='story_nav' onchange='window.location=this.value;'>");
						$n = 0;
						foreach ($stories as $story_id) {
							$story_title = db_get_value("story", "story_title", "story_id ='".addslashes($story_id)."'");
							$pagelink = $pagelinks[$n];
							if ($story_set != $j) {
								if ($story_title) {
									printc("\n\t\t\t\t<option value='".$pagelink."'".(($pagelink=='current')?" selected='selected'":"").">".$story_title."</option>");
								} else {
									$story_text = db_get_value("story", "story_text_short", "story_id ='".addslashes($story_id)."'");
									$shory_text_all = strip_tags(urldecode($story_text));
									$story_text = substr($shory_text_all,0,25);
									printc("\n\t\t\t\t<option value='".$pagelink."'".(($pagelink=='current')?" selected='selected'":"").">".$story_text."...</option>");
								}
							}
							$n = $n+1;
						}
						printc("\n\t\t\t\t</select>\n\t\t\t\t</div>");			
					}
					
					printc("\n\t\t\t\t</div>");
				}
				
				 /******************************************************************************
				 * Print out stories in pagination range
				 ******************************************************************************/
				for ($j= $start; $j < $end && $j < count($stories); $j++) {									
					$s = $stories[$j];
					if ($_REQUEST["tag"]) {
						$tagged_page = $tagged_stories[page_id][$j];
						$tagged_section = $tagged_stories[section_id][$j];					
						$o =& new story($_REQUEST[site],$tagged_section,$tagged_page,$s, $thisPage);
					} else {
						$o = & $thisPage->stories[$s];
					}
					
					if ($o && $o->canview() && indaterange($o->getField("activatedate"), $o->getField("deactivatedate"))) {
						if (($thisPage->getField("showhr") || $_REQUEST["tag"]) && $i!=0) {
							printc("\n\t\t\t\t<div class='hr'><hr /></div>");
						}
														
						
						/******************************************************************************
						 * print out story title
						 ******************************************************************************/
						
						if ($tagged_section) {
							$source_section = $section;
							$section = $tagged_section;	
						}
						if ($tagged_page) {
							$source_page = $page;
							$page = $tagged_page;
						}
						
						if ($o->getField("title")  && $o->getField("type") != "link" && $o->getField("type") != "file" && $o->getField("type") != "image") {
							printc("\n\t\t\t\t\t\t<div class='leftmargin'>\n\t\t\t\t\t\t\t<strong><a name='".$o->id."'");
							printc(" href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id."'>");
							printc(spchars($o->getField("title"))."</a></strong>\n\t\t\t\t\t</div>\n");	
						}
						
						
						/******************************************************************************
						 * Get story tags and display them.
						 ******************************************************************************/
						$record_id = $o->id;
						$user_id = $_SESSION[aid];
						$record_type = "story";
						$story_tags = get_record_tags($record_id);
						
						if ($story_tags) {
							printc("\n\t\t\t\t<div class='contentinfo' style='margin-top: 0px;'>");
							printc("Categories:");
							foreach ($story_tags as $tag) {
								$urltag = urlencode($tag);
								printc("<a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;tag=$urltag' rel='$tag'>".urldecode($tag)."</a> ");
							}
								printc("</div>\n");
						}

						/******************************************************************************
						 * include story output module
						 ******************************************************************************/
						printc("\n\t\t\t\t<div class='story'>\n\t\t\t\t\t");
						$incfile = "output_modules/".$thisSite->getField("type")."/".$o->getField("type").".inc.php";
					//	print $incfile; // debug
						include($incfile);
						printc("\n\t\t\t\t</div>");

						
					/******************************************************************************
					 * author, editor, timestamp info
					 ******************************************************************************/
					 
					
						if ($thisPage->getField("showcreator") || $thisPage->getField("showeditor") || $thisPage->getField("showdate") || $thisPage->getField("showversions")) {
							printc("<div class='contentinfo'>");
							$added = timestamp2usdate($o->getField("addedtimestamp"));
							$edited = timestamp2usdate($o->getField("editedtimestamp"));
							
							$linksAddedSoFar = false;
							// if show date but not creator							
							if ($thisPage->getField("showdate") && !$thisPage->getField("showcreator") && !$o->getField("editedtimestamp")) {
								printc(" added on $added");
								$linksAddedSoFar = true;
							} else if ($thisPage->getField("showdate") && (!$thisPage->getField("showcreator") && !$thisPage->getField("showeditor"))  && $o->getField("editedtimestamp")) {
								printc(" updated on $edited");
								$linksAddedSoFar = true;
																				
							// if show date and creator/editor
							} else if ($thisPage->getField("showdate") && ($thisPage->getField("showcreator") || $thisPage->getField("showeditor"))) {
								if ($thisPage->getField("showcreator") && $thisPage->getField("showeditor") == $thisPage->getField("showcreator") && $o->getField("editedtimestamp")) {
									printc("updated by ".$o->getField("editedbyfull")." on $edited");
								} else if ($thisPage->getField("showcreator") && !$o->getField("editedtimestamp")) {
									printc("added by ".$o->getField("addedbyfull")." on $added");
								} else if ($thisPage->getField("showeditor") && $o->getField("editedtimestamp")) {
									printc("updated by ".$o->getField("editedbyfull")." on $edited");
								} else if ($thisPage->getField("showcreator") && $o->getField("editedtimestamp")) {
								printc("added by ".$o->getField("addedbyfull")." on $added");
								$linksAddedSoFar = true;
							}
								
							// if don't show date but show creator/editor
							} else if (!$thisPage->getField("showdate") && $thisPage->getField("showeditor") && $thisPage->getField("showcreator") && $o->getField("addedbyfull") != $o->getField("editedbyfull")) {
								printc("added by ".$o->getField("addedbyfull")."<br />");
								printc("updated by ".$o->getField("editedbyfull"));
								$linksAddedSoFar = true;
							} else if (!$thisPage->getField("showdate") && $thisPage->getField("showcreator")) {
								printc("added by ".$o->getField("addedbyfull"));
								$linksAddedSoFar = true;
							} else if (!$thisPage->getField("showdate") && $thisPage->getField("showeditor")) {
								printc("updated by ".$o->getField("editedbyfull"));
								$linksAddedSoFar = true;
							}
							
														
							// if versioning then show link to versions
							if ($thisPage->getField("showversions") == 1) {
								if ($linksAddedSoFar)
									printc(" | ");
								printc(" <a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;versioning=".$o->id."'>");
								printc("versions</a>\n");								
							}
							
							if ($thisPage->getField("showcreator") || $thisPage->getField("showeditor") || $thisPage->getField("showdate")) {
								printc(" | <a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id."'>");
								printc("permalink</a>\n");
							}
							printc("</div>");
							//printc("<hr size='1' noshade /><br />");
						} 
						if (!$thisPage->getField("showhr")) {
							printc("\n\t\t\t\t<br />");
						}
					}
					$i++; // increment counter for handleStoryArchive 				
				} //end for loop stories	
			} //else of this page conditional						
		} //end if this page stories
		
		/******************************************************************************
		 * Print out story pagination again
		 ******************************************************************************/
		if (count($stories) > $num_per_set && $num_per_set != 0)  {
			printc("\n\t\t\t\t<br />\n\t\t\t\t<div class='multi_page_links'>");
			
			for ($j = 0; $j < (count($stories) / $num_per_set); $j++) {
				if ($story_set == $j)
					printc("<strong>".($j+1)."</strong> | ");
				else
					printc("<a href='$PHP_SELF?$sid&amp;$envvars&amp;action=site&amp;tag=".$_REQUEST["tag"]."&amp;story_set=".($j+1)."'>".($j+1)."</a> | ");
			}
			
			printc("\n\t\t\t\t</div>");
		}
		
		/******************************************************************************
		 * Print out page pagination
		 ******************************************************************************/
		if ($hide_sidebar) {
			printc($pagePagination);
		}


		/******************************************************************************
		 * Print out link for stories RSS
		 ******************************************************************************/
		if (is_object($thisPage) && $thisPage->hasPermission("view", "everyone")) {
			printc("\n\t\t\t\t<br />\n\t\t\t\t<div class='rss_link'>\n\t\t\t\t\t<a href='".preg_replace("/action=(viewsite|site)/","action=rss", htmlentities($_SERVER['REQUEST_URI']))."'>\n\t\t\t\t\t\t<img border='0' src='$cfg[themesdir]/common/images/rss_icon02.png' alt='rss' title='RSS feed of this page'/> RSS\n\t\t\t\t\t</a>\n\t\t\t\t</div>");
		}	
	
	} // end if this page
	
} while(0);

ob_start();

$btnw = "125px"; // button width

// add the key to the footer of the page
if ($thisSite->isEditor()
	&& $thisSite->hasPermissionDown("add || edit || delete")
	&& !$_REQUEST[themepreview]) 
{
	print "\n<br /> \n\n<div align='right'>";
	if (ereg('preview_as', $_REQUEST['action'])) {
		$editAction = ereg_replace('preview_as', '&amp;action=preview_edit_as', $_REQUEST['action']);
	 } else {
		$editAction = '&amp;action=viewsite';
	}

	if ($_SESSION[auser] == $thisSite->owningSiteObj->owner) {
		print "\n\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' name='preview_as' value=' &nbsp; Preview Site As... &nbsp;' onclick='sendWindow(\"preview_as\",400,300,\"preview.php?$sid&amp;site=$site&amp;query=".urlencode($_SERVER[QUERY_STRING])."\")' />";

	}

	$u = "$PHP_SELF?$sid".$editAction."&amp;site=$site".(($supplement)?"&amp;supplement=$supplement":"");
	if ($section) $u .= "&amp;section=$section";
	if ($page) $u .= "&amp;page=$page";
	print "\n<input type='submit' class='button' value='Edit This Site' onclick=\"window.location='$u&amp;$sid'\" />\n</div><br />";
}
if (is_object($thisPage) && $thisPage->hasPermission("view", "everyone")) {
	print "<div style='font-size: 9px;' align='right'><img border='0' src='$cfg[themesdir]/common/images/rss_icon02.png' alt='rss' /> <a href='viewrss.php?site=$site' target='rss' onclick='doWindow(\"rss\",450,200)' class='navlink' title='click for more RSS feeds...'>More RSS...</a></div>";
}	

print "\n<br />\n<div align='right'>\n<div style='font-size: 1px;'>powered by segue</div>\n<a href='http://segue.sourceforge.net' target='_blank'>\n<img border='0' src='$cfg[themesdir]/common/images/segue_logo_trans_solid.gif' alt='segue_logo'/>\n</a>\n</div>";

$sitefooter = $sitefooter . ob_get_contents();
ob_end_clean();
?>

