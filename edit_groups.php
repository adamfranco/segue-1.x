<? /* $Id$ */

include("objects/objects.inc.php");
$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

/* db_connect($dbhost, $dbuser, $dbpass, $dbdb); */

if ($del = $_REQUEST[del]) { // we're deleting something
//	print $del;
	if ($del=='group') {
		$query = "delete from classgroups where id=$group";
		log_entry("classgroups",db_get_value("classgroups","name","id=$group"),"","","$auser removed group ".db_get_value("classgroups","name","id=$group"));
	}
	if ($del=='class') {
		$list = explode(",",db_get_value("classgroups","classes","id=$group"));
		$nlist = array();
		foreach ($list as $l) {
			if ($l != $class)
				$nlist[]=$l;
		}
		$list = implode(",",$nlist);
		$query = "update classgroups set classes='$list' where id=$group";
		log_entry("classgroups",db_get_value("classgroups","name","id=$group"),"","","$auser removed $class from group ".db_get_value("classgroups","name","id=$group"));
	}
//	print $query;
	db_query($query);
	print "<script lang='JavaScript'>function updater() { opener.window.location=\"index.php?$sid\"; }</script>";
}
print mysql_error();
$r = db_query("select * from classgroups where owner='$auser'");

?>
<html>
<head>
<title>Edit Groups</title>

<style type='text/css'>
a {
	color: #a33;
	text-decoration: none;
}

a:hover {text-decoration: underline;}

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
print "<body".(($del)?" onLoad='updater()'":"").">";
?>

<? print $content; ?>

<table cellspacing=1 width='100%'>
<tr>
	<td colspan=2 style='font-variant: small-caps'>
		Class groups for <?echo $auser?>
	</td>
</tr>
<tr>
	<th>group/members</th>
	<th>del</th>
</tr>
<?
if (db_num_rows($r)) {
	while ($a=db_fetch_assoc($r)) {
		print "<tr>";
		print "<td>";
		print "$a[name]";
		print "</td>";
		print "<td align=center>";
		print "<a href='$PHP_SELF?$sid&del=group&group=$a[id]'>[del]</a>";
		print "</td>";
		
		print "</tr>";
		$classes = explode(",",$a[classes]);
		foreach ($classes as $c) {
			print "<tr>";
			print "<td style='padding-left: 20px'>";
			print "-&gt; $c</a>";
			print "</td>";
			print "<td align=center>";
			print "<a href='$PHP_SELF?$sid&del=class&group=$a[id]&class=$c'>[remove]</a>";
			print "</td>";
			print "</tr>";
		}
	}
} else {
	print "<tr><td colspan=2>No class groups.</td></tr>";
}
?>
</table><BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>