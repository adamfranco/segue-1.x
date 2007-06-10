<? 
/* output.inc.php
 this script outputs the HTML resulting from action files
 output script needs to include the following:
 -common theme functions.inc, header.inc status.inc
 -particular theme colors.inc, css.inc
 -needs to define default theme settings to use
 -needs to call horizontal and vertical navigation function
 for both Top Sections and Side Sections navigation arrangements 
 */
 
if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");;
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
include("$themesdir/common/functions.inc.php");

if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");
	
//$nav_arrange=2;

if ($themesettings[theme] == 'midd') {   // indeed these settings are for this theme

	$usebg = $themesettings[bgcolor];
	$usecolor = $themesettings[colorscheme];
	$useborder = $themesettings[borderstyle];
	$usebordercolor = $themesettings[bordercolor];
	$usetextcolor = $themesettings[textcolor];
	$uselinkcolor = $themesettings[linkcolor];
	$usenav = $themesettings[nav_arrange];
	$usenavwidth = $themesettings[nav_width];
	$usesitewidth = $themesettings[site_width];
	$usesectionnavsize = $themesettings[sectionnav_size];	
	$usenavsize = $themesettings[nav_size];	

}
if (!$usebg) $usebg = 'blue';
$bg = $_bgcolor[$usebg];

if (!$usecolor) $usecolor = 'blue';
$c = $_theme_colors[$usecolor];
$bordercolor = $c['navlinkdivider'];
$linkcolor = $c['navlinkcolor'];
$textcolor = "336699";

if (!$useborder) $useborder = 'solid';
$borders = $_borderstyle[$useborder];

//if (!$usebordercolor) $usebordercolor = 'blue';
//$bordercolor = $_bordercolor[$usebordercolor];

//if (!$usetextcolor) $usetextcolor = 'black';
//$textcolor = $_textcolor[$usetextcolor];

//if (!$uselinkcolor) $uselinkcolor = 'red';
//$linkcolor = $_linkcolor[$uselinkcolor];

$usenav = 'Side Sections';
$nav_arrange = $_nav_arrange[$usenav];

if (!$usenavwidth) $usenavwidth = '150 pixels';
$navwidth = $_nav_width[$usenavwidth];

if (!$usesitewidth) $usesitewidth = 'variable';
$sitewidth = $_site_width[$usesitewidth];

$usesectionnavsize = '10 pixels';
$sectionnavsize = $_sectionnav_size[$usesectionnavsize];

$usenavsize = '9 pixels';
$navsize = $_nav_size[$usenavsize];


/* ------------------- END THEME SETTINGS---------------------	*/

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
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel="stylesheet" type="text/css" media="screen" href="http://www.middlebury.edu/css/styles.css" />

<?
/******************************************************************************
 * Commom header stuff
 ******************************************************************************/

include("themes/common/header.inc.php");

include("themes/$theme/css.inc.php"); 

?>

<title><? echo $pagetitle; ?></title>
</head>
<body style='margin: 0px'>

<? print $obContent; ?>
<table  width='<?php echo $sitewidth ?>'  align='center' cellpadding='0' cellspacing='0'>
<tr><td>

	<!--  Status Layout -->
	<table width='<?php echo $sitewidth ?>' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td width='22'></td>
			<td></td>
			<td width='22'></td>
		</tr>
		<tr>
			<td width='22'></td>
			<td class='status'>
			<? if ($_SESSION['ltype'] == "admin" || !isset($_SESSION['ltype'])) {
					print "<div style='height: 32px; overflow: hidden;'>";
				} else {
					print "<div style='height: 16px; overflow: hidden;'>";
				}
				include("themes/common/status.inc.php"); 
				?>
	
			</td> 
			<!-- end content table cell -->
			<td width='22'></td>
		</tr>
		<tr>
			<td width='22'></td>
			<td></td>
			<td width='22'></td>
		</tr>
	</table>

</td></tr>
<tr><td>

	<!--  Body Layout -->
	<table width='<?php echo $sitewidth ?>' cellpadding='0' cellspacing='0' align='center' bgcolor='#FFFFFF'>
	<tr>
		<td class='topleft'></td>
		<td class='top'></td>
		<td class='topright'></td>
	</tr>
	<tr>
		<td class='left'>
			<img class='lefttop' src='<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/lefttop.gif"?>' alt='border' />
		</td>
		<td class='content' >
			<div class='content_wrapper'>
				<div class='find_bar'>
					<?php 
					print "\n\t\t\t";
					include("themes/common/search.inc.php"); 
					?>
	
				</div>
				
				<?
				
				$middHeader = file_get_contents("http://www.middlebury.edu/middcms/render/tools/getHeader.aspx");
				print str_replace('src="/images/', 'src="http://www.middlebury.edu/images/', $middHeader);		
				?>
				
				
				<?php
							
				/******************************************************************************
				 * Site Header, Status bar, crumbs
				 ******************************************************************************/ 
				
				
				if (preg_match('/(<img [^>]+>)/', $siteheader, $matches)) {
					$imgTag = $matches[1];
					$hasHeader = true;
					
					// ensure that the image has the correct height
	//				$imgTag = preg_replace('/\sheight=[\'"][^\'"]+[\'"]/', ' height="158px"', $imgTag);
	//				$imgTag = preg_replace('/\swidth=[\'"][^\'"]+[\'"]/', '', $imgTag);
					
	//				print "\n\t\t\t<div class='image_header'>";
					print "<table style='padding-left: 7px;' cellspacing='0' cellpadding='0' width='100%'>";
					print "<tr><td align='left' >";
					print $siteheader; 
					print "</td></tr>";
					print "</table>";
	//				print "\n\t\t\t</div>";
				} else {
					$hasHeader = false;
				}
				?>
				
	
				<table width='100%' class='contenttable'>
					<tr>
				<?
				/******************************************************************************
				 * Left Column
				 ******************************************************************************/
				 
				if ($action == "viewsite" || $leftnav && ($hide_sidebar != 1 || $nav_arrange==2)) {
					//printpre ($thisSite->getField("title"));
					print "\n\t\t\t\t\t<td class='leftnav'>";
					
					// if no image header then put dog-eared left nav header
					if (!$hasHeader) {
						print "\n\t\t\t\t\t<table cellspacing='0' cellpadding='0' width='100%'>";
						//print "\n\t\t\t\t\t<tr><td height='30px' class='navtop'>";
						//print "\n\t\t\t\t\t\t<img src='$themesdir/$theme/images/sidenav/$c[imagelocation]/top.gif'  alt='border'/>";
						print "\n\t\t\t\t\t<tr><td class='navtop' height='38px' background='$themesdir/$theme/images/sidenav/$c[imagelocation]/top.gif'  alt='border''>";
					//	print "\n\t\t\t\t\t\t<div>";
						print $thisSite->getField('title');
						print "\n\t\t\t\t\t\t</div>";
						print "\n\t\t\t\t\t</td></tr>";
						print "\n\t\t\t</table>";
					}
					
					// 				
					print "\n\t\t\t\t\t<table cellspacing='0' cellpadding='0' width='98%' style='padding-right: 1px; padding-top: 0px'>";
					print "\n\t\t\t\t\t<tr><td>";
						
					ob_start();
					if ($nav_arrange==1) {
						vertical_nav($page, $leftnav, $leftnav_extra, $bordercolor, $hide_sidebar);		
					} else {
						side_nav($section, $topnav, $leftnav, $topnav_extra, $leftnav_extra, $bordercolor);
					}
					
					print str_replace("cellpadding='2'", "cellpadding='0'", ob_get_clean());
		
					
					print "\n\t\t\t</td>";
					print "\n\t\t\t</tr>";
					print "\n\t\t\t\t\t<tr><td>";
					print "\n\t\t\t\t\t\t<img src='$themesdir/$theme/images/sidenav/$c[imagelocation]/bottom.gif'  alt='border'/>";
					print "\n\t\t\t\t\t</td></tr>";										
					print "\n\t\t\t</table>";
					
					
			//		print "\n\t\t\t<img src='$themesdir/$theme/images/sidenav/$c[imagelocation]/bottom.gif' alt='border' />";
					print "\n\t\t\t</td>";
					print "<!--   end left column   -->";
				} 
				
				
				/******************************************************************************
				 * Center Column
				 ******************************************************************************/
				?>
					<!-- start center column -->
					<td class='contentarea'
					<?php
					if ($hasHeader) {
						//print " style='padding-top: 60px;'";
					}				
					?>
					>
				<?

				print "<div class='breadcrumbTop'>".$sitecrumbs."</div>";
				print "\n".$content;

				?>
	
					</td>
				<?
				/******************************************************************************
				 * Right Column
				 ******************************************************************************/
				ob_start();
					// show right side bar only if not sidebar hidden or edit mode
				if ($rightnav && ($hide_sidebar != 1 || $action == "viewsite")) {
					print "\n\t\t\t\t\t<td class='rightnav'>";
					print vertical_nav($page, $rightnav, $leftnav_extra, $bordercolor, $hide_sidebar);
					print "\n\t\t\t\t\t</td>";
				}
				
				print str_replace("cellpadding='2'", "cellpadding='0'", ob_get_clean());
				?>
				
					</tr>
				</table>
			
				<div class='topnav' align='center'>
				
				<?
				/******************************************************************************
				 * Bottom section navigation
				 ******************************************************************************/
				if ($nav_arrange==1) 
					horizontal_nav($section, $topnav2, $topnav2_extra, $hide_sidebar);
				?>
				
				</div>
					
				<div class="footer">
				
				<div class="footerNav">
				<a href="http://go.middlebury.edu/bookstore">College Book Store</a>&nbsp;|&nbsp;
				<a href="http://go.middlebury.edu/lis">Library &amp; Information Services</a>&nbsp;|&nbsp;
		
				<a href="http://go.middlebury.edu/jobseekers">Job Seekers</a>&nbsp;|&nbsp;
				<a href="http://go.middlebury.edu/campusmap">Campus Maps</a>&nbsp;|&nbsp;
				<a href="http://go.middlebury.edu/sitemap">Site Map</a>&nbsp;|&nbsp;
				<a href="http://go.middlebury.edu/privacy">Privacy</a>&nbsp;|&nbsp;
				<a href="http://go.middlebury.edu/help">Help</a>&nbsp;|&nbsp;
		
				<a href="http://go.middlebury.edu/webmail">WebMail</a>&nbsp;|&nbsp;
				<a href="http://go.middlebury.edu/bannerweb">Banner Web</a>
				</div><br />
				Middlebury Vermont 05753 802-443-5000<br />
				<!--
				<a href="/about/copyright/">©</a>
				The President and Fellows of Middlebury College. All Rights Reserved.
				<br /><br />
				-->
				
	
				<?
				/******************************************************************************
				 * Footer
				 ******************************************************************************/
				print $sitefooter;
				?>
		
				</div>
			</div>
		</td> 
		<!-- end content table cell -->
		<td class='right'>
			<img class='righttop' src='<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/righttop.gif"?>' alt='rightop' />
		</td>
	</tr>
	<tr>
		<td class='bottomleft'></td>
		<td class='bottom'></td>
		<td class='bottomright'></td>
	</tr>
	</table>
	
</td></tr>
</table>
<br />
</body>
</html>

	
