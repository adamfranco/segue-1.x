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
//printpre($_REQUEST);
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * Action: list, review, user
 ******************************************************************************/
if ($_REQUEST['action']) {
	$curraction = $_REQUEST['action'];
	$action = $_REQUEST['action'];
} 

/******************************************************************************
 * Scope: site, discussion/assessment
 ******************************************************************************/

if ($_REQUEST['scope']) $scope = $_REQUEST['scope'];

$sql = $_REQUEST['sql'];
$query_custom = $_REQUEST['newquery'];

/******************************************************************************
 * Sort order: 
 ******************************************************************************/

if ($_REQUEST['order']) {
	$order = urldecode($_REQUEST['order']);
} else if (!$_REQUEST['order'] && $action == "user") {
	$order = "discussion_tstamp DESC";
} else {
	$order = "user_fname ASC";
}

$orderby = " ORDER BY $order";

/******************************************************************************
 * Username and id:
 * 
 ******************************************************************************/
if ($_REQUEST['findall']) {
	$userid = "'%'";
	$useruname = "";
	$find = "";
} else if ($_REQUEST['find']) {
	$findall = "";
} else if ($_REQUEST['useruname']) {
	$useruname = $_REQUEST['useruname'];
	$userid = db_get_value ("user", "user_id", "user_uname = '$useruname'");
	if (!$userid) error("invalid username");
	$userfname = db_get_value ("user", "user_fname", "user_id = $userid");
} else if ($_REQUEST['userid']) {
	$userid = $_REQUEST['userid'];
} else {
	$userid = $_SESSION['aid'];
}

// if full name and not username (ie clicking full name to review...)
if ($_REQUEST['userfname'] && !$_REQUEST['useruname']) {
	$userfname = urldecode($_REQUEST['userfname']);
	$userfname = db_get_value ("user", "user_fname", "user_id = $userid");
	$useruname = db_get_value ("user", "user_uname", "user_id = $userid");
}

/******************************************************************************
 * Story and Site ids
 ******************************************************************************/


if ($_REQUEST['storyid']) $storyid = $_REQUEST['storyid'];

$siteid = $_REQUEST['siteid'];
$class_id = $_REQUEST['site'];


/******************************************************************************
 * if class get all members of class from ldap
 * returns array with uname, fname and type
 ******************************************************************************/

if (isclass($class_id)) {
	$students = getclassstudents($class_id);
	//printpre($students);
}


/******************************************************************************
 * Query: where clause
 * story, site, and/or user or 
 * all users, all sites
 ******************************************************************************/

if ($_REQUEST['findall'] && !$_REQUEST['find']) {
	$where = "user_id > 0";
} else if ($_REQUEST['find']) {
	$useruname = $_REQUEST['useruname'];
	$userid = db_get_value ("user", "user_id", "user_uname = '$useruname'");
	if ($userid) {
		$where = "user_id = $userid";
	} else {
		error("invalid username");
		$where = "user_id > 0";
	}
} else if ($scope == "site") {
	$where = "site_id = $siteid";
} else if ($action != "user") {
	$where = "story_id = $storyid";	
} else if ($userid && $action == "user") {
	$where = "user_id = $userid";
}

if ($_REQUEST['userid'] && !$_REQUEST['findall'] && $action == "review" && $_REQUEST['userfname']) {
	$where .= " AND user_id = $userid";
}

if ($_REQUEST['findsite'] && $action == "review") {
	$findsite = $_REQUEST['findsite'];
	$where .= " AND slotname = '$findsite'";
}


/******************************************************************************
 * Query: select and order clauses
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
 * Query: number of rows (i.e. number of posts for where clause) 
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
	

	if (!isset($lowerlimit)) $lowerlimit = 0;
	if (!isset($range)) $range = 30;
	if ($lowerlimit < 0) $lowerlimit = 0;
	$limit = " LIMIT $lowerlimit,$range";

/******************************************************************************
 * Query: number of participants (i.e. distinct users)
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
	//print $numparticipants."<br>";
	
	if ($action == "list") $numrows = $numparticipants;
	//print $numrows."<br>";

/******************************************************************************
 * Query: all discussion post information based on select
 * select summary info for each user
 * select all post info for all specified users
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

<script lang="JavaScript">

function changeOrder(order) {
	f = document.searchform;
	f.order.value=order;
	f.submit();
}

</script>

</head>
<!-- <body onLoad="document.addform.uname.focus()">  -->
<body onLoad="initEditor()">

<?

/******************************************************************************
 * If admin print out admin tools (e.g. add/edit users, classes, slots updates
 ******************************************************************************/

if ($_SESSION['ltype']=='admin') {
	print "<table width=100%  class='bg'>";
	print "<tr><td class='bg'>";
	print "Logs: <a href='viewsites.php?$sid&site=$site'>sites</a>";
	print " | <a href='viewlogs.php?$sid&site=$site'>users</a>";
	print "</td><td align='right' class='bg'>";
	print "<a href='users.php?$sid&site=$site'>add/edit users</a> | ";
	print "<a href='classes.php?$sid&site=$site'>add/edit classes</a> | ";
	print "<a href='add_slot.php?$sid&site=$site'>add/edit slots</a> | ";
	print "<a href='update.php?$sid&site=$site'>segue updates</a>";
	print "</td></tr>";
	print "</table>";
}

/******************************************************************************
 * Links: Roster | Participation | Logs | Your Posts
 ******************************************************************************/

print "<table width=100%  class='bg'>";

// for admins print out participation select and where and order by sql
print "<tr><td class='bg'>";
if ($_SESSION['ltype']=='admin') {
	//print $action.": ";
	//print "WHERE ".$where." ORDER BY ";
	//print $order;
}
print "</td>";

print "<td class='bg' align='right'>";
// roster
if (isclass($_REQUEST[site])) print "<a href=add_students.php?$sid&name=$site>Roster</a> |";

// participation (not link when coming from home)
if ($_REQUEST[from] != "home") {
	if ($action == "user") {
		print " <a href='email.php?$sid&siteid=$siteid&site=$site&action=list&scope=site'>Participation</a>";
	} else {
		print " Participation";
	}
	if ($action == "user") {
		print " - Your Posts";
	} else {
		print " - <a href='email.php?$sid&siteid=$siteid&site=$site&action=user'>Your Posts</a>";
	}
	
	// logs (not link when coming from home)
	print " | <a href='viewlogs.php?$sid&site=$site'>Logs</a>";
} else {
	print " Your Posts";
}

print "</td></tr>";
print "</table><br>";
?>

<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr><td>

	<table cellspacing=1 width='100%'>
	<tr><td>
		<form action="<? echo $PHP_SELF ?>" method=get name=searchform>
		<input type=hidden name='order' value='<? echo urlencode($order) ?>'>
		<input type=hidden name='action' value='<? echo $action ?>'>
		<input type=hidden name='storyid' value='<? echo $storyid ?>'>
		<input type=hidden name='siteid' value='<? echo $siteid ?>'>
		<input type=hidden name='site' value='<? echo $site ?>'>
		<input type=hidden name='userid' value='<? echo $userid ?>'>
		<input type=hidden name='from' value='<? echo $from ?>'>
		<input type=hidden name='findall' value='<? echo $findall ?>'>
		<input type=hidden name='find' value='<? echo $find ?>'>
		<input type=hidden name='findsite' value='<? echo $findsite ?>'>
		<input type=hidden name='userfname' value='<? echo urlencode($userfname) ?>'>

<!-- 		<input type=submit name='search' value='Find'> -->
<!-- 		<input type=submit name='findall' value='Find All'> -->
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
		print "$curr of $tpages ";
		//print "(prev=$prev lowerlimit=$lowerlimit next=$next )";
		if ($prev != $lowerlimit)
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&$getvariables&action=$curraction&findall=$findall\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&$getvariables&action=$curraction&findall=$findall\"'>\n";
		?>

<!-- 		</form> -->
	
	</td></tr>
	</table>
	
	<? if (!db_num_rows($r)) {
		print "No participants found. Try extending the scope to all participants in the site";
		
/******************************************************************************
 * depending on action print out either:
 * list of participants
 * email UI
 * sent email confirmation
 ******************************************************************************/

	} //else {
		//$numusers = db_num_rows($r);
		//print "Total participants found: ".$numparticipants;
		
		/******************************************************************************
		 * Navigation Email | List | Review participants in discussion or site
		 ******************************************************************************/
		//print "<form action=$PHP_SELF method=post name=emailform>";
		print "<table><tr><td ><div style='font-size: 12px'>";
		
		// lists all participants with summary of posts and avg. rating
		if ($curraction == "list") {
			print "<a href=$PHP_SELF?$sid&action=email&$getvariables$getusers>Email</a> | ";
			print "List | ";
			print "<a href=$PHP_SELF?$sid&action=review&$getvariables&order=$order>Review</a> - ";
			print $numparticipants." participants";
		
		// reviews posts by a given user to a given site/discussion/assessment
		// or reviews all posts by all users to a given site/discussion/assessment
		} else if ($curraction == 'review') {
			print "<a href=$PHP_SELF?$sid&action=email&$getvariables$getusers&order=user_fname>Email</a> | ";
			print "<a href=$PHP_SELF?$sid&action=list&$getvariables&order=user_fname>List</a> | ";
			
			if ($userid) {
				print "<a href=$PHP_SELF?$sid&action=review&$getvariables>Review all</a> | ";
				print $numrows." posts from ".urldecode($userfname);
			} else {
				print "Review - ";
				print $numrows." posts from ".$numparticipants." participants";
			}
		
		// displays all posts of a given user across all sites	
		} else if ($curraction == 'user') {
			if ($_SESSION['ltype'] == "admin") {
				//print "<form>";
				print "username: <input type = text name=useruname value='".$useruname."' class=textfield>";
				//print "site: <input type = text name=findsite value='".$findsite."' class=textfield>";
				print " <input type=submit name='find' value='Find'>";
				print " <input type=submit name='findall' value='Find All'>  ";
				//print " (".urldecode($userfname)." ) ";

				//print "</form>";
			}
			if ($userid) {
				//print "<a href=$PHP_SELF?$sid&action=review&$getvariables>Review all</a> | ";
				print $numrows." posts";
			}
					
		// emails all participants currently listed	
		} else if ($curraction == 'email') {
			print "Email | ";
			print "<a href=$PHP_SELF?$sid&action=list&$getvariables&order=user_fname>List</a> | ";
			print "<a href=$PHP_SELF?$sid&action=review&$getvariables>Review</a> - ";
			print $numparticipants." participants";
		
		// sends email to all participants in email list	
		} else if ($curraction == 'send') {
			print "<a href=$PHP_SELF?$sid&action=email&$getvariables$getusers&order=user_fname>Email</a> | ";
			print "<a href=$PHP_SELF?$sid&action=list&$getvariables&order=user_fname>List</a> | ";
			print "<a href=$PHP_SELF?$sid&action=review&$getvariables$getusers&order=$order>Review</a> - ";
			print $numparticipants." participants";
		}
		
		// if action is not listing of a user's posts across all sites, then include scope 
		// select (i.e. participants in this discussions/assessment or in this site
		if ($curraction != 'user') {
			print " in this ";
			print "<select name=scope>";	
				
			// if viewed from roster, then no storyid and no specific discussion/assessment is viewable
			if ($_REQUEST[storyid] != "") {
				print "<option";
				($scope=='discussion')? print " value='discussion' selected": print "";
				print ">discussion/assessment";
			}
						
			if ($scope=='site' || $_REQUEST[site] != "") {
				print "<option";
				($scope=='site')? print " value='site' selected": print "";
				print ">site";
			}
			print "</select>";
			print "<input type=submit name='update' value='Update'>";			
			
		} 
		
		?>
		</form>
		</div></td></tr></table>	
		
		<?
		/******************************************************************************
		 * if action is email, then compile to list and print out email UI
		 ******************************************************************************/
		
		if ($curraction == 'email') {
			
			$emaillist = array();
			while ($a2 = db_fetch_assoc($r)) {
				array_push($emaillist, $a2['user_email']);	
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
			
			<form action="<? echo $PHP_SELF ?>" method=post name=emailform>
			<table width=100%>
			<tr><td align='right'>To:</td><td><? echo $to ?></td><td align='right'></td></tr>
			<? if ($_SESSION['ltype']=='admin' && $_SESSION['lfname'] != $_SESSION['afname']) {
					print "<tr><td align='right'>From:</td><td>".$_SESSION['lfname']." as ".$_SESSION['afname']."</td><td align='right'></td></tr>";
				} else {
					print "<tr><td align='right'>From:</td><td>".$_SESSION['afname']."</td><td align='right'></td></tr>";
				}
			?>
			<tr><td align='right'>Cc:</td><td><? echo $_SESSION['afname'] ?></td><td align='right'></td></tr>
			<tr><td align='right'>Subject</td><td><input type=text name='subject' value='' size=50> <input type=submit name='email' value='Send'></td><td align='left'></td></tr>
			<tr><td></td><td align='left'>
			<?
			require_once("htmleditor/editor.inc.php");
			include("sniffer.inc.php");
			addeditor ("body",60,20,$text,"discuss"); 
			print $content;
			?>
<!-- 			<textarea name=body cols=60 rows=20></textarea> -->
			</td><td><td align='right'></td></tr>
			</table>
			<input type=hidden name='action' value='send'>
			<input type=hidden name='scope' value='<? echo $scope ?>'>
			<input type=hidden name='storyid' value='<? echo $storyid ?>'>
			<input type=hidden name='siteid' value='<? echo $siteid ?>'>
			<input type=hidden name='site' value='<? echo $site ?>'>
			<input type=hidden name='to' value='<? echo $to ?>'>
<!-- 			<input type=hidden name='from' value='<? echo $from ?>'> -->
			<input type=hidden name='headers' value='<? echo $headers ?>'>
			</form>
			<?
			$r = db_query($query);
			exit();
			
		/******************************************************************************
		 * if action is send then mail subject and body
		 ******************************************************************************/

		} else if ($curraction == 'send') {
			if ($_SESSION['ltype']=='admin' && $_SESSION['lfname'] != $_SESSION['afname']) {
				$subject = $subject." (sent by Segue Admin: ".$_SESSION['lfname'].")";
			}
			print "<table>";
			print "<tr><td>To:</td><td>".$to."</td></tr><br><hr>\n";
			print "<tr><td>From:</td><td>".$_SESSION['afname']."</td></tr>\n";
			print "<tr><td>Cc:</td><td>".$_SESSION['afname']."</td></tr>\n";
			print "<tr><td>Subject:</td><td>".$subject."</td></tr>\n";
			print "<tr><td></td><td>".$body."</td></tr>\n";
			//print "<tr><td></td><td>".$headers."</td></tr>\n";  //debug
			print "</table>\n";
			print "</div>\n";
			//print htmlspecialchars("emailing $to, $subject, $body, $headers");
			//mail($to,$subject,$body,"From: $from");
			if (!mail($to, $subject, $body, $headers)) print "AN ERROR OCCURED SENDING MAIL!";
//			print $query;
//			$r = db_query($query);
			exit();
		}
	// } 
	
	/******************************************************************************
	 * Print out table of participant names
	 ******************************************************************************/
	
	?>
	<table width='100%'>
		<tr>
		<!-- <th>full name</th> -->
		
		<?
		print "<th><a href=# onClick=\"changeOrder('";
		if ($order =='user_fname desc') print "user_fname asc";
		else print "user_fname desc";
		print "')\">Participant Name";
		if ($order =='user_fname asc') print " &or;";
		if ($order =='user_fname desc') print " &and;";	
		print "</a></th>";


		if ($curraction == 'review'  || $curraction == 'user') {
			if ($curraction == 'user') print "<th>Site</th>";
			print "<th>Page > Topic</th>";
			print "<th>discussion_subject</th>";
/* 			print "<th>discussion_rate</th>"; */
/* 			print "<th>discussion_tstamp</th>"; */
			 			
			print "<th><a href=# onClick=\"changeOrder('";
			if ($order =='discussion_rate asc') print "discussion_rate desc";
			else print "discussion_rate asc";
			print "')\">Rating<br>Grade";
			if ($order =='discussion_rate asc') print " &or;";
			if ($order =='discussion_rate desc') print " &and;";	
			print "</a></th>";
			
			print "<th><a href=# onClick=\"changeOrder('";
			if ($order =='discussion_tstamp asc') print "discussion_tstamp desc";
			else print "discussion_tstamp asc";
			print "')\">Date Time";
			if ($order =='discussion_tstamp asc') print " &or;";
			if ($order =='discussion_tstamp desc') print " &and;";	
			print "</a></th>";
			
		} else {
			print "<th>Email</th>";
			print "<th># of Posts</th>";
			print "<th>Avg. Rating/Grade</th>";
		}
		?>
		</tr>		
		<? 
		/******************************************************************************
		 * Print out stats and links
		 ******************************************************************************/
		 	$color = 0;
			while ($a = db_fetch_assoc($r)) {
			
				/******************************************************************************
				 * If listing participants, get # of posts and avg. rating
				 ******************************************************************************/

				if ($curraction == 'list') {
					$userid = $a[user_id];
					$logged_participants[] = $a[user_uname];
					
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
						$where AND user_id = $userid $orderby $limit
					";		
					$r2 = db_query($query2);
					//$a2 = db_fetch_assoc($r2);
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
				}
							
				$discussion_date = $a['discussion_tstamp'];
				$discussion_date = timestamp2usdate($discussion_date);
				if ($action == "user") $sitename = $a['slot_name'];
				$page_link = $_full_uri."/index.php?action=site&site=".$a['slot_name']."&section=".$a['section_id']."&page=".$a['page_id'];
				$fullstory_link = $_full_uri."/index.php?action=site&site=".$a['slot_name']."&section=".$a['section_id']."&page=".$a['page_id']."&story=".$a['story_id']."&detail=".$a['story_id'];
				$dicuss_link = $_full_uri."/index.php?action=site&site=".$a['slot_name']."&section=".$a['section_id']."&page=".$a['page_id']."&story=".$a['story_id']."&detail=".$a['story_id']."#".$a['discussion_id'];
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

				print "<tr>";
				
				if ($curraction == 'review'  || $curraction == 'user') {
					// user full name
					if ($curraction == 'user') {
					//	print "<td class=td$color><a href=$PHP_SELF?$sid&action=review&userid=".$a['user_id']."&userfname=".urlencode($a['user_fname'])."&".$getvariables.">".$a['user_fname']."</a></td>";
						print "<td class=td$color>".$a['user_fname']." (".$a['user_uname'].")</td>";
					} else {
						print "<td class=td$color><a href=$PHP_SELF?$sid&action=review&userid=".$a['user_id']."&userfname=".urlencode($a['user_fname'])."&".$getvariables.">".$a['user_fname']."</a></td>";
					}
					//site links
					if ($curraction == 'user') {
						print "<td class=td$color><a href='#' onClick='opener.window.location=\"$_full_uri/index.php?action=site&site=".$a['slot_name']."\"'>".$a['slot_name']."</a></td>";
					}
					print "<td class=td$color><a href='#' onClick='opener.window.location=\"$page_link\"'>".$a['page_title']."</a> > <a href='#' onClick='opener.window.location=\"$fullstory_link\"'>".$shory_text."</a></td>";
					print "<td class=td$color><a href='#' onClick='opener.window.location=\"$dicuss_link\"'>".$discussion_subject."</a></td>";
					print "<td class=td$color>".$a['discussion_rate']."</td>";
					print "<td class=td$color><a href='#' onClick='opener.window.location=\"$dicuss_link\"'>".$discussion_date."</a></td>";
				} else {
					print "<td class=td$color><a href=$PHP_SELF?$sid&action=review&userid=".$a['user_id']."&userfname=".urlencode($a['user_fname'])."&".$getvariables.">".$a['user_fname']."</a></td>";
					print "<td class=td$color>".$a['user_email']."</td>";
					print "<td class=td$color>".$postcount."</td>";
					print "<td class=td$color>".$avg_rating."</td>";
				}
								
				print "</tr>";
				$color = 1-$color;
			}
			
			/******************************************************************************
			 * Add to list of participants students in class who have not yet
			 * participated 
			 ******************************************************************************/
			if ($curraction != 'review' && $curraction != 'user') {
			
				if (is_array($students)) {
					foreach (array_keys($students) as $key) {
							if (!in_array($students[$key][uname], $logged_participants)) {
								print "<tr>";
								print "<td class=td$color>".$students[$key][fname]."</td>";
								print "<td class=td$color>".$students[$key][email]."</td>";
								print "<td class=td$color>0</td>";
								print "<td class=td$color></td>";
								print "</tr>";
							}
					}
				}
			}

		?>
	</table>	
</td></tr>
</table>

<BR>
<div align='right'><input type=button value='Close Window' onClick='window.close()'></div>
<?

?>
