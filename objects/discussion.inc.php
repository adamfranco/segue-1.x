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
	
	function recentfirst() { $this->recent = true; }
	function recentlast() { $this->recent = false; }
	
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
		
		if ($this->recent == "true") {
			$order = "DESC";
		} else {
			$order = "ASC";
		}
		
		$query = "
	SELECT
		FK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,discussion_content,discussion_rate,FK_story,media_tag,discussion_order,user_uname,user_fname,user_email
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
	ORDER BY
		discussion_order $order";
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
		if ($this->tstamp) $query .= ",discussion_tstamp='".$this->tstamp."'";

		db_query($query);
		//printc($query);
		$newid = lastid();
		return $newid;
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
		if ($this->order) $query .= ",discussion_order=".$this->order;
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
			if ($cr) {
				// just in case...
				$this->_commithttpdata();
				printc ("<tr><td>");
				printerr2();
				printc ("</td></tr>");
				
				if ($_REQUEST['discuss'] == 'newpost') {
					$this->_outputform('newpost');
				} else {
					$newpostbar='';
					$newpostbar.="<tr><td align=right>";					
					$newpostbar.="<a href='".$_SERVER['SCRIPT_NAME']."?$sid&".$this->getinfo."&action=site&discuss=newpost'>new post</a>";
					$newpostbar.="</td></tr>";
					printc ($newpostbar);
				}
			}
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
				printc ("<tr><td style='padding: 0px'><table align=right width=95% style='padding-left:".$p."px' cellspacing=0px>");
			} else {
				printc ("<tr><td style='padding: 0px'><table width=100% style='padding-left:".$p."px' cellspacing=0px>");
			}
			for ($i=0;$i<$this->numchildren;$i++) {
				if (is_array($opt)) $this->children[$i]->opt($opt);
				if ($this->opt("useoptforchildren")) $this->children[$i]->opt($this->opt);
				$this->children[$i]->getinfo = $this->getinfo;
				if ($this->flat) $this->children[$i]->_output($cr,$o);
				else $this->children[$i]->outputAll($cr,$o);
			}
			printc ("</table></td></tr>");
		}
	}
	
	
/******************************************************************************
 * outputs discussion table
 ******************************************************************************/
	
	function output($canreply=false,$owner=false) {
		// print a small table that will house the discussion
		printc ("<table width=100% style='padding:0' cellspacing=0px>");
		$this->_output($canreply,$owner);
		printc ("</table>");
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
			if (!$_REQUEST['subject']) error("You must enter a subject.");
			if (!$_REQUEST['content']) error("You must enter some text to post.");
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
				//printc ($d->rating);
				$d->update();
				//log_entry("discussion","$_SESSION[auser] edited story ".$_REQUEST['story']." discussion post id ".$_REQUEST['id']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				
				unset($_REQUEST['discuss'],$_REQUEST['commit']);
				// unset($d);
			}
			
			if ($a=='reply'||$a=='newpost') {
				$d = & new discussion($_REQUEST['story']);
				$d->subject = $_REQUEST['subject'];
				$d->content = $_REQUEST['content'];
				if ($a=='reply') {
					$d->parentid = $_REQUEST['replyto'];
					//log_entry("discussion","$_SESSION[auser] replied to story ".$_REQUEST['story']." discussion post id ".$_REQUEST['replyto']." in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");					
				} else {
					//log_entry("discussion","$_SESSION[auser] posted to story ".$_REQUEST['story']." discussion in site ".$_REQUEST['site'],$_REQUEST['site'],$_REQUEST['story'],"story");				
				}
				$newid = $d->insert();	
			}
			/******************************************************************************
 			* gather data for sendmail function
			 ******************************************************************************/

			$d->authorid = ($_SESSION['aid'])?$_SESSION['aid']:0;
			$d->authorfname = ($_SESSION['afname'])?$_SESSION['afname']:0;
			$d->libraryfileid = $_REQUEST['libraryfileid'];
			
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
		global $sid,$error,$site_owner;
		$script = $_SERVER['SCRIPT_NAME'];
		
		
		if ($t == 'edit') {
			$b = 'update';
			$d = "<a name='".$this->id."'>You are editing your post &quot;".$this->subject."&quot;</a>";
			$c = ($_REQUEST['content'])?$_REQUEST['content']:$this->content;
			$s = ($_REQUEST['subject'])?$_REQUEST['subject']:$this->subject;
		}
		if ($t == 'reply' || $t == 'newpost') {
			$b = 'post';
			$d = "You are posting a new entry.";
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
			$d = "You are rating &quot;".$this->subject."&quot;";
			$c = ($_REQUEST['content'])?$_REQUEST['content']:$this->content;
			$s = ($_REQUEST['subject'])?$_REQUEST['subject']:$this->subject;
		}
				
		$p = ($t=='reply')?" style='padding-left: 15px'":'';
		//
		printc ("\n<form action='$script?$sid&".$this->getinfo."#".$this->id."' method=post name=postform>");
		printc ("<tr><td$p><b>$d</b></td></tr>");
		printc ("<tr><td$p>");
		printc ("<table width=100%  cellspacing=0px><tr><td align=left>");
		
		if ($t == 'rate') {	
			printc ("Subject: <input type=text size=50 name=subject value='".spchars($s)."' readonly>");
		} else {
			printc ("Subject: <input type=text size=50 name=subject value='".spchars($s)."'>");
		}
		printc ("</td><td align=right>");
		
		// if rate, put rate field and submit button
		if ($t == 'rate') {			
			printc ("<input type=text size= 3 class='textfield small' name='rating' value=".$this->rating.">");
			printc ("<a href='#' onClick='document.postform.submit()'>[$b]</a>");
		// if edit or reply, put form submit link
		} else {
			printc ("<a href='#' onClick='document.postform.submit()'>[$b]</a>");		
		}
		printc ("</td></tr></table>");
		printc ("</td></tr>");
		
		// print out post content
		printc ("<tr><td class=content$p>");
		if ($t != 'rate') {			
			printc ("<textarea name=content rows=10 cols=60>".spchars($c)."</textarea>");
		} else {
			printc ("<br>".spchars($c)."<br>");
			printc ("<input type=hidden name=content value='".spchars($c)."'>");
			//printc ("<textarea name=content rows=10 cols=60 readonly>".spchars($c)."</textarea>");
		}
		
		// print hidden fields
		printc ("<input type=hidden name=discuss value='".$_REQUEST['discuss']."'>");
		//added fullstory action for posting form
		printc ("<input type=hidden name=action value='".$_REQUEST['action']."'>");
		//added site variable for discussion logging
		printc ("<input type=hidden name=site value='".$_REQUEST['site']."'>");	
		printc ("<input type=hidden name=libraryfileid value='".$_REQUEST['libraryfileid']."'>");	
		printc ("<input type=hidden name=commit value=1>");
		if ($t=='edit' || $t=='rate') printc ("<input type=hidden name=id value=".$_REQUEST['id'].">");
		if ($t=='reply') printc ("<input type=hidden name=replyto value=".$_REQUEST['replyto'].">");
		$site = $_REQUEST[site];
		
		//print file upload UI
		if ($t != 'rate') {		
			printc ("<br>Upload a File:<input type=text name='libraryfilename' value='".$_REQUEST['libraryfilename']."' size=25 readonly><input type=button name='browsefiles' value='Browse...' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?site=$site&source=discuss&owner=$site_owner&editor=none\")' target='filebrowser' style='text-decoration: none'>");
			if ($_SESSION['aid']) printc ("<br>You will be able to edit your post as long as no-one replies to it.");
			else printc ("<br>Once submitted, you will not be able to modify your post.");
		}
		printc ("</form>\n");
		printc ("</td></tr>");
		
	}
		
/******************************************************************************
 * determines what kind of output to do (edit, del) and what to display
 ******************************************************************************/
	
	function _output($cr,$o) {
		global $sid,$error,$showallauthors,$showposts,$uploadurl,$site_owner;
		
		$siteOwnerId = db_get_value("user","user_id","user_uname='".$site_owner."'");
		$parentAuthorId = db_get_value("discussion","FK_author","discussion_id='".$this->parentid."'");
		//print $siteOwnerId;
		//printc("author=".$parentAuthorId);
		if ($showposts == 1 || $o == 1 || $_SESSION[auser] == $this->authoruname || $site_owner == $this->authoruname && $_SESSION[aid] == $parentAuthorId ) {
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
			
			$script = $_SERVER['SCRIPT_NAME'];
			
/******************************************************************************
 * 	Outputs html for displaying posts
 ******************************************************************************/

			if (!$this->id) return false;
			printc ("\n<tr><td class=dheader3>");			
			$s = "<a href='$script?$sid&action=site&".$this->getinfo."&expand=".$this->id."' name='".$this->id."'>".$this->subject."</a>";
			printc ("</form>");
	//		$s = $this->subject;
	
			$a = "";
			if ($showallauthors == 1 || $o || $_SESSION[auser] == $this->authoruname || $site_owner == $this->authoruname && $_SESSION[aid] == $parentAuthorId) {
				if ($this->opt("showauthor")) $a .= "by <span class=subject>".$this->authorfname."</span>";
				if ($this->opt("showauthor") && $this->opt("showtstamp")) $a .= " on ";
			} else {
				$a .= "posted on ";
			}
			if ($this->opt("showtstamp")) $a .= timestamp2usdate($this->tstamp);
			
			// collect possible actions to current post (rely | del | edit | rate)
			$b = array();
			if ($cr) 
				$b[] = "<a href='$script?$sid".$this->getinfo."&replyto=".$this->id."&action=site&discuss=reply#".$this->id."'>reply</a> | ";
				
			if ($o || ($_SESSION[auser] == $this->authoruname && !$this->dbcount())) 
				$b[] = "<a href='$script?$sid".$this->getinfo."&action=site&discuss=del&id=".$this->id."'>del</a> | ";
				
			if ($_SESSION[auser] == $this->authoruname && !$this->dbcount()) 
				$b[] = "<a href='$script?$sid".$this->getinfo."&id=".$this->id."&action=site&discuss=edit#".$this->id."'>edit</a> | ";
				
			if ($o) 
				$b[] = "<a href='$script?$sid".$this->getinfo."&id=".$this->id."&action=site&discuss=rate#".$this->id."'>rate</a>";
				
			if ($a != "" || count($b)) {
				$c = '';
				if (count($b)) $c .= implode(" ",$b);
				/******************************************************************************
				 * Actual discussion posting content
				 ******************************************************************************/
				printc ("\n<table width=100% cellspacing=0px>");
				printc ("<tr><td align=left>");
				if ($o) printc ("(rating=".$this->rating.")");
				printc ("<span class=subject>$s</span><br>$a</td><td align=right valign=bottom>$c</td></tr>"); 
					if ($this->media_tag) {
						$media_link = "refer to: <a href='".$uploadurl."/".$_REQUEST[site]."/".$this->media_tag."' target=media>".$this->media_tag."</a>";
						printc ("<tr><td align=left>$media_link</td></tr>");
					}
				printc("</table>");
			} else
				printc ($s);
			
			printc ("</td></tr>");
			
			// now output the content
			if ($this->opt("showcontent")) {
				printc ("<tr><td class=dtext>");
				printc (htmlbr($this->content));
				printc ("</td></tr>");
			}
			// done
			
			// now check if we're replying to this post
			if ($_REQUEST['discuss'] == 'reply' && $_REQUEST['replyto'] == $this->id) $this->_outputform('reply');
			if ($_REQUEST['discuss'] == 'rate' && $_REQUEST['replyto'] == $this->id) $this->_outputform('rate');
		}
	}
/******************************************************************************
 * Emails site owner discussion posts
 ******************************************************************************/

	function sendemail($newid=0) {
		global $sid,$error;
		global $_full_uri;
		
		$script = $_SERVER['SCRIPT_NAME'];
		$site =& new site($_REQUEST[site]);
		$siteowneremail = $site->owneremail;
		$sitetitle = $site->title;
		//$story =& new story($_REQUEST[story]);
		//$story = $storyObj->getfield(shorttext);
		
		// send an email to the siteowner
		
		$to = $siteowneremail;
		$from = $_SESSION['afname']."<".$_SESSION['aemail'].">";
		$subject = $sitetitle." Discussion: ".$_REQUEST['subject'];

		$html = 0;
		if ($html == 1) {
			$body = $sitetitle."<br>";
			$body .= "subject: ".$_REQUEST['subject']."<br>";
			$body .= "author: ".$_SESSION['afname']."<br>";
			$body .= $_REQUEST['content']."<br><br>";
			$body .= "See:<br>";
			$discussurl = "$script?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."";
			$discussurl2 = "index.php?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."";
			$body .= "<a href='".$discussurl."'>".$_full_uri.$discussurl2."</a><br><br>";			
		} else {
			$body = "site: ".$sitetitle."\n";
			//$body .= "topic: ".$this->story."\n";	
			$body .= "subject: ".$_REQUEST['subject']."\n";		
			$body .= "author: ".$_SESSION['afname']."\n";
			$body .= $_REQUEST['content']."\n\n";
			$body .= "See:\n";
			//$discussurl = "$script?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."";
			$discussurl2 = "index.php?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."#".$newid;
			$body .= $_full_uri."/".$discussurl2."\n";
		}
		
		//print "To:".$to."<br>";
		//print "From:".$from."<br><br>";
		//print $body."<br>";
		//print "discussurl=".$discussurl."<br>";
		//print "script=".$script."<br>";
		//print "_full_uri=".$_full_uri."<br>";
		//printpre($_SESSION);
		//printpre($_REQUEST);
		// send it!
		mail($to,$subject,$body,"From: $from");
	}
		
}



