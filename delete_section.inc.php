<? /* $Id$ */

/* $sections = decode_array(db_get_value("sites","sections","name='$site'")); */

/* $s = new section($_REQUEST[site],$_REQUEST[delete_section]); */
/* $s->fetchFromDB(); */

if (/* in_array($delete_section,$sections) &&  */permission($_SESSION[auser],SITE,DELETE,$_REQUEST[site])) {
	$thisSite->delSection($_REQUEST[delete_section]);
	$thisSite->updateDB();
/* 	$pages = decode_array(db_get_value("sections","pages","id=$delete_section")); */
/* 	$query = "delete from sections where id=$delete_section"; */
/* 	db_query($query); // delete the section entry */
/* 	$newsections = array(); */
/* 	foreach($sections as $se) { */
/* 		if ($se != $delete_section) array_push($newsections, $se); */
/* 	} */
/* 	$sections = encode_array($newsections); */
/* 	$query = "update sites set sections='$sections' where name='$site'"; */
/* 	db_query($query); // update the sections array in the site entry */
/* 	 */
/* 	// now delete all associated pages and stories and discussions */
/* 	 */
/* 	foreach ($pages as $p) { */
/* 		$stories = decode_array(db_get_value("pages","stories","id=$p")); */
/* 		db_query("delete from pages where id=$p"); */
/* 		foreach ($stories as $s) { */
/* 			$type = db_get_value("stories","type","id=$s"); */
/* 			if ($type == 'file' || $type=='image') */
/* 				deleteuserfile($s,urldecode(db_get_value("stories","longertext","id=$s"))); */
/* 			db_query("delete from stories where id=$s"); */
/* 		} */
/* 	} */
/* 	// done; */
	log_entry("delete_section","$_SESSION[auser] deleted section id $_REQUEST[delete_section]",$_REQUEST[site]);
} else log_entry("delete_section","$_SESSION[auser] deleting section id $_REQUEST[delete_section] failed",$_REQUEST[site],$_REQUEST[delete_section]);

header("Location: $PHP_SELF?$sid&site=$_REQUEST[site]&action=viewsite");