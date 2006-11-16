<? /* $Id$ */

include("objects/objects.inc.php");
$content = '';
$message = '';

ob_start();
session_start();



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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Username Lookup</title>

<? include("themes/common/logs_css.inc.php"); ?>

<?
?>
</head>
<body onload="document.searchform.name.focus()">

	<table cellspacing='1' width='100%' id='maintable'>
		<tr>
			<td colspan='3'>
				<form action="<? echo $PHP_SELF ?>" method='get' name='searchform'>
				Name: <input type='text' name='name' size='20' value='<?echo $name?>'/> <input type='submit' value='Find' />
				</form>
				<? if (!$usernames) print "No matching names found. Enter a name or part of a name above."; ?>
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
				print "\n\t\t<tr>";
				print "\n\t\t\t<td align='center'>$c</td>";
				print "\n\t\t\t<td>".htmlentities($f)."</td>";
				print "\n\t\t\t<td>".htmlentities($u)."</td>";
				print "\n\t\t</tr>";
				$c++;
			}
		} else {
			//print "<tr><td colspan='3'>No usernames. Enter a name or part of a name above.</td></tr>";
		}
		?>
		
	</table>
	<br />
	<div align='right'>
		<input type='button' value='Close Window' onclick='window.close()' />
	</div>

</body>
</html>