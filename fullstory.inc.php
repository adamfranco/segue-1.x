<? /* $Id$ */

//require("objects/objects.inc.php");
$content = '';

//ob_start();
/******************************************************************************
 * This script is an adaptation of fullstory.php
 * this script is included in site.inc.php when detail variable is set
 ******************************************************************************/

//session_start();

// include all necessary files
//include("includes.inc.php");

//include("$themesdir/common/header.inc.php");

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
$sectionObj =& new section($_REQUEST[site],$_REQUEST[section], &$siteObj);
$pageObj =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$sectionObj);
$storyObj =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$pageObj);
$getinfo = "site=".$siteObj->name."&section=".$sectionObj->id."&page=".$pageObj->id."&story=".$storyObj->id."&detail=".$storyObj->id;
$getinfo2 = "site=".$siteObj->name."&section=".$sectionObj->id."&page=".$pageObj->id;
$editsettingsurl = "&site=".$siteObj->name."&section=".$sectionObj->id."&page=".$pageObj->id."&action=edit_story&edit_story=".$storyObj->id."&detail=".$storyObj->id."&comingFrom=viewsite&step=4";

$storyObj->fetchFromDB();
$storyObj->owningSiteObj->fetchFromDB();
//$site_owner=slot::getOwner($story->owningSiteObj->name);
$site_owner=$storyObj->owningSiteObj->owner;
//print_r($story->owningSiteObj);
//print $site_owner;

// get the correct shorttext
if ($storyObj->getField("type") == 'story') {
	$smalltext = $storyObj->getField("shorttext");
	$fulltext = $storyObj->getField("longertext");
	$smalltext = stripslashes($smalltext);
	$fulltext = stripslashes($fulltext);
	if ($storyObj->getField("texttype") == 'text') $fulltext = htmlbr($fulltext);	
	if ($storyObj->getField("texttype") == 'text') $smalltext = htmlbr($smalltext);
}
if ($storyObj->getField("type") == 'image') {
	$filename = urldecode(db_get_value("media","media_tag","media_id=".$storyObj->getField("longertext")));
	$dir = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id=".$storyObj->getField("longertext"));
	$imagepath = "$uploadurl/$dir/$filename";
	$fulltext = "<div style='text-align: center'><br><img src='$imagepath' border=0></div>";
/* 	if ($story->getField("title")) $fulltext .= "<tr><td align=center><b>".spchars($story->getField("title"))."</b></td></tr>"; */
	if ($storyObj->getField("shorttext")) $fulltext .= "<br>".stripslashes($storyObj->getField("shorttext"));
	$fulltext .= "";
}
if ($storyObj->getField("type") == 'file') {
	$fulltext = "<br>";
	$fulltext .= makedownloadbar($storyObj);
}


?>
<!--<html>
<head>
<title>Full Content/Discussion</title>-->
<? //include("themes/common/logs_css.inc.php"); ?>
<style type="text/css">

.subject { 
	font-weight: bolder; 
}

.dtext {
	padding-top: 0px;
	padding-bottom: 20px;
	padding-left: 0px;
	padding-right: 0px;
}

.dheader {
	font-size: 14px;
	border-bottom: 1px solid #000;
	background: #EAEAEA;
	padding-left: 5px;
	padding-top: 5px;
}

.dheader2 {
	border-bottom: 1px solid #000;
	background: #EAEAEA;
	padding-right: 5px;
	padding-top: 5px;
}

.dheader3 {
	border-top: 0px solid #000;
	background: #EAEAEA;
	padding-left: 5px;
	padding-right: 5px;
}

.dinfo1 {
	border: 1px solid #000;
	padding-left: 2px;
	padding-right: 2px;
}


</style>
<!--</head>

<body>-->
<?
/******************************************************************************
 * print out shory and discussion (if any)
 ******************************************************************************/
// include("htmleditor/editor.inc.php");
if ($storyObj->getField("discuss")) $titleExtra = " Discussion";

printc("<table width=100% id='maintable' cellspacing=1>\n");
printc("<tr><td>\n");
printc("<table cellspacing=1 width=100%>\n");
printc("<tr><td align=left class=title><a href=index.php?action=site&".$getinfo2.">".spchars($pageObj->getField('title'))."</a> > in depth</td></tr>\n");
//printc("<tr><td align=left class=title>".(($pageObj->getField('title'))?spchars($pageObj->getField('title')):'&nbsp;')."</td></tr>");		 
printc("<tr><td align=left><b>".(($storyObj->getField('title'))?spchars($storyObj->getField('title')):'&nbsp;')."</b></td></tr>\n");
printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$smalltext</td></tr>\n");
printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$fulltext</td></tr>\n");

		
// output discussions?
if ($storyObj->getField("discuss")) {
	$mailposts = $storyObj->getField("discussemail");	
	$showposts = $storyObj->getField("discussdisplay");
	$showallauthors = $storyObj->getField("discussauthor");	
	$siteowner = $siteObj->getField("addedbyfull");	
	
	if ($showposts == 1) {
		printc("<td align=left><table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=left class=dheader>Discussion\n");
	} else {
		printc("<td align=left><table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=left class=dheader>Assessment\n");	
	}
	
	//get number of discuss/assess participants
	$numparticipants = participants();
	$storyid = $storyObj->getField('id');
	$siteid = $siteObj->getField('id');
	$site=$siteObj->name;
	
	printc("<div style='font-size: 10px'>");
	if ($_SESSION[auser]==$site_owner || $_SESSION[ltype]=="admin") {		
		printc("<a href='email.php?$sid&storyid=$storyid&siteid=$siteid&site=$site&action=list' onClick='doWindow(\"email\",700,500)' target='email'>List</a> | \n");
		printc("<a href='email.php?$sid&storyid=$storyid&siteid=$siteid&site=$site&action=email' onClick='doWindow(\"email\",700,500)' target='email'>Email</a> - \n");
		printc($numparticipants." participants");
	} else {
		printc($numparticipants." participants");
	}
	printc("</div>");
	
	printc("</td>\n");
	printc("<td align=right class=dheader2>\n");
	
	printc("<table>\n");
	printc("<tr><td>\n");
	$f = $_SESSION['flat_discussion'];
	printc("<form action='index.php?$sid&action=site&".$getinfo."' method=post name=viewform>\n");
	printc("<select name='flat_discussion'>\n");
	printc("<option value='true'".(($f)?" selected":"").">Flat\n");
	printc("<option value='false'".((!$f)?" selected":"").">Threaded\n");
	printc("</select>\n");
	printc("</td><td>\n");

	$r = $_SESSION['order'];
	printc("<select name='order'>\n");
	printc("<option value='2'".(($order == 2)?" selected":"").">Recent Last\n");
	printc("<option value='1'".(($order == 1)?" selected":"").">Recent First\n");
	
	//if ($_SESSION[auser]==$site_owner) {
		printc("<option value='3'".(($order == 3)?" selected":"").">Rating\n");
		printc("<option value='4'".(($order == 4)?" selected":"").">Author\n");
	//}
	printc("</select>");
	printc("<input type=submit class='button' value='Change'>\n");
	printc("</td></tr></table>\n");
	printc("</form>\n");	
	printc("</th></tr>\n");
	printc("</table>\n");
	
	// hide posts (assessment)
	if ($showposts == 2 && $showallauthors == 1) {
		printc("Posts to this assessment are currently viewable only be the site owner, <i>$siteowner</i>.  Shown here are only your posts and any replies to your post by <i>$siteowner</i>.");
	// show posts, hide author names (Anonymous Discussion)
	} else if ($showposts == 1 && $showallauthors == 2) {
		printc("Author of posts to this discussion are known only to the site owner, <i>$siteowner</i>.  Other participants will not see your name associated with your posts.");
	// hide posts, hide authornames  (assessment)
	} else if ($showposts == 2 && $showallauthors == 2) {
		printc("Posts to this assessment are currently viewable only be the site owner, <i>$siteowner</i>.  Shown here are only your posts and any replies to your post by <i>$siteowner</i>.");
	}
	if ($_SESSION[auser]==$site_owner) {
		printc("<br><table class=dinfo1 width=90% align=center>");
		printc("<tr><td align=left><div style='font-size: 9px'>");
		printc("<b>Mail Posts:</b>");
		if ($mailposts == 1) {
			printc(" All posts to this discussion will be mailed to you.");
		} else {
			printc(" Email notification of posts to this discussion has been disabled.");
		}
		printc("</div></td></tr>");		
		printc("<tr><td align=left><div style='font-size: 9px'>");
		if ($showposts == 1) {
			$type = "discussion";
			printc("<b>Discussion:</b> Participants can read and respond to each other's posts.");
		} else {
			$type = "assessment";
			printc("<b>Assessment:</b> Participants will not be able to read each other's posts.");
		}
		printc("</div></td></tr>");		
		printc("<tr><td align=left><div style='font-size: 9px'>");
		if ($showallauthors == 2 && $showposts == 1) {
			printc("<b>Hide Authors:</b> Authors of posts have been hidden from participants to allow for anonymous discussion.");
		} else if ($showallauthors == 1) {
			printc("<b>Show Authors:</b> Author of each and every post is identified to all participants.");
		}		
		printc("</div></td></tr>");		
		printc("<tr><td align=left><div style='font-size: 9px'><i>To change these settings and determine who can participant in this ".$type.", click on edit link below.</i></div></td></tr>");
		printc("<tr><td align=right><div style='font-size: 10px'><a href=index.php?".$editsettingsurl.">edit</a></div></td></tr>");
		printc("</table>");	
	}

	
	printc("</th>\n");
	printc("</tr>\n");
	
/******************************************************************************
 * Instantiate a discussion object $ds from objects/discussion.inc.php
 * and pass it discussion settings: $order, $showposts, $showallauthors, $mailposts
 ******************************************************************************/
	

	$ds = & new discussion(&$storyObj);
	if ($f) $ds->flat(); // must be called before _fetchchildren();
	
	if ($order == 1) {
		$ds->recentfirst();
	} else if ($order == 2) {
		$ds->recentlast();
	} else if ($order == 3) {
		$ds->rating();
	} else if ($order == 4) {
		$ds->author();
	} else {
		//$ds->recentlast();
	}
	
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
 
	$ds->outputAll($storyObj->hasPermission("discuss"),($_SESSION[auser]==$site_owner),true,$showposts,$showallauthors,$mailposts);
	if (!$ds->count()) printc("<tr><td>There have been no posts to this discussion.</td></tr>");
}
		
printc("<tr><td align=left><br><a href=index.php?action=site&".$getinfo2.">".spchars($pageObj->getField('title'))."</a> > in depth</td></tr>\n");
printc("</table>\n");

printc("</tr></td>\n");
printc("</table>\n");
printc("<BR><BR>\n");


function participants() {
	global $storyObj;
	$storyid = $storyObj->getField("id");	
	$where = "story_id = $storyid";
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
<!--<div align=right><input type=button value="Close Window" onClick="window.close()"></div>-->