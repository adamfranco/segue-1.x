<style type='text/css'>
/* ------------------------------------------ */
/*  REQUIRED CLASSES    */
/* ------------------------------------------ */

/*    LINKS   */
a {
	color: #<? echo $linkcolor; ?>;
	text-decoration: none;
}

a:hover {
	text-decoration: underline;
	color: #<? echo $linkcolor; ?>;
}

div, p, td, span, input { 
	color: #<? echo $textcolor; ?>;
	font-family: "Verdana", "Arial", "Helvetica", "sans-serif";
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
	/* border: 1px solid #<? echo $c['box-border-color']; ?>; */ 
	background-color: #<? echo $c['contentarea']; ?>;
	padding: 0px;
	margin-left: 0px;
	margin-right: 0px;
	text-align: right;  
}


table, td { border: 0px solid black }


/*     THE BUTTONS IN EDIT MODE (for delete, edit, etc)    */
.btnlink {
	color: #<? echo $c['btnlink-color']; ?>;
	font-weight: bold;
	font-size: 11px;
	border: 1px solid #<? echo $c['btnlink-border-color']; ?>;
	padding-left: 0px;
	padding-right: 0px;
/* 	width: 15px; */
	margin-right: 0px;
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
	padding-left: 40px;
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
	background-color: #f6f6f6;
	color: #aaa;
	padding-left: 30px;
}

/*      DATE AND TIME DISPLAYS, ETC (below content blocks)  */
.contentinfo {
	color: #<? echo $c['contentinfo']; ?>;
}

.editinfo {
	font-size: 10px;
	color: #<? echo $c['contentinfo']; ?>;
	border-bottom: 1px solid #<? echo $c['contentinfo']; ?>;
	margin-bottom: 20px;
}


/*      THE, UH, I FORGOT (oh yeah... the download bar thingy)  */
.downloadbar {
	color: #<? echo $c['text']; ?>;
	background-color: #<? echo $c['box-color']; ?>;
	border: 1px solid #<? echo $c['box-border-color']; ?>;
	padding: 5px;
	padding-left: 15px;
}

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