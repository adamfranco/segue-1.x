<? // editor_access.php


$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);
?>
<html>
<head>
<title>Editor Access</title>

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

.td1 { 
	background-color: #ccc; 
}

.td0 { 
	background-color: #ddd; 
}

th { 
	background-color: #bbb; 
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

<? print $content; ?>
<? 
$sa = db_get_line("sites","name='$site'");
$sections = decode_array($sa['sections']);


$nl='';
if (!$user) $user = $auser;
else $nl = 'disabled';

$site_owner = db_get_value("sites","addedby","name='$site'");

if ($auser == $site_owner) {
//	print $sa[editors];	// Debug
	$editors = explode(",",$sa[editors]);
	$total_columns = count($editors)*3 +1;
	
	print "<table cellspacing=1 width='100%'>";
	print "<tr>";
		print "<td colspan=$total_columns style='font-variant: small-caps'>";
			print "Editor permissions for <b>$sa[title]</b>";
		print "</td>";
	print "</tr>";
	
	print "<tr>";
		print "<th> &nbsp; </th>";
		foreach($editors as $editor) {
			print "<th colspan=3  style='border-left: 2px solid #fff;'>$editor</th>";
		}
	print "</tr>";
	
	print "<tr>";
		print "<th>Section</th>";
		foreach($editors as $editor) {
			print "<td align=center  style='border-left: 2px solid #fff; background-color: #bbb; '>Add</td>";
			print "<td align=center style='background-color: #bbb;'>Edit</td>";
			print "<td align=center style='background-color: #bbb;'>Del</td>";
		}
	print "</tr>";
	
	$color = 0;
	
	print "<tr>";
	print "<td class=td$color style='font-variant: small-caps'><a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site\"'>$sa[title]</a></td>";
	$permissions=decode_array($sa[permissions]);
	foreach($editors as $user) {
		$classes=getuserclasses($user);
		if (isclass($site)) {
//			print "is class"; //debug
			foreach ($permissions as $e=>$p) {
				if (isclass($e)) {
					$l = array();
					if ($r = isgroup($e)) {
						$l = $r;
					} else $l[]=$e;
					foreach ($l as $c) {
						if ($classes[$c]) $user = $e;
					}
				}
			}
		}
		for ($i=0;$i<3;$i++) {
			print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
			print ($permissions[$user][$i])?"X":"&nbsp;";
			print "</td>";
		}
	}
	print "</tr>";
	$color = 1-$color;

	if (count($sections)) {
		foreach ($sections as $sec) {
			print "<tr>";
			$seca = db_get_line("sections","id=$sec");
			$secp = decode_array($seca[permissions]);
			print "<td class=td$color style='padding-left: 10px'>";
			if ($seca[type]=='section') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec\"'>";
			print "$seca[title]";
			if ($seca[type]=='section') print "</a>";
	//		print "<br><pre>";print_r($secp);print "</pre>";
			print "</td>";
			foreach($editors as $user) {
				for ($i=0;$i<3;$i++) {
					print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
					print ($seca[type]!='url' && $secp[$user][$i])?"X":"&nbsp;";
					print "</td>";
				}
			}
			print "</tr>";
			$color = 1-$color;
			$pages = decode_array($seca['pages']);
			foreach ($pages as $p) {
				$pa = db_get_line("pages","id=$p");
				$pp = decode_array($pa[permissions]);
				if ($pa[type]=='divider' || $pa[type]=='heading') next;
				print "<tr>";
				print "<td class=td$color style='padding-left: 20px'>";
				print "-&gt; ";
				if ($pa[type]=='page') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$s&page=$p\"'>";
				print "$pa[title]";
				if ($pa[type]=='page') print "</a>";
				foreach($editors as $user) {
					for ($i=0;$i<3;$i++) {
						print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
						print ($pa[type]!='url' && $pp[$user][$i])?"X":"&nbsp;";
						print "</td>";
					}
				}
				print "</tr>";
				$color = 1-$color;
		
				$stories = decode_array($pa['stories']);
				$j=1;
				foreach ($stories as $s) {
					print "<tr>";
					$sa = db_get_line("stories","id=$s");
					$sp = decode_array($sa[permissions]);
					print "<td class=td$color style='padding-left: 40px'>";
					/*if ($sa[type]=='story')*/ print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec&page=$p\"'>";
					print "$j. &nbsp; $sa[title]";
					/*if ($sa[type]=='story')*/ print "</a>";
	//				print "<br><pre>";print_r($sp);print "</pre>";
					print "</td>";					
					foreach($editors as $user) {
						print "<td class=td$color align=center".((1)?"  style='border-left: 2px solid #fff;'":"").">n/a</td>";
						for ($i=1;$i<3;$i++) {
							print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
							print ($sa[type]!='url' && $sp[$user][$i])?"X":"&nbsp;";
							print "</td>";
						}
					}
					print "</tr>";
					$color = 1-$color;
					$j++;
				}
			}
		}
	} else {
		print "<tr><td class=td$color colspan=4>No sections in this site.</td></tr>";
	}

	print "</table><BR>";
} else { 
	$total_columns = 4;
	print "<table cellspacing=1 width='100%'>";
	print "<tr>";
		print "<td colspan=$total_columns style='font-variant: small-caps'>";
			print "Editor permissions for <b>$user</b> on <b>$sa[title]</b>";
		print "</td>";
	print "</tr>";
	
	print "<tr>";
		print "<th> &nbsp; </th>";
//		foreach($editors as $editor) {
			print "<th colspan=3  style='border-left: 2px solid #fff;'>$user</th>";
//		}
	print "</tr>";
	
	print "<tr>";
		print "<th>Section</th>";
//		foreach($editors as $editor) {
			print "<td align=center  style='border-left: 2px solid #fff; background-color: #bbb; '>Add</td>";
			print "<td align=center style='background-color: #bbb;'>Edit</td>";
			print "<td align=center style='background-color: #bbb;'>Del</td>";
//		}
	print "</tr>";
	
	$color = 0;
	
	print "<tr>";
	print "<td class=td$color style='font-variant: small-caps'><a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site\"'>$sa[title]</a></td>";
	$permissions=decode_array($sa[permissions]);
//	foreach($editors as $user) {
		$classes=getuserclasses($user);
		if (isclass($site)) {
//			print "is class"; //debug
			foreach ($permissions as $e=>$p) {
				if (isclass($e)) {
					$l = array();
					if ($r = isgroup($e)) {
						$l = $r;
					} else $l[]=$e;
					foreach ($l as $c) {
						if ($classes[$c]) $user = $e;
					}
				}
			}
		}
		for ($i=0;$i<3;$i++) {
			print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
			print ($permissions[$user][$i])?"X":"&nbsp;";
			print "</td>";
		}
//	}
	print "</tr>";
	$color = 1-$color;

	if (count($sections)) {
		foreach ($sections as $sec) {
			print "<tr>";
			$seca = db_get_line("sections","id=$sec");
			$secp = decode_array($seca[permissions]);
			print "<td class=td$color style='padding-left: 10px'>";
			if ($seca[type]=='section') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec\"'>";
			print "$seca[title]";
			if ($seca[type]=='section') print "</a>";
	//		print "<br><pre>";print_r($secp);print "</pre>";
			print "</td>";
//			foreach($editors as $user) {
				for ($i=0;$i<3;$i++) {
					print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
					print ($seca[type]!='url' && $secp[$user][$i])?"X":"&nbsp;";
					print "</td>";
				}
//			}
			print "</tr>";
			$color = 1-$color;
			$pages = decode_array($seca['pages']);
			foreach ($pages as $p) {
				$pa = db_get_line("pages","id=$p");
				$pp = decode_array($pa[permissions]);
				if ($pa[type]=='divider' || $pa[type]=='heading') next;
				print "<tr>";
				print "<td class=td$color style='padding-left: 20px'>";
				print "-&gt; ";
				if ($pa[type]=='page') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$s&page=$p\"'>";
				print "$pa[title]";
				if ($pa[type]=='page') print "</a>";
//				foreach($editors as $user) {
					for ($i=0;$i<3;$i++) {
						print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
						print ($pa[type]!='url' && $pp[$user][$i])?"X":"&nbsp;";
						print "</td>";
					}
//				}
				print "</tr>";
				$color = 1-$color;
		
				$stories = decode_array($pa['stories']);
				$j=1;
				foreach ($stories as $s) {
					print "<tr>";
					$sa = db_get_line("stories","id=$s");
					$sp = decode_array($sa[permissions]);
					print "<td class=td$color style='padding-left: 40px'>";
					/*if ($sa[type]=='story')*/ print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec&page=$p\"'>";
					print "$j. &nbsp; $sa[title]";
					/*if ($sa[type]=='story')*/ print "</a>";
	//				print "<br><pre>";print_r($sp);print "</pre>";
					print "</td>";					
//					foreach($editors as $user) {
						print "<td class=td$color align=center".((1)?"  style='border-left: 2px solid #fff;'":"").">n/a</td>";
						for ($i=1;$i<3;$i++) {
							print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
							print ($sa[type]!='url' && $sp[$user][$i])?"X":"&nbsp;";
							print "</td>";
						}
//					}
					print "</tr>";
					$color = 1-$color;
					$j++;
				}
			}
		}
	} else {
		print "<tr><td class=td$color colspan=4>No sections in this site.</td></tr>";
	}
	print "</table><BR>";
}
?>

<div align=right><input type=button value='Close Window' onClick='window.close()'></div>