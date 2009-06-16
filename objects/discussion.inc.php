<? /* $Id$ */

//echo "bla";
class discussion {
	var $storyid,$parentid,$id;
	var $detail;
//	var $author = array("id"=>0,"uname"=>"","fname"=>"");
	var $authorid=0,$authoruname,$authorfname,$authoremail;
	
	var $libraryfilename,$libraryfileid,$media_tag,$media_size;
	var $tstamp,$content,$subject,$order;
	var $rating = NULL;
	
	var $children=array();
	var $numchildren=0,$pointer=-1,$direction=1;
	
	var $flat=false;
	var $recent=false;
	var $dis_order="recentlast";
	var $opt = array(
			"showcontent"=>false,
			"showsubject"=>true,
			"showauthor"=>true,
			"showtstamp"=>true,
			"useoptforchildren"=>false
			);
	var $getinfo;
	var $storyObj;

/******************************************************************************
 * sets discussion options variables from $opt array
 ******************************************************************************/
							
	function opt($key,$val=NULL) {
		if ($val!=NULL) { // they're setting the option
			$this->opt[$key] = $val;
			return $val;
		}
		
		if (is_array($key)) {
			$this->opt = $key;
			return true;
		}
		
		return $this->opt[$key];
	}
	
/******************************************************************************
 * called from fetchchilden, passed $a = array
 *    (FK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,
 *    discussion_content,discussion_rate,FK_story,media_tag,discussion_order,
 *    user_uname,user_fname,user_last_name,user_email)
 * if discussion posts exist, parse post info from database
 * calls _parseDBline which creates discussion post variables for each discussion post
 * (discussion posts are displayed by outputAll called from fullstory.inc.php)
 ******************************************************************************/
	
	function discussion(& $story,$a=NULL,$parent=0) {
		if (is_array($a)) $this->_parseDBline($a);
		if (is_numeric($a)) $this->id = $a;
		if (is_object($story)) { 
			$this->storyObj =& $story; 
			$this->storyid = $story->id;
		}
		
		if (is_numeric($story)) $this->storyid = $story;
		if ($parent) $this->parentid = $parent;
	}

/******************************************************************************
 * gets next post
 ******************************************************************************/
	
	function getNext() {
		$this->pointer+=$this->direction;
		// if we're out of range, return false
		if (($this->direction > 0 && $this->pointer >= $this->numchildren) || ($this->direction < 0 && $this->pointer <= -1)) return false;
		return $this->children[$this->pointer];
	}

/******************************************************************************
 * delete post
 ******************************************************************************/
	
	function _del() {
		global $site_owner;
//		print "$site_owner";
		if ($_SESSION['auser'] != $site_owner && $this->authorid != $_SESSION['aid']) return false;
		if (!$this->id) return false;
		if ($this->count() || $this->dbcount()) {
			$this->_fetchchildren();
			for ($i = 0; $i < $this->numchildren; $i++) {
				$this->children[$i]->_del();
			}
			$this->numchildren=0;
		}
		discussion::delID($this->id);
	}
	
	function delID($id) {
//		print "deleting $id.<br />";
		$query = "
			DELETE FROM
				discussion
			WHERE
				discussion_id='".addslashes($id)."'
		";
		
		db_query($query);
		//log_entry("discussion","$_SESSION[auser] deleted story ".$_REQUEST['story']." discussion post id ".$_REQUEST['id']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
		// done;
	}

/******************************************************************************
 * stats on # of posts, last post, timestamp, etc.
 ******************************************************************************/
	
	function generateStatistics($story) {
		if (is_object($story)) $storyid = $story->id;
		if (is_numeric($story)) $storyid = $story;
		
		// get the count:
		$count = discussion::getCount($storyid);
		if ($count) $lastPostData = discussion::getLastPostData($storyid);
		else {return "No posts yet.";}
		$posts = ($count==1)?"post":"posts";
		$str = '';
		$str .= "$count $posts, last post on ";
		$str .= timestamp2usdate($lastPostData['timestamp']);
		//$str .= ' by ';
		//$str .= $lastPostData['fullname'];
		return $str;
	}
	
	function getCount($storyid) {
		$query = "
			SELECT
				COUNT(*) as count
			FROM
				discussion
			WHERE
				FK_story='".addslashes($storyid)."'
		";
		
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		return $a['count'];		
	}
	
	function getLastPostData($storyid) {
		$query = "
			SELECT
				user_fname AS fullname,discussion_tstamp AS timestamp
			FROM
				discussion
				INNER JOIN
					user
				ON
					FK_author=user_id
			WHERE
				FK_story = '".addslashes($storyid)."'
			ORDER BY
				discussion_tstamp DESC
			LIMIT 1
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			return db_fetch_assoc($r);
		}
		return null;
	}
	
	function rewind() { $this->pointer = -1; }
	function reverse() { $this->direction*=-1; }
	function setstep($s) { $this->direction=$s; }
	function end() { $this->pointer = $this->numchildren; }
	function startfrombeginning() { $this->rewind(); $this->setstep(1); }
	function startfromend() { $this->end(); $this->setstep(-1); }

/******************************************************************************
 * flat or threaded
 ******************************************************************************/
	
	function flat() { $this->flat = true; }
	function threaded() { $this->flat = false; }

/******************************************************************************
 * discussion sort order
 ******************************************************************************/
	
	function recentfirst() { $this->dis_order = "recentfirst"; }
	function recentlast() { $this->dis_order = "recentlast"; }
	function rating() { $this->dis_order = "rating"; }
	function author() { $this->dis_order = "author"; }
	
/******************************************************************************
 * number of posts (children)
 ******************************************************************************/
	
	function count() { return $this->numchildren; }
	function dbcount() {
		if ($this->numchildren) return $this->numchildren;
		$query = "
			SELECT
				COUNT(*) as count
			FROM
				discussion
			WHERE
				FK_story='".addslashes($this->storyid)."'"
				.(($this->id)?" and FK_parent='".addslashes($this->id)."'":"");
		
		
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		return $a['count'];
	}
	
	function fetchID($id) {
		$this->id = $id;
		$this->_fetch();
	}
	
	function fetch() { $this->_fetch(); }
	function fetchchildren() { $this->_fetchchildren(); }

/******************************************************************************
 * called from discussion
 * parses DB line from query for discussion post info
 * creates $_f array of discussion post variables:
 *    (FK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,
 *    discussion_content,discussion_rate,FK_story,media_tag,discussion_order,
 *    user_uname,user_fname,user_last_name,user_email)
 * creates variables for each item in $_f array
 ******************************************************************************/
	
	function _parseDBline($a) {
		$_f = array("discussion_subject"=>"subject","FK_parent"=>"parentid","FK_author"=>"authorid","FK_story"=>"storyid","media_tag"=>"media_tag","media_size"=>"media_size","discussion_id"=>"id","discussion_tstamp"=>"tstamp","discussion_content"=>"content","discussion_rate"=>"rating","discussion_order"=>"order","user_uname"=>"authoruname","user_fname"=>"authorfname","user_email"=>"authoremail");
		foreach ($_f as $f=>$v) {
			if (isset($a[$f])) $this->$v = $a[$f];
		}
		if ($this->content) $this->content = urldecode($this->content);
		if ($this->subject) $this->subject = urldecode($this->subject);
		
		// :: hack for anonymous posts
		if (!$this->authorfname) {
			$this->authorfname = $this->authoruname = "Anonymous";
			$this->authorid = 0;
		}
	}

/******************************************************************************
 * not sure when this is called...?
 ******************************************************************************/
	
	function _fetch() {
		if (!$this->id) return false;
		
		$query = "
			SELECT
				discussion_tstamp,discussion_content,discussion_subject,discussion_rate,user_uname,user_fname,FK_story,FK_author,FK_parent,media_tag,media_size
			FROM
				discussion
			INNER JOIN
				user
			ON
				FK_author = user_id
			LEFT JOIN
				media
			ON
				FK_media = media_id
			WHERE
				discussion_id='".addslashes($this->id)."'
		";

		$r = db_query($query);
		$a = db_fetch_assoc($r);
		$this->_parseDBline($a);
		return true;
	}

/******************************************************************************
 * get all discussion posts
 ******************************************************************************/
	
	function _fetchchildren() {
		if (!$this->storyid) return false;
		if ($this->numchildren) return false; // they've already called _fetchchildren();
		$this->_commithttpdata();
		
		if ($this->dis_order == "recentfirst") {
			$order = " discussion_tstamp DESC";
		} else if ($this->dis_order == "recentlast") {
			$order = " discussion_tstamp ASC";
		} else if ($this->dis_order == "rating") {
			$order = " discussion_rate DESC";
		} else if ($this->dis_order == "author") {
			$order = " user_fname ASC";
		}
	
	//$order = " discussion_rate DESC";
	//	printpre($order);
				
		$query = "
			SELECT
				FK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,discussion_content,discussion_rate,FK_story,media_tag,media_size,discussion_order,user_uname,user_fname,user_last_name,user_email
			FROM
				discussion
				LEFT JOIN
					user
				ON
					FK_author = user_id
				LEFT JOIN
					media
				ON
					FK_media = media_id
			WHERE
				FK_story = '".addslashes($this->storyid)."' ".
				// check if we're not top-level - if !flat disc, fetch all children, otherwise fetch all discussions
				(($this->flat)?"":" and FK_parent<=>".(($this->id)?"'".addslashes($this->id)."'":"NULL"))."
				
			ORDER BY
				".$order;
		
		//print $query;
		
		$r = db_query($query);
		
		/******************************************************************************
		 * instantiate a discussion object for each post (child) to this story's discussion
		 * pass discussion object $a = array of discussion posts
		 * (FK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,
		 * discussion_content,discussion_rate,FK_story,media_tag,discussion_order,
		 * user_uname,user_fname,user_last_name,user_email)
		 ******************************************************************************/
		
		while($a = db_fetch_assoc($r)) {
			if ($this->storyObj)
				$this->children[] = &new discussion($this->storyObj,$a);
			else
				$this->children[] = &new discussion($this->storyid,$a);
			$this->numchildren++;
		}
		return true;
	}
	
/******************************************************************************
 * insert new posts into discussion table
 ******************************************************************************/
	
	function _insert() {
		$query = "
			SELECT
				COUNT(*) as count
			FROM
				discussion
			WHERE
				FK_story='".addslashes($this->storyid)."'
		";
			
			$a = db_fetch_assoc(db_query($query));
			$this->order = $a['count'];
			
			
			$query = "
				INSERT INTO
					discussion
				SET
					".$this->_generateSQLdata();
			
		// If we've set a timestamp before saving, we probably want to keep it.
		//if ($this->tstamp) $query .= ",discussion_tstamp='".$this->tstamp."'";

		db_query($query);
		//printc($query);
		$this->id = lastid();
		
		return $this->id;
	}
	
	
/******************************************************************************
 * update posts in discussion table
 ******************************************************************************/
	
	function _update() {
		if (!$this->id) return false;
		$query = "
			UPDATE
				discussion
			SET
				".$this->_generateSQLdata()."
			WHERE
				discussion_id='".addslashes($this->id)."'
		";
		
//		printc ($query);
		db_query($query);
		//$newid = lastid();
		return true;
	}
	
	function insert() { 
		$newid = $this->_insert(); 
		return $newid;
	}
	function update() { $this->_update(); }
	
	function _generateSQLdata() {
		$query = "FK_author=".$this->authorid;
		if ($this->parentid) $query .= ",FK_parent=".$this->parentid;
		if ($this->libraryfileid) {
			$media_id = $this->libraryfileid;
			$query .= ",FK_media=".$media_id;
		}
		
		$query .= ",discussion_content='".urlencode(stripslashes($this->content))."'";
		$query .= ",discussion_subject='".urlencode(stripslashes($this->subject))."'";
		if (is_numeric($this->rating)) {
			$query .= ",discussion_rate=".$this->rating;
		} else {
			$query .= ",discussion_rate=NULL";
		}
		$query .= ",FK_story=".$this->storyid;
		// If we've set a timestamp before saving, we probably want to keep it.
		if ($this->tstamp) $query .= ",discussion_tstamp='".$this->tstamp."'";

		//if ($this->order) $query .= $this->order;
		return $query;
	}
	
/******************************************************************************
 * Threaded dicussion: outputs new post link and calls _output function
 * for all children of current post
 * $cr=can reply (ie has permission), $o=owner, $top=parent
 ******************************************************************************/
	
	function outputAll($cr=false,$o=false,$top=false,$showposts=1,$showallauthors=1,$mailposts=0) {
		global $sid,$content, $cfg;
		// debug
//		print "outputAll($canreply,$owner,$copt)<br />";
		// spider down and output every one
		if ($top) {
//			print_r($this->storyObj->permissions);
//			$cand = $this->storyObj->hasPermission("discuss");
			$newpostbar='';
			$newpostbar.="<tr><td>\n";
			
			/******************************************************************************
			 * if user can reply (cr) (ie has permission
			 ******************************************************************************/	
			if ($cr) {
				// just in case...
				$this->_commithttpdata();
				printc ("<tr><td>\n");
				printerr2();
				printc ("</td></tr>\n");
				
				if ($_REQUEST['discuss'] == 'newpost' && ($cfg['disable_discussion'] != TRUE || $_SESSION['atype'] == 'admin')) {
					$this->_outputform('newpost');
				} else {
					//$newpostbar='';
					//$newpostbar.="<tr><td align='right'>";
					if (!$_SESSION[auser] && $showposts != 1) {	
						$newpostbar.="You must be logged in to do this assessment.\n";
					} else {
						if ($cfg['disable_discussion'] == TRUE && $_SESSION['atype'] != 'admin') {
							$newpostbar.="<div align='right'>Discussion posting has been disabled</div>";
						} else {
							$newpostbar.="<div align='right'><a href='".$_SERVER['SCRIPT_NAME']."?$sid&amp;".$this->getinfo."&amp;action=site&amp;discuss=newpost#new'>new post</a></div>\n";
						}
					}
				//	$newpostbar.="</td></tr>";
				}
			
			/******************************************************************************
			 * if user doesn't have permission....
			 ******************************************************************************/

			} else {
				if (!$_SESSION[auser]) {
					$newpostbar.="You must be logged in to contribute to this discussion.\n";
				} else {
					$newpostbar.="Only specified groups or individuals can participant.\n";
				}
			}
			$newpostbar.="</td></tr>\n";
			printc ($newpostbar);
		}
		
		/******************************************************************************
		 * output a discussion post
		 ******************************************************************************/
				
		if ($this->id) $this->_output($cr,$o);
		
		/******************************************************************************
		 * output all discussion of current post's thread (children)
		 ******************************************************************************/
		
		$this->_outputChildren($cr,$o,(($top)?$this->opt:NULL));
		
		if ($this->numchildren && $showposts == 1) printc ($newpostbar);
	}
	
/******************************************************************************
 * Threaded discussion: calls _fetchchildren to get all threads of current post
 * for each post (child) calls outputAll (threaded) or _output (flat) to display
 ******************************************************************************/
	
	function _outputChildren($cr,$o,$opt=NULL) {
		$this->_fetchchildren();
		if ($this->numchildren) {
			if (is_array($opt)) $p = 0;
			else $p = 1;
			if ($p) {
				printc ("<tr><td style='padding: 0px'><table align='right' width='95%' style='padding-left:".$p."px' cellspacing='0px'>\n");
			} else {
				printc ("<tr><td style='padding: 0px'><table width='100%' style='padding-left:".$p."px' cellspacing='0px'>\n");
			}
			for ($i=0;$i<$this->numchildren;$i++) {
				if (is_array($opt)) $this->children[$i]->opt($opt);
				if ($this->opt("useoptforchildren")) $this->children[$i]->opt($this->opt);
				$this->children[$i]->getinfo = $this->getinfo;
				
				if ($this->flat) 
					$this->children[$i]->_output($cr,$o);
				else 
					$this->children[$i]->outputAll($cr,$o);
			}
			printc ("</table></td></tr>\n");
		}
	}
	
	
/******************************************************************************
 * outputs discussion table
 ******************************************************************************/
	
	function output($canreply=false,$owner=false) {
		// print a small table that will house the discussion
		printc ("<table width='100%' style='padding:0' cellspacing='0px'>\n");
		$this->_output($canreply,$owner);
		printc ("</table>\n");
	}

/******************************************************************************
 * commits data from posting form
 ******************************************************************************/
	
	function _commithttpdata() {
		global $sid,$error,$_full_uri;
		global $mailposts, $cfg;
		
		//require_once("htmleditor/editor.inc.php");
		if ($_REQUEST['commit'] && ($cfg['disable_discussion'] != TRUE || $_SESSION['atype'] == 'admin')) { // indeed, we are supposed to commit
			$site = $_REQUEST['site'];
			$action = $_REQUEST['action'];
			$a = $_REQUEST['discuss'];
			if (!$_REQUEST['subject']) error("You must enter a subject.\n");
			if (!$_REQUEST['content']) error("You must enter some text to post.\n");
			if (isset($_REQUEST['rating']) && !is_numeric($_REQUEST['rating']) && $_REQUEST['rating'] != "") $error = "Post rating must be numeric.\n";
			
			if ($error) { unset($_REQUEST['commit']); return false; }
			
			/******************************************************************************
			 * if public discussion and no log in then add user to user table
			 * uname = email address, type = visitor
			 ******************************************************************************/
			
			if (!$_SESSION[auser]) {
				if (user::userEmailExists($_REQUEST['visitor_email'])) {
					 error("A user with that email address already exists.  Please log in before posting.");
				}
				
				/******************************************************************************
				 * Visitor account validation:
				 * check that a name has been entered
				 * check that the email enter doesn't already exist in Segue and 
				 * is not part of the $cfg[visitor_email_excludes] specified in the config
				 ******************************************************************************/
				
				if (!$_REQUEST['visitor_name']) error("You must enter a username.");
				if (!$_REQUEST['visitor_email'] || !ereg("@", $_REQUEST['visitor_email'])) {
					error("You must enter a valid email address.");
					
				} else if ($_REQUEST['visitor_email'] ) {
					foreach ($cfg[visitor_email_excludes] as $visitor_email_exclude) {
						if ($exclude = ereg($visitor_email_exclude, $_REQUEST['visitor_email'])) {
							error("Please log in above with your $cfg[inst_name] account.");
						}
					}
				}
								
				// all good
				if (!$error) {
					$obj = &new user();
					$obj->uname = $_REQUEST['visitor_email'];
					$obj->fname = $_REQUEST['visitor_name'];
					$obj->email = $_REQUEST['visitor_email'];
					$obj->type = "visitor";
					$obj->authtype = 'db';
					$obj->randpass(5,3);
					$obj->insertDB();
					$obj->sendemail();
					$visitor_id = lastid();
				}			
			}
			
			if ($error) { unset($_REQUEST['commit']); return false; }

			
			if ($a=='edit') {
				$d = & new discussion($_REQUEST['story']);
				$d->fetchID($_REQUEST['id']);
				if ($_SESSION['auser'] != $d->authoruname) return false;
				$d->subject = $_REQUEST['subject'];
				
				$d->content = cleanEditorText($_REQUEST['content']);
				$d->content = convertInteralLinksToTags($site, $d->content);
				
				$d->update();
				//log_entry("discussion","$_SESSION[auser] edited story ".$_REQUEST['story']." discussion post id ".$_REQUEST['id']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				
				unset($_REQUEST['discuss'],$_REQUEST['commit']);
				//unset($d);
			}
			
			if ($a=='rate') {
				$d = & new discussion($_REQUEST['story']);
				$d->fetchID($_REQUEST['id']);
				$d->rating = $_REQUEST['rating'];
				
				$d->update();
				//log_entry("discussion","$_SESSION[auser] edited story ".$_REQUEST['story']." discussion post id ".$_REQUEST['id']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				
				unset($_REQUEST['discuss'],$_REQUEST['commit']);
				// unset($d);
			}
			
			if ($a=='reply'|| $a=='newpost') {
				$d = & new discussion($_REQUEST['story']);
				$d->subject = $_REQUEST['subject'];
				
				// Lets pass the cleaning of editor text off to the editor.
				$d->content = cleanEditorText($_REQUEST['content']);
				$d->content = convertInteralLinksToTags($site, $d->content);
					
				if ($a=='reply') {
					$d->parentid = $_REQUEST['replyto'];
					//log_entry("discussion","$_SESSION[auser] replied to story ".$_REQUEST['story']." discussion post id ".$_REQUEST['replyto']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				} else {
					//log_entry("discussion","$_SESSION[auser] posted to story ".$_REQUEST['story']." discussion in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");				
				}
				$d->authorid = ($_SESSION['aid'])?$_SESSION['aid']:$visitor_id;
				$d->authorfname = ($_SESSION['afname'])?$_SESSION['afname']:$_REQUEST['visitor_name'];
				$d->libraryfileid = $_REQUEST['libraryfileid'];
				$newid = $d->insert();
			}
			/******************************************************************************
 			* gather data for sendmail function
			 ******************************************************************************/

			
			if ($mailposts == 1) {
				//printpre("email sending...");
				$this->sendemail($newid);
			}

			unset($_REQUEST['discuss'],$_REQUEST['commit']);
		}
	}
	
/******************************************************************************
 * outputs posting form (new post, edit, reply)
 * add editor options here...
 ******************************************************************************/

	
	function _outputform($t) { // outputs a post form of type $t (newpost,edit,reply)
		global $sid,$error,$site_owner,$_full_uri, $cfg;
		//$script = $_SERVER['SCRIPT_NAME'];
		//printpre ("fulluri: ".$_full_uri);
		//printpre ("thisinfo: ".$this->getinfo);
		
		if ($t == 'edit') {
			$b = 'update';
			$d = "You are editing your post &quot;<a name='".$this->id."'>".$this->subject."</a>&quot;\n";
			$c = ($_REQUEST['content'])?$_REQUEST['content']:$this->content;
			$s = ($_REQUEST['subject'])?$_REQUEST['subject']:$this->subject;
		}
		if ($t == 'reply' || $t == 'newpost') {
			$b = 'post';
			$d = "<a name='new'>You are posting a new entry.</a>\n";
			$c = $_REQUEST['content'];
			if ($t == 'reply') {
				$d = "You are replying to &quot;<a name='reply' href='#'".$this->id.">".$this->subject."</a>&quot;";
				if (!$_REQUEST['subject'] && !ereg("^Re:",$this->subject))
					$s = "Re: ". $this->subject;
				else $s = $this->subject;
			}
			else $s = $_REQUEST['subject'];
		}
		if ($t == 'rate') {
			$b = 'rate';
			//$d = "<a name='".$this->id."'>You are editing your post &quot;".$this->subject."&quot;</a>";
			$s = ($_REQUEST['subject'])?$_REQUEST['subject']:$this->subject;
			$a = "by <span class='subject'>".$this->authorfname."</span>\n";
			$a .= " posted on ";
			$a .= timestamp2usdate($this->tstamp);
			$c = ($_REQUEST['content'])?$_REQUEST['content']:$this->content;						
		}
				
		$p = ($t=='reply')?" style='padding-left: 15px'":'';
		
		printc ("\n<form action='".$_full_uri."/index.php?$sid&amp;action=site&amp;".$this->getinfo."#".$this->id."' method='post' name='addform'>\n");
		printc ("<tr><td$p><b>$d</b></td></tr>\n");
		printc ("<tr><td$p>\n");
		printc ("<table width='100%'  cellspacing='0px'>\n");
		
		if ($t == 'rate') {	
			//printc ("Subject: <input type='text' size='50' name='subject' value='".spchars($s)."' readonly />");
			if ($this->rating && isnumeric($this->rating)) {
				$rating_value = $this->rating;
			} else {
				$rating_value = "";
			}
			
			printc ("<td class='dheader3'>\n");
							
			printc ("<table width='100%' cellspacing='0px'>\n");
			printc ("<tr><td align='left'>\n");
			printc ("<span class='subject'><a name='".$this->id."'>\n");
			printc ($s);
			printc ("</a><input type='hidden' name='subject' value='".spchars($s)."' />\n");
			printc (" (<input type='text' size='3' class='textfield small' name='rating' value='".$rating_value."' />\n");
			printc("<input type='submit' class='button small' value='rate' />");
			printc(" <a href='".$_full_uri."/index.php?$sid&amp;action=site&amp;".$this->getinfo."#".$this->id."'><input type='button' class='button small' value='cancel' /></a>\n");
			printc(" numeric only");
			printc(")\n");
			printc ("</span></td>\n");
			
			printc ("<td align='right'></td>\n");
			printc ("</tr><tr>\n");
			printc ("<td align='left'>\n");
			printc ($a);
			if ($this->media_tag) {
				$media_link = "<a href='".$uploadurl."/".$_REQUEST[site]."/".$this->media_tag."' target='media'>".$this->media_tag."</a>\n";
				printc ("<br />attached: $media_link\n");
			}				
			printc ("</td>\n");
			printc ("<td align='right' valign='bottom'></td></tr>\n"); 
			printc("</table>\n");
			
			printc ("</td>\n");
						
		} else {
			printc ("<tr><td align='left'>\n");
			printc ("<table>");
			
			/******************************************************************************
 			* If public discussion and not logged in
 			* add fields for visitor name and email
			 ******************************************************************************/

			if (!$_SESSION[auser]) {
				printc ("<tr><td colspan = 2><div style='font-size: 9px'>If you part of the ".$cfg[inst_name]);
				printc (" community or have posted to a public forum here and received a visitor user account, please log in <b>before</b> posting.");
				printc ("  If you do not yet have a user account, please register below.</div></td></tr>\n");
				//printc ("<tr><td>Full Name:</td><td><input type='text' size='50' name='visitor_name' value='".$_REQUEST['visitor_name']."' /></td></tr>\n");
				//printc ("<tr><td>Email:</td><td><div style='font-size: 9px'><input type='text' size='25' name='visitor_email' value='".$_REQUEST['visitor_email']."' />\n");
				printc ("<tr><td  colspan = 2 align = center><div style='font-size: 10px'>");
				printc ("<a href='passwd.php?action=login' target='password' onclick='doWindow(\"password\",400,300)'>Login</a> | ");
				printc ("<a href='passwd.php?action=register' target='password' onclick='doWindow(\"password\",400,300)'>Register</a> | ");
				printc ("<a href='passwd.php?action=reset' target='password' onclick='doWindow(\"password\",400,300)'>Forget your password?</a></div>");
				printc ("</td></tr>");
			}
			
			if ($_SESSION[auser]) {			
				printc ("<tr><td>Subject:</td><td><input type='text' class='textfield small' size='50' name='subject' value='".spchars($s)."' /></td></tr>\n");
			//	printc ("<tr><td></td><td></td></tr>\n");
				
			}
			printc ("</table>\n");
		}
		printc ("</td><td align='right'>\n");
		
		// if not rate, print edit, update or post
		if ($t != 'rate' && $_SESSION[auser]) {
			printc("<input type='submit' class='button small' value='$b' />\n");
			printc("<a href='".$_full_uri."/index.php?$sid&amp;action=site&amp;".$this->getinfo."#".$this->id."'><input type='button' class='button small' value='cancel' /></a>\n");
		}
		printc ("</td></tr></table>\n");
		printc ("</td></tr>\n");
		
		// print out post content
		//printc ("<tr><td class='content$p'>");
		
		/******************************************************************************
		 * print out editor here... (if editing post or adding new or not rating)
		 ******************************************************************************/

		if ($t != 'rate' && $_SESSION[auser]) {			
			printc ("<td class='content$p'>\n");
			$c = convertTagsToInteralLinks ($_REQUEST[site], $c);
			addeditor ("content",60,20,$c,"discuss");
		} else {
			printc ("<td>".$c."<br /><br />\n");
			printc ("<input type='hidden' name='content' value='".$c."' />\n");
		}
		
		/******************************************************************************
		 * 	print hidden fields
		 ******************************************************************************/
		 
		printc ("<input type='hidden' name='discuss' value='".$_REQUEST['discuss']."' />\n");
		//added fullstory action for posting form
		printc ("<input type='hidden' name='action' value='".$_REQUEST['action']."' />\n");
		//added site variable for discussion logging
		printc ("<input type='hidden' name='site' value='".$_REQUEST['site']."' />\n");	
		printc ("<input type='hidden' name='libraryfileid' value='".$_REQUEST['libraryfileid']."' />\n");	
		printc ("<input type='hidden' name='dis_order' value='".$this->dis_order."' />\n");
		printc ("<input type='hidden' name='commit' value='1' />\n");
		if ($t=='edit' || $t=='rate') printc ("<input type='hidden' name='id' value=".$_REQUEST['id']." />\n");
		if ($t=='reply') printc ("<input type='hidden' name='replyto' value=".$_REQUEST['replyto']." />\n");
		$site = $_REQUEST[site];
		
		/******************************************************************************
		 * print file upload UI
		 ******************************************************************************/
		 
		if ($t != 'rate'  && $_SESSION[auser]) {	
			printc ("<br />Upload a File:<input type='text' class='textfield small' name='libraryfilename' value='".$_REQUEST['libraryfilename']."' size='25' readonly />\n<input type='button' class='button small' name='browsefiles' value='Browse...' onclick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?site=$site&amp;source=discuss&amp;owner=$site_owner&amp;editor=none\")' target='filebrowser' style='text-decoration: none' />\n\n");
			if ($_SESSION['aid']) printc ("<br />You will be able to edit your post as long as no-one replies to it.\n");
			else printc ("<br />Once submitted, you will not be able to modify your post.\n");
		}
		printc ("</form>\n");
		printc ("</td></tr>\n");
		
	}
		
/******************************************************************************
 * determines what kind of output to do (edit, del) and what to display
 ******************************************************************************/
	
	function _output($cr,$o) {
		global $sid,$error,$showallauthors,$showposts,$uploadurl,$site_owner,$_full_uri;
		
		$siteOwnerId = db_get_value("user","user_id","user_uname='".addslashes($site_owner)."'");
		$parentAuthorId = db_get_value("discussion","FK_author","discussion_id='".addslashes($this->parentid)."'");
		//print $siteOwnerId;
		//printc("author=".$parentAuthorId);

		$siteObj =& $this->storyObj->owningSiteObj;
		$siteLevelEditors = $siteObj->getSiteLevelEditors();
		$isSiteEditor = in_array($_SESSION[auser], $siteLevelEditors);

		
		
		if (
			// Discussion mode, not assement
			$showposts == 1 
			// In assemment mode and one of the users that can view this post
			|| (
				// You are the author of the post
				($_SESSION[auser] == $this->authoruname
				// you are the site_owner
				|| $o == 1
				// you are a site-level editor
				|| $isSiteEditor
				// This is a reply to your post by the site owner
				|| ($site_owner == $this->authoruname && $_SESSION[aid] == $parentAuthorId && $_SESSION[auser])
				// This is a reply to your post by a site-level editor
				|| (in_array($this->authoruname, $siteLevelEditors) && $_SESSION[aid] == $parentAuthorId && $_SESSION[auser])
			)
		)) {
			// check to see if we have any info to commit
			$this->_commithttpdata();
			
			if ($_REQUEST['discuss'] == 'edit' && $_REQUEST['id'] == $this->id) {
				$this->_outputform('edit');
				return true;
			}
			
			if ($_REQUEST['discuss'] == 'del' && $_REQUEST['id'] == $this->id) {
				$this->_del();
				return true;
			}
			
			if ($_REQUEST['discuss'] == 'rate' && $_REQUEST['id'] == $this->id) {
				$this->_outputform('rate');
				return true;
			}
			
			//$script = $_SERVER['SCRIPT_NAME'];
			
			/******************************************************************************
			 * 	Outputs html for displaying posts
			 * outputs discussion post info
			 ******************************************************************************/

			if (!$this->id) return false;
			printc ("\n<tr>");			
			$s = "<a href='".$_full_uri."/index.php?$sid&amp;action=site&amp;".$this->getinfo."&amp;expand=".$this->id."' name='".$this->id."'>".$this->subject."</a>\n";
		//	printc ("</form>");
	//		$s = $this->subject;
			//printpre($_SESSION);
			$a = "";
			if ($showallauthors == 1 || ($_SESSION[auser] && ($o || $_SESSION[auser] == $this->authoruname || $site_owner == $this->authoruname && $_SESSION[aid] == $parentAuthorId))) {
				if ($this->opt("showauthor")) $a .= "by <span class='subject'>".$this->authorfname."</span>\n";
				if ($this->opt("showauthor") && $this->opt("showtstamp")) $a .= " on ";
			} else {
				$a .= "posted on ";
			}
			if ($this->opt("showtstamp")) $a .= timestamp2usdate($this->tstamp);
			
			// Wiki-markup example
			global $storyObj;
			$a .= WikiResolver::getMarkupExample($storyObj->getField("title"), $this->id);
			
			/******************************************************************************
			 * 	 collect possible actions to current post (rely | del | edit | rate)
			 ******************************************************************************/
			$b = array();
			if ($cfg['disable_discussion'] != TRUE && ($cfg['disable_discussion'] != TRUE && $_SESSION['atype'] == 'admin')) {
				if ($cr) 
					$b[] = "<a href='".$_full_uri."/index.php?$sid".$this->getinfo."&amp;replyto=".$this->id."&amp;action=site&amp;discuss=reply#reply'>reply</a>\n";
					
				if ($o || ($_SESSION[auser] == $this->authoruname && !$this->dbcount())) 
					$b[] = "| <a href='".$_full_uri."/index.php?$sid".$this->getinfo."&amp;action=site&amp;discuss=del&amp;id=".$this->id."'>delete</a>\n";
					
				if ($_SESSION[auser] == $this->authoruname && !$this->dbcount()) 
					$b[] = " | <a href='".$_full_uri."/index.php?$sid".$this->getinfo."&amp;id=".$this->id."&amp;action=site&amp;discuss=edit#".$this->id."'>edit</a>\n";
					
				if ($o) 
					$ratelink = "<a href='".$_full_uri."/index.php?$sid".$this->getinfo."&amp;id=".$this->id."&amp;action=site&amp;discuss=rate#".$this->id."'>rate</a>\n";
			}
			
			/******************************************************************************
			 * if there are dicussion actions (reply | del | edit | rate) then print 
			 ******************************************************************************/
	
			if ($a != "" || count($b)) {
				$c = '';
				if (count($b)) $c .= implode(" ",$b);
				
				/******************************************************************************
				 * discussion post header info (subject=$s, author and timestamp=$a, options=$c)
				 ******************************************************************************/
				//printc ("<table width='100%' cellspacing='0px'>\n"); 
				printc ("\n<td class='dheader3'>\n");
				
				printc ("<table width='100%' cellspacing='0px'>\n");
				printc ("<tr><td align='left'>\n");
				printc ("<span class='subject'>\n");
				// subject
				printc ($s);
				// rating
				if ($this->rating !== NULL) 
					printc (" (Rating: ".$this->rating.")");
				printc ("</span></td>\n");
				// link for rating
				printc ("<td align='right'>$ratelink</td>\n");
				printc ("</tr><tr>\n");

				printc ("<td>$a\n");
				printc ("</td>\n");						
				printc ("<td align='right' valign='bottom'>$c</td>"); 					

				printc("</tr>\n</table>\n");
				
			/******************************************************************************
			 * if there are no dicussion actions (rely | del | edit | rate) then 
			 * print subject only
			 ******************************************************************************/
				
			} else printc ($s);
			
			printc ("</td></tr>");
		
			/******************************************************************************
			 * 	print discussion post content
			 ******************************************************************************/
			if ($this->opt("showcontent")) {
				printc ("<tr><td class='dtext'>");
				
				if ($this->media_tag) {
					
					$media_link = "<a href='".$uploadurl."/".$_REQUEST[site]."/".$this->media_tag."' target='media'>".$this->media_tag."</a>\n";					
					$mediaRow[media_tag] = $this->media_tag;
					$mediaRow[slot_name] = $_REQUEST[site];
					$mediaRow[media_size] = $this->media_size;

					$audioplayer = printMediaPlayer($mediaRow);
					$downloadlink = printDownloadLink($mediaRow);
//					$citation = printCitation($mediaRow);
					
					// if attached file is an .mp3 print out audio player
					if ($audioplayer) {
						printc ("<table width='100%' cellpadding='2' border='0'>");
						printc ("<tr><td>");
						printc ($downloadlink."\n");
						printc ($audioplayer."\n");
						
//						printc ("<div style='clear: left; font-size: smaller; margin-bottom: 10px; '>");
//						printc ($citation."\n");
//						printc ("</div>");
						
						printc ("</td></tr>");
						printc ("</table>");
									
					// if attached file not .mp3 print out download link only
					} else {
						printc ("<table width='100%' cellpadding='2' border='0'>");
						printc ("<tr><td>");
						printc ("<div style='clear: left; float: left; '>$media_link</div>\n");
						printc ($downloadlink."\n");
						printc ("</td></tr>");
						printc ("</table>");
					}
				}
				
				$content = convertTagsToInteralLinks ($_REQUEST[site], stripslashes($this->content));
				$wikiResolver =& WikiResolver::instance();
				$content = $wikiResolver->parseText($content, $_REQUEST[site], $_REQUEST[section],$_REQUEST[page]);
				printc ("<div style='clear: both;'>\n");
				printc($content);
				printc ("</div>\n");
				
				//printc ("- [ $c]</td></tr>\n");
				//printc ("<tr><td align='right'>$c</td></tr>\n"); 
			}
			// done
			
			// now check if we're replying to this post
			if ($_REQUEST['discuss'] == 'reply' && $_REQUEST['replyto'] == $this->id) $this->_outputform('reply');
			//if ($_REQUEST['discuss'] == 'rate' && $_REQUEST['replyto'] == $this->id) $this->_outputform('rate');
			
			printc ("</td></tr>");
		}
	}
	
/******************************************************************************
 * Emails site owner discussion posts
 ******************************************************************************/

	function sendemail($newid=0,$emaillist=0) {
		global $sid,$error;
		global $_full_uri;
		//printpre("email sending...");
		//$script = $_SERVER['SCRIPT_NAME'];
		$site =& new site($_REQUEST[site]);
		$siteowneremail = $site->owneremail;
		$siteownerfname = $site->ownerfname;
		$sitetitle = $site->title;
		
		$pageObj =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], $sectionObj);
		$pagetitle = $pageObj->getField('title');		
		$storyObj =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], $pageObj);
		$storytext = $storyObj->getField('shorttext');
		
		// send an email to the siteowner
		$html = 1;
		$emaillist = array();
		$subject = "Segue: ".$_REQUEST['subject'];
		
		$to = $siteownerfname."<".$siteowneremail.">\n";
		//$to = $siteowneremail;
		if ($html == 1) {
			$from = $_SESSION['afname']."<".$_SESSION['aemail'].">\nContent-Type: text/html\n";
		} else {
			$from = $_SESSION['afname']."<".$_SESSION['aemail'].">\n";
		}
		$discussurl = "/index.php?$sid&amp;action=site&amp;site=".$_REQUEST['site']."&amp;section=".$_REQUEST['section']."&amp;page=".$_REQUEST['page']."&amp;story=".$_REQUEST['story']."&amp;detail=".$_REQUEST['detail']."#".$newid;

		if ($html == 1) {
			$body = $siteownerfname.", There has been a discussion posting from the following Segue site:<br />\n";			
			$body .= "<a href='".$_full_uri.$discussurl."'>".$sitetitle." > ".$pagetitle."</a><br /><br />\n";			
			$body .= "<table cellpadding='0' cellspacing='0' border='0'>";
			$body .= "<tr><td>subject: </td><td>".$_REQUEST['subject']."</td></tr>\n";
			$body .= "<tr><td>author: </td><td>".$_SESSION['afname']."</td></tr></table><br />\n";
			$body .= $_REQUEST['content']."<br /><br />\n";
			$body .= "For complete discussion, see:<br />";
			$body .= "<a href='".$_full_uri.$discussurl."'>".$sitetitle." > ".$pagetitle."</a><br /><br />\n";			
		} else {
			$body = "site: ".$sitetitle."\n";
			//$body .= "topic: ".$this->story."\n";	
			$body .= "subject: ".$_REQUEST['subject']."\n";		
			$body .= "author: ".$_SESSION['afname']."\n";
			$body .= $_REQUEST['content']."\n\n";
			$body .= "For complete discussion, see:\n";
			$discussurl2 = "/index.php?$sid&amp;action=site&amp;site=".$_REQUEST['site']."&amp;section=".$_REQUEST['section']."&amp;page=".$_REQUEST['page']."&amp;story=".$_REQUEST['story']."&amp;detail=".$_REQUEST['detail']."#".$newid;
			$body .= $_full_uri.$discussurl2."\n";
		}
		
		// send it!
		if (!mail($to,$subject,$body,"From: $from"))
			print "ERROR: Sending message, '$subject', to '$to' failed.";
	}
		
}



