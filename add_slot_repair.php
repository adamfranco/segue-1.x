<? 
/*  */

/******************************************************************************
 * add_slot.php,v 1.7.2.11 released in Segue v1.0.6 has a bug that inserts/updates
 * into slots.slot_uploadlimit the default upload limits defined in config.inc.php
 * This means that changes to $userdirlimit in config.inc.php will not update these
 * slots.
 * 
 * This scripts searches the slots table for all records where:
 * slot_uploadlimit = $userdirlimit
 * and updates these records so that slot_uploadlimit = 'NULL'
 * Must be logged in as admin to run this script
 ******************************************************************************/

include("objects/objects.inc.php");

ob_start();
session_start();


// include all necessary files
include("includes.inc.php");

/******************************************************************************
 * 
 ******************************************************************************/

if ($_SESSION['ltype'] != 'admin') {
	print "You must be logged in as an administrator to run this script";
	exit();
}

  print "Add Slot Repair Script<br>";
  print "All slots whose uploadlimit = default set in config.inc.php should have their ";
  print "slot_uploadlimit = 'NULL'<br><br>";
  print "This scripts searches the slots table for all records where:<br>";
  print "slot_uploadlimit = $userdirlimit<br>";
  print "and updates these records so that slot_uploadlimit = 'NULL'<br><br>";
  print "For more information, see: <a href=https://sourceforge.net/tracker/index.php?func=detail&aid=874014&group_id=82171&atid=565234 target=new_window>Segue bug tracker [ 874014 ] Media Library size issue</a><br><br>";


if ($_REQUEST['Update']) {

	db_connect($dbhost, $dbuser, $dbpass, $dbdb);
	
	
/******************************************************************************
 * Find total number of slots
 ******************************************************************************/
	
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
		ORDER BY
			slot.slot_name";
	
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$numslots = $a[slot_count];
	
/******************************************************************************
 * Get slots
 ******************************************************************************/
	
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
		ORDER BY
			slot.slot_name
		$limit";
	
	$r = db_query($query);


	$default_uploadlimit = $userdirlimit;
	
	if (!db_num_rows($r)) {
		print "No matching slots found";
	} else {
		//$numslots = count($allSlots);
		print "Total slots found: ".$numslots;
	} 

} else {
	print "<form><input type=submit name='Update' value='update'></form>";
}
print "<hr>";

?>
<table width='100%' border=1>
<tr>
<th>Update</th>
<th>id</th>
<th>name</th>
<th>owner</th>
<th>type</th>
<th>assoc site</th>
<th colspan=2>uploadlimit<br>(Default = <? print $default_uploadlimit ?> Bytes)</th>
</tr>
<?
$count = 0;	
while ($a = db_fetch_assoc($r)) {
	if ($a['uploadlimit'] == $userdirlimit) {
		$slot_id = 	$a['id'];
		$query2 = "
			UPDATE
				slot
			SET
				slot_uploadlimit = NULL
			WHERE
				slot_id = $slot_id";
		$r2 = db_query($query2);
		
		$count ++;
		
		print "<tr>";
		print "<td>Yes</td>";
		print "<td align=center>".$a['id']."</td>";
		print "<td>".$a['name']."</td>";
		print "<td>".$a['owner']."</td>";
		print "<td>".$a['type']."</td>";
		print "<td>".$a['assocsite_name']."</td>";
		print "<td align=center>set to 'NULL'</td>";
		print "<td></td>";
		print "<tr>";		
	}
}
print "</table>";
print "<hr>";
print "Total slots updated: $count";