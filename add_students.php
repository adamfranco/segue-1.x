<? /* $Id$ */

require("objects/objects.inc.php");

ob_start();
session_start();

include("includes.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$ugroup_id = $_REQUEST[ugroup_id];

if ($_REQUEST[n]) {
	//include("config.inc.php");
	//include("functions.inc.php");
	$usernames=userlookup($_REQUEST[n],LDAP_BOTH,LDAP_WILD,LDAP_LASTNAME,0);
}

// sort alphabetically
if (count($usernames)) {
	asort($usernames);
	reset($usernames);
}

if ($action == "add" && $addstudent) {
	// make sure the user is in the db
	$valid = 0;
	foreach ($_auth_mods as $_auth) {
		$func = "_valid_".$_auth;
//		print "<BR>AUTH: trying ".$_auth ."..."; //debug
		if ($x = $func($addstudent,"",1)) {
			$valid = 1;
			break;
		}
	}
	
	if ($valid) {
		// Get their db id
		$user_id = db_get_value("user","user_id","user_uname='$addstudent'");
		
		// add them to the ugroup
		$query = "
			INSERT INTO
				ugroup_user
			SET
				FK_user=$user_id,
				FK_ugroup=$ugroup_id			
		";
		db_query($query);
	} else {
		error("invalid username");
	}
}

if ($action == "delete" && $delstudent) {
	$query = "
		DELETE
		FROM
			ugroup_user
		WHERE
			FK_ugroup = $ugroup_id
				AND
			FK_user = $delstudent	
	";
	db_query($query);
}

?>
<html>
<head>
<title>Add Students</title>

<script lang='JavaScript'>
function addStudent(na) {
	f = document.addform;
	if (na == '') {
		alert("You must enter a username, or search for one by pressing 'find'.");
	} else {
		f.action.value = 'add';
		f.addstudent.value = na;
		f.submit();
	}
}

function delStudent(id, name) {
	if (confirm("Are you sure you want to remove " + name + "?")) {
		f = document.delform;
		f.action.value = 'delete';
		f.delstudent.value = id;
		f.submit();
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
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>

<?
printerr2();
?>

<table cellspacing=1 width='100%'>

<tr>
	<th>Use</th>
	<th>Full Name</th>
	<th>Username</th>
</tr>
<tr>
	<td align=center>
	<? 
		if ($_SESSION[ltype]=='admin') 
			print "<input type=button name='use' value='add' onClick='addStudent(document.lookup.n.value)'>"; 
		else 
			print "&nbsp;";
	?>
	</td>
	<td>
		Name: <input type=text name='n' size=20 value='<?echo $_REQUEST[n]?>'> 
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
		print "<td align=center><input type=button name='use' value='add' onClick='addStudent(\"$u\")'></td>";
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

<?
$query = "
	SELECT
		user_id,
		user_fname,
		user_uname
	FROM
		ugroup_user
			INNER JOIN
		user
			ON
		FK_user = user_id
	WHERE
		FK_ugroup = $ugroup_id
";
$r = db_query($query);
?>

<p>

<table width=100%>
<tr>
	<th> </th>
	<th>Name</th>
</tr>
<?
while ($a = db_fetch_assoc($r)) {
	print "<tr>";
		print "<td align=center>";
			print "<input type=button name='use' value='remove' onClick=\"delStudent('".$a[user_id]."','".$a[user_fname]." (".$a[user_uname].")')\">";
		print "</td>";
		print "<td>";
			print $a[user_fname]." (".$a[user_uname].")";
		print "</td>";
	print "</tr>";
}

?>
</table>

<form name='addform'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<input type=hidden name='addstudent'>
<input type=hidden name="action">
</form>

<form name='delform'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<input type=hidden name='delstudent'>
<input type=hidden name="action">
<input type=hidden name="comingfrom" value="<? echo $comingfrom ?>">
</form>

<div align=right>
<!-- <input type=button value='Add Editor' onClick='addEditor()'> -->
<input type=button value='Done' onClick='window.close()'></div>

<?

// debug output -- handy :)
print "<pre>";
print "request:\n";
print_r($_REQUEST);
print "\n\n";
print "session:\n";
print_r($_SESSION);
print "\n\n"; 
print "</pre>";