<? // viewsite.inc.php	-- allows logged in people to view a site
 
if ($settings) session_unregister("settings");

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
		$extra .= (permission($auser,SITE,EDIT,$site))?" <a href='$PHPSELF?$sid&$envvars&action=edit_section&edit_section=$s&commingFrom=viewsite' class='btnlink' title='Edit the title and properties of this section'>edit</a>":"";
		$extra .= (permission($auser,SITE,DELETE,$site))?" <a href='javascript:doconfirm(\"Are absolutely sure you want to PERMANENTLY DELETE this section, including anything that may be held within it?? (you better be SURE!)\",\"$PHPSELF?$sid&$envvars&action=delete_section&delete_section=$s\")' class='btnlink' title='Delete this section'>del</a>":"";
	}
	$i++;
	add_link(topnav,$a['title'],$link,$extra,$s,$target);
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
			$extra .= (permission($auser,SECTION,EDIT,$section))?" | <a href='$PHP_SELF?$sid&$envvars&action=edit_page&edit_page=$p&commingFrom=viewsite' class='small' title='Edit the name/settings for this page/link/heading/divider'>edit</a>":"";
			$extra .= (permission($auser,SECTION,DELETE,$section))?" | <a href='javascript:doconfirm(\"Are you sure you want to permanently delete this item and any data that may be contained within it?\",\"$PHPSELF?$sid&$envvars&action=delete_page&delete_page=$p\")' class='small' title='Delete this page/link/heading/divider'>del</a>":"";
		}
		$i++;
		if ($a[type] == 'page')
			add_link(leftnav,$a['title'],"$PHPSELF?$sid&site=$site&section=$section&page=$p&action=viewsite",$extra,$p);
		if ($a[type] == 'url')
			add_link(leftnav,$a['title']." <img src=globe.gif border=0 align=absmiddle height=15 width=15>",$a['url'],$extra,$p,"_blank");
		if ($a[type] == 'heading')
			add_link(leftnav,$a['title'],'',$extra);
		if ($a[type] == 'divider')
			add_link(leftnav,'','',"-divider-<br>".$extra);
	}
	$leftnav_extra = (permission($auser,SECTION,ADD,$section))?"<div align=right><nobr><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&commingFrom=viewsite' class='small' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></nobr></div>":"";
}

if ($page) {
/* 	$stories = decode_array(db_get_value("pages","stories","id=$page")); */
	printc("<div class=title>$pageinfo[title]</div>");
	$i=0;
	foreach ($stories as $s) {
		$a = db_get_line("stories","id=$s");
		if ($a[type] == 'story' || $a[type]=='') {
			if ($a[title]) printc("<div class=leftmargin><b>".spchars($a[title])."</b></div>");
			$st = urldecode($a['shorttext']);
			if ($a[texttype] == 'text') $st = htmlbr($st);
			if ($a[category]) {
				printc("<div class=contentinfo align=right>");
				printc("Category: <b>".spchars($a[category])."</b>");
				printc("</div>");
			}
			printc("<div>" . stripslashes($st) . "</div>");
			if ($a[discuss] || $a[longertext]) {
				printc("<div class=contentinfo align=right>");
				$link = "fullstory.php?$sid&action=fullstory&site=$site&section=$section&page=$page&story=$s";
				$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
				$l = array();
				if ($a[discuss]) $l[] = $link."discussions</a>";
				if ($a[longertext]) $l[] = $link."full text</a>";
				printc(implode(" | ",$l));
				printc("</div>");
			}
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
		}
		if ($a[type]=='image') {
			$imagepath = "$uploadurl/$site/$a[id]/".urldecode($a[longertext]);
			printc("<table align=center><tr><td align=center><img src='$imagepath' border=0></td></td>");
			if ($a[title]) printc("<tr><td align=center><b>".spchars($a[title])."</b></td></tr>");
			if ($a[shorttext]) printc("<tr><td align=left>".stripslashes(urldecode($a[shorttext]))."</td></tr>");
			printc("</table>");
		}
		if ($a[type]=='file') {
			$t = makedownloadbar($a);
			printc($t);
		}
		if ($a[type]=='link') {
			if ($a[title]) printc("<div class=leftmargin><b>".spchars($a[title])."</b></div>");
			printc("<div><a href='$a[url]' target='_blank'>$a[url]</a></div>");
			if ($a[shorttext]) printc("<div class=desc>".stripslashes(urldecode($a[shorttext]))."</div>");
		}
		printc("<div align=right>");
//		$s1=$s2=NULL;
		$l = array();
		if (($auser == $site_owner) || (($auser != $site_owner) && !$a[locked])) {
			if (($pageinfo[archiveby] == '' || $pageinfo[archiveby] == 'none' || !$pageinfo[archiveby]) && permission($auser, PAGE,EDIT,$page)) {
				if ($i!=0)$l[] = "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=story&direction=up&id=$s' class=small title='Move this Content Bloc********k up'><b>&uarr;</b></a>";
				if ($i!=count($stories)-1) $l[] = "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=story&direction=down&id=$s' class=small title='Move this Content Block down'><b>&darr;</b></a>";
			}
			$i++;
			if (permission($auser,PAGE,EDIT,$page)) $l[]="<a href='$PHP_SELF?$sid&$envvars&action=edit_story&edit_story=$s&commingFrom=viewsite' class='small' title='Edit this Content Block'>edit</a>";
			if (permission($auser,PAGE,DELETE,$page)) $l[]="<a href='javascript:doconfirm(\"Are you sure you want to delete this content?\",\"$PHP_SELF?$sid&$envvars&action=delete_story&delete_story=$s\")' class=small title='Delete this Content Block'>delete</a><hr class=block>";
		}
		printc(implode(" | ",$l));
		printc("</div>");
	}
	if (permission($auser,PAGE,ADD,$page)) printc("<br><div align=right><a href='$PHP_SELF?$sid&$envvars&action=add_story&commingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div>");
}


// add the key to the footer of the page
/*$u = "$_SERVER[SCRIPT_URI]?action=site&site=$site";*/
$u = "$PHP_SELF?$sid&action=site&site=$site";
if ($section) $u .= "&section=$section";
if ($page) $u .= "&page=$page";
$text .= "<div align=right><table><tr>";
$text .= "<td><form method=post action='$u&$sid'><input type=submit value='preview this site'></form></td>";
$text .= "<td><form action='$PHP_SELF?$sid&action=site&site=sample' target='_blank' method=post><input type=submit value='view sample site'></form></td>";
if (permission($auser,SITE,EDIT,$section)) $text .= "</tr><tr><td><form action='$PHP_SELF?$sid&action=edit_site&edit_site=$site&commingFrom=viewsite' method=post><input type=submit value='edit site settings'></form></td>";
if (permission($auser,SITE,EDIT,$section)) $text .= "<td><form action='editor_access.php?$sid&site=$site' onClick='doWindow(\"permissions\",600,400)' target='permissions' method=post><input type=submit value='&nbsp; &nbsp;permissions&nbsp; &nbsp;'></form></td>";
$text .= "</tr></table></div>";
$sitefooter = $sitefooter . $text;