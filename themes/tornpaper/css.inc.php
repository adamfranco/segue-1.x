<style type='text/css'>

body {
	 background-color: #<? echo $bg['bg']; ?>; 
}

<? 

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

if (!preg_match('/^[a-z_0-9]+$/i', $theme))
		die ('Error: invalid theme, "'.$theme.'".');

include("themes/common/css.inc.php"); ?>


/* ------------------------------------------ */
/* THEME-SPECIFIC CLASSES */
/* ------------------------------------------ */

.header {
	margin-bottom: 0px;
}

.heading {
	border-top: 1px solid #<? echo $bordercolor; ?>; 
	border-bottom: 1px solid #<? echo $bordercolor; ?>; 
	padding-top: 2px; padding-bottom: 2px;
}

.heading2 {
	border-top: 1px solid #<? echo $bordercolor; ?>; 
	padding-top: 2px; padding-bottom: 2px;
}


.topnav {
	padding: 5px;
}

.leftnav {
/* 	float: left; */
	width: <? echo $navwidth; ?>px;
	padding: 5px;
	margin-right: 5px;
	border-right: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	vertical-align: top;
}

.rightnav {
/* 	float: left; */
	width: <? echo $navwidth; ?>px;
	padding: 5px;
	margin-left: 5px;
	border-left: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	vertical-align: top;
}

.contentarea {
	vertical-align: top;
	padding: 10px;
}

.contenttable {
	border-bottom: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	border-top: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
}

.content {
	padding: 10px;
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

/* more images */

.topright {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/topright.gif"; ?>') no-repeat;
	width: 25px;
	height: 11px;
}

.top {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/top.gif"; ?>') repeat-x;
	height: 11px;
}

.topleft {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/topleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 11px;
}

.right {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/right.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.left {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/left.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.bottomleft {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/bottomleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 16px;
}

.bottom {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/bottom.gif"; ?>') repeat-x;
	height: 16px;
}

.bottomright {
	background: white url('<? echo "themes/$theme/images/$bg[bgshadow]/bottomright.gif"; ?>') no-repeat;
	width: 25px;
	height: 16px;
}

/*  the images */
.righttop, .lefttop {
	width: 25px;
	height: 22px;
	border: 0px solid gray;
}


</style>