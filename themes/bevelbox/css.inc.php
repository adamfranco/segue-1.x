<style type='text/css'>

a {
  color: #<? echo $c['a']; ?>;
	text-decoration: none;
}


a:hover {
	text-decoration: underline;
	color: #<? echo $c['a:hover']; ?>;
}

body {
	 background-color: #<? echo $bg['bg']; ?>; 
}

<? echo include("$themesdir/common/css.inc.php"); ?>;



/* ------------------------------------------ */
/* THEME-SPECIFIC CLASSES */
/* ------------------------------------------ */


.nav a {
	color: #<? echo $c['nav']; ?>;
	text-decoration: none;
}

.nav a:hover {
	color: #<? echo $c['nav']; ?>;
	text-decoration: underline;
}

.nav { 
	color: #<? echo $c['navtext']; ?>;
	font-family: "Verdana", "Arial", "Helvetica", "sans-serif";
	font-size: <? echo $navsize; ?>;
}

.sitetitle {
	color: #<? echo $c['navtext']; ?>;
	font-family: "Verdana", "Arial", "Helvetica", "sans-serif";
	font-size: 16px;
}


.header {
/* 	background-color: #<? echo $c['header']; ?>; */
/* 	border-bottom: 1px solid #<? echo $c['borders']; ?>; */
/* 	margin-left: 25px; */
/* 	margin-right: 25px; */
	margin-bottom: 5px;
}

.topnav {
	padding: 0px;
/* 	background-color: #<? echo $c['topnav']; ?>; */
}

.leftnav {
/* 	float: left; */
	width: 125px;
	padding: 10px;
	margin-right: 20px;
	vertical-align: top;
}

.contentarea {
	vertical-align: top;
	padding: 10px;
}

.contenttable {
	/*border-bottom: 1px dashed #<? echo $c['borders']; ?>;*/
	/*border-top: 1px dashed #<? echo $c['borders']; ?>;*/
}

.content {
	padding: 10px;
	background: #<? echo $c['contentarea']; ?>;
}

/* more images */

.topright {
	background: white url('<? echo "$themesdir/$theme/images/blue/topright.gif"; ?>') no-repeat;
	width: 8px;
	height: 8px;
}

body {
	 background-color: #FFFFFF; 
	 /* background-image: url(images/shadowside/bg.gif"; ?>');  */
}

.top {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/top.gif"; ?>') repeat-x;
	height: 17px;
}

.topcenter {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topcenter.gif"; ?>') repeat-x;
	height: 17px;
}

.topright {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topright.gif"; ?>') no-repeat;
	width: 9px;
	height: 43px;
}

.topright1 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topright1.gif"; ?>') no-repeat;
	width: 11px;
	height: 27px;
}

.topleft {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft.gif"; ?>') no-repeat;
	width: 147px;
	height: 43px;
}

.topleft1 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft1.gif"; ?>') no-repeat;
	width: 147px;
	height: 26px;
}

.topleft2 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft2.gif"; ?>') no-repeat;
	width: 147px;
	height: 48px;
}

.topleft3 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft3.gif"; ?>') no-repeat;
	width: 147px;
	height: 27px;
}

.left {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/left.gif"; ?>') repeat-y;
	width: 147px;
	vertical-align: top;
}


.right {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/right.gif"; ?>') repeat-y;
	width: 9px;
	vertical-align: top;
}

.right2 {
	background color: "#003366";
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/right2.gif"; ?>') repeat-y;
	width: 11px;
	vertical-align: top;
}


.bottomleft {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottomleft.gif"; ?>') no-repeat;
	width: 147px;
	height: 51px;
}

.bottom {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottom.gif"; ?>') repeat-x;
	height: 51px;
}

.bottomright1 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottomright1.gif"; ?>') no-repeat;
	width: 11px;
	height: 51px;
}

.bottomright2 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottomright2.gif"; ?>') no-repeat;
	width: 9px;
	height: 51px;
}

</style>