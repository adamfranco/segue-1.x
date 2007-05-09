<?


//printpre($_REQUEST);

if (!$_REQUEST[nostatus]) {
	print "<form action='$PHP_SELF?&site=".$_REQUEST[site]."$section=".$_REQUEST[section]."page=".$_REQUEST[page];
	foreach ($_GET as $key => $val) {
		print "&amp;".$key."=".$val;
	}
	print "' id='search' name='search' method='get'>\n";
	print "<div class='headerbox small' align='center'>\n";

	//print "Login";
	//printpre($name);
	print " <input type='text' class='textfield small' name='search' size='9' value='".(($_REQUEST['search'])?$_REQUEST['search']:'')."'/> \n";
//	print "<input type='hidden' name='search' value='1'/>\n";
	//print "<input type='hidden' name='getquery' value='".urlencode($QUERY_STRING)."'/>\n";
	//print "<input type='hidden' name='gotourl' value='".urlencode($REQUEST_URI)."'/>\n";
	print "<input type='hidden' name='site' value='".$_REQUEST[site]."'/>\n";
	print "<input type='hidden' name='section' value='".$_REQUEST[section]."'/>\n";
	print "<input type='hidden' name='page' value='".$_REQUEST[page]."'/>\n";
	print "<input type='hidden' name='action' value='".$_REQUEST[action]."'/>\n";
	print "<input type='submit' class='button small' name='button' value='Find'/><br />\n";
	print "</div>\n";
	print "</form>\n";
}
?>
