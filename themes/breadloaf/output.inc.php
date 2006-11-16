<? // output.inc.php
	// this script outputs the HTML resulting from action files
	
// what should this script have in it?
//	parses variables, topnav, leftnav, and content

// sample nav array:
/*
$nav = array(
		array("name"=>"About Us","url"=>"/aboutus.html"),
		array("name"=>"Subscribe","url"=>"/subscribe.html"),
		etc...
		);
*/

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

/*********************************************************
 * get all of the existing output buffers and place them inside our body
 *********************************************************/
$obContent = '';
while (ob_get_level())
	$obContent .= ob_get_clean();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<? include("themes/common/header.inc.php"); ?>
<? include("themes/breadloaf/css.inc.php"); ?>
<title><? echo $pagetitle; ?></title>
</head>

<body style='margin: 0px'>

<? print $obContent; ?>

<table width='100%' cellpadding='0' cellspacing='0'>
<tr>
<td class='header' align='center'>
<? include("themes/common/status.inc.php"); ?>
<? print $siteheader ?>

</td>
</tr>
<tr>
<td class='topnav' align='center'>

	<?
	// ---------------------------------------
	// top nav
	print " | ";
	foreach ($topnav as $item) {
		$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		print makelink($item,$samepage);
		print " | ";
	}
	
	print $topnav_extra;
	?>
</td>
<tr>
<tr>
<td class='contentarea'>
	<table width='750' align='center'>
	<tr><td class='leftnav' width='175' valign='top'>
		<?
		// ----------------------
		// left nav
		
		foreach ($leftnav as $item) {
			if ($item[type] == 'normal') {
				$samepage = (isset($page) && ($page == $item[id]))?1:0;
				if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
				print "<div>";
				print makelink($item,$samepage,'',1);
				print "</div>";
			}
			if ($item[type] == 'divider') {
				print "$item[extra]<br />";
			}
			if ($item[type] == 'heading') {
				if (!defined("CONFIGS_INCLUDED"))
					die("Error: improper application flow. Configuration must be included first.");
				print "<img src='$themesdir/breadloaf/images/bullet.gif' border='0' align='absmiddle' /> $item[name] :";
				if ($item[extra]) print "<div align='right'>$item[extra]</div>";
			}
		}
		print "<br />$leftnav_extra";
		?>
	</td>
	<td class='content' valign='top'>
		<? print $content; ?>
	</td>
	
	<?
	if (count($rightnav)) {
		print "<td align='right' class='rightnav'>";
		foreach ($rightnav as $item) {
			print "<a href='$item[url]'>$item[name]</a><br />";
		}
		print "</td>";
	}
	?>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>

<br />

<? print $sitefooter ?>
	
