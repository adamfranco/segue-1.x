<? /* $Id$ */
include("objects/objects.inc.php");
$content = '';
$message = '';

ob_start();
session_start();


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
//$default_uploadlimit = $userdirlimit/1048576;

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
		$obj = & new slot($_REQUEST['name']);
		$obj->owner = strtolower($_REQUEST['owner']);
		$obj->assocSite = strtolower($_REQUEST['assocsite']);
		$obj->type = $_REQUEST['type'];
		if ($_REQUEST['units'] == 'GB') {
			if ($_REQUEST['uploadlimit']*1073741824 != $userdirlimit) {
				$obj->uploadlimit = $_REQUEST['uploadlimit']*1073741824;
			} else {
				$obj->uploadlimit = 'NULL';
			}
		} else if ($_REQUEST['units'] == 'MB') {
			if ($_REQUEST['uploadlimit']*1048576 != $userdirlimit) {
				$obj->uploadlimit = $_REQUEST['uploadlimit']*1048576;
			} else {
				$obj->uploadlimit = 'NULL';
			}
		} else if ($_REQUEST['units'] == 'KB') {
			if ($_REQUEST['uploadlimit']*1024 != $userdirlimit) {
				$obj->uploadlimit = $_REQUEST['uploadlimit']*1024;
			} else {
				$obj->uploadlimit = 'NULL';
			}
		} else {
			if ($_REQUEST['uploadlimit'] != $userdirlimit) {
				$obj->uploadlimit = $_REQUEST['uploadlimit'];
			} else {
				$obj->uploadlimit = 'NULL';
			}
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
			$obj =& new slot($_REQUEST['name']);
			$obj->owner = strtolower($_REQUEST['owner']);
			$obj->assocSite = strtolower($_REQUEST['assocsite']);
			$obj->type = $_REQUEST['type'];
			if ($_REQUEST['units'] == 'GB') {
				if ($_REQUEST['uploadlimit']*1073741824 != $userdirlimit) {
					$obj->uploadlimit = $_REQUEST['uploadlimit']*1073741824;
				} else {
					$obj->uploadlimit = 'NULL';
				}
			} else if ($_REQUEST['units'] == 'MB') {
				if ($_REQUEST['uploadlimit']*1048576 != $userdirlimit) {
					$obj->uploadlimit = $_REQUEST['uploadlimit']*1048576;
				} else {
					$obj->uploadlimit = 'NULL';
				}
			} else if ($_REQUEST['units'] == 'KB') {
				if ($_REQUEST['uploadlimit']*1024 != $userdirlimit) {
					$obj->uploadlimit = $_REQUEST['uploadlimit']*1024;
				} else {
					$obj->uploadlimit = 'NULL';
				}
			} else {
				if ($_REQUEST['uploadlimit'] != $userdirlimit) {
					$obj->uploadlimit = $_REQUEST['uploadlimit'];
				} else {
					$obj->uploadlimit = 'NULL';
				}
			}
			
			$obj->updateDB();
			unset($_REQUEST['name']);
			$message = "Slot '".$obj->name."' updated successfully.";
		}
	}
}

/******************************************************************************
 * get search variables
 ******************************************************************************/

$slot_owner = $_REQUEST['slot_owner'];
$slot_name = $_REQUEST['slot_name'];
$slot_id = $_REQUEST['id'];
$slot_type = $_REQUEST['slot_type'];
$slot_use = $_REQUEST['slot_use'];

if ($findall) {
	$slot_name = '%'; 
	$slot_owner = "";
	$slot_type = "all";
	$slot_use = "all";
}

/******************************************************************************
 * compile where clause and query database 
 ******************************************************************************/

	$where = "slot.slot_name LIKE '%'";
	if ($slot_name) $where = "slot.slot_name LIKE '%".addslashes($slot_name)."%'";
	if ($slot_owner) $where .= " AND user.user_uname LIKE '%".addslashes($slot_owner)."%'";
	if ($slot_id) $where .= " AND slot.slot_id=$slot_id";
	if ($slot_type != "all"  && !$slot_id) $where .= " AND slot.slot_type = '".addslashes($slot_type)."'";
	if ($slot_use != "all"  && !$slot_id) {
		if ($slot_use == "yes") $where .= " AND slot.FK_site IS NOT NULL";
		if ($slot_use == "no") $where .= " AND slot.FK_site IS NULL";
	}

	//get number of matches found
	 $query = "
		SELECT 
			COUNT(*) AS slot_count	
		FROM 
			slot
		LEFT JOIN
			user
		ON
			slot.FK_owner = user_id
		LEFT JOIN
			slot AS assocsite
		ON
			slot.FK_assocsite = assocsite.slot_id
		WHERE
			$where
		ORDER BY
			slot.slot_name";
	
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$numslots = $a[slot_count];
	
	
	if (isset($_REQUEST['lowerlimit']))
		$lowerlimit = intval($_REQUEST['lowerlimit']);
	else
		$lowerlimit = 0;
	
	if ($lowerlimit < 0) 
		$lowerlimit = 0;
	
	$limit = " limit $lowerlimit,30";


		
	$query = "
		SELECT 
			slot.slot_id AS id,
			slot.slot_name AS name,
			user.user_uname AS owner,
			slot.slot_type AS type,
			assocsite.slot_name AS assocsite_name,
			slot.FK_site as inuse,
			slot.slot_uploadlimit AS uploadlimit
		FROM 
			slot
				LEFT JOIN
			user
				ON
					slot.FK_owner = user_id
				LEFT JOIN
					slot AS assocsite
				ON
					slot.FK_assocsite = assocsite.slot_id
		WHERE
			$where
		ORDER BY
			slot.slot_name
		$limit";
	
	
	$r = db_query($query);


//$allSlots = slot::getAllSlotsInfo($slot_owner,$slot_name,$slot_id,$slot_type, $slot_use);

printerr();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Slots</title>
<? 
include("themes/common/logs_css.inc.php"); 
include("themes/common/header.inc.php");
?>
</head>
<body onload="document.searchform.name.focus()">

<?
/******************************************************************************
 * Get site id for links to participation section
 ******************************************************************************/
	
	$siteObj =& new site($site);
	$siteid = $siteObj->id;

if ($_SESSION['ltype']=='admin') {
	print "<table width='100%'  class='bg'><tr><td class='bg'>
	Logs: <a href='viewsites.php?$sid&amp;site=$site'>sites</a>
	 | <a href='viewusers.php?$sid&amp;site=$site'>users</a>
	</td><td align='right' class='bg'>
	<a href='users.php?$sid&amp;site=$site'>add/edit users</a> | 
	<a href='classes.php?$sid&amp;site=$site'>add/edit classes</a> |  
	 add/edit slots |
	<a href='update.php?$sid&amp;site=$site'>segue updates</a>
	</td></tr></table>";
}

if ($site) {
	print "<div align='right'>";
	print "<a href='add_students.php?$sid&amp;name=$site'>Roster</a>";
	print " | <a href='email.php?$sid&amp;siteid=$siteid&amp;site=$site&amp;action=list&amp;scope=site'>Participation</a>";
	print " | <a href='viewusers.php?$sid&amp;site=$site'>Logs</a>";
	print "</div><br />";
}

?>

<?=$content?>

<table cellspacing='1' width='100%' id='maintable'>
<tr><td>

	<table cellspacing='1' width='100%'>
	<tr><td>
		<form action="<? echo $PHP_SELF ?>" method='get' name='searchform'>
		Name: <input type='text' name='slot_name' size='10' value='<?echo $slot_name?>' />
		Owner: <input type='text' name='slot_owner' size='10' value='<?echo $slot_owner?>' />
		Type: <select name='slot_type'>
				<option<?=($slot_type=='all')?" selected='selected'":""?>>all</option>
				<option<?=($slot_type=='class')?" selected='selected'":""?>>class</option>
				<option<?=($slot_type=='other')?" selected='selected'":""?>>other</option>
				<option<?=($slot_type=='personal')?" selected='selected'":""?>>personal</option>
				<option<?=($slot_type=='system')?" selected='selected'":""?>>system</option>
		</select>
		In Use: <select name='slot_use'>
				<option<?=($slot_use=='all')?" selected='selected'":""?>>all</option>
				<option<?=($slot_use=='yes')?" selected='selected'":""?>>yes</option>
				<option<?=($slot_use=='no')?" selected='selected'":""?>>no</option>
		</select>
		<input type='submit' name='search' value='Find' />
		<input type='submit' name='findall' value='Find All' />	
		</form>
		</td>	
		<td align='right'>
		<?
		$tpages = ceil($numslots/30);
		$curr = ceil(($lowerlimit+30)/30);
		$prev = $lowerlimit-30;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+30;
		if ($next >= $numslots) $next = $numslots-30;
		if ($next < 0) $next = 0;
		print "$curr of $tpages ";
//		print "$prev $lowerlimit $next ";
		if ($prev != $lowerlimit)
			print "<input type='button' value='&lt;&lt;' onclick='window.location=\"$PHP_SELF?$sid&amp;lowerlimit=$prev&amp;slot_type=$slot_type&amp;slot_name=$slot_name&amp;slot_owner=$slot_owner&amp;slot_use=$slot_use\"' />\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type='button' value='&gt;&gt;' onclick='window.location=\"$PHP_SELF?$sid&amp;lowerlimit=$next&amp;slot_type=$slot_type&amp;slot_name=$slot_name&amp;slot_owner=$slot_owner&amp;slot_use=$slot_use\"' />\n";
		?>

		
	</td></tr>
	</table>
		<? 
		$default_uploadlimit = $userdirlimit/1048576;
		if (!db_num_rows($r)) {
			print "No matching slots found";
		} else {
			//$numslots = count($allSlots);
			print "Total slots found: ".$numslots;
		} 
		print "<table cellpadding='2' cellspacing='0'>";
		print "<tr><td><b>Slot naming conventions:</b></td></tr>";
		print "<tr><td><i>future course slots</i></td><td>course_code-dev (e.g. al201a-f05-dev)</td></tr>";		
		print "<tr><td><i>student class project slots</i></td><td>These are best created from class rosters. Convention is course_code-student_username (e.g. al201a-f03-msmith).</td></tr>";
		print "<tr><td><i>faculty project slots</i></td><td>faculty_username-single_word_descriptor (e.g. jrprof-politics, saprof-poetry)</td></tr>";
		print "<tr><td><i>topical slots</i></td><td>single_word_descriptor (e.g. digitization, segue)</td></tr>";
		print "</table>";	
		?>
		
		<form action="<? echo $PHP_SELF ?>" method='post' name='addform'>
		<table width='100%'>
			<tr>
			<th>id</th>
			<th>name</th>
			<th>owner</th>
			<th>type</th>
			<th>associated<br />class site</th>
			<th colspan='2'>uploadlimit<br />(Default = <? print $default_uploadlimit ?> MB)</th>
			<th>in use?</th>
			<th>options</th>
			</tr>
			
			<? if ($curraction != 'edit') { doSlotForm($_REQUEST); }
						
				//  output found  slots			
				//foreach ($allSlots as $slot) {
				while ($a = db_fetch_assoc($r)) {
					if ($curraction == 'edit') {
						doSlotForm($a,'',1);
					} else {
						print "<tr>";
						print "<td align='center'>".$a['id']."</td>";
						print "<td>".$a['name']."</td>";
						print "<td>".$a['owner']."</td>";
						print "<td>".$a['type']."</td>";
						print "<td>".$a['assocsite_name']."</td>";
	
						if ($a['uploadlimit'] >= 1073741824) {
							print "<td align='right'>".round($a['uploadlimit']/1073741824,2)."</td>";
							print "<td>GB</td>";
						} else if ($a['uploadlimit'] >= 1048576) {
							print "<td align='right'>".round($a['uploadlimit']/1048576,2)."</td>";
							print "<td>MB</td>";
						} else if ($a[$p.'uploadlimit'] >= 1024) {
							print "<td align='right'>".round($a['uploadlimit']/1024,2)."</td>";
							print "<td>KB</td>";
						} else if ($a['uploadlimit'] > 0) {
							print "<td align='right'>".round($a['uploadlimit'],2)."</td>";
							print "<td>B</td>";
						} else {
							//print "<td align='right'>Default</td>";
							print "<td align='right'>".round($default_uploadlimit,2)."</td>";
							print "<td>MB</td>";
						}
			
						print "<td align='center'>".(($a['inuse'])?"<b>YES</b>":"NO")."</td>";
						print "<td align='center'><span style='white-space: nowrap;'>";
						if (!$a[FK_site])
							print "<a href='add_slot.php?$sid&amp;action=del&amp;id=".$a['id']."'>del</a> | \n";
						print "<a href='add_slot.php?$sid&amp;slot_type=$slot_type&amp;slot_name=$slot_name&amp;slot_owner=$slot_owner&amp;slot_use=".(($a['inuse'])?"YES":"NO")."&amp;action=edit&amp;id=".$a['id']."'>edit</a>\n";
						print "</span></td>";
						print "</tr>";
					}
				}
			?>
			
		</table>
		</form>
	</td>
</tr>
</table>

<br />
<div align='right'><input type='button' value='Close Window' onclick='window.close()' /></div>
<?
function doSlotForm($slot,$p='',$e=0) {
	global $default_uploadlimit;
	?>
	
	<tr>
	<td align='center'><?=($e)?$slot[$p.'id']:"&nbsp;"?></td>
	<td>
	<input type='hidden' name='id' value='<?=($e)?$slot[$p.'id']:"0"?>' />
	<? if ($e) {
		print $slot[$p.'name']."<input type='hidden' name='name' value='".$slot[$p.'name']."' />";
	 } else { ?>
		<input type='text' name='name' size='10' value="<?=$slot[$p.'name']?>" />
	<? } ?>
	</td>
	<td><input type='text' name='owner' size='10' value="<?=$slot['owner']?>" /> <a href="Javascript:sendWindow('addeditor',400,250,'add_editor.php?$sid&amp;comingfrom=classes')">choose</a></td>
	<td>
	<? if ($e) {
		print $slot[$p.'type']."<input type='hidden' name='type' value='".$slot[$p.'type']."' />";
	 } else { ?>
		<select name='type'>
		<option<?=($slot[$p.'type']=='class')?" selected='selected'":""?>>class</option>
		<option<?=($slot[$p.'type']=='other')?" selected='selected'":""?>>other</option>
		<option<?=($slot[$p.'type']=='personal')?" selected='selected'":""?>>personal</option>
		<option<?=($slot[$p.'type']=='system')?" selected='selected'":""?>>system</option>
		</select>
	<? } ?>
	</td>
	<td align='left'><input type='text' name='assocsite' size='10' value="<?=$slot[$p.'assocsite']?>" /></td>
	<td align='right'>
<?	if ($slot[$p.'uploadlimit'] >= 1073741824) {
		print "<input type='text' align='right' name='uploadlimit' size='5' value='".round($slot[$p.'uploadlimit']/1073741824,2)."' />";
		$units = "GB";
	} else if ($slot[$p.'uploadlimit'] >= 1048576) {
		print "<input type='text' align='right' name='uploadlimit' size='5' value='".round($slot[$p.'uploadlimit']/1048576,2)."' />";
		$units = "MB";
	} else if ($slot[$p.'uploadlimit'] >= 1024) {
		print "<input type='text' align='right' name='uploadlimit' size='5' value='".round($slot[$p.'uploadlimit']/1024,2)."' />";
		$units = "KB";
	} else if ($slot[$p.'uploadlimit'] > 0) {
		print "<input type='text' align='right' name='uploadlimit' size='5' value='".round($slot[$p.'uploadlimit'],2)."' />";
		$units = "B";
	} else {
		print "<input type='text' align='right' name='uploadlimit' size='5' value='".$default_uploadlimit."' />";
		$units = "MB";
	}
?>
	</td>
	<td><select name='units'>
		<option<?=($units=='B')?" selected='selected'":""?>>B</option>
		<option<?=($units=='KB')?" selected='selected'":""?>>KB</option>
		<option<?=($units=='MB' || !$units || !$e)?" selected='selected'":""?>>MB</option>
		<option<?=($units=='GB')?" selected='selected'":""?>>GB</option>
	</select>
	</td>
	<? if ($e) { ?>
		<td align='center'><?=(($slot[$p.'inuse'])?"<b>YES</b>":"NO")?></td>
	<? } else { ?>
		<td>&nbsp;  </td>
	<? } ?>
	<td align='center'>
	<input type='hidden' name='action' value='<?=($e)?"edit":"add"?>' />
	<?=($e)?"<input type='hidden' name='id' value='".$slot[$p."id"]."' /><input type='hidden' name='commit' value='1' />":""?>
	<a href='#' onclick='document.addform.submit()'><?=($e)?"update":"add slot"?></a>
	<!-- | <a href='add_slot.php'>cancel</a> -->
	</td>
	</tr>
	<?
}
?>

</body>
</html>