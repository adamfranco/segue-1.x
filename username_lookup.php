<? // username_lookup.php

if ($name) {
/* 	include("config.inc.php"); */
/* 	include("functions.inc.php"); */
	include("includes.inc.php");
	$usernames=ldaplookup($name,LDAP_BOTH,LDAP_WILD,LDAP_LASTNAME,0);
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
<title>Username Lookup</title>

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

<table cellspacing=1 width='100%'>
<tr>
	<td colspan=3>
		<form action="<? echo $PHP_SELF ?>" method=get>
		Name: <input type=text name='name' size=20 value='<?echo $name?>'> <input type=submit value='GO'>
		</form>
	</td>
</tr>
<tr>
	<th>Num</th>
	<th>Full Name</th>
	<th>Username</th>
</tr>
<?
if (count($usernames)) {
	$c = 1;
	foreach ($usernames as $u=>$f) {
		if (!$u || $u=='') next;
		if (!ereg("[a-z]",$u)) next;
		print "<tr>";
		print "<td align=center>$c</td><td>$f</td><td>$u</td>";
		print "</tr>";
		$c++;
	}
} else {
	print "<tr><td colspan=3>No usernames. Enter a name or part of a name above.</td></tr>";
}
?>
</table><BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>