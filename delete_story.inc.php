<? /* $Id$ */

/* $stories = decode_array(db_get_value("pages","stories","id=$page")); */
/*  */
/* print count($stories) . " stories in array<BR>"; */
/* print permission($auser,PAGE,DELETE,$page) . " permission<BR>"; */
/* print "$site_owner is the owner. should be ". db_get_value("sites","addedby","name='$site'") ."<BR>"; */

if (/* in_array($delete_story,$stories) && permission($auser,PAGE,DELETE,$page) */1) {
	$thisPage->delStory($_REQUEST[delete_story]);
	$thisPage->updateDB();
/* 	$a = db_get_line("stories","id=$delete_story"); */
/* 	if ($a[type] != 'story') { */
/* //		deleteuserfile($delete_story,urldecode($a[longertext])); */
/* 	} */
/* 	$query = "delete from stories where id=$delete_story"; */
/* 	db_query($query); */
/* 	$newstories = array(); */
/* 	foreach ($stories as $s) { */
/* 		if ($s != $delete_story) array_push($newstories,$s); */
/* 	} */
/* 	$stories = encode_array($newstories); */
/* 	$query = "update pages set stories='$stories' where id=$page"; */
/* 	db_query($query); */
	log_entry("delete_story","$_SESSION[auser] deleted story id $_REQUEST[delete_story]",$_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story]);
} else log_entry("delete_story","$_SESSION[auser] deleting story id $_REQUEST[delete_story] failed",$_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story]);

header("Location: $PHP_SELF?$sid&site=$_REQUEST[site]&section=$_REQUEST[section]&page=$_REQUEST[page]&action=viewsite");