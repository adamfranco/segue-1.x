<? // theme defaults file
	// this file contains defaults for text color, hiliting, etc etc etc

$nav_arrange = "2";
	
$css = "

/**
 * This is the area at the top of the 
 * screen in which the users' custom 
 * header is placed.
 */
.header {}

/**
 * This is the area at the bottom of 
 * the screen in which the users' 
 * custom footer is placed.
 */
.footer {
clear: both;
}

/**
 * This is the holds the login bar and 
 * links to the Segue home.
 */
.status {
float: right;
}

/**
 * This class is for the
 * 'site > section > page' links
 */
.breadcrumbs {}

/**
 * This holds the section links if 
 * 'Top Sections' is chosen.
 */
.topnav {
text-align: center;
}


/**
 * This is a container for the leftnav, 
 * content, and right nav.
 *
 * To enable floating columns, the 
 * padding-left should match the width 
 * of the left navigation and the 
 * padding-right should match the width 
 * of the right navigation.
 */
.contentarea {
padding-left: 150px; 
padding-right: 150px;
clear: both;
}

/**
 * This holds the left-navigation.
 *
 * To enable floating columns, the 
 * width this left-navigation should
 * match the padding-left of the 
 * content-area. As well, the 'right' 
 * position should match the width of 
 * this column plus the width of any 
 * borders.
 */
.leftnav_container {
position: relative;
float: left;
width: 150px;
right: 150px;
}

/**
 * This is a div nested in the 
 * leftnav_container to to allow
 * easier styling of margins 
 * around text.
 */
.leftnav {
	border: 1px dotted #CCCCCC;
	min-height: 300px;
	padding: 5px;
}

/**
 * This holds the right-navigation links
 *
 * To enable floating columns, the 
 * width this right-navigation should
 * match the padding-right of the 
 * content-area. As well, the margin-
 * right should match the width of 
 * this column plus the width of any 
 * borders.
 */
.rightnav_container {
position: relative;
float: left;
width: 150px;
margin-right: -150px;
}

/**
 * This is a div nested in the 
 * rightnav_container to to allow
 * easier styling of margins 
 * around text.
 */
.rightnav {
	border: 1px dotted #CCCCCC;
	min-height: 300px;
	padding: 5px;
}


/**
 * This holds the center content.
 */
.content_container {
position: relative;
float: left;
width: 100%;
margin-left: -150px;
}

/**
 * This holds the stories and is nested
 * inside the content_container to allow
 * easier styling of margins around text.
 */
.content {
margin: 5px;
}

/**
 * This holds the section links if 
 * 'Top Sections' is chosen.
 */
.bottomnav {
clear: both;
text-align: center;

}

/**
 * This class is used for sections 
 * in 'Side Sections' mode
 */
.sidesectionnav {
font-size: large;
}

/**
 * This class is used for sections 
 * in 'Top Sections' mode
 */
.sectionnav {
font-size: large;
}

/**
 * This class is used for pages in 
 * 'Side Sections' mode
 */
.subnav {
margin-left: 5px;
}

/**
 * This class is used for pages in 
 * 'Top Sections' mode
 */
.nav {}

/**
 * This class is used for page titles.
 */
.title {
font-size: large;
font-weight: bold;
}

/**
 * This is used for page-level rss 
 * feed and category display.
 */
.page_sidebar_content {
border: 1px solid;
}

/**
 * This is used for the edit-mode links
 * on sections and pages.
 */
.nav_extras {
font-size: small;
}

/**
 * These are the content blocks
 */
.story {}

/**
 * These are used when there multiple 
 * pages of stories
 */
.multi_page_links {}

.previous_page_link {}

.next_page_link {}

/**
 * This is used for the RSS link when 
 * displaying at the bottom of each 
 * public page.
 */
.rss_link {}

/**
 * Some other classes used by Segue...
 */
.headerbox {}

.textfield {}

.navlink {}

.btnlink {}

";	



