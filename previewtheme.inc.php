<? /* $Id$ */
// allows users to preview the theme

$pagetitle = "Segue > Theme Preview > ".$possible_themes[$theme];

add_link(topnav,"Section #1","#",'',"noid");
add_link(topnav,"Section #2","#",'',"noid");
add_link(topnav,"Section #3","#",'','previewtheme');
add_link(topnav,"Section #4","#",'',"noid");

add_link(leftnav,"Page #1","#",'','1');
add_link(leftnav,"Page #2","#",'','1');

printc("This is some sample content. Here you can enter any information you want, including links and pictures (or any HTML).");
printc("<br /><br />");
printc("<input type='button' value='Close This Window' onclick='window.close()' class='button' />");
