<? // delete_section.inc.php -- deletes an entire section

$pagetitle = "Delete Site";

$a = db_get_line("sites","name='$name'");

if (sitenamevalid($name)) {
	if ($confirm) {
		$sections = decode_array(db_get_value("sites","sections","name='$name'"));
		foreach ($sections as $section) {
			
			$pages = decode_array(db_get_value("sections","pages","id=$section"));
			$query = "delete from sections where id=$section";
			db_query($query); // delete the section entry
			// now delete all associated pages and stories and discussions
			foreach ($pages as $p) {
				$stories = decode_array(db_get_value("pages","stories","id=$p"));
				db_query("delete from pages where id=$p");
				foreach ($stories as $s) {
					$type = db_get_value("stories","type","id=$s");
					$site = $name;
//					if ($type == 'file' || $type=='image')
//						deleteuserfile($s,urldecode(db_get_value("stories","longertext","id=$s")));
					db_query("delete from stories where id=$s");
				}
			}
		}
		
		// remove the userfiles, their media table entries, and the userfiles directory
		$r = db_query("select * from media where site_id='$name'");
		while ($a = db_fetch_assoc($r)) {
			deleteuserfile($a[id]);
		}
		deleteComplete($uploaddir."/".$name);
		
		db_query("delete from sites where name='$name'");
		// done;
		log_entry("delete_site","$name","","","$auser deleted site $name");
	} else {
		printc("Are you <b>SURE</b> you want to delete the site <i>$a[title]</i>?? This operation is <b>irreversable</b>. You will <b>never</b> see any of the content of this site again, including all sections, pages, content, and discussions. You better be <b>ABSOLUTELY SURE</b> you want to do this! If so, hit 'Delete'.<br><br>");
		printc("<form action='$PHP_SELF?$sid&action=delete_site&name=$name'><input type=hidden name=confirm value=1>");
		printc("<input type=hidden name='name' value='$name'>");
		printc("<input type=hidden name=action value=delete_site>");
		printc("<input type=button value='&lt;&lt; Back' onClick='history.go(-1)'> <input type=submit value='Delete'>");
		printc("</form>");
		return;
	}
} else log_entry("failed: delete_site","$name","","","$auser deleting site $name");

header("Location: $PHP_SELF?$sid");