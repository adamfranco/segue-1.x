# phpMyAdmin MySQL-Dump
# version 2.2.0rc3
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# Host: badger
# Generation Time: August 17, 2002, 3:26 pm
# Server version: 3.23.36
# PHP Version: 4.1.2
# Database : sitesdb
# --------------------------------------------------------

#
# Table structure for table 'classgroups'
#

CREATE TABLE `classgroups` (
  `id` bigint(20) NOT NULL auto_increment,
  `owner` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `classes` blob NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM;

#
# Dumping data for table 'classgroups'
#

# --------------------------------------------------------

#
# Table structure for table 'discussions'
#

CREATE TABLE `discussions` (
  `id` bigint(20) NOT NULL auto_increment,
  `author` varchar(100) NOT NULL default '',
  `authortype` varchar(20) NOT NULL default '',
  `timestamp` timestamp(14) NOT NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM;

#
# Dumping data for table 'discussions'
#

INSERT INTO discussions VALUES (1,'gabe','user',20020809170921,'Well%2C+to+be+perfectly+frank%2C+I+love+it.+What+could+one+enjoy+more+than+eating%2C+sleeping%2C+and+having+___%3F');
INSERT INTO discussions VALUES (2,'gabe','user',20020809173017,'It+sure+is+confusing...+Let%5C%27s+just+say+that+we+who+live+in+Vermont+truly+understand%2C+well%2C+everything.+People+who+move+to+places+near+the+equator+just+don%5C%27t.+I+actually+think+it%5C%27s+that+simple.');
INSERT INTO discussions VALUES (3,'achapin','user',20020809173204,'Inevitably%2C+though+life+is+suffering.++We+are+born%2C+which+is+quite+painful%2C+not+knowing+how+to+be%2C+throughout+our+lives+we+experience+pain+in+infinitely+subtle+varieties+resulting+in+sickness+and+before+we+have+fully+grasping+what+it+means+to+be+alive+we+begin+to+die%2C+losing+the+freshness+and+vitality+that+accompanies+new+experiences.');
INSERT INTO discussions VALUES (4,'achapin','user',20020809173436,'I+disagree+with+the+simple+view+of+climate+espoused+above.++Climate%2C+like+all+phenomena%2C+is+relative.++What+is+cold+and+barren+to+one+is+warm+and+bliss+to+another');
INSERT INTO discussions VALUES (5,'afranco','user',20020809174008,'Well%2C+a+really+large+asteroid+hitting+the+earth+could+cause+enough+dust+to+fill+the+atmosphere+to+trigger+an+ice+age.++The+only+problem+is+the+collateral+damage.%0D%0A%0D%0AHow+bad+would+it+be+if+an+asteroid+hit+the+earth%3F+We+have+no+way+to+know+for+sure%2C+but+experiments+with+a+common+laboratory+frog+and+a+sledge+hammer+sugest+that+it+would+be+pretty+bad.');
INSERT INTO discussions VALUES (6,'afranco','user',20020809174350,'Life+rocks.%0D%0A%0D%0AThe+key+is+living+within+your+means+so+that+you+have+the+time+and+energy+to+experience+the+joys+of+family+and+comunity%3B+its+the+experiences+that+make+life+great%2C+not+the+objects+and+posessions+that+we+attempt+to+fill+our+lives+with.');
INSERT INTO discussions VALUES (7,'afranco','user',20020809174436,'Remember%3A%0D%0A%0D%0ANot+all+that+glitters+is+gold%2C+but+it+does+contain+free+electric+charge+carriers.');
INSERT INTO discussions VALUES (8,'gabe','user',20020810224657,'That%5C%27s+a+lot+of+bullshit.');
INSERT INTO discussions VALUES (9,'jbutler','user',20020814104123,'I+love+life.It%5C%27s+great%21');
INSERT INTO discussions VALUES (10,'jbutler','user',20020816123000,'Well%2C+as+the+facilitator+of+this+discussion%2C+let+me+just+say+that+life+is+cool.');
INSERT INTO discussions VALUES (11,'jschine','user',20020816123050,'Life+sucks+my+poop.+It%5C%27s+troubling.');
# --------------------------------------------------------

#
# Table structure for table 'logs'
#

CREATE TABLE `logs` (
  `timestamp` timestamp(14) NOT NULL,
  `type` varchar(255) NOT NULL default '',
  `content` text NOT NULL
) TYPE=MyISAM COMMENT='Log entries';

#
# Dumping data for table 'logs'
#

INSERT INTO logs VALUES (20020807174549,'login','gschine');
INSERT INTO logs VALUES (20020807180451,'login','achapin');
INSERT INTO logs VALUES (20020807180542,'login','achapin');
INSERT INTO logs VALUES (20020807180759,'change_auser','achapin as sax');
INSERT INTO logs VALUES (20020807181310,'login','gabe');
INSERT INTO logs VALUES (20020807181404,'login','gabe');
INSERT INTO logs VALUES (20020807181413,'add_site','gabe added gabe');
INSERT INTO logs VALUES (20020807181945,'delete_site','gabe deleted site gabe');
INSERT INTO logs VALUES (20020807182259,'change_auser','achapin as veguez');
INSERT INTO logs VALUES (20020809094932,'login','beyer');
INSERT INTO logs VALUES (20020809103105,'login','gabe');
INSERT INTO logs VALUES (20020809103156,'change_auser','gabe as beyer');
INSERT INTO logs VALUES (20020809104503,'change_auser','gabe as gabe');
INSERT INTO logs VALUES (20020809105904,'login','achapin');
INSERT INTO logs VALUES (20020809105921,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020809110002,'add_site','psaldarr added sp210d-f02');
INSERT INTO logs VALUES (20020809110016,'add_section','psaldarr added section id 1 to site sp210d-f02');
INSERT INTO logs VALUES (20020809110031,'add_page','psaldarr added page id 1 to sp210d-f02');
INSERT INTO logs VALUES (20020809110117,'add_story','psaldarr added story id 1 to page id 1 in site sp210d-f02');
INSERT INTO logs VALUES (20020809110139,'add_page','psaldarr added page id 2 to sp210d-f02');
INSERT INTO logs VALUES (20020809110150,'add_story','psaldarr added story id 2 to page id 2 in site sp210d-f02');
INSERT INTO logs VALUES (20020809110415,'add_section','psaldarr added section id 2 to site sp210d-f02');
INSERT INTO logs VALUES (20020809110426,'add_page','psaldarr added page id 3 to sp210d-f02');
INSERT INTO logs VALUES (20020809110552,'add_section','psaldarr added section id 3 to site sp210d-f02');
INSERT INTO logs VALUES (20020809110626,'add_page','psaldarr added page id 4 to sp210d-f02');
INSERT INTO logs VALUES (20020809110657,'add_story','psaldarr added story id 3 to page id 4 in site sp210d-f02');
INSERT INTO logs VALUES (20020809130417,'login','achapin');
INSERT INTO logs VALUES (20020809130432,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020809130510,'login','achapin');
INSERT INTO logs VALUES (20020809130517,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020809131202,'edit_site','psaldarr edited sp210d-f02');
INSERT INTO logs VALUES (20020809131325,'add_page','psaldarr added page id 5 to sp210d-f02');
INSERT INTO logs VALUES (20020809131337,'add_story','psaldarr added story id 4 to page id 5 in site sp210d-f02');
INSERT INTO logs VALUES (20020809131353,'add_page','psaldarr added page id 6 to sp210d-f02');
INSERT INTO logs VALUES (20020809131408,'add_story','psaldarr added story id 5 to page id 6 in site sp210d-f02');
INSERT INTO logs VALUES (20020809131442,'edit_story','psaldarr edited story id 4 in page id 5 in site sp210d-f02');
INSERT INTO logs VALUES (20020809132509,'add_section','psaldarr added section id 4 to site sp210d-f02');
INSERT INTO logs VALUES (20020809132543,'add_page','psaldarr added page id 7 to sp210d-f02');
INSERT INTO logs VALUES (20020809132614,'add_story','psaldarr added story id 6 to page id 7 in site sp210d-f02');
INSERT INTO logs VALUES (20020809132629,'add_page','psaldarr added page id 8 to sp210d-f02');
INSERT INTO logs VALUES (20020809132650,'add_story','psaldarr added story id 7 to page id 8 in site sp210d-f02');
INSERT INTO logs VALUES (20020809132709,'add_page','psaldarr added page id 9 to sp210d-f02');
INSERT INTO logs VALUES (20020809132720,'add_story','psaldarr added story id 8 to page id 9 in site sp210d-f02');
INSERT INTO logs VALUES (20020809132746,'add_section','psaldarr added section id 5 to site sp210d-f02');
INSERT INTO logs VALUES (20020809132809,'add_section','psaldarr added section id 6 to site sp210d-f02');
INSERT INTO logs VALUES (20020809132846,'add_page','psaldarr added page id 10 to sp210d-f02');
INSERT INTO logs VALUES (20020809133001,'add_story','psaldarr added story id 9 to page id 10 in site sp210d-f02');
INSERT INTO logs VALUES (20020809140938,'login','gabe');
INSERT INTO logs VALUES (20020809140953,'edit_site','gabe edited template1');
INSERT INTO logs VALUES (20020809141004,'delete_site','gabe deleted site test_template1');
INSERT INTO logs VALUES (20020809141231,'edit_site','gabe edited sample');
INSERT INTO logs VALUES (20020809164358,'login','gabe');
INSERT INTO logs VALUES (20020809164406,'edit_site','gabe edited template1');
INSERT INTO logs VALUES (20020809164411,'edit_site','gabe edited template2');
INSERT INTO logs VALUES (20020809164415,'edit_site','gabe edited template3');
INSERT INTO logs VALUES (20020809164419,'edit_site','gabe edited template3');
INSERT INTO logs VALUES (20020809164553,'edit_section','gabe edited section id 23');
INSERT INTO logs VALUES (20020809164602,'edit_page','gabe edited page id 79 in sample');
INSERT INTO logs VALUES (20020809165153,'edit_story','gabe edited story id 45 in page id 79 in site sample');
INSERT INTO logs VALUES (20020809165248,'edit_story','gabe edited story id 45 in page id 79 in site sample');
INSERT INTO logs VALUES (20020809165328,'edit_story','gabe edited story id 46 in page id 79 in site sample');
INSERT INTO logs VALUES (20020809165338,'edit_story','gabe edited story id 47 in page id 79 in site sample');
INSERT INTO logs VALUES (20020809165436,'edit_story','gabe edited story id 48 in page id 80 in site sample');
INSERT INTO logs VALUES (20020809165517,'add_page','gabe added page id 81 to sample');
INSERT INTO logs VALUES (20020809165536,'add_story','gabe added story id 50 to page id 81 in site sample');
INSERT INTO logs VALUES (20020809165604,'add_story','gabe added story id 51 to page id 81 in site sample');
INSERT INTO logs VALUES (20020809165621,'add_story','gabe added story id 52 to page id 81 in site sample');
INSERT INTO logs VALUES (20020809165716,'add_story','gabe added story id 53 to page id 81 in site sample');
INSERT INTO logs VALUES (20020809165747,'add_story','gabe added story id 54 to page id 81 in site sample');
INSERT INTO logs VALUES (20020809170049,'add_section','gabe added section id 24 to site sample');
INSERT INTO logs VALUES (20020809170212,'add_page','gabe added page id 82 to sample');
INSERT INTO logs VALUES (20020809170220,'add_page','gabe added page id 83 to sample');
INSERT INTO logs VALUES (20020809170231,'edit_page','gabe edited page id 82 in sample');
INSERT INTO logs VALUES (20020809170243,'edit_page','gabe edited page id 83 in sample');
INSERT INTO logs VALUES (20020809170439,'add_story','gabe added story id 55 to page id 82 in site sample');
INSERT INTO logs VALUES (20020809170531,'add_story','gabe added story id 56 to page id 82 in site sample');
INSERT INTO logs VALUES (20020809170602,'edit_story','gabe edited story id 56 in page id 82 in site sample');
INSERT INTO logs VALUES (20020809170607,'login','achapin');
INSERT INTO logs VALUES (20020809170615,'edit_story','gabe edited story id 55 in page id 82 in site sample');
INSERT INTO logs VALUES (20020809170737,'add_story','gabe added story id 57 to page id 82 in site sample');
INSERT INTO logs VALUES (20020809170748,'edit_story','gabe edited story id 57 in page id 82 in site sample');
INSERT INTO logs VALUES (20020809171201,'add_story','gabe added story id 58 to page id 83 in site sample');
INSERT INTO logs VALUES (20020809171226,'edit_site','gabe edited sample');
INSERT INTO logs VALUES (20020809171751,'edit_site','gabe edited sample');
INSERT INTO logs VALUES (20020809171802,'edit_site','gabe edited sample');
INSERT INTO logs VALUES (20020809171829,'edit_page','gabe edited page id 83 in sample');
INSERT INTO logs VALUES (20020809172633,'edit_story','gabe edited story id 56 in page id 82 in site sample');
INSERT INTO logs VALUES (20020809172904,'login','gabe');
INSERT INTO logs VALUES (20020809173309,'add_story','gabe added story id 59 to page id 80 in site sample');
INSERT INTO logs VALUES (20020809173457,'login','afranco');
INSERT INTO logs VALUES (20020809175016,'add_story','achapin added story id 60 to page id 83 in site sample');
INSERT INTO logs VALUES (20020809175231,'add_story','afranco added story id 61 to page id 83 in site sample');
INSERT INTO logs VALUES (20020809175532,'change_auser','achapin as jfu');
INSERT INTO logs VALUES (20020809175539,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175548,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175625,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020809175652,'change_auser','achapin as achapin');
INSERT INTO logs VALUES (20020809175705,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175718,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175742,'edit_story','achapin edited story id 60 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175743,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175749,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175800,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809175913,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809180007,'edit_story','afranco edited story id 61 in page id 83 in site sample');
INSERT INTO logs VALUES (20020809180118,'add_page','gabe added page id 84 to sample');
INSERT INTO logs VALUES (20020809180202,'add_story','gabe added story id 62 to page id 84 in site sample');
INSERT INTO logs VALUES (20020809180357,'add_story','gabe added story id 63 to page id 84 in site sample');
INSERT INTO logs VALUES (20020809180433,'add_story','gabe added story id 64 to page id 84 in site sample');
INSERT INTO logs VALUES (20020809183025,'login','gabe');
INSERT INTO logs VALUES (20020809183035,'change_auser','gabe as veguez');
INSERT INTO logs VALUES (20020809183049,'classgroups','veguez added sp101 with sp101a-f02,sp101b-f02');
INSERT INTO logs VALUES (20020809183511,'classgroups','veguez updated sp101 to be sp101a-f02,sp101b-f02');
INSERT INTO logs VALUES (20020810100826,'login','gabe');
INSERT INTO logs VALUES (20020810100858,'change_auser','gabe as veguez');
INSERT INTO logs VALUES (20020810100910,'classgroups','veguez removed group sp101');
INSERT INTO logs VALUES (20020810100941,'change_auser','gabe as gabe');
INSERT INTO logs VALUES (20020810125052,'login','gabe');
INSERT INTO logs VALUES (20020810125118,'add_site','gabe added gabe');
INSERT INTO logs VALUES (20020810125141,'add_section','gabe added section id 25 to site gabe');
INSERT INTO logs VALUES (20020810125147,'add_page','gabe added page id 85 to gabe');
INSERT INTO logs VALUES (20020810125153,'add_page','gabe added page id 86 to gabe');
INSERT INTO logs VALUES (20020810125409,'add_story','gabe added story id 65 to page id 86 in site gabe');
INSERT INTO logs VALUES (20020810125635,'delete_story','gabe deleted story id 65');
INSERT INTO logs VALUES (20020810125701,'change_auser','gabe as admin');
INSERT INTO logs VALUES (20020810125711,'change_auser','gabe as gabe');
INSERT INTO logs VALUES (20020810125819,'change_auser','gabe as psaldarr');
INSERT INTO logs VALUES (20020810130001,'change_auser','gabe as gabe');
INSERT INTO logs VALUES (20020810130041,'edit_site','gabe edited gabe');
INSERT INTO logs VALUES (20020810130121,'login','gschine');
INSERT INTO logs VALUES (20020810130138,'add_site','gschine added gschine');
INSERT INTO logs VALUES (20020810130225,'login','gschine');
INSERT INTO logs VALUES (20020810130243,'login','jschine');
INSERT INTO logs VALUES (20020810224446,'login','gabe');
INSERT INTO logs VALUES (20020810224832,'login','jschine');
INSERT INTO logs VALUES (20020810224911,'add_site','jschine added jschine');
INSERT INTO logs VALUES (20020810224927,'add_section','jschine added section id 30 to site jschine');
INSERT INTO logs VALUES (20020810224934,'add_page','jschine added page id 107 to jschine');
INSERT INTO logs VALUES (20020810224951,'add_story','jschine added story id 75 to page id 107 in site jschine');
INSERT INTO logs VALUES (20020810225021,'add_story','jschine added story id 76 to page id 107 in site jschine');
INSERT INTO logs VALUES (20020810225053,'add_page','jschine added page id 108 to jschine');
INSERT INTO logs VALUES (20020810225112,'add_story','jschine added story id 77 to page id 108 in site jschine');
INSERT INTO logs VALUES (20020810225152,'add_story','jschine added story id 78 to page id 108 in site jschine');
INSERT INTO logs VALUES (20020810225205,'delete_story','jschine deleted story id 78');
INSERT INTO logs VALUES (20020810225312,'edit_site','jschine edited jschine');
INSERT INTO logs VALUES (20020810225338,'edit_site','jschine edited jschine');
INSERT INTO logs VALUES (20020810225402,'add_section','jschine added section id 31 to site jschine');
INSERT INTO logs VALUES (20020810225420,'login','gabe');
INSERT INTO logs VALUES (20020810225425,'change_auser','gabe as schine');
INSERT INTO logs VALUES (20020810225445,'add_page','schine added page id 109 to jschine');
INSERT INTO logs VALUES (20020810225502,'add_story','schine added story id 79 to page id 109 in site jschine');
INSERT INTO logs VALUES (20020810225517,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020810225548,'edit_story','jschine edited story id 75 in page id 107 in site jschine');
INSERT INTO logs VALUES (20020810225617,'delete_site','jschine deleted site jschine');
INSERT INTO logs VALUES (20020810225726,'change_auser','gabe as beyer');
INSERT INTO logs VALUES (20020810225731,'change_auser','gabe as gabe');
INSERT INTO logs VALUES (20020810225852,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020810230008,'add_site','jschine added jschine');
INSERT INTO logs VALUES (20020810230026,'edit_site','jschine edited jschine');
INSERT INTO logs VALUES (20020810230203,'edit_site','jschine edited jschine');
INSERT INTO logs VALUES (20020810230253,'edit_site','jschine edited jschine');
INSERT INTO logs VALUES (20020810230325,'delete_site','jschine deleted site jschine');
INSERT INTO logs VALUES (20020811165606,'login','achapin');
INSERT INTO logs VALUES (20020811165633,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020811165951,'edit_site','psaldarr edited sp210d-f02');
INSERT INTO logs VALUES (20020811170020,'edit_site','psaldarr edited sp210d-f02');
INSERT INTO logs VALUES (20020811170036,'edit_site','psaldarr edited sp210d-f02');
INSERT INTO logs VALUES (20020811170050,'edit_site','psaldarr edited sp210d-f02');
INSERT INTO logs VALUES (20020811170110,'edit_site','psaldarr edited sp210d-f02');
INSERT INTO logs VALUES (20020811170146,'login','achapin');
INSERT INTO logs VALUES (20020811170153,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020811170208,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020811170411,'change_auser','achapin as gschine');
INSERT INTO logs VALUES (20020811170448,'change_auser','achapin as gabe');
INSERT INTO logs VALUES (20020811175319,'login','gabe');
INSERT INTO logs VALUES (20020811175343,'change_auser','gabe as psaldarr');
INSERT INTO logs VALUES (20020812100226,'login','gabe');
INSERT INTO logs VALUES (20020812102051,'login','psaldarr');
INSERT INTO logs VALUES (20020812102240,'edit_page','psaldarr edited page id 1 in sp210d-f02');
INSERT INTO logs VALUES (20020812102258,'edit_page','psaldarr edited page id 5 in sp210d-f02');
INSERT INTO logs VALUES (20020812173800,'login','gabe');
INSERT INTO logs VALUES (20020813161151,'login','achapin');
INSERT INTO logs VALUES (20020814095352,'login','achapin');
INSERT INTO logs VALUES (20020814095401,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020814095824,'edit_site','psaldarr edited sp210d-f02');
INSERT INTO logs VALUES (20020814100649,'login','admin');
INSERT INTO logs VALUES (20020814100738,'change_auser','admin as jbutler');
INSERT INTO logs VALUES (20020814100845,'classgroups','jbutler added ar310 with ar160c-s02,ar310a-s02');
INSERT INTO logs VALUES (20020814100931,'classgroups','jbutler updated ar310 to be ar160c-s02,ar310a-s02,ar309a-f02');
INSERT INTO logs VALUES (20020814100941,'classgroups','jbutler removed ar160c-s02 from group ar310');
INSERT INTO logs VALUES (20020814100947,'classgroups','jbutler removed group ar310');
INSERT INTO logs VALUES (20020814101334,'add_site','jbutler added ar310a-s02');
INSERT INTO logs VALUES (20020814101425,'delete_site','jbutler deleted site ar310a-s02');
INSERT INTO logs VALUES (20020814101437,'add_site','jbutler added ar310a-s02');
INSERT INTO logs VALUES (20020814101541,'edit_story','jbutler edited story id 91 in page id 132 in site ar310a-s02');
INSERT INTO logs VALUES (20020814101638,'add_story','jbutler added story id 95 to page id 132 in site ar310a-s02');
INSERT INTO logs VALUES (20020814101714,'edit_story','jbutler edited story id 92 in page id 133 in site ar310a-s02');
INSERT INTO logs VALUES (20020814101740,'edit_story','jbutler edited story id 93 in page id 134 in site ar310a-s02');
INSERT INTO logs VALUES (20020814101937,'add_story','jbutler added story id 96 to page id 135 in site ar310a-s02');
INSERT INTO logs VALUES (20020814102042,'delete_story','jbutler deleted story id 94');
INSERT INTO logs VALUES (20020814103136,'edit_site','jbutler edited ar310a-s02');
INSERT INTO logs VALUES (20020814103337,'add_section','jbutler added section id 39 to site ar310a-s02');
INSERT INTO logs VALUES (20020814103555,'add_page','jbutler added page id 136 to ar310a-s02');
INSERT INTO logs VALUES (20020814103613,'add_story','jbutler added story id 97 to page id 136 in site ar310a-s02');
INSERT INTO logs VALUES (20020814103806,'add_page','jbutler added page id 137 to ar310a-s02');
INSERT INTO logs VALUES (20020814103825,'add_page','jbutler added page id 138 to ar310a-s02');
INSERT INTO logs VALUES (20020814103910,'add_page','jbutler added page id 139 to ar310a-s02');
INSERT INTO logs VALUES (20020814104055,'add_story','jbutler added story id 98 to page id 139 in site ar310a-s02');
INSERT INTO logs VALUES (20020814104805,'change_auser','admin as achapin');
INSERT INTO logs VALUES (20020814104850,'change_auser','admin as jschine');
INSERT INTO logs VALUES (20020814104858,'change_auser','admin as jbutler');
INSERT INTO logs VALUES (20020814104934,'change_auser','admin as achapin');
INSERT INTO logs VALUES (20020814105409,'change_auser','admin as jschine');
INSERT INTO logs VALUES (20020814105701,'change_auser','admin as jbutler');
INSERT INTO logs VALUES (20020814105756,'change_auser','admin as jschine');
INSERT INTO logs VALUES (20020814105844,'change_auser','admin as jbutler');
INSERT INTO logs VALUES (20020814110007,'change_auser','admin as psaldarr');
INSERT INTO logs VALUES (20020814111622,'login','gabe');
INSERT INTO logs VALUES (20020814114258,'login','achapin');
INSERT INTO logs VALUES (20020814114307,'change_auser','achapin as psaldarr');
INSERT INTO logs VALUES (20020814114500,'login','gabe');
INSERT INTO logs VALUES (20020814114600,'change_auser','gabe as pyfrom');
INSERT INTO logs VALUES (20020814114604,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020814114613,'change_auser','gabe as pyfrom');
INSERT INTO logs VALUES (20020814114847,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020814114952,'change_auser','gabe as pyfrom');
INSERT INTO logs VALUES (20020814114955,'change_auser','gabe as achapin');
INSERT INTO logs VALUES (20020814115245,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020814234125,'login','achapin');
INSERT INTO logs VALUES (20020815110506,'login','baron');
INSERT INTO logs VALUES (20020815184910,'login','achapin');
INSERT INTO logs VALUES (20020816114946,'login','gabe');
INSERT INTO logs VALUES (20020816114957,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020816115009,'change_auser','gabe as jbutler');
INSERT INTO logs VALUES (20020816115014,'delete_site','jbutler deleted site ar310a-s02');
INSERT INTO logs VALUES (20020816115020,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020816115158,'change_auser','gabe as dhoughto');
INSERT INTO logs VALUES (20020816115220,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020816115316,'change_auser','gabe as jbutler');
INSERT INTO logs VALUES (20020816120705,'add_site','jbutler added ar310a-s02');
INSERT INTO logs VALUES (20020816120806,'edit_story','jbutler edited story id 99 in page id 140 in site ar310a-s02');
INSERT INTO logs VALUES (20020816120825,'add_story','jbutler added story id 103 to page id 140 in site ar310a-s02');
INSERT INTO logs VALUES (20020816120927,'edit_story','jbutler edited story id 100 in page id 141 in site ar310a-s02');
INSERT INTO logs VALUES (20020816120957,'edit_story','jbutler edited story id 101 in page id 142 in site ar310a-s02');
INSERT INTO logs VALUES (20020816121059,'add_story','jbutler added story id 104 to page id 143 in site ar310a-s02');
INSERT INTO logs VALUES (20020816121111,'delete_story','jbutler deleted story id 102');
INSERT INTO logs VALUES (20020816121407,'add_story','jbutler added story id 105 to page id 143 in site ar310a-s02');
INSERT INTO logs VALUES (20020816121438,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020816121605,'change_auser','gabe as jbutler');
INSERT INTO logs VALUES (20020816121845,'edit_site','jbutler edited ar310a-s02');
INSERT INTO logs VALUES (20020816121957,'add_section','jbutler added section id 42 to site ar310a-s02');
INSERT INTO logs VALUES (20020816122147,'add_page','jbutler added page id 144 to ar310a-s02');
INSERT INTO logs VALUES (20020816122233,'add_page','jbutler added page id 145 to ar310a-s02');
INSERT INTO logs VALUES (20020816122317,'add_story','jbutler added story id 106 to page id 144 in site ar310a-s02');
INSERT INTO logs VALUES (20020816122333,'change_auser','gabe as jschine');
INSERT INTO logs VALUES (20020816122444,'add_story','jschine added story id 107 to page id 144 in site ar310a-s02');
INSERT INTO logs VALUES (20020816122505,'edit_story','jschine edited story id 107 in page id 144 in site ar310a-s02');
INSERT INTO logs VALUES (20020816122530,'change_auser','gabe as schine');
INSERT INTO logs VALUES (20020816122558,'change_auser','gabe as dhoughto');
INSERT INTO logs VALUES (20020816122619,'add_story','dhoughto added story id 108 to page id 145 in site ar310a-s02');
INSERT INTO logs VALUES (20020816122625,'change_auser','gabe as schine');
INSERT INTO logs VALUES (20020816122646,'edit_story','schine edited story id 108 in page id 145 in site ar310a-s02');
INSERT INTO logs VALUES (20020816122659,'change_auser','gabe as jbutler');
INSERT INTO logs VALUES (20020816122724,'add_page','jbutler added page id 146 to ar310a-s02');
INSERT INTO logs VALUES (20020816122848,'add_story','jbutler added story id 109 to page id 146 in site ar310a-s02');
INSERT INTO logs VALUES (20020816123021,'login','jschine');
INSERT INTO logs VALUES (20020816123108,'login','gabe');
INSERT INTO logs VALUES (20020816123113,'change_auser','gabe as jbutler');
INSERT INTO logs VALUES (20020816123225,'delete_site','jbutler deleted site ar310a-s02');
INSERT INTO logs VALUES (20020816123232,'change_auser','gabe as gabe');
INSERT INTO logs VALUES (20020816135319,'login','jschine');
INSERT INTO logs VALUES (20020816135526,'add_site','jschine added jschine');
INSERT INTO logs VALUES (20020816230552,'login','achapin');
INSERT INTO logs VALUES (20020816230752,'change_auser','achapin as psaldarr');
# --------------------------------------------------------

#
# Table structure for table 'pages'
#

CREATE TABLE `pages` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `addedtimestamp` datetime default NULL,
  `addedby` varchar(100) NOT NULL default '',
  `editedby` varchar(100) NOT NULL default '',
  `editedtimestamp` timestamp(14) NOT NULL,
  `activatedate` date NOT NULL default '0000-00-00',
  `deactivatedate` date NOT NULL default '0000-00-00',
  `active` tinyint(4) NOT NULL default '0',
  `locked` tinyint(4) NOT NULL default '0',
  `permissions` blob NOT NULL,
  `showcreator` tinyint(4) NOT NULL default '0',
  `showdate` tinyint(4) NOT NULL default '0',
  `stories` blob NOT NULL,
  `type` varchar(20) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `ediscussion` tinyint(4) NOT NULL default '0',
  `archiveby` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM COMMENT='pages';

#
# Dumping data for table 'pages'
#

INSERT INTO pages VALUES (1,'Profesor','2002-08-09 11:00:31','psaldarr','psaldarr',20020812102240,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A1%3B%7D','page','',1,'');
INSERT INTO pages VALUES (2,'Texto','2002-08-09 11:01:39','psaldarr','',20020809110150,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A2%3B%7D','page','',1,'');
INSERT INTO pages VALUES (3,'Prueba 1','2002-08-09 11:04:26','psaldarr','',20020809110426,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (4,'Introducción','2002-08-09 11:06:26','psaldarr','',20020809110657,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A3%3B%7D','page','',1,'');
INSERT INTO pages VALUES (5,'Objetivos','2002-08-09 13:13:25','psaldarr','psaldarr',20020812102258,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A4%3B%7D','page','',1,'');
INSERT INTO pages VALUES (6,'Asistencia','2002-08-09 13:13:53','psaldarr','',20020809131408,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A5%3B%7D','page','',1,'');
INSERT INTO pages VALUES (7,'Introducción','2002-08-09 13:25:43','psaldarr','',20020809132614,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A6%3B%7D','page','',1,'');
INSERT INTO pages VALUES (8,'Los criterios para tus trabajos','2002-08-09 13:26:29','psaldarr','',20020809132650,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A7%3B%7D','page','',1,'');
INSERT INTO pages VALUES (9,'Guías para los trabajos escritos','2002-08-09 13:27:09','psaldarr','',20020809132720,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A8%3B%7D','page','',1,'');
INSERT INTO pages VALUES (10,'Introducción','2002-08-09 13:28:46','psaldarr','',20020809133001,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A9%3B%7D','page','',1,'');
INSERT INTO pages VALUES (52,'Download Syllabus','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A28%3B%7D','page','',1,'');
INSERT INTO pages VALUES (51,'View Syllabus','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A27%3B%7D','page','',1,'');
INSERT INTO pages VALUES (50,'Week 13','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (49,'Week 12','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (47,'Week 10','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (48,'Week 11','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (46,'Week 9','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (45,'Week 8','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (44,'Week 7','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (43,'Week 6','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (42,'Week 5','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (41,'Week 4','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (40,'Week 3','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (39,'Week 2','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (38,'Week 1','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A26%3B%7D','page','',1,'');
INSERT INTO pages VALUES (37,'Announcements','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','',1,'a%3A1%3A%7Bi%3A0%3Bi%3A25%3B%7D','page','',1,'week');
INSERT INTO pages VALUES (36,'Professor','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A24%3B%7D','page','',1,'');
INSERT INTO pages VALUES (35,'Grading','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A23%3B%7D','page','',1,'');
INSERT INTO pages VALUES (34,'Requirements','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A22%3B%7D','page','',1,'');
INSERT INTO pages VALUES (33,'Description','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A21%3B%7D','page','',1,'');
INSERT INTO pages VALUES (53,'Links','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','','','a%3A2%3A%7Bi%3A0%3Bi%3A29%3Bi%3A1%3Bi%3A30%3B%7D','page','',1,'');
INSERT INTO pages VALUES (54,'Topics','2002-08-09 14:10:23','gabe','',20020809141023,'0000-00-00','0000-00-00',1,'','','',1,'a%3A1%3A%7Bi%3A0%3Bi%3A31%3B%7D','page','',1,'none');
INSERT INTO pages VALUES (55,'Description','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A32%3B%7D','page','',1,'');
INSERT INTO pages VALUES (56,'Requirements','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A33%3B%7D','page','',1,'');
INSERT INTO pages VALUES (57,'Grading','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A34%3B%7D','page','',1,'');
INSERT INTO pages VALUES (58,'Professor','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A35%3B%7D','page','',1,'');
INSERT INTO pages VALUES (59,'Week 1','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A36%3B%7D','page','',1,'');
INSERT INTO pages VALUES (60,'Week 2','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (61,'Week 3','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (62,'Week 4','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (63,'Week 5','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (64,'Week 6','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (65,'Week 7','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (66,'Week 8','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (67,'Week 9','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (68,'Week 10','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (69,'Week 11','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (70,'Week 12','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (71,'Week 13','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (72,'View Syllabus','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A37%3B%7D','page','',1,'');
INSERT INTO pages VALUES (73,'Download Syllabus','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A38%3B%7D','page','',1,'');
INSERT INTO pages VALUES (74,'Links','2002-08-09 14:10:33','gabe','',20020809141033,'0000-00-00','0000-00-00',1,'','','','','a%3A2%3A%7Bi%3A0%3Bi%3A39%3Bi%3A1%3Bi%3A40%3B%7D','page','',1,'');
INSERT INTO pages VALUES (75,'Description','2002-08-09 14:10:39','gabe','',20020809141039,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A41%3B%7D','page','',1,'');
INSERT INTO pages VALUES (76,'Professor','2002-08-09 14:10:39','gabe','',20020809141039,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A42%3B%7D','page','',1,'');
INSERT INTO pages VALUES (77,'View Syllabus','2002-08-09 14:10:39','gabe','',20020809141039,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A43%3B%7D','page','',1,'');
INSERT INTO pages VALUES (78,'Download Syllabus','2002-08-09 14:10:39','gabe','',20020809141039,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A44%3B%7D','page','',1,'');
INSERT INTO pages VALUES (79,'Description','2002-08-09 14:10:46','gabe','gabe',20020809164602,'0000-00-00','0000-00-00',1,'','','','','a%3A3%3A%7Bi%3A0%3Bi%3A45%3Bi%3A1%3Bi%3A46%3Bi%3A2%3Bi%3A47%3B%7D','page','',1,'');
INSERT INTO pages VALUES (80,'Articles','2002-08-09 14:10:46','gabe','',20020809173309,'0000-00-00','0000-00-00',1,'','','',1,'a%3A3%3A%7Bi%3A0%3Bi%3A48%3Bi%3A1%3Bi%3A49%3Bi%3A2%3Bi%3A59%3B%7D','page','',1,'none');
INSERT INTO pages VALUES (81,'Links','2002-08-09 16:55:17','gabe','',20020809165747,'0000-00-00','0000-00-00',1,'','','','','a%3A5%3A%7Bi%3A0%3Bi%3A50%3Bi%3A1%3Bi%3A51%3Bi%3A2%3Bi%3A52%3Bi%3A3%3Bi%3A53%3Bi%3A4%3Bi%3A54%3B%7D','page','',1,'');
INSERT INTO pages VALUES (82,'Discussion','2002-08-09 17:02:12','gabe','gabe',20020809170737,'0000-00-00','0000-00-00',1,'','','',1,'a%3A3%3A%7Bi%3A0%3Bi%3A55%3Bi%3A1%3Bi%3A56%3Bi%3A2%3Bi%3A57%3B%7D','page','',1,'none');
INSERT INTO pages VALUES (83,'Collaboration','2002-08-09 17:02:20','gabe','gabe',20020814105143,'0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bs%3A7%3A%22achapin%22%3Ba%3A3%3A%7Bi%3A0%3Bi%3A1%3Bi%3A1%3Bi%3A1%3Bi%3A2%3Bi%3A0%3B%7Ds%3A7%3A%22afranco%22%3Ba%3A3%3A%7Bi%3A0%3Bi%3A1%3Bi%3A1%3Bi%3A1%3Bi%3A2%3Bi%3A0%3B%7D%7D',1,1,'a%3A3%3A%7Bi%3A0%3Bs%3A2%3A%2258%22%3Bi%3A1%3Bs%3A2%3A%2260%22%3Bi%3A2%3Bi%3A61%3B%7D','page','',1,'none');
INSERT INTO pages VALUES (84,'File Downloads','2002-08-09 18:01:18','gabe','',20020809180433,'0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bs%3A7%3A%22achapin%22%3Ba%3A3%3A%7Bi%3A0%3Bi%3A0%3Bi%3A1%3Bi%3A0%3Bi%3A2%3Bi%3A0%3B%7Ds%3A7%3A%22afranco%22%3Ba%3A3%3A%7Bi%3A0%3Bi%3A0%3Bi%3A1%3Bi%3A0%3Bi%3A2%3Bi%3A0%3B%7D%7D','','','a%3A3%3A%7Bi%3A0%3Bi%3A62%3Bi%3A1%3Bi%3A63%3Bi%3A2%3Bi%3A64%3B%7D','page','',1,'');
INSERT INTO pages VALUES (85,'View','2002-08-10 12:51:47','gabe','',20020810125147,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (86,'Download','2002-08-10 12:51:53','gabe','',20020810125635,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (87,'Description','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A66%3B%7D','page','',1,'');
INSERT INTO pages VALUES (88,'Requirements','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A67%3B%7D','page','',1,'');
INSERT INTO pages VALUES (89,'Grading','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A68%3B%7D','page','',1,'');
INSERT INTO pages VALUES (90,'Professor','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A69%3B%7D','page','',1,'');
INSERT INTO pages VALUES (91,'Week 1','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A70%3B%7D','page','',1,'');
INSERT INTO pages VALUES (92,'Week 2','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (93,'Week 3','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (94,'Week 4','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (95,'Week 5','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (96,'Week 6','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (97,'Week 7','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (98,'Week 8','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (99,'Week 9','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (100,'Week 10','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (101,'Week 11','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (102,'Week 12','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (103,'Week 13','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (104,'View Syllabus','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A71%3B%7D','page','',1,'');
INSERT INTO pages VALUES (105,'Download Syllabus','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A72%3B%7D','page','',1,'');
INSERT INTO pages VALUES (106,'Links','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00',1,'','','','','a%3A2%3A%7Bi%3A0%3Bi%3A73%3Bi%3A1%3Bi%3A74%3B%7D','page','',1,'');
INSERT INTO pages VALUES (164,'View Syllabus','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A115%3B%7D','page','',1,'');
INSERT INTO pages VALUES (165,'Download Syllabus','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A116%3B%7D','page','',1,'');
INSERT INTO pages VALUES (166,'Links','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A2%3A%7Bi%3A0%3Bi%3A117%3Bi%3A1%3Bi%3A118%3B%7D','page','',1,'');
INSERT INTO pages VALUES (158,'Week 8','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (159,'Week 9','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (160,'Week 10','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (161,'Week 11','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (162,'Week 12','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (163,'Week 13','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (147,'Description','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A110%3B%7D','page','',1,'');
INSERT INTO pages VALUES (148,'Requirements','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A111%3B%7D','page','',1,'');
INSERT INTO pages VALUES (149,'Grading','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A112%3B%7D','page','',1,'');
INSERT INTO pages VALUES (150,'Professor','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A113%3B%7D','page','',1,'');
INSERT INTO pages VALUES (151,'Week 1','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','a%3A1%3A%7Bi%3A0%3Bi%3A114%3B%7D','page','',1,'');
INSERT INTO pages VALUES (152,'Week 2','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (153,'Week 3','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (154,'Week 4','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (155,'Week 5','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (156,'Week 6','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
INSERT INTO pages VALUES (157,'Week 7','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'','','','','','page','',1,'');
# --------------------------------------------------------

#
# Table structure for table 'sections'
#

CREATE TABLE `sections` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `editedtimestamp` timestamp(14) NOT NULL,
  `addedby` varchar(100) NOT NULL default '',
  `editedby` varchar(100) NOT NULL default '',
  `addedtimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `activatedate` date NOT NULL default '0000-00-00',
  `deactivatedate` date NOT NULL default '0000-00-00',
  `active` tinyint(4) NOT NULL default '1',
  `permissions` blob NOT NULL,
  `pages` blob NOT NULL,
  `type` varchar(20) NOT NULL default '',
  `url` varchar(250) NOT NULL default '',
  `locked` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM COMMENT='site sections';

#
# Dumping data for table 'sections'
#

INSERT INTO sections VALUES (1,'Temario',20020809131353,'psaldarr','','2002-08-09 11:00:16','0000-00-00','0000-00-00',1,'','a%3A4%3A%7Bi%3A0%3Bi%3A1%3Bi%3A1%3Bs%3A1%3A%222%22%3Bi%3A2%3Bi%3A5%3Bi%3A3%3Bi%3A6%3B%7D','section','http://','');
INSERT INTO sections VALUES (2,'Exámenes y pruebas',20020809110426,'psaldarr','','2002-08-09 11:04:15','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A3%3B%7D','section','http://','');
INSERT INTO sections VALUES (3,'Gramática',20020809110626,'psaldarr','','2002-08-09 11:05:52','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A4%3B%7D','section','http://','');
INSERT INTO sections VALUES (4,'Trabajos e informes',20020809132709,'psaldarr','','2002-08-09 13:25:09','0000-00-00','0000-00-00',1,'','a%3A3%3A%7Bi%3A0%3Bi%3A7%3Bi%3A1%3Bi%3A8%3Bi%3A2%3Bi%3A9%3B%7D','section','http://','');
INSERT INTO sections VALUES (5,'Lecturas',20020809132746,'psaldarr','','2002-08-09 13:27:46','0000-00-00','0000-00-00',1,'','','section','http://','');
INSERT INTO sections VALUES (6,'Película',20020809132846,'psaldarr','','2002-08-09 13:28:09','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A10%3B%7D','section','http://','');
INSERT INTO sections VALUES (15,'Links',20020809141023,'gabe','','2002-08-09 14:10:23','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A53%3B%7D','section','http://','');
INSERT INTO sections VALUES (14,'Syllabus',20020809141023,'gabe','','2002-08-09 14:10:23','0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bi%3A0%3Bi%3A51%3Bi%3A1%3Bi%3A52%3B%7D','section','http://','');
INSERT INTO sections VALUES (13,'Assignments',20020809141023,'gabe','','2002-08-09 14:10:23','0000-00-00','0000-00-00',1,'','a%3A13%3A%7Bi%3A0%3Bi%3A38%3Bi%3A1%3Bi%3A39%3Bi%3A2%3Bi%3A40%3Bi%3A3%3Bi%3A41%3Bi%3A4%3Bi%3A42%3Bi%3A5%3Bi%3A43%3Bi%3A6%3Bi%3A44%3Bi%3A7%3Bi%3A45%3Bi%3A8%3Bi%3A46%3Bi%3A9%3Bi%3A47%3Bi%3A10%3Bi%3A48%3Bi%3A11%3Bi%3A49%3Bi%3A12%3Bi%3A50%3B%7D','section','http://','');
INSERT INTO sections VALUES (12,'Introduction',20020809141023,'gabe','','2002-08-09 14:10:23','0000-00-00','0000-00-00',1,'','a%3A5%3A%7Bi%3A0%3Bi%3A33%3Bi%3A1%3Bi%3A34%3Bi%3A2%3Bi%3A35%3Bi%3A3%3Bi%3A36%3Bi%3A4%3Bi%3A37%3B%7D','section','http://','');
INSERT INTO sections VALUES (16,'Discussions',20020809141023,'gabe','','2002-08-09 14:10:23','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A54%3B%7D','section','http://','');
INSERT INTO sections VALUES (17,'Introduction',20020809141033,'gabe','','2002-08-09 14:10:33','0000-00-00','0000-00-00',1,'','a%3A4%3A%7Bi%3A0%3Bi%3A55%3Bi%3A1%3Bi%3A56%3Bi%3A2%3Bi%3A57%3Bi%3A3%3Bi%3A58%3B%7D','section','http://','');
INSERT INTO sections VALUES (18,'Assignments',20020809141033,'gabe','','2002-08-09 14:10:33','0000-00-00','0000-00-00',1,'','a%3A13%3A%7Bi%3A0%3Bi%3A59%3Bi%3A1%3Bi%3A60%3Bi%3A2%3Bi%3A61%3Bi%3A3%3Bi%3A62%3Bi%3A4%3Bi%3A63%3Bi%3A5%3Bi%3A64%3Bi%3A6%3Bi%3A65%3Bi%3A7%3Bi%3A66%3Bi%3A8%3Bi%3A67%3Bi%3A9%3Bi%3A68%3Bi%3A10%3Bi%3A69%3Bi%3A11%3Bi%3A70%3Bi%3A12%3Bi%3A71%3B%7D','section','http://','');
INSERT INTO sections VALUES (19,'Syllabus',20020809141033,'gabe','','2002-08-09 14:10:33','0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bi%3A0%3Bi%3A72%3Bi%3A1%3Bi%3A73%3B%7D','section','http://','');
INSERT INTO sections VALUES (20,'Links',20020809141033,'gabe','','2002-08-09 14:10:33','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A74%3B%7D','section','http://','');
INSERT INTO sections VALUES (21,'Introduction',20020809141039,'gabe','','2002-08-09 14:10:39','0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bi%3A0%3Bi%3A75%3Bi%3A1%3Bi%3A76%3B%7D','section','http://','');
INSERT INTO sections VALUES (22,'Syllabus',20020809141039,'gabe','','2002-08-09 14:10:39','0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bi%3A0%3Bi%3A77%3Bi%3A1%3Bi%3A78%3B%7D','section','http://','');
INSERT INTO sections VALUES (23,'Introduction',20020809165517,'gabe','gabe','2002-08-09 14:10:46','0000-00-00','0000-00-00',1,'','a%3A3%3A%7Bi%3A0%3Bi%3A79%3Bi%3A1%3Bi%3A80%3Bi%3A2%3Bi%3A81%3B%7D','section','http://','');
INSERT INTO sections VALUES (24,'Advanced',20020809180118,'gabe','','2002-08-09 17:00:49','0000-00-00','0000-00-00',1,'','a%3A3%3A%7Bi%3A0%3Bi%3A82%3Bi%3A1%3Bi%3A83%3Bi%3A2%3Bi%3A84%3B%7D','section','http://','');
INSERT INTO sections VALUES (25,'Resume',20020810125153,'gabe','','2002-08-10 12:51:41','0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bi%3A0%3Bi%3A85%3Bi%3A1%3Bi%3A86%3B%7D','section','http://','');
INSERT INTO sections VALUES (26,'Introduction',20020810130138,'gschine','','2002-08-10 13:01:38','0000-00-00','0000-00-00',1,'','a%3A4%3A%7Bi%3A0%3Bi%3A87%3Bi%3A1%3Bi%3A88%3Bi%3A2%3Bi%3A89%3Bi%3A3%3Bi%3A90%3B%7D','section','http://','');
INSERT INTO sections VALUES (27,'Assignments',20020810130138,'gschine','','2002-08-10 13:01:38','0000-00-00','0000-00-00',1,'','a%3A13%3A%7Bi%3A0%3Bi%3A91%3Bi%3A1%3Bi%3A92%3Bi%3A2%3Bi%3A93%3Bi%3A3%3Bi%3A94%3Bi%3A4%3Bi%3A95%3Bi%3A5%3Bi%3A96%3Bi%3A6%3Bi%3A97%3Bi%3A7%3Bi%3A98%3Bi%3A8%3Bi%3A99%3Bi%3A9%3Bi%3A100%3Bi%3A10%3Bi%3A101%3Bi%3A11%3Bi%3A102%3Bi%3A12%3Bi%3A103%3B%7D','section','http://','');
INSERT INTO sections VALUES (28,'Syllabus',20020810130138,'gschine','','2002-08-10 13:01:38','0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bi%3A0%3Bi%3A104%3Bi%3A1%3Bi%3A105%3B%7D','section','http://','');
INSERT INTO sections VALUES (29,'Links',20020810130138,'gschine','','2002-08-10 13:01:38','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A106%3B%7D','section','http://','');
INSERT INTO sections VALUES (46,'Links',20020816135526,'jschine','','2002-08-16 13:55:26','0000-00-00','0000-00-00',1,'','a%3A1%3A%7Bi%3A0%3Bi%3A166%3B%7D','section','http://','');
INSERT INTO sections VALUES (45,'Syllabus',20020816135526,'jschine','','2002-08-16 13:55:26','0000-00-00','0000-00-00',1,'','a%3A2%3A%7Bi%3A0%3Bi%3A164%3Bi%3A1%3Bi%3A165%3B%7D','section','http://','');
INSERT INTO sections VALUES (43,'Introduction',20020816135526,'jschine','','2002-08-16 13:55:26','0000-00-00','0000-00-00',1,'','a%3A4%3A%7Bi%3A0%3Bi%3A147%3Bi%3A1%3Bi%3A148%3Bi%3A2%3Bi%3A149%3Bi%3A3%3Bi%3A150%3B%7D','section','http://','');
INSERT INTO sections VALUES (44,'Assignments',20020816135526,'jschine','','2002-08-16 13:55:26','0000-00-00','0000-00-00',1,'','a%3A13%3A%7Bi%3A0%3Bi%3A151%3Bi%3A1%3Bi%3A152%3Bi%3A2%3Bi%3A153%3Bi%3A3%3Bi%3A154%3Bi%3A4%3Bi%3A155%3Bi%3A5%3Bi%3A156%3Bi%3A6%3Bi%3A157%3Bi%3A7%3Bi%3A158%3Bi%3A8%3Bi%3A159%3Bi%3A9%3Bi%3A160%3Bi%3A10%3Bi%3A161%3Bi%3A11%3Bi%3A162%3Bi%3A12%3Bi%3A163%3B%7D','section','http://','');
# --------------------------------------------------------

#
# Table structure for table 'sites'
#

CREATE TABLE `sites` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `title` varchar(250) NOT NULL default '',
  `theme` varchar(100) NOT NULL default '',
  `themesettings` blob NOT NULL,
  `header` blob NOT NULL,
  `footer` blob NOT NULL,
  `editors` blob NOT NULL,
  `permissions` blob NOT NULL,
  `viewpermissions` varchar(100) NOT NULL default '',
  `addedtimestamp` datetime default NULL,
  `addedby` varchar(200) NOT NULL default '',
  `editedby` varchar(200) NOT NULL default '',
  `editedtimestamp` timestamp(14) NOT NULL,
  `activatedate` date NOT NULL default '0000-00-00',
  `deactivatedate` date NOT NULL default '0000-00-00',
  `active` tinyint(4) NOT NULL default '1',
  `sections` blob NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM;

#
# Dumping data for table 'sites'
#

INSERT INTO sites VALUES (2,'sp210d-f02','Intermediate Spanish I','shadowbox','','%3Cimg+src%3D%5C%22userfiles%2Fsp210d-f02%2F210header.jpg%5C%22%3E+','','','','anyone','2002-08-09 11:00:02','psaldarr','psaldarr',20020814095824,'0000-00-00','0000-00-00',1,'a%3A6%3A%7Bi%3A0%3Bi%3A1%3Bi%3A1%3Bi%3A2%3Bi%3A2%3Bs%3A1%3A%224%22%3Bi%3A3%3Bi%3A3%3Bi%3A4%3Bi%3A5%3Bi%3A5%3Bi%3A6%3B%7D');
INSERT INTO sites VALUES (4,'template1','Template #1','minimal','','','','','','anyone','2002-08-09 14:10:23','gabe','gabe',20020809164406,'0000-00-00','0000-00-00',1,'a%3A5%3A%7Bi%3A0%3Bi%3A12%3Bi%3A1%3Bi%3A13%3Bi%3A2%3Bi%3A14%3Bi%3A3%3Bi%3A15%3Bi%3A4%3Bi%3A16%3B%7D');
INSERT INTO sites VALUES (5,'template2','Template #2','default','','','','','','anyone','2002-08-09 14:10:33','gabe','gabe',20020809164411,'0000-00-00','0000-00-00',1,'a%3A4%3A%7Bi%3A0%3Bi%3A17%3Bi%3A1%3Bi%3A18%3Bi%3A2%3Bi%3A19%3Bi%3A3%3Bi%3A20%3B%7D');
INSERT INTO sites VALUES (6,'template3','Template #3','default','','','','','','anyone','2002-08-09 14:10:39','gabe','gabe',20020809164419,'0000-00-00','0000-00-00',1,'a%3A2%3A%7Bi%3A0%3Bi%3A21%3Bi%3A1%3Bi%3A22%3B%7D');
INSERT INTO sites VALUES (7,'sample','SitesDB Sample Site','minimal','a%3A2%3A%7Bs%3A5%3A%22theme%22%3Bs%3A7%3A%22minimal%22%3Bs%3A11%3A%22colorscheme%22%3Bs%3A5%3A%22olive%22%3B%7D','%3Cdiv+style%3D%5C%27background-color%3A+%23ddd%3B+padding%3A10px%5C%27+align%3Dcenter%3E%3Cb%3EHeader+Space%3C%2Fb%3E%3CBR%3E%0D%0AThis+space+can+contain+any+information+you+enter%2C+like+banner+pictures%2C+links+%28like+one+to+%3Ca+href%3D%5C%27http%3A%2F%2Fwww.middlebury.edu%2F%5C%27+target%3D%5C%27_blank%5C%27%3EMiddlebury+College%3C%2Fa%3E%29%2C+or+general+info.%0D%0A%3C%2Fdiv%3E','%3Cdiv+style%3D%5C%27background-color%3A+%23ddd%3B+padding%3A10px%5C%27+align%3Dcenter%3E%3Cb%3EFooter+Space%3C%2Fb%3E%3CBR%3E%0D%0AAgain%2C+enter+anything+you+like.%0D%0A%3C%2Fdiv%3E','achapin,afranco','a%3A2%3A%7Bs%3A7%3A%22achapin%22%3Ba%3A3%3A%7Bi%3A0%3Bi%3A0%3Bi%3A1%3Bi%3A0%3Bi%3A2%3Bi%3A0%3B%7Ds%3A7%3A%22afranco%22%3Ba%3A3%3A%7Bi%3A0%3Bi%3A0%3Bi%3A1%3Bi%3A0%3Bi%3A2%3Bi%3A0%3B%7D%7D','anyone','2002-08-09 14:10:46','gabe','gabe',20020809171802,'0000-00-00','0000-00-00',1,'a%3A2%3A%7Bi%3A0%3Bi%3A23%3Bi%3A1%3Bi%3A24%3B%7D');
INSERT INTO sites VALUES (8,'gabe','Gabe Schine','shadowbox','a%3A2%3A%7Bs%3A5%3A%22theme%22%3Bs%3A9%3A%22shadowbox%22%3Bs%3A11%3A%22colorscheme%22%3Bs%3A9%3A%22lightblue%22%3B%7D','','','','','anyone','2002-08-10 12:51:18','gabe','gabe',20020810130041,'0000-00-00','0000-00-00','','a%3A1%3A%7Bi%3A0%3Bi%3A25%3B%7D');
INSERT INTO sites VALUES (9,'gschine','test','minimal','','','','','','anyone','2002-08-10 13:01:38','gschine','',20020810130138,'0000-00-00','0000-00-00','','a%3A4%3A%7Bi%3A0%3Bi%3A26%3Bi%3A1%3Bi%3A27%3Bi%3A2%3Bi%3A28%3Bi%3A3%3Bi%3A29%3B%7D');
INSERT INTO sites VALUES (15,'jschine','Joe\'s Great site','minimal','','Yo+this+is+cool','','','','anyone','2002-08-16 13:55:26','jschine','',20020816135526,'0000-00-00','0000-00-00',1,'a%3A4%3A%7Bi%3A0%3Bi%3A43%3Bi%3A1%3Bi%3A44%3Bi%3A2%3Bi%3A45%3Bi%3A3%3Bi%3A46%3B%7D');
# --------------------------------------------------------

#
# Table structure for table 'stories'
#

CREATE TABLE `stories` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `addedby` varchar(100) NOT NULL default '',
  `addedtimestamp` datetime default NULL,
  `editedby` varchar(100) NOT NULL default '',
  `editedtimestamp` timestamp(14) NOT NULL,
  `shorttext` longblob NOT NULL,
  `longertext` longblob NOT NULL,
  `permissions` blob NOT NULL,
  `activatedate` date NOT NULL default '0000-00-00',
  `deactivatedate` date NOT NULL default '0000-00-00',
  `discuss` tinyint(4) NOT NULL default '0',
  `discusspermissions` varchar(100) NOT NULL default '',
  `locked` tinyint(4) NOT NULL default '0',
  `category` varchar(200) NOT NULL default '',
  `discussions` blob NOT NULL,
  `texttype` varchar(20) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM;

#
# Dumping data for table 'stories'
#

INSERT INTO stories VALUES (1,'','psaldarr','2002-08-09 11:01:17','',20020809110117,'Prof.+Patricia+Saldarriaga%0D%0AHillcrest+22%2C+extension+x3258%0D%0AHoras+de+asesor%C3%ADa%3A+%0D%0AMartes%3A+2%3A00+-+3%3A00%0D%0AMi%C3%A9rcoles%3A+2%3A00+-+4%3A00%0D%0Ay+por+cita%0D%0AE-mail%3A+%0D%0Apsaldarr%40middlebury.edu','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (2,'','psaldarr','2002-08-09 11:01:50','',20020809110150,'1.+Rusch%2C+Debbie%2C+Marcela+Dom%C3%ADnguez+y+Luc%C3%ADa+Caycedo+Garner.+Fuentes%3A+conversaci%C3%B3n+y+gram%C3%A1tica.+2da+ed.+Boston%3A+Houghton+Mifflin+Company%2C+2000+%28FCG%29+%0D%0A%0D%0A2.+Fuentes%3A+Activities+Manual.+2da+ed.+Boston%3A+Houghton+Mifflin+Company%2C+2000.+%28FAM%29%0D%0A%0D%0A3.-+Esquivel%2C+Laura.+Como+agua+para+chocolate.+New+York%3A+Anchor+Books%2C+1994.+%28CAPC%29+%0D%0A','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (3,'','psaldarr','2002-08-09 11:06:57','',20020809110657,'%3Ci%3ELa+gram%C3%A1tica%3C%2Fi%3E%0D%0A%0D%0A%C2%BFPor+qu%C3%A9+es+importante+la+gram%C3%A1tica%3F+Para+hablar+correctamente+un+idioma+deber%C3%A1s+dominar+ciertas+reglas+generales.+A+lo+largo+del+semestre+revisaremos+algunos+cap%C3%ADtulos+importantes+como+por+ejemplo%3A+el+uso+de+ser+y+estar%2C+el+pret%C3%A9rito+vs.+el+imperfecto%2C+el+uso+de+los+adjetivos%2C+los+pronombres+de+objeto+directo+e+indirecto%2C+etc.%0D%0A%0D%0AEn+esta+secci%C3%B3n+encontrar%C3%A1s+teor%C3%ADa+y+pr%C3%A1ctica+para+complementar+tu+libro.+Por+favor+lee+con+atenci%C3%B3n+las+explicaciones+y+haz+los+ejercicios+respectivos+para+cada+secci%C3%B3n.+No+mires+las+respuestas%2C+sino+hasta+despu%C3%A9s+de+que+tengas+todo+hecho.%0D%0A%0D%0ALas+explicaciones+sobre+la+acentuaci%C3%B3n+te+ayudar%C3%A1n+a+mejorar+tu+ortograf%C3%ADa.+Una+escritura+sin+acentos+le+quita+peso+a+tus+ideas+puesto+que+el+lector+tendr%C3%A1+dificultades+para+seguirlas.+Por+el+contrario%2C+un+ensayo+con+las+tildes+necesarias+da+una+buena+impresi%C3%B3n+y+puede+ayudar+a+que+el+lector+mantenga+el+inter%C3%A9s+en+tu+escrito.%0D%0ASer%C3%ADa+interesante+que+una+vez+que+aprendieras+las+reglas+revisaras+un+par+de+art%C3%ADculos+en+los+peri%C3%B3dicos.+A+ver+si+encuentras+algunas+fallas+de+acentuaci%C3%B3n.+Si+lo+haces%2C+mu%C3%A9stramelas.%0D%0A%0D%0AEn+tu+libro+no+tienes+explicaciones+respecto+de+los+art%C3%ADculos.+Revisa+la+secci%C3%B3n+y+haz+los+ejercicios+correspondientes.+Hay+grandes+diferencias+en+comparaci%C3%B3n+con+el+ingl%C3%A9s.+Debes+diferenciar+entre+el+art%C3%ADculo+definido+y+el+indefinido+%28the%2C+a%29.+%0D%0A%0D%0ALa+secci%C3%B3n+de+%5C%22ser+vs.+estar%5C%22+te+ayudar%C3%A1+en+la+medida+que+podr%C3%A1s+hacer+los+ejercicios.+Comprueba+las+respuestas+despu%C3%A9s+de+haber+escrito+las+tuyas+propias.%0D%0A','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (4,'','psaldarr','2002-08-09 13:13:37','psaldarr',20020809131442,'1-+Mejoramiento+del+dominio+del+espa%C3%B1ol+en+las+cuatro+habilidades%3A+lectura%2C+escritura%2C+comprensi%C3%B3n+y+conversaci%C3%B3n.+%0D%0A2-+Consolidaci%C3%B3n+del+conocimiento+de+la+gram%C3%A1tica+espa%C3%B1ola.+%0D%0A3-+Ampliaci%C3%B3n+del+conocimiento+de+la+cultura+del+mundo+hisp%C3%A1nico.+%0D%0A','','','0000-00-00','0000-00-00','','anyone','','','','text','story','');
INSERT INTO stories VALUES (5,'','psaldarr','2002-08-09 13:14:08','',20020809131408,'La+asistencia+a+clase+es+obligatoria.+A+lo+largo+del+curso+s%C3%B3lo+se+aceptar%C3%A1n+3+%28tres%29+ausencias.+A+partir+de+estas+ausencias+se%C3%B1aladas%2C+se+bajar%C3%A1+la+nota+final+por+un+nivel%2C+por+ejemplo%2C+de+B+%2B+a+B.+En+caso+de+enfermedad+seria%2C+el+estudiante+debe+ir+a+la+oficina+del+Decano+de+Estudiantes+para+obtener+una+ausencia+%5C%22justificada%5C%22+%28justified%29.+La+ausencia+%5C%22excusada%5C%22+%28excused%29+no+sirve+para+explicar+la+ausencia.+Es+responsabilidad+del+estudiante+avisar+de+la+ausencia+con+antelaci%C3%B3n+y+planear+las+actividades+alternativas+con+el+profesor.+%0D%0A','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (6,'','psaldarr','2002-08-09 13:26:14','',20020809132614,'Lee+con+atenci%C3%B3n+las+fechas+y+los+temas+para+los+informes+orales+y+las+escenificaciones.+Recuerda+que+te+debes+inscribir+para+un+m%C3%ADnimo+de+%281%29+informe+y+%281%29+escenificaci%C3%B3n.+Si+no+recuerdas+la+fecha+para+la+que+te+inscribiste%2C+haz+click+en+%5C%22participantes%5C%22+a+la+izquierda+de+este+p%C3%A1rrafo+y+escoge+la+secci%C3%B3n+en+la+que+est%C3%A1s+matriculado.%0D%0APara+una+preparaci%C3%B3n+%C3%B3ptima+en+tus+informes%2C+junta+tu+bibliograf%C3%ADa+con+la+suficiente+anticipaci%C3%B3n%2C+selecciona+la+informaci%C3%B3n+m%C3%A1s+importante+y+haz+un+resumen+que+sea+interesante+para+todos+tus+compa%C3%B1eros.%0D%0ALas+escenificaciones+se+basan+en+un+cap%C3%ADtulo+espec%C3%ADfico+de+la+novela+que+leeremos+durante+el+semestre.+Aseg%C3%BArate+de+leer+el+texto+y+entenderlo+antes+de+preparar+la+escenificaci%C3%B3n.%0D%0APara+la+descripci%C3%B3n+de+los+trabajos+escritos+anda+al+SERVAL+y+revisa+las+gu%C3%ADas+de+lectura%2C+as%C3%AD+como+los+glosarios+correspondientes+a+cada+cap%C3%ADtulo.%0D%0A','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (7,'','psaldarr','2002-08-09 13:26:50','',20020809132650,'Para+los+ponentes%0D%0AC%C3%B3mo+hacer+tu+presentaci%C3%B3n+oral%0D%0ACuando+hagas+tu+informe%2C+ten+en+cuenta+lo+siguientes+puntos.+Recuerda+que+la+nota+se+basar%C3%A1+en+estos+criterios%3A%0D%0A%0D%0A1.-+Refi%C3%A9rete+a+tus+notas%2C+pero+no+las+leas.%0D%0A2.-+Habla+con+claridad.+Es+importante+que+tus+compa%C3%B1eros+oigan+y+entiendan+todas+tus+palabras.%0D%0A3.-+Evita+palabras+o+expresiones+de+relleno+%28fumble+words%29+como%3A+%5C%22hmm%2C+ah%2C+eh%2C+este%2C+este%2C+aj%C3%A1%2C+aj%C3%A1%2C+etc.%0D%0A4.-+Si+olvidas+la+palabra+exacta+para+expresar+algo+en+espa%C3%B1ol%2C+describe+lo+que+quieres+decir+%28circumlocute%29%2C+o+simplemente+usa+palabras+equivalentes+para+expresar+la+misma+idea.+No+uses+ingl%C3%A9s.+Esto+te+evitar%C3%A1+hacer+preguntas+tipo%3A+%5C%22%C2%BFC%C3%B3mo+se+dice+circunspect%3F%0D%0A5.-+No+uses+m%C3%A1s+tiempo+del+que+se+te+ha+se%C3%B1alado+para+tu+presentaci%C3%B3n+%2815+minutos+por+grupo+de+3+estudiantes%29%0D%0A6.-+Cuando+termines%2C+espera+las+preguntas+de+tus+oyentes.%0D%0A%0D%0APara+las+escenificaciones%0D%0AEn+principio+rigen+los+mismos+criterios+que+para+las+presentaciones+orales.+Se+aprecia+sentido+del+humor%2C+material+adicional+para+la+escenificaci%C3%B3n+%28props%29+y+se+calificar%C3%A1+la+interpretaci%C3%B3n+de+la+escena+propuesta.+En+otras+palabras+utilicen+su+creatividad.%0D%0A%0D%0APara+los+oyentes%0D%0APresta+atenci%C3%B3n+a+la+presentaci%C3%B3n+de+tus+compa%C3%B1eros+y+escribe+lo+siguiente%3A%0D%0A%0D%0A1.-+Un+dato+o+hecho+de+la+presentaci%C3%B3n+que+m%C3%A1s+te+gust%C3%B3+de+la+charla.%0D%0A2.-+Una+pregunta+que+le+quieres+hacer+a+tus+compa%C3%B1eros+sobre+lo+que+han+presentado%0D%0A3.-+%C2%BFQu%C3%A9+nota+le+dar%C3%ADas%3F+Basa+tu+juicio+en+el+contenido+del+informe+y+en+los+criterios+para+juzgar+una+presentaci%C3%B3n+%28%5C%22C%C3%B3mo+hacer+una+presentaci%C3%B3n+en+clase%5C%22%29%0D%0A4.-+Un+consejo+pr%C3%A1ctico+para+mejorar+la+pr%C3%B3xima+presentaci%C3%B3n.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (8,'','psaldarr','2002-08-09 13:27:20','',20020809132720,'Atenci%C3%B3n%3A+las+gu%C3%ADas+para+los+diarios+las+puedes+encontrar+en+las+gu%C3%ADas+de+lectura.+A+continuaci%C3%B3n+tendr%C3%A1s+ayudas+para+las+composiciones.%0D%0A%0D%0APrimera+composici%C3%B3n.+%0D%0AExtensi%C3%B3n%3A+La+primera+composici%C3%B3n+debe+ser+de+unas+dos+p%C3%A1ginas.%0D%0ATemas+posibles%3A+1%29+%C2%BFqu%C3%A9+ocupaci%C3%B3n+piensas+practicar+despu%C3%A9s+de+tu+graduaci%C3%B3n+y+por+qu%C3%A9%3F+%0D%0A2%29+la+importancia+de+la+ropa+y+de+la+apariencia+f%C3%ADsica+para+tener+%C3%A9xito+en+la+vida.+%0D%0A%0D%0APresenta+tus+ideas+de+manera+organizada.+Escribe+primero+un+p%C3%A1rrafo+introductorio.+Por+ejemplo%2C+si+vas+a+escribir+sobre+el+primer+tema%2C+tu+p%C3%A1rrafo+introductorio+debe+decir+la+ocupaci%C3%B3n+que+piensas+seguir+despu%C3%A9s+de+tu+graduaci%C3%B3n.+Luego+defines+esa+ocupaci%C3%B3n%3B+si+es+algo+complicado--por+ejemplo+si+quieres+ser+pediatra%2C+debes+explicar+que+quieres+ser+un+doctor+de+ni%C3%B1os%2C+luego+hablar+de+la+edad+de+los+ni%C3%B1os+que+quieres+curar%2C+etc.+%0D%0AEn+el+segundo+p%C3%A1rrafo+dices+las+razones+por+las+que+quieres+ser+pediatra--que+tu+madre+es+pediatra%2C+por+ejemplo%2C+o+que+te+gusta+trabajar+con+ni%C3%B1os%2C+o+que+ya+trabajaste+en+un+hospital+hace+un+verano.+%0D%0AUsa+los+verbos+en+el+pret%C3%A9rito+que+has+aprendido.+Puedes+escribir+otro+p%C3%A1rrafo%2C+usando+el+presente+y+el+pret%C3%A9rito%2C+en+el+que+describas+la+preparaci%C3%B3n+que+has+tenido+y+que+est%C3%A1s+recibiendo+ahora+para+ser+m%C3%A9dico--por+ejemplo%2C%5C%22+estudi%C3%A9+qu%C3%ADmica+org%C3%A1nica+el+semestre+pasado+y+ahora+estudio+biolog%C3%ADa%2C%5C%22+y+puedes+describir+algo+de+esas+clases+y+de+la+importancia+de+la+misma+en+tus+planes.+%0D%0AEl+%C3%BAltimo+p%C3%A1rrafo+puede+ser+sobre+lo+que+vas+a+hacer+en+el+futuro+y+debes+usar+ir+a%2Binfinitivo%2C+me+gustar%C3%ADa%2Binfinitivo%2C+quisiera%2Binfinitivo.+Cada+p%C3%A1rrafo+debe+tener+al+menos+cinco+oraciones+y+usar+el+vocabulario+que+hemos+estudiado+hasta+ahora.+Escribe+tu+ensayo+en+la+computadora+y+deja+un+espacio+en+blanco+entre+cada+dos+l%C3%ADneas+de+texto.+Dale+un+t%C3%ADtulo+a+tu+ensayo+y+pon+tu+nombre.+Si+tienes+preguntas%2C+puedes+escribirme.+Buena+suerte.+%0D%0A%0D%0A%0D%0A','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (9,'','psaldarr','2002-08-09 13:30:01','',20020809133001,'Para+hacer+los+ejercicios+de+la+pel%C3%ADcula%2C+haz+click+en+el+enlace+a+continuaci%C3%B3n.+No+olvides+leer+con+atenci%C3%B3n+todas+las+instrucciones+antes+de+trabajar+en+el+v%C3%ADdeo+clip.+Te+recomiendo+que+siempre+leas+el+texto+con+antelaci%C3%B3n.+%C2%A1Que+te+diviertas%21+%0D%0A%0D%0A%3Ca+href%3D%5C%22http%3A%2F%2Fet.middlebury.edu%2Fmots%5C%22%3EComo+agua+para+chocolat%3C%2Fa%3E%0D%0A%0D%0AAtenci%C3%B3n%3A+debes+mandar+tus+respuestas+a+tu+profesor+o+profesora.+Adem%C3%A1s+de+los+ejercicios+para+rellenar+tienes+actividades+para+la+comprensi%C3%B3n.+Haz+todos+los+ejercicios+disponibles.+Te+ser%C3%A1n+sumamente+%C3%BAtiles.%0D%0A%0D%0AOJO%3A+todos+juntos+veremos+la+pel%C3%ADcula+completa%0D%0ACu%C3%A1ndo%3A+el+d%C3%ADa+martes+ocho+de+mayo%0D%0AD%C3%B3nde%3A+en+BIH+216%0D%0AA+qu%C3%A9+hora%3A+a+las+7%3A30+pm+%0D%0A%0D%0A%0D%0A%C2%A1Por+favor+asistan+todos%21','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (31,'','gabe','2002-08-09 14:10:23','',20020809141023,'Add+here+any+topics+you+wish+to+be+discussed.+Notice+how+the+date%2Ftime+the+content+was+added+is+displayed+beneath+it.%0D%0A%0D%0ATo+enable+discussion+on+a+certain+Text+Block%2C+click+on+%5C%27%2B+add+content%5C%27+below%2C+and+click+the+%3Cb%3ESwitch+to+Advanced+View%3C%2Fb%3E+button.+Then+scroll+down+to+%5C%22Discussion%5C%22+and+check+the+enable+discussion+checkbox.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (30,'Middlebury Libraries','gabe','2002-08-09 14:10:23','',20020809141023,'This+website+allows+you+to+search+the+Middlebury+Library+catalogue.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (29,'Middlebury College','gabe','2002-08-09 14:10:23','',20020809141023,'This+is+Middlebury+College%5C%27s+website.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (28,'','gabe','2002-08-09 14:10:23','',20020809141023,'To+make+your+syllabus+avialable+for+download%2C+click+the+%5C%22add+content%5C%22+button+and+choose+%5C%22File+for+Download%5C%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%5C%22Add%5C%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+Microsoft+Word+and+RTF.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (27,'','gabe','2002-08-09 14:10:23','',20020809141023,'Add+here+a+text+version+of+your+syllabus.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (26,'','gabe','2002-08-09 14:10:23','',20020809141023,'For+this+page%2C+add+the+first+week%5C%27s+assignments.+Do+the+same+for+the+subsequent+weeks+on+their+respective+pages.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (25,'','gabe','2002-08-09 14:10:23','',20020809141023,'Add+here+any+announcements+you+have+for+visitors+to+your+site.+Notice+how+all+content+displays+the+date+it+was+added%2Fedited+underneath+it.%0D%0A%0D%0AAlso+notice+how+the+content+on+this+page+is+archived+by+week+%28meaning+the+last+7+days+of+content+are+displayed+by+default%29.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (21,'','gabe','2002-08-09 14:10:23','',20020809141023,'Add+here+a+description+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (22,'','gabe','2002-08-09 14:10:23','',20020809141023,'Add+here+content+such+as+requirements+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (23,'','gabe','2002-08-09 14:10:23','',20020809141023,'Add+here+grading+policies+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (24,'','gabe','2002-08-09 14:10:23','',20020809141023,'Add+here+information+about+you%2C+the+professor.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (32,'','gabe','2002-08-09 14:10:33','',20020809141033,'Add+here+a+description+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (33,'','gabe','2002-08-09 14:10:33','',20020809141033,'Add+here+content+such+as+requirements+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (34,'','gabe','2002-08-09 14:10:33','',20020809141033,'Add+here+grading+policies+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (35,'','gabe','2002-08-09 14:10:33','',20020809141033,'Add+here+information+about+you%2C+the+professor.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (36,'','gabe','2002-08-09 14:10:33','',20020809141033,'For+this+page%2C+add+the+first+week%5C%27s+assignments.+Do+the+same+for+the+subsequent+weeks+on+their+respective+pages.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (37,'','gabe','2002-08-09 14:10:33','',20020809141033,'Add+here+a+text+version+of+your+syllabus.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (38,'','gabe','2002-08-09 14:10:33','',20020809141033,'To+make+your+syllabus+avialable+for+download%2C+click+the+%5C%22add+content%5C%22+button+and+choose+%5C%22File+for+Download%5C%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%5C%22Add%5C%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+Microsoft+Word+and+RTF.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (39,'Middlebury College','gabe','2002-08-09 14:10:33','',20020809141033,'This+is+Middlebury+College%5C%27s+website.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (40,'Middlebury Libraries','gabe','2002-08-09 14:10:33','',20020809141033,'This+website+allows+you+to+search+the+Middlebury+Library+catalogue.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (41,'','gabe','2002-08-09 14:10:39','',20020809141039,'Add+here+a+description+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (42,'','gabe','2002-08-09 14:10:39','',20020809141039,'Put+here+information+about+you%2C+the+professor.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (43,'','gabe','2002-08-09 14:10:39','',20020809141039,'Add+here+a+text+version+of+your+syllabus.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (44,'','gabe','2002-08-09 14:10:39','',20020809141039,'To+make+your+syllabus+avialable+for+download%2C+click+the+%5C%22add+content%5C%22+button+and+choose+%5C%22File+for+Download%5C%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%5C%22Add%5C%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+Microsoft+Word+and+RTF.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (45,'General Info','gabe','2002-08-09 14:10:46','gabe',20020809165248,'In+%3Cb%3ESitesDB%3C%2Fb%3E+you+can+add+sections+%28above%2C+like+this+one%2C+%3Ci%3EIntroduction%3C%2Fi%3E%29.+Each+section+contains+one+or+multiple+pages+%28on+the+left%2C+%3Ci%3EDescription%3C%2Fi%3E+for+example%29.+On+every+page%2C+you+can+add+content+%28like+this%29.+Content+can+range+from+plain+text+to+images+to+files+to%2C+well%2C+whatever+you+want.+Pages+can+contain+as+many+text+blocks+%28entities+with+an+optional+title%2C+content%2C+and+optional+discussions%29+as+you+want.%0D%0A%0D%0AHere+are+some+ideas+for+using+this+space%3A%0D%0A%3Cul%3E%0D%0A%3Cli%3EClass+assignments%3Cli%3EA+resume%3Cli%3ESchedules%3Cli%3EWeekly+archived+articles%3Cli%3EDiscussion+topics%0D%0A%3C%2Ful%3E','','','0000-00-00','0000-00-00','','anyone','','','','text','story','');
INSERT INTO stories VALUES (46,'Another Example','gabe','2002-08-09 14:10:46','gabe',20020809165328,'This+is+yet+another+example+of+a+text+block.+This+text+block+has+a+title%2C+%5C%22Another+Example%5C%22.+The+story+below+does+not+have+a+title+associated+with+it.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','');
INSERT INTO stories VALUES (47,'','gabe','2002-08-09 14:10:46','gabe',20020809165338,'This+text+block+does+not+have+an+associated+title.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','');
INSERT INTO stories VALUES (48,'Journal Article Excerpts','gabe','2002-08-09 14:10:46','gabe',20020809165436,'Below+are+a+few+examples+of+creating+text+blocks+with+an+abridged+version+of+the+content+and+an+associated+full+content.+These+are+excerpts+from+journals%2Fnews+taken+from+the+web+on+July+19th%2C+2002.+The+full+content+for+the+article+is+not+the+full+article+taken+from+the+web%2C+though.+Links+are+provided+to+the+journal%5C%27s+site.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','');
INSERT INTO stories VALUES (49,'A Space in Time','gabe','2002-08-09 14:10:46','',20020809141046,'In+the+evenings%2C+when+my+particular+piece+of+Earth+has+turned+away+from+the+Sun%2C+and+is+exposed+instead+to+the+rest+of+the+cosmos%2C+I+sit+in+front+of+a+keyboard%2C+log+on%2C+and+seek+out+the+windows+that+look+down+at+the+planets+and+out+at+the+stars.+It%5C%27s+a+markedly+different+experience+from+looking+at+reproductions+on+paper.+What+I+see+is+closer+to+the+source.+In+fact%2C+it%5C%27s+indistinguishable+from+the+source.+These+are+images+that+have+never+registered+on+a+negative.+Like+the+Internet+itself%2C+they+are+products+of+a+digitized+era.+Over+the+past+couple+of+years+I%5C%27ve+been+monitoring+the+long+rectangular+strips+of+Martian+surface+being+beamed+across+the+void%2C+in+a+steady+stream+of+zeroes+and+ones%2C+from+the+umbrella-shaped+high-gain+antenna+of+the+Mars+Global+Surveyor+spacecraft.+These+pictures+are+so+fresh+that+their+immediacy+practically+crackles.+Call+it+%5C%22chrono-clarity.%5C%22+That+bluish+wispy+cloud...','In+the+evenings%2C+when+my+particular+piece+of+Earth+has+turned+away+from+the+Sun%2C+and+is+exposed+instead+to+the+rest+of+the+cosmos%2C+I+sit+in+front+of+a+keyboard%2C+log+on%2C+and+seek+out+the+windows+that+look+down+at+the+planets+and+out+at+the+stars.+It%5C%27s+a+markedly+different+experience+from+looking+at+reproductions+on+paper.+What+I+see+is+closer+to+the+source.+In+fact%2C+it%5C%27s+indistinguishable+from+the+source.+These+are+images+that+have+never+registered+on+a+negative.+Like+the+Internet+itself%2C+they+are+products+of+a+digitized+era.+Over+the+past+couple+of+years+I%5C%27ve+been+monitoring+the+long+rectangular+strips+of+Martian+surface+being+beamed+across+the+void%2C+in+a+steady+stream+of+zeroes+and+ones%2C+from+the+umbrella-shaped+high-gain+antenna+of+the+Mars+Global+Surveyor+spacecraft.+These+pictures+are+so+fresh+that+their+immediacy+practically+crackles.+Call+it+%5C%22chrono-clarity.%5C%22+That+bluish+wispy+cloud%2C+for+example%2C+hovering+over+the+Hecates+Tholus+volcano%2C+which+rears+above+the+pockmarked+surface+of+the+Elysium+Volcanic+Region+in+the+Martian+eastern+hemisphere%E2%80%94it+has+barely+had+time+to+disperse+before+I%2C+or+anyone+with+Internet+access%2C+can+see+it+in+all+its+spooky+beauty.+The+volcano+emerges+from+the+pink+Martian+desert%2C+which+looks+organic+and+impressionable%E2%80%94like+human+skin%2C+or+the+surface+of+a+clay+pot+before+firing.+The+tenuous+cloud+floats+near+the+volcano%5C%27s+mouth%2C+as+if+in+prelude+to+an+eruption.+It%5C%27s+a+picture+composed+of+millions+of+dots+and+dashes+of+data%2C+produced+by+a+transmission+technique+just+a+few+steps+removed+from+Morse+code%3B+but+it+reveals+a+landscape+the+likes+of+which+Samuel+Morse%2C+let+alone+the+ranks+of+Earth-based+astronomers+who+have+surveyed+the+planets+since+well+before+Babylonian+times%2C+could+scarcely+have+envisioned.%0D%0A%0D%0AIn+case+there+was+any+doubt%2C+many+of+those+good+old+science-fiction+predictions+from+the+1950s+and+the+1960s+are+coming+true.+%5C%22NEW+SQUAD+OF+ROBOTS+READY+TO+ASSAULT+MARS%5C%22+read+a+1998+headline+in+the+online+Houston+Chronicle%2C+stirring+submerged+memories+of+my+adolescent+readings+of+Isaac+Asimov%5C%27s+I%2C+Robot+stories.+But+Asimov%5C%27s+sentient+robots+were+frequently+confused.+Something+always+seemed+to+be+going+wrong+with+them%2C+and+the+mayhem+that+followed+could+inevitably+be+traced+back+to+a+programming+error+by+their+human+handlers%E2%80%94a+situation+not+unfamiliar+to+those+running+NASA%5C%27s+Mars+program%2C+which+was+temporarily+grounded+after+a+catastrophic+pair+of+failures+in+late+1999.+%28The+Mars+Climate+Orbiter+was+lost+owing+to+the+stark+failure+by+one+group+of+engineers+to+translate+another+group%5C%27s+figures+into+metric+units+of+measurement%2C+and+the+Mars+Polar+Lander+because+for+some+unfathomable+reason+its+landing+gear+hadn%5C%27t+been+adequately+tested.%29%0D%0A%0D%0ATo+read+the+full+article+from+%3Ci%3EThe+Atlantic%3C%2Fi%3E%2C+%3Ca+href%3D%5C%27http%3A%2F%2Fwww.theatlantic.com%2Fissues%2F2002%2F07%2Fbenson.htm%5C%27+target%3D%5C%27_blank%5C%27%3Eclick+here%3C%2Fa%3E.','','0000-00-00','0000-00-00','','anyone','','','','text','','http://');
INSERT INTO stories VALUES (50,'','gabe','2002-08-09 16:55:36','',20020809165536,'On+this+page%2C+you+will+find+some+links+to+random+places+on+the+internet.+','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (51,'Middlebury College','gabe','2002-08-09 16:56:04','',20020809165604,'This+is+a+link+to+Middlebury+College%5C%27s+web+site.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://www.middlebury.edu/');
INSERT INTO stories VALUES (52,'Google','gabe','2002-08-09 16:56:21','',20020809165621,'The+best+websearch+engine+out+there%21','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://www.google.com/');
INSERT INTO stories VALUES (53,'MOTS','gabe','2002-08-09 16:57:16','',20020809165716,'This+is+an+online+testing+program+for+faculty+and+students.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://et.middlebury.edu/mots/');
INSERT INTO stories VALUES (54,'MediaDB','gabe','2002-08-09 16:57:47','',20020809165747,'A+media+database+for+faculty.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://et.middlebury.edu/mediadb5/');
INSERT INTO stories VALUES (55,'','gabe','2002-08-09 17:04:39','gabe',20020809170615,'This+page+contains+multiple+discussion+topics%2C+each+of+which+can+be+discussed+by+Middlebury+College+users+%28you+have+the+option+of+allowing+anyone+to+discuss%2C+or+only+students+in+your+class%2C+for+class+websites%29.+Click+the+%3Cb%3Ediscussions%3C%2Fb%3E+link+below+each+topic+to+view+the+discussion%2C+or+add+your+own+two+cents.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','');
INSERT INTO stories VALUES (56,'Life, The Universe, and Everything','gabe','2002-08-09 17:05:31','gabe',20020810224700,'This+quote%2C+from+a+book+by+Douglas+Adams%2C+asks+us+what+we+think+about+life.+What+do+%3Cb%3Eyou%3C%2Fb%3E+think+about+life%3F','','','0000-00-00','0000-00-00',1,'midd','','','a%3A4%3A%7Bi%3A0%3Bi%3A1%3Bi%3A1%3Bi%3A3%3Bi%3A2%3Bi%3A6%3Bi%3A3%3Bi%3A7%3B%7D','text','story','');
INSERT INTO stories VALUES (57,'Climates','gabe','2002-08-09 17:07:37','gabe',20020809174008,'Wise+men+have+said+that+many+people+prefer+warm+climates%2C+as+opposed+to+the+climate+found+in+Vermont.+Vermont+may+have+its+own+beauty%2C+but+many+don%5C%27t+take+this+into+account.+What+do+you+think+of+this%3F','','','0000-00-00','0000-00-00',1,'midd','','','a%3A3%3A%7Bi%3A0%3Bi%3A2%3Bi%3A1%3Bi%3A4%3Bi%3A2%3Bi%3A5%3B%7D','text','story','');
INSERT INTO stories VALUES (58,'','gabe','2002-08-09 17:12:01','',20020809171201,'One+of+the+most+advanced+features+of+SitesDB+is+to+allow+site+creators+to+specify+editors+to+their+sites.+Editors+can+be+assigned+permissions+to+add%2C+edit+or+delete+content+from+certain+parts+of+a+site.+Two+editors+%28specifically+%3Ci%3EAlex+Chapin%3C%2Fi%3E+and+%3Ci%3EAdam+Franco%3C%2Fi%3E%29+have+been+assigned+add+and+edit+permissions+to+this+page.%0D%0A%0D%0ASome+of+the+content+following+has+been+added+by+them.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (59,'Critics lament the state of stem cell research','gabe','2002-08-09 17:33:09','',20020809173309,'In+a+move+to+appease+those+who+support+the+idea%2C+and+those+who+believe+creating+human+embryonic+stem+cells+for+research+is+wrong%2C+Bush+decided+funding+would+only+support+research+on+a+small+number+of+existing+cell+lines+held+by+only+a+few+labs.%0D%0A%0D%0ANow%2C+stem+cell+research+is+at+virtual+standstill%2C+critics+say.+Researchers+complain+they+can%5C%27t+get+the+cells+they+need+because+supplies+are+so+scarce.%0D%0A%0D%0AOne+of+those+critics+is+Stephen+Wakefield+from+Atlanta%2C+Georgia.+He+used+to+ski+and+run+marathons+--+but+not+anymore.%0D%0A%0D%0ASix+years+ago%2C+he+was+diagnosed+with+a+neuromuscular+disorder+similar+to+Lou+Gehrig%5C%27s+disease.+There%5C%27s+no+cure+and+it+will+only+get+worse.+','In+a+move+to+appease+those+who+support+the+idea%2C+and+those+who+believe+creating+human+embryonic+stem+cells+for+research+is+wrong%2C+Bush+decided+funding+would+only+support+research+on+a+small+number+of+existing+cell+lines+held+by+only+a+few+labs.%0D%0A%0D%0ANow%2C+stem+cell+research+is+at+virtual+standstill%2C+critics+say.+Researchers+complain+they+can%5C%27t+get+the+cells+they+need+because+supplies+are+so+scarce.%0D%0A%0D%0AOne+of+those+critics+is+Stephen+Wakefield+from+Atlanta%2C+Georgia.+He+used+to+ski+and+run+marathons+--+but+not+anymore.%0D%0A%0D%0ASix+years+ago%2C+he+was+diagnosed+with+a+neuromuscular+disorder+similar+to+Lou+Gehrig%5C%27s+disease.+There%5C%27s+no+cure+and+it+will+only+get+worse.%0D%0A%0D%0AAt+first%2C+he+was+devastated.+But+then+--like+most+of+us+--+he+heard+about+embryonic+stem+cells.%0D%0A%0D%0AScientists+believe+stem+cells+can+be+turned+into+anything%2C+including+the+nerve+cells+Steve+needs+to+replace+the+ones+dying+in+his+body.%0D%0A%0D%0A%5C%22I+believe+it+is+the+only+thing+on+the+horizon+that+will+provide+me+with+a+cure%2C%5C%22+Wakefield+says+through+his+wife%2C+Pam%2C+who+must+translate+for+him.%0D%0A%0D%0ASo+what+do+Steve+and+Pam+think+of+President+Bush%5C%27s+decision+a+year+ago%3F%0D%0A%0D%0AThat%5C%27s+a+tricky+question.+They%5C%27re+close+to+the+Bushes+and+campaigned+for+them+for+25+years.+A+lawyer%2C+Steve+even+served+in+the+first+Bush+administration+as+general+counsel+for+the+Department+of+Energy.%0D%0A%0D%0ABut+they+think+the+president+made+the+wrong+decision+and+now+feel+disappointed+and+frustrated.%0D%0A%0D%0A%5C%22It+just+seems+like+it%5C%27s+just+so+obvious+to+save+a+life%2C+to+save+many+lives+that+Bush+and+his+administration+would+want+to+go+forward+big+time+with+this+stem+cell+research%2C%5C%22+says+Pam.%0D%0A%0D%0AThey+say+because+of+Bush%5C%27s+decision%2C+stem+cell+research+hasn%5C%27t+made+much+headway.+And+time+is+not+on+Steve%5C%27s+side.%0D%0A%0D%0AIn+the+time+the+decision+was+announced%2C+%5C%22You+have+gotten+worse%2C%5C%22+Pam+says+to+Steve.+He+nods.%0D%0A%0D%0ACNN+spoke+with+several+stem+cell+researchers+who+said+they%5C%27ve+tried%2C+but+for+various+legal+and+scientific+reasons%2C+can%5C%27t+get+their+hands+on+the+cells+from+the+11+labs+that+are+approved+sources.%0D%0A%0D%0ADr.+Curt+Civin%2C+editor+of+the+journal+Stem+Cells+and+a+pediatric+cancer+specialist%2C+wants+stem+cells+to+rebuild+bone+marrow+for+young+cancer+patients.%0D%0A%0D%0ABut%2C+he+says%2C+embryonic+stem+cell+research+is+at+a+virtual+standstill.%0D%0A%0D%0A%5C%22Certainly+in+our+lab+we+haven%5C%27t+been+able+to+get+going%2C+even+on+studying+the+embryonic+stem+cells%2C+because+we+can%5C%27t+get+our+hands+on+them%2C%5C%22+says+Civin.%0D%0A%0D%0AThe+Department+of+Health+and+Human+Services+says+it%5C%27s+trying+to+make+it+easier+for+researchers+like+Civin.%0D%0A%0D%0A%5C%22It%5C%27s+going+to+continue+to+ramp+up%2C+starting+slow%2C+but+continue+to+move+forward%2C%5C%22+says+Tommy+Thompson%2C+HHS+Secretary.%0D%0A%0D%0AIn+fact%2C+the+government+just+recently+started+to+make+arrangements+to+distribute+stem+cells+to+researchers.%0D%0A%0D%0AAnd+Civin+says+he+hopes+to+get+some+of+those+stem+cells+into+his+lab+at+Johns+Hopkins+University+in+Baltimore%2C+Maryland.%0D%0A%0D%0ABut+the+Wakefields+can%5C%27t+help+but+think+this+is+all+moving+too+slowly.%0D%0A%0D%0A%5C%22Reagan+said%2C+%5C%27Mr.+Gorbachev%2C+tear+down+these+walls%5C%27+and+Bush+can+say%2C+%5C%27tear+down+those+walls+of+disease%5C%27.+And+he+could+be+such+a+hero+for+all+eternity%2C%5C%22+says+Pam.+%0D%0A%0D%0AHere+is+a+link+to+%3Ca+href%3D%5C%27http%3A%2F%2Fwww.cnn.com%2F2002%2FHEALTH%2F08%2F09%2Fstem.cell.promise%2Findex.html%5C%27%3Ethe+original+article%3C%2Fa%3E+on+%3Ci%3ECNN.com%3C%2Fi%3E.','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (60,'','achapin','2002-08-09 17:50:16','achapin',20020809175742,'El+Greco','greco2.jpg','','0000-00-00','0000-00-00','','anyone','','','','','image','');
INSERT INTO stories VALUES (61,'Rose','afranco','2002-08-09 17:52:31','afranco',20020809180007,'A+picture+of+a+rose+in+my+mother%5C%27s+flower+garden.','PinkRose.jpg','','0000-00-00','0000-00-00','','anyone','','','','','image','');
INSERT INTO stories VALUES (62,'','gabe','2002-08-09 18:02:02','',20020809180202,'SitesDB+also+allows+you+to+upload+files+and+allow+people+to+download+them.+This+is+useful+if+you+have+a+resume+or+syllabus+in+Microsoft+Word+format%2C+or+RTF+format%2C+or+some+other+file+format+that+you+would+like+to+allow+visitors+to+download.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (63,'Sample Text File','gabe','2002-08-09 18:03:57','',20020809180357,'This+is+a+sample+text+file.+It+doesn%5C%27t+contain+much+text.','sample.txt','','0000-00-00','0000-00-00','','anyone','','','','','file','http://');
INSERT INTO stories VALUES (64,'Sample Word Document','gabe','2002-08-09 18:04:33','',20020809180433,'This+is+a+sample+Micrsoft+Word+document+%28.doc%29.+You+can+download+it+and+view+it+on+your+computer.','sample.doc','','0000-00-00','0000-00-00','','anyone','','','','','file','http://');
INSERT INTO stories VALUES (66,'','gschine','2002-08-10 13:01:38','',20020810130138,'Add+here+a+description+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (67,'','gschine','2002-08-10 13:01:38','',20020810130138,'Add+here+content+such+as+requirements+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (68,'','gschine','2002-08-10 13:01:38','',20020810130138,'Add+here+grading+policies+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (69,'','gschine','2002-08-10 13:01:38','',20020810130138,'Add+here+information+about+you%2C+the+professor.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (70,'','gschine','2002-08-10 13:01:38','',20020810130138,'For+this+page%2C+add+the+first+week%5C%27s+assignments.+Do+the+same+for+the+subsequent+weeks+on+their+respective+pages.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (71,'','gschine','2002-08-10 13:01:38','',20020810130138,'Add+here+a+text+version+of+your+syllabus.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (72,'','gschine','2002-08-10 13:01:38','',20020810130138,'To+make+your+syllabus+avialable+for+download%2C+click+the+%5C%22add+content%5C%22+button+and+choose+%5C%22File+for+Download%5C%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%5C%22Add%5C%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+Microsoft+Word+and+RTF.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (73,'Middlebury College','gschine','2002-08-10 13:01:38','',20020810130138,'This+is+Middlebury+College%5C%27s+website.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (74,'Middlebury Libraries','gschine','2002-08-10 13:01:38','',20020810130138,'This+website+allows+you+to+search+the+Middlebury+Library+catalogue.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (118,'Middlebury Libraries','jschine','2002-08-16 13:55:26','',20020816135526,'This+website+allows+you+to+search+the+Middlebury+Library+catalogue.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (110,'','jschine','2002-08-16 13:55:26','',20020816135526,'Add+here+a+description+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (111,'','jschine','2002-08-16 13:55:26','',20020816135526,'Add+here+content+such+as+requirements+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (112,'','jschine','2002-08-16 13:55:26','',20020816135526,'Add+here+grading+policies+for+your+course...','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (113,'','jschine','2002-08-16 13:55:26','',20020816135526,'Add+here+information+about+you%2C+the+professor.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (116,'','jschine','2002-08-16 13:55:26','',20020816135526,'To+make+your+syllabus+avialable+for+download%2C+click+the+%5C%22add+content%5C%22+button+and+choose+%5C%22File+for+Download%5C%22.+Then+select+your+syllabus+on+your+comptuer+and+click+%5C%22Add%5C%22+and+users+will+be+able+to+download+your+syllabus+to+their+computer.+Popular+file+formats+for+these+documents+are+Microsoft+Word+and+RTF.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (117,'Middlebury College','jschine','2002-08-16 13:55:26','',20020816135526,'This+is+Middlebury+College%5C%27s+website.','','','0000-00-00','0000-00-00','','anyone','','','','','link','http://');
INSERT INTO stories VALUES (115,'','jschine','2002-08-16 13:55:26','',20020816135526,'Add+here+a+text+version+of+your+syllabus.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
INSERT INTO stories VALUES (114,'','jschine','2002-08-16 13:55:26','',20020816135526,'For+this+page%2C+add+the+first+week%5C%27s+assignments.+Do+the+same+for+the+subsequent+weeks+on+their+respective+pages.','','','0000-00-00','0000-00-00','','anyone','','','','text','story','http://');
# --------------------------------------------------------

#
# Table structure for table 'users'
#

CREATE TABLE `users` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `uname` varchar(10) NOT NULL default '',
  `pass` varchar(20) NOT NULL default '',
  `fname` varchar(200) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `type` varchar(10) NOT NULL default '',
  `status` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

#
# Dumping data for table 'users'
#

INSERT INTO users VALUES (1,'gschine','LDAP PASS',' Gabriel Schine','gschine@middlebury.edu','stud','ldap');
INSERT INTO users VALUES (2,'gabe','x33dm1m','Gabe Schine','gschine@middlebury.edu','admin','db');
INSERT INTO users VALUES (3,'achapin','LDAP PASS',' Alex Chapin','achapin@middlebury.edu','admin','ldap');
INSERT INTO users VALUES (4,'sax','LDAP PASS',' Shel Sax','sax@middlebury.edu','stud','ldap');
INSERT INTO users VALUES (5,'veguez','LDAP PASS',' Roberto Veguez','veguez@middlebury.edu','prof','ldap');
INSERT INTO users VALUES (6,'beyer','LDAP PASS',' Tom Beyer','beyer@middlebury.edu','prof','ldap');
INSERT INTO users VALUES (7,'psaldarr','LDAP PASS',' Patricia Saldarriaga','psaldarr@middlebury.edu','prof','ldap');
INSERT INTO users VALUES (8,'admin','ls1cet2#%&','Aministrator','gschine@middlebury.edu','admin','db');
INSERT INTO users VALUES (9,'afranco','LDAP PASS',' Adam Franco','afranco@middlebury.edu','stud','ldap');
INSERT INTO users VALUES (10,'jfu','LDAP PASS',' Jiaxin Fu','jfu@middlebury.edu','stud','ldap');
INSERT INTO users VALUES (11,'jschine','LDAP PASS',' Joseph Schine','jschine@middlebury.edu','stud','ldap');
INSERT INTO users VALUES (12,'schine','LDAP PASS',' Robert Schine','schine@middlebury.edu','prof','ldap');
INSERT INTO users VALUES (13,'jbutler','LDAP PASS',' James Butler','jbutler@middlebury.edu','prof','ldap');
INSERT INTO users VALUES (14,'pyfrom','LDAP PASS',' Mark Pyfrom','pyfrom@middlebury.edu','stud','ldap');
INSERT INTO users VALUES (15,'baron','LDAP PASS',' Andrew Baron','baron@middlebury.edu','stud','ldap');
INSERT INTO users VALUES (16,'dhoughto','LDAP PASS',' Daniel E Houghton','dhoughto@middlebury.edu','stud','ldap');

