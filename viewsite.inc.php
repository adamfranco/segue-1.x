<? /* $Id$ */

if ($thisSite) $site=$thisSite->name;
if ($thisSection) $section=$thisSection->id;
if ($thisPage) $page = $thisPage->id;

//printpre($_SESSION['showorder']);
//if (isset($_REQUEST['showorder'])) {
//	unset($_SESSION['showorder']);
//	$_SESSION['showorder'] = $_REQUEST['showorder'];
//	printpre($_SESSION['showorder']);
//}

//printpre($_SESSION['story_set']);
//if (isset($_REQUEST['story_set'])) {
//	unset($_SESSION['story_set']);
//	$_SESSION['story_set'] = $_REQUEST['story_set'];
//	printpre($_SESSION['story_set']);
//}

function printStoryEditLinks() {
	global $thisSite, $thisSection, $thisPage, $thisStory, $tagged_page, 
		$site_owner, $o, $story_set, $s, $sid, $site, $section, $page, $cfg;
		
	printc("<div class='edit_links'>");	
	if ($cfg['disable_edit_content'] == TRUE && $_SESSION['ltype'] != 'admin') {
		printc ("<div align='right'>editing disabled</div>");
	} else {
		
	
		/******************************************************************************
		 * edit, delete options 
		 ******************************************************************************/
		if ($_REQUEST['tag']) {
			$envvars = "&amp;site=".$thisSite->name;
			if ($tagged_section) {
				$envvars .= "&amp;section=".$tagged_section;
			}
			if ($tagged_page) {
				$envvars .= "&amp;page=".$tagged_page;
			}
		}
		//printpre($story_set);
		$l = array();
		if (($_SESSION[auser] == $site_owner) || (($_SESSION[auser] != $site_owner) && !$o->getField("locked"))) {
			if (!$_REQUEST['tag']) {
			
				if (($thisPage->getField("storyorder") == 'custom' || $thisPage->getField("storyorder") == '') && $thisPage->hasPermission("edit") ) {
					if ($_REQUEST[showorder] == "story") {
						$l[] = "<a href='$PHP_SELF?$sid$envvars&amp;action=viewsite&amp;site=$site&amp;section=$section&amp;page=$page&amp;showorder=0".(($story_set)?"&amp;story_set=".($story_set+1):"")."' class='small' title='Reorder content blocks on this page'>hide order</a>";
					} else {
						$l[] = "<a href='$PHP_SELF?$sid$envvars&amp;action=viewsite&amp;site=$site&amp;section=$section&amp;page=$page&amp;showorder=story".(($story_set)?"&amp;story_set=".($story_set+1):"")."' class='small' title='Reorder content blocks on this page'>reorder</a>";									
					}
				}
	
				if (($thisPage->getField("archiveby") == '' || $thisPage->getField("archiveby") == 'none' || !$thisPage->getField("archiveby")) && $thisPage->hasPermission("edit")) {
													
	//								if ($i!=0 && ($thisPage->getField("storyorder") == 'custom' || $thisPage->getField("storyorder") == '')) {
	//									$l[] = "<a href='$PHP_SELF?$sid$envvars&amp;action=viewsite&amp;reorder=story&amp;direction=up&amp;id=$s' class='small' title='Move this Content Block up'><b>&uarr;</b></a>";
	//								}
	//								if ($i!=count($thisPage->stories)-1 && ($thisPage->getField("storyorder") == 'custom' || $thisPage->getField("storyorder") == '')) {
	//									$l[] = "<a href='$PHP_SELF?$sid$envvars&amp;action=viewsite&amp;reorder=story&amp;direction=down&amp;id=$s' class='small' title='Move this Content Block down'><b>&darr;</b></a>";
	//								}
				}
				if ($thisPage->hasPermission("edit") || $o->hasPermission("edit")) $l[]="<a href='copy_parts.php?$sid&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$s&amp;type=story' class='small' title='Move/Copy this Content Block to another page' onclick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>";
			}
			if ($thisPage->hasPermission("edit") || $o->hasPermission("edit")) 
				$l[]="<a href='$PHP_SELF?$sid$envvars&amp;action=edit_story&amp;site=$site&amp;section=$section&amp;page=$page&amp;edit_story=$s&amp;comingFrom=viewsite".(($story_set)?"&amp;story_set=".($story_set+1):"")."' class='small' title='Edit this Content Block'>edit</a>";
			if ($thisPage->hasPermission("delete") || $o->hasPermission("delete")) 
				$l[]="<a href='javascript:doconfirm(\"Are you sure you want to delete this content?\",\"$PHP_SELF?$sid$envvars&amp;site=$site&amp;section=$section&amp;page=$page&amp;action=delete_story&amp;delete_story=$s\")' class='small' title='Delete this Content Block'>delete</a>";
			if ($thisPage->hasPermission("edit") || $o->hasPermission("edit"))
				$l[]= "<a href='index.php?$sid&amp;action=viewsite&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$s."&amp;versioning=".$s."' class='small' title='View the history of this content'>versions</a>";
		}
		printc(implode(" | ",$l));
	}
	printc("</div>");
}


do {
	// for publication sites
	$supplement = preg_replace('/[^a-z0-9_-]/i', '', $_REQUEST[supplement]);
	if ($supplement) {
		if ($thisSite->getField("type")=='publication' && ($supplement == 'listarticles' || $supplement == 'listissues')) {
			include("$supplement.inc.php");
			break;
		}
	}
	
	if ($thisSite) {
		if (!$thisSection && count($thisSite->getField("sections"))) {
			$thisSite->fetchDown();
			foreach (array_keys($thisSite->sections) as $k=>$s) {
				$o =& $thisSite->sections[$s];
				if ($o->getField("type") == 'section' && $o->canview()) { $thisSection =& $o; break; }
			}
		}
	/* 	print count($thisSite->sections); */
		$sitetype = $thisSite->getField("type");
	}
	unset($o);
	
	if ($thisSection) {
		$pageorder = $thisSection->getField("pageorder");
		if ($pageorder=="") $thisSection->setField("pageorder", "custom");
		
		//if no page specified get first that can be viewed
		if (!$thisPage && count($thisSection->getField("pages"))) {
			$thisSection->fetchDown();
						
			foreach (array_keys($thisSection->pages) as $k=>$p) {
				$o =& $thisSection->pages[$p];
				if ($o->getField("type") == 'page' && $o->canview()) { $thisPage =& $o; break; }
			}
		}
		$st = " > " . $thisSection->getField("title");
		// check category permissions
	}
	unset($o);

	if ($thisPage) {		// we're viewing a page
		$pt = " > " . $thisPage->getField("title");
		// check page permissions
	}
	$pagetitle = $previewTitle . $thisSite->getField("title") . $st . $pt;
	
	if ($thisSite->site_does_not_exist) {
		
		// Check for a Segue 2 site if appropriate, and forward if it exists.
		if (defined('DATAPORT_SEGUE2_BASE_URL')) {
			$result = @file_get_contents(DATAPORT_SEGUE2_BASE_URL
				."?module=dataport&action=site_exists&site=".$_REQUEST['site']);
			
			if ($result === 'true') {
				// Send our same query string to Segue 2 and see if it can resolve it.
				$url = DATAPORT_SEGUE2_BASE_URL."?".$_SERVER['QUERY_STRING'];
				header("Location: ".$url);
				
			} else if ($result === false) {
				print "\n		<li>Configuration error: could not check Segue 2.</li>";
			}
		}
	}
	
	if (!$thisSite->isEditor()
		|| !$thisSite->hasPermissionDown("add || edit || delete")) 
	{
		
		error("You do not have permission to edit this site.");
		return;
	}
	
	// check for proper instance of scripts
	if ($allowclasssites != $allowpersonalsites) {
		$type = $thisSite->getField("type");
		if ($allowclasssites && !$allowpersonalsites) {
			if ($type == 'personal') {
				header("Location: $personalsitesurl/index.php?action=viewsite&site=$site&section=$section&page=&page");
				exit;	
			}
		} else if (!$allowclasssites && $allowpersonalsites) {
			if ($type != 'personal' && $type != 'system') {
				header("Location: $classsitesurl/index.php?action=viewsitesite=$site&section=$section&page=&page");
				exit;
			}
		}
	}
	
	// we are reordering either pages or sections (or stories?)
	if ($_REQUEST[reorder]) {
		
		if ($_REQUEST[reorder] == 'page' && $thisSection->hasPermission("edit")) {
		}
		if ($_REQUEST[reorder] == 'section' && $thisSite->hasPermission("edit")) {
		}
		if ($_REQUEST[reorder] == 'story' && $thisPage->hasPermission("edit")) {
		}
	}	
	
	$envvars = "&amp;site=".$thisSite->name;
	if ($thisSection) $envvars .= "&amp;section=".$thisSection->id;
	if ($thisPage) $envvars .= "&amp;page=".$thisPage->id;
	
	$site=$thisSite->name;
	$section=$thisSection->id;
	$page=$thisPage->id;
	$thisSite->fetchDown();			// just in case we haven't already
	
	$topsections = !ereg("Side\+Sections",$thisSite->getField("themesettings"));
	/* print "themsettings: \"".$thisSite->getField("themesettings")."\"<br />"; */
	/* print $topsections; */
	
//	$thisSection->handlePageOrder();
	// build the navbar
	$siteType = preg_replace('/[^a-z0-9_-]/i', '', $thisSite->getField("type"));
	include ("output_modules/".$siteType."/navbars.inc.php");
	
	if ((!is_object($thisSection) &&is_object($thisSite) && count($thisSite->sections == 0))) 
	{
		printc("Click the '+ add section' button to add a section to this site.\n<br/>");
	}
	
	if ($thisPage) {
		$thisPage->fetchDown();
		
		// if hide_sidebars then set hide_sidebar variable
		if ($thisSection->getField("hide_sidebar") == 1) {
			$hide_sidebar = 1;
		} else {
			$hide_sidebar = 0;
		}
		//printpre ($hide_sidebar);
 
		if ($thisPage->canview()) {
		
			if ($thisPage->getField("type") == "tags" && $_REQUEST["tag"]) {
				printc("<div class='title'>".$thisPage->getField("title")." > ");
				printc(urldecode($_REQUEST["tag"])."</div>");
			} else if ($_REQUEST["tag"]) {
				printc("<div class='title'>Categories > ");
				printc(urldecode($_REQUEST["tag"])."</div>");
			} else if ($_REQUEST['versioning'] || $_REQUEST['version']) {
				printc("");
			} else {
				printc("<div class='title'>".$thisPage->getField("title"));
				if ($thisSection->hasPermission("edit")) {
					printc(" <span style='font-variant: normal; font-weight: normal;'>");
//					if ($_REQUEST["showorder"] == "story" && $thisPage->getField("storyorder") == 'custom') {
//						printc("<a href='$PHP_SELF?$sid$envvars&amp;action=viewsite&amp;showorder=0' class='small' title='Hide reorder fields on this page'>hide order</a> | ");
//					} else if ($thisPage->getField("storyorder") == 'custom') {
//						printc("<a href='$PHP_SELF?$sid$envvars&amp;action=viewsite&amp;showorder=story' class='small' title='Reorder items on this page'>reorder</a> | ");
//					}
//					printc("<a href='copy_parts.php?$sid&amp;site=$site&amp;section=$section&amp;page=$page&amp;type=page' class='small' title='Move/Copy this page to another section' onclick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a> |");
					printc("<a href='$PHP_SELF?$sid$envvars&amp;action=edit_page&amp;edit_page=$page&amp;step=3&amp;comingFrom=viewsite' class='small' title='Edit display of this page'>[display options]</a>");
//					printc("<a href='javascript:doconfirm(\"Are you sure you want to permanently delete this page and any data that may be contained within it?\",\"$PHPSELF?$sid$envvars&amp;action=delete_page&amp;delete_page=$page\")' class='small' title='Delete this page'>delete</a>");
					printc("</span>");
				}
				printc("</div>");
//				$linking_pages = getLinkingPages($site, $section, $page);
//				printc("linking pages:");
//				foreach ($linking_pages as $linktitle => $link) {
//					printc("<a href='".$link."'>".$linktitle."</a>, ");
//				
//				}
//				printc("<hr>");
			}
		}
		
		// handle ordering of stories
	/* 	if ($thisPage->getField("storyorder") != 'custom' && $thisPage->getField("storyorder") != '') */
	/* 		$stories = handlestoryorder($stories,$pageinfo[storyorder]); */
		$thisPage->handleStoryOrder();
		
		$_top_addlink_orders = array("addeddesc","editeddesc","author","editor","category","titleasc","titledesc");
		if ($thisPage->hasPermission("add") && in_array($thisPage->getField("storyorder"),$_top_addlink_orders)) 
			printc("<br /><div align='right'><a href='$PHP_SELF?$sid$envvars&amp;action=add_story&amp;comingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div><br /><hr class='block' />");
		
		$i=0;
		
		/******************************************************************************
		 * Stories: 
		 ******************************************************************************/
		
		if ($thisPage->stories || $thisPage->getField("type") == "tags") {
			//if detail then print only story detail ie full text/discussion
			if ($_REQUEST['detail']) {
				$o =& $thisPage->stories[$_REQUEST['detail']];
				$s = $_REQUEST['detail'];
				
				$tmp = $content;
				$content = '';
				printStoryEditLinks();
				$storyEditLinks = $content;  
				$content = $tmp;
								
				include("fullstory.inc.php");

			} else if ($_REQUEST['versioning'] || $_REQUEST['version'] ) {
				$o =& $thisPage->stories[$_REQUEST['versioning']];
				include("versions.inc.php");	
			
			
			} else if ($_REQUEST['user']) {
				//$o =& $thisPage->stories[$_REQUEST['versioning']];
				//include("participation.inc.php");	
				
			} else if ($_REQUEST['search']) {
				
				include("search.inc.php");
			
			} else {
				/******************************************************************************
				 * Set up pagination variables
				 ******************************************************************************/
				 
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
	
				/******************************************************************************
				 * Print out story pagination links
				 ******************************************************************************/
	
				if ($_REQUEST["tag"]) {
					$tagged_stories = get_tagged_stories($site,$section,$page,$_REQUEST["tag"]);
					$stories = $tagged_stories[story_id];
					//printpre($stories);
				} else {
					$stories = $thisPage->data[stories];
				}
			
				printc("<div align='right'>");
				if (count($stories) > $num_per_set)  {
					for ($j = 0; $j < (count($stories) / $num_per_set); $j++) {
					
						if ($story_set == $j) {
							$pagelinks[] = "current";
							printc(" <strong>".($j+1)."</strong> | ");
						} else {
							$pagelinks[] = "?$sid$envvars&amp;action=viewsite&amp;story_set=".($j+1).(($_REQUEST['showorder']=='story')?"&amp;showorder=story":"");
							printc("<a href='$PHP_SELF?$sid$envvars&amp;action=viewsite&amp;story_set=".($j+1).(($_REQUEST['showorder']=='story')?"&amp;showorder=story":"")."'>".($j+1)."</a> | ");
						}
					}
				}
				
				/******************************************************************************
				 * If num_per_set = 1 show menu of story titles (or snippets from short text)
				 ******************************************************************************/
				 
				if ($num_per_set == 1) {
				//	printpre ($num_per_set);
					printc("\n<div align='right'>\n\t<select name='story_nav' onchange=\"window.location=this.value;\">");
					$n = 0;
					foreach ($stories as $story_id) {
						$story_title = db_get_value("story", "story_title", "story_id ='".addslashes($story_id)."'");
						$pagelink = $pagelinks[$n];
						if ($story_set != $j) {
							if ($story_title) {
								printc("\n\t\t<option value='".$pagelink."'".(($pagelink=='current')?" selected='selected'":"").">".$story_title."</option>");
							} else {
								$story_text = db_get_value("story", "story_text_short", "story_id ='".addslashes($story_id)."'");
								$shory_text_all = strip_tags(urldecode($story_text));
								$story_text = substr($shory_text_all,0,25);
								printc("\n\t\t<option value='".$pagelink."'".(($pagelink=='current')?" selected='selected'":"").">".$story_text."...</option>");
							}
						}
						$n = $n+1;
					}
					printc("\n\t</select>\n</div>");			
				}
	
				printc("\n</div>\n\n");
									
				 /******************************************************************************
				 * Print out stories in pagination range
				 ******************************************************************************/
				$nextorder = 0;
				
				for ($j= $start; $j < $end && $j < count($stories); $j++ ) {
					
					$s = $stories[$j];
					$reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page.(($_REQUEST['story_set'])?"&amp;story_set=".$_REQUEST['story_set']:"")."&amp;reorderContent=".$s."&amp;newPosition=";
					
					
					if ($_REQUEST["tag"]) {
						$tagged_page = $tagged_stories[page_id][$j];
						$tagged_section = $tagged_stories[section_id][$j];					
						$o =& new story($_REQUEST[site],$tagged_section,$tagged_page,$s, $thisPage);
					} else {
						$o = & $thisPage->stories[$s];
					}
					
					if ($o && $o->canview()) {
						if ($i!=0)
							printc("<hr class='block' style='margin-top: 10px' />");
							
	
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
						
						printc("<div class='leftmargin'>\n");
						
						// Reorder Links	
						if ($_REQUEST['showorder'] == "story" && ($thisPage->getField("storyorder") == 'custom' || $thisPage->getField("storyorder") == '') && $thisPage->hasPermission("edit")) {
							//print "<select name='reorder2' style = 'font-size: 9px; display: none;' class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
							printc("<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'");
							for ($i=0; $i<count($stories); $i++) {
								printc( "<option value='".$i."'".(($i==$nextorder || $i==$story_set)?" selected":"").">".($i+1)."</option>\n");
							}
							printc("</select>\n");
						}
						
						if ($o->getField("title") && $o->getField("type") != "link" && $o->getField("type") != "file" && $o->getField("type") != "image") {
							
							printc("<strong>");			
							printc("<a name='".$o->id."'");
							printc(" href='index.php?$sid&amp;action=viewsite&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id."'>");
							printc(spchars($o->getField("title"))."</a></strong>\n");	
						}
						printc("</div>\n");
	
						/******************************************************************************
						 * check is story is active and if not, display active dates.
						 ******************************************************************************/
	
						if (!indaterange($o->getField("activatedate"), $o->getField("deactivatedate"))) {
							printc("<div class='contentinfo' align='left'>\n");
							if (!nulldate($o->getField("activatedate"))) printc("Active dates: <strong><a href='index.php?$sid&amp;action=edit_story&amp;edit_story=".$o->id."&amp;comingFrom=viewsite&amp;step=3&amp;site=$site&amp;section=$section&amp;page=$page&amp;'>".$o->getField("activatedate")."</a></strong>");
							if (!nulldate($o->getField("deactivatedate"))) printc(" to <strong><a href='index.php?$sid&amp;action=edit_story&amp;edit_story=".$o->id."&amp;comingFrom=viewsite&amp;step=3&amp;site=$site&amp;section=$section&amp;page=$page&amp;'>".$o->getField("deactivatedate")."</strong>");
							printc("</div>");
						}
						
						/******************************************************************************
						 * Get story tags and display them
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
								printc("<a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;tag=$urltag'>".urldecode($tag)."</a>\n");
							}
							if ($cfg['disable_edit_content'] != TRUE && $_SESSION['ltype'] != 'admin') {
								printc(" <a href='index.php?$sid&amp;action=edit_story&amp;edit_story=".$o->id."&amp;comingFrom=viewsite&amp;step=3&amp;site=$site&amp;section=$section&amp;page=$page&amp;".(($story_set)?"&amp;story_set=".($story_set+1):"")."'>[edit]</a>");
							}									
							printc("</div>\n");							
						}
						
						$siteType = preg_replace('/[^a-z0-9_-]/i', '', $thisSite->getField("type"));
						$oType = preg_replace('/[^a-z0-9_-]/i', '', $o->getField("type"));
						$incfile = "output_modules/".$siteType."/".$oType.".inc.php";
		/* 				print "<br />".$incfile; */
						include($incfile);
						
						/******************************************************************************
						 * author, editor, timestamp info
						 ******************************************************************************/
						
						if ($thisPage->getField("showcreator") || $thisPage->getField("showeditor") || $thisPage->getField("showdate") || $thisPage->getField("showversions")) {
							$linksAddedSoFar = false;
							printc("<div class='contentinfo' align='right'>");
							$added = timestamp2usdate($o->getField("addedtimestamp"));
							$edited = timestamp2usdate($o->getField("editedtimestamp"));
							
		
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
								}
								$linksAddedSoFar = true;
								
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
								printc(" <a href='index.php?$sid&amp;action=viewsite&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;versioning=".$o->id."'>");
								printc("versions</a>\n");								
							}
	
							printc("</div>");
							//printc("<hr size='1' noshade /><br />");
						}						
					
						printStoryEditLinks($thisPage);
						$i++;
					}
					unset($o);
					$nextorder++;
				}
			}
			
		/******************************************************************************
		 * if now stories print content add note
		 ******************************************************************************/
			
		} else if ($thisPage && count($thisPage->stories) == 0) {
			if ($thisPage->getField("type") != "page") {
				printc("Only New Page item types can have content block here...\n<br/>");
			} else {
				printc("Click the '+ add content' button to add a content block to this page.\n<br/>");
			}
		}else if ($thisPage && count($thisPage->stories) == 0) {
			printc("Click the '+ add content' button to add a content block to this page.\n<br/>");
		}
		
		$_b = array("","custom","addedasc","editedasc");
		if ($thisPage->hasPermission("add") 
			&& in_array($thisPage->getField("storyorder"),$_b) 
			&& $thisPage->getField("type") == "page"
			&& !($_REQUEST['versioning'] || $_REQUEST['detail'] || $_REQUEST['version'])) 
		{

			if ($cfg['disable_edit_content'] == TRUE && $_SESSION['ltype'] != 'admin') {
				printc ("<div align='right'>adding content disabled</div>");
			} else {			
				printc("<br /><hr class='block' /><div align='right'><a href='$PHP_SELF?$sid$envvars&amp;action=add_story&amp;comingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div>");
			}
		}
	} else if ($thisSection && count($thisSection->pages) == 0) {
		printc("Click the '+ add item' button to add a page to this section.\n<br/>");
	}
} while (0);



/******************************************************************************
 * bottom button box
 ******************************************************************************/

if (ereg('preview_edit_as', $_REQUEST['action'])) {
	$previewAction = ereg_replace('preview_edit_as', '&amp;action=preview_as', $_REQUEST['action']);
 } else {
	$previewAction = '&amp;action=site';
}
// add the key to the footer of the page
/*$u = "$_SERVER[SCRIPT_URI]?action=site&amp;site=$site";*/
$u = "$PHP_SELF?$sid".$previewAction."&amp;site=$site";
if ($section) $u .= "&amp;section=$section";
if ($page) $u .= "&amp;page=$page";
if ($supplement) $u .="&amp;supplement=$supplement";


ob_start();
print "\n\n<br />\n<div align='right'>\n<table style='border-top: 2px solid #666; border-left: 2px solid #666; border-bottom: 2px solid #666; border-right: 2px solid #666; background-color: #ddd;'>\n\t<tr>";
print "\n\t<td valign='top' align='left'>";

$btnw = "125px"; // button width
$sty = "style='width: $btnw;"; // ignore this
if ($thisSite->hasPermission("edit")) {
	print "\n\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' value='Edit Site Settings' onclick=\"window.location='index.php?$sid&amp;action=edit_site&amp;sitename=$site&amp;comingFrom=viewsite'\" />";
} else {
	print "\n\t&nbsp; ";
}

print "\n\t</td>";
print "\n\t<td valign='top' align='left'>";

if (!ereg('preview_edit_as', $_REQUEST['action'])) {
	print "\n\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' name='sitemap' value=' Permissions ' onclick='sendWindow(\"permissions\",600,400,\"edit_permissions.php?$sid&amp;site=$site\")'  />";
}

print "\n\t</td>";
print "\n\t<td valign='top' align='left'>";

print "\n\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' value='View This Site'  onclick=\"window.location='$u&amp;$sid'\" />";

print "\n\t</td>";

print "\n\t<td valign='middle' align='center' rowspan='2'>";
print helplink("index");
print "\n\t</td>";

print "\n\t</tr><tr><td valign='top' align='left'>";

print "\n\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' name='browsefiles' value=' &nbsp; Media Library &nbsp; ' onclick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&amp;editor=none&amp;site=$site&amp;comingfrom=viewsite\")' />";

print "\n\t</td>";

print "\n\t<td valign='top' align='left'>";
print "\n\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' name='sitemap' value=' &nbsp; Site Map &nbsp; &nbsp;' onclick='sendWindow(\"sitemap\",600,400,\"site_map.php?$sid&amp;site=$site\")'  />";
print "\n\t</td>";

print "\n\t<td valign='top' align='left'>";
if ($_SESSION[auser] == $site_owner) {
	print "\n\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' name='preview_as' value=' &nbsp; Preview Site As... &nbsp;' onclick='sendWindow(\"preview_as\",400,300,\"preview.php?$sid&amp;site=$site&amp;query=".urlencode($_SERVER[QUERY_STRING])."\")' />";
}
print "\n\t</td>";

print "\n\t</tr>\n</table>\n</div>";

print "\n\n<br /><div align='right'><a href='http://segue.sourceforge.net' target='_blank'><img border='0' src='themes/common/images/segue_logo_trans_solid.gif' alt='Segue Logo' /></a></div>";
$sitefooter = $sitefooter . ob_get_contents();
ob_end_clean();