<?
/****************************************
**  Steve Beyer Productions
**  Website and Talent Database
**
**  Concept: Steve Beyer
**  Code: Presence
**
**  Last Edit: 20130423
****************************************/

function Init() {
	global $conn;
	global $dirlocation;
	global $pagination;
	global $videoheight;
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
	$dirlocation = "/home/presence/sbp_web";	// no trailing slash.
	$host  = "localhost";
	$db    = "sbpweb";
	require_once("db.php");
	$conn = mysqli_connect($host, $user, $pass, $db) or die(mysqli_error());
	$pagination = "10";	// number of entries per "page"
	$videoheight = 300;
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
	if ((strlen($_REQUEST['url']) === 0) || ((string)$_REQUEST['url'] === "web_stats")) {
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
					switch ($_REQUEST['executeButton']) {
						case "delete":
							AdminArtistDelete($_REQUEST['aid']);	// XXX: it's actually the AID, not url, but whatever.
							break;
						case "update":
							// XXX ASDF DO ME
							echo "yay updates";
							AdminArtistEditSingle(preg_replace("/[^0-9]/","",$_REQUEST['aid']));
							break;
						default:
							if ($_REQUEST['listpage'] > 0) {
								AdminArtistEditSingle(preg_replace("/[^0-9]/","",$_REQUEST['listpage'])); // hack for direct URL access
							} else {
								AdminArtistEditSingle(preg_replace("/[^0-9]/","",$_REQUEST['aid']));
							}
					}
				case "del_artist_for_reals":
					AdminArtistDeleteGo($_REQUEST['aid']);
					AdminArtistList();
					break;
				default:
					AdminArtistList();
			}
		}
	}
}

function AdminArtistDelete($aid) {
	$nextfunction = "del_artist_for_reals";
	$urlDo = "artists";
	$urlCancel = "artists/edit/$aid";
	$desc = $aid;
	AdminShowDeleteConfirmation($aid,$desc,$urlDo,$urlCancel,$nextfunction);
}

function AdminArtistAddNew() {
	if (!isset($_REQUEST['formpage'])) {
		// brand new artist
		AdminArtistFormNew();
	} elseif ((string)$_REQUEST['formpage'] === "1") {
		// attempt to save the artist info and media
		$aid = AdminArtistSaveNew();
		if (strlen($aid) > 0) {
			// there's an $aid from saving basic data, so check that media in
			$filecount = AdminArtistSaveMedia($aid);
			if ($filecount >= 1) {
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
	// echo "I'm the AdminArtistEditSingle page<br>";
	$artistinfo = array();
	$query = sprintf("SELECT * FROM `artists` WHERE `aid` = %s", mysqli_real_escape_string($conn,$aid));
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	$artistinfo['aid'] = $aid;
	foreach ($row as $fieldname => $value) {
		$artistinfo[$fieldname] = $value;
	}
	mysqli_free_result($result);
	// AdminSelectCategories($aid) AdminSelectStyles($aid) AdminSelectLocations($aid)
	// lemme have hash of media
	$query = sprintf("SELECT * FROM `media` WHERE `aid` = %s", mysqli_real_escape_string($conn,$aid));
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$artistinfo['media']['mid'][$row['mid']] = $row['mid'];
		$artistinfo['media']['name'][$row['mid']] = $row['name'];
		$artistinfo['media']['filetype'][$row['mid']] = $row['filetype'];
		$artistinfo['media']['filename'][$row['mid']] = $row['filename'];
		$artistinfo['media']['thumbwidth'][$row['mid']] = $row['thumbwidth'];
		$artistinfo['media']['thumbheight'][$row['mid']] = $row['thumbheight'];
		$artistinfo['media']['height'][$row['mid']] = $row['height'];
		$artistinfo['media']['width'][$row['mid']] = $row['width'];
		$artistinfo['media']['vidlength'][$row['mid']] = $row['vidlength'];
		$artistinfo['media']['is_highlighted'][$row['mid']] = $row['is_highlighted'];
		$artistinfo['media']['viewable'][$row['mid']] = $row['viewable'];
		$artistinfo['media']['published'][$row['mid']] = DateSQLtoPHP($row['published']);
	}
	mysqli_free_result($result);
	// Before showing this mess, mebe we should check for any updates that were maybe submitted?
	$artistinfo = AdminArtistFormSingleSaveChanges($artistinfo);
	AdminArtistFormSingle($artistinfo);
}

function AdminArtistFormSingleSaveChanges($artistinfo) {
	// compare any $_REQUEST stuff with existing database, update.
	// check for new file uploads, deal with.
	// return the (adjusted) $artistinfo hash
	return($artistinfo);
}

function PrepareVideoPlayer($input) {
	// Put video(s) into jwplayer
	global $conn;
	global $videoheight;
	if (is_array($input)) {
		$artistinfo = $input;
		$videocount = 0;
		// I am the artistinfo's media keyed array
		// If this is used, SHOW ALL (viewable) VIDEOS
		foreach ($artistinfo['media']['mid'] as $mid) {
			// if in the admin page, or is viewable, and media is a video, ...
			if (((string)$_REQUEST['page'] === 'admin' OR (string)$artistinfo['media']['viewable'][$mid] == '1') AND ($artistinfo['media']['vidlength'][$mid] > 0)) {
				$videocount++;
				// single out the one media ID for the Video Player
				$tempartistinfo = $artistinfo;
				unset ($tempartistinfo['media']);	// dump all the media info on this artist, replacing with the one video to display
				// make video players a reasonable size
				if ($artistinfo['media']['height'][$mid] > $videoheight) {
					$width = $artistinfo['media']['width'][$mid];
					$height = $artistinfo['media']['height'][$mid];
					$scale = $height / $videoheight;
					$tempartistinfo['media']['width'] = ceil($width / $scale);
					$tempartistinfo['media']['height'] = ceil($height / $scale);
				} else {
					$tempartistinfo['media']['width'] = $artistinfo['media']['width'][$mid];
					$tempartistinfo['media']['height'] = $artistinfo['media']['height'][$mid];
				}
				$tempartistinfo['media']['realdimensions'] = $artistinfo['media']['width'][$mid] . "x" . $artistinfo['media']['height'][$mid];
				$tempartistinfo['media']['mid'] = $artistinfo['media']['mid'][$mid];
				$tempartistinfo['media']['previewimage'] = substr($artistinfo['media']['filename'][$mid],0,-4) . ".jpg";
				$tempartistinfo['media']['vidlength'] = $artistinfo['media']['vidlength'][$mid];
				$tempartistinfo['media']['name'] = $artistinfo['media']['name'][$mid];
				$tempartistinfo['media']['filename'] = $artistinfo['media']['filename'][$mid];
				$tempartistinfo['media']['fileid'] = substr($artistinfo['media']['filename'][$mid], 0, -4);
				$tempartistinfo['media']['is_highlighted'] = $artistinfo['media']['is_highlighted'][$mid];
				$tempartistinfo['media']['viewable'] = $artistinfo['media']['viewable'][$mid];
				$tempartistinfo['media']['published'] = $artistinfo['media']['published'][$mid];
				if ((string)$artistinfo['media']['viewable'][$mid] === '1') {
					$tempartistinfo['classname'] = "VideoPlayer";
				} else {
					$tempartistinfo['classname'] = "VideoPlayerNOVIEW";
				}
				DisplayVideoPlayer($tempartistinfo);
				AdminVideoPreviewChooser($tempartistinfo);
			}
		}
		if (($_REQUEST['page'] === 'admin') && ((string)$videocount === '0')) {
			echo "<div class='AdminError'>No Videos Available for this Artist!</div>";
		}
	} elseif (is_string($input) || is_int($input)) {
		// I just got a media ID only, lemme populate with all info
		// If this is used, show THIS ONE video
		$mid = preg_replace("/[^0-9]/","",$input);
		$artistinfo = array();
		$query = sprintf("SELECT * FROM `media` WHERE `mid` = %s",
			mysqli_real_escape_string($conn,$mid)
		);
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		foreach ($row as $fieldname => $value) {
			$artistinfo['media'][$fieldname] = $value;
		}
		mysqli_free_result($result);
		if ($artistinfo['media']['viewable'] == 0) {
			echo "<div class='AdminError'>Video not available.</div>";
		} else {
			// now lemme get some meta data of the Artist for the player
			// XXX: duplicated from AdminArtistEditSingle
			$query = sprintf("SELECT * FROM `artists` WHERE `aid` = %s", mysqli_real_escape_string($conn,$artistinfo['media']['aid']));
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$artistinfo['aid'] = $aid;
			foreach ($row as $fieldname => $value) {
				$artistinfo[$fieldname] = $value;
			}
			mysqli_free_result($result);
			// scale the video if necessary
			if ($artistinfo['media']['height'][$mid] > $videoheight) {
				$width = $artistinfo['media']['width'][$mid];
				$height = $artistinfo['media']['height'][$mid];
				$scale = $height / $videoheight;
				$artistinfo['media']['width'] = ceil($width / $scale);
				$artistinfo['media']['height'] = ceil($height / $scale);
			}
			DisplayVideoPlayer($artistinfo);
		}
	}
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
	// XXX: we don't deal with CR/newlines or html/markup at all in bio field yet!
	(strlen($_REQUEST['bio']) > 0)? $bio = htmlspecialchars(convert_smart_quotes(trim($_REQUEST['bio']))) : $errors[] = "Missing the artist's bio. Please have at least a paragraph describing the artist.";
	if (strlen($_REQUEST['display_name']) > 0) {
		$display_name = htmlspecialchars(MakeCase(convert_smart_quotes(trim($_REQUEST['display_name']))));
	} else {
		// crappy way of guessing a band's obfuscated "display" name: first whole word, then first letter of each additional word
		// unless only two words, then initials all the way
		// logic subject to be totally changed on SB's whim
		$words = explode(" ", $name);
		$display_name = "";
		$counter = 0;
		if (count($words) > 0) {
			if (count($words) == 1) {
				$display_name = $name;	// one word artist name is a one-word artist name.
			} elseif (count($words) == 2) {
				foreach ($words as $word) {
					$display_name .= substr($word,0,1);
					$display_name .= ".";
				}
			} elseif (count($words) >= 3) {
				foreach ($words as $word) {
					if ($counter == 0) {
						$display_name = "$word ";
					} else {
						$display_name .= substr($word,0,1);
						$display_name .= ".";
					}
					$counter++;
				}
			}
		}
	}
	$use_display_name = isset($_REQUEST['use_display_name']);
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
	$alt_url = MakeURL(strtolower($display_name));	// what's the URL if we're in use_display_name mode?  XXX: This is pretty retarded. I want full names in URL for SEO.  even specifiying an URL at all is unnecessary since going to just search on name anyways.
	// insert into artist table and get the auto_incremented aid
	if (!isset($errors)) {
		$query = sprintf("INSERT INTO `artists` (`name`,`display_name`,`url`,`alt_url`,`slug`,`bio`,`use_display_name`,`is_active`,`is_highlighted`,`is_searchable`,`last_updated`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
			mysqli_real_escape_string($conn, $name),
			mysqli_real_escape_string($conn, $display_name),
			mysqli_real_escape_string($conn, $url),
			mysqli_real_escape_string($conn, $alt_url),
			mysqli_real_escape_string($conn, $slug),
			mysqli_real_escape_string($conn, $bio),
			mysqli_real_escape_string($conn, $use_display_name),
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
			if ($savedfilecount == 0) {
				$highlightmeplz = "1";	// First uploaded file is highlighted. Crude.
			} else {
				$highlightmeplz = "0";	// No, no highlighted.
			}
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
				$highlightmeplz,
				"1",	// Assume yes, media file is viewable for this initial upload.
				mysqli_real_escape_string($conn, DatePHPtoSQL(time()))
			);
			if (mysqli_query($conn,$query) === TRUE) {
				$savedfilecount++;
			} else {
				$errors[] = "Media file '<i>$filename</i>' not saved in database!<br>". mysqli_error($conn);
			}
		}
	}
	if (count($errors) > 0) {
		foreach ($errors as $error) {
			echo "<div class='AdminError'><B>$error</B></div>";
		}
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
		list($mediainfo['width'], $mediainfo['height'], $filetype, $attr) = getimagesize("$dirlocation/i/$purpose/original-$fileid");
		list($mediainfo['thumbwidth'], $mediainfo['thumbheight'], $thumbtype, $thumbattr) = getimagesize("$dirlocation/i/$purpose/$fileid");
		$mediainfo['filetype'] = "jpg";
		$mediainfo['vidlength'] = 0;
	} else if (preg_match("/\.png/",$fileid)) {
		list($mediainfo['width'], $mediainfo['height'], $filetype, $attr) = getimagesize("$dirlocation/i/$purpose/original-$fileid");
		list($mediainfo['thumbwidth'], $mediainfo['thumbheight'], $thumbtype, $thumbattr) = getimagesize("$dirlocation/i/$purpose/$fileid");
		$mediainfo['filetype'] = "png";
		$mediainfo['vidlength'] = 0;
	} else if (preg_match("/mp4/",$fileid)) {
		$ffmpeg = new ffmpeg_movie("$dirlocation/m/$fileid");
		$mediainfo['vidlength'] = ceil($ffmpeg->getDuration());
		$mediainfo['width'] = $ffmpeg->getFrameWidth();
		$mediainfo['height'] = $ffmpeg->getFrameHeight();
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
			echo "<div class='AdminError'>Style Entry <B>$name</B> [$sid] Failed to Update!<br>". mysqli_error($conn) ."</div>";
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
			unlink("$dirlocation/i/category/$old_fileid");
			unlink("$dirlocation/i/category/original-$old_fileid"); // XXX: we're not deleting jpegs, only png.
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `force_display_names` = '%s', `published` = '%s', `image_filename` = '%s', `image_id` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'",
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,preg_replace("/[^YNI]/","",$_REQUEST['force_display_names'])),
				mysqli_real_escape_string($conn,$published),
				mysqli_real_escape_string($conn,$filename),
				mysqli_real_escape_string($conn,$newfileid),
				mysqli_real_escape_string($conn,DatePHPtoSQL(time())),
				mysqli_real_escape_string($conn,$cid)
			);
		} else {
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `force_display_names` = '%s', `published` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'",
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,preg_replace("/[^YNI]/","",$_REQUEST['force_display_names'])),
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
	AdminShowDeleteConfirmation($targetcategoryurl,$targetcategoryurl,$url,$url,$nextfunction);  
}

function AdminArtistDeleteGo($aid) {
	global $conn;
	global $dirlocation;
	// delte from database
	// delete mid stuff (video/photo)
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
	unlink("$dirlocation/i/category/$fileid");
	unlink("$dirlocation/i/category/original-$fileid"); // XXX: we're not deleting jpegs, only png.
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
		$query = sprintf("SELECT `cid` FROM `artistcategories` WHERE `aid` = %s",
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
		$query = sprintf("SELECT `sid` FROM `artiststyles` WHERE `aid` = %s",
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
		$query = sprintf("SELECT `lid` FROM `artistlocations` WHERE `aid` = %s",
			mysqli_real_escape_string($conn,$aid)
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
		$query = sprintf("INSERT INTO `categories` (`url`,`category`,`description`,`force_display_names`,`published`,`image_filename`,`image_id`, `last_updated`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",
			mysqli_real_escape_string($conn,$url),
			mysqli_real_escape_string($conn,$category),
			mysqli_real_escape_string($conn,htmlspecialchars(ucwords(trim($_REQUEST['form_description'])))),
			mysqli_real_escape_string($conn,preg_replace("/[^YNI]/","",$_REQUEST['force_display_names'])),
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
		$origimage = imagecreatefromjpeg("$dirlocation/i/$purpose/original-$fileid");
	} elseif (preg_match("/\.png/",$fileid)) {
		$origimage = imagecreatefrompng("$dirlocation/i/$purpose/original-$fileid");
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
			imagejpeg($newimage, "$dirlocation/i/$purpose/$newfilename", 80); // http://www.ebrueggeman.com/blog/php_image_optimization
		} else if (preg_match("/\.png/",$fileid)) {
			$newfilename = substr($fileid,0,-4) . ".png";
			imagepng($newimage, "$dirlocation/i/$purpose/$newfilename",9);
		}
		imagedestroy($origimage);
		imagedestroy($newimage);
	}
	if (preg_match("/\.mp4/",$fileid)) {
		// create a thumbnail
		$ffmpeg = new ffmpeg_movie("$dirlocation/m/$fileid");
		$totalframes = $ffmpeg->getFrameCount();
		// XXX: This takes some time to render
		for ($i = 1; $i < 5; $i++) {
			$thumbnailname = substr($fileid,0,-4) . "-$i.jpg";
			$frame = $ffmpeg->getFrame(ceil($totalframes*($i * "0.1"))); // make four thumbnails every 100 frames
			$gd_image = $frame->toGDImage();
			imagejpeg($gd_image, "$dirlocation/i/$purpose/$thumbnailname");
			imagedestroy($gd_image);
		}
		// whatever image is fileid.jpg is the visible thumbnail, others just there for backup wasting space
		copy ("$dirlocation/i/$purpose/".substr($fileid,0,-4) . "-1.jpg", "$dirlocation/i/$purpose/". substr($fileid,0,-4) .".jpg");
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
			move_uploaded_file($tmp_name, $dirlocation . "/i/" . $purpose  . "/original-" . $fileid );
			if (filesize($dirlocation . "/i/" . $purpose . "/original-" . $fileid) < 1024) {
				// if the file is smaller than 1kb, I don't trust it.
				unlink($dirlocation . "/i/" . $purpose . "/original-" . $fileid);
				echo "<div class='AdminError'>File Upload Error: File is invalid due to small size.</div>";
				$happyuploads[] = array(NULL,NULL);
			} else {
				// Yay, its a file!  Lets totally blow off the given file name and replace with my own.
				$finfo = finfo_open(FILEINFO_MIME);
				$type = finfo_file($finfo, $dirlocation . "/i/" . $purpose . "/original-" . $fileid);
				if (preg_match("/jpeg/i",$type)) {
					$newfileid = "$fileid.jpg";	
					rename (
						$dirlocation . "/i/" . $purpose . "/original-". $fileid,
						$dirlocation . "/i/" . $purpose . "/original-". $newfileid
					);
				} elseif (preg_match("/png/i",$type)) {
					$newfileid = "$fileid.png";
					rename (
						$dirlocation . "/i/" . $purpose . "/original-". $fileid,
						$dirlocation . "/i/" . $purpose . "/original-". $newfileid
					);
				} elseif (preg_match("/mp4/i",$type)) {
					$newfileid = "$fileid.mp4";
					rename (
						$dirlocation . "/i/" . $purpose . "/original-". $fileid,
						$dirlocation . "/m/$newfileid"
					);
				} elseif (preg_match("/word/i",$type)) {
					$newfileid = "$fileid.doc";
					rename (
						$dirlocation . "/i/" . $purpose . "/original-". $fileid,
						$dirlocation . "/i/" . $purpose . "/original-". $newfileid
					);
				} elseif (preg_match("/excel/i",$type)) {
					$newfileid = "$fileid.xls";
					rename (
						$dirlocation . "/i/" . $purpose . "/original-". $fileid,
						$dirlocation . "/i/" . $purpose . "/original-". $newfileid
					);
				} elseif (preg_match("/pdf/i",$type)) {
					$newfileid = "$fileid.pdf";
					rename (
						$dirlocation . "/i/" . $purpose . "/original-". $fileid,
						$dirlocation . "/i/" . $purpose . "/original-". $newfileid
					);
				} else {
					$newfileid = $fileid;
					rename (
						$dirlocation . "/i/" . $purpose . "/original-". $fileid,
						$dirlocation . "/i/" . $purpose . "/original-". $newfileid
					);
				}
				$happyuploads[] = array($newfileid,$filename);
				$gotafile = TRUE;
			}
		}
	}
	// We accepted a positive number of files !
	return $happyuploads;
}

function ShowPhotoArray($mediadata) {
	// show a bunch of photos
	// argument is just $artistinfo['media']
	global $conn;
	$photosorder = array();
	foreach ($mediadata['mid'] as $arraykey => $mid) {
		if (preg_match("/png|jpg/",$mediadata['filetype'][$mid])) {
			// check if highlighted is viewable, then put highlighted first
			if (($mediadata['is_highlighted'][$mid] == 1) && ($mediadata['viewable'][$mid] == 1)) {
				$location = $arraykey;
				$photosorder[$location] = $mediadata['mid'][$mid];
			}
			// viewable items next, sorted by recent published first
			if (($mediadata['viewable'][$mid] == 1) && ($mediadata['is_highlighted'][$mid] == 0)) {
				$location = $arraykey * 100;	// put this mediaID later in the sort index
				$photosorder[$location] = $mediadata['mid'][$mid];
			}
			// non-viewable crap, with marker
			if ($mediadata['viewable'][$mid] == 0) {
				$location = $arraykey * 1000;	// put this mediaID later in the sort index
				$photosorder[$location] = $mediadata['mid'][$mid];
			}
		}
	}
	ksort($photosorder, SORT_NUMERIC);
	foreach ($photosorder as $key => $mid) {
		//echo "$key = $mid / real mid: ".$mediadata['mid'][$mid] ."<br>";
		if ($mediadata['is_highlighted'][$mid]) {
			$highlightclass = "AdminImagesPreviewHighlighted";
		} elseif ($mediadata['viewable'][$mid] == 0) {
			$highlightclass = "AdminImagesPreviewNotVisible";
		} else {
			$highlightclass = "AdminImagesPreviewNormal";
		}
		$megapixels = $mediadata['width'][$mid] * $mediadata['height'][$mid];
		// if image is over 1 megapixel, call it "high-res"
		// XXX: what about DPI? This is a bad way and a bad place to do this.
		if ($megapixels > 1000000) {
			$adminimageicon = "AdminImageIconHighRes";
		} else {
			$adminimageicon = "AdminImageIconLowRes";
		}
		if ($mediadata['is_highlighted'][$mid] == 1) {
			$highlighted = "Remove Highlight";
		} else {
			$highlighted = "Highlight This";
		}
		if ($mediadata['viewable'][$mid] == 1) {
			$viewable = "Hide";
		} else {
			$viewable = "Show";
		}
		if (strlen($mediadata['name'][$mid]) > 18) {
			$filename = htmlspecialchars(substr($mediadata['name'][$mid],0,18) . "...");
		} else {
			$filename = htmlspecialchars($mediadata['name'][$mid]);
		}
		$string = sprintf("<div class='CheckBoxImageContainer'>".
			"<a href='/i/artist/%s' target='_new' border='0'>".
			"<img class='%s' src='/i/artist/%s' data-width='%s' data-height='%s' alt='%s' title='%s'>".
			"</a>".
			"<div class='%s'></div>".
			"<select name='1234' class='DropDownImage' id='%s'>".
			"<option>Image Features</option>".
			"<option>%s</option>".
			"<option>%s</option>".
			"<option>Remove</option>".
			"<optgroup disabled='disabled' label='Image Info'>".
			"<option disabled='disabled'>%s</option>".
			"<option disabled='disabled'>Size: %sx%s</option>".
			"<option disabled='disabled'>Uploaded: %s</option>".
			"</select></div>",
			"original-".$mediadata['filename'][$mid],
			$highlightclass,
			$mediadata['filename'][$mid],
			$mediadata['thumbwidth'][$mid],
			$mediadata['thumbheight'][$mid],
			$mediadata['name'][$mid],
			$mediadata['name'][$mid],
			$adminimageicon,
			$mediadata['mid'][$mid],
			$highlighted,
			$viewable,
			$filename,
			$mediadata['width'][$mid],
			$mediadata['height'][$mid],
			date("M d, Y",$mediadata['published'][$mid])
		);
		echo "$string\n";
	}
}

function aasort (&$array, $key) {
	// sort an array's array by the sub-array's key name
	$sorter=array();
	$ret=array();
	if (isset($array)) {
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

function DisplayNamesOptionsDropDown($cid) {
	// Show a dropdown of category's "use real or obfuscated display name" per category
	// N force real names only, I individual artist mode, Y force display names only
	global $conn;
	if ($cid) {
		$query = sprintf("SELECT `force_display_names` FROM `categories` WHERE `cid` = %s",
			preg_replace("/[^0-9]/","",$cid)
		);
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
	}
	$display_mode = $row['force_display_names'];
	$options = array(
		"I" => "Use the Individual Artist's Setting",
		"N" => "Real Names to be used for All artists in this category",
		"Y" => "Obfuscated Display Names to be used for All artists in this category"
	);
	$string = "";
	foreach ($options as $key => $value) {
		$string .= sprintf("<option value='%s'%s>%s</option>",
			$key,
			($key == $display_mode)? ' selected="SELECTED"' : '',
			$value
		);
	}
	return ($string);
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
			preg_match_all("/(\sof|\sis|\sa|\san|\sthe|\sbut|\sor|\snot|\syet|\sat|\son|\sin|\sover|\sabove|\sunder|\sbelow|\sbehind|\snext\sto|\sbeside|\sby|\samoung|\sbetween|\sby|\still|\ssince|\sdurring|\sfor|\sthroughout|\sto|\sand){2}/i",$new ,$matches);
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

