<? /* $Id$ */


$siteObj =& new site($_REQUEST[site]);
$sectionObj =& new section($_REQUEST[site],$_REQUEST[section], $siteObj);
$pageObj =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], $sectionObj);
$storyObj =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], $pageObj);
$getinfo = "site=".$siteObj->name."&amp;section=".$sectionObj->id."&amp;page=".$pageObj->id."&amp;story=".$storyObj->id."&amp;detail=".$storyObj->id;
$getinfo2 = "site=".$siteObj->name."&amp;section=".$sectionObj->id."&amp;page=".$pageObj->id;
$editsettingsurl = "&amp;site=".$siteObj->name."&amp;section=".$sectionObj->id."&amp;page=".$pageObj->id."&amp;action=edit_story&amp;edit_story=".$storyObj->id."&amp;detail=".$storyObj->id."&amp;step=4&amp;goback=discuss&amp;link=1";

$storyObj->fetchFromDB();
$storyObj->owningSiteObj->fetchFromDB();
//$site_owner=slot::getOwner($story->owningSiteObj->name);
$site_owner=$storyObj->owningSiteObj->owner;
//print_r($story->owningSiteObj);
//print $site_owner;

// get the correct shorttext
if ($storyObj->getField("type") == 'story') {
	$smalltext = convertTagsToInteralLinks($siteObj->name, $storyObj->getField("shorttext"));
	$fulltext = convertTagsToInteralLinks($siteObj->name, $storyObj->getField("longertext"));
	$smalltext = stripslashes($smalltext);
	$fulltext = stripslashes($fulltext);
	if ($storyObj->getField("texttype") == 'text') $fulltext = htmlbr($fulltext);	
	if ($storyObj->getField("texttype") == 'text') $smalltext = htmlbr($smalltext);
}

if ($storyObj->getField("type") == 'image') {
	$filename = urldecode(db_get_value("media","media_tag","media_id='".addslashes($storyObj->getField("longertext"))."'"));
	$dir = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id='".addslashes($storyObj->getField("longertext"))."'");
	$imagepath = "$uploadurl/$dir/$filename";
	$fulltext = "\n<div style='text-align: center'><br /><img src='$imagepath' border='0' /></div>";
/* 	if ($story->getField("title")) $fulltext .= "<tr><td align='center'><b>".spchars($story->getField("title"))."</b></td></tr>"; */
	if ($storyObj->getField("shorttext")) {
		$captiontext = $st = convertTagsToInteralLinks($site, $storyObj->getField("shorttext"));
		$captiontext = "<br />".stripslashes($captiontext);
	}
	$fulltext .= "";
}
if ($storyObj->getField("type") == 'file') {
	$fulltext = "<br />";
	$fulltext .= makedownloadbar($storyObj);
}

/******************************************************************************
 * print out shory and discussion (if any)
 ******************************************************************************/
// require_once("htmleditor/editor.inc.php");
if ($storyObj->getField("discuss")) $titleExtra = " Discussion";

printc("\n<table width='100%' id='maintable' cellspacing='1'>");

printc($pagePagination);
if ($_REQUEST['selversions']) {
	$_SESSION['selversions'] = $_REQUEST['selversions'];
}


//printpre($_SESSION['selversions']);
//printpre($_REQUEST['selversions']);

/******************************************************************************
 * print out title and options
 ******************************************************************************/
 
printc("<tr><td align='left' class=title>");
printc("<a href=index.php?action=site&".$getinfo2.">".spchars($pageObj->getField('title'))."</a>");

if ($storyObj->getField('title')) {
	printc(" > ".spchars($storyObj->getField('title')));
}

if ($_REQUEST['selversions']) {
	$_SESSION['selversions'] = $_REQUEST['selversions'];
	//printpre($_SESSION['selversions']);
	printc(" > <a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
	printc(" > Selected Versions");
	
} else if ($_REQUEST['versioning']) {
	printc(" > All Versions");

/******************************************************************************
 * if particular version then get version details
 ******************************************************************************/
	
} else if ($_REQUEST['version']) {
	$version = get_versions($storyObj->id, $_REQUEST['version']);
	$version_id = $version[0]['version_id'];
	$version_num = $version[0]['version_order'];
	$smalltext = urldecode($version[0]['version_text_short']);
	$fulltext = urldecode($version[0]['version_text_long']);
	$version_date = $version[0]['version_created_tstamp'];
	$version_author = $version[0]['FK_createdby'];
	printc(" > <a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
	printc(" > Selected Versions");
	
} else {
	printc(" > in depth");
}
printc("</td></tr>\n");


/******************************************************************************
 * if selected versions request, then print out selected versions to compare
 ******************************************************************************/
	
if ($_REQUEST['selversions']) {

	$version01 = get_versions($story, $_REQUEST['selversions'][0]);
	$version01_num = $version01[0]['version_order'];
	$version02 = get_versions($story, $_REQUEST['selversions'][1]);
	$version02_num = $version02[0]['version_order'];
//	printpre($version01);
	//printpre($version02);	
	printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>");
	printc("<table width='100%' cellpadding='3'>");
	printc("<tr>\n");
	printc("<td>");
	printc("<strong><a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;version=$version01_num'>Revision ".$version01_num."</a></strong> ");
	printc("(".$version01[0]['version_created_tstamp']." - ".$version01[0]['FK_createdby'].")\n");
	printc("</td>");
	printc("<td>");
	printc("<strong><a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;version=$version02_num'>Revision ".$version02_num."</a></strong> ");
	printc("(".$version02[0]['version_created_tstamp']." - ".$version02[0]['FK_createdby'].")\n");
	printc("</td>");
	printc("</tr>\n");
	printc("<tr>\n");
	printc("<td width='50%' valign='top' style='border: 1px dotted #CCC;'>".$version01[0]['version_text_short']."</td>\n");
	printc("<td width='50%' valign='top'  style='border: 1px dotted #CCC;'>".$version02[0]['version_text_short']."</td>\n");
	printc("</tr>\n");
	printc("<tr>\n");
	printc("<td style='border: 1px dotted #CCCCCC;'>".$version01[0]['version_text_long']."</td>\n");
	printc("<td style='border: 1px dotted #CCCCCC;'>".$version02[0]['version_text_long']."</td>\n");
	printc("</tr>\n");
	printc("</table>");
	printc("</tr>\n");

/******************************************************************************
 * if versioning then then show list of versions with date, version author
 ******************************************************************************/

} else if ($_REQUEST['versioning']) {
	$u = "$PHP_SELF?$sid&amp;action=site&site=$site&section=$section&page=$page&story=$story&versioning=1";
	printc("<form action=$u method='post'>");
	printc("<tr><td>");
	// compare selected versions button (top)
	printc("<br \><button type='submit' class='button' value='compare' onClick=\"window.location='$u'\">Compare selected revisions</button><br /><br />");
	printc("<table cellspacing='3' width='100%'>\n");
	$versions = get_versions($storyObj->id);
	//printpre($versions);	
	
	printc("<tr><th colspan='2'>Select</th><th>Revision</th><th>Revision Date</th><th>Revision Author</th></tr>\n");
		
	$color = 0;
	$i = 0;
	foreach ($versions as $version) {
		$version_id = $version['version_id'];
		$version_num = $version['version_order'];
		
		printc("<tr>\n");
		printc("<td class=ts$color align='right'>");
		
		if ($i > 0) {
			printc("<input type='radio' name='oldversion' value='".$version_num."' ");
			
			if (isset($_SESSION['oldversion'])) {
				if ($_SESSION['oldversion'] == $version_num)
					printc(" checked='checked'");
			} else {
				if ($i == 1)
					printc(" checked='checked'");
			}
			
			printc(" />");
		}
		
		printc("</td>\n<td class=ts$color align='left'>");
		
		if ($i < count($versions) - 1) {
			printc("<input type='radio' name='newversion' value='".$version_num."' ");
			
			if (isset($_SESSION['newversion'])) {
				if ($_SESSION['newversion'] == $version_num)
					printc(" checked='checked'");
			} else {
				if (!$i)
					printc(" checked='checked'");
			}
			
			printc(" />");
		}

		printc("</td>");
		printc("<td class=ts$color><a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;version=$version_num'>Revision $version_num</a></td>");
		printc("<td class=ts$color>".$version['version_created_tstamp']."</td>");
		printc("<td class=ts$color>".$version['FK_createdby']."</td>\n");
		printc("</tr>\n");
		
		$color = 1-$color;
		$i++;
	}	

	printc("</table>\n");
	// compare selected versions button (bottom)
	printc("<br /><button type='submit' class='button' value='compare'>Compare selected revisions</button><br /><br /> ");
	printc("</form>");
	printc("</td></tr>");


/******************************************************************************
 * if no versioning or selected versions then print a single version
 ******************************************************************************/

} else {

	/******************************************************************************
	 * if a particular version specified print out
	 ******************************************************************************/
	
	// Revert to this version link (top location)
	if ($_REQUEST['version']  && $storyObj->hasPermission("edit")) {
		printc("<tr><td>");
		printc("<br \><table width='100%' cellspacing='0'><tr><td>");
		printc("<strong>Revision ".$version_num."</strong> (".$version_date." - ".$version_author.")");
		printc("</td><td align='right'>");
		// revert to this version link (top)
		printc("<a class='btnlink2' href='index.php?$sid&amp;action=edit_story&amp;site=$site&amp;section=$section&amp;page=$page&amp;edit_story=$story&amp;version=$version_num&amp;comingFrom=viewsite'>Revert to this Version</a>\n");
		printc("</td></td></table><br \>");
		printc("</td></tr>\n");
		printc("<tr><td width='100%' valign='top' style='border: 1px dotted #CCCCCC;'>$smalltext</td></tr>\n");
		printc("<tr><td width='100%' valign='top' style='border: 1px dotted #CCCCCC;'>$fulltext</td></tr>\n");

	/******************************************************************************
	 * if no version specified print out current version
	 ******************************************************************************/
	} else {
						
		if ($storyObj->getField('type') != "image") printc("<tr><td align='left'><strong>".(($storyObj->getField('title'))?spchars($storyObj->getField('title')):'&nbsp;')."</strong></td></tr>\n");
		
		$record_id = $story;
		$user_id = $_SESSION[aid];
		$record_type = "story";
		$story_tags = get_record_tags($site,$record_id,$user_id, $record_type);
		//printpre($story_tags);
		
		if (isset($story_tags)) {
			printc("<tr><td align='left'><div class='contentinfo' id='contentinfo2' align='left'>\n");
			printc("Categories:");
			foreach ($story_tags as $tag) {
				$urltag = urlencode($tag);
				$tagname = urldecode($tag);
				printc("<a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;tag=$urltag'>".$tagname."</a>\n");
			}
			printc("\n");
			printc("</div></td></tr>\n\n");
		}
	
		
			printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$smalltext</td></tr>\n");
			printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$fulltext</td></tr>\n");
		}
		
		// Revert to this version link (bottom location)
		if ($_REQUEST['version']  && $storyObj->hasPermission("edit")) {
			printc("<tr><td align='center'><br \>");
			printc("<a class='btnlink2' href='index.php?$sid&amp;action=edit_story&amp;site=$site&amp;section=$section&amp;page=$page&amp;edit_story=$story&amp;version=$version_num&amp;comingFrom=viewsite'>Revert to this Version</a><br \><br \>\n");
			printc("</td></tr>\n");
		}
	
		if ($storyObj->getField('type') == "image") {
			printc("<tr><td align='center' font-size: 12px'><strong>".spchars($storyObj->getField('title'))."</strong></td></tr>\n");
			printc("<tr><td font-size: 12px'>$captiontext</td></tr>\n");
		}
}

/******************************************************************************
 * print out title and options
 ******************************************************************************/
 
printc("<tr><td align='left'");
printc("<a href=index.php?action=site&".$getinfo2.">".spchars($pageObj->getField('title'))."</a>");

if ($storyObj->getField('title')) {
	printc(" > ".spchars($storyObj->getField('title')));
}

if ($_REQUEST['selversions']) {
	//printpre($_SESSION['selversions']);
	printc(" > <a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
	printc(" > Selected Versions");
	
} else if ($_REQUEST['versioning']) {
	printc(" > All Versions");

/******************************************************************************
 * if particular version then get version details
 ******************************************************************************/
 
} else if ($_REQUEST['version']) {
	printc(" > <a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
	printc(" > Revision ".$version_num);
	
} else {
	printc(" > in depth");
}
printc("</td></tr>\n");


		printc("<table>");
printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<br /><a href='index.php?action=site&amp;".$getinfo2."'>".spchars($pageObj->getField('title'))."</a> &gt; in depth</td>\n\t</tr>");
printc("</table>\n");


?>