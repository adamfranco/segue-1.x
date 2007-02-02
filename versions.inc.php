<? /* $Id$ */

//require("objects/objects.inc.php");
$content = '';

//ob_start();
/******************************************************************************
 * This script is an adaptation of fullstory.php
 * this script is included in site.inc.php when detail variable is set
 ******************************************************************************/
//printpre($_REQUEST);
//session_start();

// include all necessary files
//include("includes.inc.php");

//include(dirname(__FILE__)."/".$themesdir."/common/header.inc.php");

if ($tmp = $_REQUEST['flat_discussion']) {
	$_SESSION['flat_discussion'] = ($tmp=='true')?true:false;
}

if ($tmp = $_REQUEST['order']) {
	$_SESSION['order'] = $_REQUEST['order'];
}

/* if ($tmp2 = $_REQUEST['recent']) { */
/* 	$_SESSION['recent'] = ($tmp2=='true')?true:false; */
/* } */

//printpre($_SESSION);
//printpre($_REQUEST);

$partialstatus = 1;
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
	
	printc("<tr><th>Select</th><th>Revision</th><th>Revision Date</th><th>Revision Author</th></tr>\n");
		
	$color = 0;
	foreach($versions as $version) {
		$version_id = $version['version_id'];
		$version_num = $version['version_order'];
		if (is_array($_SESSION[selversions]) && in_array($version_num,$_SESSION[selversions])) {
			$checkstatus = " checked";
		} else {
			$checkstatus = "";
		}

		printc("<tr>\n");
		printc("<td class=ts$color align='center'><input type='checkbox' name='selversions[]' value='".$version_num."' ".$checkstatus."></td>");
		printc("<td class=ts$color><a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=$story&amp;version=$version_num'>Revision $version_num</a></td>");
		printc("<td class=ts$color>".$version['version_created_tstamp']."</td>");
		printc("<td class=ts$color>".$version['FK_createdby']."</td>\n");
		printc("</tr>\n");
		$color = 1-$color;
	}	

	printc("</table>\n");
	// compare selected versions button (bottom)
	printc("<br /><button type='submit' class='button' value='compare'>Compare selected revisions</button><br \><br \> ");
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
 *  if discussions, then print these
 ******************************************************************************/
if ($storyObj->getField("discuss")) {
	$mailposts = $storyObj->getField("discussemail");	
	$showposts = $storyObj->getField("discussdisplay");
	$showallauthors = $storyObj->getField("discussauthor");	
	$siteowner = $siteObj->ownerfname;
	$discusslabel = $storyObj->getField("discusslabel");	
	printc("\n<table width='100%' cellspacing='1'>");
	printc("\n\t<tr>\n\t\t<td align='left' class='dheader'>\n\t\t\t<a name='discuss'></a>");
	printc(($discusslabel)? $discusslabel:"Discuss");
	
/* 	if ($showposts == 1) { */
/* 		printc("<td align='left'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td align='left' class='dheader'>Discussion\n"); */
/* 	} else { */
/* 		printc("<td align='left'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td align='left' class='dheader'>Assessment\n");	 */
/* 	} */
	
	//get number of discuss/assess participants
	$numparticipants = participants();
	$storyid = $storyObj->getField('id');
	$siteid = $siteObj->getField('id');
	$site=$siteObj->name;
	//printpre($_SESSION);
	printc("\n\t\t\t<div style='font-size: 10px'>\n\t\t\t\t");
	if ($_SESSION[auser]==$site_owner) {	
		printc($numparticipants." participants");
		printc(" - <a href='email.php?$sid&amp;storyid=$storyid&amp;siteid=$siteid&amp;site=$site&amp;action=list' onclick='doWindow(\"email\",700,500)' target='email'>Summary &amp; Email</a> \n");
		//printc("<a href='email.php?$sid&amp;storyid=$storyid&amp;siteid=$siteid&amp;site=$site&amp;action=email' onclick='doWindow(\"email\",700,500)' target='email'>Email</a> - \n");
		
	} else {
		printc($numparticipants." participants");
	}
	printc("\n\t\t\t</div>");
	
	printc("\n\t\t</td>");
	printc("\n\t\t<td align='right' class='dheader2'>");
	
	printc("\n\t\t\t<table>");
	printc("\n\t\t\t\t<tr>\n\t\t\t\t\t<td>");
	$f = $_SESSION['flat_discussion'];
	printc("\n\t\t\t\t\t\t<form action='index.php?$sid&amp;action=site&amp;".$getinfo."' method='post' name='viewform'>");
	printc("\n\t\t\t\t\t\t\t<select name='flat_discussion'>");
	printc("\n\t\t\t\t\t\t\t\t<option value='true'".(($f)?" selected='selected'":"").">Flat</option>");
	printc("\n\t\t\t\t\t\t\t\t<option value='false'".((!$f)?" selected='selected'":"").">Threaded</option>");
	printc("\n\t\t\t\t\t\t\t</select>");
	//printc("</td><td>\n");

	$r = $_SESSION['order'];
	printc("\n\t\t\t\t\t\t\t<select name='order'>");
	printc("\n\t\t\t\t\t\t\t\t<option value='2'".(($r == 2)?" selected='selected'":"").">Recent Last</option>");
	printc("\n\t\t\t\t\t\t\t\t<option value='1'".(($r == 1)?" selected='selected'":"").">Recent First</option>");
	
	//if ($_SESSION[auser]==$site_owner) {
		printc("\n\t\t\t\t\t\t\t\t<option value='3'".(($r == 3)?" selected='selected'":"").">Rating</option>");
		printc("\n\t\t\t\t\t\t\t\t<option value='4'".(($r == 4)?" selected='selected'":"").">Author</option>");
	//}
	printc("\n\t\t\t\t\t\t\t</select>");
	printc("\n\t\t\t\t\t\t\t<input type='submit' class='button' value='Change' />");
	printc("\n\t\t\t\t\t\t</form>");
	printc("\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t</table>");	
	printc("\n\t\t</td>\n\t</tr>");
	printc("\n</table>\n");
	
/******************************************************************************
 * Explain discuss/assess settings to participants 
 ******************************************************************************/
	
	// hide posts (assessment)
	if ($showposts == 2 && $showallauthors == 1) {
		printc("Posts to this assessment are currently viewable only be the site owner, <i>$siteowner</i>.  Shown here are only your posts and any replies to your post by <i>$siteowner</i>.");
	// show posts, hide author names (Anonymous Discussion)
	} else if ($showposts == 1 && $showallauthors == 2) {
		printc("Author of posts to this discussion are known only to the site owner, <i>$siteowner</i>.  Other participants will not see your name associated with your posts (unless you include it in the subject or body of your post).");
	// hide posts, hide authornames  (assessment)
	} else if ($showposts == 2 && $showallauthors == 2) {
		printc("Posts to this assessment are currently viewable only be the site owner, <i>$siteowner</i>.  Shown here are only your posts and any replies to your post by <i>$siteowner</i>.");
	}
	
/******************************************************************************
 * Summarize discuss/assess settings for site owner
 ******************************************************************************/
	
	if ($_SESSION[auser]==$site_owner) {
		printc("\n<br />\n<table class='dinfo1' width='90%' align='center'>");
		printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<div style='font-size: 12px'>");
		printc("\n\t\t\t\t<strong>Current Discussion Settings:</strong>");
		printc("\n\t\t\t</div>\n\t\t</td>\n\t</tr>");
		printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<div style='font-size: 9px'>");
		printc("\n\t\t\t\t<b>Mail Posts:</b>");
		if ($mailposts == 1) {
			printc(" All posts to this discussion/assessment will be mailed to you.");
		} else {
			printc(" Email notification of posts to this discussion/assessment has been disabled.");
		}
		printc("\n\t\t\t</div>\n\t\t</td>\n\t</tr>");		
		printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<div style='font-size: 9px'>");
		if ($showposts == 1) {
			$type = "discussion";
			printc("<b>Discussion:</b> Participants can read and respond to each other's posts.");
		} else {
			$type = "assessment";
			printc("<b>Assessment:</b> Participants will not be able to read each other's posts.");
		}
		printc("\n\t\t\t</div>\n\t\t</td>\n\t</tr>");		
		printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<div style='font-size: 9px'>");
		// if showposts == 2 (assessment), info about authors display is not necessary
		if ($showallauthors == 2 && $showposts != 2) {
			printc("<b>Hide Authors:</b> Authors of posts have been hidden from participants to allow for anonymous discussion (only you can see participant names).");
		} else if ($showallauthors == 1 && $showposts != 2) {
			printc("<b>Show Authors:</b> Author of each and every post is identified to all participants.");
		}		
		printc("\n\t\t\t</div>\n\t\t</td>\n\t</tr>");		
		printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<div style='font-size: 9px'><i>To change these settings and determine who can participant in this ".$type.", click on edit link below.</i>\n\t\t\t</div>\n\t\t</td>\n\t</tr>");
		printc("\n\t<tr>\n\t\t<td align='right'>\n\t\t\t<div style='font-size: 10px'><a href='index.php?".$editsettingsurl."'>edit</a></div>\n\t\t</td>\n\t</tr>");
		printc("\n</table>");	
	}

	
/******************************************************************************
 * Instantiate a discussion object $ds from objects/discussion.inc.php
 * and pass it discussion settings: $order, $showposts, $showallauthors, $mailposts
 ******************************************************************************/
	

	$ds = & new discussion($storyObj);
	if ($f) $ds->flat(); // must be called before _fetchchildren();
	
	if ($_REQUEST['order'] == 1) {
		$ds->recentfirst();
	} else if ($_REQUEST['order'] == 2) {
		$ds->recentlast();
	} else if ($_REQUEST['order'] == 3) {
		$ds->rating();
	} else if ($_REQUEST['order'] == 4) {
		$ds->author();
	} else {
		//$ds->recentlast();
	}
	
// 	printpre($_REQUEST['order']);
	
/******************************************************************************
 * get all discussion posts and order them by $order
 * (recent first, recent last, rating, author)
 * returns an array of discussion objects
 ******************************************************************************/
	
	$ds->_fetchchildren();
	
/******************************************************************************
 * set discussion options
 ******************************************************************************/
	
	$ds->opt("showcontent",true);
	$ds->opt("showauthor",false);
	$ds->opt("showtstamp",false);
	$ds->opt("useoptforchildren",true);
	$ds->getinfo = $getinfo;
	
			
/******************************************************************************
 * 	output all posts that user has permission to view
 *  using array of posts generated by _fetchchildren
 ******************************************************************************/
 	// Start with default perms
	$canReply = $storyObj->hasPermission("discuss");
	
	// Unless everyone is specifically given permission; i.e "anonymous posting" is allowed,
	// make sure that the user is logged in.
	if ((!$_SESSION[auser] || $_SESSION[atype] == "visitor") && !$storyObj->hasPermission("discuss", "everyone", 1))
		$canReply = FALSE;
	
	
	printc("\n<table width='100%' cellspacing='1'>");
	$ds->outputAll($canReply,($_SESSION[auser]==$site_owner),true,$showposts,$showallauthors,$mailposts);
	if (!$ds->count()) printc("\n\t<tr>\n\t\t<td>There have been no posts to this discussion.</td>\n\t</tr>");
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

function participants() {
	global $storyObj;
	$storyid = $storyObj->getField("id");	
	$where = "story_id ='".addslashes( $storyid)."'";
	$query = "
	SELECT 
		distinct user_fname, user_email
	FROM 
		discussion
	INNER JOIN story ON FK_story = story_id
	INNER JOIN page ON FK_page = page_id
	INNER JOIN section ON FK_section = section_id
	INNER JOIN site ON FK_site = site_id
	INNER JOIN user ON FK_author = user_id
	WHERE 
		$where
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$num = db_num_rows($r);
	//$num.= " participants";
	return $num;

}

?>