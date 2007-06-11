<? /* $Id$ */

//require("objects/objects.inc.php");
$content = '';

//ob_start();
/******************************************************************************
 * This script is an adaptation of fullstory.php
 * this script is included in site.inc.php when detail variable is set
 ******************************************************************************/
if ($_REQUEST['action'] == 'viewsite') 
	$action = 'viewsite';
else
	$action = 'site';


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
	//printpre($storyObj->getField("shorttext"));
	$smalltext = convertTagsToInteralLinks($siteObj->name, $storyObj->getField("shorttext"));
	$fulltext = convertTagsToInteralLinks($siteObj->name, $storyObj->getField("longertext"));
	$smalltext = stripslashes($smalltext);
	$fulltext = stripslashes($fulltext);
	
//	$smalltext = convertWikiMarkupToLinks($site,$section,$page,$o->id, $page_title, $smalltext);
//	$fulltext = convertWikiMarkupToLinks($site,$section,$page,$o->id, $page_title, $fulltext);
	$wikiResolver =& WikiResolver::instance();
	$smalltext = $wikiResolver->parseText($smalltext, $site, $section, $page);
	$fulltext = $wikiResolver->parseText($fulltext, $site, $section, $page);
	
	if ($storyObj->getField("texttype") == 'text') $fulltext = htmlbr($fulltext);	
	if ($storyObj->getField("texttype") == 'text') $smalltext = htmlbr($smalltext);
} else if ($storyObj->getField("type") == 'image') {
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
} else if ($storyObj->getField("type") == 'file') {
	$fulltext = "<br />";
	$fulltext .= makedownloadbar($storyObj);
} else {
	$fulltext = "<br />";
	$incfile = "output_modules/".$siteObj->getField("type")."/".$storyObj->getField("type").".inc.php";
	//	print $incfile; // debug
		include($incfile);
	$fulltext .= $content;
	$content = '';
}

/******************************************************************************
 * print out shory and discussion (if any)
 ******************************************************************************/
// require_once("htmleditor/editor.inc.php");
if ($storyObj->getField("discuss")) $titleExtra = " Discussion";

printc("\n<table width='100%' id='maintable' cellspacing='1'>");

printc($pagePagination);
				
printc("\n\t<tr>\n\t\t<td align='left' class='title'>\n\t\t\t<a href='index.php?action=".$action."&amp;".$getinfo2."'>".spchars($pageObj->getField('title'))."</a>");

if ($storyObj->getField('title')) {
	printc("\n\t\t\t&gt; ".spchars($storyObj->getField('title'))." &gt; in depth");
} else {
	printc("\n\t\t\t&gt; in depth");
}
printc("\n\t\t</td>\n\t</tr>");


if ($storyObj->getField('type') != "image" && $storyObj->getField('type') != "link" && $storyObj->getField('type') != "rss") printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<strong>".(($storyObj->getField('title'))?spchars($storyObj->getField('title')):'&nbsp;')."</strong>\n\t\t</td>\n\t</tr>");

$record_id = $story;
$user_id = $_SESSION[aid];
$record_type = "story";
$story_tags = get_record_tags($record_id);
//printpre($story_tags);

if ($story_tags) {
	printc("\n\t\t\t\t<div class='contentinfo' style='margin-top: 0px;'>");
	printc("\n\t\t\t\tCategories:");
	foreach ($story_tags as $tag) {
		$urltag = urlencode($tag);
		$tagname = urldecode($tag);
		printc("\n\t\t\t\t<a href='index.php?$sid&amp;action=".$action."&amp;site=$site&amp;section=$section&amp;page=$page&amp;tag=$urltag'>".$tagname."</a>");
	};
	printc("\n\t\t\t</div>\n\t\t</td>\n\t</tr>");
}

printc("\n\t<tr>\n\t\t<td style='padding-bottom: 15px; font-size: 12px'>\n\t\t\t$smalltext\n\t\t</td>\n\t</tr>");
if ($storyObj->getField('type') != "rss") {
	printc("\n\t<tr>\n\t\t<td style='padding-bottom: 15px; font-size: 12px'>\n\t\t\t$fulltext\n\t\t</td>\n\t</tr>");
}


if ($storyObj->getField('type') == "image") {
	printc("\n\t<tr>\n\t\t<td align='center' font-size: 12px'>\n\t\t\t<strong>".spchars($storyObj->getField('title'))."</strong>\n\t\t</td>\n\t</tr>");
	printc("\n\t<tr>\n\t\t<td font-size: 12px'>$captiontext</td>\n\t</tr>");
}

if ($storyObj->getField('type') == "rss") {
	//include_once (dirname(__FILE__)."/carprss/carp.php");
	ob_clean();	
	ob_start();
	print "\n\n";
	
	$url = $storyObj->getField("url");
	MyCarpConfReset();
	MyCarpConfReset('rss_contentblock');
	
	if (is_numeric($storyObj->getField("longertext"))) {
		$num_per_set = $storyObj->getField("longertext");
		CarpConf('maxitems',$num_per_set);						
	} else {
		CarpConf('maxitems',5);
	}
	
	
	// If we have an auser, create a cache just for them.
	if ($_SESSION['auser']) {
		CarpCacheShow($url, '', 1,  $_SESSION['auser']);
	} else {
	
		// If the user has a valid campus ip-address, then they are a
		// member of 'institute'.
		$ipIsInInstitute = FALSE;
		$ip = $_SERVER[REMOTE_ADDR];
		// check if our IP is in inst_ips
		if (is_array($cfg[inst_ips])) {
			foreach ($cfg[inst_ips] as $i) {
				if (ereg("^$i",$ip)) 
					$ipIsInInstitute = TRUE;
			}
		}
		
		// if we are in the institute IPs, use the institute
		// cache.
		if ($ipIsInInstitute) {
			CarpCacheShow($url, '', 1, 'institute');
		}
		// If we aren't logged in or in the institute IPs, just use the
		// everyone cache.
		else {
			CarpCacheShow($url);
		}
	}
	$rssitems = ob_get_contents();
	printc("\n\t<tr>\n\t\t<td style='padding-bottom: 15px; font-size: 12px'>\n\t\t\t$rssitems\n\t\t</td>\n\t</tr>");
	ob_clean();	


}

printc("\n</table>\n");

/*********************************************************
 * Print out edit links if we are in viewsite mode
 *********************************************************/
if ($action == 'viewsite' && isset($storyEditLinks))
 	printc($storyEditLinks);

/*********************************************************
 * output discussions?
 *********************************************************/
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
	printc("\n\t\t\t\t\t\t<form action='index.php?$sid&amp;action=".$action."&amp;".$getinfo."' method='post' name='viewform'>");
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
	$siteLevelEditors = $siteObj->getSiteLevelEditors();
	//printpre($siteLevelEditors);
		
	// hide posts (assessment)
	if ($showposts == 2 && $showallauthors == 1) {
		printc("Posts to this assessment are currently viewable only be the site owner, <i>$siteowner</i>");		
		if (count($siteLevelEditors)) {
			printc(" and full editors of this site");	
		}
		printc(".");
		
		printc("  Shown here are only your posts and any replies to your post by <i>$siteowner</i>");
		if (count($siteLevelEditors)) {
			printc(" and full editors of this site");	
		}
		printc(".");


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
		printc("<table>");
printc("\n\t<tr>\n\t\t<td align='left'>\n\t\t\t<br /><a href='index.php?action=".$action."&amp;".$getinfo2."'>".spchars($pageObj->getField('title'))."</a> &gt; in depth</td>\n\t</tr>");
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