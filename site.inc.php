<? // site.inc.php	-- allows logged in people to view a site

$siteinfo = db_get_line("sites","name='$site'");
$site_owner = $siteinfo[addedby];

// check view permissions
siteviewpermissions($siteinfo);

// check for proper instance of scripts
if ($allowclasssites != $allowpersonalsites) {
	$type = db_get_value("sites","type","name='$site'");
	if ($allowclasssites && !$allowpersonalsites) {
		if ($type == 'personal')
			header("Location: $personalsitesurl/index.php?action=site&site=$site&section=$section&page=&page");
	} else if (!$allowclasssites && $allowpersonalsites) {
		if ($type != 'personal' && $type != 'system')
			header("Location: $classsitesurl/index.php?action=site&site=$site&section=$section&page=&page");
	} else {
		// Do nothing
	}
}

// if we're an admin, override all errors
if ($ltype == 'admin') {
	clearerror();
}

// if we produced an error, return (don't let them view the site)
if ($error) return;

if ($site) {
//	$query = "select * from sites where id=$site";
	$siteinfo = db_get_line("sites","name='$site'");
	$sections = decode_array($siteinfo['sections']);
	if (!$section && count($sections)) {
		for ($i=0; $i<count($sections) && !$section; $i++) {
			$a = db_get_line("sections","id=$sections[$i]");
			if ($a[type]=='section' && canview($a,SECTION))$section=$sections[$i];
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
			$a = db_get_line("pages","id=$pages[$i]");
			if ($a[type] == 'page' && canview($a,PAGE)) $page = $pages[$i];
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



$envvars = "site=$site";
if ($section) $envvars .= "&section=$section";
if ($page) $envvars .= "&page=$page";

/* $sections = decode_array($siteinfo['sections']); */
$i=0;
foreach ($sections as $s) {
	$a = db_get_line("sections","id=$s");
	if (canview($a,SECTION)) {
		if ($a[type] == 'section') $link = "$PHPSELF?$sid&site=$site&section=$s&action=site";
		if ($a[type] == 'url') { $link = $a[url]; $target="_self";}
		$extra = '';
		$i++;
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
}

if ($page) {
/* 	$stories = decode_array(db_get_value("pages","stories","id=$page")); */
	printc("<div class=title>$pageinfo[title]</div>");
	$i=0;	
	// handle archiving -- monthly, weekly, etc
	if ($pageinfo[archiveby] != 'none' && $pageinfo[archiveby] != '')
		$stories = handlearchive($stories,$pageinfo[archiveby]);
	// handle ordering of stories
	if ($pageinfo[storyorder] != 'custom' && $pageinfo[storyorder] != '')
		$stories = handlestoryorder($stories,$pageinfo[storyorder]);
	
	foreach ($stories as $s) {
	
		$a = db_get_line("stories","id=$s");
		if (canview($a,STORY)) {		
			if (($pageinfo[showcreator] || $pageinfo[showdate] || $pageinfo[showhr]) && $i!=0) 
					printc("<hr size='1' noshade style='margin-top: 10px'>");
			if ($a[category]) {
				printc("<div class=contentinfo id=contentinfo2 align=right>");
				printc("Category: <b>".spchars($a[category])."</b>");
				printc("</div>");
			}
					
			printc("<div style='margin-bottom: 10px'>");
			
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
			}
			printc("</div>");
		}
		$i++;
	}
}

// add the key to the footer of the page
if (is_editor($auser,$site) && !$themepreview) {
	/*$u = "$_SERVER[SCRIPT_URI]?action=viewsite&site=$site";*/
	$u = "$PHP_SELF?$sid&action=viewsite&site=$site";
	if ($section) $u .= "&section=$section";
	if ($page) $u .= "&page=$page";
	$text .= " <div align=right><form method=post action='$u&$sid'><input type=submit value='edit this site'></form></div>";
	$sitefooter = $sitefooter . $text;
}