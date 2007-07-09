<?php

if (!isset($bordercolor))
	$bordercolor = '000000';
	
?>

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

.sidesectionnav { 
	font-size: <? echo $sectionnavsize; ?>px;
	border-top: 1px solid #<? echo $bordercolor; ?>; 
	border-bottom: 1px solid #<? echo $bordercolor; ?>;
	padding-top: 2px; 
	padding-bottom: 2px;
}

.nav { 
	font-size: <? echo $navsize; ?>px;
}

.subnav { 
	margin-left: 7px;
	font-size: <? echo $navsize; ?>px;
}

a:hover {
	text-decoration: underline;
}

body { 
<?php
	if (isset($textcolor) && $textcolor)
		print "\tcolor: #".$textcolor.";";
?>

	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: <? echo $c['font-size']; ?>;
}

div.hr {
	height: 1px;
	color: #<? echo $bordercolor; ?>;
    background-color: #<? echo $bordercolor; ?>;
    margin-top: 5px;
	margin-bottom: 5px;
}

 div.hr hr {
  display: none;
}



/*    INPUT BUTTONS    */
.button {
	color: #000000;
	border: 1px solid #<? echo $c['input-borders']; ?>;
	background-color: #FFFFFF;
	color: #000000;
}

/*     TEXTFIELDS    */
.textfield {
	color: #000000;
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



/*     THE BUTTONS IN EDIT MODE (for delete, edit, etc)    */
.btnlink {
	color: #<? echo $linkcolor; ?>;
	font-size: 10px;
	border: 1px solid #<? echo $linkcolor; ?>;
	padding-left: 1px;
	padding-right: 1px;
/* 	width: 15px; */
	margin-right: 0px;
}

.btnlink2 {
	color: #<? echo $linkcolor; ?>;
	font-size: 10px;
	border: 1px solid #<? echo $linkcolor; ?>;
	padding: 3px;
	margin-bottom: 7px;
}

.versions {
	color: #<? echo $linkcolor; ?>;
	border: 1px solid #<? echo $linkcolor; ?>;
	padding-left: 3px;
	padding-right: 3px;
	padding-top: 3px;
	padding-bottom: 3px;/* 	width: 15px; */
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
	border-bottom: 1px solid #<? echo $c['title-color']; ?>;
	padding: 5px;
	font-size: 16px;
	padding-left: 0px;
	margin-bottom: 7px;
	font-variant: small-caps;
	font-weight: bolder;
}

.title2 {
	border-bottom: 1px solid #<? echo $c['title-color']; ?>;
	padding: 3px;
	font-size: 12px;
	padding-left: 0px;
	margin-bottom: 3px;

}

.foundtext {
	font-weight: bold;
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

.contentinfo2 {
	color: #<? echo $c['contentinfo']; ?>;
	border: 1px solid #<? echo $linkcolor; ?>;
	padding-left: 1px;
	padding-right: 1px;
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
	border: 1px solid #<? echo $c['box-border-color']; ?>;
	padding: 5px;
	padding-left: 15px;
}

/*********************************************************
 * Discussion Styles
 *********************************************************/
.subject { 
	font-weight: bolder; 
	font-color: #<? echo $bg['bglink']; ?>;
}

.subject a {
	color: #<? echo $bg['bglink']; ?>;
}

.dtext {
	padding-top: 0px;
	padding-bottom: 20px;
	padding-left: 0px;
	padding-right: 0px;
}


.dheader {
	font-size: 14px;
	font-color: #<? echo $bg['bglink']; ?>;
	color: #<? echo $bg['bglink']; ?>;
	border-top: 1px solid #<? echo $bordercolor; ?>;
	border-bottom: 1px solid #<? echo $bordercolor; ?>;
	background: #<? echo $bg['bg']; ?>;
	padding-left: 5px;
	padding-top: 5px;
}

.dheader a {
	color: #<? echo $bg['bglink']; ?>;
}

.dheader2 {
	border-top: 1px solid #<? echo $bordercolor; ?>;
	border-bottom: 1px solid #<? echo $bordercolor; ?>;
	background: #<? echo $bg['bg']; ?>;
	color: #<? echo $bg['bglink']; ?>;
	padding-right: 5px;
	padding-top: 5px;
}

.dheader2 a {
	color: #<? echo $bg['bglink']; ?>;
}


.dheader3 {
	border: 1px solid #<? echo $bordercolor; ?>;
	background: #<? echo $bg['bg']; ?>;
	color: #<? echo $bg['bglink']; ?>;
	font-color: #<? echo $bg['bglink']; ?>;
	padding-left: 5px;
	padding-right: 5px;
}

.dheader3 a {
	color: #<? echo $bg['bglink']; ?>;
}


.dinfo1 {
	border: 1px solid #000;
	border: 1px solid #<? echo $bordercolor; ?>;
	padding-left: 2px;
	padding-right: 2px;
}

/*********************************************************
 * Version Styles
 *********************************************************/

.versionborders {
	border-bottom: 1px dotted #<? echo $bordercolor; ?>;
}

.versionshade {
	border-bottom: 1px dotted #<? echo $bordercolor; ?>;
}
