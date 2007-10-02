<? // output.inc.php
	// this script outputs the HTML resulting from action files
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//exit();

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

if (!preg_match('/^[a-z_0-9]+$/i', $theme))
		die ('Error: invalid theme, "'.$theme.'".');

if (file_exists("themes/$theme/colors.inc.php"))
	include("themes/$theme/colors.inc.php");

if ($themesettings[theme] == 'program') {   // indeed these settings are for this theme
	$use = $themesettings[colorscheme];
}
if (!$use) $use = 'gray';
$c = $_theme_colors[$use];

/* ------------------- END ---------------------------	*/

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

<?/* ------------------------------------------- */
/* ------------- COMMON HEADER --------------- */
/* ------------------------------------------- */
include("themes/common/header.inc.php"); ?>

<?
$h = date("H");
$m = date("i");
$h++;$m++;
/* if (($m >= 3 && $m < 6) && !($m%15)) $_timefunctions=1; */
if (($m >= 3 && $m < 6) && !($m%15)) $_timefunctions=1;
if ($_timefunctions) { include("themes/common/timefunctions.inc.php"); $_ol = " onload='init()'"; }

if (!$_REQUEST[nostatus]) {
	if (!$_loggedin) {
		print <<<END
		
		<script type='text/javascript'>
		// <![CDATA[

			function focusLogin() {
				forms = document.forms;
				for (i=0; i<forms.length; i++) {
					if (forms[i].id == 'loginform') {
						loginForm = forms[i];
						break;
					}
				}
				
				if (loginForm) {
					elements = loginForm.elements;
					for (i=0; i<elements.length; i++) {
						if (elements[i].name == 'name') {
							elements[i].focus();
							break;
						}
					}
				}
			}
			
		// ]]>
		</script>
		
END;
		$_ol = " onload='focusLogin()'";
	}
}

include("themes/$theme/css.inc.php");
?>

<?/* ------------------------------------------- */
/* -------------- PAGE TITLE ----------------- */
/* ------------------------------------------- */?>
<title><? echo $pagetitle; ?></title>

</head>

<body<? echo $_ol; ?>>

<? print $obContent; ?>

<? if ($_timefunctions) include("themes/common/timeoutput.inc.php"); ?>

<?/* ------------------------------------------- */
/* -------------- STATUS BAR ----------------- */
/* ------------------------------------------- */
//include("themes/common/status.inc.php"); ?>
<br />

<table width='90%' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td class="topleft" ><? if ($cfg['inst_logo_url_leftlogo_tophalf']) print "<img src='".$cfg['inst_logo_url_leftlogo_tophalf']."' alt='Institution Logo, upper half'/>"; ?></td>
    <td class="top"> 
    <div align="right"><? if ($cfg['inst_logo_url']) print "<img src='".$cfg['inst_logo_url']."' alt='Institution Logo' />"; ?></div>
    </td>
    <td class="top"></td>
    <td class="topright">&nbsp;</td>
   </tr>
   
   <tr>
    <td class="topleft2" valign='top' style="background-color: #<? echo $c[bgcolor] ?>"><? if ($cfg['inst_logo_url_leftlogo_bottomhalf']) print "<img src='".$cfg['inst_logo_url_leftlogo_bottomhalf']."' alt='Institution Logo, bottom half' />"; ?></td>
    <td style="background-color: #<? echo $c[bgcolor] ?>">
    <? include("themes/common/status.inc.php");?>

       <div class='topnav' align='center'>
	<div class='nav'>
	<?
	/* ------------------------------------------- */
	/* -------------- TOP NAV    ----------------- */
	/* ------------------------------------------- */

	if (count($topnav) > 0) {
		$i=0;
		foreach ($topnav as $item) {
			if ($i > 0) print " | ";
			print "<span style='color: #000;";
			if (!$item[url])
				print " font-weight: bold;";
			print "'>";
		
			$samepage = (isset($section) && ($section == $item[id]))?1:0;
			if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
			print makelink($item,$samepage);
			print "</span>";
			$i++;
		}
		
		print $topnav_extra;
	}
	?>
	</div>
	</div>
	
    </td>
   <td style="background-color: #<? echo $c[bgcolor] ?>"> &nbsp;</td>
    <td class="right"></td>
   </tr>
   
  <tr> 
    <td class="topleft3"></td>
    <td class="topcenter"></td>
    <td class="topright1"></td>
    <td class="right"></td>
  </tr>

<tr>

<td class="left" width='160' height="100%">


<table width='100%' cellpadding='5' cellspacing='0'>
	<tr>
		<td class='leftnav' height="100%">
	<?
/* ------------------------------------------- */
/* -------------- LEFT NAV   ----------------- */
/* ------------------------------------------- */
		
	foreach ($leftnav as $item) {
		if ($item[type] == 'normal') {
			$samepage = (isset($page) && ($page == $item[id]))?1:0;
			if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
			print "\n\t\t\t<div class='nav'>";
			print "\n\t\t\t\t".makelink($item,$samepage,'',1);
			print "\n\t\t\t</div>";
		}
		if ($item[type] == 'divider') {
			print "\n\t\t\t\t".$item[extra]."<br />";
		}
		if ($item[type] == 'heading') {
			print "\n\t\t\t\t<img src='themes/breadloaf/images/bullet.gif' border='0' align='absmiddle' alt='bullet icon' /> $item[name] :";
			if ($item[extra]) {
				print "\n\t\t\t\t<div align='right'>";
				print "\n\t\t\t\t\t".$item[extra];
				print "\n\t\t\t\t</div>";
			}
		}
	}
	print "\n\t\t\t<div class='nav'>";
	print "\n\t\t\t\t<br />$leftnav_extra";
	print "\n\t\t\t</div>";
	?>

		</td>
	</tr>
</table>

</td>

<td class='contentarea'>

<? 

print $content; 

?>

</td>

<?
if (count($rightnav)) {
	print "<td style='margin-left: 20px'>";
	
	foreach ($rightnav as $item) {
		print "<a href='$item[url]'>$item[name]</a><br />";
	}
	print "</td>";
}
?>
<td class="right2">&nbsp;</td>
<td class="right">&nbsp;</td>
</tr>
<tr> 
    <td class="bottomleft">&nbsp;</td>
    <td class="bottom">
<div class='topnav' align='center'>
<div class='nav'>
</div>
</div> 
       
</td>
<td class="bottomright1">&nbsp;</td>
<td class="bottomright2">&nbsp;</td>
</tr>
</table>
<br />

<table width='90%' align='center' cellpadding='0' cellspacing='0'>
<tr><td>
<?/* ------------------------------------------- */
/* -------------- FOOTER     ----------------- */
/* ------------------------------------------- */
print $sitefooter ?>
</td></tr>
</table>
<br />

</body>
</html>