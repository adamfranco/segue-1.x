<? /* $Id$ */

include("output_modules/common.inc.php");

if ($areadmin) {
	// first build list of categories
	$topnav_extra = ((permission($auser,SITE,ADD,$site))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&commingFrom=viewsite' class='btnlink' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>":"");
}

/* $sections = decode_array($siteinfo['sections']); */
$i=0;
foreach ($sections as $s) {
	$a = db_get_line("sections","id=$s");
	if ($areuser) {
		if (canview($a,SECTION)) {
			if ($a[type] == 'section') $link = "$PHPSELF?$sid&site=$site&section=$s&action=site";
			if ($a[type] == 'url') { $link = $a[url]; $target="_self";}
			$extra = '';
			$i++;
			add_link(topnav,$a['title'],$link,$extra,$s,$target);
		}
	}
	if ($areadmin) {
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
}

// next, if we have a section, build a list of leftnav items
if ($section) {
/* 	$pages = decode_array(db_get_value("sections","pages","id=$section")); */
	$i = 0;
	foreach ($pages as $p) {
		$a = db_get_line("pages","id=$p");
		$extra = '';
		if ($areuser) {
			if (canview($a,PAGE)) {
				if ($a[type] == 'page')
					add_link(leftnav,$a['title'],"$PHPSELF?$sid&site=$site&section=$section&page=$p&action=site",$extra,$p);
				if ($a[type] == 'url')
					add_link(leftnav,$a['title'],$a['url'],$extra,$p,"_blank");
				if ($a[type] == 'heading')
					add_link(leftnav,$a['title'],'',$extra);
				if ($a[type] == 'divider')
					add_link(leftnav,'','',$extra);
				$i++;
			}
		}
		if ($areadmin) {
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
	}
	if ($areadmin) $leftnav_extra = (permission($auser,SECTION,ADD,$section))?"<div align=right><nobr><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&commingFrom=viewsite' class='small' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></nobr></div>":"";
}
