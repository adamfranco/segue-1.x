<? /* $Id$ */
 
// session variable reset has moved to index.php
		

$site_owner = db_get_value("sites","addedby","name='$site'");
if ($site) {
//	$query = "select * from sites where id=$site";
	$siteinfo = db_get_line("sites","name='$site'");
	$sections = decode_array($siteinfo['sections']);
	if (!$section && count($sections)) {
		for ($i=0; $i<count($sections) && !$section; $i++) {
			if (db_get_value("sections","type","id=$sections[$i]")=='section')$section=$sections[$i];
		}
	}
	$sitetype = $siteinfo['type'];
	// once sites are objects this will no longer be needed -- the check can be done within the object
	if (!$sitetype || $sitetype=='') $sitetype = "personal";
}
if ($section) {
//	$query = "select * from sections where id=$category";
	$sectioninfo = db_get_line("sections","id=$section");
	$st = " > " . $sectioninfo['title'];
	$pages = decode_array($sectioninfo['pages']);
	if (!$page && count($pages)) {
		for ($i=0;$i<count($pages) && !$page;$i++) {
			if (db_get_value("pages","type","id=$pages[$i]") == 'page') $page = $pages[$i];
		}
	}
	// check category permissions
}
if ($page) {		// we're viewing a page
//	$query = "select * from pages where id=$page";
	$pageinfo = db_get_line("pages","id=$page");
	$stories = decode_array($pageinfo[stories]);
	$pt = " > " . $pageinfo['title'];
	// check page permissions
}
$pagetitle = $siteinfo['title'] . $st . $pt;

if (!is_editor($auser,$site)) {
	error("You are not an editor for this site.");
	return;
}

// check for proper instance of scripts
if ($allowclasssites != $allowpersonalsites) {
	$type = db_get_value("sites","type","name='$site'");
	if ($allowclasssites && !$allowpersonalsites) {
		if ($type == 'personal')
			header("Location: $personalsitesurl/index.php?action=viewsite&site=$site&section=$section&page=&page");
	} else if (!$allowclasssites && $allowpersonalsites) {
		if ($type != 'personal' && $type != 'system')
			header("Location: $classsitesurl/index.php?action=viewsitesite=$site&section=$section&page=&page");
	} else {
		// Do nothing
	}
}

// we are reordering either pages or sections (or stories?)
if ($reorder) {
	if ($reorder == 'page' && permission($auser,SECTION,EDIT,$section)) {
		$pages = reorder($pages,$id,$direction);
		$query = "update sections set pages='".encode_array($pages)."' where id=$section";
		db_query($query);
	}
	if ($reorder == 'section' && permission($auser,SITE,EDIT,$site)) {
		$sections = reorder($sections, $id,$direction);
		$query = "update sites set sections='".encode_array($sections)."' where name='$site'";
		db_query($query);
	}
	if ($reorder == 'story' && permission($auser,PAGE,EDIT,$page)) {
		$stories = reorder($stories,$id,$direction);
		$query = "update pages set stories='".encode_array($stories)."' where id=$page";
		db_query($query);
	}
}


$envvars = "site=$site";
if ($section) $envvars .= "&section=$section";
if ($page) $envvars .= "&page=$page";

// first build list of categories
$topnav_extra = ((permission($auser,SITE,ADD,$site))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&commingFrom=viewsite' class='btnlink' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>":"");

/* $sections = decode_array($siteinfo['sections']); */
$i=0;
foreach ($sections as $s) {
	$a = db_get_line("sections","id=$s");
	if ($a[type] == 'section') $link = "$PHPSELF?$sid&site=$site&section=$s&action=viewsite";
	if ($a[type] == 'url') { $link = $a[url]; $target="_blank";}
	$extra = '';
	if (($section == $s) || ($a[type] == 'url')) {
		if (permission($auser,SITE,EDIT,$site)) {
			if ($i != 0) $extra .= " <a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=section&direction=up&id=$s' class=btnlink title='Move this section to the left'>&larr;</a>";
			if ($i != count($sections)-1) $extra .= " <a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=section&direction=down&id=$s' class=btnlink title='Move this section to the right'>&rarr;</a>";
		}
		$extra .= (permission($auser,SITE,EDIT,$site))?" <a href='copy_parts.php?$sid&site=$site&section=$s&type=section' class='btnlink' title='Move/Copy this section to another site' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
		$extra .= (permission($auser,SITE,EDIT,$site))?" <a href='$PHPSELF?$sid&site=$site&section=$s&action=edit_section&edit_section=$s&commingFrom=viewsite' class='btnlink' title='Edit the title and properties of this section'>edit</a>":"";
		$extra .= (permission($auser,SITE,DELETE,$site))?" <a href='javascript:doconfirm(\"Are absolutely sure you want to PERMANENTLY DELETE this section, including anything that may be held within it?? (you better be SURE!)\",\"$PHPSELF?$sid&$envvars&action=delete_section&delete_section=$s\")' class='btnlink' title='Delete this section'>del</a>":"";
	}
	$i++;
	if ($a[active] || has_permissions($auser,SECTION,$site,$s,"","")) {
		add_link(topnav,$a['title'],$link,$extra,$s,$target);
	}	
}

// next, if we have a section, build a list of leftnav items
if ($section) {
/* 	$pages = decode_array(db_get_value("sections","pages","id=$section")); */
	$i = 0;
	foreach ($pages as $p) {
		$a = db_get_line("pages","id=$p");
		$extra = '';
		if ($p == $page || $a[type] != 'page') {
			if (permission($auser,SECTION,EDIT,$section)) {
				if ($i != 0) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=up&id=$p' class=small title='Move this page/link/heading/divider up'><b>&uarr;</b></a>";
				if (($i != 0) && ($i != count($pages)-1)) $extra .= " | ";
				if ($i != count($pages)-1) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=down&id=$p' class=small title='Move this page/link/heading/divider down'><b>&darr;</b></a>";
				//if (count($pages)!=1) $extra .= "<BR>";
			}
			$extra .= (permission($auser,SECTION,EDIT,$section))?" | <a href='copy_parts.php?$sid&site=$site&section=$section&page=$p&type=page' class='small' title='Move/Copy this page to another section' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
			$extra .= (permission($auser,SECTION,EDIT,$section))?" | <a href='$PHP_SELF?$sid&$envvars&action=edit_page&edit_page=$p&commingFrom=viewsite' class='small' title='Edit the name/settings for this page/link/heading/divider'>edit</a>":"";
			$extra .= (permission($auser,SECTION,DELETE,$section))?" | <a href='javascript:doconfirm(\"Are you sure you want to permanently delete this item and any data that may be contained within it?\",\"$PHPSELF?$sid&$envvars&action=delete_page&delete_page=$p\")' class='small' title='Delete this page/link/heading/divider'>del</a>":"";
		}
		$i++;
		if ($a[active] || has_permissions($auser,PAGE,$site,$section,$p,"")) {
			if ($a[type] == 'page')
				add_link(leftnav,$a['title'],"$PHPSELF?$sid&site=$site&section=$section&page=$p&action=viewsite",$extra,$p);
			if ($a[type] == 'url')
				add_link(leftnav,$a['title']." <img src=globe.gif border=0 align=absmiddle height=15 width=15>",$a['url'],$extra,$p,"_blank");
			if ($a[type] == 'heading')
				add_link(leftnav,$a['title'],'',$extra);
			if ($a[type] == 'divider')
				add_link(leftnav,'','',"-divider-<br>".$extra);
		}
	}
	$leftnav_extra = (permission($auser,SECTION,ADD,$section))?"<div align=right><nobr><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&commingFrom=viewsite' class='small' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></nobr></div>":"";
}

if ($page) {
/* 	$stories = decode_array(db_get_value("pages","stories","id=$page")); */
	if (db_get_value("pages","active","id=$page") || has_permissions($auser,PAGE,$site,$section,$page,"")) {
		printc("<div class=title>$pageinfo[title]</div>");
	}
	
	// handle ordering of stories
	if ($pageinfo[storyorder] != 'custom' && $pageinfo[storyorder] != '')
		$stories = handlestoryorder($stories,$pageinfo[storyorder]);
		
	if (permission($auser,PAGE,ADD,$page) && ($pageinfo[storyorder] == 'addeddesc' || $pageinfo[storyorder] == 'editeddesc' || $pageinfo[storyorder] == 'author' || $pageinfo[storyorder] == 'editor' || $pageinfo[storyorder] == 'category' || $pageinfo[storyorder] == 'titleasc' || $pageinfo[storyorder] == 'titledesc')) 
	printc("<br><div align=right><a href='$PHP_SELF?$sid&$envvars&action=add_story&commingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div><br><hr class=block>");
	
	$i=0;
	foreach ($stories as $s) {
		$a = db_get_line("stories","id=$s");
		if ($a[active] || has_permissions($auser,STORY,$site,$section,$page,$s)) {
			if ($i!=0)
				printc("<hr class=block style='margin-top: 10px'>");
				
			if ($a[category]) {
				printc("<div class=contentinfo id=contentinfo2 align=right>");
				printc("Category: <b>".spchars($a[category])."</b>");
				printc("</div>");
			}
		
			$incfile = "output_modules/$sitetype/$a[type].inc.php";
			//print $incfile; // debug
			include($incfile);
			
			if ($pageinfo[showcreator] || $pageinfo[showdate]) {
				printc("<div class=contentinfo align=right>");
				$added = datetime2usdate($a[addedtimestamp]);
				printc("added");
				if ($pageinfo[showcreator]) printc(" by $a[addedby]");
				if ($pageinfo[showdate]) printc(" on $added");
				if ($a[editedby]) {
					printc(", edited");
					if ($pageinfo[showcreator]) printc(" by $a[editedby]");
					if ($pageinfo[showdate]) printc(" on ".timestamp2usdate($a[editedtimestamp]));
				}
				printc("</div>");
				//printc("<hr size='1' noshade><br>");
			}
			
			printc("<div align=right>");
//			$s1=$s2=NULL;
			$l = array();
			if (($auser == $site_owner) || (($auser != $site_owner) && !$a[locked])) {
				if (($pageinfo[archiveby] == '' || $pageinfo[archiveby] == 'none' || !$pageinfo[archiveby]) && permission($auser, PAGE,EDIT,$page)) {
					if ($i!=0 && ($pageinfo[storyorder] == 'custom' || $pageinfo[storyorder] == ''))$l[] = "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=story&direction=up&id=$s' class=small title='Move this Content Block up'><b>&uarr;</b></a>";
					if ($i!=count($stories)-1 && ($pageinfo[storyorder] == 'custom' || $pageinfo[storyorder] == '')) $l[] = "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=story&direction=down&id=$s' class=small title='Move this Content Block down'><b>&darr;</b></a>";
				}
				if (permission($auser,PAGE,EDIT,$page) || permission($auser,STORY,EDIT,$s)) $l[]="<a href='copy_parts.php?$sid&site=$site&section=$section&page=$page&story=$s&type=story' class='small' title='Move/Copy this Content Block to another page' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>";
				if (permission($auser,PAGE,EDIT,$page) || permission($auser,STORY,EDIT,$s)) $l[]="<a href='$PHP_SELF?$sid&$envvars&action=edit_story&edit_story=$s&commingFrom=viewsite' class='small' title='Edit this Content Block'>edit</a>";
				if (permission($auser,PAGE,DELETE,$page) || permission($auser,STORY,DELETE,$s)) $l[]="<a href='javascript:doconfirm(\"Are you sure you want to delete this content?\",\"$PHP_SELF?$sid&$envvars&action=delete_story&delete_story=$s\")' class=small title='Delete this Content Block'>delete</a>";
			}
			printc(implode(" | ",$l));
			printc("</div>");
			$i++;
		}
	}
	if (permission($auser,PAGE,ADD,$page) && ($pageinfo[storyorder] == '' || $pageinfo[storyorder] == 'custom' || $pageinfo[storyorder] == 'addedasc' || $pageinfo[storyorder] == 'editedasc')) printc("<br><hr class=block><div align=right><a href='$PHP_SELF?$sid&$envvars&action=add_story&commingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div>");
}


// add the key to the footer of the page
/*$u = "$_SERVER[SCRIPT_URI]?action=site&site=$site";*/
$u = "$PHP_SELF?$sid&action=site&site=$site";
if ($section) $u .= "&section=$section";
if ($page) $u .= "&page=$page";
$text .= "<div align=right><table><tr>";
$text .= "<td><form method=post action='$u&$sid'><input type=submit value='Preview This Site'></form></td>";
$text .= "<td><form action='$PHP_SELF?$sid&action=site&site=sample' target='_blank' method=post><input type=submit value='View Sample Site'></form></td>";
if (permission($auser,SITE,EDIT,$site)) $text .= "</tr><tr><td><form action='$PHP_SELF?$sid&action=edit_site&edit_site=$site&commingFrom=viewsite' method=post><input type=submit value='Edit Site Settings'></form></td>";
else $text .= "</tr><tr><td> &nbsp; </td>";
$text .= "<td><form action='editor_access.php?$sid&site=$site' onClick='doWindow(\"permissions\",600,400)' target='permissions' method=post><input type=submit value='View Permissions'></form></td>";
$text .= "</tr></table></div>";
$sitefooter = $sitefooter . $text;