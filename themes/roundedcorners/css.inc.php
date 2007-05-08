<style type='text/css'>

body {
	 background-color: #<? echo $bg['bgcolor']; ?>; 
	 background: url('<? echo "$themesdir/$theme/images/background/$bg[bgimage]"; ?>') repeat-x;
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

.status {
	padding-top: 3px;
	background: #<? echo $c['contentarea']; ?>;
}


/* top left outer border images */

.r1c1 {
	/*background:  url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r1c1.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}

.r1c2 {
	/*background:  url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r1c2.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}


/* top border outer repeating image */

.r1c3 {
	/*background:  url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r1c3.gif"; ?>') repeat-x;*/
	height: 2px;
}


/* top right outer border images */

.r1c4 {
	/*background:  url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r1c4.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}


.r1c5 {
	/*background:  url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r1c5.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}

/* top left inner border images */

.r2c1 {
	/*background:  url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r2c1.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 16px;
}

.r2c2 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r2c2.gif"; ?>') no-repeat;
	width: 16px;
	height: 16px;
}

/* top border inner repeating image */

.r2c3 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r2c3.gif"; ?>') repeat-x;
	height: 16px;
}

/* top right inner border images */

.r2c4 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r2c4.gif"; ?>') no-repeat;
	width: 16px;
	height: 16px;
}

.r2c5 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r2c5.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 16px;
}

/* left side  repeating images */

.r3c1 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r3c1.gif"; ?>') repeat-y;*/
	width: 16px;
	vertical-align: top;
}

.r3c2 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r3c2.gif"; ?>') repeat-y;
	width: 16px;
	vertical-align: top;
}

/* right side  repeating images */

.r3c4 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r3c4.gif"; ?>') repeat-y;
	width: 16px;
	vertical-align: top;
}

.r3c5 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r3c5.gif"; ?>') repeat-y;*/
	width: 16px;
	vertical-align: top;
}


/* bottom left inner border images */

.r4c1 {
	//background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r4c1.gif"; ?>') no-repeat;
	width: 16px;
	height: 16px;
}

.r4c2 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r4c2.gif"; ?>') no-repeat;
	width: 16px;
	height: 16px;
}

/* bottom border inner repeating image */

.r4c3 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r4c3.gif"; ?>') repeat-x;
	height: 16px;
}


/* bottom right inner border images */

.r4c4 {
	background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r4c4.gif"; ?>') no-repeat;
	width: 16px;
	height: 16px;
}


.r4c5 {
	//background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r4c5.gif"; ?>') no-repeat;
	width: 16px;
	height: 16px;
}

/* bottom left outer border images */

.r5c1 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r5c1.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}


.r5c2 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r5c2.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}

/* bottom border outer repeating image */

.r5c3 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r5c3.gif"; ?>') repeat-x;*/
	height: 2px;
}


/* bottom right outer border images */

.r5c4 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r5c4.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}


.r5c5 {
	/*background: url('<? echo "$themesdir/$theme/images/$c[imagelocation]/r5c5.gif"; ?>') no-repeat;*/
	width: 16px;
	height: 2px;
}


/*  the images */
.righttopcorner, .lefttopcorner {
	width: 16px;
	height: 16px;
	border: 0px solid gray;
}
</style>
