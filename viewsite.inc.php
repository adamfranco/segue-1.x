<? /* $Id$ */

/* print "hi"; */
/* if ($thisSite) print "hello"; */

if ($thisSite) $site=$thisSite->name;
if ($thisSection) $section=$thisSection->id;
if ($thisPage) $page = $thisPage->id;

do {
	// for publication sites
	if ($supplement = $_REQUEST[supplement]) {
		if ($thisSite->getField("type")=='publication' && ($supplement == 'listarticles' || $supplement == 'listissues')) {
			include("$supplement.inc.php");
			break;
		}
	}
	
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
	
	$topsections = !ereg("Side\+Sections",$thisSite->getField("themesettings"));
	/* print "themsettings: \"".$thisSite->getField("themesettings")."\"<br>"; */
	/* print $topsections; */
	
	// build the navbar
	include ("output_modules/".$thisSite->getField("type")."/navbars.inc.php");
	
	
	
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
} while (0);


// add the key to the footer of the page
/*$u = "$_SERVER[SCRIPT_URI]?action=site&site=$site";*/
$u = "$PHP_SELF?$sid&action=site&site=$site";
if ($section) $u .= "&section=$section";
if ($page) $u .= "&page=$page";
if ($supplement) $u .="&supplement=$supplement";

$text .= "<div align=right><table><tr>";
$text .= "<td valign=top align=right><input type=button value='Preview This Site'  onClick=\"window.location='$u&$sid'\"></td>";
$text .= "<td valign=top align=left><input type=button name='sitemap' value=' &nbsp; Site Map &nbsp; ' onClick='sendWindow(\"sitemap\",600,400,\"site_map.php?$sid&site=$site\")' target='sitemap' style='text-decoration: none'></td>";
if ($thisSite->hasPermission("edit")) $text .= "</tr><tr><td valign=top align=right><input type=button value='Edit Site Settings' onClick=\"window.location='index.php?$sid&action=edit_site&edit_site=$site&comingFrom=viewsite'\"></td>";
else $text .= "</tr><tr><td> &nbsp; </td>";
$text .= "<td valign=top align=left><input type=button name='sitemap' value=' Permissions ' onClick='sendWindow(\"permissions\",600,400,\"edit_permissions.php?$sid&site=$site\")' target='permissions' style='text-decoration: none'></td>";
$text .= "</tr><tr>";
$text .= "<td valign=top align=right><input type=button name='sample' value='View Sample Site' onClick='sendWindow(\"sample\",\"\",\"\",\"index.php?$sid&action=site&site=sample\")' target='sample' style='text-decoration: none'></td>";
$text .= "<td valign=top align=left><input type=button name='browsefiles' value='Media Library' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=none&site=$site&comingfrom=viewsite\")' target='filebrowser' style='text-decoration: none'></td>";
$text .= "</tr></table></div>";
$sitefooter = $sitefooter . $text;