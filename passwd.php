<? /* $Id$ */

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// include all necessary files
include("includes.inc.php");

if ($_REQUEST[submit]) {
	$db_pass = db_get_value("user","user_pass","user_uname = '$_SESSION[auser]'");
	$origPassValid = !strcmp($_REQUEST[oldpass],$db_pass);
	if ($origPassValid) {
		$oldpass = $_REQUEST[oldpass];
		$passwordsMatch = !strcmp($_REQUEST[newpass1],$_REQUEST[newpass2]);
		if ($passwordsMatch) {
			$validLength = ereg(".{8,200}",$_REQUEST[newpass1]);
			if ($validLength) {
				$validChars = !ereg("[\'\"]",$_REQUEST[newpass1]);
				if ($validChars) {
					$passwordGood = 1;
					$query = "UPDATE user SET user_pass='$_REQUEST[newpass1]' where user_uname='$_SESSION[auser]'";
					db_query($query);
				} else {
					unset($newpass1);
					unset($newpass2);
					error("New password contains prohibited characters");
				}
			} else {
				unset($newpass1);
				unset($newpass2);
				error("New password is not between 8 and 200 characters");
			}
		} else {
			unset($newpass1);
			unset($newpass2);
			error ("New passwords don't match");
		}
	} else {
		unset($oldpass);
		error ("Your old password is invalid");
	}
}



?>
<html>
<head>
<title>Change Password</title>

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

<?
printerr2();
?>

<form action="<? echo $PHP_SELF ?>" method=post>
<table cellspacing=1 width='100%'>
<tr>
	<th colspan=2>Change Password</th>
</tr>
<?
if ($passwordGood) {
?>

<tr>
	<td>
		Your password has been changed.
	</td>
</tr>

<? } else { ?>
<tr>
	<td>
		User Name: 
	</td>
	<td>
		<input type=text name='uname' size=30 value='<? echo $_SESSION[auser] ?>' readonly> 
	</td>
</tr>
<tr>
	<td>
		Full Name: 
	</td>
	<td>
		<input type=text name='fname' size=30 value='<? echo $_SESSION[afname] ?>' readonly> 
	</td>
</tr>
<tr>
	<td>
		Email Address: 
	</td>
	<td>
		<input type=text name='email' size=30 value='<?echo $_SESSION[aemail] ?>' readonly> 
	</td>
</tr>
<tr>
	<td>
		Old Password: 
	</td>
	<td>
		<input type=password name='oldpass' size=30 value='<?echo $oldpass?>'> 
	</td>
</tr>
<tr>
	<td>
		New Password: 
	</td>
	<td>
		<input type=password name='newpass1' size=30 value='<?echo $newpass1?>'> <span style='color: #a00'>*</span>
	</td>
</tr>
<tr>
	<td>
		Again: 
	</td>
	<td>
		<input type=password name='newpass2' size=30 value='<?echo $newpass2?>'>  <span style='color: #a00'>*</span>
	</td>
</tr>
<tr>
	<td colspan=2>
		<span style='color: #a00'>* Must be 8-200 characters long and not contain any of the following: " '</span> 
	</td>
</tr>
<tr>
	<td colspan=2 align=center>
		<input type=submit name='submit' value='Change Password'>
	</td>
</tr>
<? } ?>

</table><BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>

<? 
// debug output -- handy :)
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */

/* if (is_object($thisPage)) { */
/* 	print "\n\n"; */
/* 	print "thisPage:\n"; */
/* 	print_r($thisPage); */
/* } else if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
/* print "</pre>"; */
?>