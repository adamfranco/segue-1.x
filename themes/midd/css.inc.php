<style type='text/css'>

body {
	 background-color: #<? echo $bg['bg']; ?>; 
}

<?

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

include("$themesdir/common/css.inc.php"); 

?>


/* ------------------------------------------ */
/* THEME-SPECIFIC CLASSES */
/* ------------------------------------------ */

.title {
	color: #<? echo $c['title-color']; ?>;
	padding-top: 0.6em;
	padding-right: 0px;
	padding-bottom: 0px;
	padding-left: 0px;
	font-family: Arial,Helvetica,Verdana,Sans-Serif;
	font-size: 2em;
	font-weight: bold;
	border-bottom: 0px solid #<? echo $c['title-color']; ?>;
}


.image_header {
/* 	background-color: #<? echo $c['header']; ?>; */
/* 	border-bottom: 1px solid #<? echo $c['borders']; ?>; */
 	margin-left: 6px; 
 	margin-right: 5px; 
	margin-bottom: 0px;
	
	/* Image underlay styles */
/*	height: 101px; */
	text-align: left;
/*	z-index: -100;  */
	
/*	width: 760px; */
}

.heading {
	text-transform: uppercase;
	padding-top: 2px; padding-bottom: 2px;
	
}

.heading2 {
	padding-top: 2px; padding-bottom: 2px;
}

.navtop {
/*	background: white url('<? echo "$themesdir/$theme/images/sidenav/$c[imagelocation]/top.gif"; ?>');
	background-repeat: no-repeat;*/
	color: #FFFFFF;
	font-size: 12px;
	text-indent: 3px;
}

.navtop div {
/*	position: relative; */
	padding-left: 9px;
/*	top: -10px; */
}

.navtop img {
/*	position: relative;
	top: 15px;
	left: -1px; 
*/
}


.topnav {
	padding: 5px;
/* 	background-color: #<? echo $c['topnav']; ?>; */
}

.leftnav {
/* 	float: left; */
	width: <? echo $navwidth; ?>px;
	padding: 0px;
	padding-left: 5px;
	margin-right: 20px;
/*	border-right: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>; */
	vertical-align: top;
}

.rightnav {
/* 	float: left; */
	width: <? echo $navwidth; ?>px;

/*	padding-top: 57px; */
	margin-left: 20px;
/*	border-left: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;*/
	vertical-align: top;
}

.contentarea {
	vertical-align: top;
	padding: 5px;
	font-size: 10px;
	
}

.contenttable {
/*
	border-bottom: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	border-top: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
*/
}



.breadcrumbTop {

	background-image: url(http://www.middlebury.edu//images/nav/nav_gateway_dotted_rule.gif);
	background-attachment: scroll;
	background-x-position: top;
	background-y-position: bottom;
	background-repeat: repeat-x;
	width: 100%;
	padding-top: 0px;
	padding-right: 0px;
	padding-bottom: 0px;
	padding-left: 0px;
	font-family: Arial,Helvetica,Sans-Serif;
	font-size: 1em;

	overflow: hidden;
}


.content {
	padding: 1px;
	background: #<? echo $c['contentarea']; ?>;
}

.status {
	padding: 5px;
	margin-right: 5px;
	background: #<? echo $bg['status']; ?>;
	color: #<? echo $bg['bgtext']; ?>;
}

.status a {
	color: #<? echo $bg['bglink']; ?>;
	text-decoration: none;
}

.status a:hover {
	text-decoration: underline;
}

.content_wrapper {
	position: relative;
}

div.find_bar {
	position: absolute;
	top: 30px;
	right: 10px;
}


.nav, .sidesectionnav {
	color: #<? echo $c['navlinkcolor']; ?>;
	background-color: #<? echo $c['navrowcolor']; ?>;

	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	margin-left: 0px;
	padding-top: 4px;
	padding-right: 10px;
	padding-bottom: 4px;
	padding-left: 21px;
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #<? echo $c['navlinkdivider']; ?>;
	display: block;
	text-decoration: none;
	text-transform: uppercase;
	border-top: 0px solid #<? echo $bordercolor; ?>; 
	border-bottom: 1px solid #<? echo $bordercolor; ?>;
}

.subnav {
	color: #<? echo $c['navlinkcolor']; ?>;
	background-color: #<? echo $c['subnavrowcolor']; ?>;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	margin-left: 0px;
	padding-top: 4px;
	padding-right: 10px;
	padding-bottom: 4px;
	padding-left: 30px;
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #<? echo $c['navlinkdivider']; ?>;
	display: block;
	text-decoration: none;
}




.nav_extras {
	text-transform: lowercase;
}

/* Body images */

.topright {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/topright.gif"; ?>') no-repeat;
	width: 25px;
	height: 5px;
}

.top {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/top.gif"; ?>') repeat-x;
	height: 5px;
}

.topleft {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/topleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 5px;
}

.right {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/right.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.left {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/left.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.bottomleft {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/bottomleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 6px;
}

.bottom {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/bottom.gif"; ?>') repeat-x;
	height: 6px;
}

.bottomright {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/bottomright.gif"; ?>') no-repeat;
	width: 25px;
	height: 6px;
}

.righttop, .lefttop {
	width: 25px;
	height: 22px;
	border: 0px solid gray;
}

/* Status images */

.topright-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/topright-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 5px;
}

.top-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/top-s.gif"; ?>') repeat-x;
	height: 5px;
}

.topleft-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/topleft-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 5px;
}

.right-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/right-s.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.left-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/left-s.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.bottomleft-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/bottomleft-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 6px;
}

.bottom-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/bottom-s.gif"; ?>') repeat-x;
	height: 6px;
}

.bottomright-s {
	background: white url('<? echo "$themesdir/$theme/images/bg/$bg[bgshadow]/bottomright-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 6px;
}

.righttop-s, .lefttop-s {
	width: 25px;
	height: 22px;
	border: 0px solid gray;
}

</style>
