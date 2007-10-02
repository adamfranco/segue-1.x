<style type='text/css'>

<? 

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

if (!preg_match('/^[a-z_0-9]+$/i', $theme))
		die ('Error: invalid theme, "'.$theme.'".');

include("themes/common/css.inc.php"); ?>

/* ------------------------------------------ */
/* THEME-SPECIFIC CLASSES */
/* ------------------------------------------ */

a {
  	color: #<? echo $c['nav']; ?>;
	text-decoration: none;
}

.contentarea a {
  	color: #<? echo $c['a']; ?>;
}

a:hover {
	text-decoration: underline;
}

body {
	 background-color: #<? echo $c['bgcolor']; ?>; 
	 color: #FFF;
}

.contentarea {
	color: #000;
}


.btnlink {
	color: #<? echo $c['nav']; ?>;
	border: 1px solid #<? echo $c['nav']; ?>;
}

.contentarea  .btnlink {
	color: #<? echo $linkcolor; ?>;
	border: 1px solid #<? echo $linkcolor; ?>;
}

.navlink a {
	color: #<? echo $c['nav']; ?>;
	text-decoration: none;
}

.navlink {
	color: #<? echo $c['nav']; ?>;
	text-decoration: none;
}


.nav a:hover {
	text-decoration: underline;
}

.nav {
	color: #<? echo $c['navtext']; ?>;
	text-decoration: none;
}

.subnav a:hover {
	text-decoration: underline;
}

.subnav {
	color: #<? echo $c['navtext']; ?>;
	text-decoration: none;
}

.sitetitle {
	color: #<? echo $c['navtext']; ?>;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 16px;
}

.header {
/* 	background-color: #<? echo $c['header']; ?>; */
/* 	border-bottom: 1px solid #<? echo $c['borders']; ?>; */
/* 	margin-left: 25px; */
/* 	margin-right: 25px; */
	margin-bottom: 5px;
}

.heading {
	color: #<? echo $c['navtext']; ?>;
	border-top: 1px solid #<? echo $c['nav']; ?>; 
	border-bottom: 1px solid #<? echo $c['nav']; ?>; 
	padding-top: 2px; 
	padding-bottom: 2px;
}

.heading2 {
	border-top: 1px solid #<? echo $bordercolor; ?>; 
	padding-top: 2px; 
	padding-bottom: 2px;
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

.rightnav {
/* 	float: left; */
	width: 125px;
	padding: 10px;
	margin-left: 20px;
	vertical-align: top;
}

.rightnavcolor {
	background-color: #<? echo $c['bgcolor']; ?>;
}


.contentarea {
	vertical-align: top;
	padding: 10px;
}

.contentarea2 {
	vertical-align: top;
	padding: 10px;
	border-right: 1px solid #<? echo $c['borders']; ?>;
}

.contenttable {
	/*border-bottom: 1px dashed #<? echo $c['borders']; ?>;*/
	/*border-top: 1px dashed #<? echo $c['borders']; ?>;*/
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
	background: white url('<? echo "themes/$theme/images/blue/topright.gif"; ?>') no-repeat;
	width: 8px;
	height: 8px;
}

body {
	 background-color: #FFFFFF; 
	 /* background-image: url(images/shadowside/bg.gif"; ?>');  */
}

.top {
	background: url('<? echo "themes/$theme/images/$c[bg]/top.gif"; ?>') repeat-x;
	height: 43px;
}

.topcenter {
	background: url('<? echo "themes/$theme/images/$c[bg]/topcenter.gif"; ?>') repeat-x;
	height: 15px;
}

.topright {
	background: url('<? echo "themes/$theme/images/$c[bg]/topright.gif"; ?>') no-repeat;
	width: 9px;
	height: 43px;
}

.topright1 {
	background: url('<? echo "themes/$theme/images/$c[bg]/topright1.gif"; ?>') no-repeat;
	width: 11px;
	height: 15px;
}

.topleft {
	background: url('<? echo "themes/$theme/images/$c[bg]/topleft.gif"; ?>') no-repeat;
	width: 147px;
	height: 43px;
}

.topleft1 {
	background: url('<? echo "themes/$theme/images/$c[bg]/topleft1.gif"; ?>') no-repeat;
	width: 147px;
	height: 43px;
}

.topleft2 {
	background-color: #003366;
	background: url('<? echo "themes/$theme/images/$c[bg]/topleft2.gif"; ?>') no-repeat;
	width: 147px;
	height: 64px;
}

.topleft3 {
	background: url('<? echo "themes/$theme/images/$c[bg]/topleft3.gif"; ?>') no-repeat;
	width: 147px;
	height: 15px;
}

.left {
	background: url('<? echo "themes/$theme/images/$c[bg]/left.gif"; ?>') repeat-y;
	width: 147px;
	vertical-align: top;
}

.right {
	background: url('<? echo "themes/$theme/images/$c[bg]/right.gif"; ?>') repeat-y;
	width: 9px;
	vertical-align: top;
}

.right2 {
	background-color: #003366;
	background: url('<? echo "themes/$theme/images/$c[bg]/right2.gif"; ?>') repeat-y;
	width: 11px;
	vertical-align: top;
}


.bottomleft {
	background: url('<? echo "themes/$theme/images/$c[bg]/bottomleft.gif"; ?>') no-repeat;
	width: 147px;
	height: 76px;
}

.bottom {
	background: url('<? echo "themes/$theme/images/$c[bg]/bottom.gif"; ?>') repeat-x;
	height: 76px;
}

.bottomright1 {
	background: url('<? echo "themes/$theme/images/$c[bg]/bottomright1.gif"; ?>') no-repeat;
	width: 11px;
	height: 76px;
}

.bottomright2 {
	background: url('<? echo "themes/$theme/images/$c[bg]/bottomright2.gif"; ?>') no-repeat;
	width: 9px;
	height: 76px;
}


</style>