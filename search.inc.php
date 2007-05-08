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
	$search = $_REQUEST['search'];

		
//	$pageResults = searchPages($search);
	$contentResults = searchContent($_REQUEST['search'], $_REQUEST[site]);
//	$discussResults = searchDiscussions($search);

	/******************************************************************************
	 * Print out search results
	 ******************************************************************************/

	
	$totalResults = count($pageResults) + count($contentResults) + count($discussResults);
//	printc($totalResults." found");
	printc("<table width='100%' border=0 align = center cellpadding=4, cellspacing=0>");
	printc("</td><td></td></tr>");
	printc("<tr><td colspan=2 align='left' class='title'>Content containing '$search' ($totalResults results)</td></tr>");
//	printc("<tr><td>Site</td><td>Author</td></tr>");
	
//	foreach ($pageResults as $result) {
//		printPageItem($result);
//	}
	
	foreach ($contentResults as $result) {
	//	printpre($result);
		printContentItem($result);
	}
	
//	foreach ($discussResults as $result) {
//		printContentItem($result);
//	}


	printc("</table>");

}


function printContentItem($result) {
	global $_full_uri;
	//printpre($result);
		ob_start();
		print "<tr>";
		$record_tag_tstamp = timestamp2usdate($result[story_updated_tstamp]);
		//if ($record_tag_tstamp != '0000-00-00 00:00:00') {
			//$record_tag_tstamp =& TimeStamp::fromString($record_tag_tstamp);			
			//$record_tag_time =& $record_tag_tstamp->asTime();
		
			//print "<td valign='top' class='listtext'>".$record_tag_tstamp->ymdString()."<br/>".$record_tag_time->string12(false)."</td>";
		//	print "<td valign='top' class='listtext'>".$record_tag_tstamp."</td>";
			print "<td valign='top' class='listtext'><a href=".$_full_uri."/index.php?&action=site&site=".$_REQUEST[site];
			print "&section=".$result['section_id'];
			print "&page=".$result['page_id'];
			print "&story=".$result['story_id'];
			print "&detail=".$result['story_id'];
			print " target=new_window>";
			print stripslashes(urldecode($result['section_title']));
			print " > ".stripslashes(urldecode($result['page_title']));
			if ($result['story_title']) print " > ".stripslashes(urldecode($result['story_title']));
			print "</a></td></tr>";
			print "<tr><td valign='top' class='contentinfo'>added by".$result['user_fname']." on ".$record_tag_tstamp."</td>";
			print "</tr>";
			print "<tr>";
			print "<td class='list' colspan='2' valign='top' class='list'>";
		//	printpre($a['story_text_short']);
			$content = stripslashes(urldecode($result['story_text_short']));
			$content .= stripslashes(urldecode($result['story_text_long']));			
			//$content = find_abstract($content, $search);
			//print $content;
			print "</td>";
			print "</tr>";		
		//}
		$contentItem = ob_get_clean();
		printc($contentItem);  
}


function searchContent ($search, $site) {
	
	$terms = explode(" ", $search);
	foreach ($terms as $key => $term) {
		$terms[$key] = " LIKE ('%".addslashes($term)."%')";
	}
	
	$limit = 10;
	$query = "
		SELECT
			story_id, page_id, section_id, story_updated_tstamp,
			slot_name, page_title, section_title, story_title,
			story_text_short, story_text_long, user_fname, user_uname
		FROM
			slot
		INNER JOIN
		 	section
		 	ON section.FK_site = slot.FK_site
		INNER JOIN
			page
			ON FK_section = section_id
		INNER JOIN	
			story
			ON FK_page = page_id
		INNER JOIN
			user
			ON story.FK_createdby = user_id
		
		WHERE
			slot_name = '".addslashes($site)."'
		AND (
			";
	
	// Stories
	$query .= "\n\n(";
	foreach ($terms as $i => $term) {
		if ($i > 0) {
			$query .= "\n AND ";
		}
		$query .= "\n\t(";
		$query .= "story_text_short ".$term;
		$query .= "\n\tOR story_text_long ".$term;
		$query .= "\n\tOR story_title ".$term;
		$query .= ")";
	}
				
	
	$query .= "\n)";
	
	$query .= "
			)
		Order BY
			story_updated_tstamp DESC
		LIMIT 0, $limit
		";

	$r = db_query($query);
		
	if (db_num_rows($r) > 0) {
		$found_content = array();
		while ($a = db_fetch_assoc($r)) {
			$a[story_text_short] = stripslashes(urldecode($a['story_text_short']));
			$found_content[] = $a;			
		}
	}

	return $found_content;

}


?>