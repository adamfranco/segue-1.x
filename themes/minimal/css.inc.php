<style type='text/css'>

/*  REQUIRED CLASSES    */

a {
	color: #<? echo $c['a']; ?>;
/*	font-weight: bolder;*/
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

.button {
/*	font-size: 10px;*/
	border: 1px solid #<? echo $c['input-borders']; ?>;
	background-color: #FFFFFF;
/*	padding-bottom: 1px;*/
}

.textfield {
	border: 1px solid #<? echo $c['input-borders']; ?>;
	background-color: #fff;
}

select, textarea, input {
	font-size: <? echo $c['input-size']; ?>;
}

.headerbox {
	border: 1px solid #<? echo $c['box-border-color']; ?>;
	background-color: #<? echo $c['box-color']; ?>;
	padding: 5px;
}

table, td { border: 0px solid black }

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

.contentinfo {
	color: #<? echo $c['contentinfo']; ?>;
/* 	background-color: #f0f0f0; */
}

.downloadbar {
	color: #<? echo $c['text']; ?>;
	background-color: #<? echo $c['box-color']; ?>;
	border: 1px solid #<? echo $c['box-border-color']; ?>;
	padding: 5px;
	padding-left: 15px;
}

/* THEME-SPECIFIC CLASSES */

.leftnav {
	border-left: 1px dashed #<? echo $c['borders']; ?>;
	padding-left: 10px;
}

.header {
	background-color: #<? echo $c['header']; ?>;
	border-bottom: 1px solid #<? echo $c['borders']; ?>;
}

.topnav {
	padding: 10px;
	background-color: #<? echo $c['topnav']; ?>;
}

.contentarea {
	padding: 10px;
	background-color: #<? echo $c['contentarea']; ?>;
	border-bottom: 1px dashed #<? echo $c['borders']; ?>;
	border-top: 1px dashed #<? echo $c['borders']; ?>;
}

.content {
	padding: 10px;
	border-right: 1px dashed #<? echo $c['borders']; ?>;
	border-left: 1px dashed #<? echo $c['borders']; ?>;
}

</style>