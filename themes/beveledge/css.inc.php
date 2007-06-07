<style type='text/css'>

body {
	 background-color: #<? echo $bg['bg']; ?>; 
}

<? 
if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

include("$themesdir/common/css.inc.php"); ?>


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
	padding: 10px;
	margin-left: 20px;
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



/* Body images */

.topright {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topright.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}

.top {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/top.gif"; ?>') repeat-x;
	height: 7px;
}

.topleft {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}

.right {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/right.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.left {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/left.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.bottomleft {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottomleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}

.bottom {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottom.gif"; ?>') repeat-x;
	height: 7px;
}

.bottomright {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottomright.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}


.righttop, .lefttop {
	width: 25px;
	height: 22px;
	border: 0px solid gray;
}

/*  Status Images */
.topright-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topright-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}

.top-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/top-s.gif"; ?>') repeat-x;
	height: 7px;
}

.topleft-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topleft-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}

.right-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/right-s.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.left-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/left-s.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.bottomleft-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottomleft-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}

.bottom-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottom-s.gif"; ?>') repeat-x;
	height: 7px;
}

.bottomright-s {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottomright-s.gif"; ?>') no-repeat;
	width: 25px;
	height: 7px;
}


.righttop-s, .lefttop-s {
	width: 25px;
	height: 22px;
	border: 0px solid gray;
}

</style>