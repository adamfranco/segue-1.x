<? /* $Id$ */

$pagetitle = "Delete Site";

$s = new site($_REQUEST[name]);
$s->fetchDown();
//print_r($s);

if (sitenamevalid($_REQUEST[name])) {
	if ($_REQUEST[confirm]) {
		$s->delete();
		log_entry("delete_site","$_SESSION[auser] deleted site $_REQUEST[name]","$_REQUEST[name]");
	} else {
		printc("Are you <b>SURE</b> you want to delete the site <i>".$s->getField("title")."</i>?? This operation is <b>irreversable</b>. You will <b>never</b> see any of the content of this site again, including all sections, pages, content, and discussions. You better be <b>ABSOLUTELY SURE</b> you want to do this! If so, hit 'Delete'.<br><br>");
		printc("<form action='$PHP_SELF?$sid&action=delete_site&name=$_REQUEST[name]'><input type=hidden name=confirm value=1>");
		printc("<input type=hidden name='name' value='$name'>");
		printc("<input type=hidden name=action value=delete_site>");
		printc("<input type=button value='&lt;&lt; Back' onClick='history.go(-1)'> <input type=submit value='Delete'>");
		printc("</form>");
		return;
	}
} else log_entry("delete_site","$_SESSION[auser] deleting site $_REQUEST[name] failed","$_REQUEST[name]");

header("Location: $PHP_SELF?$sid");