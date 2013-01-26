<?
/****************************************
**  Steve Beyer Productions
**  Website and Talent Database
**
**  Concept: Steve Beyer
**  Code: Presence
**
**  Last Edit: 20130121
****************************************/

function Init() {
	global $conn;
	global $dirlocation;
	global $pagination;
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
	$pagination = "10";	// number of entries per "page"
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
		"Artists" => "artists",
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
					AdminSaveSingleCategory();
					AdminEditCategories();
					break;
				default:
					AdminEditCategories();
			}
		}
		if ($_REQUEST['url'] == "styles_list") {
			switch($_REQUEST['function']) {
				case "add_style":
					AdminSaveNewStyle();
					AdminListStyles();
					break;
				case "del_style":
					AdminDeleteStyle($_REQUEST['sid']);
					AdminListStyles();
					break;
				case "edit_style":
					AdminEditSingleStyle($_REQUEST['sid']);
					break;
				case "save_style":
					AdminSaveSingleStyle();
					AdminListStyles();
					break;
				default:
					AdminListStyles();
			}
		}
		if ($_REQUEST['url'] == "locations_list") {
			switch($_REQUEST['function']) {
				case "add_location":
					AdminSaveNewLocation();
					AdminListLocations();
					break;
				case "del_location":
					AdminDeleteLocation($_REQUEST['lid']);
					AdminListLocations();
					break;
				case "edit_location":
					AdminEditSingleLocation($_REQUEST['lid']);
					break;
				case "save_location":
					AdminSaveSingleLocation();
					AdminListLocations();
					break;
				default:
					AdminListLocations();
			}
		}
		if ($_REQUEST['url'] == "artists") {
			AdminArtistsButtonBar(); // display that additional nav/button bar
			switch($_REQUEST['function']) {
				case "search":
					echo $_REQUEST['q'];
					break;
				case "list_all":
					AdminArtistList();
					break;
				case "add_new":
					AdminArtistAddNew();
					break;
				case "edit":
					AdminArtistEditSingle($_REQUEST['aid']);
					break;
				default:
					AdminArtistList();
			}
		}
	}
}

function AdminArtistAddNew() {
	if (!isset($_REQUEST['formpage'])) {
		// brand new artist
		AdminArtistFormNew();
	} elseif ($_REQUEST['formpage'] == "1") {
		// attempt to save the artist info and media
		$aid = AdminArtistSaveNew();
		if (strlen($aid) > 0) {
			// there's an $aid from saving basic data, so check that media in
			$filecount = AdminArtistSaveMedia($aid);
			if ($filecount > 1) {
				echo "<div class='AdminSuccess'>New artist added with $filecount media files!</div>";
			} else {
				echo "<div class='AdminError'>No media was saved. Please add a photo and/or video now.</div>";
			}
			// regardless of media, we did save SOMETHING, so sets see it.
			AdminArtistEditSingle($aid);
		} else {
			// Error in first page, redisplay first page.
			AdminArtistFormNew();
		}
	}
}

function AdminArtistEditSingle($aid) {
	global $conn;
	$aid = preg_replace("/[^0-9]/",'',$aid);
	// XXX: this could be some massive joined query
	echo "I'm the AdminArtistEditSingle page<br>";
	$artistinfo = array();
	$query = sprintf("SELECT * FROM `artists` WHERE `aid` = %s", mysqli_real_escape_string($conn,$aid));
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	$artistinfo['aid'] = $aid;
	foreach ($row as $key => $value) {
		$artistinfo[$key] = $value;
	}
	mysqli_free_result($result);
	// lemme have hash of category names
	$categories = array();
	$query = "SELECT `cid` FROM `artistcategories` WHERE `aid` = $aid";
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$cid = $row['cid'];
		$query = "SELECT `category` FROM `categories` WHERE `cid` = $cid";
		$cresult = mysqli_query($conn,$query);
		$crow = mysqli_fetch_assoc($cresult);
		$categories[$cid] = $crow['category'];
	}
	$artistinfo['categories'] = $categories;
	mysqli_free_result($result);
	// lemme have hash of styles
	mysqli_free_result($result);
	// lemme have hash of locations
	mysqli_free_result($result);
	// lemme have hash of media
	mysqli_free_result($result);

	AdminArtistFormSingle($artistinfo);
}

function FigurePageNav($type,$page=1) {
	global $conn;
	global $pagination;
	if ($type == "list_all") {
		$query = "SELECT COUNT(*) FROM `artists`";
	}
	$result = mysqli_query($conn,$query);
	list($count) = mysqli_fetch_array($result);
	// get maximum number of pages
	$maximum = ceil($count/$pagination); // round up to a whole page number
	// get previous page
	if ($page ==  1) {
		$previous = 1;
	} else {
		$previous = abs($page - 1); 
	}
	// get next page
	if ($maximum > $page) {
		$next = ($page + 1);
	} else {
		$next = $page;
	}
	return(
		array(
			"type"=>$type,
			"first"=>1,
			"previous"=>$previous,
			"page"=>$page,
			"next"=>$next,
			"maximum"=>$maximum
		)
	);
}

function AdminArtistSaveNew() {
	// save artist info, locations, styles, categories.
	// then go to AdminArtistSaveMedia() for the media processing
	global $conn;
	(strlen($_REQUEST['name']) > 0)? $name = htmlspecialchars(MakeCase(convert_smart_quotes(trim($_REQUEST['name'])))) : $errors[] = "Please enter the artist or act name.";
	(strlen($_REQUEST['slug']) > 0)? $slug = htmlspecialchars(MakeCase(convert_smart_quotes(trim($_REQUEST['slug'])))) : $errors[] = "Please provide a descriptive phrase about artist.";
	(strlen($_REQUEST['bio']) > 0)? $bio = htmlspecialchars(convert_smart_quotes(trim($_REQUEST['bio']))) : $errors[] = "Missing the artist's bio. Please have at least a paragraph describing the artist.";
	$is_active = isset($_REQUEST['is_active']);
	$is_searchable = isset($_REQUEST['is_searchable']);
	$is_highlighted = isset($_REQUEST['is_highlighted']);
	if (isset($_REQUEST['categories'])) {
		$categories = array();
		foreach ($_REQUEST['categories'] as $key => $value) {
			$categories[$key] = preg_replace("/[^0-9]/","",$value);
		}
	} else {
		$errors[] = "Please select one or more categories for this artist.";
	}
	if (isset($_REQUEST['styles'])) {
		$styles = array();
		foreach ($_REQUEST['styles'] as $key => $value) {
			$styles[$key] = preg_replace("/[^0-9]/","",$value);
		}
	} else {
		$errors[] = "Please select one or more styles of entertainment this artist performs.";
	}
	if (isset($_REQUEST['locations'])) {
		$locations = array();
		foreach ($_REQUEST['locations'] as $key => $value) {
			$locations[$key] = preg_replace("/[^0-9]/","",$value);
		}
	} else {
		$errors[] = "Please select one or more cities that this artist is local to.";
	}
	// check if this is a duplicate // being a real jerk by including the "cleaned" URL text
	$query = sprintf("SELECT `name` FROM `artists` WHERE `name` = '%s' OR `url` = '%s'",
		mysqli_real_escape_string($conn, $name),
		mysqli_real_escape_string($conn, $url)
	);
	$result = mysqli_query($conn,$query);
	if (mysqli_num_rows($result) > 0) {
		$errors[] = "This artist may already exist in the database! Please check the artist's name carefully.";
	}
	// XXX: This guess at an URL is pretty weaksauce
	$url = MakeURL(strtolower($name));
	// insert into artist table and get the auto_incremented aid
	if (!isset($errors)) {
		$query = sprintf("INSERT INTO `artists` (`name`,`url`,`slug`,`bio`,`is_active`,`is_highlighted`,`is_searchable`,`last_updated`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",
			mysqli_real_escape_string($conn, $name),
			mysqli_real_escape_string($conn, $url),
			mysqli_real_escape_string($conn, $slug),
			mysqli_real_escape_string($conn, $bio),
			mysqli_real_escape_string($conn, $is_active),
			mysqli_real_escape_string($conn, $is_highlighted),
			mysqli_real_escape_string($conn, $is_searchable),
			mysqli_real_escape_string($conn, DatePHPtoSQL(time()))
		);
		if (mysqli_query($conn,$query) === TRUE) {
			$aid = mysqli_insert_id($conn);
			foreach($categories as $cid) {
				$query = sprintf("INSERT INTO `artistcategories` (`cid`,`aid`) VALUES (%s,%s)",
					mysqli_real_escape_string($conn, $cid),
					mysqli_real_escape_string($conn, $aid)
				);
				if (mysqli_query($conn,$query) === FALSE) {
					$errors[] = "Error saving category $cid for $aid!" .mysqli_error($conn);
				}
			}
			foreach($styles as $sid) {
				$query = sprintf("INSERT INTO `artiststyles` (`sid`,`aid`) VALUES (%s,%s)",
					mysqli_real_escape_string($conn, $sid),
					mysqli_real_escape_string($conn, $aid)
				);
				if (mysqli_query($conn,$query) === FALSE) {
					$errors[] = "Error saving style $sid for $aid!" .mysqli_error($conn);
				}
			}
			foreach($locations as $lid) {
				$query = sprintf("INSERT INTO `artistlocations` (`lid`,`aid`) VALUES (%s,%s)",
					mysqli_real_escape_string($conn, $lid),
					mysqli_real_escape_string($conn, $aid)
				);
				if (mysqli_query($conn,$query) === FALSE) {
					$errors[] = "Error saving location $lid for $aid!" .mysqli_error($conn);
				}
			}
			echo "<div class='AdminSuccess'>Artist information for <B>$name</B> saved!</div>";
		} else {
			$errors[] = "<B>Did not save new artist!</B> Database Failure: ".mysqli_error($conn);
		}
	}
	if (isset($errors)) {
		echo "<div class='AdminError'><B>There are some missing details preventing us from saving this artist.</B><ul>";
		foreach ($errors as $error) {
			echo "<li>$error</li>";
		}
		echo "</ul></div>\n";
	}
	return($aid); // or null if bad
}

function AdminArtistSaveMedia($aid) {
	global $conn;
	$savedfilecount = 0;
	// page 1's save artist's media
	if (!CheckForFiles()) {
		$errors[] = "No Media Uploaded.";
	} else {
		$newfiles = array();
		// put all uploaded files into the filesystem 
		$newfiles = SaveFile("artist"); // should return an array of [fileid, orig name]
		// step thru each uploaded file and process media
		foreach($newfiles as $key => $newfileinfo) {
			if (strlen($newfiles[$key][0]) < 1) {  // [0] is fileid
				continue; // XXX: This isn't a file, this is just SaveFile() noise
			}
			// make thumbnail
			$newfileid = ResizeImage($newfiles[$key][0],"artist"); 
			// XXX: watermark function here?
			$filename = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $_FILES['filesToUpload']['name'][$key]);
			$mediainfo = MediaInfo($newfileid,"artist"); 
			// Save the media file to database
			$query = sprintf("INSERT INTO `media` (`filename`, `filetype`, 
				`aid`, `name`, `thumbwidth`, `thumbheight`, `width`, `height`, 
				`vidlength`, `is_highlighted`, `viewable`, `published`
				) VALUES ( '%s','%s',%s,'%s',%s,%s,%s,%s,%s,%s,%s,'%s')",
				mysqli_real_escape_string($conn, $newfileid),
				mysqli_real_escape_string($conn, $mediainfo['filetype']),
				mysqli_real_escape_string($conn, preg_replace("/[^0-9]/",'',$aid)),
				mysqli_real_escape_string($conn, $filename),
				preg_replace("/[^0-9]/",'',$mediainfo['thumbwidth']),
				preg_replace("/[^0-9]/",'',$mediainfo['thumbheight']),
				preg_replace("/[^0-9]/",'',$mediainfo['width']),
				preg_replace("/[^0-9]/",'',$mediainfo['height']),
				preg_replace("/[^0-9]/",'',$mediainfo['vidlength']),
				"0",	// No, no highlighted.
				"1",	// Assume yes, is viewable for this initial upload.
				mysqli_real_escape_string($conn, DatePHPtoSQL(time()))
			);
			if (mysqli_query($conn,$query) === TRUE) {
				$savedfilecount++;
			} else {
				echo "<div class='AdminError'>Media file '<B>$filename</B>' not saved in database!<br>". mysqli_error($conn) ."</div>";
			}
		}
	}
	foreach ($errors as $error) { 
		echo "<div class='AdminError'><B>$error</B></div>";
	}
	if (count($errors) > 0) {
		return (0);
	} else {
		return ($savedfilecount);
	}
}

function MediaInfo($fileid,$purpose) {
	global $dirlocation;
	//fileid includes confirmed file extension
	$mediainfo = array();
	if (preg_match("/\.jpg/",$fileid)) {
		list($mediainfo['width'], $mediainfo['height'], $filetype, $attr) = getimagesize("$dirlocation/images/$purpose/original-$fileid");
		list($mediainfo['thumbwidth'], $mediainfo['thumbheight'], $thumbtype, $thumbattr) = getimagesize("$dirlocation/images/$purpose/$fileid");
		$mediainfo['filetype'] = "jpg";
		$mediainfo['vidlength'] = 0;
	} else if (preg_match("/\.png/",$fileid)) {
		list($mediainfo['width'], $mediainfo['height'], $filetype, $attr) = getimagesize("$dirlocation/images/$purpose/original-$fileid");
		list($mediainfo['thumbwidth'], $mediainfo['thumbheight'], $thumbtype, $thumbattr) = getimagesize("$dirlocation/images/$purpose/$fileid");
		$mediainfo['filetype'] = "png";
		$mediainfo['vidlength'] = 0;
	} else if (preg_match("/mp4/",$fileid)) {
		ob_start();
		passthru("/usr/local/bin/ffmpeg -i $dirlocation/images/$purpose/original-$fileid 2>&1");
		$ffmpeg = ob_get_contents();
		ob_end_clean();
		// duration
		preg_match("/Duration: (.*?),/",$ffmpeg,$matches);
		$seconds = explode(":", $matches[1]);
		$mediainfo['vidlength'] = ($seconds[0] * 3600) + ($seconds[1] * 60) + ((int) preg_replace("/\..*/",'',$seconds[2]));
		// resolution 
		preg_match("/Video: (.*?)fps/",$ffmpeg,$matches);
		preg_match("/ (\d+)x(\d+) /",$matches[1],$width);
		$mediainfo['width'] = $width[1];
		$mediainfo['height'] = $width[2];
		$mediainfo['thumbwidth'] = 0;
		$mediainfo['thumbheight'] = 0;
		$mediainfo['filetype'] = "mp4";
	}
	return ($mediainfo);
}

function AdminArtistList() {
	global $conn;
	global $pagination;
	if ($_REQUEST['listpage'] > 0) {
		$page = preg_replace("/[^0-9]/","",$_REQUEST['listpage']);
	} else {
		$page = 1;
	}
	$limit_start = (abs($page - 1) * $pagination);
	$limit_end = $pagination;
	//echo "$limit_start / $limit_end";
	// XXX: Presence -- Figure out what to select, plz
	$query = sprintf("SELECT * FROM `artists` ORDER BY `name` LIMIT %s,%s",
		mysqli_real_escape_string($conn,$limit_start),
		mysqli_real_escape_string($conn,$limit_end)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	AdminArtistListPage($result,$page);
	mysqli_free_result($result);
}

function AdminEditSingleLocation($lid) {
	global $conn;
	$query = sprintf("SELECT `lid`, `city`,`state` FROM `locations` WHERE `lid` = '%s'",
		mysqli_real_escape_string($conn,$lid)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	AdminEditLocation($row);
	mysqli_free_result($result);
}

function AdminListLocations() {
	global $conn;
	$query = "SELECT `lid`, `city`, `state` FROM `locations` ORDER BY `state`, `city`";
	$result = mysqli_query($conn,$query);
	$locationslist = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$locations[] = array(
			"lid" => $row['lid'],
			"city" => $row['city'],
			"state" => StateCodeToName($row['state'])
		);
	}
	mysqli_free_result($result);
	AdminShowLocations($locations);
}

function AdminSaveNewLocation() {
	global $conn;
	if ( (strlen($_REQUEST['city']) == 0) || (strlen($_REQUEST['state']) != 2) ) {
		echo "<div class='AdminError'>Please input both a city and state.</div>";
	} else {
		$city = htmlspecialchars(ucwords(trim($_REQUEST['city'])));
		$state = htmlspecialchars(strtoupper(trim($_REQUEST['state'])));
		$query = sprintf("INSERT INTO `locations` (`city`,`state`) VALUES ('%s','%s')",
			mysqli_real_escape_string($conn,$city),
			mysqli_real_escape_string($conn,$state)
		); // XXX: there may be two legitimate cities named the same, but in different states.  Not allowed currently.
		$statename = StateCodeToName($state); 
		if (mysqli_query($conn,$query) === TRUE) {
			echo "<div class='AdminSuccess'><B>$city, $statename</B> Successfully Added.</div>";
		} else {
			echo "<div class='AdminError'><B>$city, $statename</B> Failed to Save!<br>". mysqli_error($conn) ."</div>";
		}
	}
}

function AdminDeleteLocation($lid) {
	global $conn;
	$sid = preg_replace("/[^0-9]/","",$lid); // input sanitization -- only numbers
	// find all artists using this location in `artistlocations` and clean 'em up
	$query = sprintf("DELETE FROM `artistlocations` WHERE `lid` = '%s'",
		mysqli_real_escape_string($conn,$lid)
	);
	if (mysqli_query($conn,$query) === FALSE) {
		echo "<div class='AdminError'>Whoa, couldn't delete '$lid' from artistlocations. ". mysqli_error($conn) ."</div>";
	}
	$query = sprintf("DELETE FROM `locations` WHERE `lid` = '%s'",
		mysqli_real_escape_string($conn,$lid)
	);
	if (mysqli_query($conn,$query) === TRUE) {
		echo "<div class='AdminSuccess'>Location removed.</div>";
	} else {
		echo "<div class='AdminError'>Hmm, couldn't delete '$lid' from locations.". mysqli_error($conn) ."</div>";
	}
}

function AdminListStyles() {
	global $conn;
	$query = "SELECT `sid`, `name` FROM `styles`";
	$result = mysqli_query($conn,$query);
	$categorieslist = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$styles[] = array(
			"sid" => $row['sid'],
			"name" => $row['name']
		);
	}
	mysqli_free_result($result);
	aasort($styles,"name");
	$quantity = count($styles);
	AdminShowStyles($styles,$quantity);
}

function AdminSaveSingleLocation() {
	// save an existing location that was just edited
	global $conn;
	if ( (strlen($_REQUEST['city']) == 0) || (strlen($_REQUEST['state']) != 2) ) {
		echo "<div class='AdminError'>Please input both a city and state.</div>";
	} else {
		$lid = preg_replace("/[^0-9]/","",$_REQUEST['lid']); // input sanitization -- only numbers
		$city = htmlspecialchars(ucwords(trim($_REQUEST['city'])));
		$state = htmlspecialchars(strtoupper(trim($_REQUEST['state'])));
		$statename = StateCodeToName($state); 
		$query = sprintf("UPDATE `locations` SET `city` = '%s', `state` = '%s' WHERE `lid` = '%s'",
			mysqli_real_escape_string($conn,$city),
			mysqli_real_escape_string($conn,$state),
			mysqli_real_escape_string($conn,$lid)
		);
		if (mysqli_query($conn,$query) === TRUE) {
			echo "<div class='AdminSuccess'>Location <B>$city</B>, <B>$statename</B> [$lid] Successfully Updated.</div>";
		} else {
			echo "<div class='AdminError'>Location <B>$city</B>, <B>$statename</B> [$lid] Failed to Update!<br>". mysqli_error($conn) ."</div>";
		}
	}
}

function AdminSaveSingleStyle() {
	// save an existing style that was just edited
	global $conn;
	$sid = preg_replace("/[^0-9]/","",$_REQUEST['sid']); // input sanitization -- only numbers
	$name = htmlspecialchars(ucwords(trim($_REQUEST['name'])));
	if (strlen($name) == 0) {
		echo "<div class='AdminError'>Please fill in the style's Name.</div>";
	} else {
		$sid = preg_replace("/\[^0-9]/","",trim($_REQUEST['sid']));
		$query = sprintf("UPDATE `styles` SET `name` = '%s' WHERE `sid` = '%s'",
			mysqli_real_escape_string($conn,$name),
			mysqli_real_escape_string($conn,$sid)
		);
		if (mysqli_query($conn,$query) === TRUE) {
			echo "<div class='AdminSuccess'>Style Entry <B>$name</B> [$sid] Successfully Updated.</div>";
		} else {
			echo "<div class='AdminError'>Category Entry <B>$name</B> [$sid] Failed to Update!<br>". mysqli_error($conn) ."</div>";
		}
	}
}

function AdminDeleteStyle($sid) {
	global $conn;
	$sid = preg_replace("/[^0-9]/","",$sid); // input sanitization -- only numbers
	// find all artists using this category in `artistcategories` and clean 'em up
	$query = sprintf("DELETE FROM `artiststyles` WHERE `sid` = '%s'",
		mysqli_real_escape_string($conn,$sid)
	);
	if (mysqli_query($conn,$query) === FALSE) {
		echo "<div class='AdminError'>Whoa, couldn't delete '$sid' from artiststyles. ". mysqli_error($conn) ."</div>";
	}
	// delete the style from `styles`
	$query = sprintf("DELETE FROM `styles` WHERE `sid` = '%s'",
		mysqli_real_escape_string($conn,$sid)
	);
	if (mysqli_query($conn,$query) === TRUE) {
		echo "<div class='AdminSuccess'>Style removed.</div>";
	} else {
		echo "<div class='AdminError'>Hmm, couldn't delete '$sid' from categories.". mysqli_error($conn) ."</div>";
	}
}

function AdminEditSingleStyle($sid) {
	global $conn;
	$query = sprintf("SELECT `sid`,`name` FROM `styles` WHERE `sid` = '%s'",
		mysqli_real_escape_string($conn,$sid)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	AdminEditStyle($row);
	mysqli_free_result($result);
}

function AdminSaveNewStyle() {
	// save a NEW style
	global $conn;
	if (strlen($_REQUEST['name']) == 0) {
		echo "<div class='AdminError'>Please fill in the style's name.</div>";
	} else {
		$name = htmlspecialchars(ucwords(trim($_REQUEST['name'])));
		$query = sprintf("INSERT INTO `styles` (`name`) VALUES ('%s')",
			mysqli_real_escape_string($conn,$name)
		); // I'm trusting that MySQL's UNIQUE will prevent duplicates
		if (mysqli_query($conn,$query) === TRUE) {
			echo "<div class='AdminSuccess'>Style Entry <B>$name</B> Successfully Added.</div>";
		} else {
			echo "<div class='AdminError'>Style Entry <B>$name</B> Failed to Save!<br>". mysqli_error($conn) ."</div>";
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
		if (strlen($_REQUEST['published'])) {
			$published = TRUE;
		} else { 
			$published = FALSE;
		}
		if ($filename) {
			// delete the old category image file from the system
			$query = sprintf("SELECT `image_id` FROM `categories` WHERE `cid` = '%s'", mysqli_real_escape_string($conn,$cid));
			$result = mysqli_query($conn,$query);
			list($old_fileid) = mysqli_fetch_array($result);
			unlink("$dirlocation/images/category/$old_fileid");
			unlink("$dirlocation/images/category/original-$old_fileid"); // XXX: we're not deleting jpegs, only png.
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `published` = '%s', `image_filename` = '%s', `image_id` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'", 
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,$published),
				mysqli_real_escape_string($conn,$filename),
				mysqli_real_escape_string($conn,$newfileid),
				mysqli_real_escape_string($conn,DatePHPtoSQL(time())),
				mysqli_real_escape_string($conn,$cid)
			);
		} else {
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `published` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'", 
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,$published),
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
			"description" => $row['description'],
			"published" => $row['published']
		);
	}
	mysqli_free_result($result);
	aasort($categorieslist,"category");
	AdminShowCategories($categorieslist);
}

function AdminSelectCategories($aid = NULL) {
	// for adding or editing an artist
	// XXX: This does not respect user-defined priority/sequence order from webform or database
	global $conn;
	$categorieslist = array();
	$artistcategories = array();
	$query = "SELECT * FROM `categories` ORDER BY `category`";
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$categorieslist[$row['cid']] = $row['category'];
	}
	mysqli_free_result($result);
	if ($aid) {
		$query = sprintf("SELECT `cid` FROM `artistcategories` WHERE 'aid' = %s",
			mysqli_real_escape_string($conn,$aid)
		);
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$artistcategories[$row['cid']] = TRUE;
		}
	}
	foreach($categorieslist as $cid => $category) {
		$s = 0; // selected flag
		if ($artistcategories[$cid]) {
			// if artist is assigned to category is in the database
			$s++;
		}
		if (($_REQUEST['formpage'] == 1) && (count($_REQUEST['categories']) > 0)) {
			// if this is from the new artist webform...
			if (strlen(array_search($cid,$_REQUEST['categories'])) > 0) { // LAME: array_search returns null for a key that's "0"
				// if artist selected to category from form page 1
				$s++;
			}
		}
		$string .= sprintf("<option value='%s'%s>%s</option>",
			$cid,
			($s > 0)? ' selected' : '',
			$category
		);
	}
	return($string);
}

function AdminSelectStyles($aid = NULL) {
	// for adding or editing an artist // I'm totally duplicating code // please review AdminSelectCategories() above for notes
	global $conn;
	$styleslist = array();
	$artiststyles = array();
	$query = "SELECT * FROM `styles` ORDER BY `name`";
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$styleslist[$row['sid']] = $row['name'];
	}
	mysqli_free_result($result);
	if ($aid) {
		$query = sprintf("SELECT `sid` FROM `artiststyles` WHERE 'aid' = %s",
			mysqli_real_escape_string($conn,$aid)
		);
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$artiststyles[$row['sid']] = TRUE;
		}
	}
	foreach($styleslist as $sid => $name) {
		$s = 0; 
		if ($artiststyles[$sid]) {
			$s++;
		}
		if (($_REQUEST['formpage'] == 1) && (count($_REQUEST['styles']) > 0)) {
			if (strlen(array_search($sid,$_REQUEST['styles'])) > 0) {
				$s++;
			}
		}
		$string .= sprintf("<option value='%s'%s>%s</option>",
			$sid,
			($s > 0)? ' selected' : '',
			$name
		);
	}
	return($string);
}

function AdminSelectLocations($aid = NULL) {
	// for adding or editing an artist // I'm totally duplicating code // please review AdminSelectCategories() above for notes
	global $conn;
	$locationslist = array();
	$artistlocations = array();
	$query = "SELECT * FROM `locations` ORDER BY `state`, `city`";
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$locationslist[$row['lid']] = array($row['city'],$row['state']);
	}
	mysqli_free_result($result);
	if ($aid) {
		$query = sprintf("SELECT `lid` FROM `artistlocations` WHERE 'aid' = %s",
			mysqli_real_escape_string($aid)
		);
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$artistlocations[$row['lid']] = TRUE;
		}
	}
	// insanity to make the dropdown list nice grouped by state
	$oldstate = "";
	foreach($locationslist as $lid => $citystate) {
		if ($citystate[1] != $oldstate) {
			($firstpost)? $string .= "</optgroup>" : $firstpost++;
			$string .= "\n<optgroup label='". StateCodeToName($citystate[1]) ."'>";
			$oldstate = $citystate[1];
		}
		$s = 0;
		if ($artistlocations[$lid]) {
			$s++;
		}
		if (($_REQUEST['formpage'] == 1) && (count($_REQUEST['locations']) > 0)) {
			if (strlen(array_search($lid,$_REQUEST['locations'])) > 0) {
				$s++;
			}
		}
		$string .= sprintf("<option value='%s'%s>%s, %s</option>",
			$lid,
			($s > 0)? ' selected' : '',
			$citystate[0],
			StateCodeToName($citystate[1])
		);
	}
	$string .= "</optgroup>";
	return($string);
}

function AdminSaveNewCategory() {
	// save a NEW category
	global $conn;
	if (strlen($_REQUEST['form_url']) == 0 || strlen($_REQUEST['form_category']) == 0 || strlen($_REQUEST['form_description']) == 0 || (!CheckForFiles())) {
		echo "<div class='AdminError'>Please fill in all three Category name fields and the Category Graphic</div>";
	} else {
		list ($fileid, $filename) = SaveFile("category")[0]; // for Categories, only one image uploaded.
		$newfileid = ResizeImage($fileid,"category"); // 728x90
		$url = preg_replace("/ /","_",strtolower(strip_tags(trim($_REQUEST['form_url']))) );
		$category = htmlspecialchars(ucwords(trim($_REQUEST['form_category'])));
		if (strlen($_REQUEST['published'])) {
			$published = TRUE;
		} else { 
			$published = FALSE;
		}
		$query = sprintf("INSERT INTO `categories` (`url`,`category`,`description`,`published`,`image_filename`,`image_id`, `last_updated`) VALUES ('%s','%s','%s','%s','%s','%s','%s')",
			mysqli_real_escape_string($conn,$url),
			mysqli_real_escape_string($conn,$category),
			mysqli_real_escape_string($conn,htmlspecialchars(ucwords(trim($_REQUEST['form_description'])))),
			mysqli_real_escape_string($conn,$published),
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
	if (preg_match("/\.jpg/",$fileid)) {
		$origimage = imagecreatefromjpeg("$dirlocation/images/$purpose/original-$fileid");
	} elseif (preg_match("/\.png/",$fileid)) {
		$origimage = imagecreatefrompng("$dirlocation/images/$purpose/original-$fileid");
		imagealphablending($origimage, true);
		imagesavealpha($origimage, true); 
	}
	if (preg_match("/category/",$purpose)) {
		$width = 728;
		$height = 90;
	}
	if (preg_match("/artist/",$purpose)) {
		$height = 450;
		$width = abs(round( (imagesX($origimage) / imagesY($origimage)) * $height ));
	}
	if ($origimage) {
		$newimage = imagecreatetruecolor($width,$height);
		imagesavealpha($newimage, true); 
		$color = imagecolorallocatealpha($newimage,0x00,0x00,0x00,127);
		imagefill($newimage, 0, 0, $color); 
		// dest , src , x dest, y dest , x src , y src , dest w, dest h, src w, src h
		if (!imagecopyresampled($newimage,$origimage,0, 0, 0, 0, $width, $height, imagesX($origimage), imagesY($origimage))) {
			echo "<div class='AdminError'>Image No Web Resize/Compress WTF $fileid</div>";
		}
		// if its a category, only do a transparent png.  Artist, whatever came in.
		if (preg_match("/\.jpg/",$fileid) && (!preg_match("/category/",$purpose))) {
			$newfilename = substr($fileid,0,-4) . ".jpg";
			imagejpeg($newimage, "$dirlocation/images/$purpose/$newfilename", 80); // http://www.ebrueggeman.com/blog/php_image_optimization
		} else if (preg_match("/\.png/",$fileid)) {
			$newfilename = substr($fileid,0,-4) . ".png";
			imagepng($newimage, "$dirlocation/images/$purpose/$newfilename",9);
		}
		imagedestroy($origimage);
		imagedestroy($newimage);
	}
	if (preg_match("/\.mp4/",$fileid)) {
		// create a thumbnail here?
		$newfilename = $fileid;
	}
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
		$gotafile = FALSE;
		//make the filename safe
		$filename = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $_FILES['filesToUpload']['name'][$ref]);
		$errorIndex = $_FILES['filesToUpload']['error'][$ref];
		if ($errorIndex > 0) {
			if ( ($errorIndex != 4) && (!$gotafile) ) {
				// listen, we got at least one file, so I no longer care about "no file uploaded" errors.
				$error_message = $error_types[$_FILES['filesToUpload']['error'][$ref]]; 
				echo "<div class='AdminError'>File Upload Error: $error_message.</div>";
   			$happyuploads[] = array(NULL,NULL);
			}
		} else {
			$fileid = uniqid();
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
				$gotafile = TRUE;
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

function StatesArray() {
	return(array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa",  'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland", 'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma", 'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming",'UK'=>'United Kingdom','AU'=>"Australia",'MX'=>"Mexico",'CN'=>"China",'CD'=>"Canada"));
}

function StateCodeToName($code) {
	$states = StatesArray();
	return($states[$code]);
}

function StateNameToCode($state) {
	$states = StatesArray();
	return(array_search($state, $states)); 
}

function StateOptionsDropDown($active) {
	// show a dropdown of states with the active state highlighted, or just zend a zero for nuffin
	$states = StatesArray();
	$string = "";
	foreach($states as $code => $state) {
		$string .= sprintf("<option value='%s'%s>%s</option>",
			$code,
			($active == $code)? ' selected="SELECTED"' : '', // yeah bitches
			$state
		);	
	}
	return($string);
}

function convert_smart_quotes($string) { 
	$search = array(
		chr(145), 
		chr(146), 
		chr(147), 
		chr(148), 
		chr(151)
	); 
	$replace = array(
		"'", 
		"'", 
		'"', 
		'"', 
		'-'
	); 
	return str_replace($search, $replace, $string); 
} 

function MakeURL($str, $replace=array(), $delimiter='-') {
	setlocale(LC_ALL, 'en_US.UTF8');
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	return $clean;
}

//Converts a string to Title Case based on one set of title case rules
// put <no_parse></no_parse> around content that you don't want to be parsed by the title case rules
function MakeCase($string) {
	//remove no_parse content
	$string_array = preg_split("/(<no_parse>|<\/no_parse>)+/i",$string);
	$newString = "";
	for ($k=0; $k<count($string_array); $k=$k+2) {
		$string = $string_array[$k];
		//if the entire string is upper case dont perform any title case on it
		if ($string != strtoupper($string)){
			//TITLE CASE RULES:
			//1.) uppercase the first char in every word
			$new = preg_replace("/(^|\s|\'|'|\"|-){1}([a-z]){1}/ie","''.stripslashes('\\1').''.stripslashes(strtoupper('\\2')).''", $string);
			//2.) lower case words exempt from title case
			// Lowercase all articles, coordinate conjunctions ("and", "or", "nor"), and prepositions regardless of length, when they are other than the first or last word.
			// Lowercase the "to" in an infinitive." - this rule is of course aproximated since it is contex sensitive
			$matches = array();
			// perform recusive matching on the following words
			preg_match_all("/(\sof|\sa|\san|\sthe|\sbut|\sor|\snot|\syet|\sat|\son|\sin|\sover|\sabove|\sunder|\sbelow|\sbehind|\snext\sto|\sbeside|\sby|\samoung|\sbetween|\sby|\still|\ssince|\sdurring|\sfor|\sthroughout|\sto|\sand){2}/i",$new ,$matches);
			for ($i=0; $i<count($matches); $i++) {
				for ($j=0; $j<count($matches[$i]); $j++){
					$new = preg_replace("/(".$matches[$i][$j]."\s)/ise","''.strtolower('\\1').''",$new);
 				}
			}
			//3.) do not allow upper case appostraphies
			$new = preg_replace("/(\w'S)/ie","''.strtolower('\\1').''",$new);
			$new = preg_replace("/(\w'\w)/ie","''.strtolower('\\1').''",$new);
			$new = preg_replace("/(\W)(of|a|an|the|but|or|not|yet|at|on|in|over|above|under|below|behind|next to| beside|by|amoung|between|by|till|since|durring|for|throughout|to|and)(\W)/ise","'\\1'.strtolower('\\2').'\\3'",$new);
			//4.) capitalize first letter in the string always
			$new = preg_replace("/(^[a-z]){1}/ie","''.strtoupper('\\1').''", $new);
			//5.) replace special cases
			// SBP add to this as find case specific problems
			$new = preg_replace("/\sin-/i"," In-",$new);
			$new = preg_replace("/(\W|^){1}(cross){1}(\s){1}(connection){1}(\W|$){1}/ie","'\\1\\2-\\4\\5'",$new); //always hyphonate cross-connections
			$new = preg_replace("/(\s|\"|\'){1}(vs\.){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1Vs.\\3'",$new);
			$new = preg_replace("/(\s|\"|\'){1}(on-off){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1On-Off\\3'",$new);
			$new = preg_replace("/(\s|\"|\'){1}(on-site){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1On-Site\\3'",$new);
			$new = stripslashes($new);
			$string_array[$k] = $new;
		} 
	}
	for ($k=0; $k<count($string_array); $k++){
		$newString .= $string_array[$k];
	}
	return($newString); 
};

