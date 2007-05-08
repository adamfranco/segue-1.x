<? /* $Id$ */

//require("objects/objects.inc.php");
$content = '';

//ob_start();
/******************************************************************************
 * This script is an adaptation of fullstory.php
 * this script is included in site.inc.php when search variable is set
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

if ($_REQUEST['search']) {
	printc("<div class='title'>Search: ".$_REQUEST['search']);
	//printc( "search =".$search);
}

// get the correct shorttext
if ($storyObj->getField("type") == 'story') {
	//printpre($storyObj->getField("shorttext"));
//	$smalltext = convertTagsToInteralLinks($siteObj->name, $storyObj->getField("shorttext"));
//	$fulltext = convertTagsToInteralLinks($siteObj->name, $storyObj->getField("longertext"));
//	$smalltext = stripslashes($smalltext);
//	$fulltext = stripslashes($fulltext);
//	
////	$smalltext = convertWikiMarkupToLinks($site,$section,$page,$o->id, $page_title, $smalltext);
////	$fulltext = convertWikiMarkupToLinks($site,$section,$page,$o->id, $page_title, $fulltext);
//	$wikiResolver =& WikiResolver::instance();
//	$smalltext = $wikiResolver->parseText($smalltext, $site, $section, $page);
//	$fulltext = $wikiResolver->parseText($fulltext, $site, $section, $page);
//	
//	if ($storyObj->getField("texttype") == 'text') $fulltext = htmlbr($fulltext);	
//	if ($storyObj->getField("texttype") == 'text') $smalltext = htmlbr($smalltext);
}



//printc("\n</table>\n");

/*********************************************************
 * Print out edit links if we are in viewsite mode
 *********************************************************/
//if ($action == 'viewsite' && isset($storyEditLinks))
// 	printc($storyEditLinks);


?>