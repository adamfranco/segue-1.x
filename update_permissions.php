<? 
// this script updates the permissions from arrays in fields to permissions table
// it currently creates a number of permissions table entries without user values
// these entries without user values are deleted 
// this script is based on editor_access.php


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

$query = "select * from sites";
$r = db_query($query);

while ($a = db_fetch_assoc($r)) {
	$site = $a['name'];
	$site_id = $a['id'];
	$sa = db_get_line("sites","name='$site'");
	$sections = decode_array($sa['sections']);
	$viewpermissions = $sa['viewpermissions'];
	
	$nl='';
	if (!$user) $user = $auser;
	else $nl = 'disabled';
	
	$site_owner = db_get_value("sites","addedby","name='$site'");
	
	//if ($auser == $site_owner) {
	if ($auser) {
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
		$add = 0; $edit = 0; $delete = 0;
		if ($viewpermissions == "anyone") {
			$user = "everyone";
			update_permissions ($permissions,$site,$user,"site",$site_id,$add,$edit,$delete);
			$user = "institute";
			update_permissions ($permissions,$site,$user,"site",$site_id,$add,$edit,$delete);
		}
		if ($viewpermissions == "midd") {
			$user = "institute";
			update_permissions ($permissions,$site,$user,"site",$site_id,$add,$edit,$delete);
		}
		
		//add editors to permissions only if there are editors to add duh...
		//for some reason will still has permissions with null user....
		if (count($editors) > 0) {
		
			print "editors:	".count($editors)."<br>";
			print "site id:	".$site."<br>";	
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
				
				$add = 0; $edit = 0; $delete = 0;
				
				for ($i=0;$i<3;$i++) {			
					print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
					print ($permissions[$user][$i])?"X":"&nbsp;";
					if ($permissions[$user][$i]) {
						//print "1";
						if ($i==0) {
							$add = 1;
							print "a";
						} else if ($i==1) {
							$edit = 1;
							print "e";
						} else {
							$delete = 1;
							print "d";
						}
					}			
					print "</td>";
				}
				update_permissions ($permissions,$site,$user,'site',$site_id,$add,$edit,$delete);		
			}
			print "</tr>";
			$color = 1-$color;
		
			if (count($sections)) {
				foreach ($sections as $sec) {
					print "<tr>";
					$seca = db_get_line("sections","id=$sec");
					$section_id = $seca[id];
					$secp = decode_array($seca[permissions]);
					print "<td class=td$color style='padding-left: 10px'>";
					if ($seca[type]=='section') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec\"'>";
					print "$seca[title]";
					if ($seca[type]=='section') print "</a>";
			//		print "<br><pre>";print_r($secp);print "</pre>";
					print "</td>";
					
					$add = 0; $edit = 0; $delete = 0;
					if ($viewpermissions == "anyone") {
						$user = "everyone";
						update_permissions ($permissions,$site,$user,"section",$section_id,$add,$edit,$delete);
						$user = "institute";
						update_permissions ($permissions,$site,$user,"section",$section_id,$add,$edit,$delete);
					}
					if ($viewpermissions == "midd") {
						$user = "institute";
						update_permissions ($permissions,$site,$user,"section",$section_id,$add,$edit,$delete);
					}
		
					foreach($editors as $user) {
						$add = 0; $edit = 0; $delete = 0;
		
						for ($i=0;$i<3;$i++) {
							print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
							print ($seca[type]!='url' && $secp[$user][$i])?"X":"&nbsp;";
							if ($seca[type]!='url' && $secp[$user][$i]) {
								//print "1";
								if ($i==0) {
									$add = 1;
									print "a";
								} else if ($i==1) {
									$edit = 1;
									print "e";
								} else {
									$delete = 1;
									print "d";
								}
							}
							print "</td>";
						}
						update_permissions ($permissions,$site,$user,'section',$section_id,$add,$edit,$delete);
					}
					print "</tr>";
					$color = 1-$color;
					$pages = decode_array($seca['pages']);
					foreach ($pages as $p) {
						$pa = db_get_line("pages","id=$p");
						$page_id = $pa[id];
						$pp = decode_array($pa[permissions]);
						if ($pa[type]=='divider' || $pa[type]=='heading') next;
						print "<tr>";
						print "<td class=td$color style='padding-left: 20px'>";
						print "-&gt; ";
						if ($pa[type]=='page') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$s&page=$p\"'>";
						print "$pa[title]";
						if ($pa[type]=='page') print "</a>";
						
						$add = 0; $edit = 0; $delete = 0;
						if ($viewpermissions == "anyone") {
							$user = "everyone";
							update_permissions ($permissions,$site,$user,"page",$page_id,$add,$edit,$delete);
							$user = "institute";
							update_permissions ($permissions,$site,$user,"page",$page_id,$add,$edit,$delete);
						}
						if ($viewpermissions == "midd") {
							$user = "institute";
							update_permissions ($permissions,$site,$user,"page",$page_id,$add,$edit,$delete);
						}	
						
						foreach($editors as $user) {
							$add = 0; $edit = 0; $delete = 0;
							for ($i=0;$i<3;$i++) {
								print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
								print ($pa[type]!='url' && $pp[$user][$i])?"X":"&nbsp;";
								if ($pa[type]!='url' && $pp[$user][$i]) {
									//print "1";
									if ($i==0) {
										$add = 1;
										print "a";
									} else if ($i==1) {
										$edit = 1;
										print "e";
									} else {
										$delete = 1;
										print "d";
									}
								}
								print "</td>";
							}
							update_permissions ($permissions,$site,$user,'page',$page_id,$add,$edit,$delete);
						}
						
						print "</tr>";
						$color = 1-$color;
				
						$stories = decode_array($pa['stories']);
						$j=1;
						foreach ($stories as $s) {
							print "<tr>";
							$sa = db_get_line("stories","id=$s");
							$story_id = $sa[id];
							$sp = decode_array($sa[permissions]);
							print "<td class=td$color style='padding-left: 40px'>";
							/*if ($sa[type]=='story')*/ print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec&page=$p\"'>";
							print "$j. &nbsp; $sa[title]";
							/*if ($sa[type]=='story')*/ print "</a>";
			//				print "<br><pre>";print_r($sp);print "</pre>";
							print "</td>";	
							$add = 0; $edit = 0; $delete = 0;
							if ($viewpermissions == "anyone") {
								$user = "everyone";
								update_permissions ($permissions,$site,$user,"story",$story_id,$add,$edit,$delete);
								$user = "institute";
								update_permissions ($permissions,$site,$user,"story",$story_id,$add,$edit,$delete);
							}
							if ($viewpermissions == "midd") {
								$user = "institute";
								update_permissions ($permissions,$site,$user,"story",$story_id,$add,$edit,$delete);
							}	
												
							foreach($editors as $user) {
								print "<td class=td$color align=center".((1)?"  style='border-left: 2px solid #fff;'":"").">n/a</td>";
								$add = 0; $edit = 0; $delete = 0;
								for ($i=1;$i<3;$i++) {
									print "<td class=td$color align=center".(($i==0)?"  style='border-left: 2px solid #fff;'":"").">";
									print ($sa[type]!='url' && $sp[$user][$i])?"X":"&nbsp;";
									if ($sa[type]!='url' && $sp[$user][$i]) {
										//print "1";
										if ($i==0) {
											$add = 1;
											print "a";
										} else if ($i==1) {
											$edit = 1;
											print "e";
										} else {
											$delete = 1;
											print "d";
										}
									}
									print "</td>";
								}
								update_permissions ($permissions,$site,$user,'story',$story_id,$add,$edit,$delete);
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
	}
}

//removes any permissions records with no user specified
$sitequery2 = "delete from permissions where user=''";
db_query($sitequery2);


function update_permissions ($permissions,$site,$user,$scope,$scopeid,$add,$edit,$delete) {
	$sitequery = "insert into permissions set 
			user = '$user', 
			site = '$site',  
			scope = '$scope',
			scopeid = '$scopeid',
			v = '1', a = '$add', e = '$edit', d = '$delete'";
	//print "<br>".$sitequery;
	db_query($sitequery);
}
?>

<div align=right><input type=button value='Close Window' onClick='window.close()'></div>