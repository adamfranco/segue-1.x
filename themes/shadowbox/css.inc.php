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

div, p, td, span, input { 
	color: #<? echo $c['text']; ?>;
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
	border: 1px solid #<? echo $c['box-border-color']; ?>;
	background-color: #<? echo $c['box-color']; ?>;
	padding: 5px;
	margin-left: 25px;
	margin-right: 25px;
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


.header {
/* 	background-color: #<? echo $c['header']; ?>; */
/* 	border-bottom: 1px solid #<? echo $c['borders']; ?>; */
/* 	margin-left: 25px; */
/* 	margin-right: 25px; */
	margin-bottom: 10px;
}

.topnav {
	padding: 10px;
/* 	background-color: #<? echo $c['topnav']; ?>; */
}

.leftnav {
/* 	float: left; */
	width: 125px;
	padding: 10px;
	margin-right: 20px;
	border-right: 1px dashed #<? echo $c['borders']; ?>;
	vertical-align: top;
}

.contentarea {
	vertical-align: top;
	padding: 10px;
}

.contenttable {
	border-bottom: 1px dashed #<? echo $c['borders']; ?>;
	border-top: 1px dashed #<? echo $c['borders']; ?>;
}

.content {
	padding: 10px;
	background: #<? echo $c['contentarea']; ?>;
}

/* more images */

.topright {
	background: white url('<? echo "$themesdir/$theme/images/topright.gif"; ?>') no-repeat;
	width: 25px;
	height: 25px;
}

.top {
	background: white url('<? echo "$themesdir/$theme/images/top.gif"; ?>') repeat-x;
	height: 25px;
}

.topleft {
	background: white url('<? echo "$themesdir/$theme/images/topleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 25px;
}

.right {
	background: white url('<? echo "$themesdir/$theme/images/right.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.left {
	background: white url('<? echo "$themesdir/$theme/images/left.gif"; ?>') repeat-y;
	width: 25px;
	vertical-align: top;
}

.bottomleft {
	background: white url('<? echo "$themesdir/$theme/images/bottomleft.gif"; ?>') no-repeat;
	width: 25px;
	height: 25px;
}

.bottom {
	background: white url('<? echo "$themesdir/$theme/images/bottom.gif"; ?>') repeat-x;
	height: 25px;
}

.bottomright {
	background: white url('<? echo "$themesdir/$theme/images/bottomright.gif"; ?>') no-repeat;
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