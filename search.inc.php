<? /* $Id$ */

require_once("objects/objects.inc.php");
//require_once(dirname(__FILE__)."/objects/String.class.php");
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

//if ($tmp = $_REQUEST['order']) {
//	$_SESSION['order'] = $_REQUEST['order'];
//}

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
//$site_owner =$siteObj->owner;
//print_r($story->owningSiteObj);
//print $site_owner;
//printpre($site_owner);

if ($_REQUEST['search']) {
	$search = $_REQUEST['search'];

		
//	$pageResults = searchPages($search);
	$sectionResults = searchSections($_REQUEST['search'], $_REQUEST[site]);
	$pageResults = searchPages($_REQUEST['search'], $_REQUEST[site]);
	$contentResults = searchContent($_REQUEST['search'], $_REQUEST[site]);
	$discussResults = searchDiscussions($_REQUEST['search'], $_REQUEST[site]);

	/******************************************************************************
	 * Print out search results
	 ******************************************************************************/

	
	$totalResults = count($pageResults) + count($contentResults) + count($discussResults);
//	printc($totalResults." found");
	printc("<table width='100%' border='0' align='center' cellpadding='4' cellspacing='0'>");
	printc("\n\t<tr><td></td><td></td></tr>");
	printc("\n\t<tr><td colspan='2' align='left' class='title'>Found: '$search'</td></tr>");
//	printc("<tr><td>Site</td><td>Author</td></tr>");
	
	if (count($sectionResults) > 0) {
		printc("\n\t<tr><td class='title2' >Section titles containing: '$search'</td><td></td></tr>");	
		foreach ($sectionResults as $result) {
			printContentItem($result, "section", $siteObj, $search);
		}
	}
 
 if (count($pageResults) > 0) {
		printc("\n\t<tr><td class='title2' >Pages titles containing: '$search'</td><td></td></tr>");	
		foreach ($pageResults as $result) {
			printContentItem($result, "page", $siteObj, $search);
		}
	}
	
	if (count($contentResults) > 0) {
		printc("\n\t<tr><td class='title2' >'$search' in text</td><td></td></tr>");	
		foreach ($contentResults as $result) {
			printContentItem($result, "content", $siteObj, $search);
		}
	}

	if (count($discussResults) > 0) {
		printc("\n\t<tr><td class='title2' >'$search' in discussions</td><td></td></tr>");		
		foreach ($discussResults as $result) {
			printContentItem($result, "discussion", $siteObj);
		}
	}


	printc("</table>");

}


function printContentItem($result, $type, & $siteObj, $search="") {
	global $_full_uri, $site_owner;
		$foundSection =& new section($_REQUEST[site],$result['section_id'], $siteObj);
		$foundPage =& new page($_REQUEST[site],$result['section_id'], $result['page_id'], $foundSection);
		$foundContent =& new story($_REQUEST[site], $result['section_id'], $result['page_id'], $result['story_id'], $foundPage);

		if (($foundSection->canview() && $foundPage->canview() && $foundContent->canview()) || $_SESSION[auser] == $site_owner) {
			ob_start();
			print "\n\t<tr>";
			$record_tag_tstamp = timestamp2usdate($result[story_updated_tstamp]);
			//if ($record_tag_tstamp != '0000-00-00 00:00:00') {
				//$record_tag_tstamp =& TimeStamp::fromString($record_tag_tstamp);			
				//$record_tag_time =& $record_tag_tstamp->asTime();
			
				//print "<td valign='top' class='listtext'>".$record_tag_tstamp->ymdString()."<br/>".$record_tag_time->string12(false)."</td>";
			//	print "<td valign='top' class='listtext'>".$record_tag_tstamp."</td>";
				print "\n\t\t<td valign='top'><a href='".$_full_uri."/index.php?&amp;action=site&amp;site=".$_REQUEST[site];
				print "&amp;section=".$result['section_id'];
				print "&amp;page=".$result['page_id'];
				print "&amp;story=".$result['story_id'];
				print "&amp;detail=".$result['story_id'];
				if ($type == "discussion") {
					print "#".$result['discussion_id'];
				}
				print "'>";
				print stripslashes(urldecode($result['section_title']));
				print " > ".stripslashes(urldecode($result['page_title']));
				if ($result['story_title']) print " > ".stripslashes(urldecode($result['story_title']));
				if ($result['discussion_subject']) {
					print " > ".stripslashes(urldecode($result['discussion_subject']));
					print " (".$result['user_fname'].")";
				}
				print "</a>\n\t\t</td>";
			//	print "<tr><td valign='top' class='contentinfo'>added by".$result['user_fname']." on ".$record_tag_tstamp."</td>";
				print "\n\t</tr>";
				print "\n\t\t<tr>";
				print "\n\t\t\t<td class='list' valign='top'>";
				if ($type == "content") {
					$content = stripslashes(urldecode($result['story_text_short']));
					$content .= stripslashes(urldecode($result['story_text_long']));
				} else if ($type == "discussion") {
					$content = stripslashes(urldecode($result['discussion_content']));
				}
				$wikiResolver =& WikiResolver::instance();
				$content = $wikiResolver->parseText($content, $_REQUEST[site], $result['section_id'], $result['page_id']);
	
				$content = find_abstract($content, $search);
				if ($type != "page" && $type != "section") {
					print $content;
				}
				print "\n\t\t</td>";
				print "\n\t</tr>";		
			//}
			$contentItem = ob_get_clean();
			printc($contentItem);
		}
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
		
	//printpre($query);
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

function searchDiscussions ($search, $site) {

	$terms = explode(" ", $search);
	foreach ($terms as $key => $term) {
		$terms[$key] = " LIKE ('%".addslashes($term)."%')";
	}

	$limit = 10;
	$query = "
		SELECT
			discussion_content, discussion_tstamp, discussion_subject, discussion_id, user_fname,
			story_id, story_title, page_title, section_title, page_id, section_id, 
			FK_author, user_uname
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
			discussion
			ON story_id = FK_story
		INNER JOIN
			user
				ON discussion.FK_author = user_id 				
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
		$query .= "discussion_content ".$term;
		$query .= "\n\tOR discussion_subject ".$term;
		$query .= "\n\tOR user_fname ".$term;
		$query .= ")";
	}
				
	
	$query .= "\n)";
	
	$query .= "
			)
		Order BY
			 discussion_tstamp  DESC
		LIMIT 0, $limit
		";

	//printpre($query);
	$r = db_query($query);
		
	if (db_num_rows($r) > 0) {
		$found_discussions = array();
		while ($a = db_fetch_assoc($r)) {
			$a[story_text_short] = stripslashes(urldecode($a['story_text_short']));
			$found_discussions[] = $a;			
		}
	}

	return $found_discussions;


}

function searchPages ($search, $site) {

	$terms = explode(" ", $search);
	foreach ($terms as $key => $term) {
		$terms[$key] = " LIKE ('%".addslashes($term)."%')";
	}

	$limit = 10;
	$query = "
		SELECT
			page_title, page_updated_tstamp, section_title, page_id, section_id
		FROM
			slot
		INNER JOIN
		 	section
		 	ON section.FK_site = slot.FK_site
		INNER JOIN
			page
			ON FK_section = section_id 				
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
		$query .= "page_title ".$term;
		$query .= ")";
	}
				
	
	$query .= "\n)";
	
	$query .= "
			)
		Order BY
			 page_updated_tstamp  DESC
		LIMIT 0, $limit
		";

	//printpre($query);
	$r = db_query($query);
		
	if (db_num_rows($r) > 0) {
		$found_pages = array();
		while ($a = db_fetch_assoc($r)) {
			$a[page_title] = stripslashes(urldecode($a['page_title']));
			$found_pages[] = $a;			
		}
	}
	return $found_pages;
}

function searchSections ($search, $site) {

	$terms = explode(" ", $search);
	foreach ($terms as $key => $term) {
		$terms[$key] = " LIKE ('%".addslashes($term)."%')";
	}

	$limit = 10;
	$query = "
		SELECT
			section_title, section_updated_tstamp, section_title, section_id
		FROM
			slot
		INNER JOIN
		 	section
		 	ON section.FK_site = slot.FK_site				
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
		$query .= "section_title ".$term;
		$query .= ")";
	}
				
	
	$query .= "\n)";
	
	$query .= "
			)
		Order BY
			 section_updated_tstamp  DESC
		LIMIT 0, $limit
		";

	//printpre($query);
	$r = db_query($query);
		
	if (db_num_rows($r) > 0) {
		$found_sections = array();
		while ($a = db_fetch_assoc($r)) {
			$a[section_title] = stripslashes(urldecode($a['section_title']));
			$found_sections[] = $a;			
		}
	}
	return $found_sections;
}

function find_abstract ($content, $search, $numwords=25) {
	$content = eregi_replace("<br>", "..", $content);
	$content = eregi_replace("<br \/>", "..", $content);			
//	$content = eregi_replace("\[\[linkpath\]\]", "...", $content);
	$search = " ".$search;
	
	$htmlstring =& HtmlString::withValue($content);
	$content = $htmlstring->stripTagsAndTrim(500);
	$lowercontent = strtolower($content);
	$searchstart = strpos($lowercontent, $search);
	
	if ($searchstart < 60) {
		$searchbegin = 0;
		$searchend = 150;
	} else if ($searchstart == 0) {
		$searchbegin = 0;
		$searchend = 150;
	} else {
		$searchbegin = $searchstart - 50;
		$searchend = $searchstart + 150;
	}
				
	$content = substr($content, $searchbegin, 500);
	
	$htmlstring =& HtmlString::withValue($content);
	$content = $htmlstring->stripTagsAndTrim(25);
	
	$clean = explode(" ", $content);
	
	$clean_content = "";
	foreach ($clean as $word) {
		if (strlen($word) > 50) $word = substr($word, 0, 50)."...";
		$clean_content .= $word." "; 
	}
	
	$search_term = "<span class='foundtext'>".$search."</span>";
	$clean_content = eregi_replace($search, $search_term, $clean_content);
				
	if ($searchstart < 40) {
		$clean_content = $clean_content."...";
	} else {
		$clean_content = "...".$clean_content."...";
	}
	
	return $clean_content;

}
?>