<? /* $Id$ */
require("objects/objects.inc.php");

session_start();
ob_start();

require("includes.inc.php");

$add = $_REQUEST[add];
$name = $_REQUEST[name];
$owner = $_REQUEST[owner];
$assocsite = $_REQUEST[assocsite];
$delete = $_REQUEST[delete];

if ($add) {
	// add the slot
	if (!$name || $name == "") error("You must enter a Site Name");
	if (!$owner || $owner == "") error("You must choose an owner");
	
	
	if (!$error) {
		$slotObj = new slot($name,$owner,$type,$assocsite);
		$successful = $slotObj->insertDB();
		if ($successful) {
			$name = "";
		}
	}
}

if ($delete) {
	// delete a slot
	$slotObj = new slot("","","","",$delete);
	$slotObj->delete();
}

?>
<html>
<head>
<title>Slots</title>

<script lang='JavaScript'>

function doWindow(name,width,height) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.focus();
}

</script>

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
	background-color: #bbb; 
	font-variant: small-caps;
}

.td1 { 
	background-color: #ccc; 
}

.td0 { 
	background-color: #ddd; 
}

body { 
	background-color: white; 
}

body, table, td, th, input {
	font-size: 10px;
	font-family: "Verdana", "sans-serif";
}

/* td { font-size: 10px; } */

input,select {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

</style>
</head>
<body>
<? printerr2(); ?>

<form action="<? echo $PHP_SELF ?>" method=post name='addform'>
<table cellspacing=1 width='100%'>
<tr>
<td>Site Name:</td>
<td><input type=text name="name" value="<? echo $name ?>"></td>
</tr>
<tr>
<td>Owner: <a href='add_editor.php?$sid&comingfrom=add_slot' target='addeditor' onClick='doWindow("addeditor",400,250);'>choose</a></td>
<td><input type=text name="owner" value="<? echo $owner ?>"></td>
</tr>
<tr>
<td valign=top>Type:</td>
<td>
<input type=radio name="type" value="class"<? print (($type == 'class' || !isset($type))?" checked":""); ?>> Class
<br><input type=radio name="type" value="personal"<? print (($type == 'personal')?" checked":""); ?>> Personal
<br><input type=radio name="type" value="other"<? print (($type == 'other')?" checked":""); ?>> Other
<br><input type=radio name="type" value="system"<? print (($type == 'system')?" checked":""); ?>> System
<br><input type=radio name="type" value="publication"<? print (($type == 'publication')?" checked":""); ?>> Publication
</td>
</tr>
<tr>
<td>Associated Site: (optional)</td>
<td><input type=text name="assocsite" value="<? echo $assocsite ?>"></td>
</tr>
</table>

<div align=right>
<input type=submit name='add' value='Add'>
</form>
<input type=button value='Done' onClick='window.close()'></div>
<br>

<table cellspacing=1 width='100%'>
<tr>
<th> &nbsp; </th>
<th>id</th>
<th>name</th>
<th>owner</th>
<th>type</th>
<th>assocSite</th>
</tr>

<?
$allSlots = slot::getAllSlotsInfo();
foreach ($allSlots as $slot) {
	print "<tr>";
	print "<td><a href=''><a href='$PHP_SELF?$SID&delete=$slot[id]'>delete</a></td>";
	print "<td>$slot[id]</td>";
	print "<td>$slot[name]</td>";
	print "<td>$slot[owner]</td>";
	print "<td>$slot[type]</td>";
	print "<td>$slot[assocsite]</td>";
	print "</tr>";
}
?>

</table>

<div align=right>
<input type=button value='Done' onClick='window.close()'></div>