<? /* $Id$ */

if ($_REQUEST['action'] == 'viewsite') 
	$action = 'viewsite';
else
	$action = 'site';

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

printc("\n<table width='100%' id='maintable' cellspacing='1'>");

printc($pagePagination);

/******************************************************************************
 * print out title and options
 ******************************************************************************/
 
printc("<tr><td align='left' class='title'>");
printc("<a href='index.php?action=".$action."&amp;".$getinfo2."'>".spchars($pageObj->getField('title'))."</a>");

if ($storyObj->getField('title')) {
	printc(" > ".spchars($storyObj->getField('title')));
}

/******************************************************************************
 * if request is to revert, then create new version based on revert version number
 ******************************************************************************/
if ($_REQUEST['revert']) {

	$version = get_versions($storyObj->id, $_REQUEST['revert']);
	$version_num = $version[0]['version_order'];

	$version_short = stripslashes(urldecode($version[0]['version_text_short']));
	$version_long = stripslashes(urldecode($version[0]['version_text_long']));
	$story_id = $story;
	$version_comments = "reverted to revision ".$_REQUEST['revert'];
	
	save_version ($version_short, $version_long, $story_id, $version_comments);
}


if ($_REQUEST['oldversion'] && $_REQUEST['newversion']) {
	if (!isset($_SESSION['oldversion']) || !is_array($_SESSION['oldversion']))
		$_SESSION['oldversion'] = array();
	if (!isset($_SESSION['newversion']) || !is_array($_SESSION['newversion']))
		$_SESSION['newversion'] = array();
		
	$_SESSION['oldversion'][$_REQUEST['story']] = $_REQUEST['oldversion'];
	$_SESSION['newversion'][$_REQUEST['story']] = $_REQUEST['newversion'];
		
	printc(" > <a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
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
	$version_date = $version[0]['version_created_tstamp'];
	$version_author = $version[0]['FK_createdby'];
	
	//convert internal links
	$smalltext = convertTagsToInteralLinks($siteObj->name, stripslashes(urldecode($version[0]['version_text_short'])));
	$fulltext = convertTagsToInteralLinks($siteObj->name, stripslashes(urldecode($version[0]['version_text_long'])));
	$smalltext = urldecode($smalltext);
	$fulltext = urldecode($fulltext);
		
	printc(" > <a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
	printc(" > Selected Versions");
	
	printc("<tr><td>");
	printc("<br /><table width='100%' cellspacing='0'><tr><td class='list'>");
	printc("<strong>Revision ".$version_num."</strong> (".$version_date." - ".$version_author.")");
	printc("</td><td align='right'>");
	
	// revert to this version link (top)
	if ($storyObj->hasPermission("edit")) {
		printc("<a class='btnlink2' href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;revert=$version_num&amp;versioning=$story&amp;comingFrom=viewsite'>Revert to this Version</a>\n");		
	}
	
	printc("</td></td></table><br />");
	printc("</td></tr>\n");
	printc("<tr><td width='100%' valign='top' style='border: 1px dotted #CCCCCC;'>$smalltext</td></tr>\n");
	printc("<tr><td width='100%' valign='top' style='border: 1px dotted #CCCCCC;'>$fulltext</td></tr>\n");

	
} else {
	printc(" > in depth");
}
printc("</td></tr>\n");

// printpre($_REQUEST);
/******************************************************************************
 * if selected versions request, then print out selected versions to compare
 ******************************************************************************/
	
if ($_REQUEST['oldversion'] && $_REQUEST['newversion']) {

	$version01 = get_versions($story, $_REQUEST['oldversion']);
	$version01_num = $version01[0]['version_order'];
	$version02 = get_versions($story, $_REQUEST['newversion']);
	$version02_num = $version02[0]['version_order'];
	$smalltext01 = convertTagsToInteralLinks($siteObj->name, stripslashes(urldecode($version01[0]['version_text_short'])));
	$smalltext02 = convertTagsToInteralLinks($siteObj->name, stripslashes(urldecode($version02[0]['version_text_short'])));	
	$fulltext01 = convertTagsToInteralLinks($siteObj->name, stripslashes(urldecode($version01[0]['version_text_long'])));
	$fulltext02 = convertTagsToInteralLinks($siteObj->name, stripslashes(urldecode($version02[0]['version_text_long'])));
	
//	printpre($version01);
	//printpre($version02);	
	printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>");
	printc("<table width='100%' cellpadding='3'>");
	printc("<tr>\n");
	printc("<td>");
	printc("<strong><a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;version=$version01_num'>Revision ".$version01_num."</a></strong> ");
	printc("(".$version01[0]['version_created_tstamp']." - ".$version01[0]['FK_createdby'].")\n");
	printc("</td>");
	printc("<td>");
	printc("<strong><a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;version=$version02_num'>Revision ".$version02_num."</a></strong> ");
	printc("(".$version02[0]['version_created_tstamp']." - ".$version02[0]['FK_createdby'].")\n");
	printc("</td>");
	printc("</tr>\n");
	printc("<tr>\n");
	printc("<td width='50%' valign='top' style='border: 1px dotted #CCC;'>".$smalltext01."</td>\n");
	printc("<td width='50%' valign='top'  style='border: 1px dotted #CCC;'>".$smalltext02."</td>\n");
	printc("</tr>\n");
	printc("<tr>\n");
	printc("<td style='border: 1px dotted #CCCCCC;'>".$fulltext01."</td>\n");
	printc("<td style='border: 1px dotted #CCCCCC;'>".$fulltext02."</td>\n");
	printc("</tr>\n");
	printc("</table>");
	printc("</tr>\n");


/******************************************************************************
 * if versioning then then show list of versions with date, version author
 ******************************************************************************/

} else if ($_REQUEST['versioning']) {
	printc("\n<tr>\n\t<td>\n\t\t");
	$u = "$PHP_SELF?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=1";
	printc("<form action='$u' method='post'>\n");
	
	ob_start();
	
	print<<<END
	
	<script type='text/javascript'>
	// <![CDATA[
	
	if (!Array.prototype.push) {

		/**
		 * IE 5.01 does not implement the push method, so it needs to be
		 * added
		 * 
		 * @param mixed element
		 * @return int
		 * @access public
		 * @since 1/31/07
		 */
		Array.prototype.push = function ( element ) {
			var key = this.length;
			this[key] = element;
			return key;
		}
		
	}
	
	/**
	 * Update the radio buttons checked if needed to preven un-allowed situations:
	 * 		- comparing a version to itself
	 *		- mixing up new and old versions
	 * 
	 * @param object RadioButton button
	 * @return void
	 * @access public
	 * @since 2/2/07
	 */
	function updateVersionSelection (button) {
		var oldButtons = new Array();
		oldButtons.push(null);
		var newButtons = new Array();
		
		for (var i = 0; i < button.form.elements.length; i++) {
			// Sort the elements into old and new arrays and record
			// what row in each is selected
			var element = button.form.elements[i];
			if (element.name == 'oldversion') {
				oldButtons.push(element);
				if (element.checked)
					var oldRow = oldButtons.length - 1;
			} else if (element.name == 'newversion') {
				newButtons.push(element);
				if (element.checked)
					var newRow = newButtons.length - 1;
			}
		}
						
		// If a new version was selected make sure that the old version is older
		if (button.name == 'newversion') {
			if (oldRow <= newRow) {
				oldButtons[oldRow].checked = '';
				oldButtons[newRow + 1].checked = 'checked';
			}
			
			for (var i = 1; i < oldButtons.length; i++) {
				if (i <= newRow)
					oldButtons[i].style.visibility = 'hidden';
				else
					oldButtons[i].style.visibility = 'visible';
			}
		} 
		// If an old version was selected make sure that the new version is newer
		else {
			if (newRow >= oldRow) {
				newButtons[newRow].checked = '';
				newButtons[oldRow - 1].checked = 'checked';
			}
			
			for (var i = 1; i < newButtons.length; i++) {
				if (i >= oldRow)
					newButtons[i].style.visibility = 'hidden';
				else
					newButtons[i].style.visibility = 'visible';
			}
		}
	}
	
	// ]]>
	</script>
	
	
END;
	printc(ob_get_clean());
	
	// compare selected versions button (top)
	printc("<table cellspacing='3' width='100%'>\n\t<tr>");
	printc("\n\t<td><button type='submit' class='button' value='compare' onclick=\"window.location='$u'\">Compare selected revisions</button></td>");
	if ($action == "viewsite") {
		printc("\n\t<td align='right'><a class='btnlink2' href='index.php?$sid&amp;action=edit_story&amp;site=$site&amp;section=$section&amp;page=$page&amp;edit_story=$story&amp;comingFrom=viewsite'>Edit current version</a></td>\n");
	}
	printc("\n\t</tr>\n</table>");

	printc("\n<table cellspacing='3' width='100%'>\n");
	$versions = get_versions($storyObj->id);
	//printpre($versions);	
	
	printc("<thead><tr><th colspan='2'>Select</th><th>Revision</th><th>Revision Date</th><th>Revision Author</th><th>Revision Comment</th></tr></thead>\n");
	
	printc("\n<tbody style='vertical-align: top;'>\n");
		
	$shadeStyle = ' background-color: #F3F3F3; ';
	$shade = 0;
	$i = 0;
	$hideOld = true;
	$hideNew = false;
	$currentversion = true;
	foreach ($versions as $version) {
		$version_id = $version['version_id'];
		$version_num = $version['version_order'];
		
		printc("<tr>\n");
		printc("<td align='right' style='");
		if ($shade) printc($shadeStyle);
		printc("'>");
		
		if ($i > 0) {
			printc("<input type='radio' name='oldversion' value='".$version_num."' ");
			
			if (isset($_SESSION['oldversion'][$_REQUEST['story']])) {
				if ($_SESSION['oldversion'][$_REQUEST['story']] == $version_num) {
					printc(" checked='checked'");
					$hideNew = true;
				}
			} else {
				if ($i == 1) {
					printc(" checked='checked'");
					$hideNew = true;
				}
			}
			
			if ($hideOld)
				printc(" style='visibility: hidden;'");
			
			printc(" onclick=\"updateVersionSelection(this);\"");
			
			printc(" />");
		}
		
		printc("</td>\n<td align='left' style='");
		if ($shade) printc($shadeStyle);
		printc("'>");
		
		if ($i < count($versions) - 1) {
			printc("<input type='radio' name='newversion' value='".$version_num."' ");
			
			if (isset($_SESSION['newversion'][$_REQUEST['story']])) {
				if ($_SESSION['newversion'][$_REQUEST['story']] == $version_num) {
					printc(" checked='checked'");
					$hideOld = false;
				}
			} else {
				if (!$i) {
					printc(" checked='checked'");
					$hideOld = false;
				}
			}
			
			if ($hideNew)
				printc(" style='visibility: hidden;'");
			
			printc(" onclick=\"updateVersionSelection(this);\"");
			printc(" />");
		}

		printc("</td>");
		if ($currentversion) {
			printc("<td  style='");
		if ($shade) printc($shadeStyle);
		printc("'>");
		printc("<a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;detail=$story'>Revision $version_num</a> (current)</td>");
		} else {
			printc("<td  style='");
		if ($shade) printc($shadeStyle);
		printc("'>");
		printc("<a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;version=$version_num'>Revision $version_num</a></td>");
		}
		
		printc("<td  style='white-space: nowrap; ");
		if ($shade) printc($shadeStyle);
		printc("'>");
		printc($version['version_created_tstamp']."</td>");
		
		printc("<td  style='white-space: nowrap; ");
		if ($shade) printc($shadeStyle);
		printc("'>");
		printc($version['FK_createdby']."</td>\n");
		
		printc("<td  style='font-size: smaller; ");
		if ($shade) printc($shadeStyle);
		printc("'>");
		printc($version['version_comments']."</td>\n");
		
		printc("</tr>\n");
		
		$shade = 1-$shade;
		$currentversion = false;
		$i++;
	}
	
	printc("\n</tbody>\n");

	printc("</table>\n");
	// compare selected versions button (bottom)
	printc("\n<table cellspacing='3' width='100%'>\n\t<tr>");
	printc("\n\t<td><button type='submit' class='button' value='compare' onclick=\"window.location='$u'\">Compare selected revisions</button></td>");

//	printc("<td align='right'><button type='submit' class='button' value='compare' onclick=\"window.location='$u'\">Edit this version</button></td>");
	printc("\n\t</tr>\n</table>");

// 	printc("<br /><button type='submit' class='button' value='compare'>Compare selected revisions</button><br /><br /> ");
	printc("\n\t\t</form>");
	printc("\n\t</td>\n</tr>");

}
		
// Revert to this version link (bottom location)
if ($_REQUEST['version']  && $storyObj->hasPermission("edit")) {
	printc("\n\t<tr><td align='center'><br />");
	printc("<a class='btnlink2' href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;revert=$version_num&amp;versioning=$story&amp;comingFrom=viewsite'>Revert to this Version</a>\n");		
	printc("</td></tr>\n");
}


/******************************************************************************
 * print out title and options
 ******************************************************************************/
 
printc("<tr><td align='left'>");
printc("<a href='index.php?action=".$action."&amp;".$getinfo2."'>".spchars($pageObj->getField('title'))."</a>");

if ($storyObj->getField('title')) {
	printc(" > ".spchars($storyObj->getField('title')));
}

if ($_REQUEST['oldversion'] && $_REQUEST['newversion']) {
	printc(" > <a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
	printc(" > Selected Versions");
	
} else if ($_REQUEST['versioning']) {
	printc(" > All Versions");

/******************************************************************************
 * if particular version then get version details
 ******************************************************************************/
 
} else if ($_REQUEST['version']) {
	printc(" > <a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;versioning=$story'>All Versions</a>");
	printc(" > Revision ".$version_num);
	
} else {
	printc(" > in depth");
}
printc("</td></tr>\n");

printc("</table>\n");


?>