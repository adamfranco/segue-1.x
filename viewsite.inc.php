<? /* $Id$ */

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
			foreach (array_keys($thisSite->sections) as $k=>$s) {
				$o =& $thisSite->sections[$s];
				if ($o->getField("type") == 'section' && $o->canview()) { $thisSection =& $o; break; }
			}
		}
	/* 	print count($thisSite->sections); */
		$sitetype = $thisSite->getField("type");
	}
	unset($o);
	
	if ($thisSection) {
		if (!$thisPage && count($thisSection->getField("pages"))) {
			$thisSection->fetchDown();
			foreach (array_keys($thisSection->pages) as $k=>$p) {
				$o =& $thisSection->pages[$p];
				if ($o->getField("type") == 'page' && $o->canview()) { $thisPage =& $o; break; }
			}
		}
		$st = " > " . $thisSection->getField("title");
		// check category permissions
	}
	unset($o);

	if ($thisPage) {		// we're viewing a page
		$pt = " > " . $thisPage->getField("title");
		// check page permissions
	}
	$pagetitle = $previewTitle . $thisSite->getField("title") . $st . $pt;
	
	if (!$thisSite->isEditor()
		|| !$thisSite->hasPermissionDown("add || edit || delete")) 
	{
		error("You do not have permission to edit this site.");
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
			foreach(array_keys($thisSection->pages) as $k=>$id)
				$thisSection->pages[$id]->changed[order] = 1;
			$thisSection->updateDB(1);
//			$thisSection->fetcheddown=0;
//			$thisSection->fetchDown();
		}
		if ($_REQUEST[reorder] == 'section' && $thisSite->hasPermission("edit")) {
/* 			echo "<pre>"; */
/* 			print_r ($thisSite->getField("sections")); */
			$thisSite->setField("sections",reorder($thisSite->getField("sections"), $_REQUEST[id],$_REQUEST[direction]));
/* 			print_r ($thisSite->getField("sections")); */
	
			foreach(array_keys($thisSite->sections) as $k=>$id)
				$thisSite->sections[$id]->changed[order] = 1;

			$thisSite->updateDB(1);
			$section_id = $page_id = 0;
			if ($thisSection)
				$section_id = $thisSection->id;
			if ($thisPage)
				$page_id = $thisPage->id;
			$thisSite->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen($_REQUEST[section],$_REQUEST[page]);
			if ($thisSection)
				$thisSection =& $thisSite->sections[$section_id];
			if ($thisPage)
				$thisPage =& $thisSite->sections[$section_id]->pages[$page_id];

/* 			print_r($thisSite); */
				
//			$thisSite->fetcheddown=0;
//			$thisSite->fetchDown();
		}
		if ($_REQUEST[reorder] == 'story' && $thisPage->hasPermission("edit")) {
//			print_r($thisPage->getfield("stories"));
			$thisPage->setField("stories",reorder($thisPage->getField("stories"),$_REQUEST[id],$_REQUEST[direction]));
//			print_r($thisPage->getfield("stories"));
			foreach(array_keys($thisPage->stories) as $k=>$id)
				$thisPage->stories[$id]->changed[order] = 1;
			$thisPage->updateDB(1);
//			$thisPage->fetcheddown=0;
//			$thisPage->fetchDown();
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
		if ($thisPage->canview()) {
			printc("<div class=title>".$thisPage->getField("title")."</div>");
		}
		
		// handle ordering of stories
	/* 	if ($thisPage->getField("storyorder") != 'custom' && $thisPage->getField("storyorder") != '') */
	/* 		$stories = handlestoryorder($stories,$pageinfo[storyorder]); */
		$thisPage->handleStoryOrder();
		
		$_top_addlink_orders = array("addeddesc","editeddesc","author","editor","category","titleasc","titledesc");
		if ($thisPage->hasPermission("add") && in_array($thisPage->getField("storyorder"),$_top_addlink_orders)) 
			printc("<br><div align='right'><a href='$PHP_SELF?$sid&$envvars&action=add_story&comingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div><br><hr class=block>");
		
		$i=0;
		
		/******************************************************************************
		 * Stories: 
		 ******************************************************************************/
		
		if ($thisPage->stories) {
			foreach ($thisPage->data[stories] as $s) {
				$o =& $thisPage->stories[$s];
		/* 		$a = db_get_line("stories","id=$s"); */
				if ($o->canview()) {
					if ($i!=0)
						printc("<hr class=block style='margin-top: 10px'>");
						
					if ($o->getField("category")) {
						printc("<div class=contentinfo id=contentinfo2 align='right'>");
						printc("Category: <b>".spchars($o->getField("category"))."</b>");
						printc("</div>");
					}
					$incfile = "output_modules/".$thisSite->getField("type")."/".$o->getField("type").".inc.php";
	/* 				print "<br>".$incfile; */
					include($incfile);
					
					/******************************************************************************
					 * author, editor, timestamp info
					 ******************************************************************************/
					
					if ($thisPage->getField("showcreator") || $thisPage->getField("showdate")) {
						printc("<div class=contentinfo align='right'>");
						$added = timestamp2usdate($o->getField("addedtimestamp"));
						printc("added");
						if ($thisPage->getField("showcreator")) printc(" by ".$o->getField("addedbyfull"));
						if ($thisPage->getField("showdate")) printc(" on $added");
						if ($o->getField("editedby") && !($o->getField("editedby") == $o->getField("addedby") && $o->getField("addedtimestamp") == $o->getField("editedtimestamp"))) {
							printc(", edited");
							if ($thisPage->getField("showcreator")) printc(" by ".$o->getField("editedbyfull"));
							if ($thisPage->getField("showdate")) printc(" on ".timestamp2usdate($o->getField("editedtimestamp")));
						}
						printc("</div>");
						//printc("<hr size='1' noshade><br>");
					}
					
					printc("<div align='right'>");
		//			$s1=$s2=NULL;
		
					/******************************************************************************
					 * edit, delete options 
					 ******************************************************************************/

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
				unset($o);
			}
		}
		$_b = array("","custom","addedasc","editedasc");
		if ($thisPage->hasPermission("add") && in_array($thisPage->getField("storyorder"),$_b)) printc("<br><hr class=block><div align='right'><a href='$PHP_SELF?$sid&$envvars&action=add_story&comingFrom=viewsite' class='small' title='Add a new Content Block. This can be text, an image, a file for download, or a link.'>+ add content</a></div>");
	}
} while (0);


/******************************************************************************
 * bottom button box
 ******************************************************************************/

if (ereg('preview_edit_as', $_REQUEST['action'])) {
	$previewAction = ereg_replace('preview_edit_as', '&action=preview_as', $_REQUEST['action']);
 } else {
	$previewAction = '&action=site';
}
// add the key to the footer of the page
/*$u = "$_SERVER[SCRIPT_URI]?action=site&site=$site";*/
$u = "$PHP_SELF?$sid".$previewAction."&site=$site";
if ($section) $u .= "&section=$section";
if ($page) $u .= "&page=$page";
if ($supplement) $u .="&supplement=$supplement";


ob_start();
print "\n\n<br>\n<div align='right'>\n<table style='border-top: 2px solid #666; border-left: 2px solid #666; border-bottom: 2px solid #666; border-right: 2px solid #666; background-color: #ddd;'>\n\t<tr>";
print "\n\t<td valign=top align='left'>";

$btnw = 125 . "px"; // button width
$sty = "style='width: $btnw;"; // ignore this
if ($thisSite->hasPermission("edit")) {
	print "\n\t<input type=button style='width: $btnw' class='button' value='Edit Site Settings' onClick=\"window.location='index.php?$sid&action=edit_site&sitename=$site&comingFrom=viewsite'\">";
} else {
	print "\n\t&nbsp; ";
}

print "\n\t</td>";
print "\n\t<td valign=top align='left'>";

if (!ereg('preview_edit_as', $_REQUEST['action'])) {
	print "\n\t<input type=button style='width: $btnw' class='button' name='sitemap' value=' Permissions ' onClick='sendWindow(\"permissions\",600,400,\"edit_permissions.php?$sid&site=$site\")' target='permissions' style='text-decoration: none'>";
}

print "\n\t</td>";
print "\n\t<td valign=top align='left'>";

print "\n\t<input type=button style='width: $btnw' class='button' value='View This Site'  onClick=\"window.location='$u&$sid'\">";

print "\n\t</td>";

print "\n\t<td valign='center' align='center' rowspan=2>";
print helplink("index");
print "\n\t</td>";

print "\n\t</tr><tr><td valign=top align='left'>";

print "\n\t<input type=button style='width: $btnw' class='button' name='browsefiles' value=' &nbsp; Media Library &nbsp; ' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=none&site=$site&comingfrom=viewsite\")' target='filebrowser' style='text-decoration: none'>";

print "\n\t</td>";

print "\n\t<td valign=top align='left'>";
print "\n\t<input type=button style='width: $btnw' class='button' name='sitemap' value=' &nbsp; Site Map &nbsp; &nbsp;' onClick='sendWindow(\"sitemap\",600,400,\"site_map.php?$sid&site=$site\")' target='sitemap' style='text-decoration: none'>";
print "\n\t</td>";

print "\n\t<td valign=top align='left'>";
if ($_SESSION[auser] == $site_owner) {
	print "\n\t<input type=button style='width: $btnw' class='button' name='preview_as' value=' &nbsp; Preview Site As... &nbsp;' onClick='sendWindow(\"preview_as\",400,300,\"preview.php?$sid&site=$site&query=".urlencode($_SERVER[QUERY_STRING])."\")' target='preview_as' style='text-decoration: none'>";
}
print "\n\t</td>";

print "\n\t</tr>\n</table>\n</div>";

print "\n\n<br><div align='right'><a href='http://segue.sourceforge.net' target='_blank'><img border=0 src=$cfg[themesdir]/common/images/segue_logo_trans_solid.gif></a></div>";
$sitefooter = $sitefooter . ob_get_contents();
ob_end_clean();