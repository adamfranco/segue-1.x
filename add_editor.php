<? // username_lookup.php
session_start();
ob_start();


if ($n) {
	//include("config.inc.php");
	//include("functions.inc.php");
	include("includes.inc.php");
	$usernames=userlookup($n,LDAP_BOTH,LDAP_WILD,LDAP_LASTNAME,0);
}

// sort alphabetically
if (count($usernames)) {
	asort($usernames);
	reset($usernames);
}

/* print "<pre>"; */
/* print_r($usernames); */
/* print "</pre>"; */

?>
<html>
<head>
<title>Add Editors</title>

<script lang='JavaScript'>
function addEditor(na) {
	f = document.lookup;
	o = opener.document.addform;
	if (na == '') {
		alert("You must enter a username, or search for one by pressing 'find'.");
	} else {
		o.edaction.value = 'add';
		o.edname.value = na;
		o.submit();
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
<form action="<? echo $PHP_SELF ?>" method=get name='lookup'>
<table cellspacing=1 width='100%'>

<tr>
	<th>Use</th>
	<th>Full Name</th>
	<th>Username</th>
</tr>
<tr>
	<td align=center><? if ($_SESSION[ltype]=='admin') print "<input type=button name='use' value='add' onClick='addEditor(document.lookup.n.value)'>"; else print "&nbsp;";?></td>
	<td>
		Name: <input type=text name='n' size=20 value='<?echo $n?>'> 
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
		print "<td align=center><input type=button name='use' value='add' onClick='addEditor(\"$u\")'></td>";
		print "<td>$f</td><td>$u</td>";
		print "</tr>";
		$c++;
	}
} else {
	print "<tr><td colspan=3>No usernames. Enter a name or part of a name above.</td></tr>";
}
?>
</table>
</form>
<div align=right>
<!-- <input type=button value='Add Editor' onClick='addEditor()'> -->
<input type=button value='Done' onClick='window.close()'></div>