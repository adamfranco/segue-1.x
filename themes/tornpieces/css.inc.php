<style type='text/css'>
body {
	 background-color: #<? echo $bg['bg']; ?>; 
}

<? include("$themesdir/common/css.inc.php"); ?>



/* ------------------------------------------ */
/* THEME-SPECIFIC CLASSES */
/* ------------------------------------------ */

body {
	 background-color: #<? echo $bg['bg']; ?>; 
}


.header {
/* 	background-color: #<? echo $c['header']; ?>; */
/* 	border-bottom: 1px solid #<? echo $c['borders']; ?>; */
/* 	margin-left: 25px; */
/* 	margin-right: 25px; */
	margin-bottom: 0px;
}

.topnav {
	padding: 2px;
/* 	background-color: #<? echo $c['topnav']; ?>; */
}

.leftnav {
/* 	float: left; */
	width: <? echo $navwidth; ?>px;
	vertical-align: top;
}

.contentarea {
	vertical-align: top;
	padding: 0px;
}

.contenttable {
}

.content {
	padding: 0px;
	background: #<? echo $c['contentarea']; ?>;
}

/* more images */

.topright {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topright.gif"; ?>') no-repeat;
	width: 10px;
	height: 12px;
}

.top {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/top.gif"; ?>') repeat-x;
	height: 12px;
}

.topleft {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/topleft.gif"; ?>') no-repeat;
	width: 10px;
	height: 10px;
}

.right {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/right.gif"; ?>') repeat-y;
	width: 10px;
	vertical-align: top;
}

.left {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/left.gif"; ?>') repeat-y;
	width: 10px;
	vertical-align: top;
}

.bottomleft {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottomleft.gif"; ?>') no-repeat;
	width: 10px;
	height: 18px;
}

.bottom {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottom.gif"; ?>') repeat-x;
	height: 18px;
}

.bottomright {
	background: white url('<? echo "$themesdir/$theme/images/$bg[bgshadow]/bottomright.gif"; ?>') no-repeat;
	width: 10px;
	height: 18px;
}

/*  the images */
.righttop, .lefttop {
	width: 10px;
	height: 22px;
	border: 0px solid gray;
}


</style>