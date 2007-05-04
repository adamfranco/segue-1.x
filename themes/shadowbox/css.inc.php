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



.header {
/* 	background-color: #<? echo $c['header']; ?>; */
/* 	border-bottom: 1px solid #<? echo $c['borders']; ?>; */
/* 	margin-left: 25px; */
/* 	margin-right: 25px; */
	margin-bottom: 0px;
}

.heading {
	border-top: 1px solid #<? echo $bordercolor; ?>; 
	border-bottom: 1px solid #<? echo $bordercolor; ?>; 
	padding-top: 2px; padding-bottom: 2px;
}

.heading2 {
	padding-top: 2px; padding-bottom: 2px;
}




.topnav {
	padding: 5px;
/* 	background-color: #<? echo $c['topnav']; ?>; */
}

.leftnav {
/* 	float: left; */
	width: <? echo $navwidth; ?>px;
	padding: 10px;
	margin-right: 20px;
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

/* more images */

.topright {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topright.gif"; ?>') no-repeat;
	width: 25px;
	height: 25px;
}

.top {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/top.gif"; ?>') repeat-x;
	height: 25px;
}

.topleft {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 25px;
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
	height: 25px;
}

.bottom {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottom.gif"; ?>') repeat-x;
	height: 25px;
}

.bottomright {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottomright.gif"; ?>') no-repeat;
	width: 25px;
	height: 25px;
}

/*  the images */
.righttop, .lefttop {
	width: 25px;
	height: 22px;
	border: 0px solid gray;
}
</style>
