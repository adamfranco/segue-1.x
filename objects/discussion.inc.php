<? /* $Id$ */
//echo "bla";
class discussion {
	var $storyid,$parentid,$id;
	var $storyObj;
	var $detail;
//	var $author = array("id"=>0,"uname"=>"","fname"=>"");
	var $authorid=0,$authoruname,$authorfname;
	
	var $tstamp,$content,$subject,$order;
	
	var $children=array();
	var $numchildren=0,$pointer=-1,$direction=1;
	
	var $flat=false;
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
	
	function discussion($story,$a=NULL,$parent=0) {
		if (is_array($a)) $this->_parseDBline($a);
		if (is_numeric($a)) $this->id = $a;
		if (is_object($story)) { $this->storyObj = &$story; $this->storyid = $story->id; }
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
		$str .= ' by ';
		$str .= $lastPostData['fullname'];
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
		$_f = array("discussion_subject"=>"subject","FK_parent"=>"parentid","FK_author"=>"authorid","FK_story"=>"storyid","discussion_id"=>"id","discussion_tstamp"=>"tstamp","discussion_content"=>"content","discussion_order"=>"order","user_uname"=>"authoruname","user_fname"=>"authorfname");
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
		discussion_tstamp,discussion_content,discussion_subject,user_uname,user_fname,FK_story,FK_author,FK_parent
	FROM
		discussion
		INNER JOIN
			user
		ON
			FK_author = user_id
	WHERE
		discussion_id=".$this->id;

		$r = db_query($query);
		$a = db_fetch_assoc($r);
		$this->_parseDBline($a);
		return true;
	}
	
	function _fetchchildren() {
		if (!$this->storyid) return false;
		if ($this->numchildren) return false; // they've already called _fetchchildren();
		$this->_commithttpdata();
		
		
		$query = "
	SELECT
		FK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,discussion_content,FK_story,discussion_order,user_uname,user_fname
	FROM
		discussion
		LEFT JOIN
			user
		ON
			FK_author = user_id
	WHERE
		FK_story = ".$this->storyid.
		// check if we're not top-level - if !flat disc, fetch all children, otherwise fetch all discussions
		(($this->flat)?"":" and FK_parent<=>".(($this->id)?$this->id:"NULL"))
		."
	ORDER BY
		discussion_order ASC";
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
		db_query($query);
		return true;
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
		
		db_query($query);
		return true;
	}
	
	function insert() { $this->_insert(); }
	function update() { $this->_update(); }
	
	function _generateSQLdata() {
		$query = "FK_author=".$this->authorid;
		if ($this->parentid) $query .= ",FK_parent=".$this->parentid;
		$query .= ",discussion_content='".urlencode(stripslashes($this->content))."'";
		$query .= ",discussion_subject='".urlencode(stripslashes($this->subject))."'";
		$query .= ",FK_story=".$this->storyid;
		if ($this->order) $query .= ",discussion_order=".$this->order;
		return $query;
	}
	
/******************************************************************************
 * outputs new post link and calls _output function
 ******************************************************************************/

	
	function outputAll($cr=false,$o=false,$top=false) {
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
		if ($this->numchildren) printc ($newpostbar);
	}
	
/******************************************************************************
 * 
 ******************************************************************************/
	
	function _outputChildren($cr,$o,$opt=NULL) {
		$this->_fetchchildren();
		if ($this->numchildren) {
			if (is_array($opt)) $p = 0;
			else $p = 10;
			printc ("<tr><td><table width=100% style='padding-left:".$p."px'>");
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
		printc ("<table width=100%>");
		$this->_output($canreply,$owner);
		printc ("</table>");
	}

/******************************************************************************
 * commits data from posting form
 ******************************************************************************/
	
	function _commithttpdata() {
		global $error;
		if ($_REQUEST['commit']) { // indeed, we are supposed to commit
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
				unset($d);
			}
			if ($a=='reply'||$a=='newpost') {
				$d = & new discussion($_REQUEST['story']);
				$d->subject = $_REQUEST['subject'];
				$d->content = $_REQUEST['content'];
				if ($a=='reply') $d->parentid = $_REQUEST['replyto'];
				$d->authorid = ($_SESSION['aid'])?$_SESSION['aid']:0;
				$d->insert();
			}
			unset($_REQUEST['discuss'],$_REQUEST['commit']);
		}
	}
	
/******************************************************************************
 * outputs posting form (new post, edit, reply)
 ******************************************************************************/

	
	function _outputform($t) { // outputs a post form of type $t (newpost,edit,reply)
		global $sid,$error;
		$script = $_SERVER['SCRIPT_NAME'];
		if ($t == 'edit') {
			$b = 'update';
			$d = "<a name='".$this->id."'>You are editing your post &quot;".$this->subject."&quot;</a>";
			$c = ($_REQUEST['content'])?$_REQUEST['content']:$this->content;
			$s = ($_REQUEST['subject'])?$_REQUEST['subject']:$this->subject;
		}
		if ($t == 'reply' || $t == 'newpost') {
			$b = 'post';
			$d = "You are posting a new discussion entry.";
			$c = $_REQUEST['content'];
			if ($t == 'reply') {
				$d = "You are replying to &quot;".$this->subject."&quot;";
				if (!$_REQUEST['subject'] && !ereg("^Re:",$this->subject))
					$s = "Re: ". $this->subject;
				else $s = $this->subject;
			}
			else $s = $_REQUEST['subject'];
		}
		$p = ($t=='reply')?" style='padding-left: 15px'":'';
		//
		printc ("<form action='$script?$sid&".$this->getinfo."#".$this->id."' method=post name=postform>");
		printc ("<tr><td$p><b>$d</b></td></tr>");
		printc ("<tr><td$p>");
		printc ("<table width=100%><tr><td align=left>");
		printc ("Subject: <input type=text size=50 name=subject value='".spchars($s)."'>");
		printc ("</td><td align=right class=info><a href='#' onClick='document.postform.submit()'>[$b]</a></td></tr></table>");
		printc ("</td></tr>");
		printc ("<tr><td class=content$p>");
		printc ("<textarea name=content rows=4 cols=80>".spchars($c)."</textarea>");
		//changed from action to discuss
		printc ("<input type=hidden name=discuss value='".$_REQUEST['discuss']."'>");
		//added fullstory action for posting form
		printc ("<input type=hidden name=action value='".$_REQUEST['action']."'>");
		printc ("<input type=hidden name=commit value=1>");
		if ($t=='edit') printc ("<input type=hidden name=id value=".$_REQUEST['id'].">");
		if ($t=='reply') printc ("<input type=hidden name=replyto value=".$_REQUEST['replyto'].">");
		if ($_SESSION['aid']) printc ("<br>You will be able to edit your post as long as no-one replies to it.");
		else printc ("<br>Once submitted, you will not be able to modify your post.");
		printc ("</td></tr>");
		printc ("</form>");
	}
		
/******************************************************************************
 * determines what kind of output to do (edit, del) and what to display
 ******************************************************************************/
	
	function _output($cr,$o) {
		global $sid,$error;
		
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
		
		$script = $_SERVER['SCRIPT_NAME'];
		
		// output the html and stuff
		if (!$this->id) return false;
		printc ("<tr><td>");
		$s = "<a href='$script?$sid&action=site&".$this->getinfo."&expand=".$this->id."' name='".$this->id."'>".$this->subject."</a>";
//		$s = $this->subject;

		$a = array();
		if ($this->opt("showauthor")) $a[] = $this->authorfname;
		if ($this->opt("showtstamp")) $a[] = timestamp2usdate($this->tstamp);
		$b = array();
		if ($cr) $b[] = "<a href='$script?$sid".$this->getinfo."&replyto=".$this->id."&action=site&discuss=reply#".$this->id."' class=info>reply</a>";
		if ($o || ($_SESSION[auser] == $this->authoruname && !$this->dbcount())) $b[] = "<a href='$script?$sid".$this->getinfo."&action=site&discuss=del&id=".$this->id."' class=info> | del</a>";
		if ($_SESSION[auser] == $this->authoruname && !$this->dbcount()) 
			$b[] = "<a href='$script?$sid".$this->getinfo."&id=".$this->id."&action=site&discuss=edit#".$this->id."' class=info> | edit</a>";
		if (count($a) || count($b)) {
			$c = '';
			if (count($a)) $c .= "(".implode(" - ",$a).") ";
			if (count($b)) $c .= implode(" ",$b);
			/******************************************************************************
			 * Actual discussion posting content
			 ******************************************************************************/
			printc ("<table width=100%><tr><td align=left class=subject>$s</td><td align=right class=info>$c</td></tr></table>");
		} else
			printc ($s);
		
		// now output the content
		if ($this->opt("showcontent")) {
			printc ("<tr><td class=content>");
			printc (htmlbr($this->content));
			printc ("</td></tr>");
		}
		// done
		
		// now check if we're replying to this post
		if ($_REQUEST['discuss'] == 'reply' && $_REQUEST['replyto'] == $this->id) $this->_outputform('reply');
	}
}