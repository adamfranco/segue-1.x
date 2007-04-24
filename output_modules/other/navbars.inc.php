<? /* $Id$ */

if ($action == 'viewsite' || ereg('preview_edit_as', $action)) {
	//$topnav_extra = ($thisSite->hasPermission("add"))?" <a href='$PHP_SELF?$sid&$envvars&amp;action=add_section&amp;comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>":"";
	if ($thisSite->hasPermission("add")) {
		$topnav_extra = " <a href='$PHP_SELF?$sid&amp;$envvars&amp;action=add_section&amp;comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>";
	}
}
$i=0;

/******************************************************************************
 * print out links to sections
 ******************************************************************************/

if ($thisSite->sections) {
	foreach ($thisSite->data[sections] as $s) {
		$o =& $thisSite->sections[$s];
		if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {
			if ($o->getField("type") == 'section') $link = "$PHPSELF?$sid&amp;site=$site&amp;section=$s&amp;action=$action";
			
			if ($o->getField("type") == 'link') { 
				$url = $o->getField("url");				
				/******************************************************************************
				 * replace general media library urls (i.e. $mediapath/$sitename/filename)
				 * replace general with specific
				 ******************************************************************************/
				$url = convertTagsToInteralLinks($site, $url);

				$link = $url; $target="_self";
			}
			$extra = '';
			if (($action == 'viewsite' || ereg('preview_edit_as', $action)) && (($section == $s) || ($o->getField("type") == 'link'))) {
			
				if (!$o->getField("active")) {
					$extra = "[hidden]";
				}

				if ($thisSite->hasPermission("edit")) {
					if ($_REQUEST['showorder'] == "section") {
						$extra .= "<a href='$PHP_SELF?$sid&amp;$envvars&amp;action=viewsite&amp;showorder=0' class='btnlink' title='hide order sections in this section'>hide order</a>";
					} else {
						$extra .= "<a href='$PHP_SELF?$sid&amp;$envvars&amp;action=viewsite&amp;showorder=section' class='btnlink' title='Reorder sections in this site'>reorder</a>";
					}

					if ($i != 0) {
					
//						$extra .= " <a href='$PHP_SELF?$sid&amp;$envvars&amp;action=viewsite&amp;reorder=section&amp;direction=up&amp;id=$s' class='";
//						$extra .= (($topsections)?"btnlink":"small")."' title='Move this section ";
//						$extra .= (($topsections)?"left":"up")."'><b>".(($topsections)?"&larr;":"&uarr;")."</b></a>";
					}
					//if ($i != count($thisSite->sections)-1) $extra .= " <a href='$PHP_SELF?$sid&amp;$envvars&amp;action=viewsite&amp;reorder=section&amp;direction=down&amp;id=$s' class=".(($topsections)?"btnlink":"small")." title='Move this section ".(($topsections)?"right":"down")."'><b>".(($topsections)?"&rarr;":"&darr;")."</b></a>";
				}
				
				$extra .= ($thisSite->hasPermission("edit"))?" ".(($topsections)?"":"| ")."<a href='copy_parts.php?$sid&amp;site=$site&amp;section=$s&amp;type=section' class='".(($topsections)?"btnlink":"small")."' title='Move/Copy this section to another site' onclick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
				$extra .= ($thisSite->hasPermission("edit"))?" ".(($topsections)?"":"| ")."<a href='$PHP_SELF?$sid&amp;site=$site&amp;section=$s&amp;action=edit_section&amp;edit_section=$s&amp;comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Edit the title and properties of this section'>edit</a>":"";
				$extra .= ($thisSite->hasPermission("delete"))?" ".(($topsections)?"":"| ")."<a href='javascript:doconfirm(\"Are absolutely sure you want to PERMANENTLY DELETE this section, including anything that may be held within it?? (you better be SURE!)\",\"$PHP_SELF?$sid&amp;$envvars&amp;action=delete_section&amp;delete_section=$s\")' class='".(($topsections)?"btnlink":"small")."' title='Delete this section'>del</a>":"";
				//$extra .= (($topsections)?" ":"<hr />");
			}
			$i++;
			add_link(topnav,$o->getField("title"),$link,$extra,$s,$target);
			add_link(topnav2,$o->getField("title"),$link,$extra,$s,$target);
		}
	}
}
/******************************************************************************
 * If we have a section, build an array of page links/content (e.g. $leftnav)
 * nav array is
 ******************************************************************************/
 
if ($thisSection) {
/* 	print "thisSection found...<br />"; */
	$thisSection->fetchDown();	//just in case...
	$i = 0;
	
/******************************************************************************
 * print out links to pages
 * links compiled with functions.inc.php/add_function
 * function add_link($array,$name="",$url="",$extra='',$id=0,$target='_self',$content='')
 * $array = array of nav links (e.g. leftnav)
 * $name = $title required for page links and options for other types of pages
 * $extra = editing UI printed out only if action = viewsite and has permissions
 * $id = ? $target = target of links
 * $content = content of content and rss type pages
 ******************************************************************************/

	if ($thisSection->pages) {
		$thisSection->handlePageOrder();
		
		foreach ($thisSection->data[pages] as $p) {
			$o =& $thisSection->pages[$p];
			$extra = '';
			$content = '';
			$nextorder = 1;
			if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {
				if (($action == 'viewsite' || ereg('preview_edit_as', $action)) && ($p == $page || $o->getField("type") != 'page')) {
					
					/******************************************************************************
					 * Pages get same extras (ie edit options) regardless of navigation arrangement
					 ******************************************************************************/
					if (!indaterange($o->getField("activatedate"), $o->getField("deactivatedate"))) {
						$extra .= "<div class='small' align='left'><a href='$PHP_SELF?$sid&amp;$envvars&amp;action=edit_page&amp;step=2&amp;edit_page=$p&amp;comingFrom=viewsite'>[inactive]</a></div>";
					}
					if (!$o->getField("active")) {
						$extra .= "<div class='small' align='left'><a href='$PHP_SELF?$sid&amp;$envvars&amp;action=edit_page&amp;step=2&amp;edit_page=$p&amp;comingFrom=viewsite'>[hidden]</a></div>";
					}

					if ($thisSection->hasPermission("edit")) {
						if ($thisSection->getField("pageorder") == "custom") {
							if ($_REQUEST['showorder'] == "page") {
								$extra .= "<a href='$PHP_SELF?$sid&amp;$envvars&amp;action=viewsite&amp;showorder=0' class='small' title='HIde reorder fields in this section'>hide order</a>";
							} else {
								$extra .= "<a href='$PHP_SELF?$sid&amp;$envvars&amp;action=viewsite&amp;showorder=page' class='small' title='Reorder pages in this section'>reorder</a>";
							}
						}


//						$extra .= "<a style='cursor: pointer;' onclick=\"var orderFields = getElementsByAttribute(document.body, 'select', 'class', 'pageOrder'); for (var i = 0; i < orderFields.length; i++) {orderFields[i].style.display='inline';} this.style.display='none'; this.nextSibling.style.display='inline';\" class='small' title='Reorder pages in this section'>order</a>";
//						$extra .= "<a style='cursor: pointer; display: none' onclick=\"var orderFields = getElementsByAttribute(document.body, 'select', 'class', 'pageOrder'); for (var i = 0; i < orderFields.length; i++) {orderFields[i].style.display='none';} this.style.display='none'; this.previousSibling.style.display='inline';\" class='small' title='Hide the reorder fields'>hide order</a>";
						
					}
					// move
					$extra .= ($thisSection->hasPermission("edit"))?" | <a href='copy_parts.php?$sid&amp;site=$site&amp;section=$section&amp;page=$p&amp;type=page' class='small' title='Move/Copy this page to another section' onclick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
					// edit
					$extra .= ($thisSection->hasPermission("edit"))?" | <a href='$PHP_SELF?$sid&amp;$envvars&amp;action=edit_page&amp;edit_page=$p&amp;comingFrom=viewsite' class='small' title='Edit the name/settings for this page/link/heading/divider'>edit</a>":"";
					// delete
					$extra .= ($thisSection->hasPermission("delete"))?" | <a href='javascript:doconfirm(\"Are you sure you want to permanently delete this item and any data that may be contained within it?\",\"$PHPSELF?$sid&amp;$envvars&amp;action=delete_page&amp;delete_page=$p\")' class='small' title='Delete this page/link/heading/divider'>del</a>":"";

				}
				
				//nav reorder option list in themes/common/function.inc.php 
				//$extra .= "<option value='".$nextorder."'".(($pagelink=='current')?" selected":"").">".$story_text."...</option>\n");

				/******************************************************************************
				 * define default nav array values
				 ******************************************************************************/
				$url = $o->getField("url");
				$name = $o->getField("title");
				$type = $o->getField("type");
				$target="_self";
				$id = $p;
				$content = "";

				/******************************************************************************
				 * Page type pages (i.e. pages with content blocks)
				 ******************************************************************************/
				if ($o->getField("type") == 'page') {
					$url = "$PHPSELF?$sid&amp;site=$site&amp;section=$section&amp;page=$p&amp;action=$action";
					
				/******************************************************************************
				 * Category type pages 
				 ******************************************************************************/
				
				} else if ($o->getField("type") == 'tags') {
					$url = "'$PHPSELF?$sid&amp;site=$site&amp;section=$section&amp;page=$p&amp;action=$action'";
					//$text = $o->getField("text");
					$url = "#";
					$tags = get_tags($site,$section,$page);
					if ($tags) {
						$taglist = "<table cellspacing='3' cellpadding='0' width='100%'>";					
						foreach ($tags as $key => $value) {
								$tag = ereg_replace("_", " ", $key);
								$urltag = urlencode($key);
								$tagcount = $value;
								$taglist .= "<tr><td><div class='nav'><a href='$PHPSELF?$sid&amp;site=$site&amp;section=$section&amp;page=$p&amp;action=$action";
								$taglist .= "&amp;tag=$urltag'>";
								$taglist .= urldecode($tag)."</a> ($tagcount)</div></td></tr>";
						}
						$taglist .= "</table>";
					}
				//	printpre($tags);					
					$content = $taglist;
					
				/******************************************************************************
				 * Participant type pages 
				 ******************************************************************************/
				
				} else if ($o->getField("type") == 'participants') {
					$url = "'$PHPSELF?$sid&amp;site=$site&amp;section=$section&amp;page=$p&amp;action=$action'";
					//$text = $o->getField("text");
					$url = "#";
					$director_uname = $thisSite->getField('addedby');
					$director = db_get_value("user","user_fname","user_uname = '".addslashes($director_uname)."'");
					$editors = $thisSite->getEditors();
					$participants = getclassstudents($site);
					$director = "<div class='nav'><strong>Site Owner</strong><br />".$director." <br /></div>";
					
					if ($participants) {
						$participantslist = "<table cellspacing='3' cellpadding='0' width='100%'>";
						$participantslist .= "<tr><td><div class='nav'><strong><i>Roster</i></strong></div></td></tr>";
						foreach ($participants as $key => $value) {
							$fname = $value[fname];
							$uname = $value[uname];
							$email = $value[email];
							$urlname = urlencode($fname);
							$utype = $value[type];
							$associatedExists = associatedSiteExists($uname, $site);
							if ($associatedExists) {
							//	printpre("ok");
								$slotname = $site."-".$uname;
								$participantslist .= "<tr><td><a href='$cfg[full_uri]/sites/$slotname' target='new_window'>$fname</a>";
							} else {
								$participantslist .= "<tr><td>";
								$participantslist .= "<div class='nav'>$fname</div>";
							}
							if ($utype == "prof") {
								$participantslist .= " (instructor)</td></tr>";
							} else {
								$participantslist .= "</td></tr>";
							}
						}
						$participantslist .= "</table>";
					}
					
					if (count($editors) > 2) {
						$editorslist = "<table cellspacing='3' cellpadding='0' width='100%'>";
						$editorslist .= "<tr><td><div class='nav'><strong><i>Editors</i></strong></div></td></tr>";
						foreach ($editors as $editor) {
							if ($editor != "everyone" && $editor != "institute") {
								$fname = db_get_value("user","user_fname","user_uname = '".addslashes($editor)."'");
								$urlname = urlencode($fname);
								$editorslist .= "<tr><td><div class='nav'>$fname</div></td></tr>";
							}
						}
						$editorslist .= "</table>";
					}

				//	printpre($editors);	
				//	printpre($participants);	
					$content = $director.$participantslist.$editorslist;
				
				/******************************************************************************
				 * Link type pages
				 ******************************************************************************/
				
				} else if ($o->getField("type") == 'link') {
				
					/******************************************************************************
					 * replace general media library urls (i.e. $mediapath/$sitename/filename)
					 * replace general with specific
					 ******************************************************************************/
					$url = convertTagsToInteralLinks($site, $url);
					
				/******************************************************************************
				 * Content type pages (i.e. pages which are content
				 ******************************************************************************/
				
				} else if ($o->getField("type") == 'content') {
					$text = stripslashes(urldecode($o->getField("text")));
					$wikiResolver =& WikiResolver::instance();
					$text = $wikiResolver->parseText($text, $site, $section, $page);
					$url = "#";

					
					/******************************************************************************
					 * replace general media library urls (i.e. $mediapath/$sitename/filename)
					 * replace general with specific
					 ******************************************************************************/
					$content = convertTagsToInteralLinks($site, $text);
					//					printpre($site);
					//exit;
														
				/******************************************************************************
				 * RSS type pages
				 ******************************************************************************/

				} else if ($o->getField("type") == 'rss') {
					ob_start();
					include_once (dirname(__FILE__)."/carprss/carp.php");					
					$rss_url = $o->getField("url");
					$url = "#";

					/******************************************************************************
					 * replace general media library urls (i.e. $mediapath/$sitename/filename)
					 * replace general with specific
					 ******************************************************************************/
					$rss_url = convertTagsToInteralLinks($site, $rss_url);	
					//printpre($rss_url);
					$rss_style = "rss_titles";
					$site_match = "site=$site";
					$page_match = "page=$page";
					MyCarpConfReset($rss_style);
					
					/******************************************************************************
					 * RSS channel display options
					 * if feed from same page, don't display channel link and open items in same window
					 * if feed from another page on same site, display channel and open in same window
					 * all other feeds display channel link and open in new window
					 ******************************************************************************/

					if (ereg($site_match, $rss_url)) {					
						$carpconf['ilinktarget'] = "_self";
						CarpConf('cborder','');
						if (ereg($page_match, $rss_url)) {
							CarpConf('cborder','');
						} else {
							$carpconf['clinktarget'] = "_self";
						}
					}
					
					if (is_numeric($o->getField("archiveby"))) {
						$num_per_set = $o->getField("archiveby");
						CarpConf('maxitems',$num_per_set);						
					} else {
						CarpConf('maxitems',5);
					}
					
					
					// If we have an auser, create a cache just for them.
					if ($_SESSION['auser']) {
						CarpCacheShow($rss_url, '', 1,  $_SESSION['auser']);
					} else {					
						// If the user has a valid campus ip-address, then they are a
						// member of 'institute'.
						$ipIsInInstitute = FALSE;
						$ip = $_SERVER[REMOTE_ADDR];
						// check if our IP is in inst_ips
						if (is_array($cfg[inst_ips])) {
							foreach ($cfg[inst_ips] as $i) {
								if (ereg("^$i",$ip)) 
									$ipIsInInstitute = TRUE;
							}
						}
						
						// if we are in the institute IPs, use the institute
						// cache.
						if ($ipIsInInstitute) {
							CarpCacheShow($rss_url, '', 1, 'institute');
						}
						// If we aren't logged in or in the institute IPs, just use the
						// everyone cache.
						else {
							CarpCacheShow($rss_url);
						}
					}					
					$content = ob_get_contents();
					ob_clean();
														
				/******************************************************************************
				 * Heading type pages 
				 ******************************************************************************/	
				 
				} else if ($o->getField("type") == 'heading') {				
					$url = '';
					
				/******************************************************************************
				 * Divider type pages 
				 ******************************************************************************/				

				} else if ($o->getField("type") == 'divider') {
					$url = '';
					$name = '';
					if ($action=='viewsite' || ereg('preview_edit_as', $action)) {
						$extra = "-divider-<br />".$extra;
					} else {
						$extra = "".$extra;
					}					
				}
								
				/******************************************************************************
				 * Build page arrays based on location and navigational arrangement 
				 ******************************************************************************/				
				
				if ($o->getField("location") == 'right') {
					add_link(rightnav,$name,$url,$extra,$id,$target,$type,$content);
				} else {
					add_link(leftnav,$name,$url,$extra,$id,$target,$type,$content);
					add_link(leftnav2,$name,$url,$extra,$id,$target,$type,$content);
				}
				$content = "";
				$i++;
			}
		}
	}
	//printpre($leftnav);

	if ($action == 'viewsite' || ereg('preview_edit_as', $action)) {
		//$leftnav_extra = ($thisSection->hasPermission("add"))?"<div align='right'><span style='white-space: nowrap;'><a href='$PHP_SELF?$sid&amp;site=$site&amp;section=$section&amp;action=add_page&amp;comingFrom=viewsite' class='".(($topsections)?"small":"btnlink")."' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></span></div>":"";
		if ($thisSection->hasPermission("add")) {
			$leftnav_extra = "<div align='right'><span style='white-space: nowrap;'><a href='$PHP_SELF?$sid&amp;site=$site&amp;section=$section&amp;action=add_page&amp;comingFrom=viewsite' class='small' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></span></div>";
			$leftnav_extra .= (($topsections)?" ":"<hr />");

		}		
	}
}