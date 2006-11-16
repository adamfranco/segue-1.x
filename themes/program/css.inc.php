<style type='text/css'>
/* ------------------------------------------ */
/*  REQUIRED CLASSES    */
/* ------------------------------------------ */

/*    LINKS   */
a {
  color: #<? echo $c['a']; ?>;
	text-decoration: none;
}


a:hover {
	text-decoration: underline;
	color: #<? echo $c['a:hover']; ?>;
}

.tagused {
	color: #<? echo $c['a']; ?>;
	border: 1px solid;
	padding: 1px;
}

.level_four {
 	color: #<? echo $c['a']; ?>;
	font-size: 12px;
}

.level_three {
 	color: #<? echo $c['a']; ?>;
	font-size: 12px;
}

.level_two {
 	color: #<? echo $c['a']; ?>;
	font-size: 12px;

}

.level_one {
 	color: #<? echo $c['tag01']; ?>;
	font-size: 12px;
}

.tagcloud {
	font-size: 12px;
	line-height: 19px;
	text-align: justify;
}

div, p, td, span, input { 
	color: #<? echo $c['text']; ?>;
	font-family: Arial, Verdana, Helvetica, sans-serif;
	font-size: <? echo $c['font-size']; ?>;
}

/*    INPUT BUTTONS    */
.button {
	border: 1px solid #<? echo $c['input-borders']; ?>;
	background-color: #FFFFFF;
}

/*     TEXTFIELDS    */
.textfield {
	border: 1px solid #<? echo $c['input-borders']; ?>;
	background-color: #fff;
}

select, textarea, input {
	font-size: <? echo $c['input-size']; ?>;
}

/*		THE STATUS BAR      */
.headerbox {
	padding: 0px;
	margin-top: 5px;
	margin-left: 0px;
	margin-right: 0px;
	text-align: right;
}

table, td { border: 0px solid black }


/*     THE BUTTONS IN EDIT MODE (for delete, edit, etc)    */
.btnlink {
	color: #<? echo $c['btnlink-color']; ?>;
	font-weight: bold;
	font-size: 11px;
	border: 1px solid #<? echo $c['btnlink-border-color']; ?>;
	padding-left: 2px;
	padding-right: 2px;
/* 	width: 15px; */
	margin-right: 4px;
}

/*     BLOCK DIVIDERS           */
.block {
	color: #<? echo $c['a']; ?>;
	height: 1px;
	border: 1px dashed #<? echo $c['a']; ?>;
}


/*     TITLES           */

.title {
	color: #<? echo $c['title-color']; ?>;
	border-bottom: 1px solid #<? echo $c['title-under-color']; ?>;
	padding: 5px;
	font-size: 16px;
	padding-left: 5px;
	margin-bottom: 2px;
	font-variant: small-caps;
	font-weight: bolder;
}

th {
	font-size: 12px;
	font-weight: normal;
	color: #<? echo $c['th-color']; ?>;
	background-color: #<? echo $c['th-background']; ?>;
}

.td0, .td1 {
	font-size: 10px;
	font-weight: normal;
	color: #<? echo $c['td-color']; ?>;
	padding: 2px;
}

.td1 { background-color: #<? echo $c['td1']; ?>; }
.td0 { background-color: #<? echo $c['td0']; ?>; }

.inlineth {
	font-size: 14px;
	font-weight: bold;
	font-variant: small-caps;
	background-color: #C0C0C0;
	color: #333333;
	padding-left: 5px;
	padding-top: 10px;	
	border-top: 1px solid #000000;
	border-bottom: 1px solid #000000;
}

/*      DATE AND TIME DISPLAYS, ETC (below content blocks)  */
.contentinfo {
	color: #<? echo $c['contentinfo']; ?>;
}

/*      THE, UH, I FORGOT (oh yeah... the download bar thingy)  */
.downloadbar {
	color: #<? echo $c['text']; ?>;
	background-color: #<? echo $c['box-color']; ?>;
	border: 1px solid #<? echo $c['box-border-color']; ?>;
	padding: 5px;
	padding-left: 15px;
}
.editnote {
	color: #<? echo $c['a']; ?>;
}

.error {
	color: #E00;
	font-weight: bold;
}

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
	font-family: Arial, Verdana, Helvetica, sans-serif;
	font-size: <? echo $c['font-size']; ?>;
}

.sitetitle {
	color: #<? echo $c['navtext']; ?>;
	font-family: Arial, Verdana, Helvetica, sans-serif;
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
	background: white url('<? echo "$themesdir/$theme/images/$c[bg]/topright.gif"; ?>') no-repeat;
	width: 7px;
	height: 51px;
}

body {
	 background-color: #FFFFFF; 
	 /* background-image: url(images/shadowside/bg.gif"; ?>');  */
}

.top {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/top.gif"; ?>') repeat-x;
	height: 29px;
}

.topcenter {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topcenter.gif"; ?>') repeat-x;
	height: 10px;
}

.topright {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topright.gif"; ?>') no-repeat;
	width: 7px;
	height: 29px;
}

.topright1 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topright1.gif"; ?>') no-repeat;
	width: 9px;
	height: 10px;
}

.topleft {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft.gif"; ?>') no-repeat;
	width: 160px;
	height: 29px;
}

.topleft1 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft1.gif"; ?>') no-repeat;
	width: 160px;
	height: 26px;
}

.topleft2 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft2.gif"; ?>') no-repeat;
	background-color: #<? echo $c[bgcolor] ?>;
	width: 160px;
	height: 36px;
}

.topleft3 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/topleft3.gif"; ?>') no-repeat;
	width: 160px;
	height: 10px;
}

.left {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/left.gif"; ?>') repeat-y;
	width: 160px;
	vertical-align: top;
}


.right {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/right.gif"; ?>') repeat-y;
	width: 7px;
	vertical-align: top;
}

.right2 {
	background-color: #003366;
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/right2.gif"; ?>') repeat-y;
	width: 9px;
	vertical-align: top;
}


.bottomleft {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottomleft.gif"; ?>') no-repeat;
	width: 160px;
	height: 20px;
}

.bottom {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottom.gif"; ?>') repeat-x;
	height: 20px;
}

.bottomright1 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottomright1.gif"; ?>') no-repeat;
	width: 9px;
	height: 20px;
}

.bottomright2 {
	background: url('<? echo "$themesdir/$theme/images/$c[bg]/bottomright2.gif"; ?>') no-repeat;
	width: 7px;
	height: 20px;
}

</style>