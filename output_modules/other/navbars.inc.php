<? /* $Id$ */

if ($action == 'viewsite' || ereg('preview_edit_as', $action)) {
	//$topnav_extra = ($thisSite->hasPermission("add"))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>":"";
	if ($thisSite->hasPermission("add")) {
		$topnav_extra = " <a href='$PHP_SELF?$sid&$envvars&action=add_section&comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>";
	}
}
$i=0;

/******************************************************************************
 * print out links to sections
 ******************************************************************************/

if ($thisSite->sections) {
	foreach ($thisSite->data[sections] as $s) {
		$o = &$thisSite->sections[$s];
		if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {
			if ($o->getField("type") == 'section') $link = "$PHPSELF?$sid&site=$site&section=$s&action=$action";
			
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
				if ($thisSite->hasPermission("edit")) {
					if ($i != 0) $extra .= " <a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=section&direction=up&id=$s' class='".(($topsections)?"btnlink":"small")."' title='Move this section to the left'><b>".(($topsections)?"&larr;":"&uarr;")."</b></a>";
					if ($i != count($thisSite->sections)-1) $extra .= " <a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=section&direction=down&id=$s' class=".(($topsections)?"btnlink":"small")." title='Move this section to the right'><b>".(($topsections)?"&rarr;":"&darr;")."</b></a>";
				}
				$extra .= ($thisSite->hasPermission("edit"))?" ".(($topsections)?"":"| ")."<a href='copy_parts.php?$sid&site=$site&section=$s&type=section' class='".(($topsections)?"btnlink":"small")."' title='Move/Copy this section to another site' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
				$extra .= ($thisSite->hasPermission("edit"))?" ".(($topsections)?"":"| ")."<a href='$PHP_SELF?$sid&site=$site&section=$s&action=edit_section&edit_section=$s&comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Edit the title and properties of this section'>edit</a>":"";
				$extra .= ($thisSite->hasPermission("delete"))?" ".(($topsections)?"":"| ")."<a href='javascript:doconfirm(\"Are absolutely sure you want to PERMANENTLY DELETE this section, including anything that may be held within it?? (you better be SURE!)\",\"$PHP_SELF?$sid&$envvars&action=delete_section&delete_section=$s\")' class='".(($topsections)?"btnlink":"small")."' title='Delete this section'>del</a>":"";
				$extra .= (($topsections)?" ":"<hr align=right width=75%>");
			}
			$i++;
			add_link(topnav,$o->getField("title"),$link,$extra,$s,$target);
			add_link(topnav2,$o->getField("title"),$link,$extra,$s,$target);
		}
	}
}
// next, if we have a section, build a list of leftnav items
if ($thisSection) {
/* 	print "thisSection found...<BR>"; */
	$thisSection->fetchDown();	//just in case...
	$i = 0;
	
/******************************************************************************
 * print out links to pages
 ******************************************************************************/

	if ($thisSection->pages) {
		foreach ($thisSection->data[pages] as $p) {
			$o = &$thisSection->pages[$p];
			$extra = '';
			if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {
				if (($action == 'viewsite' || ereg('preview_edit_as', $action)) && ($p == $page || $o->getField("type") != 'page')) {
				
					/******************************************************************************
					 * Pages get same extras regardless of navigation arrangement
					 ******************************************************************************/

					if ($thisSection->hasPermission("edit")) {
						if ($i != 0) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=up&id=$p' class='".(($topsections)?"small":"small")."' title='Move this page/link/heading/divider up'>".(($topsections)?"&uarr;":"&uarr;")."</a>";
						if (($i != 0) && ($i != count($thisSection->pages)-1)) $extra .= " ".(($topsections)?"| ":"| ");
						if ($i != count($thisSection->pages)-1) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=down&id=$p' class='".(($topsections)?"small":"small")."' title='Move this page/link/heading/divider down'>".(($topsections)?"&darr;":"&darr;")."</a>";
						//if (count($pages)!=1) $extra .= "<BR>";
					}
					$extra .= ($thisSection->hasPermission("edit"))?" ".(($topsections)?"| ":"| ")."<a href='copy_parts.php?$sid&site=$site&section=$section&page=$p&type=page' class='".(($topsections)?"small":"small")."' title='Move/Copy this page to another section' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
					$extra .= ($thisSection->hasPermission("edit"))?" ".(($topsections)?"| ":"| ")."<a href='$PHP_SELF?$sid&$envvars&action=edit_page&edit_page=$p&comingFrom=viewsite' class='".(($topsections)?"small":"small")."' title='Edit the name/settings for this page/link/heading/divider'>edit</a>":"";
					$extra .= ($thisSection->hasPermission("delete"))?" ".(($topsections)?"| ":"| ")."<a href='javascript:doconfirm(\"Are you sure you want to permanently delete this item and any data that may be contained within it?\",\"$PHPSELF?$sid&$envvars&action=delete_page&delete_page=$p\")' class='".(($topsections || $nav_arrange == 2)?"small":"small")."' title='Delete this page/link/heading/divider'>del</a>":"";
				}

				if ($o->getField("type") == 'page') {
					add_link(leftnav,$o->getField("title"),"$PHPSELF?$sid&site=$site&section=$section&page=$p&action=$action",$extra,$p);
					add_link(leftnav2,$o->getField("title"),"$PHPSELF?$sid&site=$site&section=$section&page=$p&action=$action",$extra,$p);
				}
				if ($o->getField("type") == 'link') {
					$url = $o->getField("url");
					
					/******************************************************************************
					 * replace general media library urls (i.e. $mediapath/$sitename/filename)
					 * replace general with specific
					 ******************************************************************************/
					$url = convertTagsToInteralLinks($site, $url);
				
					add_link(leftnav,$o->getField("title"),$url,$extra,$p);
					add_link(leftnav2,$o->getField("title"),$url,$extra,$p);
				}
				if ($o->getField("type") == 'heading') {
					add_link(leftnav,$o->getField("title"),'',$extra);
					add_link(leftnav2,$o->getField("title"),'',$extra);
				}
				if ($o->getField("type") == 'divider') {
					add_link(leftnav,'','',(($action=='viewsite' || ereg('preview_edit_as', $action))?"-divider-<br>":"").$extra);
					add_link(leftnav2,'','',(($action=='viewsite' || ereg('preview_edit_as', $action))?"-divider-<br>":$extra));
				}
				$i++;
			}
		}
	}
	if ($action == 'viewsite' || ereg('preview_edit_as', $action)) {
		//$leftnav_extra = ($thisSection->hasPermission("add"))?"<div align=right><nobr><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&comingFrom=viewsite' class='".(($topsections)?"small":"btnlink")."' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></nobr></div>":"";
		if ($thisSection->hasPermission("add")) {
			$leftnav_extra = "<div align=right><nobr><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&comingFrom=viewsite' class='".(($topsections)?"small":"small")."' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></nobr></div>";
			$leftnav_extra .= (($topsections)?" ":"<hr>");

		}		
	}
}