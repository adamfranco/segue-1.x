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
?>
<head>
<? include("themes/common/header.inc.php"); ?>
<? include("themes/default/css.inc.php"); ?>
<title><? echo $pagetitle; ?></title>
</head>

<table width=700 align=center>
<tr>
	<td>
	
	<? include("themes/common/status.inc.php"); ?>
	
	<? print ($siteheader)?"$siteheader":""; ?>
	<? print $sitecrumbs; ?>
	
	<table width=100% cellspacing=0>
	<tr>
		<td align=center class='toppadding'>
		&nbsp;
		</td>
		
		<?
		// ---------------------------------------
		// top nav
		foreach ($topnav as $item) {
			$samepage = (isset($section) && ($section == $item[id]))?1:0;
			if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
			print "<td class='toptab' style='".(($samepage)?"border-bottom: 0px;":"background-color: #eee") . "' align=center>";
			print "<nobr>";
			print makelink($item,$samepage);
			print "</nobr></td>";
			print "<td class='toppadding'>&nbsp;</td>";
		}
		
		print "<td class='toppadding' align=right width=100%>&nbsp; " . $topnav_extra . "</td>";
		
		?>

		</td>
	</tr>
	<tr>
		<?
		$numtopnavs = count($topnav);
		$colspan = ($numtopnavs)?$numtopnavs*2:1;
		$colspan+=2;
		
		?>
		
		<td class='contentarea' colspan=<?echo $colspan?>>
		<table cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td valign=top>
			
			<table cellpadding=0 cellspacing=0 height=100%>
			<?
			
			// ----------------------
			// left nav
			
			foreach ($leftnav as $item) {
				if ($item[type] == 'normal') {
					$samepage = (isset($page) && ($page == $item[id]))?1:0;
					if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
					print "<tr><td align=right valign=middle class=".(($samepage)?"leftnavsel":"leftnav")."><nobr>";
					print makelink($item,$samepage,'',1);
					print "</nobr></td></tr>";
				}
				if ($item[type] == 'divider') {
					print "<tr><td class=leftpadding align=right>&nbsp;$item[extra]</td></tr>";
				}
				if ($item[type] == 'heading') {
					print "<tr><td class=leftpadding align=right><div style='font-size: 14px; font-weight: bold;' align=left>$item[name]</div>$item[extra]</td></tr>";
				}
			}
			print "<tr><td class=leftpadding valign=top height=100%>".$leftnav_extra."&nbsp;</tr>";
			?>
			</table>
			</td>
			<td class=smallercontentarea width=100% valign=top>
			<?
//			print "<pre>";print_r($leftnav);print "</pre>";
			print "$content";
			?>
			</td>
			
		
			<td valign=top style='padding-left: 10px'>
			<?
			foreach ($rightnav as $item) {
				if ($item[type]=='heading')
					print "<div style='padding-left:10px'><nobr><b><li>$item[name]</b></nobr></div>";
				else print "<div><nobr><a href='$item[url]'>$item[name]</a></nobr></div>";
			}
			?>
			</td>
		
		</table>
	
	</tr></table>
	
	<br>

	<? print $sitefooter ?>
	
	</td>
</tr>
</table>