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

if ($_SESSION['ltype'] != 'admin') {
	// take them right to the user lookup page
	header("Location: username_lookup.php");
	exit;
}

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

// what's the action?
$curraction = $_REQUEST['action'];

// if they want to delete a slot
if ($curraction == 'del') {
	$id = $_REQUEST['id'];
	if ($id > 0) {
		// delete a slot
		$slotObj = new slot("","","","",$id);
		$slotObj->delete();
		$message = "Slot ID $id deleted successfully."; 
	}
}

// if they want to add a slot...
if ($curraction == 'add') {
	// check for errors first
	if (slot::exists($_REQUEST['name'])) error("A slot with that name already exists.");
	if (!$_REQUEST['name']) error("You must enter a name.");
	if (!user::userExists($_REQUEST['owner'])) error("User, '".$_REQUEST['owner']."', does not exist. Please choose an existing user.");
	if (!$_REQUEST['owner']) error("You must enter an valid owner.");
	if (!is_numeric($_REQUEST['uploadlimit'])) error("Upload Limit must be an number.");
	
	// all good
	if (!$error) {
		$obj = &new slot($_REQUEST['name']);
		$obj->owner = strtolower($_REQUEST['owner']);
		$obj->assocSite = strtolower($_REQUEST['assocsite']);
		$obj->type = $_REQUEST['type'];
		if ($_REQUEST['units'] == 'GB') {
			$obj->uploadlimit = $_REQUEST['uploadlimit']*1073741824;
		} else if ($_REQUEST['units'] == 'MB') {
			$obj->uploadlimit = $_REQUEST['uploadlimit']*1048576;
		} else if ($_REQUEST['units'] == 'KB') {
			$obj->uploadlimit = $_REQUEST['uploadlimit']*1024;
		} else {
			$obj->uploadlimit = $_REQUEST['uploadlimit'];
		}
		
		$obj->insertDB();
		unset($_REQUEST['name']);
		$message = "Slot '".$obj->name."' added successfully.";
	}
}

// if they're editing a slot
if ($curraction == 'edit') {
	if ($_REQUEST['commit']==1) {
		// check for errors first
		if (!$_REQUEST['name']) error("You must enter a name.");
		if (!user::userExists($_REQUEST['owner'])) error("User, '".$_REQUEST['owner']."', does not exist. Please choose an existing user.");
		if (!$_REQUEST['owner']) error("You must enter an valid owner.");
		if (!is_numeric($_REQUEST['uploadlimit'])) error("Upload Limit must be an number.");
		
		// all good
		if (!$error) {
			$obj = &new slot($_REQUEST['name']);
			$obj->owner = strtolower($_REQUEST['owner']);
			$obj->assocSite = strtolower($_REQUEST['assocsite']);
			$obj->type = $_REQUEST['type'];
			if ($_REQUEST['units'] == 'GB') {
				$obj->uploadlimit = $_REQUEST['uploadlimit']*1073741824;
			} else if ($_REQUEST['units'] == 'MB') {
				$obj->uploadlimit = $_REQUEST['uploadlimit']*1048576;
			} else if ($_REQUEST['units'] == 'KB') {
				$obj->uploadlimit = $_REQUEST['uploadlimit']*1024;
			} else {
				$obj->uploadlimit = $_REQUEST['uploadlimit'];
			}
			
			$obj->updateDB();
			unset($_REQUEST['name']);
			$message = "Slot '".$obj->name."' updated successfully.";
		}
	}
}

/******************************************************************************
 * get search variables and call getAllSlotsInfo function from slots object
 ******************************************************************************/

$slot_owner = $_REQUEST['slot_owner'];
$slot_name = $_REQUEST['slot_name'];
$slot_id = $_REQUEST['id'];
$slot_type = $_REQUEST['slot_type'];
if ($findall) $slot_name = '%'; $slot_owner = "";
if ($slot_name) $slot_type = "";

if ($slot_id) {
	$slot_owner = "";
	$allSlots = slot::getAllSlotsInfo($slot_owner,$slot_name,$slot_id,$slot_type);
} else {
	$allSlots = slot::getAllSlotsInfo($slot_owner,$slot_name,$slot_id,$slot_type);
}


printerr();

?>

<html>
<head>
<title>Users</title>
<? 
include("themes/common/logs_css.inc.php"); 
include("themes/common/header.inc.php");
?>
</head>
<!-- <body onLoad="document.addform.<?=($curraction == 'edit')?"owner":"name"?>.focus()"> -->
<body onLoad="document.searchform.name.focus()">

<?=($_SESSION['ltype']=='admin')?"<div align=right><a href='username_lookup.php?$sid'>user lookup</a> | <a href='users.php?$sid'>add/edit users</a> | <a href='classes.php?$sid'>add/edit classes</a> | add/edit slots</div>":""?>

<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr><td>

	<table cellspacing=1 width='100%'>
	<tr><td>
		<form action="<? echo $PHP_SELF ?>" method=get name=searchform>
		<b>Slot:</b> Name: <input type=text name='slot_name' size=10 value='<?echo $slot_name?>'> 
		Owner: <input type=text name='slot_owner' size=10 value='<?echo $slot_owner?>'>
		Type: <select name=slot_type>
				<option<?=($slot_type=='')?" selected":""?>>all
				<option<?=($slot_type=='class')?" selected":""?>>class
				<option<?=($slot_type=='other')?" selected":""?>>other
				<option<?=($slot_type=='personal')?" selected":""?>>personal
				<option<?=($slot_type=='system')?" selected":""?>>system
		</select>
		<input type=submit name='search' value='Find'>
		<input type=submit name='findall' value='Find All'>
		</form>
		<? if (!$allSlots) print "No matching slots found"; ?>
	</td></tr>
	</table>

		<table width='100%'>
			<tr>
			<th>id</th>
			<th>name</th>
			<th>owner</th>
			<th>type</th>
			<th>assoc site</th>
			<th colspan=2>uploadlimit (0=Default)</th>
			<th>in use?</th>
			<th>options</th>
			</tr>
			
			<? if ($curraction != 'edit') { doSlotForm($_REQUEST); }
			
			//  output found  slots			
			foreach ($allSlots as $slot) {
				if ($curraction == 'edit' && $_REQUEST['id'] == $slot['id'])
					doSlotForm($slot,'',1);
				else {
					print "<tr>";
					print "<td align=center>".$slot['id']."</td>";
					print "<td>".$slot['name']."</td>";
					print "<td>".$slot['owner']."</td>";
					print "<td>".$slot['type']."</td>";
					print "<td>".$slot['assocsite']."</td>";

					if ($slot['uploadlimit'] >= 1073741824) {
						print "<td align=right>".round($slot['uploadlimit']/1073741824,2)."</td>";
						print "<td>GB</td>";
					} else if ($slot['uploadlimit'] >= 1048576) {
						print "<td align=right>".round($slot['uploadlimit']/1048576,2)."</td>";
						print "<td>MB</td>";
					} else if ($slot[$p.'uploadlimit'] >= 1024) {
						print "<td align=right>".round($slot['uploadlimit']/1024,2)."</td>";
						print "<td>KB</td>";
					} else if ($slot['uploadlimit'] > 0) {
						print "<td align=right>".round($slot['uploadlimit'],2)."</td>";
						print "<td>B</td>";
					} else {
						print "<td align=right>Default</td>";
						print "<td> &nbsp; </td>";
					}
		
					print "<td align=center>".(($slot[FK_site])?"<b>YES</b>":"NO")."</td>";
					print "<td align=center><nobr>";
					if (!$slot[FK_site])
						print "<a href='add_slot.php?$sid&action=del&id=".$slot['id']."'>del</a> | \n";
					print "<a href='add_slot.php?$sid&action=edit&id=".$slot['id']."'>edit</a>\n";
					print "</nobr></td>";
					print "</tr>";
				}
			}
			?>
			
		</table>
	</td>
</tr>
</table>

<BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
<?
function doSlotForm($slot,$p='',$e=0) {
	?>
			<form method='post' name='addform'>
			<tr>
			<td align=center><?=($e)?$slot[$p.'id']:"&nbsp"?></td>
			<input type=hidden name='id' value='<?=($e)?$slot[$p.'id']:"0"?>'>
			<td>
			<? if ($e) {
				print $slot[$p.'name']."<input type=hidden name='name' value='".$slot[$p.'name']."'>";
			 } else { ?>
				<input type=text name='name' size=10 value="<?=$slot[$p.'name']?>">
			<? } ?>
			</td>
			<td><input type=text name='owner' size=8 value="<?=$slot['owner']?>"> <a href="Javascript:sendWindow('addeditor',400,250,'add_editor.php?$sid&comingfrom=classes')">choose</a></td>
			<td>
			<? if ($e) {
				print $slot[$p.'type']."<input type=hidden name='type' value='".$slot[$p.'type']."'>";
			 } else { ?>
				<select name=type>
				<option<?=($slot[$p.'type']=='class')?" selected":""?>>class
				<option<?=($slot[$p.'type']=='other')?" selected":""?>>other
				<option<?=($slot[$p.'type']=='personal')?" selected":""?>>personal
				<option<?=($slot[$p.'type']=='system')?" selected":""?>>system
				</select>
			<? } ?>
			</td>
			<td align=right><input type=text name='assocsite' size=10 value="<?=$slot[$p.'assocsite']?>"></td>
		<?	if ($slot[$p.'uploadlimit'] >= 1073741824) {
				print "<td><input type=text name='uploadlimit' size=10 value='".round($slot[$p.'uploadlimit']/1073741824,2)."'></td>";
				$units = "GB";
			} else if ($slot[$p.'uploadlimit'] >= 1048576) {
				print "<td><input type=text name='uploadlimit' size=10 value='".round($slot[$p.'uploadlimit']/1048576,2)."'></td>";
				$units = "MB";
			} else if ($slot[$p.'uploadlimit'] >= 1024) {
				print "<td><input type=text name='uploadlimit' size=10 value='".round($slot[$p.'uploadlimit']/1024,2)."'></td>";
				$units = "KB";
			} else if ($slot[$p.'uploadlimit'] > 0) {
				print "<td><input type=text name='uploadlimit' size=10 value='".round($slot[$p.'uploadlimit'],2)."'></td>";
				$units = "B";
			} else {
				print "<td><input type=text name='uploadlimit' size=10 value='0'></td>";
				$units = "MB";
			}
		?>
			<td><select name=units>
				<option<?=($units=='B')?" selected":""?>>B
				<option<?=($units=='KB')?" selected":""?>>KB
				<option<?=($units=='MB' || !$units || !$e)?" selected":""?>>MB
				<option<?=($units=='GB')?" selected":""?>>GB
			</select>
			</td>
			<? if ($e) { ?>
				<td align=center><?=(($slot[FK_site])?"<b>YES</b>":"NO")?></td>
			<? } else { ?>
				<td>&nbsp;  </td>
			<? } ?>
			<input type=hidden name='action' value='<?=($e)?"edit":"add"?>'>
			<?=($e)?"<input type=hidden name='id' value='".$slot[$p."id"]."'><input type=hidden name=commit value=1>":""?>
			<td align=center>
			<a href='#' onClick='document.addform.submit()'><?=($e)?"update":"add slot"?></a> | <a href='add_slot.php'>cancel</a>
			</td>
			</tr>
			</form>
	<?
}
?>