<?
/****************************************
**  Steve Beyer Productions
**  Website and Talent Database
**
**  Concept: Steve Beyer
**  Code: Presence
**
**  Last Edit: 20121109
****************************************/

function Init() {
	global $conn;
	global $dirlocation;
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
	$dirlocation = "/home/presence/samba_public_share/sbp_app";	// no trailing slash.
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
		<div class="metricsOverviewBlock">
		<div class="metricsBlock">
			<div class="metricsHeader">Website Activity - 7 Days</div>
			<? foreach ($domains as $domain) { ?>
				<div class="metricsDomain"><?= $domain; ?></div>
				<div class="metricsLabel">Visitors:</div>
				<div class="metricsValue"><?= count($visitors[$domain]); ?></div>
				<div class="metricsLabel">Hits:</div>
				<div class="metricsValue"><?= $hits[$domain] + 0; ?></div>
			<? } ?>
		</div> <!-- class="metricsBlock" -->
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
	?>
		<div class="metricsBlock">
			<div class="metricsHeader">Website Activity - 24 Hours</div>
			<? foreach ($domains as $domain) { ?>
				<div class="metricsDomain"><?= $domain; ?></div>
				<div class="metricsLabel">Visitors:</div>
				<div class="metricsValue"><?= count($visitors[$domain]); ?></div>
				<div class="metricsLabel">Hits:</div>
				<div class="metricsValue"><?= $hits[$domain] + 0; ?></div>
			<? } ?>
		</div> <!-- class="metricsBlock" -->
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
	?>
		<div class="metricsBlock">
			<div class="metricsHeader">Website Activity - 60 Minutes</div>
			<? foreach ($domains as $domain) { ?>
				<div class="metricsDomain"><?= $domain; ?></div>
				<div class="metricsLabel">Visitors:</div>
				<div class="metricsValue"><?= count($visitors[$domain]); ?></div>
				<div class="metricsLabel">Hits:</div>
				<div class="metricsValue"><?= $hits[$domain] + 0; ?></div>
			<? } ?>
		</div> <!-- class="metricsBlock" -->
		</div> <!-- class="MetricsOverviewBlock" -->
	<?
}

function ShowAdminPage() {
	$adminfunctions = array(
		"Overview" => "",
		"Artists" => "artist_editor",
		"Categories" => "categories_list",
		"Styles" => "styles_list",
		"Locations" => "locations_list",
		"Featured" => "categories_featured",
		"Blog" => "blog_editor",
		"Metrics" => "web_stats"
	);
	include("templates/admin.php");
	AdminHead($_REQUEST['url'],$adminfunctions);
	AdminNav($_REQUEST['url'],$adminfunctions);
	if ((strlen($_REQUEST['url']) == 0) || ($_REQUEST['url'] == "web_stats")) {
		AdminDisplaySiteStats();
	} else {
		if ($_REQUEST['url'] == "categories_list") {
			switch($_REQUEST['function']) {
				case "add_category":
					AdminSaveNewCategory();
					AdminEditCategories();
					break;
				case "del_category":
					AdminDeleteCategory($_REQUEST['categoryurl']);
					break;
				case "del_category_for_reals":
					AdminDeleteCategoryGo($_REQUEST['targetcategoryurl']);
					AdminEditCategories();
					break;
				case "edit_category":
					AdminEditSingleCategory($_REQUEST['categoryurl']);
					break;
				case "save_category":
					AdminSaveSingleCategory($_REQUEST['cid']);
					AdminEditCategories();
					break;
				default:
					AdminEditCategories();
			}
		}
	}
}

function AdminEditSingleCategory($targetcategoryurl) {
	global $conn;
	$query = sprintf("SELECT * FROM `categories` WHERE `url` = '%s'",
		mysqli_real_escape_string($conn,$targetcategoryurl)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	AdminEditCategory($row);
	mysqli_free_result($result);
}

function AdminSaveSingleCategory($cid) {
	// save an existing category that was just edited
	global $conn;
	global $dirlocation;
	if (CheckForFiles()) {
		list ($fileid, $filename) = SaveFile("category")[0]; // for Categories, only one image uploaded.
		$newfileid = ResizeImage($fileid,"category"); // 728x90
	}
	if (strlen($_REQUEST['form_url']) == 0 || strlen($_REQUEST['form_category']) == 0 || strlen($_REQUEST['form_description']) == 0) {
		echo "<div class='AdminError'>Please fill in all three Category fields</div>";
	} else {
		$cid = preg_replace("/\[^0-9]/","",trim($_REQUEST['form_cid']));
		$url = preg_replace("/ /","_",strtolower(strip_tags(trim($_REQUEST['form_url']))) );
		$category = htmlspecialchars(trim($_REQUEST['form_category']));
		if ($filename) {
			// delete the old category image file from the system
			$query = sprintf("SELECT `image_id` FROM `categories` WHERE `cid` = '%s'", mysqli_real_escape_string($conn,$cid));
			$result = mysqli_query($conn,$query);
			list($old_fileid) = mysqli_fetch_array($result);
			unlink("$dirlocation/images/category/$old_fileid");
			unlink("$dirlocation/images/category/original-$old_fileid"); // XXX: we're not deleting jpegs, only png.
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `image_filename` = '%s', `image_id` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'", 
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,$filename),
				mysqli_real_escape_string($conn,$newfileid),
				mysqli_real_escape_string($conn,DatePHPtoSQL(time())),
				mysqli_real_escape_string($conn,$cid)
			);
		} else {
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'", 
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,DatePHPtoSQL(time())),
				mysqli_real_escape_string($conn,$cid)
			);
		}
		if (mysqli_query($conn,$query) === TRUE) {
			echo "<div class='AdminSuccess'>Category Entry <B>$category</B> [$url] Successfully Updated.</div>";
		} else {
			echo "<div class='AdminError'>Category Entry <B>$category</B> [$url] Failed to Update!<br>". mysqli_error($conn) ."</div>";
		}
	}
}

function AdminDeleteCategory($targetcategoryurl) {
	$nextfunction = "del_category_for_reals";
	$url = "categories_list";
	AdminShowDeleteConfirmation($targetcategoryurl,$url,$nextfunction);
}

function AdminDeleteCategoryGo($targetcategoryurl) {
	global $conn;
	global $dirlocation;
	// fetch us the category id for tihs url
	// XXX: could this be some sort of joined thing?  Yeah.  Am I doing it?  Dunno how.  Does it matter?  Not this time.
	$query = sprintf("SELECT `cid`,`image_id` FROM `categories` WHERE `url` = '%s'",
		mysqli_real_escape_string($conn,$targetcategoryurl)
	);
	$result = mysqli_query($conn,$query);
	list($cid,$fileid) = mysqli_fetch_array($result);
	if (strlen($cid) == 0) {
		echo "<div class='AdminError'>Huh, I couldn't retrieve the cid from $targetcategoryurl.". mysqli_error($conn) ."</div>";
	}
	// find all artists using this category in `artistcategories` and clean 'em up
	$query = sprintf("DELETE FROM `artistcategories` WHERE `cid` = '%s'",
		mysqli_real_escape_string($conn,$cid)
	);
	if (mysqli_query($conn,$query) === FALSE) {
		echo "<div class='AdminError'>Whoa, couldn't delete '$cid' from artistcategories.". mysqli_error($conn) ."</div>";
	}
	$artists = array();	
	// delete the category from `categories`
	$query = sprintf("DELETE FROM `categories` WHERE `cid` = '%s'",
		mysqli_real_escape_string($conn,$cid)
	);
	// delete the category image file from the system
	unlink("$dirlocation/images/category/$fileid");
	unlink("$dirlocation/images/category/original-$fileid"); // XXX: we're not deleting jpegs, only png.
	if (mysqli_query($conn,$query) === TRUE) {
		echo "<div class='AdminSuccess'>The light is green, the trap is clean.</div>";
	} else {
		echo "<div class='AdminError'>Hmm, couldn't delete '$cid' from categories.". mysqli_error($conn) ."</div>";
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
	// save a NEW category
	global $conn;
	if (CheckForFiles()) {
		list ($fileid, $filename) = SaveFile("category")[0]; // for Categories, only one image uploaded.
	} else {
		echo "<div class='AdminError'>No Category Image Selected!</div>";
	}
	if (strlen($_REQUEST['form_url']) == 0 || strlen($_REQUEST['form_category']) == 0 || strlen($_REQUEST['form_description']) == 0 || strlen($fileid == 0)) {
		echo "<div class='AdminError'>Please fill in all three Category name fields and the Category Graphic</div>";
	} else {
		$newfileid = ResizeImage($fileid,"category"); // 728x90
		$url = preg_replace("/ /","_",strtolower(strip_tags(trim($_REQUEST['form_url']))) );
		$category = htmlspecialchars(ucwords(trim($_REQUEST['form_category'])));
		$query = sprintf("INSERT INTO `categories` (`url`,`category`,`description`,`image_filename`,`image_id`, `last_updated`) VALUES ('%s','%s','%s','%s','%s','%s')",
			mysqli_real_escape_string($conn,$url),
			mysqli_real_escape_string($conn,$category),
			mysqli_real_escape_string($conn,htmlspecialchars(ucwords(trim($_REQUEST['form_description'])))),
			mysqli_real_escape_string($conn,$filename),
			mysqli_real_escape_string($conn,$newfileid),
			mysqli_real_escape_string($conn,DatePHPtoSQL(time()))
		);
		if (mysqli_query($conn,$query) === TRUE) {
			echo "<div class='AdminSuccess'>Category Entry <B>$category</B> [$url] Successfully Added.</div>";
		} else {
			echo "<div class='AdminError'>Category Entry <B>$category</B> [$url] Failed to Save!<br>". mysqli_error($conn) ."</div>";
		}
	}
}

function ResizeImage($fileid,$purpose) {
	// Resize image according to its purpose
	global $dirlocation;
	if (preg_match("/category/",$purpose)) {
		$width = 728;
		$height = 90;
	}
	$newimage = imagecreatetruecolor($width,$height);
	imagesavealpha($newimage, true); 
	$color = imagecolorallocatealpha($newimage,0x00,0x00,0x00,127);
	imagefill($newimage, 0, 0, $color); 
	if (preg_match("/\.jpg/",$fileid)) {
		$origimage = imagecreatefromjpeg("$dirlocation/images/$purpose/original-$fileid");
	} elseif (preg_match("/\.png/",$fileid)) {
		$origimage = imagecreatefrompng("$dirlocation/images/$purpose/original-$fileid");
		imagealphablending($origimage, true);
		imagesavealpha($origimage, true); 
	}
	// dest , src , x dest, y dest , x src , y src , dest w, dest h, src w, src h
	if (!imagecopyresampled($newimage,$origimage,0, 0, 0, 0, $width, $height, imagesX($origimage), imagesY($origimage))) {
		echo "<div class='AdminError'>Category Image No Web Resize/Compress WTF $fileid</div>";
	}
	//imagejpeg($origimage, "$dirlocation/images/$purpose/$fileid", 80); // http://www.ebrueggeman.com/blog/php_image_optimization
	$newfilename = substr($fileid,0,-4) . ".png";
	imagepng($newimage, "$dirlocation/images/$purpose/$newfilename",9);
	imagedestroy($origimage);
	imagedestroy($newimage);
	return($newfilename);
}

function CheckForFiles() {
	$count = 0;
	foreach ($_FILES['filesToUpload']['error'] as $status){
		if ($status === UPLOAD_ERR_OK) {
			$count++;
		}
	}
	return ($count);
}

function SaveFile($purpose) {
	// save a form's files into their purpose's directory as a unique ID, returning back the id and "file name"
	global $dirlocation;
	$fileid = uniqid();
	$happyuploads = array(); // the array of ids & names of the numerous uploaded files
	$error_types = array(
		1=>"Your file is too large for the server",
		2=>"Your file is larger than expected",
		3=>"Your file upload incomplete, only partially uploaded",
		4=>"You did not select a file, so no file uploaded",
		6=>"Server problem with the temp directory",
		7=>"Server failed to write file to disk",
		8=>"Server PHP extension prevented upload"
	);
	foreach ($_FILES['filesToUpload']['tmp_name'] as $ref => $tmp_name) {
		//make the filename safe
		$filename = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $_FILES['filesToUpload']['name'][$ref]);
		$errorIndex = $_FILES['filesToUpload']['error'][$ref];
		if ($errorIndex > 0) {
			$error_message = $error_types[$_FILES['filesToUpload']['error'][$ref]]; 
			echo "<div class='AdminError'>File Upload Error: $error_message.</div>";
   			$happyuploads[] = array(NULL,NULL);
		} else {
			// XXX: I am a race condition, where my unconfirmed file name is exposed on the webs
			move_uploaded_file($tmp_name, $dirlocation . "/images/" . $purpose  . "/original-" . $fileid );
			if (filesize($dirlocation . "/images/" . $purpose . "/original-" . $fileid) < 1024) {
				// if the file is smaller than 1kb, I don't trust it.
				unlink($dirlocation . "/images/" . $purpose . "/original-" . $fileid);
				echo "<div class='AdminError'>File Upload Error: File is invalid due to small size.</div>";
				$happyuploads[] = array(NULL,NULL);
			} else {
				// Yay, its a file!  Lets totally blow off the given file name and replace with my own.
				$finfo = finfo_open(FILEINFO_MIME);
				$type = finfo_file($finfo, $dirlocation . "/images/" . $purpose . "/original-" . $fileid);
				if (preg_match("/jpeg/i",$type)) {
					$newfileid = "$fileid.jpg";	
				} elseif (preg_match("/png/i",$type)) {
					$newfileid = "$fileid.png";
				} elseif (preg_match("/mp4/i",$type)) {
					$newfileid = "$fileid.mp4";
				} elseif (preg_match("/word/i",$type)) {
					$newfileid = "$fileid.doc";
				} elseif (preg_match("/excel/i",$type)) {
					$newfileid = "$fileid.xls";
				} elseif (preg_match("/pdf/i",$type)) {
					$newfileid = "$fileid.pdf";
				} else {
					$newfileid = $fileid;
				}
				rename (
					$dirlocation . "/images/" . $purpose . "/original-". $fileid,
					$dirlocation . "/images/" . $purpose . "/original-". $newfileid 
				);
				$happyuploads[] = array($newfileid,$filename);
			}
		}
	}
	// We accepted a positive number of files !
	return $happyuploads;
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

//Takes a password and returns the salted hash
//$password - the password to hash
//returns - the hash of the password (192 hex characters)
function HashPassword($password) {
    $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); //get 256 random bits in hex
    $hash = hash("sha512", $salt . $password); //prepend the salt, then hash
    //store the salt and hash in the same string, so only 1 DB column is needed
    $final = $salt . $hash;
    return $final;
}

//Validates a password
//$hash - the hash created by HashPassword (stored in your DB)
//$password - the password to verify
//returns - true if the password is valid, false otherwise.
function ValidatePassword($password, $correctHash) {
    $salt = substr($correctHash, 0, 64); //get the salt from the front of the hash
    $validHash = substr($correctHash, 64, 128); //the SHA512
    $testHash = hash("sha256", $salt . $password); //hash the password being tested
    //if the hashes are exactly the same, the password is valid
    return $testHash === $validHash;
}

function nicetime($date) {
	if(empty($date)) {
		return "ERROR: No date provided";
	}
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	$now = time();
	$unix_date = strtotime($date);
	// check validity of date
	if(empty($unix_date)) {
		return "ERROR: Invalid date";
	}
	// is it future date or past date
	if($now > $unix_date) {
		$difference = $now - $unix_date;
		$tense = "ago";
	} else {
		$difference = $unix_date - $now;
		$tense = "from now";
	} for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
	if($difference != 1) {
	//  $periods[$j] .= "s"; // plural for English language
		$periods = array("seconds", "minutes", "hours", "days", "weeks", "months", "years", "decades"); // plural for international words
	}
	return "$difference $periods[$j] {$tense}";
}
