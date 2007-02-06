<?php


if ($thisSection) $section=$thisSection->id;


/******************************************************************************
 * Reordering of pages
 ******************************************************************************/

if ($_REQUEST['reorderPage']) {
	$page_location = db_get_value("page", "page_location", "page_id='".addslashes($_REQUEST['reorderPage'])."'");
	if (!isset($page_location)) $page_location = "left"; 
	
	
	$orderedSetLeft = new OrderedSet(null);
	$orderedSetRight = new OrderedSet(null);
	
	$query = "
		SELECT
			page_id, page_order, page_location, page_title
		FROM
			page
		WHERE
			FK_section = '".addslashes($section)."'
		ORDER BY page_order			
	";
	
	printpre($query);
	$r = db_query($query);
	
	// Populate the Set with the original page order
	while ($a=db_fetch_assoc($r)) {
		printpre($a['page_order']."-".$a['page_id']."-".$a['page_title']."-".$a['page_location']);
		
		if ($a['page_location'] == 'right') {
			$orderedSetRight->addItem($a['page_id']);	
		} else {
			$orderedSetLeft->addItem($a['page_id']);	
		}
	}
			
	// Move our page to its new position
	if ($page_location == 'right')
		$orderedSetRight->moveToPosition($_REQUEST['reorderPage'], $_REQUEST['newPosition']);
	else
		$orderedSetLeft->moveToPosition($_REQUEST['reorderPage'], $_REQUEST['newPosition']);
		
	// Save the new order
	$orderedSetLeft->reset();		// Make sure the iterator is at the begining.
	$orderedSetRight->reset();
	
	$order = 0;
	while ($orderedSetLeft->hasNext()) {
		$item = $orderedSetLeft->next();
		
		// Update the db
		$query = "
			UPDATE
				page
			SET
				page_order =  '".addslashes($order)."'
			WHERE
				page_id = '".addslashes($item)."'
		";
		
		printpre($query);
		$r = db_query($query);			
		$order++;
	}
	while ($orderedSetRight->hasNext()) {
		$item = $orderedSetRight->next();
		
		// Update the db
		$query = "
			UPDATE
				page
			SET
				page_order =  '".addslashes($order)."'
			WHERE
				page_id = '".addslashes($item)."'
		";
		
		printpre($query);
		$r = db_query($query);			
		$order++;
	}
	$showorder = "page";

/******************************************************************************
 * Reordering of content blocks
 ******************************************************************************/
	
} else if ($_REQUEST['reorderContent']) {

	$orderedSet = new OrderedSet(null);
	$query = "
		SELECT
			story_id, story_order
		FROM
			story
		WHERE
			FK_page = '".addslashes($page)."'
		ORDER BY story_order			
	";
	
//	printpre($query);
	$r = db_query($query);
	
	// Populate the Set with the original page order
	while ($a=db_fetch_assoc($r)) {
		printpre($a['story_order']."-".$a['story_id']);			
		$orderedSet->addItem($a['story_id']);		
	}
			
	// Move our page to its new position
	$orderedSet->moveToPosition($_REQUEST['reorderContent'], $_REQUEST['newPosition']);
		
	// Save the new order
	$orderedSet->reset();		// Make sure the iterator is at the begining.
	$order = 0;
	while ($orderedSet->hasNext()) {
		$item = $orderedSet->next();
		printpre($order."-".$item);
		
		// Update the db
		$query = "
			UPDATE
				story
			SET
				story_order =  '".addslashes($order)."'
			WHERE
				story_id = '".addslashes($item)."'
		";
		
		//printpre($query);
		$r = db_query($query);			
		$order++;
	}

	$showorder = "story";

/******************************************************************************
 * Reordering of sections
 ******************************************************************************/

} else if ($_REQUEST['reorderSection']) {
	$site_id = db_get_value("slot", "FK_site", "slot_name='".addslashes($_REQUEST['site'])."'");
	
	$orderedSet = new OrderedSet(null);
	$query = "
		SELECT
			section_id, section_order
		FROM
			section
		WHERE
			FK_site = '".addslashes($site_id)."'
		ORDER BY section_order			
	";
	
	printpre($query);
	$r = db_query($query);
	
	// Populate the Set with the original page order
	while ($a=db_fetch_assoc($r)) {
		printpre($a['section_order']."-".$a['section_id']);			
		$orderedSet->addItem($a['section_id']);		
	}
			
	// Move our page to its new position
	$orderedSet->moveToPosition($_REQUEST['reorderSection'], $_REQUEST['newPosition']);
		
	// Save the new order
	$orderedSet->reset();		// Make sure the iterator is at the begining.
	$order = 0;
	while ($orderedSet->hasNext()) {
		$item = $orderedSet->next();
		printpre($order."-".$item);
		
		// Update the db
		$query = "
			UPDATE
				section
			SET
				section_order =  '".addslashes($order)."'
			WHERE
				section_id = '".addslashes($item)."'
		";
		
		//printpre($query);
		$r = db_query($query);			
		$order++;
	}
	$showorder = "section";

}

// exit;

$returnURL = $_SERVER['PHP_SELF']."?&action=viewsite&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&showorder=$showorder".(($_REQUEST['story_set'])?"&story_set=".$_REQUEST['story_set']:"");
//printpre($returnURL);

header("Location: ".$returnURL);
exit;