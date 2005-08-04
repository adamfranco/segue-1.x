<? /* $Id$ */
//error_reporting(E_ALL && ~E_NOTICE);
require("objects/objects.inc.php");

ob_start();
session_start();

if ($_REQUEST[n]) {
	//include("config.inc.php");
	//include("functions.inc.php");
	include("includes.inc.php");
	$usernames=userlookup($_REQUEST[n],LDAP_BOTH,LDAP_WILD,LDAP_LASTNAME,0);
}

// sort alphabetically
if (count($usernames)) {
//	$usernames = array_change_key_case($usernames, CASE_LOWER);
	asort($usernames);
	reset($usernames);
}

/* print "<pre>"; */
/* print_r($usernames); */
/* print "</pre>"; */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add Editors</title>

<script lang='JavaScript'>
function addEditor(na) {
	f = document.lookup;
	o = opener.document.addform;
	if (na == '') {
		alert("You must enter a username, or search for one by pressing 'find'.");
	} else {
	<? if ($comingfrom == "add_slot") { ?>
		o.owner.value = na;
		window.close();
	<? } else if ($comingfrom == "classes") { ?>
		o.owner.value = na;
		window.close();
	<? } else { ?>
		o.edaction.value = 'add';
		o.edname.value = na;
		o.submit();
	<? } ?>
	}
}
</script>

<style type='text/css'>
table {
	border: 1px solid #555;
}

th, td {
	border: 0px;
	background-color: #ddd;
}

th { 
	background-color: #ccc; 
	font-variant: small-caps;
}

body { 
	background-color: white; 
}

body, table, td, th, input {
	font-size: 12px;
	font-family: "Verdana", "sans-serif";
}

input {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

</style>
<body onload="document.lookup.n.focus()">
<form action="<? echo $PHP_SELF ?>" method=get name='lookup'>

<table cellspacing=1 width='100%'>

<tr>
	<th>Use</th>
	<th>Full Name</th>
	<th>Username</th>
</tr>
<tr>
	<td align='center'>
	<? 
		if ($_SESSION[ltype]=='admin') 
			print "<input type=button name='use' value='add' onClick='addEditor(document.lookup.n.value)'>"; 
		else 
			print "&nbsp;";
	?>
	</td>
	<td>
		Name: <input type='text' name='n' size=20 value='<?echo $_REQUEST[n]?>'> 
	</td>
	<td>
		<input type=submit value='find'>
	</td>
</tr>
<?
if (count($usernames)) {
	$c = 1;
	foreach ($usernames as $u=>$f) {
		if (!$u || $u=='') next;
		if (!ereg("[a-z]",$u)) next;
		$u = strtolower($u);
		print "<tr>";
		print "<td align='center'><input type=button name='use' value='add' onClick='addEditor(\"$u\")'></td>";
		print "<td>$f</td><td>$u</td>";
		print "</tr>";
		$c++;
	}
} else {
	print "<tr><td colspan=3>No usernames. Enter a name or part of a name above.</td></tr>";
}
?>
</table>

<input type=hidden name="comingfrom" value="<? echo $_REQUEST[comingfrom] ?>">
</form>
<div align='right'>
<!-- <input type=button value='Add Editor' onClick='addEditor()'> -->
<input type=button value='Done' onClick='window.close()'></div>

<?

// debug output -- handy :)
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n";  */
/* print "</pre>"; */