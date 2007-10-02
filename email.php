<? 
include("objects/objects.inc.php");

$content = '';
$message = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

/* if ($_SESSION['ltype'] != 'admin') { */
/* 	// take them right to the user lookup page */
/* 	header("Location: username_lookup.php"); */
/* 	exit; */
/* } */

//printpre($curraction);


db_connect($dbhost, $dbuser, $dbpass, $dbdb);


/******************************************************************************
 * Action: list, review, user
 ******************************************************************************/
if ($_REQUEST['action']) {
	$curraction = $_REQUEST['action'];
	$action = $_REQUEST['action'];
} else {
	$action = 'list';
	$curraction = 'list';
}

if ($_REQUEST['email']) {
	if ($_REQUEST['action'] == 'send') {
		$curraction = 'send';
	} else {
		$curraction = 'email';
	}
	$action = 'email';
} 


/******************************************************************************
 * Determine which subsets of participants will be checked
 ******************************************************************************/

if ($_REQUEST[checkclass] == "Check Class only") $checkgroup = "Check Class only";

//$checkgroup = $_REQUEST['checkgroup'];
//$checkgroup = "Check Class only";


if ($curraction != "list" && $curraction != "review") $_SESSION[editors] = $_REQUEST['editors'];
//$_SESSION[editors] = $_REQUEST['editors'];
//printpre($_SESSION[editors]);
//printpre($_REQUEST);


/******************************************************************************
 * Scope: site, discussion/assessment
 ******************************************************************************/

if ($_REQUEST['scope']) 
	$scope = $_REQUEST['scope'];
else if ($_REQUEST['storyid'])
	$scope = 'discussion';
else
	$scope = 'site';


$sql = $_REQUEST['sql'];
$query_custom = $_REQUEST['newquery'];

/******************************************************************************
 * Sort order: 
 ******************************************************************************/

if ($_REQUEST['order']) {
	$order = urldecode($_REQUEST['order']);
} else if (!$_REQUEST['order'] && $action == "user") {
	$order = "discussion_tstamp DESC";
} else if (!isset($order)
	|| !preg_match('/^[a-z0-9_.]+( (ASC|DESC))?$/i', $order)) {
	$order = "user_fname ASC";
}

$orderby = " ORDER BY $order";


/******************************************************************************
 * Username and id or findall
 ******************************************************************************/
 
if ($_REQUEST['findall']) {
	$userid = "'%'";
	$useruname = "";
	$find = "";
} else if ($_REQUEST['find']) {
	$findall = "";
} else if ($_REQUEST['useruname']) {
	$useruname = $_REQUEST['useruname'];
	$userid = db_get_value ("user", "user_id", "user_uname = '".addslashes($useruname)."'");
	if (!$userid) error("invalid username");
	$userfname = db_get_value ("user", "user_fname", "user_id = '".addslashes($userid)."'");
} else if ($_REQUEST['userid']) {
	$userid = $_REQUEST['userid'];
} else {
	$userid = $_SESSION['aid'];
}

// if full name and not username (ie clicking full name to review...)
if ($_REQUEST['userfname'] && !$_REQUEST['useruname']) {
	$userfname = urldecode($_REQUEST['userfname']);
	$userfname = db_get_value ("user", "user_fname", "user_id = '".addslashes($userid)."'");
	$useruname = db_get_value ("user", "user_uname", "user_id = '".addslashes($userid)."'");
}

/******************************************************************************
 * Story and Site ids
 ******************************************************************************/


if ($_REQUEST['storyid']) $storyid = $_REQUEST['storyid'];


$siteid = $_REQUEST['siteid'];
$class_id = $_REQUEST['site'];
$site = $_REQUEST['site'];


/******************************************************************************
 * STUDENT and CLASS if class get all members of class from ldap
 * returns array with uname, fname and type
 ******************************************************************************/
$students = array();
$roster_ids = array();

if (isclass($class_id)) {
	$students = getclassstudents($class_id);	
	foreach (array_keys($students) as $key) {
		$roster_ids[] = $students[$key][id];
	}

	//printpre($students);
}

/******************************************************************************
 * Add Participants to Roster
 ******************************************************************************/

if ($_REQUEST[addtoclass] == "Add Checked to Roster") {
	$_SESSION[editors] = $_REQUEST[editors];
	foreach($_SESSION[editors] as $studentid) {
	
		//get ids of all student currently in class
		$currentstudents = array();
		foreach (array_keys($students) as $key) {
			$currentstudents[] = $students[$key][id];
		}

		//add to class roster only if not currently a student
		if (!in_array($studentid, $currentstudents)) {
			//print "Participants added to roster";
			$user_id = $studentid;
			$ugroup_id = getClassUGroupId($class_id);
			
			// add them to the ugroup
			$query = "
				INSERT INTO
					ugroup_user
				SET
					FK_user='".addslashes($user_id)."',
					FK_ugroup='".addslashes($ugroup_id)."'			
			";
			//printpre($query);
			db_query($query);
		}
	}
	unset($_SESSION[editors]);
	unset($_SESSION[roster_ids]);
	unset($_SESSION[non_roster_ids]);
	unset($_SESSION[logged_participants_ids]);
	$students = getclassstudents($class_id);
	foreach (array_keys($students) as $key) {
		$roster_ids[] = $students[$key][id];
	}

}


/******************************************************************************
 * Query: WHERE clause
 * story, site, and/or user or 
 * all users, all sites
 ******************************************************************************/

if ($_REQUEST['findall'] && !$_REQUEST['find']) {
	$where = "user_id > 0";
} else if ($_REQUEST['find']) {
	$useruname = $_REQUEST['useruname'];
	$userid = db_get_value ("user", "user_id", "user_uname = '".addslashes($useruname)."'");
	if ($userid) {
		$where = "user_id = '".addslashes($userid)."'";
	} else {
		error("invalid username");
		$where = "user_id > 0";
	}
} else if ($scope == "site") {
	$where = "site_id = '".addslashes($siteid)."'";
} else if ($action != "user") {
	$where = "story_id = '".addslashes($storyid)."'";	
} else if ($userid && $action == "user") {
	$where = "user_id = '".addslashes($userid)."'";
}

if ($_REQUEST['userid'] && !$_REQUEST['findall'] && $action == "review" && $_REQUEST['userfname']) {
	$where .= " AND user_id = '".addslashes($userid)."'";
}

if ($_REQUEST['findsite'] && $action == "review") {
	$findsite = $_REQUEST['findsite'];
	$where .= " AND slotname = ''".addslashes($findsite)."''";
}


/******************************************************************************
 * Query: SELECT and ORDER clauses
 ******************************************************************************/

if ($action == "review" || $action == "user") {
	$select = "user_id, user_fname, user_uname, user_email, discussion_rate, discussion_tstamp, discussion_id, discussion_subject, story_id, page_id, page_title, story_text_short, section_id, site_id, slot_name";
	if (!isset($order)) $order = "discussion_tstamp ASC";
// action = list, email
} else {
	$select = "DISTINCT user_id, user_fname, user_uname, user_email";
	$order = "user_fname ASC";	
}


/******************************************************************************
 * Query: NUMBER of post for given user (i.e. number of posts for WHERE clause) 
 ******************************************************************************/
 
	$query = "
	SELECT 
		user_id
	FROM 
		discussion
	INNER JOIN story ON FK_story = story_id
	INNER JOIN page ON FK_page = page_id
	INNER JOIN section ON FK_section = section_id
	INNER JOIN site ON FK_site = site_id
	INNER JOIN user ON FK_author = user_id
	WHERE 
		$where
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$numrows = db_num_rows($r);
	

/******************************************************************************
 * Query: NUMBER and ID's of participants (i.e. distinct users)
 ******************************************************************************/
	
	$query = "
	SELECT 
		DISTINCT user_id
	FROM 
		discussion
	INNER JOIN story ON FK_story = story_id
	INNER JOIN page ON FK_page = page_id
	INNER JOIN section ON FK_section = section_id
	INNER JOIN site ON FK_site = site_id
	INNER JOIN user ON FK_author = user_id
	WHERE 
		$where
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$numparticipants = db_num_rows($r);
	$logged_participants = db_query($query);
	//print $numparticipants."<br />";
	
	if ($action == "list") $numrows = $numparticipants;
	//print $numrows."<br />";

///******************************************************************************
// * Query: GET ids of all participants discussion post information based on select:
// * 1. select summary info for each user
// * 2. select all post info for all specified users
// ******************************************************************************/
//
//	$query = "
//	SELECT 
//		$select
//	FROM 
//		discussion
//	INNER JOIN story ON FK_story = story_id
//	INNER JOIN page ON FK_page = page_id
//	INNER JOIN section ON FK_section = section_id
//	INNER JOIN site ON section.FK_site = site.site_id
//	INNER JOIN slot ON slot.FK_site = site.site_id
//	INNER JOIN user ON FK_author = user_id
//	WHERE 
//		$where $orderby
//	";	
//		
//	//printpre($_REQUEST);
//	//printpre("where: ".$where);	
//	//printpre($query);
//	//printpre($curraction);
//	//$r = db_query($query);
//	//$r2 = db_query($query);
//	$logged_participants = db_query($query);

	
printerr();

/******************************************************************************
 * SUBSETS of participants 
 * $logged_participants_ids = ids of all users who have posted to discussion
 * $roster_ids = ids of all participants in roster
 * $non_roster_ids = ids of all participants not in roster
 ******************************************************************************/
 
$non_roster_ids = array();
$logged_participants_ids = array();

while ($a = db_fetch_assoc($logged_participants)) {
	$logged_participant_id = $a[user_id];
	$logged_participants_ids[] = $a[user_id];
	
	if (!in_array($logged_participant_id, $roster_ids)) {
		$non_roster_ids[] = $logged_participant_id;
	}	
}
//printpre($roster_ids);
//printpre($non_roster_ids);
//printpre($_SESSION[editors]);

/******************************************************************************
 * define limits for pagination of results
 ******************************************************************************/

//if (!isset($lowerlimit)) $lowerlimit = 0;
//if (!isset($range)) $range = 30;
//if ($lowerlimit < 0) $lowerlimit = 0;

if (isset($_REQUEST['range']))
	$range = intval($_REQUEST['range']);
else
	$range = 30;


if (isset($_REQUEST['lowerlimit']))
	$lowerlimit = intval($_REQUEST['lowerlimit']);
else
	$lowerlimit = 0;

if ($lowerlimit < 0) 
	$lowerlimit = 0;

$limit = " limit $lowerlimit,30";


if ($action != "list") $limit = " LIMIT $lowerlimit,$range";


/******************************************************************************
 * Query: GET all discussion post information based on select:
 * 1. select summary info for each user
 * 2. select all post info for all specified users
 ******************************************************************************/

	$query = "
	SELECT 
		$select
	FROM 
		discussion
	INNER JOIN story ON FK_story = story_id
	INNER JOIN page ON FK_page = page_id
	INNER JOIN section ON FK_section = section_id
	INNER JOIN site ON section.FK_site = site.site_id
	INNER JOIN slot ON slot.FK_site = site.site_id
	INNER JOIN user ON FK_author = user_id
	WHERE 
		$where $orderby $limit
	";	
		
	//printpre($_REQUEST);
	//printpre("where: ".$where);	
	//printpre($query);
	//printpre($curraction);
	$r = db_query($query);
	$r2 = db_query($query);
	//$logged_participants = db_query($query);

	
printerr();




/******************************************************************************
 * Print out HTML
 ******************************************************************************/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<? 
	if ($action == "user") {
		print "<title>Your Posts</title>";
	} else {
		print "<title>Participants</title>";
	}
	
	include("themes/common/logs_css.inc.php"); ?>
	
	<script type="text/javascript">
	// <![CDATA[
	
	function changeOrder(order) {
		f = document.searchform;
		f.order.value=order;
		f.submit();
	}
	
	function doWindow(name,width,height) {
		var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
		win.focus();
	}
	
	function sendWindow(name,width,height,url) {
		var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
		win.document.location=url.replace(/&amp;/, '&');
		win.focus();
	}
	
	function checkAll() {
		field = document.forms[1].elements['editors[]'];
		for (i = 0; i < field.length; i++)
			field[i].checked = true ;
	}
	
	function uncheckAll() {
		field = document.forms[1].elements['editors[]'];
		for (i = 0; i < field.length; i++)
			field[i].checked = false ;
	}
	
	function checkGroup() {
		selectField = document.forms[1].elements['groupcheck'];
		groupName = selectField.value;
		field = document.forms[1].elements['editors[]'];
		
		classIds = new Array ();
		<? 
		foreach ($roster_ids as $id)
			print "\n\t\tclassIds.push('".$id."');";
		?>
		
		
		otherIds = new Array ();
		<? 
		foreach ($non_roster_ids as $id)
			print "\n\t\totherIds.push('".$id."');";
		?>
		

		switch(groupName) {
		case 'all':
			checkAll();
			break;
		case 'un_all':
			uncheckAll();
			break;
		case 'class':
			checkArrayMembersInField(classIds, field, true);
			break;
		case 'un_class':
			checkArrayMembersInField(classIds, field, false);
			break;
		case 'other':
			checkArrayMembersInField(otherIds, field, true);
			break;
		case 'un_other':
			checkArrayMembersInField(otherIds, field, false);
			break;
		}
	}
	
	function checkArrayMembersInField (arrayToCheck, field, checkValue) {
		for (i=0; i<arrayToCheck.length; i++) {
				id = arrayToCheck[i];
				for (j = 0; j < field.length; j++) {
					if (field[j].value == id)
						field[j].checked = checkValue;
				}
		}
	}
	
	function doFieldChange(user,scope,site,section,page,story,field,what) {
		f = document.addform;
		f.fieldchange.value = 1;
		f.puser.value = user;
		f.pscope.value = scope;
		f.psite.value = site;
		f.psection.value = section;
		f.ppage.value = page;
		f.pstory.value = story;
		f.pfield.value = field;
		f.pwhat.value = what;
		f.submit();
	}
	
	// ]]>
	</script>

</head>
<body>

	<?
	
	//printpre($_REQUEST);
	
	/******************************************************************************
	 * If admin print out admin tools (e.g. add/edit users, classes, slots updates
	 ******************************************************************************/
	
	if ($_SESSION['ltype']=='admin') {
		print "\n\t<table width='100%'  class='bg'>";
		print "\n\t\t<tr>\n\t\t\t<td class='bg'>";
		print "\n\t\t\t\tLogs: <a href='viewsites.php?$sid&amp;site=$site'>sites</a>";
		print "\n\t\t\t\t | <a href='viewlogs.php?$sid&amp;site=$site'>users</a>";
		print "\n\t\t\t</td>\n\t\t\t<td align='right' class='bg'>";
		print "\n\t\t\t\t<a href='users.php?$sid&amp;site=$site'>add/edit users</a> | ";
		print "\n\t\t\t\t<a href='classes.php?$sid&amp;site=$site'>add/edit classes</a> | ";
		print "\n\t\t\t\t<a href='add_slot.php?$sid&amp;site=$site'>add/edit slots</a> | ";
		print "\n\t\t\t\t<a href='update.php?$sid&amp;site=$site'>segue updates</a>";
		print "\n\t\t\t</td>\n\t\t</tr>";
		print "\n\t</table>";
	}
	
	/******************************************************************************
	 * Links: Roster | Participation | Logs | Your Posts
	 ******************************************************************************/
	
	print "\n\t<table width='100%'  class='bg'>";
	
	// for admins print out participation select and where and order by sql
	print "\n\t\t<tr>\n\t\t\t<td class='bg'>";
	if ($_SESSION['ltype']=='admin') {
		//print $action.": ";
		//print "WHERE ".$where." ORDER BY ";
		//print $order;
	}
	print "\n\t\t\t</td>";
	
	print "\n\t\t\t<td class='bg' align='right'>";
	// roster
	if (isclass($_REQUEST[site])) print "\n\t\t\t\t<a href='add_students.php?$sid&amp;name=$site&amp;scope=$scope&amp;storyid=".$_REQUEST['storyid']."'>Roster</a> |";
	
	// participation (not link when coming from home)
	if ($_REQUEST[from] != "home") {
		if ($action == "user") {
			print "\n\t\t\t\t <a href='email.php?$sid&amp;siteid=$siteid&amp;storyid=$storyid&amp;site=$site&amp;scope=$scope&amp;action=list'>Participation</a>";
		} else {
			print " Participation";
		}
		if ($action == "user") {
			print " - Your Posts";
		} else {
			print "\n\t\t\t\t - <a href='email.php?$sid&amp;siteid=$siteid&amp;storyid=$storyid&amp;scope=$scope&amp;site=$site&amp;action=user'>Your Posts</a>";
		}
		
		// logs (not link when coming from home)
		print "\n\t\t\t\t | <a href='viewlogs.php?$sid&amp;site=$site&amp;storyid=$storyid&amp;scope=$scope&amp;'>Logs</a>";
	} else {
		print "\n\t\t\t\t Your Posts";
	}
	
	print "\n\t\t\t</td>\n\t\t</tr>";
	print "\n\t</table>\n\t<br />";
	?>
	
	<?=$content?>
	
	<table cellspacing='1' width='100%' id='maintable'>
		<tr>
			<td>
				<form action="<? echo $PHP_SELF ?>" method='get' name='searchform'>
					<table cellspacing='1' width='100%'>
						<tr>
							<td>
								<input type='hidden' name='order' value='<? echo urlencode($order) ?>' />
								<input type='hidden' name='action' value='<? echo $action ?>' />
								<input type='hidden' name='checkgroup' value='<? echo $checkgroup ?>' />
								<input type='hidden' name='storyid' value='<? echo $storyid ?>' />
								<input type='hidden' name='siteid' value='<? echo $siteid ?>' />
								<input type='hidden' name='site' value='<? echo $site ?>' />
								<input type='hidden' name='userid' value='<? echo $userid ?>' />
								<input type='hidden' name='from' value='<? echo $from ?>' />
								<input type='hidden' name='findall' value='<? echo $findall ?>' />
								<input type='hidden' name='find' value='<? echo $find ?>' />
								<input type='hidden' name='findsite' value='<? echo $findsite ?>' />
								<input type='hidden' name='userfname' value='<? echo urlencode($userfname) ?>' />
							</td>
							<td align='right'>
								<?
								//$order = urlencode($order);
								if ($curraction == 'user') {
									$getvariables = "storyid=$storyid&siteid=$siteid&scope=$scope";
								} else {
									$getvariables = "storyid=$storyid&siteid=$siteid&site=$site&scope=$scope";
								}
								
								if ($userid) {
									$userfname = urlencode($userfname);
									$getusers = "&userid=$userid&userfname=$userfname";
								}
						
						
								$tpages = ceil($numrows/$range);
								$curr = ceil(($lowerlimit+$range)/$range);
								$prev = $lowerlimit-$range;
								if ($prev < 0) $prev = 0;
								$next = $lowerlimit+$range;
								if ($next >= $numrows) $next = $numrows-$range;
								if ($next < 0) $next = 0;
								
								if ($action != "list") {
								print "$curr of $tpages ";
								
								if ($prev != $lowerlimit)
									if (!$userfname) {
										print "\n\t\t\t\t\t\t\t\t<input type='button' value='&lt;&lt;' onclick='window.location=\"$PHP_SELF?$sid&lowerlimit=".$prev."&".$getvariables."&action=".$curraction."\"' />";
									} else {
										//$userfname = urlencode($userfname);
										print "\n\t\t\t\t\t\t\t\t<input type='button' value='&lt;&lt;' onclick='window.location=\"$PHP_SELF?$sid&lowerlimit=".$prev."&".$getvariables."&action=$curraction&userfname=$userfname&userid=$userid\"' />";
									}
								if ($next != $lowerlimit && $next > $lowerlimit)
									if (!$userfname) {
										print "\n\t\t\t\t\t\t\t\t<input type='button' value='&gt;&gt;' onclick='window.location=\"$PHP_SELF?$sid&lowerlimit=".$next."&".$getvariables."&action=$curraction\"' />";
									} else {
										print "\n\t\t\t\t\t\t\t\t<input type='button' value='&gt;&gt;' onclick='window.location=\"$PHP_SELF?$sid&lowerlimit=".$next."&".$getvariables."&action=$curraction&userfname=$userfname&userid=$userid\"' />";
									}
								}
						
								?>
						
							</td>
						</tr>
					</table>
				</form>
				
				<form action="<? echo $PHP_SELF ?>" method='post'>
					<input type='hidden' name='storyid' value='<? echo $storyid ?>' />
					<input type='hidden' name='siteid' value='<? echo $siteid ?>' />
					<input type='hidden' name='site' value='<? echo $site ?>' />
		
				<? 
				if ($numparticipants == 0) {
					print "No participants found. Try extending the scope to all participants in the site";
				}
					
			/******************************************************************************
			 * depending on action print out either:
			 * list of participants
			 * email UI
			 * sent email confirmation
			 ******************************************************************************/
			
					/******************************************************************************
					 * Navigation Email | List | Review participants in discussion or site
					 ******************************************************************************/
					print "\n\t\t\t\t<table>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td style='font-size: 12px'>";
					
					// lists all participants with summary of posts and avg. rating
					if ($curraction == "list") {
						//print "<a href='$PHP_SELF?$sid&amp;action=email&".$getvariables.$getusers."'>Email</a> | ";
						print "\n\t\t\t\t\t\t\tList | ";
						print "\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=review&amp;".htmlspecialchars($getvariables)."&amp;order=$order'>Review</a> - ";
						print $numparticipants." participants";
					
					// reviews posts by a given user to a given site/discussion/assessment
					// or reviews all posts by all users to a given site/discussion/assessment
					} else if ($curraction == 'review') {
						print "\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=list&amp;".htmlspecialchars($getvariables)."&amp;order=user_fname'>List</a> | ";
						
						if ($_REQUEST['userid']) {
							print "\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=review&amp;".htmlspecialchars($getvariables)."'>Review all</a> - ";
							print $numrows." posts from ".urldecode($userfname);
						} else {
							print "\n\t\t\t\t\t\t\tReview - ";
							print $numrows." posts from ".$numparticipants." participants";
						}
					
					// displays all posts of a given user across all sites	
					} else if ($curraction == 'user') {
						if ($_SESSION['ltype'] == "admin") {
							print "\n\t\t\t\t\t\t\tusername: <input type='text' name='useruname' value='".$useruname."' class='textfield' />";
							print "\n\t\t\t\t\t\t\t <input type='submit' name='find' value='Find' />";
							print "\n\t\t\t\t\t\t\t <input type='submit' name='findall' value='Find All' />  ";		
			
						}
						if ($userid) {
							print "\n\t\t\t\t\t\t\t".$numrows." posts";
						}
						print "\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>";
								
					// emails all participants currently listed	
					} else if ($curraction == 'email') {
						//print "Email | ";
						print "\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=list&amp;".htmlspecialchars($getvariables)."&amp;order=user_fname'>List</a> | ";
						print "\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=review&amp;".htmlspecialchars($getvariables)."'>Review</a> - ";
						print $numparticipants." participants";
					
					// sends email to all participants in email list	
					} else if ($curraction == 'send') {
						print "\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=list&amp;".htmlspecialchars($getvariables)."&amp;order=user_fname'>List</a> | ";
						print "\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=review&amp;".htmlspecialchars($getvariables).htmlspecialchars($getusers)."&amp;order=$order'>Review</a> - ";
						print "\n\t\t\t\t\t\t\t".$numparticipants." participants";
					}
					
					// if action is not listing of a user's posts across all sites, then include scope 
					// select (i.e. participants in this discussions/assessment or in this site
					if ($curraction != 'user') {
						print " in this ";
						print "\n\t\t\t\t\t\t\t<select name='scope'>";	
							
						// if viewed from roster, then no storyid and no specific discussion/assessment is viewable
						if ($_REQUEST[storyid] != "") {
							print "\n\t\t\t\t\t\t\t\t<option value='discussion'";
							if ($scope=='discussion')
								print " selected='selected'";
							print ">discussion/assessment</option>";
						}
									
						if ($scope=='site' || $_REQUEST[site] != "") {
							print "\n\t\t\t\t\t\t\t\t<option";
							($scope=='site')? print " value='site' selected='selected'": print "";
							print ">site</option>";
						}
						print "\n\t\t\t\t\t\t\t</select>";
						print "\n\t\t\t\t\t\t\t<input type='submit' name='update' value='Update' />";
						print "\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>";
						
						
						/******************************************************************************
						 * Buttons:
						 * check all/uncheck all buttons, check class only
						 * add checked to roster, email checked participants
						 ******************************************************************************/
												
						$selectbuttons .= "\n\t\t\t\t\t\t\t<select name='groupcheck' onchange='checkGroup()'>";
						$selectbuttons .= "\n\t\t\t\t\t\t\t\t<option value=''>Check...</option>";
						$selectbuttons .= "\n\t\t\t\t\t\t\t\t<option value='all'>Check All</option>";
						$selectbuttons .= "\n\t\t\t\t\t\t\t\t<option value='un_all'>Uncheck All</option>";
						if (isclass($_REQUEST[site])) $selectbuttons .= "\n\t\t\t\t\t\t\t\t<option value='class'>Check Roster Participants</option>";
						if (isclass($_REQUEST[site])) $selectbuttons .= "\n\t\t\t\t\t\t\t\t<option value='un_class'>Uncheck Roster Participants</option>";
						if (isclass($_REQUEST[site])) $selectbuttons .= "\n\t\t\t\t\t\t\t\t<option value='other'>Check Other Participants</option>";	
						if (isclass($_REQUEST[site])) $selectbuttons .= "\n\t\t\t\t\t\t\t\t<option value='un_other'>uncheck Other Participants</option>";
						$selectbuttons .= "\n\t\t\t\t\t\t\t</select> ";
						
						if (isclass($_REQUEST[site])) $buttons .= "\n\t\t\t\t\t\t\t<input type='submit' name='addtoclass' value='Add Checked to Roster' /> ";
						$buttons .= "\n\t\t\t\t\t\t\t<input type='submit' name='email' value='Email Checked Participants-&gt;' onclick=\"for (var i = 0; i < this.form.elements.length; i++) {if (this.form.elements[i].name == 'editors[]' && this.form.elements[i].checked) {return true;}} alert('None selected'); return false;\"  />";
						if ($action != 'email') {
							print "\n\t\t\t\t\t<tr>";
							print "\n\t\t\t\t\t\t<td align='left' colspan='2'>";
							print $selectbuttons;
							print $buttons;
							print "\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>";
						}
			
					} 
					
					?>


				</table>	
			
				<?
				/******************************************************************************
				 * if action is email, then compile to list and print out email UI
				 ******************************************************************************/
				
				if ($curraction == 'email') {
					
					$emaillist = array();
		
					foreach ($_REQUEST[editors] as $editor) {
						$editor_email = db_get_value("user","user_email", "user_id ='".addslashes($editor)."'");
						$editor_fname = db_get_value("user","user_fname", "user_id ='".addslashes($editor)."'");
						$editor_femail = $editor_fname."<".$editor_email.">";
						array_push($emaillist, $editor_femail);
						$emaillist = array_unique($emaillist);
					}
					
								
					$to = implode(", ", $emaillist);
								
					//compile from and cc into headers
					if ($_SESSION['ltype']=='admin' && $_SESSION['lfname'] != $_SESSION['afname']) {
						$from = $_SESSION['lfname']." as ".$_SESSION['afname']." <".$_SESSION['aemail'].">";
					} else {
						$from = $_SESSION['afname']." <".$_SESSION['aemail'].">";
					}
		
					$headers = "From: ".$from."\n";
					$headers .= "Cc: ".$from."\n";
					
					//add content type to header
					$html = 1;
					if ($html == 1) {
						$headers .= "Content-Type: text/html\n";
					} 
				
					//$text = "email text here";
					//$textarea = "email";
					?>
					
					<form action="<? echo $PHP_SELF ?>" method='post' name='emailform'>
						<table width='100%'>
							<tr>
								<td align='right'>To:</td>
								<td><? echo $to ?></td>
								<td align='right'></td>
							</tr>
					<? if ($_SESSION['ltype']=='admin' && $_SESSION['lfname'] != $_SESSION['afname']) {
							print "\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td align='right'>From:</td>\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lfname']." as ".$_SESSION['afname']."</td>\n\t\t\t\t\t\t\t\t<td align='right'></td>\n\t\t\t\t\t\t\t</tr>";
						} else {
							print "\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td align='right'>From:</td>\n\t\t\t\t\t\t\t\t<td>".$_SESSION['afname']."</td>\n\t\t\t\t\t\t\t\t<td align='right'></td>\n\t\t\t\t\t\t\t</tr>";
						}
					?>
							<tr>
								<td align='right'>Cc:</td>
								<td><? echo $_SESSION['afname'] ?></td>
								<td align='right'></td>
							</tr>
							<tr>
								<td align='right'>Subject</td>
								<td>
									<input type='text' name='subject' value='' size='50' /> 
									<input type='submit' name='email' value='Send' />
								</td>
								<td align='left'></td>
							</tr>
							<tr>
								<td></td>
								<td align='left'>
								
									<?
									require_once("htmleditor/editor.inc.php");
									include("sniffer.inc.php");
									addeditor ("body",80,20,$text,"discuss"); 
									print $content;
									?>
					
								</td>
								<td align='right'></td>
							</tr>
						</table>
						<input type='hidden' name='action' value='send' />
						<input type='hidden' name='scope' value='<? echo $scope ?>' />
						<input type='hidden' name='storyid' value='<? echo $storyid ?>' />
						<input type='hidden' name='siteid' value='<? echo $siteid ?>' />
						<input type='hidden' name='site' value='<? echo $site ?>' />
						<input type='hidden' name='to' value='<? echo $to ?>' />
						<input type='hidden' name='headers' value='<? echo $headers ?>' />
					</form>
					<?
				//	$r = db_query($query);
					exit();
					
				/******************************************************************************
				 * if action is send then mail subject and body
				 ******************************************************************************/
		
				} else if ($curraction == 'send') {
					if ($_SESSION['ltype']=='admin' && $_SESSION['lfname'] != $_SESSION['afname']) {
						$subject = $subject." (sent by Segue Admin: ".$_SESSION['lfname'].")";
					}
					print "\n\t\t\t\t\t<table>";
					print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>To:</td>\n\t\t\t\t\t\t<td>".$to."</td>\n\t\t\t\t\t</tr>";
					print "\n\t\t\t\t\t<br /><hr />";	// BAD!
					print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>From:</td>\n\t\t\t\t\t\t<td>".$_SESSION['afname']."</td>\n\t\t\t\t\t</tr>";
					print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>Cc:</td>\n\t\t\t\t\t\t<td>".$_SESSION['afname']."</td>\n\t\t\t\t\t</tr>";
					print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>Subject:</td>\n\t\t\t\t\t<td>".$subject."</td>\n\t\t\t\t\t</tr>";
					print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t<td>".$body."</td>\n\t\t\t\t\t</tr>";
					print "\n\t\t\t</table>";

					if (!mail($to, $subject, $body, $headers)) 
						print "\n\t\t\t\tAN ERROR OCCURED SENDING MAIL!";
;
					exit();
				}
			// } 
		
		/******************************************************************************
		 * Print out table of participant names
		 ******************************************************************************/
		
		?>

				<table width='100%'>
					<tr>
						<th>edit</th>							
						<?
						print "\n\t\t\t\t\t\t<th><a href='#' onclick=\"changeOrder('";
						if ($order =='user_fname desc') print "user_fname asc";
						else print "user_fname desc";
						print "')\">Participant Name";
						if ($order =='user_fname asc') print " &or;";
						if ($order =='user_fname desc') print " &and;";	
						print "</a></th>";
				
				
						if ($curraction == 'review'  || $curraction == 'user') {
							if ($curraction == 'user') 
								print "\n\t\t\t\t\t\t<th>Site</th>";
							print "\n\t\t\t\t\t\t<th>Page > Topic</th>";
							print "\n\t\t\t\t\t\t<th>discussion_subject</th>";
										
							print "\n\t\t\t\t\t\t<th><a href='#' onclick=\"changeOrder('";
							if ($order =='discussion_rate asc') print "discussion_rate desc";
							else print "discussion_rate asc";
							print "')\">Rating<br />Grade";
							if ($order =='discussion_rate asc') print " &or;";
							if ($order =='discussion_rate desc') print " &and;";	
							print "</a></th>";
							
							print "\n\t\t\t\t\t\t<th><a href='#' onclick=\"changeOrder('";
							if ($order =='discussion_tstamp asc') print "discussion_tstamp desc";
							else print "discussion_tstamp asc";
							print "')\">Date Time";
							if ($order =='discussion_tstamp asc') print " &or;";
							if ($order =='discussion_tstamp desc') print " &and;";	
							print "</a></th>";
							
						} else {
							print "\n\t\t\t\t\t\t<th>Email</th>";
							print "\n\t\t\t\t\t\t<th># of Posts</th>";
							print "\n\t\t\t\t\t\t<th>Avg. Rating/Grade</th>";
						}
						?>

					</tr>
					
					<? 
			
						
						/******************************************************************************
						 * if a class site, print out list of students
						 * if student has participated get post stats
						 * get # of posts and avg. rating
						 ******************************************************************************/
						$color = 0;
						$logged_students_id = array();
						 
						if (is_array($students) && $curraction == 'list' && isclass($_REQUEST[site])) {
							$rostercount = count($students);
							print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan='4'>\n\t\t\t\t\t\t\t<b>".$rostercount." Participants from Roster</b>\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>";
							
							foreach (array_keys($students) as $key) {
								$e = $students[$key][id];
			
//								if (!$_SESSION[editors]) {
//									$checkstatus = " checked='checked'";
//								} else if (in_array($e,$_SESSION[editors])) {
//									$checkstatus = " checked='checked'";
//								} else {
//									$checkstatus = "";
//								}
								
								print "\n\t\t\t\t\t<tr>";
												
								// if not in logged participant array, then just print out name
								if (!in_array($students[$key]['id'], $logged_participants_ids)) {
									print "\n\t\t\t\t\t\t<td class='td$color' align='center'>\n\t\t\t\t\t\t\t<input type='checkbox' name='editors[]' value='$e' ".$checkstatus." /></td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>".$students[$key][fname]."</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>".$students[$key][email]."</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>0</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'></td>";
									
								// if in logged participants, then query for post and print summary
								} else {
									$userid = $students[$key]['id'];
									$postcount = getNumPosts($userid);
									$avg_rating = getAvgRating($userid);
									print "\n\t\t\t\t\t\t<td class='td$color' align='center'>\n\t\t\t\t\t\t\t<input type='checkbox' name='editors[]' value='$e' ".$checkstatus." />\n\t\t\t\t\t\t</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=review&amp;userid=".$students[$key][id]."&amp;userfname=".urlencode($students[$key][fname])."&amp;".htmlspecialchars($getvariables)."'>".$students[$key][fname]."</a\n\t\t\t\t\t\t></td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>".$students[$key][email]."</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>".$postcount."</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>".$avg_rating."</td>";
									$logged_students_id[] = $students[$key][id];
								}
								print "\n\t\t\t\t\t</tr>";
								$color = 1-$color;
							}
						}
						
						
						if ($curraction == 'list' && is_array($students) && isclass($_REQUEST[site])) 
							print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan='4'>\n\t\t\t\t\t\t\t<b>Participants not in Roster</b>\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>";
						
						$logged_participants = array();
						
						while ($a = db_fetch_assoc($r)) {
							
							$userid = $a['user_id'];
							$e = $a['user_id'];
							
							/******************************************************************************
							 * if listing participants and site has roster, 
							 * include here only non-roster participants (roster participants listed above)
							 * for each participant get # of posts and avg. rating
							 ******************************************************************************/
							 
							if (!in_array($userid, $logged_students_id) && $curraction == 'list') {
							
								$userid = $a[user_id];
								$logged_participants[] = $a[user_uname];
								
								$postcount = getNumPosts($userid);
								$avg_rating = getAvgRating($userid);
								
//								if (!$_SESSION[editors]) {
//									$checkstatus = " checked='checked'";
//								} else if (in_array($e,$_SESSION[editors])) {
//									$checkstatus = " checked='checked'";
//								} else {
//									$checkstatus = "";
//								}
								
								print "\n\t\t\t\t\t<tr>";
								print "\n\t\t\t\t\t\t<td class='td$color' align='center'>\n\t\t\t\t\t\t\t<input type='checkbox' name='editors[]' value='$e' ".$checkstatus." />\n\t\t\t\t\t\t</td>";
								print "\n\t\t\t\t\t\t<td class='td$color'>\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=review&amp;userid=".$a['user_id']."&amp;userfname=".urlencode($a['user_fname'])."&amp;".htmlspecialchars($getvariables)."'>".$a['user_fname']."</a>\n\t\t\t\t\t\t</td>";
								print "\n\t\t\t\t\t\t<td class='td$color'>".$a['user_email']."</td>";
								print "\n\t\t\t\t\t\t<td class='td$color'>".$postcount."</td>";
								print "\n\t\t\t\t\t\t<td class='td$color'>".$avg_rating."</td>";
								print "\n\t\t\t\t\t</tr>";
							}
							
							
								$discussion_date = $a['discussion_tstamp'];
								$discussion_date = timestamp2usdate($discussion_date);
								if ($action == "user") $sitename = $a['slot_name'];
								$page_link = $_full_uri."/index.php?action=site&amp;site=".$a['slot_name']."&amp;section=".$a['section_id']."&amp;page=".$a['page_id'];
								$fullstory_link = $_full_uri."/index.php?action=site&amp;site=".$a['slot_name']."&amp;section=".$a['section_id']."&amp;page=".$a['page_id']."&amp;story=".$a['story_id']."&amp;detail=".$a['story_id'];
								$dicuss_link = $_full_uri."/index.php?action=site&amp;site=".$a['slot_name']."&amp;section=".$a['section_id']."&amp;page=".$a['page_id']."&amp;story=".$a['story_id']."&amp;detail=".$a['story_id']."#".$a['discussion_id'];
								$shory_text_all = strip_tags(urldecode($a['story_text_short']));
								$shory_text = substr($shory_text_all,0,15)."...";
								$discussion_subject_all = urldecode($a['discussion_subject']);
								$discussion_subject = substr($discussion_subject_all,0,15)."...";
								
					
								/******************************************************************************
								 * Print Participants (depends on curraction
								 * Review: participant name, page > topic, discussion subject, rating, time
								 * User: participant name, site, page > topic, discussion subject, rating, time
								 * List: participant name, email, # of posts, average rating
								 ******************************************************************************/
								
								
								
								if ($curraction == 'review'  || $curraction == 'user') {
									print "\n\t\t\t\t\t<tr>";
									
									// user full name
									if ($curraction == 'user') {
										print "\n\t\t\t\t\t\t<td class='td$color' align='center'>\n\t\t\t\t\t\t\t<input type='checkbox' name='editors[]' value='$e' ".$checkstatus." />\n\t\t\t\t\t\t</td>";
										print "\n\t\t\t\t\t\t<td class='td$color'>".$a['user_fname']." (".$a['user_uname'].")</td>";
									} else {
										print "\n\t\t\t\t\t\t<td class='td$color' align='center'>\n\t\t\t\t\t\t\t<input type='checkbox' name='editors[]' value='$e' ".$checkstatus." />\n\t\t\t\t\t\t</td>";
										print "\n\t\t\t\t\t\t<td class='td$color'>\n\t\t\t\t\t\t\t<a href='$PHP_SELF?$sid&amp;action=review&amp;userid=".$a['user_id']."&amp;userfname=".urlencode($a['user_fname'])."&amp;".htmlspecialchars($getvariables)."'>".$a['user_fname']."</a>\n\t\t\t\t\t\t</td>";
									}
									//site links
									if ($curraction == 'user') {
										print "\n\t\t\t\t\t\t<td class='td$color'>\n\t\t\t\t\t\t\t<a href='#' onclick='opener.window.location=\"$_full_uri/index.php?action=site&site=".$a['slot_name']."\"'>".$a['slot_name']."</a>\n\t\t\t\t\t\t</td>";
									}
									print "\n\t\t\t\t\t\t<td class='td$color'>\n\t\t\t\t\t\t\t<a href='#' onclick='opener.window.location=\"$page_link\"'>".$a['page_title']."</a> &gt; \n\t\t\t\t\t\t\t<a href='#' onclick='opener.window.location=\"$fullstory_link\"'>".$shory_text."</a>\n\t\t\t\t\t\t</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>\n\t\t\t\t\t\t\t<a href='#' onclick='opener.window.location=\"$dicuss_link\"'>".$discussion_subject."</a>\n\t\t\t\t\t\t</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>".$a['discussion_rate']."</td>";
									print "\n\t\t\t\t\t\t<td class='td$color'>\n\t\t\t\t\t\t\t<a href='#' onclick='opener.window.location=\"$dicuss_link\"'>".$discussion_date."</a>\n\t\t\t\t\t\t</td>";
									
									print "\n\t\t\t\t\t</tr>";
								}
												
								
								$color = 1-$color;				
						}
			
						
					?>
					
				</table>

				<? 
				if ($action != 'email') {
				//	print $selectbuttons;
					print $buttons;
				}
				?>
				
				
				</form>
			</td>
		</tr>
	</table>
	
	<br />
	<div align='right'>
		<input type='button' value='Close Window' onclick='window.close()' />
	</div>
	<?
	
	function getNumPosts ($userid) {
		global $where, $orderby, $limit;
		
		$query2 = "
		SELECT 
			user_id, user_email, discussion_rate, discussion_tstamp
		FROM 
			discussion
		INNER JOIN story ON FK_story = story_id
		INNER JOIN page ON FK_page = page_id
		INNER JOIN section ON FK_section = section_id
		INNER JOIN site ON FK_site = site_id
		INNER JOIN user ON FK_author = user_id
		WHERE 
			$where AND user_id = '".addslashes($userid)."' 
		";		
		$r2 = db_query($query2);
		//$a2 = db_fetch_assoc($r2);
		$postcount = db_num_rows($r2);
		return $postcount;
		
	}
	
	function getAvgRating ($userid) {
		global $where, $orderby, $limit;
		
		$query2 = "
		SELECT 
			user_id, user_email, discussion_rate, discussion_tstamp
		FROM 
			discussion
		INNER JOIN story ON FK_story = story_id
		INNER JOIN page ON FK_page = page_id
		INNER JOIN section ON FK_section = section_id
		INNER JOIN site ON FK_site = site_id
		INNER JOIN user ON FK_author = user_id
		WHERE 
			$where AND user_id = '".addslashes($userid)."' 
		";		
		$r2 = db_query($query2);
		$postcount = db_num_rows($r2);
		$rating_sum = 0;
		if ($postcount == 1) {
			$avg_rating = $a2['discussion_rate'];
		} else {
			$adjpostcount = $postcount;
			
			
			while ($a2 = db_fetch_assoc($r2)) {
				if ($a2['discussion_rate'] == 0) 
					$adjpostcount = $adjpostcount - 1;
				$rating_sum = $rating_sum + $a2['discussion_rate'];
			}
			if ($adjpostcount)
				$avg_rating = round($rating_sum/$adjpostcount, 1);
			else
				$avg_rating = "n/a";
		}
		return $avg_rating;
	}
	
	?>

</body>
</html>