<? /* $Id$ */

/* $pages = decode_array(db_get_value("sections","pages","id=$section")); */
/* $p = new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[delete_page]); */
/* $p->fetchFromDB(); */

if ($thisSection->hasPermission("delete",$_SESSION[auser])) {
	$thisSection->delPage($_REQUEST[delete_page]);
	$thisSection->updateDB();
/* 	$stories = decode_array(db_get_value("pages","stories","id=$delete_page")); // get stories to delete */
/* 	$query = "delete from pages where id=$delete_page"; */
/* 	db_query($query); // delete the story entry */
/* 	// now remove the entry from the section's pages array */
/* 	$newpages = array(); */
/* 	foreach ($pages as $p) { */
/* 		if ($p != $delete_page) array_push($newpages,$p); */
/* 	} */
/* 	$pages = encode_array($newpages); */
/* 	$query = "update sections set pages='$pages' where id=$section"; */
/* 	db_query($query); */
/* 	 */
/* 	// now delete all of the stories associated with the page */
/* 	foreach ($stories as $s) { */
/* 		$type = db_get_value("stories","type","id=$s"); */
/* 		if ($type == 'file' || $type=='image') */
/* 			deleteuserfile($s,urldecode(db_get_value("stories","longertext","id=$s"))); */
/* 		db_query("delete from stories where id=$s"); */
/* 	} */
	log_entry("delete_page","$_SESSION[auser] deleted page id $_REQUEST[delete_page]",$_REQUEST[section],"section");
} else log_entry("delete_page","$_SESSION[auser] deleting page id $_REQUEST[delete_page] failed",$_REQUEST[delete_page],"page");

header("Location: $PHP_SELF?$sid&site=$_REQUEST[site]&section=$_REQUEST[section]&action=viewsite");