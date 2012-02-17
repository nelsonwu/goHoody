<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facebook SEO Comments</title>
</head>

<body>

<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:comments href="http://gohoody.com/ask/computer-and-electronics-gta/" num_posts="5" width="500"></fb:comments>

<div style="visibility:hidden">
</div>
<div>
<?php


//Connect to Hoody MySQL database
	include "misc.inc";
	include "hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	
	








$STD_PATTERN = "<img src='@@userpicture@@'/><a href='@@userlink@@'>@@username@@</a> <BR> @@message@@ <BR> @@formatteddate@@<HR>";


/* PLEASE DON'T MODIFY THE CODE BELOW THIS COMMENT */
class SEO_FBComments {
	const GRAPH_COMMENTS_URL = "https://graph.facebook.com/comments/?ids=";
	
	private $pattern;
	private $pageUrl;

	/**
	 * @param string $pattern
	 * @param bool $debug
	 */
	public function __construct($pattern = null, $debug = null) {
		$this->pageUrl = $this->getSelfUrl();
		$this->pattern = $this->getPattern($pattern);
		
		if(is_null($debug)) $debug = ($_REQUEST["debug"] == "1");
		
	
		$this->echoComments();
	}
	
	function echoComments() {
		$oldTimezone = ini_get("date.timezone");
		ini_set("date.timezone", "UTC");
		
		$comments = $this->GetFBCommentsHTML($this->pageUrl, $this->pattern);
		$comments = "<div class='fb_comments'>$comments</div>";
		
		ini_set("date.timezone", $oldTimezone);
		
		echo $comments;
	}
	
	function getPattern($pattern) {
		global $STD_PATTERN;
		
		if(is_null($pattern)) $pattern = $_REQUEST["pattern"];
		if(!$pattern) $pattern = $STD_PATTERN;
		
		return $pattern;
	}
	
	
	/**
	 * 
	 * Retrieves a list of Facebook comments 
	 * from the Comments Plugin
	 * 
	 * @param string $ids
	 * @return array
	 */
	function GetFBComments($ids) {		
		$query = "SELECT comments FROM Comments WHERE category_id=1";
	$result = mysql_query($query) or die (minor_error(191, $fbme, $uid, $today, $query, mysql_error()));
	$row1 = mysql_fetch_array($result,MYSQL_ASSOC);
	$content = stripslashes($row1['comments']);

		
		$comments = json_decode($content);
		$comments = $comments->$ids->data;
		return $comments;
	}
	
	function dayDiff($date1, $date2 = null) {
		if(is_null($date2)) $date2 = time();
		
		$dateDiff = abs($date1 - $date2);
		$fullDays = floor($dateDiff / (60 * 60 * 24));
		
		return $fullDays;
	}
	function formatDate($date) {
		$dateFormat = "F j \a\\t g:ia";
		
		$date = strtotime($date);
		
		$daysBefore = $this->dayDiff($date);
		
		if($daysBefore > 6)
			$formatteddate = date($dateFormat, $date);
		else {
			switch ($daysBefore) {
				case 0:
					$day = "Today";
					break;
				case 1:
					$day = "Yesterday";
					break;
				default:
					$day = date("l", $date);
					break;
			}
			$formatteddate = "$day at " . date("g:ia", $date);
		}
		
		return $formatteddate;
	}
	
	function getComment($data) {
		$username = $data->from->name;
		$userid = $data->from->id;
		$messageid = $data->id;
		$message = $data->message;
		$date = $data->created_time;
		$formatteddate = $this->formatDate($date);
		
		$USER = json_decode(file_get_contents("https://graph.facebook.com/$userid"));
		$userpicture = "https://graph.facebook.com/$userid/picture";
		$userlink = "http://www.facebook.com/" . $USER->username;
		
		$comment = preg_replace("/@@([^@]+)@@/e", "$\\1", $this->pattern);
		
		return $comment;
	}
	
	function getComments($comments) {
		$html = "";
		
		foreach ($comments as $data) {
			$item = $this->getComment($data);
			$html .= $item;
			
			if($data->comments)
				$html .= $this->getComments($data->comments->data);
		}
		
		return $html;
	}
	
	function getSelfUrl() {
		//$protocol = ($_SERVER["SERVER_PORT"] == "80") ? "http" : "https";
		//return "$protocol://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
		return "http://dev.gohoody.com/ask1/computer-and-electronics-gta/";
	}
	
	function GetFBCommentsHTML($ids, $pattern) {
		$comments = $this->getFBComments($ids);
		$html = $this->getComments($comments);
		
		return $html;
	}
}

new SEO_FBComments;
?>

</div>
</body>
</html>
