<style type='text/css'>

body {
	 background-color: #<? echo $bg['bg']; ?>; 
}


<? include("$themesdir/common/css.inc.php"); ?>

/* ------------------------------------------ */
/* THEME-SPECIFIC CLASSES */
/* ------------------------------------------ */


.leftnav {
/* 	float: left; */
	width: <? echo $navwidth; ?>px;
	padding: 5px;
	vertical-align: top;
}


.header {
	margin-bottom: 0px;
	background: #<? echo $bg['bg']; ?>;	
}

.topnav {
	padding: 5px;
	background: #<? echo $bg['bg']; ?>;

}

.contentarea {
	vertical-align: top;
	padding: 10px;
	background-color: #<? echo $c['contentarea']; ?>;
	border-bottom: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	border-top: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;

}

.contenttable {
	border-right: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	border-left: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
}


<? $contentwidth = 600 - $navwidth; ?>
.content {
	width: <? echo $contentwidth; ?>px;
	padding: 10px;
	border-left: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;

}

</style>