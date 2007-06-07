<?


//printpre($_REQUEST);

if (!$_REQUEST[nostatus]) {
	print "<form action='$PHP_SELF?&site=".$_REQUEST[site]."$section=".$_REQUEST[section]."page=".$_REQUEST[page];
	foreach ($_GET as $key => $val) {
		print "&amp;".$key."=".$val;
	}
	print "' id='search' name='search' method='get'>\n";
	print "\n\t<div class='headerbox small' align='center'>\n";

	//print "Login";
	//printpre($name);
	print " \n\t\t<input type='text' class='textfield small' name='search' size='9' value='".(($_REQUEST['search'])?$_REQUEST['search']:'')."'/>";
//	print "<input type='hidden' name='search' value='1'/>\n";
	//print "<input type='hidden' name='getquery' value='".urlencode($QUERY_STRING)."'/>\n";
	//print "<input type='hidden' name='gotourl' value='".urlencode($REQUEST_URI)."'/>\n";
	print "\n\t\t<input type='hidden' name='site' value='".$_REQUEST[site]."'/>";
	print "\n\t\t<input type='hidden' name='section' value='".$_REQUEST[section]."'/>";
	print "\n\t\t<input type='hidden' name='page' value='".$_REQUEST[page]."'/>";
	print "\n\t\t<input type='hidden' name='action' value='".$_REQUEST[action]."'/>";
	print "\n\t\t<input type='submit' class='button small' name='button' value='Find'/><br />";
	print "\n\t</div>\n";
	print "\n</form>\n";
}
?>
