<? /* $Id$ */

/* print "hi"; */
/* if ($thisSite) print "hello"; */

if ($thisSite) $site=$thisSite->name;
if ($thisSection) $section=$thisSection->id;
if ($thisPage) $page = $thisPage->id;

if ($thisSite) {
	if (!$thisSection && count($thisSite->getField("sections"))) {
		$thisSite->fetchDown();
		foreach ($thisSite->sections as $s=>$o) {
			if ($o->getField("type") == 'section' && ($o->canview() || $o->hasPermissionDown("add or edit or delete"))) { $thisSection = &$thisSite->sections[$s]; break; }
		}
	}
/* 	print count($thisSite->sections); */
	$sitetype = $thisSite->getField("type");
}
if ($thisSection) {
	if (!$thisPage && count($thisSection->getField("pages"))) {
		$thisSection->fetchDown();
		foreach ($thisSection->pages as $p=>$o) {
			if ($o->getField("type") == 'page' && ($o->canview() || $o->hasPermissionDown("add or edit or delete"))) { $thisPage = &$thisSection->pages[$p]; break; }
		}
	}
	$st = " > " . $thisSection->getField("title");
	// check category permissions
}
if ($thisPage) {		// we're viewing a page
	$pt = " > " . $thisPage->getField("title");
	// check page permissions
}
$pagetitle = $thisSite->getField("title") . $st . $pt;

if (!$thisSite->isEditor()) {
	error("You are not an editor for this site.");
	return;
}

// check for proper instance of scripts
if ($allowclasssites != $allowpersonalsites) {
	$type = $thisSite->getField("type");
	if ($allowclasssites && !$allowpersonalsites) {
		if ($type == 'personal')
			header("Location: $personalsitesurl/index.php?action=viewsite&site=$site&section=$section&page=&page");
	} else if (!$allowclasssites && $allowpersonalsites) {
		if ($type != 'personal' && $type != 'system')
			header("Location: $classsitesurl/index.php?action=viewsitesite=$site&section=$section&page=&page");
	}
}

// we are reordering either pages or sections (or stories?)
if ($_REQUEST[reorder]) {
	if ($_REQUEST[reorder] == 'page' && $thisSection->hasPermission("edit")) {
		$thisSection->setField("pages",reorder($thisSection->getField("pages"),$_REQUEST[id],$_REQUEST[direction]));
		$thisSection->updateDB();
		$thisSection->fetcheddown=0;
		$thisSection->fetchDown();
	}
	if ($_REQUEST[reorder] == 'section' && $thisSite->hasPermission("edit")) {
		$thisSite->setField("sections",reorder($thisSite->getField("sections"), $_REQUEST[id],$_REQUEST[direction]));
		$thisSite->updateDB();
		$thisSite->fetcheddown=0;
		$thisSite->fetchDown();
	}
	if ($_REQUEST[reorder] == 'story' && $thisPage->hasPermission("edit")) {
		$thisPage->setField("stories",reorder($thisPage->getField("stories"),$_REQUEST[id],$_REQUEST[direction]));
		$thisPage->updateDB();
		$thisPage->fetcheddown=0;
		$thisPage->fetchDown();
	}
}


$envvars = "site=".$thisSite->name;
if ($thisSection) $envvars .= "&section=".$thisSection->id;
if ($thisPage) $envvars .= "&page=".$thisPage->id;

$site=$thisSite->name;
$section=$thisSection->id;
$page=$thisPage->id;
$thisSite->fetchDown();			// just in case we haven't already

$topsections = ((ereg("Top\+Sections{1}",$thisSite->getField("themesettings")) || $thisSite->getField("themesettings") == "" || !$thisSite->getField("themesettings"))?1:0);

// first build list of categories
$topnav_extra = ($thisSite->hasPermission("add"))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>":"";


$i=0;
if ($thisSite->sections) {
	foreach ($thisSite->sections as $id=>$s) {
		if ($s->getField("type") == 'section') $link = "$PHP_SELF?$sid&site=$site&section=$id&action=viewsite";
		if ($s->getField("type") == 'url') { $link = $s->getField("url"); $target="_blank";}
		$extra = '';
		if (($section == $id) || ($s->getField("type") == 'url')) {
			if ($thisSite->hasPermission("edit")) {
				if ($i != 0) $extra .= " <a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=section&direction=up&id=$id' class='".(($topsections)?"btnlink":"small")."' title='Move this section to the left'>".(($topsections)?"&larr;":"&uarr;")."</a>";
				if ($i != count($thisSite->sections)-1) $extra .= " <a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=section&direction=down&id=$id' class=".(($topsections)?"btnlink":"small")." title='Move this section to the right'>".(($topsections)?"&rarr;":"&darr;")."</a>";
			}
			$extra .= ($thisSite->hasPermission("edit"))?" ".(($topsections)?"":"| ")."<a href='copy_parts.php?$sid&site=$site&section=$id&type=section' class='".(($topsections)?"btnlink":"small")."' title='Move/Copy this section to another site' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
			$extra .= ($thisSite->hasPermission("edit"))?" ".(($topsections)?"":"| ")."<a href='$PHP_SELF?$sid&site=$site&section=$id&action=edit_section&edit_section=$id&comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Edit the title and properties of this section'>edit</a>":"";
			$extra .= ($thisSite->hasPermission("delete"))?" ".(($topsections)?"":"| ")."<a href='javascript:doconfirm(\"Are absolutely sure you want to PERMANENTLY DELETE this section, including anything that may be held within it?? (you better be SURE!)\",\"$PHP_SELF?$sid&$envvars&action=delete_section&delete_section=$id\")' class='".(($topsections)?"btnlink":"small")."' title='Delete this section'>del</a>":"";
		}
		$i++;
		if ($s->canview() || $s->hasPermissionDown("add or edit or delete")) {
			add_link(topnav,$s->getField("title"),$link,$extra,$id,$target);
			add_link(topnav2,$s->getField("title"),$link,"",$id,$target);
		}	
	}
}

// next, if we have a section, build a list of leftnav items
if ($thisSection) {
	$thisSection->fetchDown();
	$i = 0;
	if ($thisSection->pages) {
		foreach ($thisSection->pages as $id=>$p) {
			$extra = '';
			if ($id == $page || $p->getField("type") != 'page') {
				if ($thisSection->hasPermission("edit")) {
					if ($i != 0) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=up&id=$id' class='".(($topsections)?"small":"btnlink")."' title='Move this page/link/heading/divider up'><b>".(($topsections)?"&uarr;":"&larr;")."</b></a>";
					if (($i != 0) && ($i != count($thisSection->pages)-1)) $extra .= " ".(($topsections)?"| ":"");
					if ($i != count($thisSection->pages)-1) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=down&id=$id' class='".(($topsections)?"small":"btnlink")."' title='Move this page/link/heading/divider down'><b>".(($topsections)?"&darr;":"&rarr;")."</b></a>";
					//if (count($pages)!=1) $extra .= "<BR>";
				}
				$extra .= ($thisSection->hasPermission("edit"))?" ".(($topsections)?"| ":"")."<a href='copy_parts.php?$sid&site=$site&section=$section&page=$id&type=page' class='".(($topsections)?"small":"btnlink")."' title='Move/Copy this page to another section' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
				$extra .= ($thisSection->hasPermission("edit"))?" ".(($topsections)?"| ":"")."<a href='$PHP_SELF?$sid&$envvars&action=edit_page&edit_page=$id&comingFrom=viewsite' class='".(($topsections)?"small":"btnlink")."' title='Edit the name/settings for this page/link/heading/divider'>edit</a>":"";
				$extra .= ($thisSection->hasPermission("delete"))?" ".(($topsections)?"| ":"")."<a href='javascript:doconfirm(\"Are you sure you want to permanently delete this item and any data that may be contained within it?\",\"$PHPSELF?$sid&$envvars&action=delete_page&delete_page=$id\")' class='".(($topsections)?"small":"btnlink")."' title='Delete this page/link/heading/divider'>del</a>":"";
			}
			$i++;
			if ($p->canview() || $p->hasPermissionDown("add or edit or delete")) {
				if ($p->getField("type") == 'page') {
					add_link(leftnav,$p->getField("title"),"$PHPSELF?$sid&site=$site&section=$section&page=$id&action=viewsite",$extra,$id);
					add_link(leftnav2,$p->getField("title"),"$PHPSELF?$sid&site=$site&section=$section&page=$id&action=viewsite","",$id);
				
				}
				if ($p->getField("type") == 'url') {
					add_link(leftnav,$p->getField("title")." <img src=globe.gif border=0 align=absmiddle height=15 width=15>",$p->getField("url"),$extra,$id,"_blank");
					add_link(leftnav2,$p->getField("title")." <img src=globe.gif border=0 align=absmiddle height=15 width=15>",$p->getField("url"),"",$id,"_blank");
				}
				if ($p->getField("type") == 'heading') {
					add_link(leftnav,$p->getField("title"),'',$extra);
					add_link(leftnav2,$p->getField("title"),'','');
				}
				if ($p->getField("type") == 'divider') {
					add_link(leftnav,'','',"-divider-<br>".$extra);
					add_link(leftnav2,'','',"-divider-<br>");
				}
			}
		}
	}
	$leftnav_extra = ($thisSection->hasPermission("add"))?"<div align=right><nobr><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&comingFrom=viewsite' class='".(($topsections)?"small":"btnlink")."' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></nobr></div>":"";
}

if ($thisPage) {
	$thisPage->fetchDown();
	if ($thisPage->canview() || $thisPage->hasPermissionDown("add or edit or delete")) {
		printc("<div class=title>".$thisPage->getField("title")."</div>");
	}
	
	// handle ordering of stories
/* 	if ($thisPage->getField("storyorder") != 'custom' && $thisPage->getField("storyorder") != '') */
/* 		$stories = handlestoryorder($stories,$pageinfo[storyorder]); */
	$thisPage->handleStoryOrder();
	
	$_top_addlink_orders = array("addeddesc","editeddesc","author","editor","category","titleasc","titledesc");
	if ($thisPage->hasPermission("add") && in_array($thisPage->getField("storyorder"),$_top_addlink_orders)) 
		printc("<br><div align=right><a href='$PHP_SELF?$sid&$envvars&action=add_story&comingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div><br><hr class=block>");
	
	$i=0;
	if ($thisPage->stories) {
		foreach ($thisPage->stories as $s=>$o) {
	/* 		$a = db_get_line("stories","id=$s"); */
			if ($o->canview() || $thisPage->hasPermissionDown("add or edit or delete")) {
				if ($i!=0)
					printc("<hr class=block style='margin-top: 10px'>");
					
				if ($o->getField("category")) {
					printc("<div class=contentinfo id=contentinfo2 align=right>");
					printc("Category: <b>".spchars($o->getField("category"))."</b>");
					printc("</div>");
				}
				$incfile = "output_modules/".$thisSite->getField("type")."/".$o->getField("type").".inc.php";
/* 				print "<br>".$incfile; */
				include($incfile);
				
				if ($thisPage->getField("showcreator") || $thisPage->getField("showdate")) {
					printc("<div class=contentinfo align=right>");
					$added = datetime2usdate($o->getField("addedtimestamp"));
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
				
				printc("<div align=right>");
	//			$s1=$s2=NULL;
				$l = array();
				if (($_SESSION[auser] == $site_owner) || (($_SESSION[auser] != $site_owner) && !$o->getField("locked"))) {
					if (($thisPage->getField("archiveby") == '' || $thisPage->getField("archiveby") == 'none' || !$thisPage->getField("archiveby")) && $thisPage->hasPermission("edit")) {
						if ($i!=0 && ($thisPage->getField("storyorder") == 'custom' || $thisPage->getField("storyorder") == ''))$l[] = "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=story&direction=up&id=$s' class=small title='Move this Content Block up'><b>&uarr;</b></a>";
						if ($i!=count($thisPage->stories)-1 && ($thisPage->getField("storyorder") == 'custom' || $thisPage->getField("storyorder") == '')) $l[] = "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=story&direction=down&id=$s' class=small title='Move this Content Block down'><b>&darr;</b></a>";
					}
					if ($thisPage->hasPermission("edit") || $o->hasPermission("edit")) $l[]="<a href='copy_parts.php?$sid&site=$site&section=$section&page=$page&story=$s&type=story' class='small' title='Move/Copy this Content Block to another page' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>";
					if ($thisPage->hasPermission("edit") || $o->hasPermission("edit")) $l[]="<a href='$PHP_SELF?$sid&$envvars&action=edit_story&edit_story=$s&comingFrom=viewsite' class='small' title='Edit this Content Block'>edit</a>";
					if ($thisPage->hasPermission("delete") || $o->hasPermission("delete")) $l[]="<a href='javascript:doconfirm(\"Are you sure you want to delete this content?\",\"$PHP_SELF?$sid&$envvars&action=delete_story&delete_story=$s\")' class=small title='Delete this Content Block'>delete</a>";
				}
				printc(implode(" | ",$l));
				printc("</div>");
				$i++;
			}
		}
	}
	$_b = array("","custom","addedasc","editedasc");
	if ($thisPage->hasPermission("add") && in_array($thisPage->getField("storyorder"),$_b)) printc("<br><hr class=block><div align=right><a href='$PHP_SELF?$sid&$envvars&action=add_story&comingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div>");
}


// add the key to the footer of the page
/*$u = "$_SERVER[SCRIPT_URI]?action=site&site=$site";*/
$u = "$PHP_SELF?$sid&action=site&site=$site";
if ($section) $u .= "&section=$section";
if ($page) $u .= "&page=$page";
$text .= "<div align=right><table><tr>";
$text .= "<td><form method=post action='$u&$sid'><input type=submit value='Preview This Site'></form></td>";
$text .= "<td><form action='site_map.php?$sid&site=$site' onClick='doWindow(\"sitemap\",600,400)' target='sitemap' method=post><input type=submit value=' &nbsp; Site Map &nbsp;'></form></td>";
if ($thisSite->hasPermission("edit")) $text .= "</tr><tr><td><form action='$PHP_SELF?$sid&action=edit_site&edit_site=$site&comingFrom=viewsite' method=post><input type=submit value='Edit Site Settings'></form></td>";
else $text .= "</tr><tr><td> &nbsp; </td>";
$text .= "<td><form action='edit_permissions.php?$sid&site=$site' onClick='doWindow(\"permissions\",600,400)' target='permissions' method=post><input type=submit value='Permissions'></form></td>";
$text .= "</tr><tr>";
$text .= "<td><form action='$PHP_SELF?$sid&action=site&site=sample' target='_blank' method=post><input type=submit value='View Sample Site'></form></td>";
$text .= "</tr></table></div>";
$sitefooter = $sitefooter . $text;