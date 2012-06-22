<?
/****************************************
**  Steve Beyer Productions
**  Website and Talent Database
**
**  Concept: Steve Beyer
**  Code: Presence
**
**  Last Edit: 20120620
****************************************/

function Init() {
  global $conn;
	error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	date_default_timezone_set('America/Los_Angeles');
	session_start(); // I want to track people thru the site
	$_SESSION['last_move'] = $_SESSION['last_activity']; // testing how long page to page
	if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 120)) {
		// last request was more than 60 minates ago (3600 seconds)
		session_destroy();   // destroy session data in storage
		session_unset();     // unset $_SESSION variable for the runtime
	}
	$_SESSION['last_activity'] = time(); // update last activity time stamp
	// lets count how many pages visitor's looked at
	isset($_SESSION['count']) ? $_SESSION['count']++ : $_SESSION['count'] = 0;
	$host  = "localhost";
	$db    = "sbpweb";
	require_once("db.php");
  $conn = mysqli_connect($host, $user, $pass, $db) or die(mysqli_error());
}

function RecordHit() {
	global $conn;
	$query = sprintf("INSERT INTO sitehits (hit_datetime, hit_ip, hit_url, user_agent, referrer, sessionid, sesscount) values ('%s','%s','%s','%s','%s', '%s', %s);",
		mysqli_real_escape_string($conn, DatePHPtoSQL(time())),
		mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']),
		mysqli_real_escape_string($conn, $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']),
		mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT']),
		mysqli_real_escape_string($conn, $_SERVER['HTTP_REFERRER']),
		mysqli_real_escape_string($conn, session_id()),
		mysqli_real_escape_string($conn, $_SESSION['count'])
	);
	$result = mysqli_query($conn, $query);
}

function DateSQLtoPHP($mysqldate) {
	// mysql's datetime to PHP seconds-since-epoch date format
	return (strtotime($mysqldate));
}

function DatePHPtoSQL($phpdate) {
	// php's seconds-since-epoch timestamp to MySQL
	return (date('Y-m-d H:i:s', $phpdate));
}

function DebugShow() {
	echo "You wanted to look at: ";
	if (strlen($_REQUEST['url']) > 0) {
		printf ("Page: %s<br>\nSpecifically: %s<br>\nCount: %s<br>\nLast Activity:%s seconds<br>\n",
			$_REQUEST['page'],
			$_REQUEST['url'],
			$_SESSION['count'],
			(time() - $_SESSION['last_move'])
		);
	} else {
		printf ("Page: %s<br>\nCount: %s<br>\nLast Activity:%s seconds<br>\n",
			$_REQUEST['page'],
			$_SESSION['count'],
			(time() - $_SESSION['last_move'])
		);
	}
}

function AdminDisplaySiteStats() {
	global $conn;
	$domains = array();
	$hits = array();
	$visitors = array();
	$query = "SELECT * FROM sitehits where hit_datetime >= '".DatePHPtoSQL(strtotime('-7 days'))."' AND hit_datetime <= '".DatePHPtoSQL(time())."';";
	$result = mysqli_query($conn, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		preg_match('@^(?:http://)?(www\.)?([^/]+)@i',$row['hit_url'],$matches);
		$domain = $matches[2];
		$domains[$domain] = $domain;
		$visitors[$domain][$row['hit_ip']] = 1;
		$hits[$domain] = $hits[$domain] + 1;
	}
	?>
	<table border="0" cellpadding="10"><tr valign="top"><td>
		<table border="0" cellpadding="2" cellspacing="0"><tr><td bgcolor="#DE9A40">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr><td bgcolor="#DE9A40" colspan="3">Website Activity - 7 Days</td></tr>
			<? foreach ($domains as $domain) { ?>
			<tr bgcolor="#2A5756"><td align="right" rowspan="3"><? echo $domain; ?>: </td></tr>
			<tr bgcolor="#2A5756"><td>Visitors:</td><td><? echo count($visitors[$domain]); ?></td></tr>
			<tr bgcolor="#2A5756"><td>Hits:</td></td><td><? echo $hits[$domain] + 0; ?></td></tr>
			<tr><td colspan="3"></td></tr>
			<? } ?>
		</table></td></tr></table>
		<?

	$hits = array();
	$visitors = array();
	$query = "SELECT * FROM sitehits where hit_datetime >= '".DatePHPtoSQL(strtotime('-1 day'))."' AND hit_datetime <= '".DatePHPtoSQL(time())."';";
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		preg_match('@^(?:http://)?(www\.)?([^/]+)@i',$row['hit_url'],$matches);
		$domain = $matches[2];
		$domains[$domain] = $domain;
		$visitors[$domain][$row['hit_ip']] = 1;
		$hits[$domain] = $hits[$domain] + 1;
	}
	?></td><td>
		<table border="0" cellpadding="2" cellspacing="0"><tr><td bgcolor="#DE9A40">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr><td bgcolor="#DE9A40" colspan="3">Website Activity - 24 Hours</td></tr>
			<? foreach ($domains as $domain) { ?>
			<tr bgcolor="#2A5756"><td align="right" rowspan="3"><? echo $domain; ?>: </td></tr>
			<tr bgcolor="#2A5756"><td>Visitors:</td><td><? echo count($visitors[$domain]); ?></td></tr>
			<tr bgcolor="#2A5756"><td>Hits:</td></td><td><? echo $hits[$domain] + 0; ?></td></tr>
			<tr><td colspan="3"></td></tr>
			<? } ?>
		</table></td></tr></table>
		<?

	$hits = array();
	$visitors = array();
	$query = "SELECT * FROM sitehits where hit_datetime >= '".DatePHPtoSQL(strtotime('-7 days'))."' AND hit_datetime <= '".DatePHPtoSQL(time())."';";
	$query = "SELECT * FROM sitehits where hit_datetime >= '".DatePHPtoSQL(strtotime('-1 hour'))."' AND hit_datetime <= '".DatePHPtoSQL(time())."';";
	$result = mysqli_query($conn, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		preg_match('@^(?:http://)?(www\.)?([^/]+)@i',$row['hit_url'],$matches);
		$domain = $matches[2];
		$domains[$domain] = $domain;
		$visitors[$domain][$row['hit_ip']] = 1;
		$hits[$domain] = $hits[$domain] + 1;
	}
	?></td><td>
		<table border="0" cellpadding="2" cellspacing="0"><tr><td bgcolor="#DE9A40">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr><td bgcolor="#DE9A40" colspan="3">Website Activity - 60 Minutes</td></tr>
			<? foreach ($domains as $domain) { ?>
			<tr bgcolor="#2A5756"><td align="right" rowspan="3"><? echo $domain; ?>: </td></tr>
			<tr bgcolor="#2A5756"><td>Visitors:</td><td><? echo count($visitors[$domain]); ?></td></tr>
			<tr bgcolor="#2A5756"><td>Hits:</td></td><td><? echo $hits[$domain] + 0; ?></td></tr>
			<tr><td colspan="3"></td></tr>
			<? } ?>
		</table></td></tr></table>
		</td></tr></table>
		<?
}

function ShowAdminPage() {
	$adminfunctions = array(
		"Welcome" => "",
		"Artist Editor" => "artist_editor",
		"Categories List" => "categories_list",
		"Styles List" => "styles_list",
		"Locations List" => "locations_list",
		"Featured Categories" => "categories_featured",
		"Featured Artists" => "artists_featured",
		"News Blog" => "blog_editor",
		"Website Metrics" => "web_stats"
	);
	include("templates/admin.php");
	AdminHead($_REQUEST['url'],$adminfunctions);
	AdminNav($_REQUEST['url'],$adminfunctions);
	if ((strlen($_REQUEST['url']) == 0) || ($_REQUEST['url'] == "web_stats")) {
		AdminDisplaySiteStats();
	} else {
		if ($_REQUEST['url'] == "categories_list") {
			if ($_REQUEST['function'] == "add_category") {
				AdminSaveNewCategory();
			}
			if ($_REQUEST['function'] == "del_category") {
				AdminDeleteCategory();
			}
			AdminEditCategories();
		}
	}
}

function AdminEditCategories() {
	global $conn;
	$query = "SELECT * FROM `categories`";
	$result = mysqli_query($conn,$query);
	$categorieslist = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$categorieslist[] = array(
			"url" => $row['url'],
			"category" => $row['category'],
			"description" => $row['description']
		);
	}
	mysqli_free_result($result);
	aasort($categorieslist,"category");
	AdminShowCategories($categorieslist);
}

function AdminSaveNewCategory() {
	global $conn;
	$url = preg_replace("/ /","_",strtolower(strip_tags($_REQUEST['url'])));
	$category = htmlspecialchars(ucwords($_REQUEST['category']));
	$query = sprintf("INSERT INTO `categories` (`url`,`category`,`description`) VALUES ('%s','%s','%s')",
		mysqli_real_escape_string($conn,$url),
		mysqli_real_escape_string($conn,$category),
		mysqli_real_escape_string($conn,($_REQUEST['description']))
	);
	if (mysqli_query($conn,$query) === TRUE) {
		echo "<div class='AdminSuccess'>Category Entry <B>$category</B> [$url] Successfully Added.</div>";
	} else {
		echo "<div class='AdminError'>Category Entry <B>$category</B> [$url] Failed to Save!</div>";
	}
}

function aasort (&$array, $key) {
	// sort an array's array by the sub-array's key name
	$sorter=array();
	$ret=array();
	reset($array);
	foreach ($array as $ii => $va) {
		$sorter[$ii]=$va[$key];
	}
	asort($sorter);
	foreach ($sorter as $ii => $va) {
		$ret[$ii]=$array[$ii];
	}
	$array=$ret;
}

