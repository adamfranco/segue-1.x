<? /* $Id$ */

include("objects/objects.inc.php");
$content = '';
$message = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';


// include all necessary files
include("includes.inc.php");

if ($name) {
	$usernames=userlookup($name,LDAP_BOTH,LDAP_WILD,LDAP_LASTNAME,0);
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

<? include("themes/common/logs_css.inc.php"); ?>

<?=($_SESSION['ltype']=='admin')?"<div align=right>user lookup | <a href='users.php?$sid'>add/edit users</a> | <a href='classes.php?$sid'>add/edit classes</a></div>":""?>

<table cellspacing=1 width='100%' id='maintable'>
<tr><td>
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
</table>
</td></tr></table>
<BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>