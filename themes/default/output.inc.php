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

/*********************************************************
 * get all of the existing output buffers and place them inside our body
 *********************************************************/
$obContent = '';
while (ob_get_level())
	$obContent .= ob_get_clean();

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo $pagetitle; ?></title>

<?
/******************************************************************************
 * Commom header stuff
 ******************************************************************************/
if (!preg_match('/^[a-z_0-9]+$/i', $theme))
	die ('Error: invalid theme, "'.$theme.'".');
include("themes/common/header.inc.php");
include("themes/$theme/css.inc.php"); 

?>
</head>
<body>

<? print $obContent; ?>

<table width='95%' align='center'>
	<tr>
		<td>
		<?
		/******************************************************************************
		 * Site Header, Status bar, crumbs
		 ******************************************************************************/ 	
		include("themes/common/status.inc.php");
		print ($siteheader)?"$siteheader":"";
		
		print "\n\t\t\t<div style='float: right; height: 20px; overflow: hidden;'>";
		include("themes/common/search.inc.php"); 
		print "\n\t\t\t</div>";
		
		print "\n\t\t\t<div style='float: left;'>";
		print $sitecrumbs;
		print "\n\t\t\t</div>";
	//	print $sitecrumbs;
		?>
		
			<table width='100%' cellspacing='0' style='clear: both;'>
				<tr>
					<td align='center' class='toppadding'>&nbsp;</td>
					
					<?
					/******************************************************************************
					 * Section Navigation
					 ******************************************************************************/
					$nextorder = 0;
					foreach ($topnav as $item) {
						$reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page."&amp;reorderSection=".$item['id']."&amp;newPosition=";
						
						$samepage = (isset($section) && ($section == $item[id]))?1:0;
						if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
						print "\n\t\t\t\t\t<td class='toptab' style='white-space: nowrap; ".(($samepage)?"border-bottom: 0px;":"background-color: #eee;") . "' align='center'>";
	
						if ($_REQUEST['showorder'] == "section") {
							print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
							for ($i=0; $i<count($topnav); $i++) {
								print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
							}
							print "\n\t\t\t\t\t\t\t\t\t\t</select>";
						}

						print "\n\t\t\t\t\t\t".makelink($item,$samepage);
						print "\n\t\t\t\t\t</td>";
						print "\n\t\t\t\t\t<td class='toppadding'>&nbsp;</td>";
						$nextorder++;
					}		
					print "\n\t\t\t\t\t<td class='toppadding' align='right' width='100%'>\n\t\t\t\t\t\t&nbsp; " . $topnav_extra . "\n\t\t\t\t\t</td>";
					?>
					
				</tr>
				<tr>
					<?
					/******************************************************************************
					 * Determine cols needed for sections
					 ******************************************************************************/
					$numtopnavs = count($topnav);
					$colspan = ($numtopnavs)?$numtopnavs*2:1;
					$colspan+=2;		
					?>
					
					<td class='contentarea' colspan='<?echo $colspan?>'>
						<table cellpadding='0' cellspacing='0' width='100%'>
							<tr>
								<td valign='top'>			
									<table cellpadding='0' cellspacing='0' style='height: 100%'>
									<?
			/******************************************************************************
									 * Left Column
									 ******************************************************************************/	
									$nextorder = 0;
									foreach ($leftnav as $item) {
										$reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page."&amp;reorderPage=".$item['id']."&amp;newPosition=";

										if ($item[type] == 'normal') {
											$samepage = (isset($page) && ($page == $item[id]))?1:0;
											if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
											
											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td align='right' valign='middle' class='".(($samepage)?"leftnavsel":"leftnav")."' style='white-space: nowrap;'>";
											
											// reorder UI
											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($leftnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}

											print "\n\t\t\t\t\t\t\t\t\t\t\t\t".makelink($item,$samepage,'',1);
											print "\n\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
										}
										
										if ($item[type] == 'content' || $item[type] == 'rss' || $item[type] == 'tags' || $item[type] == 'participants') {
											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td align='right' valign='middle' class='".(($samepage)?"leftnavsel":"leftnavsel")."'>";
											if ($action == "viewsite") {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<table width='150' cellspacing='0' cellpadding='1' style='border: 1px solid #$bordercolor; white-space: nowrap;'>";
											} else {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<table width='150' cellspacing='0' cellpadding='0' style='white-space: nowrap;'>";
											}
											
											// reorder UI
											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($leftnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}

											if ($item[name]) print "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td style='border-top: 1px solid #$bordercolor; border-bottom: 1px solid #$bordercolor; padding-top: 2px; padding-bottom: 2px;'>$item[name]</td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>";
											if ($item[type] == 'content') {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>$item[content]<br />";
											} else {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>$item[content]";				
											}
											//print "<tr><td style='border-bottom: 1px solid #$bordercolor; padding-bottom: 2px;'></td></tr>";
											if ($action == "viewsite") print "$item[extra]<br />";
											print "\n\t\t\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t</table>";
											print "\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>";
									}
										if ($item[type] == 'divider') {

											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td class='leftpadding' align='right'>";

											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($leftnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}

											print "&nbsp;$item[extra]</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
										}
										if ($item[type] == 'heading') {

											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td class='leftpadding' align='right'>";
											
											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($leftnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}
										print"\n\t\t\t\t\t\t\t\t\t\t\t\t<div class='heading'>$item[name]</div>$item[extra]\n\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
										}
										$nextorder++;
									}
									print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td class='leftpadding' valign='top' height='100%'>".$leftnav_extra."&nbsp;</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
									?>
									
									</table>
								</td>
								<td class='smallercontentarea' width='100%' valign='top'>
								
								
								<?
								/******************************************************************************
								 * Center Column
								 ******************************************************************************/
								print "$content";
								?>
								
								
								</td>		
								<td valign='top' style='padding-left: 0px'>
									<table cellpadding='0' cellspacing='0' style='height: 100%'>
									<?
									/******************************************************************************
									 * Right Column
									 ******************************************************************************/
									$nextorder = 0;
									foreach ($rightnav as $item) {
										$reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page."&amp;reorderPage=".$item['id']."&amp;newPosition=";
										if ($item[type] == 'normal') {
											$samepage = (isset($page) && ($page == $item[id]))?1:0;
											if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td align='left' valign='middle' class=".(($samepage)?"rightnavsel":"rightnav")." style='white-space: nowrap;'>";

											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($rightnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}

											print makelink($item,$samepage,'',1);
											print "</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
										}
										
										if ($item[type] == 'content' || $item[type] == 'rss' || $item[type] == 'tags' || $item[type] == 'participants') {
											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td align='right' valign='middle' class='".(($samepage)?"leftnavsel":"leftnavsel")."'>";
											if ($action == "viewsite") {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<table width='150' cellspacing='0' cellpadding='1' style='border: 1px solid #$bordercolor; white-space: normal;'>";
											} else {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<table width='150' cellspacing='0' cellpadding='0' style='white-space: normal;'>";
											}
											if ($item[name]) print "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<td style='border-top: 1px solid #$bordercolor; border-bottom: 1px solid #$bordercolor; padding-top: 2px; padding-bottom: 2px;'>$item[name]</td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>";


											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($rightnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}


											if ($item[type] == 'content') {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>$item[content]<br />";
											} else {
												print "\n\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>$item[content]";				
											}
											//print "<tr><td style='border-bottom: 1px solid #$bordercolor; padding-bottom: 2px;'></td></tr>";
											if ($action == "viewsite") print "$item[extra]<br />";
											print "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t\t</table>";
											print "\n\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
										}
						
										if ($item[type] == 'divider') {
											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td class='rightpadding' align='right'>";

											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($rightnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}

											print"&nbsp;$item[extra]</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
										}
										if ($item[type] == 'heading') {
											print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td class='rightpadding' align='right'>";

											if ($_REQUEST['showorder'] == "page") {
												print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
												for ($i=0; $i<count($rightnav); $i++) {
													print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
												}
												print "\n\t\t\t\t\t\t\t\t\t\t</select>";
											}

											print"<div style='font-size: 12px; font-weight: bold;' align='left'>$item[name]</div>$item[extra]</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
										}
										$nextorder++;
									}
									print "\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td class='rightpadding' valign='top' height='100%'>".$leftnav_extra."&nbsp;</td>\n\t\t\t\t\t\t\t\t\t\t</tr>";
									
									?>

									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>	
			<br />
			
			<? 
			/******************************************************************************
			 * Footer
			 ******************************************************************************/	
			print $sitefooter 	
			?>	
		</td>
	</tr>
</table>
</body>
</html>
