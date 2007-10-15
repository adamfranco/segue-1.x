<? /* $Id$ */

include("objects/objects.inc.php");
$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if ($del = $_REQUEST[del]) { // we're deleting something
//	print $del;
	if ($del=='group') {
		$query = "UPDATE class SET FK_classgroup=NULL WHERE FK_classgroup='".addslashes($_REQUEST['group'])."'";
		db_query($query);
		$query = "DELETE FROM classgroup WHERE classgroup_id='".addslashes($_REQUEST['group'])."'";
		db_query($query);
 		log_entry("classgroups","".$_SESSION['auser']." removed group ".db_get_value("classgroup","classgroup_name","classgroup_id='".addslashes($_REQUEST[group])."'"),"NULL",'".addslashes($group)."',"classgroup");
	}
	if ($del=='class') {
		$query = "UPDATE class SET FK_classgroup=NULL WHERE class_id='".addslashes($_REQUEST['class'])."'";
		printpre($query);
		db_query($query);
		log_entry("classgroup","".$_SESSION['auser']." removed $class from group ".db_get_value("classgroup","classgroup_name","classgroup_id='".addslashes($_REQUEST[group])."'"),"NULL",$_REQUEST[group],"classgroup");
	}
	print<<<END

<script type='text/javascript'>
// <![CDATA[

	function updater() {
		opener.window.location="index.php?$sid"; 
	}

// ]]>
</script>

END;

}
print mysql_error();


$query = "
	SELECT
		COUNT(*)
	FROM
		classgroup
			INNER JOIN
		user
			ON
				classgroup.FK_owner = user_id 
	WHERE 
		user_uname='".addslashes($_SESSION['auser'])."'
";
/* echo $query; */
$r = db_query($query);
$a = db_fetch_assoc($r);
$numGroups = $a["COUNT(*)"];

$query = "
	SELECT
		*
	FROM
		classgroup
			INNER JOIN
		user
			ON
				classgroup.FK_owner = user_id 
	WHERE 
		user_uname='".addslashes($_SESSION['auser'])."'
";
/* echo $query; */
$r = db_query($query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
print "<body".(($del)?" onload='updater()'":"").">";
?>

<? print $content; ?>

<table cellspacing='1' width='100%'>
<tr>
	<td colspan='2' style='font-variant: small-caps'>
		Class groups for <?echo $_SESSION['auser']?>
	</td>
</tr>
<tr>
	<th>group/members</th>
	<th>del</th>
</tr>
<?
if ($numGroups) {
	while ($a=db_fetch_assoc($r)) {
		print "<tr>";
		print "<td>";
		print "$a[classgroup_name]";
		print "</td>";
		print "<td align='center'>";
		print "<a href='$PHP_SELF?$sid&amp;del=group&amp;group=$a[classgroup_id]'>[del]</a>";
		print "</td>";
		
		print "</tr>";
		$query = "
			SELECT
				*
			FROM
				classgroup
					INNER JOIN
				class
					ON
						FK_classgroup = classgroup_id
			WHERE
				classgroup_id = $a[classgroup_id]
		";

		$r2 = db_query($query);
		while($b = db_fetch_assoc($r2)){
			print "<tr>";
			print "<td style='padding-left: 20px'>";
			print "-&gt; ".generateCourseCode($b[class_id])."</a>";
			print "</td>";
			print "<td align='center'>";
			print "<a href='$PHP_SELF?$sid&amp;del=class&amp;group=$a[classgroup_id]&amp;class=$b[class_id]'>[remove]</a>";
			print "</td>";
			print "</tr>";
		}
	}
} else {
	print "<tr><td colspan='2'>No class groups.</td></tr>";
}
?>
</table><br />
<div align='right'><input type='button' value='Close Window' onclick='window.close()' /></div>