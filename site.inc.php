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
			header("Location: $personalsitesurl/index.php?action=site&site=$site");
	} else if (!$allowclasssites && $allowpersonalsites) {
		if ($type != 'personal' && $type != 'system')
			header("Location: $classsitesurl/index.php?action=site&site=$site");
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
			printc("<div style='margin-bottom: 4px'>");
			if ($a[type]=='story' || $a[type]=='') {
			
				$st = urldecode($a['shorttext']);
				$st = str_replace("src='####","####",$st);
				$st = str_replace("####'","####",$st);
				$textarray1 = explode("####", $st);
				if (count($textarray1) > 1) {
					for ($i=1; $i<count($textarray1); $i=$i+2) {
						$id = $textarray1[$i];
						$filename = urldecode(db_get_value("media","name","id=$id"));
						$userdir = db_get_value("media","site_id","id=$id");
						$filepath = $uploadurl."/".$userdir."/".$filename;
						$textarray1[$i] = "src='".$filepath."'";
					}		
					$st = implode("",$textarray1);
				}
				
				if ($a[title]) printc("<div class=leftmargin><b>".spchars($a[title])."</b></div>");
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
					printc("<hr size='1' noshade><br>");
				}
			}
			if ($a[type]=='image') {
				$filename = urldecode(db_get_value("media","name","id=$a[longertext]"));
				$dir = db_get_value("media","site_id","id=$a[longertext]");
				$imagepath = "$uploadurl/$dir/$filename";
				printc("<table align=center><tr><td align=center><img src='$imagepath' border=0></td></td>");
				if ($a[title]) printc("<tr><td align=center><b>".spchars($a[title])."</b></td></tr>");
				if ($a[shorttext]) printc("<tr><td align=left>".stripslashes(urldecode($a[shorttext]))."</td></tr>");
				printc("</table>");

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
					printc("<hr size='1' noshade><br>");
				}
			}
			if ($a[type]=='file') {
				$t = makedownloadbar($a);
				printc($t);

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
					printc("<hr size='1' noshade><br>");
				}
			}
			if ($a[type]=='link') {
				if ($a[title]) printc("<div class=leftmargin><b>".spchars($a[title])."</b></div>");
				printc("<div><a href='$a[url]' target='_blank'>$a[url]</a></div>");
				if ($a[shorttext]) printc("<div class=desc>".stripslashes(urldecode($a[shorttext]))."</div>");

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
					printc("<hr size='1' noshade><br>");
				}
			}
			printc("</div>");
		}
	}
}

// add the key to the footer of the page
if (is_editor($auser,$site)) {
	/*$u = "$_SERVER[SCRIPT_URI]?action=viewsite&site=$site";*/
	$u = "$PHP_SELF?$sid&action=viewsite&site=$site";
	if ($section) $u .= "&section=$section";
	if ($page) $u .= "&page=$page";
	$text .= " <div align=right><form method=post action='$u&$sid'><input type=submit value='edit this site'></form></div>";
	$sitefooter = $sitefooter . $text;
}