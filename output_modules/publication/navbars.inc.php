<? /* $Id$ */

if ($_SESSION[ltype] == 'admin' && $action=='viewsite') { include("output_modules/other/navbars.inc.php"); return; }

//if ($action == 'viewsite') $topnav_extra = ($thisSite->hasPermission("add"))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>":"";

$isediting = 0;
if ($action == 'viewsite') $isediting=1;
if ($action == 'listarticles' && $thisSection->hasPermission("add and edit and delete")) $isediting=1;

// build topnav items
$_ids = array_keys($thisSite->sections);
/* print_r($_ids); */
/* print "hello?"; */
$link = "$PHP_SELF?$sid&site=$site&action=listissues";
add_link(topnav,"ISSUES",$link,$extra,'',$target);
add_link(topnav2,"ISSUES",$link,$extra,'',$target);
if (count($_ids)) {
	$s = $_ids[0];$int = &$thisSite->sections;
	$l = $_ids[count($_ids)-1];
	if ($section && $thisSection->getField("title")!="TOP") $last=&$int[$section];
	else $last = &$int[$l];
	$first = &$int[$s];
	
	/* print_r($so->pages); */
	foreach ($first->pages as $p=>$o) {
/* 		print_r($o); */
		$link = "$PHP_SELF?$sid&site=$site&section=$section&page=$p&action=$action";
		add_link(topnav,$o->getField("title"),$link,$extra,'',$target);
		add_link(topnav2,$o->getField("title"),$link,$extra,'',$target);
	}
	add_link(leftnav,"<span class=smaller><i>".strtoupper($last->getField("title"))."</i></span>");
	foreach ($last->pages as $p=>$o) {
		$link = "$PHP_SELF?$sid&site=$site&section=$section&page=$p&action=$action";
		$extra = $list = '';
		if (($author = $o->getField("url")) && $author != "http://") $extra .= "<div class='leftmargin small' align=left>by $author</div>";
		if ($isediting) {
			$list .= ($last->hasPermission("edit"))?"<a href='$PHP_SELF?$sid&action=edit_page&site=$site&section=$section&page=$p&edit_page=$p&comingFrom=$action'>edit</a>\n":"";
			$list .= ($last->hasPermission("delete"))?"<a href='$PHP_SELF?$sid&action=delete_page&site=$site&section=$section&page=$p&delete_page=$p&comingFrom=$action'>del</a>\n":"";
			if ($list != '') $extra .= "<div class=small align=right>".$list."</div>";
		}
		add_link(leftnav,$o->getField("title"),$link,$extra,$p,$target);
	}
	add_link(leftnav);
//	add_link(leftnav2);
}


$i=0;
$total=count($thisSite->sections);
if ($thisSite->sections) {
	add_link(leftnav2,"<span class=smaller>ISSUES</span>");
	foreach (array_reverse($thisSite->sections,TRUE) as $s=>$o) {
		if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {
			if ($i!=$total-1) {
				if ($o->getField("type") == 'section') $link = "$PHP_SELF?$sid&site=$site&section=$s&action=listarticles";
				if ($o->getField("type") == 'url') { $link = $o->getField("url"); $target="_self";}
				$extra = '';
				if ($isediting) {
					$extra .= ($thisSite->hasPermission("edit"))?"\n<a href='$PHP_SELF?$sid&site=$site&section=$s&action=edit_section&edit_section=$s&comingFrom=viewsite' class='small' title='Edit the title and properties of this section'>edit</a>":"";
					$extra .= ($thisSite->hasPermission("delete"))?"\n<a href='javascript:doconfirm(\"Are absolutely sure you want to PERMANENTLY DELETE this section, including anything that may be held within it?? (you better be SURE!)\",\"$PHP_SELF?$sid&$envvars&action=delete_section&delete_section=$s\")' class='small' title='Delete this section'>del</a>":"";
				}
				add_link(leftnav2,$o->getField("title"),$link,$extra,$s,$target);
			
			}
			$i++;
		}
	}
	if ($isediting) $leftnav_extra = ($thisSite->hasPermission("add"))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&comingFrom=viewsite' class='small' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add issue</a>":"";
}
// next, if we have a section, build a list of leftnav items
/*if ($thisSection) {
	$thisSection->fetchDown();	//just in case...
	$i = 0;
	if ($thisSection->pages) {
		foreach ($thisSection->pages as $p=>$o) {
			$extra = '';
			if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {
				if ($action == 'viewsite' && ($p == $page || $o->getField("type") != 'page')) {
					if ($thisSection->hasPermission("edit")) {
						if ($i != 0) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=up&id=$p' class='".(($topsections)?"small":"btnlink")."' title='Move this page/link/heading/divider up'><b>".(($topsections)?"&uarr;":"&larr;")."</b></a>";
						if (($i != 0) && ($i != count($thisSection->pages)-1)) $extra .= " ".(($topsections)?"| ":"");
						if ($i != count($thisSection->pages)-1) $extra .= "<a href='$PHP_SELF?$sid&$envvars&action=viewsite&reorder=page&direction=down&id=$p' class='".(($topsections)?"small":"btnlink")."' title='Move this page/link/heading/divider down'><b>".(($topsections)?"&darr;":"&rarr;")."</b></a>";
						//if (count($pages)!=1) $extra .= "<BR>";
					}
					$extra .= ($thisSection->hasPermission("edit"))?" ".(($topsections)?"| ":"")."<a href='copy_parts.php?$sid&site=$site&section=$section&page=$p&type=page' class='".(($topsections)?"small":"btnlink")."' title='Move/Copy this page to another section' onClick=\"doWindow('copy_parts','300','250')\" target='copy_parts'>move</a>":"";
					$extra .= ($thisSection->hasPermission("edit"))?" ".(($topsections)?"| ":"")."<a href='$PHP_SELF?$sid&$envvars&action=edit_page&edit_page=$p&comingFrom=viewsite' class='".(($topsections)?"small":"btnlink")."' title='Edit the name/settings for this page/link/heading/divider'>edit</a>":"";
					$extra .= ($thisSection->hasPermission("delete"))?" ".(($topsections)?"| ":"")."<a href='javascript:doconfirm(\"Are you sure you want to permanently delete this item and any data that may be contained within it?\",\"$PHPSELF?$sid&$envvars&action=delete_page&delete_page=$p\")' class='".(($topsections)?"small":"btnlink")."' title='Delete this page/link/heading/divider'>del</a>":"";
				}

				if ($o->getField("type") == 'page') {
					add_link(leftnav,$o->getField("title"),"$PHPSELF?$sid&site=$site&section=$section&page=$p&action=$action",$extra,$p);
					add_link(leftnav2,$o->getField("title"),"$PHPSELF?$sid&site=$site&section=$section&page=$p&action=$action",$extra,$p);
				}
				if ($o->getField("type") == 'url') {
					add_link(leftnav,$o->getField("title"),$o->getField("url"),$extra,$p,"_blank");
					add_link(leftnav2,$o->getField("title"),$o->getField("url"),$extra,$p,"_blank");
				}
				if ($o->getField("type") == 'heading') {
					add_link(leftnav,$o->getField("title"),'',$extra);
					add_link(leftnav2,$o->getField("title"),'',$extra);
				}
				if ($o->getField("type") == 'divider') {
					add_link(leftnav,'','',(($action=='viewsite')?"-divider-<br>":"").$extra);
					add_link(leftnav2,'','',(($action=='viewsite')?"-divider-<br>":$extra));
				}
				$i++;
			}
		}
	}
	if ($action == 'viewsite') $leftnav_extra = ($thisSection->hasPermission("add"))?"<div align=right><nobr><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&comingFrom=viewsite' class='".(($topsections)?"small":"btnlink")."' title='Add a new item to this section. This can be a Page that holds content, a link, a divider, or a heading.'>+ add item</a></nobr></div>":"";
}*/