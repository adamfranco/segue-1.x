
/* ------------------------------------------ */
/*  REQUIRED CLASSES    */
/* ------------------------------------------ */

/*    LINKS   */
a {
	color: #<? echo $linkcolor; ?>;
	text-decoration: none;
}

.sectionnav { 
	font-size: <? echo $sectionnavsize; ?>px;
}

.nav { 
	font-size: <? echo $navsize; ?>px;
}

.subnav { 
	font-size: <? echo ($navsize-2); ?>px;
	margin-left: 7px;
}

a:hover {
	text-decoration: underline;
	color: #<? echo $linkcolor; ?>;
}

div, p, td, span, input { 
	color: #<? echo $textcolor; ?>;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: <? echo $c['font-size']; ?>;
}



/*    INPUT BUTTONS    */
.button {
	border: 1px solid #<? echo $c['input-borders']; ?>;
	background-color: #FFFFFF;
	color: #000000; ?>;
}

/*     TEXTFIELDS    */
.textfield {
	border: 1px solid #<? echo $c['input-borders']; ?>;
	background-color: #fff;
}

input.small {
	font-size: 10px;
	vertical-align: middle;	
}


select, textarea, input {
	font-size: <? echo $c['input-size']; ?>;
}

/*		THE STATUS BAR      */
.headerbox {
	padding: 0px;
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
	padding-left: 0px;
	padding-right: 0px;
/* 	width: 15px; */
	margin-right: 0px;
}

/*     BLOCK DIVIDERS           */
.block {
<? if ($c['a'])
	print "color: #".$c['a'].";";
?>
	height: 1px;
	border: 1px dashed<? if ($c['a']) print " #".$c['a'].";"; ?>;
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
	<? if ($c['text']) echo "color: #".$c['text'].";"; ?>
	background-color: #<? echo $c['box-color']; ?>;
	border: 1px solid #<? echo $c['box-border-color']; ?>;
	padding: 5px;
	padding-left: 15px;
}


