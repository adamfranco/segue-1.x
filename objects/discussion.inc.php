<? /* $Id$ */
//echo "bla";
class discussion {
	var $storyid,$parentid,$id;
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
		if ($story) $this->storyid = $story;
		if ($parent) $this->parentid = $parent;
	}
	
	
	function getNext() {
		$this->pointer+=$this->direction;
		// if we're out of range, return false
		if (($this->direction > 0 && $this->pointer >= $this->numchildren) || ($this->direction < 0 && $this->pointer <= -1)) return false;
		return $this->children[$this->pointer];
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
		(($this->id)?" and LK_parent=".$this->id:"");
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
		$_f = array("discussion_subject"=>"subject","LK_parent"=>"parentid","FK_author"=>"authorid","FK_story"=>"storyid","discussion_id"=>"id","discussion_tstamp"=>"tstamp","discussion_content"=>"content","discussion_order"=>"order","user_uname"=>"authoruname","user_fname"=>"authorfname");
		foreach ($_f as $f=>$v) {
			if ($a[$f]) $this->$v = $a[$f];
		}
		if ($this->content) $this->content = urldecode($this->content);
		if ($this->subject) $this->subject = urldecode($this->subject);
	}
	
	function _fetch() {
		if (!$this->id) return false;
		
		$query = "
	SELECT
		discussion_tstamp,discussion_content,discussion_subject,user_uname,user_fname,FK_story,FK_author,LK_parent
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
		
		
		
		$query = "
	SELECT
		LK_parent,discussion_subject,discussion_id,FK_author,discussion_tstamp,discussion_content,FK_story,discussion_order,user_uname,user_fname
	FROM
		discussion
		INNER JOIN
			user
		ON
			FK_author = user_id
	WHERE
		FK_story = ".$this->storyid.
		// check if we're not top-level - if !flat disc, fetch all children, otherwise fetch all discussions
		(($this->flat)?"":" and LK_parent<=>".(($this->id)?$this->id:"NULL"))
		."
	ORDER BY
		discussion_order ASC";
		
		
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
		if ($this->parentid) $query .= ",LK_parent=".$this->parentid;
		$query .= ",discussion_content='".urlencode($this->content)."'";
		$query .= ",discussion_subject='".urlencode($this->subject)."'";
		$query .= ",FK_story=".$this->storyid;
		$query .= ",discussion_order=".$this->order;
		return $query;
	}
	
	function outputAll($cr=false,$o=false,$copt=false) {
		// debug
//		print "outputAll($canreply,$owner,$copt)<BR>";
		// spider down and output every one
		if ($this->id) $this->_output($cr,$o);
		$this->_outputChildren($cr,$o,(($copt)?$this->opt:NULL));
	}
	
	function _outputChildren($cr,$o,$opt=NULL) {
		$this->_fetchchildren();
		if ($this->numchildren) {
			if (is_array($opt)) $p = 0;
			else $p = 10;
			print "<tr><td><table width=100% style='padding-left:".$p."px'>";
			for ($i=0;$i<$this->numchildren;$i++) {
				if (is_array($opt)) $this->children[$i]->opt($opt);
				if ($this->opt("useoptforchildren")) $this->children[$i]->opt($this->opt);
				$this->children[$i]->getinfo = $this->getinfo;
				if ($this->flat) $this->children[$i]->_output($cr,$o);
				else $this->children[$i]->outputAll($cr,$o);
			}
			print "</table></td></tr>";
		}
	}
	
	function output($canreply=false,$owner=false) {
		// print a small table that will house the discussion
		print "<table width=100%>";
		$this->_output($canreply,$owner);
		print "</table>";
	}
	
	function _commithttpdata() {
		global $error;
		if ($_REQUEST['commit']) { // indeed, we are supposed to commit
			$a = $_REQUEST['action'];
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
				$d->authorid = $_SESSION['aid'];
				$d->insert();
			}
			unset($_REQUEST['action'],$_REQUEST['commit']);
		}
	}
	
	function _outputform($t) { // outputs a post form of type $t (newpost,edit,reply)
		global $sid,$error;
		$script = $_SERVER['SCRIPT_NAME'];
		if ($t == 'edit') {
			$b = 'update';
			$d = "You are editing your post &quot;".$this->subject."&quot;";
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
		$p = ($t=='reply')?" style='padding-left: 15px'":0;
		print "<form action='$script?$sid&".$this->getinfo."#".$this->id."' method=post name=postform>";
		print "<tr><td$p><b>$d</b></td></tr>";
		print "<tr><td$p>";
		print "<table width=100%><tr><td align=left>";
		print "Subject: <input type=text size=50 name=subject value='$s'>";
		print "</td><td align=right class=info><a href='#' onClick='document.postform.submit()'>[$b]</a></td></tr></table>";
		print "</td></tr>";
		print "<tr><td class=content$p>";
		print "<textarea name=content rows=4 cols=80>".spchars($c)."</textarea>";
		print "<input type=hidden name=action value='".$_REQUEST['action']."'>";
		print "<input type=hidden name=commit value=1>";
		if ($t=='edit') print "<input type=hidden name=id value=".$_REQUEST['id'].">";
		if ($t=='reply') print "<input type=hidden name=replyto value=".$_REQUEST['replyto'].">";
		print "<br>You will be able to edit/delete your post as long as no-one replies to it.";
		print "</td></tr>";
		print "</form>";
	}
		
	
	function _output($cr,$o) {
		global $sid,$error;
		
		// check to see if we have any info to commit
		$this->_commithttpdata();
		
		if ($_REQUEST['action'] == 'edit' && $_REQUEST['id'] == $this->id) {
			$this->_outputform('edit');
			return true;
		}
		
		$script = $_SERVER['SCRIPT_NAME'];
		
		// output the html and stuff
		if (!$this->id) return false;
		print "<tr><td>";
		$s = "<a href='#' name='".$this->id."'>".$this->subject."</a>";
		$a = array();
		if ($this->opt("showauthor")) $a[] = $this->authorfname;
		if ($this->opt("showtstamp")) $a[] = timestamp2usdate($this->tstamp);
		$b = array();
		if ($cr) $b[] = "<a href='$script?$sid".$this->getinfo."&replyto=".$this->id."&action=reply' class=info>[reply]</a>";
		if ($o) $b[] = "<a href='#' class=info>[del]</a>";
		if ($_SESSION[auser] == $this->authoruname && !$this->dbcount()) 
			$b[] = "<a href='#' class=info>[edit]</a>";
		if (count($a) || count($b)) {
			$c = '';
			if (count($a)) $c .= "(".implode(" - ",$a).") ";
			if (count($b)) $c .= implode(" ",$b);
			print "<table width=100%><tr><td align=left class=subject>$s</td><td align=right class=info>$c</td></tr></table>";
		} else
			print $s;
		
		// now output the content
		if ($this->opt("showcontent")) {
			print "<tr><td class=content>";
			print $this->content;
			print "</td></tr>";
		}
		// done
		
		// now check if we're replying to this post
		if ($_REQUEST['action'] == 'reply' && $_REQUEST['replyto'] == $this->id) $this->_outputform('reply');
	}
}