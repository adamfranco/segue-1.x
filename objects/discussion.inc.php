<? /* $Id$ */
//echo "bla";
class discussion {
	var $storyid,$parentid,$id;
	var $storyObj;
	var $detail;
//	var $author = array("id"=>0,"uname"=>"","fname"=>"");
	var $authorid=0,$authoruname,$authorfname,$authoremail;
	
	var $libraryfilename,$libraryfileid,$media_tag;
	var $tstamp,$content,$subject,$order;
	var $rating;
	
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
	
	function getNext() {
		$this->pointer+=$this->direction;
		// if we're out of range, return false
		if (($this->direction > 0 && $this->pointer >= $this->numchildren) || ($this->direction < 0 && $this->pointer <= -1)) return false;
		return $this->children[$this->pointer];
	}
	
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
//		print "deleting $id.<BR>";
		$query = "
	DELETE FROM
		discussion
	WHERE
		discussion_id=$id";
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
				FK_story=$storyid
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
				FK_story = $storyid
			ORDER BY
				discussion_tstamp DESC
			LIMIT 1";
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
	
	function flat() { $this->flat = true; }
	function threaded() { $this->flat = false; }
	
	function recentfirst() { $this->dis_order = "recentfirst"; }
	function recentlast() { $this->dis_order = "recentlast"; }
	function rating() { $this->dis_order = "rating"; }
	function author() { $this->dis_order = "author"; }
	
	function count() { return $this->numchildren; }
	function dbcount() {
		if ($this->numchildren) return $this->numchildren;
		$query = "
	SELECT
		COUNT(*) as count
	FROM
		discussion
	WHERE
		FK_story=".$this->storyid.
		(($this->id)?" and FK_parent=".$this->id:"");
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
	
	function _parseDBline($a) {
		$_f = array("discussion_subject"=>"subject","FK_parent"=>"parentid","FK_author"=>"authorid","FK_story"=>"storyid","media_tag"=>"media_tag","discussion_id"=>"id","discussion_tstamp"=>"tstamp","discussion_content"=>"content","discussion_rate"=>"rating","discussion_order"=>"order","user_uname"=>"authoruname","user_fname"=>"authorfname","user_email"=>"authoremail");
		foreach ($_f as $f=>$v) {
			if ($a[$f]) $this->$v = $a[$f];
		}
		if ($this->content) $this->content = urldecode($this->content);
		if ($this->subject) $this->subject = urldecode($this->subject);
		
		// :: hack for anonymous posts
		if (!$this->authorfname) {
			$this->authorfname = $this->authoruname = "Anonymous";
			$this->authorid = 0;
		}
	}
	
	function _fetch() {
		if (!$this->id) return false;
		
		$query = "
		SELECT
			discussion_tstamp,discussion_content,discussion_subject,discussion_rate,user_uname,user_fname,FK_story,FK_author,FK_parent,media_tag
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
			discussion_id=".$this->id;

		$r = db_query($query);
		$a = db_fetch_assoc($r);
		$this->_parseDBline($a);
		return true;
	}

/******************************************************************************
 * 
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
			$order = "user_last_name DESC";
		}
				
		$query = "
	SELECT
		FK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,discussion_content,discussion_rate,FK_story,media_tag,discussion_order,user_uname,user_fname,user_last_name,user_email
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
		FK_story = ".$this->storyid.
		// check if we're not top-level - if !flat disc, fetch all children, otherwise fetch all discussions
		(($this->flat)?"":" and FK_parent<=>".(($this->id)?$this->id:"NULL"))
		."
	ORDER BY ".
		$order;
		
		//print $query;
		
		$r = db_query($query);
		while($a = db_fetch_assoc($r)) {
			$this->children[] = &new discussion($this->storyid,$a);
			$this->numchildren++;
		}
		return true;
	}
	
	function _insert() {
		$query = "
		SELECT
			COUNT(*) as count
		FROM
			discussion
		WHERE
			FK_story=".$this->storyid;
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
	
	
	function _update() {
		if (!$this->id) return false;
		$query = "
		UPDATE
			discussion
		SET
			".$this->_generateSQLdata()."
		WHERE
			discussion_id=".$this->id;
		
		//printc ($query);
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
		if ($this->rating) {
			$query .= ",discussion_rate=".$this->rating;
		}
		$query .= ",FK_story=".$this->storyid;
		// If we've set a timestamp before saving, we probably want to keep it.
		if ($this->tstamp) $query .= ",discussion_tstamp='".$this->tstamp."'";

		//if ($this->order) $query .= $this->order;
		return $query;
	}
	
/******************************************************************************
 * outputs new post link and calls _output function
 ******************************************************************************/

	
	function outputAll($cr=false,$o=false,$top=false,$showposts=1,$showallauthors=1,$mailposts=0) {
		global $sid,$content;
		// debug
//		print "outputAll($canreply,$owner,$copt)<BR>";
		// spider down and output every one
		if ($top) {
//			print_r($this->storyObj->permissions);
//			$cand = $this->storyObj->hasPermission("discuss");
			$newpostbar='';
			$newpostbar.="<tr><td>\n";
			if ($cr) {
				// just in case...
				$this->_commithttpdata();
				printc ("<tr><td>\n");
				printerr2();
				printc ("</td></tr>\n");
				
				if ($_REQUEST['discuss'] == 'newpost') {
					$this->_outputform('newpost');
				} else {
					//$newpostbar='';
					//$newpostbar.="<tr><td align=right>";
					if (!$_SESSION[auser] && $showposts != 1) {	
						$newpostbar.="You must be logged in to do this assessment.\n";
					} else {				
						$newpostbar.="<div align=right><a href='".$_SERVER['SCRIPT_NAME']."?$sid&".$this->getinfo."&action=site&discuss=newpost'>new post</a></div>\n";
					}
				//	$newpostbar.="</td></tr>";
				}
			} else {
				if (!$_SESSION[auser]) {
					$newpostbar.="You must be logged in to do contribute to this discussion.\n";
				} else {
					$newpostbar.="Only specified groups or individuals can post to this discussion.\n";
				}
			}
			$newpostbar.="</td></tr>\n";
			printc ($newpostbar);
		}
		
		if ($this->id) $this->_output($cr,$o);
		
		$this->_outputChildren($cr,$o,(($top)?$this->opt:NULL));
		
		if ($this->numchildren && $showposts == 1) printc ($newpostbar);
	}
	
/******************************************************************************
 * 
 ******************************************************************************/
	
	function _outputChildren($cr,$o,$opt=NULL) {
		$this->_fetchchildren();
		if ($this->numchildren) {
			if (is_array($opt)) $p = 0;
			else $p = 1;
			if ($p) {
				printc ("<tr><td style='padding: 0px'><table align=right width=95% style='padding-left:".$p."px' cellspacing=0px>\n");
			} else {
				printc ("<tr><td style='padding: 0px'><table width=100% style='padding-left:".$p."px' cellspacing=0px>\n");
			}
			for ($i=0;$i<$this->numchildren;$i++) {
				if (is_array($opt)) $this->children[$i]->opt($opt);
				if ($this->opt("useoptforchildren")) $this->children[$i]->opt($this->opt);
				$this->children[$i]->getinfo = $this->getinfo;
				if ($this->flat) $this->children[$i]->_output($cr,$o);
				else $this->children[$i]->outputAll($cr,$o);
			}
			printc ("</table></td></tr>\n");
		}
	}
	
	
/******************************************************************************
 * outputs discussion table
 ******************************************************************************/
	
	function output($canreply=false,$owner=false) {
		// print a small table that will house the discussion
		printc ("<table width=100% style='padding:0' cellspacing=0px>\n");
		$this->_output($canreply,$owner);
		printc ("</table>\n");
	}

/******************************************************************************
 * commits data from posting form
 ******************************************************************************/
	
	function _commithttpdata() {
		global $sid,$error;
		global $mailposts;

		if ($_REQUEST['commit']) { // indeed, we are supposed to commit
			$site = $_REQUEST['site'];
			$action = $_REQUEST['action'];
			$a = $_REQUEST['discuss'];
			if (!$_REQUEST['subject']) error("You must enter a subject.\n");
			if (!$_REQUEST['content']) error("You must enter some text to post.\n");
			if ($_REQUEST['rate'] && !is_numeric($_REQUEST['rate'])) $error = "Post rating must be numeric.\n";
			if ($error) { unset($_REQUEST['commit']); return false; }
			
			if ($a=='edit') {
				$d = & new discussion($_REQUEST['story']);
				$d->fetchID($_REQUEST['id']);
				if ($_SESSION['auser'] != $d->authoruname) return false;
				$d->subject = $_REQUEST['subject'];
				$d->content = $_REQUEST['content'];
				$d->update();
				//log_entry("discussion","$_SESSION[auser] edited story ".$_REQUEST['story']." discussion post id ".$_REQUEST['id']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				
				unset($_REQUEST['discuss'],$_REQUEST['commit']);
				//unset($d);
			}
			
			if ($a=='rate') {
				$d = & new discussion($_REQUEST['story']);
				$d->fetchID($_REQUEST['id']);
				$d->authoruname;
				$d->subject = $_REQUEST['subject'];
				$d->content = $_REQUEST['content'];
				$d->rating = $_REQUEST['rating'];
				
				$d->update();
				//log_entry("discussion","$_SESSION[auser] edited story ".$_REQUEST['story']." discussion post id ".$_REQUEST['id']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				
				unset($_REQUEST['discuss'],$_REQUEST['commit']);
				// unset($d);
			}
			
			if ($a=='reply'|| $a=='newpost') {
				$d = & new discussion($_REQUEST['story']);
				$d->subject = $_REQUEST['subject'];
				$d->content = $_REQUEST['content'];
				if ($a=='reply') {
					$d->parentid = $_REQUEST['replyto'];
					//log_entry("discussion","$_SESSION[auser] replied to story ".$_REQUEST['story']." discussion post id ".$_REQUEST['replyto']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				} else {
					//log_entry("discussion","$_SESSION[auser] posted to story ".$_REQUEST['story']." discussion in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");				
				}
				$d->authorid = ($_SESSION['aid'])?$_SESSION['aid']:0;
				$d->authorfname = ($_SESSION['afname'])?$_SESSION['afname']:0;
				$d->libraryfileid = $_REQUEST['libraryfileid'];
				$newid = $d->insert();	
			}
			/******************************************************************************
 			* gather data for sendmail function
			 ******************************************************************************/

			
			if ($mailposts == 1) {
				$this->sendemail($newid);
			}

			unset($_REQUEST['discuss'],$_REQUEST['commit']);
		}
	}
	
/******************************************************************************
 * outputs posting form (new post, edit, reply)
 ******************************************************************************/

	
	function _outputform($t) { // outputs a post form of type $t (newpost,edit,reply)
		global $sid,$error,$site_owner,$_full_uri;
		//$script = $_SERVER['SCRIPT_NAME'];
		//printpre ("fulluri: ".$_full_uri);
		//printpre ("thisinfo: ".$this->getinfo);
		
		if ($t == 'edit') {
			$b = 'update';
			$d = "<a name='".$this->id."'>You are editing your post &quot;".$this->subject."&quot;</a>\n";
			$c = ($_REQUEST['content'])?$_REQUEST['content']:$this->content;
			$s = ($_REQUEST['subject'])?$_REQUEST['subject']:$this->subject;
		}
		if ($t == 'reply' || $t == 'newpost') {
			$b = 'post';
			$d = "You are posting a new entry.\n";
			$c = $_REQUEST['content'];
			if ($t == 'reply') {
				$d = "You are replying to &quot;".$this->subject."&quot;";
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
			$a = "by <span class=subject>".$this->authorfname."</span>\n";
			$a .= " posted on ";
			$a .= timestamp2usdate($this->tstamp);
			$c = ($_REQUEST['content'])?$_REQUEST['content']:$this->content;						
		}
				
		$p = ($t=='reply')?" style='padding-left: 15px'":'';
		
		printc ("\n<form action='".$_full_uri."/index.php?$sid&action=site&".$this->getinfo."#".$this->id."' method=post name=postform>\n");
		printc ("<tr><td$p><b>$d</b></td></tr>\n");
		printc ("<tr><td$p>\n");
		printc ("<table width=100%  cellspacing=0px>\n");
		
		if ($t == 'rate') {	
			//printc ("Subject: <input type=text size=50 name=subject value='".spchars($s)."' readonly>");
			printc ("<td class=dheader3>\n");
							
			printc ("<table width=100% cellspacing=0px>\n");
			printc ("<tr><td align=left>\n");
			printc ("<span class=subject><a name='".$this->id."'>\n");
			printc ($s);
			printc ("</a><input type=hidden name=subject value='".spchars($s)."'>\n");
			printc (" (<input type=text size= 3 class='textfield small' name='rating' value=".$this->rating.">\n");
			printc("<input type=submit class='button small' value='rate'> numeric only)\n");
			printc ("</span></td>\n");
			
			printc ("<td align=right></td>\n");
			printc ("</tr><tr>\n");
			printc ("<td align=left>\n");
			printc ($a);
			if ($this->media_tag) {
				$media_link = "<a href='".$uploadurl."/".$_REQUEST[site]."/".$this->media_tag."' target=media>".$this->media_tag."</a>\n";
				printc ("<br>attached: $media_link\n");
			}				
			printc ("</td>\n");
			printc ("<td align=right valign=bottom></td></tr>\n"); 
			printc("</table>\n");
			
			printc ("</td>\n");
						
		} else {
			printc ("<tr><td align=left>\n");
			printc ("Subject: <input type=text size=50 name=subject value='".spchars($s)."'>\n");
		}
		printc ("</td><td align=right>\n");
		
		// if not rate, print edit, update or post
		if ($t != 'rate') {
			printc("<input type=submit class='button small' value=$b>\n");
			//printc ("<a href='#' onClick='document.postform.submit()'>$b</a>");		
		}
		printc ("</td></tr></table>\n");
		printc ("</td></tr>\n");
		
		// print out post content
		//printc ("<tr><td class=content$p>");
		if ($t != 'rate') {			
			printc ("<td class=content$p><textarea name=content rows=10 cols=60>".spchars($c)."</textarea>\n");
		} else {
			printc ("<td>".spchars($c)."<br><br>\n");
			printc ("<input type=hidden name=content value='".spchars($c)."'>\n");
		}
		
		// print hidden fields
		printc ("<input type=hidden name=discuss value='".$_REQUEST['discuss']."'>\n");
		//added fullstory action for posting form
		printc ("<input type=hidden name=action value='".$_REQUEST['action']."'>\n");
		//added site variable for discussion logging
		printc ("<input type=hidden name=site value='".$_REQUEST['site']."'>\n");	
		printc ("<input type=hidden name=libraryfileid value='".$_REQUEST['libraryfileid']."'>\n");	
		printc ("<input type=hidden name=dis_order value='".$this->dis_order."'>\n");
		printc ("<input type=hidden name=commit value=1>\n");
		if ($t=='edit' || $t=='rate') printc ("<input type=hidden name=id value=".$_REQUEST['id'].">\n");
		if ($t=='reply') printc ("<input type=hidden name=replyto value=".$_REQUEST['replyto'].">\n");
		$site = $_REQUEST[site];
		
		//print file upload UI
		if ($t != 'rate'  && $_SESSION[auser]) {		
			printc ("<br>Upload a File:<input type=text name='libraryfilename' value='".$_REQUEST['libraryfilename']."' size=25 readonly>\n<input type=button name='browsefiles' value='Browse...' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?site=$site&source=discuss&owner=$site_owner&editor=none\")' target='filebrowser' style='text-decoration: none'>\n\n");
			if ($_SESSION['aid']) printc ("<br>You will be able to edit your post as long as no-one replies to it.\n");
			else printc ("<br>Once submitted, you will not be able to modify your post.\n");
		}
		printc ("</form>\n");
		printc ("</td></tr>\n");
		
	}
		
/******************************************************************************
 * determines what kind of output to do (edit, del) and what to display
 ******************************************************************************/
	
	function _output($cr,$o) {
		global $sid,$error,$showallauthors,$showposts,$uploadurl,$site_owner,$_full_uri;
		
		$siteOwnerId = db_get_value("user","user_id","user_uname='".$site_owner."'");
		$parentAuthorId = db_get_value("discussion","FK_author","discussion_id='".$this->parentid."'");
		//print $siteOwnerId;
		//printc("author=".$parentAuthorId);
		if ($showposts == 1 || $o == 1 || $_SESSION[auser] == $this->authoruname || $site_owner == $this->authoruname && $_SESSION[aid] == $parentAuthorId && $_SESSION[auser]) {
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
 ******************************************************************************/

			if (!$this->id) return false;
			//printc ("\n<tr><td class=dheader3>");			
			$s = "<a href='".$_full_uri."/index.php?$sid&action=site&".$this->getinfo."&expand=".$this->id."' name='".$this->id."'>".$this->subject."</a>\n";
		//	printc ("</form>");
	//		$s = $this->subject;
	
			$a = "";
			if ($showallauthors == 1 || $o || $_SESSION[auser] == $this->authoruname || $site_owner == $this->authoruname && $_SESSION[aid] == $parentAuthorId) {
				if ($this->opt("showauthor")) $a .= "by <span class=subject>".$this->authorfname."</span>\n";
				if ($this->opt("showauthor") && $this->opt("showtstamp")) $a .= " on ";
			} else {
				$a .= "posted on ";
			}
			if ($this->opt("showtstamp")) $a .= timestamp2usdate($this->tstamp);
			
			// collect possible actions to current post (rely | del | edit | rate)
			$b = array();
			if ($cr) 
				$b[] = "<a href='".$_full_uri."/index.php?$sid".$this->getinfo."&replyto=".$this->id."&action=site&discuss=reply#".$this->id."'>reply</a>\n";
				
			if ($o || ($_SESSION[auser] == $this->authoruname && !$this->dbcount())) 
				$b[] = "| <a href='".$_full_uri."/index.php?$sid".$this->getinfo."&action=site&discuss=del&id=".$this->id."'>delete</a>\n";
				
			if ($_SESSION[auser] == $this->authoruname && !$this->dbcount()) 
				$b[] = " | <a href='".$_full_uri."/index.php?$sid".$this->getinfo."&id=".$this->id."&action=site&discuss=edit#".$this->id."'>edit</a>\n";
				
			if ($o) 
				$ratelink = "<a href='".$_full_uri."/index.php?$sid".$this->getinfo."&id=".$this->id."&action=site&discuss=rate#".$this->id."'>rate</a>\n";
				
			if ($a != "" || count($b)) {
				$c = '';
				if (count($b)) $c .= implode(" ",$b);
				/******************************************************************************
				 * discussion post header info (subject=$s, author and timestamp=$a, options=$c)
				 ******************************************************************************/
				printc ("\n<tr><td class=dheader3>\n");
				
				printc ("<table width=100% cellspacing=0px>\n");
				printc ("<tr><td align=left>\n");
				printc ("<span class=subject>\n");
				printc ($s);
				if ($this->rating) printc (" (Rating: ".$this->rating.")");
				printc ("</span></td>\n");
				printc ("<td align=right>$ratelink</td>\n");
				printc ("</tr><tr>\n");
				printc ("<td align=left>$a\n");
				if ($this->media_tag) {
					$media_link = "<a href='".$uploadurl."/".$_REQUEST[site]."/".$this->media_tag."' target=media>".$this->media_tag."</a>\n";
					printc ("<br>attached: $media_link\n");
				}				
				printc ("</td>\n");
				
				printc ("<td align=right valign=bottom>$c</td></tr>\n"); 
				printc("</table>\n");
			} else
				printc ($s);
			
			printc ("</td></tr>");
			
			/******************************************************************************
			 * 	discussion entry content
			 ******************************************************************************/
			if ($this->opt("showcontent")) {
				printc ("<tr><td class=dtext>");
				printc (htmlbr($this->content));
				printc ("</td></tr>\n");
			}
			// done
			
			// now check if we're replying to this post
			if ($_REQUEST['discuss'] == 'reply' && $_REQUEST['replyto'] == $this->id) $this->_outputform('reply');
			//if ($_REQUEST['discuss'] == 'rate' && $_REQUEST['replyto'] == $this->id) $this->_outputform('rate');
		}
	}
/******************************************************************************
 * Emails site owner discussion posts
 ******************************************************************************/

	function sendemail($newid=0) {
		global $sid,$error;
		global $_full_uri;
		
		//$script = $_SERVER['SCRIPT_NAME'];
		$site =& new site($_REQUEST[site]);
		$siteowneremail = $site->owneremail;
		$sitetitle = $site->title;
		//$story =& new story($_REQUEST[story]);
		//$story = $storyObj->getfield(shorttext);
		
		// send an email to the siteowner
		
		$to = $siteowneremail;
		$from = $_SESSION['afname']."<".$_SESSION['aemail'].">\n";
		$subject = $sitetitle." Discussion: ".$_REQUEST['subject'];

		$html = 0;
		if ($html == 1) {
			$body = $sitetitle."<br>\n";
			$body .= "subject: ".$_REQUEST['subject']."<br>\n";
			$body .= "author: ".$_SESSION['afname']."<br>\n";
			$body .= $_REQUEST['content']."<br><br>\n";
			$body .= "See:<br>";
			$discussurl = "/index.php?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."";
			$body .= "<a href='".$_full_uri.$discussurl."'>".$_full_uri.$discussurl."</a><br><br>\n";			
		} else {
			$body = "site: ".$sitetitle."\n";
			//$body .= "topic: ".$this->story."\n";	
			$body .= "subject: ".$_REQUEST['subject']."\n";		
			$body .= "author: ".$_SESSION['afname']."\n";
			$body .= $_REQUEST['content']."\n\n";
			$body .= "See:\n";
			$discussurl2 = "/index.php?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."#".$newid;
			$body .= $_full_uri.$discussurl2."\n";
		}
		
		// send it!
		mail($to,$subject,$body,"From: $from");
	}
		
}



