<? // delete_page.inc.php -- delete a page

$pages = decode_array(db_get_value("sections","pages","id=$section"));

if (in_array($delete_page,$pages) && permission($auser,SECTION,DELETE,$section)) {
	$stories = decode_array(db_get_value("pages","stories","id=$delete_page")); // get stories to delete
	$query = "delete from pages where id=$delete_page";
	db_query($query); // delete the story entry
	// now remove the entry from the section's pages array
	$newpages = array();
	foreach ($pages as $p) {
		if ($p != $delete_page) array_push($newpages,$p);
	}
	$pages = encode_array($newpages);
	$query = "update sections set pages='$pages' where id=$section";
	db_query($query);
	
	// now delete all of the stories associated with the page
	foreach ($stories as $s) {
		$type = db_get_value("stories","type","id=$s");
		if ($type == 'file' || $type=='image')
			deleteuserfile($s,urldecode(db_get_value("stories","longertext","id=$s")));
		db_query("delete from stories where id=$s");
	}
	log_entry("delete_page","$auser deleted page id $delete_page");
} else log_entry("failed: delete_page","$auser deleting page id $delete_page");

header("Location: $PHP_SELF?$sid&site=$site&section=$section&action=viewsite");