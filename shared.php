<?
/****************************************
**  Steve Beyer Productions
**  Website and Talent Database
**
**  Concept: Steve Beyer
**  Code: Presence
**
**  Last Edit: 20131125
****************************************/

function Init() {
	global $conn;
	global $dirlocation;
	global $pagination;
	global $videowidth;
	error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	date_default_timezone_set('America/Los_Angeles');
	session_start(); // I want to track people thru the site
	$_SESSION['last_move'] = $_SESSION['last_activity']; // testing how long page to page
	if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 300)) {
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
	$pagination = "20";	// number of entries per "page"
	$videowidth = 600;
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
	echo "<div class='Debug'>";
	echo "You wanted to look at: ";
	if (isEmpty($_REQUEST['url'])) {
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
	echo "</div>";
}

function ArtistPage() {
	global $conn;
	require_once("templates/header.php");
	require_once("templates/artistpage.php");
	require_once("templates/FWDconstructors.php"); // shit to make grid and carousel go
	require_once("templates/Parsedown/Parsedown.php");
	if (isEmpty($_REQUEST['url'])) {
		// Whoops, no artist name in the URL, show the categories
		header("Location: http://". $_SERVER['HTTP_HOST'] ."/categories", TRUE, 302);
	} else {
		$artistinfo = getArtistInfo();
		if (count($artistinfo) === 0) {
			header("Location: http://". $_SERVER['HTTP_HOST'] ."/categories", TRUE, 302);
		} elseif (count($artistinfo) === 1) {
			$artistinfo = obfuscateArtistInfo($artistinfo);
			$artistinfo = insertBreadCrumb($artistinfo);
			$meta = getArtistMetaTags($artistinfo);
			$meta['css'][] = "Rb-ui.css";
			$meta['js'][] = "jwplayer/jwplayer.js";
			$meta['js'][] = "FWDGrid.js";
			htmlHeader($meta);
			htmlMasthead($meta);
			htmlNavigation($meta);
			htmlWavesFullStart();
			htmlBreadcrumb($meta);
			htmlArtistPageTop($artistinfo);
			htmlBodyStart();
			htmlStylesTags($artistinfo);
			htmlArtistPageBottom($artistinfo);
			htmlFooter($meta);
			fwdConsGrid(); // dump this stuff in at the bottom of html
		} else {
			// show multiple matching artist chooser
			// XXX: Not Done
		}
	}
}

function getArtistMetaTags($artistinfo) {
	$meta = array();
	// meta keywords are name, categories, styles, locations
	$meta['keywords'] = "Steve Beyer Productions, SBP, ";
	foreach (array_keys($artistinfo) as $key) {
		$meta['keywords'] .= $artistinfo[$key]['name'] . ", ";
		foreach (array_keys($artistinfo[$key]['categories']) as $subkey) {
			$meta['keywords'] .= $artistinfo[$key]['categories'][$subkey] . ", ";
		}
		foreach (array_keys($artistinfo[$key]['styles']) as $subkey) {
			$meta['keywords'] .= $artistinfo[$key]['styles'][$subkey] . ", ";
		}
		foreach (array_keys($artistinfo[$key]['locations']) as $subkey) {
			$meta['keywords'] .= "'" . $artistinfo[$key]['locations'][$subkey] . "', ";
		}
	}
	$meta['keywords'] = substr($meta['keywords'], 0, -2) . ".";
	// meta description is: name - slug (cat,egor,ies)
	$meta['description'] = "";
	foreach (array_keys($artistinfo) as $key) {
		$meta['description'] .= $artistinfo[$key]['name'];
		$meta['description'] .= " - ";
		$meta['description'] .= $artistinfo[$key]['slug'];
		$meta['description'] .= " (";
		foreach (array_keys($artistinfo[$key]['styles']) as $subkey) {
			$meta['description'] .= $artistinfo[$key]['styles'][$subkey] . ", ";
		}
		$meta['description'] = substr($meta['description'], 0, -2) . ")";
		$meta['description'] .= " / ";
	}
	$meta['description'] = substr($meta['description'], 0, -3);
	// title is: SBP Presents name - slug (sty,les)
	$meta['title'] = "SBP presents ";
	foreach (array_keys($artistinfo) as $key) {
		$meta['title'] .= $artistinfo[$key]['name'];
		$meta['title'] .= " - ";
		$meta['title'] .= $artistinfo[$key]['slug'];
		$meta['title'] .= " (";
		foreach (array_keys($artistinfo[$key]['categories']) as $subkey) {
			$meta['title'] .= $artistinfo[$key]['categories'][$subkey] . ", ";
		}
		$meta['title'] = substr($meta['title'], 0, -2) . ")";
		$meta['description'] .= " / ";
	}
	$meta['description'] = substr($meta['description'], 0, -3);
	$meta['url'] = CurPageURL();
	// breadcrumb
	// step 0 : categories
	// step 1 : category name
	// step 2 : artist name
	$aid = $artistinfo[key($artistinfo)]['aid'];
	$meta['breadcrumb'][0]['name'] = "Talent";
	$meta['breadcrumb'][0]['url'] = CurServerURL() . "talent";
	if (count($artistinfo) === 1) {
		$meta['breadcrumb'][1]['name'] = $artistinfo[$aid]['category'];
		$meta['breadcrumb'][1]['url'] = curServerURL() . "category/" . $artistinfo[$aid]['caturl'];
		$meta['breadcrumb'][2]['name'] = $artistinfo[$aid]['name'];
		$meta['breadcrumb'][2]['url'] = curPageURL();
	} else {
		$meta['breadcrumb'][1]['name'] = "Selection: ";
		$meta['breadcrumb'][1]['url'] = curPageURL();
		foreach (array_keys($artistinfo) as $key) {
			$meta['breadcrumb'][1]['name'] .= $artistinfo[$key]['name'];
			$meta['breadcrumb'][1]['name'] .= ", ";
		}
		$meta['breadcrumb'][1] = substr($meta['breadcrumb'][1], 0, -2);
	}	
	// image
	$meta['image']  = CurServerUrl() . "i/artist/";
	$meta['image'] .= $artistinfo[$aid]['media']['filename'][key($artistinfo[$aid]['media']['filename'])];
	return ($meta);

}

function obfuscateArtistInfo($artistinfo) {
	// determine if it is necessarty to obfuscate any artists in this array, munge if so, and send the array back
	global $conn;
	foreach ($artistinfo as $key => $blah) {
		$obfuscateMe = 0;
		// artist's own entry forced to obfuscate?
		if ((int)$artistinfo[$key]['use_display_name'] === 1) {
			$obfuscateMe++;
		}
		// artist's category selected to obfuscate?
		$query = sprintf(
			"SELECT `force_display_names` FROM `categories` WHERE `cid` = '%s'",
			mysqli_real_escape_string($conn,$artistinfo[$key]['cid'])
		);
		$row = mysqli_fetch_array(mysqli_query($conn,$query), MYSQLI_ASSOC);
		if ($row['force_display_names'] === "Y") {
			$obfuscateMe++;
		}
		if ($row['force_display_names'] === "N") {
			// AH HA, no, this special category MUST show full real name!
			$obfuscateMe = -9;
		}
		// was the obfuscate session cookie previously set?
		if ($_SESSION['obfuscate'] == "1") {
			$obfuscateMe++;
		}
		// incoming URL used obfuscated artist name?
		$url = MakeURL(strtolower(trim($_REQUEST['url'])));
		$query = sprintf(
			"SELECT `alt_url` FROM `artists` WHERE `alt_url` = '%s'",
			mysqli_real_escape_string($conn,$url)
		);
		$result = mysqli_query($conn, $query);
		if (mysqli_num_rows($result) > 0) {
			$obfuscateMe++;
			// XXX: Oh hey, set a cookie on this to obfuscate further artists during this user's visit
			// XXX: Since this user was originally given an obfuscated URL
			$_SESSION['obfuscate'] = 1;
		} else {
			// XXX: nevermind, they found a legit url somehow, remove that cookie
			if ($_SESSION['obfuscate'] == "1") {
				$obfuscateMe--;
				$_SESSION['obfuscate'] = 0;
			}
		}
		if ($obfuscateMe > 0) {
			$artistinfo[$key]['bio'] = str_ireplace($artistinfo[$key]['name'], $artistinfo[$key]['display_name'], $artistinfo[$key]['bio']);
			$artistinfo[$key]['slug'] = str_ireplace($artistinfo[$key]['name'], $artistinfo[$key]['display_name'], $artistinfo[$key]['slug']);
			$artistinfo[$key]['name'] = $artistinfo[$key]['display_name'];
			$artistinfo[$key]['url'] = $artistinfo[$key]['alt_url'];
		}
	}
	return ($artistinfo);
}

function getArtistInfo() {
	global $conn;
	$url = MakeURL(strtolower(trim($_REQUEST['url'])));
	$artistnames = array(); // to find the nearest artist name
	// search to find exact full URL match first
	$query = sprintf(
		"SELECT * FROM `artists` WHERE (`url` = '%s' OR `alt_url` = '%s') AND `is_active` = 1",
		mysqli_real_escape_string($conn, $url),
		mysqli_real_escape_string($conn, $url)
	);
	$result = mysqli_query($conn, $query);
	if (mysqli_num_rows($result)) {
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$artistnames[$row['aid']] = $row;
		}
	}
	// check category and get the hell outta here if we can
	if (count($artistnames) === 1) {
		$aid = key($artistnames);
		// has the user come in via a category page listing?
		$artistnames[key($artistnames)]['cid'] = getArtistCategory($aid);
		// Get other categories, styles, and locations
		$query = sprintf(
			"SELECT `categories`.`cid`, `categories`.`category` FROM `categories`
			 LEFT OUTER JOIN `artistcategories` ON `artistcategories`.`cid` = `categories`.`cid`
			 WHERE `artistcategories`.`aid` = '%s' AND `categories`.`published` = 1",
			mysqli_real_escape_string($conn,$aid)
		);
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$artistnames[$aid]['categories'][$row['cid']] = $row['category'];
		}
		$query = sprintf(
			"SELECT `styles`.`sid`, `styles`.`name` FROM `styles`
			 LEFT OUTER JOIN `artiststyles` ON `artiststyles`.`sid` = `styles`.`sid`
			 WHERE `artiststyles`.`aid` = '%s'",
			mysqli_real_escape_string($conn,$aid)
		);
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$artistnames[$aid]['styles'][$row['sid']] = $row['name'];
		}
		$query = sprintf(
			"SELECT `locations`.`lid`, `locations`.`city`, `locations`.`state` FROM `locations`
			 LEFT OUTER JOIN `artistlocations` ON `artistlocations`.`lid` = `locations`.`lid`
			 WHERE `artistlocations`.`aid` = '%s' ORDER BY `locations`.`state`",
			mysqli_real_escape_string($conn,$aid)
		);
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$artistnames[$aid]['locations'][$row['lid']] = $row['city'] . ", " . StateCodeToName($row['state']);
		}
		$query = sprintf(
			"SELECT * FROM `media` WHERE `aid` = %s ORDER BY `is_highlighted` DESC, `vidlength` ASC", 
			mysqli_real_escape_string($conn,$aid)
		);
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$artistnames[$aid]['media']['mid'][$row['mid']] = $row['mid'];
			$artistnames[$aid]['media']['name'][$row['mid']] = $row['name'];
			$artistnames[$aid]['media']['filetype'][$row['mid']] = $row['filetype'];
			$artistnames[$aid]['media']['filename'][$row['mid']] = $row['filename'];
			$artistnames[$aid]['media']['thumbwidth'][$row['mid']] = $row['thumbwidth'];
			$artistnames[$aid]['media']['thumbheight'][$row['mid']] = $row['thumbheight'];
			$artistnames[$aid]['media']['height'][$row['mid']] = $row['height'];
			$artistnames[$aid]['media']['width'][$row['mid']] = $row['width'];
			$artistnames[$aid]['media']['vidlength'][$row['mid']] = $row['vidlength'];
			$artistnames[$aid]['media']['is_highlighted'][$row['mid']] = $row['is_highlighted'];
			$artistnames[$aid]['media']['viewable'][$row['mid']] = $row['viewable'];
			$artistnames[$aid]['media']['published'][$row['mid']] = DateSQLtoPHP($row['published']);
		}
		mysqli_free_result($result);
		return ($artistnames);
	}
	//$closestArtistFromRequest = ClosestWord($url,$artistnames);
}

function insertBreadCrumb($artistinfo) {
	// slap in the breadcrumbs for each artist in the array
	foreach ($artistinfo as $key => $blah) {
		$catarray = getCategoryBreadcrumb($artistinfo[$key]['cid']);
		$artistinfo[$key]['category'] = $catarray['category'];
		$artistinfo[$key]['caturl'] = $catarray['url'];
	}
	return ($artistinfo);
}

function getCategoryBreadcrumb($cid) {
	// collect an incoming CID, return the URL and Name plz.
	global $conn;
	$query = sprintf(
		"SELECT `category`,`url` FROM `categories` WHERE `cid` = '%s'",
		mysqli_real_escape_string($conn,$cid)
	);
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	return ($row);
}

function getArtistCategory($aid) {
	global $conn;
	// from looking at the artist id, return the category ID or null
	$cid = NULL;
	if (strlen(preg_replace("/[^0-9]/","",$_SESSION['category'])) >= 1 ) {
		// did the web visitor pass through a category listing page?
		$cid = preg_replace("/[^0-9]/","",$_SESSION['category']);
	} else {
		// no category was in the session, prolly a direct link, so go pick a category for this one artist
		$query = sprintf(
			"SELECT `categories`.`cid` FROM `categories` 
			 LEFT OUTER JOIN `artistcategories` ON `categories`.`cid` = `artistcategories`.`cid` 
			 WHERE `artistcategories`.`aid` = '%s' AND `categories`.`published` = 1 ORDER BY `categories`.`is_highlighted` DESC, `categories`.`category` ASC
			 LIMIT 0,1",
			mysqli_real_escape_string($conn,$aid)
		);
		$result = mysqli_query($conn, $query);
		if (mysqli_num_rows($result)) {
			$cid = mysqli_fetch_array($result, MYSQLI_ASSOC)['cid'];
		}
	}
	return($cid);
}

function CategoriesList() {
	global $conn;
	require_once("templates/header.php");
	require_once("templates/categories.php");
	require_once("templates/FWDconstructors.php"); // shit to make grid and carousel go
	$meta = array();
	if (isEmpty($_REQUEST['url'])) {
		// all public categories, highlighted first.
		$query = "SELECT * FROM `categories` WHERE `published` = 1 ORDER BY is_highlighted DESC, `category` ASC";
		$result = mysqli_query($conn, $query);
		if (mysqli_num_rows($result)) {
			$categoryList = array();
			$highlightedList = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				if ($row['is_highlighted'] == 1) {
					$highlightedList[] = $row;
				}
				// all categories go in here, but only lightlighted go into highlighted for the carousel
				$categoryList[] = $row;
			}
			// meta keywords
			$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Talent, Musicians, Artists, Bands, Entertainment, Category List, Categories, ";
			foreach ($categoryList as $category) {
				$meta['keywords'] .= $category['category'] . ", ";
			}
			$meta['keywords'] = substr($meta['keywords'], 0, -2) . ".";
			$meta['description'] = "Categories Listing including ";
			foreach($highlightedList as $category) {
				$meta['description'] .= $category['category'] . ", ";
			}
			$meta['description'] = substr($meta['description'], 0, -2) . ".";
			$meta['title'] = "Entertainment Categories Listing - Steve Beyer Productions";
			$meta['url'] = CurPageURL();
			$meta['image'] = CurServerUrl() . "i/category/" . $highlightedList[0]['carousel_id'];
			$meta['css'][] = "skin_modern_silver.css";
			//$meta['js'][] = "FWDRoyal3DCarousel.js";
			$meta['js'][] = "FWDRoyal3DCarousel_uncompressed.js";
			$meta['breadcrumb'][0]['name'] = "Talent";
			$meta['breadcrumb'][0]['url'] = curPageURL();
			// display all the categories
			htmlHeader($meta);
			htmlMasthead($meta);
			htmlNavigation($meta);
			htmlWavesStart();
			htmlBreadcrumb($meta);
			ListCategoryCarousel($highlightedList);
			htmlBodyStart();
			ListAllCategories($categoryList);
			fwdConsCarousel(); // carousel constructor settings
			ListCategoriesTextLinks($categoryList);
			htmlFooter($meta);
		} else {
			ErrorDisplay("Categories Listing Unavailable!");
		}
	} else {
		// all published artists in a specific category
		$url = MakeURL(strtolower(trim($_REQUEST['url'])));
		// what are all the categories we can choose from?
		$categorynames = array();
		$categoryurls= array();
		$query = "SELECT `url`, `category` FROM `categories`";
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$categorynames[] = strtolower($row['category']);
			$categoryurls[] = strtolower($row['url']);
		}
		$closestCategoryFromRequest = ClosestWord($url,$categoryurls);
		// Artist names default to not obfuscated using real names, not requiring display names.
		$obfuscatedArtistNames = 0;
		// dig up closest category that matches the request
		$query = sprintf(
			"SELECT * FROM `categories` WHERE `url` = '%s' ORDER BY is_highlighted DESC, `category` ASC",
			mysqli_real_escape_string($conn,$closestCategoryFromRequest)
		);
		$resultMatchingCategories = mysqli_query($conn, $query);
		if (mysqli_num_rows($resultMatchingCategories) == 0) {
			ErrorDisplay("No Categories Match Your Request");
		} else {
			$categoryInfo = mysqli_fetch_array($resultMatchingCategories, MYSQLI_ASSOC);
			// check obfuscated cookie status
			if ($_SESSION['obfuscate'] == "1") {
				$obfuscatedArtistNames = 1;
			}
			// check if any of the categories require obfuscated artist names
			// "Y" is to force display names. (N is force real names, I is individual artist mode)
			if ($categoryInfo['force_display_names'] == "I" && $obfuscatedArtistNames == 0) {
				$obfuscatedArtistNames = "I";
			} else if ($categoryInfo['force_display_names'] == "Y") {
				$obfuscatedArtistNames = 1;
			}
			// joined query to find artists IDs that match the category,
			// sort them by is_highlighted desc, name asc,
			// save data to $artists array
			$artists = array(); // the big array of good artists data
			$query = sprintf(
				"SELECT `artists`.`aid`, `artists`.`name`, `artists`.`display_name`, `artists`.`url`, 
				 `artists`.`alt_url`, `artists`.`slug`, `artists`.`use_display_name`, `artists`.`is_highlighted`
				 FROM `artists` LEFT OUTER JOIN `artistcategories` ON `artists`.`aid` = `artistcategories`.`aid` 
				 WHERE `artistcategories`.`cid` = '%s' AND `artists`.`is_searchable` = 1 AND `artists`.`is_active` = 1 
				 ORDER BY `artists`.`is_highlighted` DESC, `artists`.`name` ASC",
				mysqli_real_escape_string($conn,$categoryInfo['cid'])
			);
			$result = mysqli_query($conn,$query);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$artists[$row['aid']] = array();
				if ($obfuscatedArtistNames == "I") {
					// we're allowd to display names depending on the individual artist 
					if ($row['use_display_name'] == "1") {
						// obfuscate this one artist
						$artists[$row['aid']]['name'] = $row['display_name'];
						$artists[$row['aid']]['url'] = $row['alt_url'];
					} else {
						// Real name for this one artist
						$artists[$row['aid']]['name'] = $row['name'];
						$artists[$row['aid']]['url'] = $row['url'];
					}
				} else if ($obfuscatedArtistNames == 1) {
					// obfuscate ALL names in this list
					$artists[$row['aid']]['name'] = $row['display_name'];
					$artists[$row['aid']]['url'] = $row['alt_url'];
				} else { // we can use normal real names!
					$artists[$row['aid']]['name'] = $row['name'];
					$artists[$row['aid']]['url'] = $row['url'];
				}
				$artists[$row['aid']]['aid'] = $row['aid'];
				$artists[$row['aid']]['slug'] = $row['slug'];
				$artists[$row['aid']]['is_highlighted'] = $row['is_highlighted'];
				$artists[$row['aid']]['use_display_name'] = $row['use_display_name'];

				$query = sprintf(
					"SELECT `filename`,`thumbwidth`,`thumbheight` 
					 FROM `media` WHERE `aid` = %s AND `viewable` = 1 ORDER BY `is_highlighted` DESC, `width` DESC LIMIT 0,1",
					mysqli_real_escape_string($conn,$row['aid'])
				);
				$photoresult = mysqli_query($conn,$query);
				$rowphoto = mysqli_fetch_array($photoresult, MYSQLI_ASSOC);
				$artists[$row['aid']]['filename'] = $rowphoto['filename'];
				$artists[$row['aid']]['thumbwidth'] = $rowphoto['thumbwidth'];
				$artists[$row['aid']]['thumbheight'] = $rowphoto['thumbheight'];
			}
			$artistsHighlighted = array();	// array of highlighted good artists
			foreach ($artists as $aid => $garbage) {
				if ($artists[$aid]['is_highlighted'] == 1) {
					// highlighted carousel data
					$artistsHighlighted[$aid] = $artists[$aid];
				}
			}
			// So what was the best real category name that matches the random user request?
			$closestCategoryFromRequest = $categoryInfo['category'];
			//$closestCategoryFromRequest = ucWords(ClosestWord($url,$categorynames));

			// meta keywords
			$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Entertainment, Category, List, Listing, ";
			$meta['keywords'] .= "$closestCategoryFromRequest, ";
			$meta['keywords'] .= $categoryInfo['description'] .", ";
			foreach ($artists as $artist) {
				$meta['keywords'] .= $artist['name'] . ", ";
			}
			$meta['keywords'] = substr($meta['keywords'], 0, -2) . ".";
			$meta['description'] = "Listing of $closestCategoryFromRequest - ". $categoryInfo['description'] ." including ";
			foreach($artistsHighlighted as $highlights) {
				$meta['description'] .= $highlights['name'] . ", ";
			}
			$meta['description'] = substr($meta['description'], 0, -2) . ".";
			$meta['title'] = "$closestCategoryFromRequest Entertainment Category Listing";
			$meta['url'] = CurPageURL();
			if (isEmpty($categoryInfo['carousel_id'])) {
				$meta['image'] = CurServerURL() . "i/category/" . $categoryInfo['image_id'];
			} else {
				$meta['image'] = CurServerURL() . "i/category/" . $categoryInfo['carousel_id'];
			}
			$meta['css'][] = "skin_modern_silver.css";
			$meta['css'][] = "skin_minimal_dark_global.css";
			//$meta['css'][] = "jquery-ui.css";
			//$meta['js'][] = "FWDGrid.js";
			//$meta['js'][] = "FWDRoyal3DCarousel.js";
			$meta['js'][] = "FWDRoyal3DCarousel_uncompressed.js";
			//$meta['js'][] = "jquery.js";
			//$meta['js'][] = "jquery-ui.js";
			//$meta['js'][] = "presgrid.js";
			$meta['breadcrumb'][0]['name'] = "Talent";
			$meta['breadcrumb'][0]['url'] = curServerURL() . "talent/";
			$meta['breadcrumb'][1]['name'] = $closestCategoryFromRequest;
			$meta['breadcrumb'][1]['url'] = curPageURL();

			$_SESSION['category'] = $categoryInfo['cid'];
			htmlHeader($meta);
			htmlMasthead($meta);
			htmlNavigation($meta);
			if (count($artistsHighlighted) > 0) {
				htmlWavesStart();
				htmlBreadcrumb($meta);
				ListArtistCarousel($closestCategoryFromRequest,$artistsHighlighted);
				htmlBodyStart();
				htmlCategoryImageBelow($categoryInfo['image_id'], $closestCategoryFromRequest);
				ListArtistsForCategory($closestCategoryFromRequest,$artists);
				ListArtistsTextLinks($categoryInfo,$artists); 
				fwdConsCarousel(); // dump this stuff in at the bottom of html
			} else {
				// snap, we don't like no ones in this category! Put up a simple category header image
				htmlWavesShortStart();
				htmlBreadcrumb($meta);
				htmlCategoryImage($categoryInfo['image_id'], $closestCategoryFromRequest);
				htmlBodyStart();
				ListArtistsForCategory($closestCategoryFromRequest,$artists);
				ListArtistsTextLinks($categoryInfo,$artists); 
			}
			htmlFooter($meta);
		}
	}
}

function HomePage() {
	require_once("templates/header.php");
	require_once("templates/homepage.php");
	require_once("templates/FWDconstructors.php"); // shit to make grid and carousel go
	require_once("templates/Parsedown/Parsedown.php");
	$content['body'] = Parsedown::instance()->parse(htmlspecialchars_decode(file_get_contents("body.txt")));
	$content['news'] = Parsedown::instance()->parse(htmlspecialchars_decode(file_get_contents("news.txt")));
	$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Talent, Musicians, Artists, Bands, Entertainment, Decor, Production, Wedding, Special Events";
	$meta['description'] = "Steve Beyer Productions - The Entertainment and Production Company";
	$meta['title'] = "Steve Beyer Productions - The Entertainment and Production Company";
	$meta['url'] = CurPageURL();
	$meta['image'] = CurServerUrl() . "sbp.png";
	$meta['css'][] = "skin_modern_silver.css";
	//$meta['js'][] = "FWDRoyal3DCarousel.js";
	$meta['js'][] = "FWDRoyal3DCarousel_uncompressed.js";
	htmlHeader($meta);
	htmlMasthead($meta);
	htmlNavigation($meta);
	htmlWavesStart();
	homePageCarousel(gatherHighlightedArtists());
	htmlBodyStart();
	htmlHomePageTop();
	htmlHomePageCategories(allPublicCategories());
	htmlHomePageContent($content);
	//htmlBreadcrumb($meta);
	fwdConsCarousel(); // dump this stuff in at the bottom of html
	htmlFooter($meta);
}

function ProductionPage() {
	require_once("templates/header.php");
	$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Talent, Musicians, Artists, Bands, Entertainment, Decor, Production, Wedding, Special Events";
	$meta['description'] = "Steve Beyer Productions - The Entertainment and Production Company";
	$meta['title'] = "Production Staging, Projection, Lighting, Sound Reinforcement - Steve Beyer Productions";
	$meta['url'] = CurPageURL();
	$meta['image'] = CurServerUrl() . "sbp.png";
	htmlHeader($meta);
	htmlMasthead($meta);
	htmlNavigation($meta);
	htmlWavesStart();
	htmlBodyStart();
	//htmlBreadcrumb($meta);
	htmlFooter($meta);
}

function EventPage() {
	require_once("templates/header.php");
	$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Talent, Musicians, Artists, Bands, Entertainment, Decor, Production, Wedding, Special Events";
	$meta['description'] = "Steve Beyer Productions - The Entertainment and Production Company";
	$meta['title'] = "Special Events - Steve Beyer Productions";
	$meta['url'] = CurPageURL();
	$meta['image'] = CurServerUrl() . "sbp.png";
	htmlHeader($meta);
	htmlMasthead($meta);
	htmlNavigation($meta);
	htmlWavesStart();
	htmlBodyStart();
	//htmlBreadcrumb($meta);
	htmlFooter($meta);
}

function DecorPage() {
	require_once("templates/header.php");
	$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Decor, Props, Fabrication, Floral, Design, Treatment, Centerpieces";
	$meta['description'] = "Steve Beyer Productions - The Entertainment and Production Company";
	$meta['title'] = "Decor, Props, Design, Floral, Fabrication, and Treatments - Steve Beyer Productions";
	$meta['url'] = CurPageURL();
	$meta['image'] = CurServerUrl() . "sbp.png";
	htmlHeader($meta);
	htmlMasthead($meta);
	htmlNavigation($meta);
	htmlWavesStart();
	htmlBodyStart();
	//htmlBreadcrumb($meta);
	htmlFooter($meta);
}

function WeddingPage() {
	require_once("templates/header.php");
	$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Talent, Musicians, Artists, Bands, Entertainment, Decor, Production, Wedding, Special Events";
	$meta['description'] = "Steve Beyer Productions - The Entertainment and Production Company";
	$meta['title'] = "Weddings - Steve Beyer Productions";
	$meta['url'] = CurPageURL();
	$meta['image'] = CurServerUrl() . "sbp.png";
	htmlHeader($meta);
	htmlMasthead($meta);
	htmlNavigation($meta);
	htmlWavesStart();
	htmlBodyStart();
	//htmlBreadcrumb($meta);
	htmlFooter($meta);
}

function AboutPage() {
	require_once("templates/header.php");
	$meta['keywords'] = "Steve Beyer Productions, SBP, Las Vegas, Talent, Musicians, Artists, Bands, Entertainment, Decor, Production, Wedding, Special Events";
	$meta['description'] = "Steve Beyer Productions - The Entertainment and Production Company";
	$meta['title'] = "About & Contact - Steve Beyer Productions";
	$meta['url'] = CurPageURL();
	$meta['image'] = CurServerUrl() . "sbp.png";
	htmlHeader($meta);
	htmlMasthead($meta);
	htmlNavigation($meta);
	htmlWavesStart();
	htmlBodyStart();
	//htmlBreadcrumb($meta);
	htmlFooter($meta);
}

function gatherHighlightedArtists() {
	// get some artists for the homepage carousel
	global $conn;
	$query = "
		SELECT `artists`.`name`, `artists`.`display_name`, `artists`.`slug`, 
		 `artists`.`url`, `artists`.`alt_url`, `artists`.`use_display_name`,
		 `media`.`filename`, `media`.`thumbwidth`, `media`.`thumbheight`, `artists`.`aid`
		FROM `artists`
		LEFT OUTER JOIN `media` ON `media`.`aid` = `artists`.`aid`
		WHERE `artists`.`is_active` = 1 AND `artists`.`is_highlighted` = 1 AND `artists`.`is_searchable` = 1
		AND `media`.`viewable` = 1 AND `media`.`is_highlighted` = 1";
	$result = mysqli_query($conn,$query);
	$artists = array();
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$artists[$row['aid']] = $row;
		if (($_SESSION['obfuscate'] == 1) || ($row['use_display_name'] == 1) ) {
			$artists[$row['aid']]['name'] = $artists[$row['aid']]['display_name'];
			$artists[$row['aid']]['url'] = $artists[$row['aid']]['alt_url'];
		}
	}
	return ($artists);
}

function DisplayVideoPlayer($artistinfo) {
	$vidcount = $artistinfo['media']['vidcount'];
	if ($vidcount == 1) {
		?>
			<div class="artistVideoIndividual" style="text-align: center; max-width: 540px;"><div class="<?= $artistinfo['classname']; ?>" id="container<?= $artistinfo['media']['mid']; ?>">Loading video for <?= ($artistinfo['use_display_name'])? $artistinfo['display_name'] : $artistinfo['name']; ?></div></div>
		<?
	} elseif ($vidcount > 1) {
		?>
			<div class="col6 artistVideoIndividual"><div class="<?= $artistinfo['classname']; ?>" style="text-align: center; position: absolute;" id="container<?= $artistinfo['media']['mid']; ?>">Loading video for <?= ($artistinfo['use_display_name'])? $artistinfo['display_name'] : $artistinfo['name']; ?></div></div>
		<?
	}
	?>
	<script type="text/javascript">
		jwplayer('container<?= $artistinfo['media']['mid']; ?>').setup({
			'modes': [
				{type: 'html5'},
				{type: 'flash', src: '/templates/js/jwplayer/player.swf'},
				{type: 'download'}
			],
			'author': 'Steve Beyer Productions',
			'description': '<?= ($artistinfo['use_display_name'])? htmlspecialchars($artistinfo['display_name'], ENT_QUOTES) : htmlspecialchars($artistinfo['name'], ENT_QUOTES); ?>',
			'file': '/m/<?= $artistinfo['media']['filename']; ?>',
			'image': '/i/artist/<?= $artistinfo['media']['previewimage']; ?>',
			'duration': '<?= $artistinfo['media']['vidlength']; ?>',
			'controlbar': 'over',
			'shownavigation': 'true',
			'icons': false,
			'width': '<?= $artistinfo['media']['widthdisplay']; ?>',
			'stretching': 'uniform',
			<?= ($artistinfo['media']['heightdisplay'])? $artistinfo['media']['heightdisplay'] : NULL ?>
			'aspectratio': '<?= $artistinfo['media']['aspectratio']; ?>',
		});
	</script>
	<?
	/*
			//'width': '<?= $artistinfo['media']['width']; ?>',
			//'height': '<?= $artistinfo['media']['height']; ?>'
	*/
}

function getCommonDivisor($a, $b) {
	if ($a == 0 || $b == 0) {
		return abs( max(abs($a), abs($b)) );
	}
	$r = $a % $b;
	return ($r != 0) ? getCommonDivisor($b, $r) : abs($b);
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
	if (isEmpty($_REQUEST['url']) || ((string)$_REQUEST['url'] === "web_stats")) {
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
					AdminSaveSingleCategory($_REQUEST['form_cid']);
					AdminEditSingleCategory(GetCatUrlFromCID($_REQUEST['form_cid']));
					break;
				case "search_category":
					AdminListArtistsByCategory();
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
					break;
				case "del_style_for_reals":
					AdminDeleteStyleGo($_REQUEST['targetcategoryurl']);
					AdminListStyles();
					break;
				case "edit_style":
					AdminEditSingleStyle($_REQUEST['sid']);
					break;
				case "save_style":
					AdminSaveSingleStyle();
					AdminListStyles();
					break;
				case "search_style":
					AdminListArtistByStyle();
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
					AdminArtistListSearchResults();
					break;
				case "list_all":
					AdminArtistList();
					break;
				case "list_new":
					AdminArtistListNew();
					break;
				case "list_feat":
					AdminArtistListFeat();
					break;
				case "list_secret":
					AdminArtistListSecret();
					break;
				case "add_new":
					AdminArtistAddNew();
					break;
				case "edit":
					if ($_REQUEST['listpage'] > 0) {
						$aid = (preg_replace("/[^0-9]/","",$_REQUEST['listpage'])); // hack for direct URL access
					} else {
						$aid = (preg_replace("/[^0-9]/","",$_REQUEST['aid']));
					}
					switch ($_REQUEST['executeButton']) {
						case "delete":
							AdminArtistDelete($aid);
							break;
						case "update":
							AdminArtistSaveSingle();
							AdminArtistEditSingle($aid);
							break;
						default:
							AdminArtistEditSingle($aid);
					}
					break;
				case "del_artist_for_reals":
					AdminArtistDeleteGo($_REQUEST['targetcategoryurl']); // yeah, it's really the aid
					AdminArtistList();
					break;
				default:
					AdminArtistList();
			}
		}
	}
}

function AdminArtistDelete($aid) {
	global $conn;
	$nextfunction = "del_artist_for_reals";
	$urlDo = "artists";
	$urlCancel = "artists/edit/$aid";
	$query = sprintf("SELECT `name` FROM `artists` WHERE `aid` = %s", preg_replace("/[^0-9]/","",$aid));
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	$desc = $row['name'];
	AdminShowDeleteConfirmation($aid,$desc,$urlDo,$urlCancel,$nextfunction);
}

function AdminArtistAddNew() {
	if (!isset($_REQUEST['formpage'])) {
		// brand new artist
		AdminArtistFormNew();
	} elseif ((string)$_REQUEST['formpage'] === "1") {
		// attempt to save the artist info and media
		$aid = AdminArtistSaveNew();
		if (isEmpty($aid)) {
			// Error in first page, redisplay first page.
			AdminArtistFormNew();
		} else {
			// there's an $aid from saving basic data, so check that media in
			$filecount = AdminArtistSaveMedia($aid);
			if ($filecount >= 1) {
				echo "<div class='AdminSuccess'>New artist added with $filecount media files!</div>";
			} else {
				echo "<div class='AdminError'>No media was saved. Please add a photo and/or video now.</div>";
			}
			// regardless of media, we did save SOMETHING, so sets see it.
			AdminArtistEditSingle($aid);
		}
	}
}

function allPublicCategories() {
	global $conn;
	$query = "SELECT `category`,`url`,`description` FROM `categories` WHERE `published` = 1 ORDER BY `category` ASC";
	$result = mysqli_query($conn,$query);
	$categories = array();
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$categories[] = $row;
	}
	return ($categories);
}

function GatherArtistInfo($aid) {
	global $conn;
	$aid = preg_replace("/[^0-9]/",'',$aid);
	// XXX: this could be some massive joined query
	$artistinfo = array();
	if (isEmpty($aid)) {
		echo "<div class='AdminError'>Bad Request for Artist Record!</div>";
		return($artistinfo);
	}
	$query = sprintf("SELECT * FROM `artists` WHERE `aid` = %s", mysqli_real_escape_string($conn,$aid));
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	$artistinfo['aid'] = $aid;
	if (isEmpty($row['aid'])) {
		echo "<div class='AdminError'>No record available for ID Number $aid.</div>";
	} else {
		foreach ($row as $fieldname => $value) {
			$artistinfo[$fieldname] = $value;
		}
	}
	mysqli_free_result($result);
	// AdminSelectCategories($aid) AdminSelectStyles($aid) AdminSelectLocations($aid)
	// lemme have hash of media
	$query = sprintf("SELECT `cid` FROM `artistcategories` WHERE `aid` = %s", mysqli_real_escape_string($conn,$aid));
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$artistinfo['categories'][$row['cid']] = $row['cid'];
	}
	mysqli_free_result($result);
	$query = sprintf("SELECT `lid` FROM `artistlocations` WHERE `aid` = %s", mysqli_real_escape_string($conn,$aid));
	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$artistinfo['locations'][$row['lid']] = $row['lid'];
	}
	mysqli_free_result($result);
	$query = sprintf("SELECT `sid` FROM `artiststyles` WHERE `aid` = %s", mysqli_real_escape_string($conn,$aid));

	$result = mysqli_query($conn,$query);
	while ($row = mysqli_fetch_assoc($result)) {
		$artistinfo['styles'][$row['sid']] = $row['sid'];
	}
	mysqli_free_result($result);
	$query = sprintf(
		"SELECT * FROM `media` WHERE `aid` = %s ORDER BY `is_highlighted` DESC, `vidlength` ASC", 
		mysqli_real_escape_string($conn,$aid)
	);
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
	return $artistinfo;
}

function AdminArtistEditSingle($aid) {
	$artistinfo = GatherArtistInfo($aid);
	if (!isEmpty($artistinfo['name'])) {
		AdminArtistFormSingle($artistinfo);
	}
}

function ObfuscateArtistNameAutomatically($name) {
	// crappy way of guessing a band's obfuscated "display" name: first whole word, then first letter of each additional word
	// unless only two words, then initials all the way
	// logic subject to be totally changed on SB's whim
	$words = explode(" ", $name);
	$display_name = "";
	$counter = 0;
	if (count($words) > 0) {
		if (count($words) == 1) {
			$display_name = substr($name,0,3);	// one word artist name is now first 3 letters
		} elseif (count($words) == 2) {
			foreach ($words as $word) {
				$display_name .= strtoupper(substr($word,0,1));
				$display_name .= ".";
			}
		} elseif (count($words) >= 3) {
			foreach ($words as $word) {
				if (preg_match("/\b(the|group|band|of|a|an|and)\b/i",$word)) {
					$display_name .= "$word ";
				} else {
					if ($counter == 0) {
						$display_name .= strtoupper(substr($word,0,1));
						$display_name .= ". ";
						//$display_name = "$word ";
					} else {
						$display_name .= strtoupper(substr($word,0,1));
						$display_name .= ". ";
					}
				}
				$counter++;
			}
		}
	}
	return (makeCase(trim($display_name)));
}

function AdminArtistSaveSingle() {
	// Updated an artist's data from within the artist editor
	global $conn;
	global $dirlocation;
	$aid = preg_replace("/[^0-9]/",'',$_REQUEST['aid']);
	$artistinfo = GatherArtistInfo($aid);
	$artistsave = array("aid" => $aid);
	// compare any $_REQUEST stuff with existing database, update.
	// check for new file uploads, deal with.
	// return the (adjusted) $artistinfo hash
	// name
	if (isEmpty($_REQUEST['name'])) { 
		$errors[] = "Please enter the artist or act name.";
	} else {
		$name = makeCase(htmlspecialchars(convert_smart_quotes(trim($_REQUEST['name']))));
		if ($name !== $artistinfo['name']) {
			$artistsave['name'] = $name;
			$url = MakeURL(strtolower($name));
			if ($url !== $artistinfo['url']) {
				$artistsave['url'] = $url;
			}
			// check if this is a duplicate // being a real jerk by including the "cleaned" URL text
			$query = sprintf("SELECT `name` FROM `artists` WHERE `aid` <> '%s' AND (`name` = '%s' OR `url` = '%s')",
				mysqli_real_escape_string($conn, $artistsave['aid']),
				mysqli_real_escape_string($conn, $artistsave['name']),
				mysqli_real_escape_string($conn, $artistsave['url'])
			);
			$result = mysqli_query($conn,$query);
			if (mysqli_num_rows($result) > 0) {
				$errors[] = "This artist's name is already used by someone else! Please check the artist's name carefully.";
			}
		}
	}
	// slug
	if (isEmpty($_REQUEST['slug'])) {
		$errors[] = "Please provide a descriptive phrase about artist.";
	} else {
		$slug = htmlspecialchars(convert_smart_quotes(trim($_REQUEST['slug'])));
		if ($slug !== $artistinfo['slug']) {
			$artistsave['slug'] = $slug;
		}
	}
	// bio
	if (isEmpty($_REQUEST['bio'])) {
		$errors[] = "Missing the artist's bio. Please have at least a paragraph describing the artist.";
	} else {
		$bio = htmlspecialchars(convert_smart_quotes(trim($_REQUEST['bio'])));
		if ($bio !== $artistinfo['bio']) {
			$artistsave['bio'] = $bio;
		}
	}
	// display name
	if (isEmpty($_REQUEST['display_name'])) {
		$display_name = MakeCase(ObfuscateArtistNameAutomatically(htmlspecialchars(convert_smart_quotes(trim($_REQUEST['name'])))));
	} else {
		$display_name = htmlspecialchars(convert_smart_quotes(trim($_REQUEST['display_name'])));
	}
	if ($display_name !== $artistinfo['display_name']) {
			$artistsave['alt_url'] = GetAltUrl(MakeURL(preg_replace("/\.\s/","",strtolower($display_name))));
			$artistsave['display_name'] = $display_name;
	}
	// alt-url standalone change
	if (isEmpty($artistsave['alt_url'])) {
		if (!isEmpty($_REQUEST['alt_url'])) {
			$artistsave['alt_url'] = MakeURL(htmlspecialchars(strtolower(trim($_REQUEST['alt_url']))));
		} else {
			$artistsave['alt_url'] = GetAltUrl(MakeURL(strtolower($display_name)));
		}
	}
	// use display
	$use_display_name = isset($_REQUEST['use_display_name']);
	if ($use_display_name != $artistinfo['use_display_name']) {
		$artistsave['use_display_name'] = $use_display_name;
	}
	// active?
	$is_active = isset($_REQUEST['is_active']);
	if ($is_active != $artistinfo['is_active']) {
		$artistsave['is_active'] = $is_active;
	}
	// searchable?
	$is_searchable = isset($_REQUEST['is_searchable']);
	if ($is_searchable != $artistinfo['is_searchable']) {
		$artistsave['is_searchable'] = $is_searchable;
	}
	// highlighted?
	$is_highlighted = isset($_REQUEST['is_highlighted']);
	if ($is_highlighted != $artistinfo['is_highlighted']) {
		$artistsave['is_highlighted'] = $is_highlighted;
	}
	// categories update
	if (isset($_REQUEST['categories'])) {
		$categories = array();
		foreach ($_REQUEST['categories'] as $key => $value) {
			$categories[$key] = preg_replace("/[^0-9]/","",$value);
		}
		if (array_diff($categories, $artistinfo['categories']) || array_diff($artistinfo['categories'], $categories)) {
			$artistsave['artistcategories'] = $categories;	// categories have been updated
		}
	} else {
		$errors[] = "Please select one or more categories for this artist.";
	}
	// styles
	if (isset($_REQUEST['styles'])) {
		$styles = array();
		foreach ($_REQUEST['styles'] as $key => $value) {
			$styles[$key] = preg_replace("/[^0-9]/","",$value);
		}
		if (isEmpty($artistinfo['styles'])) {
			$artistsave['artiststyles'] = $styles;	// styles have been updated
		} else if (array_diff($styles, $artistinfo['styles']) || array_diff($artistinfo['styles'], $styles)) {
			$artistsave['artiststyles'] = $styles;	// styles have been updated
		}
	} else {
		$errors[] = "Please select one or more styles of entertainment this artist performs.";
	}
	// locations
	if (isset($_REQUEST['locations'])) {
		$locations = array();
		foreach ($_REQUEST['locations'] as $key => $value) {
			$locations[$key] = preg_replace("/[^0-9]/","",$value);
		}
		if (array_diff($locations, $artistinfo['locations']) || array_diff($artistinfo['locations'], $locations)) {
			$artistsave['artistlocations'] = $locations;	// styles have been updated
		}
	} else {
		$errors[] = "Please select one or more cities that this artist is local to.";
	}
	if (!isset($errors)) {
		if (count($artistsave) > 1) { // more than just the AID...
			foreach ($artistsave as $field => $value) {
				if (preg_match("/(aid|artistcategories|artiststyles|artistlocations)/",$field)) {
					continue;
				} else {
					$query = sprintf("UPDATE `artists` SET `%s` = '%s' WHERE `aid` = %s",
						mysqli_real_escape_string($conn, $field),
						mysqli_real_escape_string($conn, $value),
						mysqli_real_escape_string($conn, $artistsave['aid'])
					);
					if (mysqli_query($conn,$query) === FALSE) {
						$errors[] = "<B>Did not update artist!</B> Database Failure: ".mysqli_error($conn);
					}
				}
			}
			// update timestamp
			$query = sprintf("UPDATE `artists` SET `last_updated` = '%s' WHERE `aid` = %s",
				mysqli_real_escape_string($conn, DatePHPtoSQL(time())),
				mysqli_real_escape_string($conn, $artistsave['aid'])
			);
			if (mysqli_query($conn,$query) === FALSE) {
				$errors[] = "<B>Did not update artist information!</B> Database Failure: ".mysqli_error($conn);
			}
		}
		foreach (array("artistcategories" => "cid","artiststyles" => "sid","artistlocations" => "lid") as $table => $column) {
			if (count($artistsave[$table]) > 0) {
				// wipe out old data
				$query = sprintf("DELETE FROM `%s` WHERE `aid` =  '%s'", 
					mysqli_real_escape_string($conn, $table),
					mysqli_real_escape_string($conn, $artistsave['aid'])
				);
				mysqli_query($conn,$query);
				// pump in new data
				foreach($artistsave[$table] as $id) { // individual cid, sid, or lid items that we're saving
					$query = sprintf("INSERT INTO `%s` (`%s`,`aid`) VALUES (%s,%s)",
						mysqli_real_escape_string($conn, $table),
						mysqli_real_escape_string($conn, $column),
						mysqli_real_escape_string($conn, $id),
						mysqli_real_escape_string($conn, $artistsave['aid'])
					);
					if (mysqli_query($conn,$query) === FALSE) {
						$errors[] = "Error saving category $cid for $aid!" .mysqli_error($conn);
					}
				}
			}// else no category/style/locaiton changes
		}
		// Save Uploaded Files
		$filecount = AdminArtistSaveMedia($aid);
		if ($filecount >= 1) {
			echo "<div class='AdminSuccess'>Added $filecount additional media files!</div>";
		}
		// Modify existing photos
		if (isset($_REQUEST['ImageFeatures'])) {
			foreach ($_REQUEST['ImageFeatures'] as $mid => $change) {
				$mid = preg_replace("/[^0-9]/","",$mid);
				if ($artistinfo['media']['mid'][$mid] !== $mid) {
					echo "<div class='AdminError'>Media request is not valid.</div>";
				} else {
					// legit media id, do the request
					foreach (array("ToggleHighlight" => "is_highlighted", "ToggleHidden" => "viewable", "Remove" => "") as $action => $column) {
						if ($_REQUEST['ImageFeatures'][$mid] === $action) {
							if ($action === "Remove") {
								unlink($dirlocation . "/i/artist/original-" . $artistinfo['media']['filename'][$mid]);
								unlink($dirlocation . "/i/artist/" . $artistinfo['media']['filename'][$mid]);
								$query = sprintf("DELETE FROM `media` WHERE `mid` = '%s'", mysqli_real_escape_string($conn,$mid));
							} else {
								($artistinfo['media'][$column][$mid] == 1)? $switcheroo = 0 : $switcheroo = 1;
								$query = sprintf("UPDATE `media` SET `%s` = '%s' WHERE `mid` = '%s'",
									mysqli_real_escape_string($conn, $column),
									mysqli_real_escape_string($conn, $switcheroo),
									mysqli_real_escape_string($conn, $mid)
								);
							}
							if (mysqli_query($conn,$query) === FALSE) {
								$errors[] = "Error updating image status $mid!" .mysqli_error($conn);
							}
						}
					}
				}
			}
		}
		// Modify video screen shot
		if (isset($_REQUEST['radio'])) {
			foreach ($_REQUEST['radio'] as $mid => $change) {
				$mid = preg_replace("/[^0-9]/","",$mid);
				if ($artistinfo['media']['mid'][$mid] !== $mid) {
					echo "<div class='AdminError'>Media request is not valid.</div>";
				} else {
					$change = preg_replace("/[^0-9]/","",$change);
					$fileid = substr($artistinfo['media']['filename'][$mid], 0, -4);
					// preview images are in jpg format from ResizeImage() and ffmpeg thumbnailer
					if (!copy("$dirlocation/i/artist/$fileid-$change.jpg","$dirlocation/i/artist/$fileid.jpg")) {
						$errors[] = "Failed to replace video thumbnail.";
					}
				}
			}
		}
		// process video actions
		if (isset($_REQUEST['videoaction'])) {
			foreach ($_REQUEST['videoaction'] as $mid => $change) {
				$mid = preg_replace("/[^0-9]/","",$mid);
				if ($artistinfo['media']['mid'][$mid] !== $mid) {
					echo "<div class='AdminError'>Media request is not valid.</div>";
				} else {
					if ($_REQUEST['videoaction'][$mid] === "delete") {
						$fileid = substr($artistinfo['media']['filename'][$mid], 0, -4);
						unlink ("$dirlocation/m/". $artistinfo['media']['filename'][$mid]);
						unlink ("$dirlocation/i/artist/$fileid.jpg");	// video screenies are always jpeg.
						unlink ("$dirlocation/i/artist/$fileid-1.jpg");
						unlink ("$dirlocation/i/artist/$fileid-2.jpg");
						unlink ("$dirlocation/i/artist/$fileid-3.jpg");
						unlink ("$dirlocation/i/artist/$fileid-4.jpg");
						$query = sprintf("DELETE FROM `media` WHERE `mid` = %s", mysqli_real_escape_string($conn,$mid));
					} else {
						$value = preg_replace("/[^01]/","",$_REQUEST['videoaction'][$mid]);
						$query = sprintf("UPDATE `media` SET `viewable` = %s WHERE `mid` = %s",
							mysqli_real_escape_string($conn,$value),
							mysqli_real_escape_string($conn,$mid)
						);
					}
					if (mysqli_query($conn,$query) === FALSE) {
							$errors[] = "Error updating video status $mid!" .mysqli_error($conn);
					}
				}
			}
		} // no video changes
	} // else there are errors!
	if (isset($errors)) { // not included above since new errors could have been introduced
		echo "<div class='AdminError'><B>There are some missing details preventing us from saving this artist.</B><ul>";
		foreach ($errors as $error) {
			echo "<li>$error</li>";
		}
		echo "</ul></div>\n";
	}
	return($aid); // or null if bad
}

function PrepareVideoPlayer($input) {
	// Put video(s) into jwplayer
	global $conn;
	global $videowidth;
	if (is_array($input)) {
		$artistinfo = $input;
		$videocount = 0;
		// I am the artistinfo's media keyed array
		// If this is used, SHOW ALL (viewable) VIDEOS
		//if (is_array($artistinfo['media'])) { // are there videos here (fixes error on the foreach line below)
		if (count($artistinfo['media']['vidlength']) > 0) { // This too does not actually count videos. grr, c'mon.
			// run the loop once so I have an accurate video count plz
			foreach ($artistinfo['media']['mid'] as $mid) {
				if (((string)$_REQUEST['page'] === 'admin' OR (string)$artistinfo['media']['viewable'][$mid] == '1') AND ($artistinfo['media']['vidlength'][$mid] > 0)) {
					$videocount++;
				}
			}
			foreach ($artistinfo['media']['mid'] as $mid) {
				// if in the admin page, or is viewable, and media is a video, ...
				if (((string)$_REQUEST['page'] === 'admin' OR (string)$artistinfo['media']['viewable'][$mid] == '1') AND ($artistinfo['media']['vidlength'][$mid] > 0)) {
					// single out the one media ID for the Video Player
					$tempartistinfo = $artistinfo;
					unset ($tempartistinfo['media']);	// dump all the media info on this artist, replacing with the one video to display
					$gcd=getCommonDivisor($artistinfo['media']['width'][$mid],$artistinfo['media']['height'][$mid]);
					if ((string)$_REQUEST['page'] === "admin") {
						// make video players a reasonable size
						if ($artistinfo['media']['width'][$mid] > $videowidth) {
							$width = $artistinfo['media']['width'][$mid];
							$height = $artistinfo['media']['height'][$mid];
							$scale = $width / $videowidth;
							$tempartistinfo['media']['width'] = ceil($width / $scale);
							$tempartistinfo['media']['height'] = ceil($height / $scale);
							$tempartistinfo['media']['widthdisplay'] = $tempartistinfo['media']['width'];
							$tempartistinfo['media']['heightdisplay'] = "'height': '". $tempartistinfo['media']['height'] ."',";
						}
					} else {
						$tempartistinfo['media']['width'] = $artistinfo['media']['width'][$mid];
						$tempartistinfo['media']['height'] = $artistinfo['media']['height'][$mid];
						$tempartistinfo['media']['widthdisplay'] = "100%";
					}
					$tempartistinfo['media']['realdimensions'] = $artistinfo['media']['width'][$mid] . "x" . $artistinfo['media']['height'][$mid];
					$tempartistinfo['media']['aspectratio'] = ($artistinfo['media']['width'][$mid]/$gcd) . ":" . ($artistinfo['media']['height'][$mid]/$gcd);
					$tempartistinfo['media']['mid'] = $artistinfo['media']['mid'][$mid];
					$tempartistinfo['media']['previewimage'] = substr($artistinfo['media']['filename'][$mid],0,-4) . ".jpg";
					$tempartistinfo['media']['vidlength'] = $artistinfo['media']['vidlength'][$mid];
					$tempartistinfo['media']['name'] = $artistinfo['media']['name'][$mid];
					$tempartistinfo['media']['filename'] = $artistinfo['media']['filename'][$mid];
					$tempartistinfo['media']['fileid'] = substr($artistinfo['media']['filename'][$mid], 0, -4);
					$tempartistinfo['media']['is_highlighted'] = $artistinfo['media']['is_highlighted'][$mid];
					$tempartistinfo['media']['viewable'] = $artistinfo['media']['viewable'][$mid];
					if ((string)$_REQUEST['page'] === 'admin') {
						$tempartistinfo['media']['published'] = $artistinfo['media']['published'][$mid];
						if ((string)$artistinfo['media']['viewable'][$mid] === '1') {
							$tempartistinfo['classname'] = "VideoPlayer";
						} else {
							$tempartistinfo['classname'] = "VideoPlayerNOVIEW";
						}
						echo sprintf(
							"<div class='AdminVideoTitle'>%s (%s) %s</div>",
							$artistinfo['media']['name'][$mid],
							date("i:s",($artistinfo['media']['vidlength'][$mid])),
							date("F d, Y",$artistinfo['media']['published'][$mid])
						);
					}
					$tempartistinfo['media']['vidcount'] = $videocount; // how many videos are there for this artist?
					DisplayVideoPlayer($tempartistinfo);
					if ((string)$_REQUEST['page'] === 'admin') {
						AdminVideoPreviewChooser($tempartistinfo);
					}
				}
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
	if ($type === "list_all" || $type === "list_new") {
		$query = "SELECT COUNT(*) FROM `artists`";
	} else if ($type === "list_feat") {
		$query = "SELECT COUNT(*) FROM `artists` WHERE `is_highlighted` = 1";
	} else if ($type === "list_secret") {
		$query = "SELECT COUNT(*) FROM `artists` WHERE `is_searchable` = 0 OR `is_active` = 0";
	}
	$result = mysqli_query($conn,$query);
	list($count) = mysqli_fetch_array($result);
	mysqli_free_result($result);
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
	(isEmpty($_REQUEST['name']))? $errors[] = "Please enter the artist or act name." : $name = htmlspecialchars(makeCase(convert_smart_quotes(trim($_REQUEST['name']))));
	(isEmpty($_REQUEST['slug']))? $errors[] = "Please provide a descriptive phrase about artist." : $slug = htmlspecialchars(makeCase(convert_smart_quotes(trim($_REQUEST['slug']))));
	// XXX: we don't deal with CR/newlines or html/markup at all in bio field yet!
	(isEmpty($_REQUEST['bio']))? $errors[] = "Missing the artist's bio. Please have at least a paragraph describing the artist." : $bio = htmlspecialchars(convert_smart_quotes(trim($_REQUEST['bio'])));
	if (isEmpty($_REQUEST['display_name'])) {
		$display_name = ObfuscateArtistNameAutomatically(htmlspecialchars(convert_smart_quotes(trim($_REQUEST['name']))));
	} else {
		$display_name = htmlspecialchars(makeCase(convert_smart_quotes(trim($_REQUEST['display_name']))));
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
	$alt_url = GetAltUrl(MakeURL(strtolower($display_name)));	// what's the URL if we're in use_display_name mode?  XXX: This is pretty retarded. I want full names in URL for SEO.  even specifiying an URL at all is unnecessary since going to just search on name anyways.
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
			echo "<div class='AdminSuccess'>Artist information for <B>$name</B> updated!</div>";
		} else {
			$errors[] = "<B>Did not update artist!</B> Database Failure: ".mysqli_error($conn);
		}
	}
	if (isset($errors)) {
		echo "<div class='AdminError'><B>There are some missing details preventing us from updating this artist.</B><ul>";
		foreach ($errors as $error) {
			echo "<li>$error</li>";
		}
		echo "</ul></div>\n";
	}
	return($aid); // or null if bad
}

function GetAltUrl($alturl) {
	global $conn;
	$query = sprintf("SELECT `alt_url` FROM `artists` WHERE `alt_url` = '%s'", mysqli_real_escape_string($conn,$alturl));
	$result = mysqli_query($conn,$query);
	if (mysqli_num_rows($result) == 0) {
		return ($alturl);
	} else {
		// go loop for a new alturl
		$number = 1;
		while (!$done) {
			$alturl = "$alturl$number";
			$query = sprintf("SELECT `alt_url` FROM `artists` WHERE `alt_url` = '%s'", mysqli_real_escape_string($conn,$alturl));
			$result = mysqli_query($conn,$query);
			(mysqli_num_rows($result) == 0)? $done++ : $number++; 
		}
		return ($alturl);
	}
}

function AdminArtistSaveMedia($aid) {
	global $conn;
	$savedfilecount = 0;
	// page 1's save artist's media
	if (CheckForFiles()) {
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
				"0",	// XXX: we don't highlight on upload, user gotta select one manually.
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

function AdminArtistListNew() {
	global $conn;
	global $pagination;
	if ($_REQUEST['listpage'] > 0) {
		$page = preg_replace("/[^0-9]/","",$_REQUEST['listpage']);
	} else {
		$page = 1;
	}
	$limit_start = (abs($page - 1) * $pagination);
	$limit_end = $pagination;
	$query = sprintf("SELECT * FROM `artists` ORDER BY `last_updated` DESC LIMIT %s,%s",
		mysqli_real_escape_string($conn,$limit_start),
		mysqli_real_escape_string($conn,$limit_end)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	AdminArtistListPageNew($result,$page);
	mysqli_free_result($result);
}

function AdminArtistListFeat() {
	global $conn;
	global $pagination;
	if ($_REQUEST['listpage'] > 0) {
		$page = preg_replace("/[^0-9]/","",$_REQUEST['listpage']);
	} else {
		$page = 1;
	}
	$limit_start = (abs($page - 1) * $pagination);
	$limit_end = $pagination;
	$query = sprintf("SELECT * FROM `artists` WHERE `is_highlighted` = 1 ORDER BY `name` LIMIT %s,%s",
		mysqli_real_escape_string($conn,$limit_start),
		mysqli_real_escape_string($conn,$limit_end)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	AdminArtistListPageFeat($result,$page);
	mysqli_free_result($result);
}

function AdminArtistListSecret() {
	global $conn;
	global $pagination;
	if ($_REQUEST['listpage'] > 0) {
		$page = preg_replace("/[^0-9]/","",$_REQUEST['listpage']);
	} else {
		$page = 1;
	}
	$limit_start = (abs($page - 1) * $pagination);
	$limit_end = $pagination;
	$query = sprintf("SELECT * FROM `artists` WHERE `is_active` = 0 OR `is_searchable` = 0 ORDER BY `is_active` DESC, `name` LIMIT %s,%s",
		mysqli_real_escape_string($conn,$limit_start),
		mysqli_real_escape_string($conn,$limit_end)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	AdminArtistListPageSecret($result,$page);
	mysqli_free_result($result);
}

function AdminListArtistsByCategory() {
	global $conn;
	// wtf is the cid for categoryurl
	$query = sprintf(
		"SELECT `cid` FROM `categories` WHERE `url` = '%s'",
		mysqli_real_escape_string($conn,$_REQUEST['categoryurl'])
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	// wtf are all the aid's associated with the one cid?
	$query = sprintf(
		"SELECT * FROM `artists` LEFT OUTER JOIN `artistcategories` ON `artists`.`aid` = `artistcategories`.`aid` WHERE `artistcategories`.`cid` = %s ORDER BY `artists`.`name`",
		mysqli_real_escape_string($conn,$row['cid'])
	);
	$result = mysqli_query($conn,$query);
	// XXX: no pagination, just a long listing
	AdminArtistListPageByCategory($result,$page);
	mysqli_free_result($result);
}

function AdminListArtistByStyle() {
	global $conn;
	// wtf are all the aid's associated with the one sid?
	$query = sprintf(
		"SELECT * FROM `artists` LEFT OUTER JOIN `artiststyles` ON `artists`.`aid` = `artiststyles`.`aid` WHERE `artiststyles`.`sid` = %s ORDER BY `artists`.`name`",
		mysqli_real_escape_string($conn,preg_replace("/[^0-9]/","",$_REQUEST['sid']))
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	// XXX: no pagination, just a long listing
	AdminArtistListPageByStyle($result,$page);
	mysqli_free_result($result);
}

function AdminArtistListSearchResults() {
	global $conn;
	if (isEmpty($_REQUEST['q'])) {
		$search = htmlspecialchars(strip_tags(strtolower(trim($_REQUEST['listpage']))));
	} else {
		$search = htmlspecialchars(strip_tags(strtolower(trim($_REQUEST['q']))));
	}
	$query = sprintf(
		"SELECT * FROM `artists` WHERE `name` LIKE '%%%s%%' ORDER BY `name`",
		mysqli_real_escape_string($conn,$search)
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	// XXX: no pagination, just a long listing
	AdminArtistListPageBySearchResult($result,$page);
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
	if ( (isEmpty($_REQUEST['city'])) || (strlen($_REQUEST['state']) != 2) ) {
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
	if ( (isEmpty($_REQUEST['city'])) || (strlen($_REQUEST['state']) != 2) ) {
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
	if (isEmpty($name)) {
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

function AdminDeleteStyle($targetcategoryurl) {
	global $conn;
	$query = sprintf(
		"SELECT `name` FROM `styles` WHERE `sid` = %s",
		mysqli_real_escape_string($conn,preg_replace("/[^0-9]/","",$_REQUEST['sid']))
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	$nextfunction = "del_style_for_reals";
	$url = "styles_list";
	AdminShowDeleteConfirmation($targetcategoryurl,$row['name'],$url,$url,$nextfunction);  
}

function AdminDeleteStyleGo($sid) {
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
	if (isEmpty($_REQUEST['name'])) {
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
	if ( (isEmpty($_REQUEST['form_url'])) || (isEmpty($_REQUEST['form_category'])) || (isEmpty($_REQUEST['form_description'])) ) {
		echo "<div class='AdminError'>Please fill in all three Category fields</div>";
	} else {
		$cid = preg_replace("/\[^0-9]/","",trim($cid));
		$url = preg_replace("/ /","_",strtolower(strip_tags(trim($_REQUEST['form_url']))) );
		$category = htmlspecialchars(trim($_REQUEST['form_category']));
		if (strlen($_REQUEST['published'])) {
			$published = TRUE;
		} else {
			$published = FALSE;
		}
		// do highlighted carousel image
		$highlighted = FALSE;
		if (strlen($_REQUEST['is_highlighted'])) {
			list ($highlighted_fileid, $highlighted_filename) = SaveCategoryHighlight(); // for Highghlighted Carousel image, only one file uploaded.
			if (!isEmpty($highlighted_fileid)) {
				$query = sprintf("UPDATE `categories` SET `carousel_filename` = '%s', `carousel_id` = '%s' WHERE `cid` = '%s'",
					mysqli_real_escape_string($conn,$highlighted_filename),
					mysqli_real_escape_string($conn,$highlighted_fileid),
					mysqli_real_escape_string($conn,$cid)
				);
				if (mysqli_query($conn,$query) === TRUE) {
					echo "<div class='AdminSuccess'>Category Entry <B>$category</B> [$url] Carousel Image Successfully Updated.</div>";
				} else {
					echo "<div class='AdminError'>Category Entry <B>$category</B> [$url] Failed to Update Carousel Image!<br>". mysqli_error($conn) ."</div>";
				}
			}
			// XXX: I'm leaving behind the old category carousel image.
			$highlighted = TRUE;
		}
		if ($filename) {
			// delete the old category image file from the system
			$query = sprintf("SELECT `image_id` FROM `categories` WHERE `cid` = '%s'", mysqli_real_escape_string($conn,$cid));
			$result = mysqli_query($conn,$query);
			list($old_fileid) = mysqli_fetch_array($result);
			unlink("$dirlocation/i/category/$old_fileid");
			unlink("$dirlocation/i/category/original-$old_fileid"); // XXX: we're not deleting jpegs, only png.
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `force_display_names` = '%s', `published` = '%s', `image_filename` = '%s', `image_id` = '%s', `is_highlighted` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'",
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,preg_replace("/[^YNI]/","",$_REQUEST['force_display_names'])),
				mysqli_real_escape_string($conn,$published),
				mysqli_real_escape_string($conn,$filename),
				mysqli_real_escape_string($conn,$newfileid),
				mysqli_real_escape_string($conn,$highlighted),
				mysqli_real_escape_string($conn,DatePHPtoSQL(time())),
				mysqli_real_escape_string($conn,$cid)
			);
		} else {
			$query = sprintf("UPDATE `categories` SET `url` = '%s', `category` = '%s', `description` = '%s', `force_display_names` = '%s', `published` = '%s', `is_highlighted` = '%s', `last_updated` = '%s' WHERE `cid` = '%s'",
				mysqli_real_escape_string($conn,$url),
				mysqli_real_escape_string($conn,$category),
				mysqli_real_escape_string($conn,htmlspecialchars(trim($_REQUEST['form_description']))),
				mysqli_real_escape_string($conn,preg_replace("/[^YNI]/","",$_REQUEST['force_display_names'])),
				mysqli_real_escape_string($conn,$published),
				mysqli_real_escape_string($conn,$highlighted),
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
	$aid = preg_replace("/[^0-9]/","",$aid);
	// Delete media files from filesystem
	$query = "SELECT `filename`,`filetype` FROM `media` WHERE `aid` = $aid";
	$result = mysqli_query($conn, $query);	
	while ($row = mysqli_fetch_assoc($result)) {
		($row['filetype'] == "mp4")? $type = "m" : $type = "i";
		$filename = $row['filename'];
		unlink ("$dirlocation/$type/artist/$filename");
		unlink ("$dirlocation/$type/artist/original-$filename");
	}
	// delete from database tables
	$tables = array("artiststyles","artistcategories","artistlocations","artistmembers","media","artists");
	$error = 0;
	foreach ($tables as $table) {
		$query = "DELETE FROM `$table` WHERE `aid` = $aid";
		if (mysqli_query($conn,$query) === FALSE) {
			echo "<div class='AdminError'>Error deleting $aid from $table: ". mysqli_error($conn) ."</div>";
			$error++;
		}
	}
	if ($error == 0) {
		echo "<div class='AdminSuccess'>Who? What? Them be gone, Man.</div>";
	}
}

function AdminDeleteCategoryGo($targetcategoryurl) {
	global $conn;
	global $dirlocation;
	// fetch us the category id for tihs url
	// XXX: could this be some sort of joined thing?  Yeah.  Am I doing it?  Dunno how.  Does it matter?  Not this time.
	$query = sprintf("SELECT `cid`,`image_id`,`carousel_id` FROM `categories` WHERE `url` = '%s'",
		mysqli_real_escape_string($conn,$targetcategoryurl)
	);
	$result = mysqli_query($conn,$query);
	list($cid,$fileid,$carousel_id) = mysqli_fetch_array($result);
	if (isEmpty($cid)) {
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
	@unlink("$dirlocation/i/category/$fileid");
	@unlink("$dirlocation/i/category/original-$fileid"); // XXX: we're not deleting jpegs, only png.
	if (!isEmpty($carousel_id)) {
		unlink("$dirlocation/i/category/$carousel_id");
		unlink("$dirlocation/i/category/original-$carousel_id");
	}
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
			"published" => $row['published'],
			"highlighted" => $row['is_highlighted']
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

function CategoryNameFromURL($caturl) {
	// simple turn categoryurl to nice category name
	global $conn;
	$query = sprintf("SELECT `category` FROM `categories` WHERE `url` = '%s'", mysqli_real_escape_string($conn,$caturl));
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	return ($row['category']);
}

function GetCatUrlFromCID($cid) {
	// simple turn categoryid to url, since categories was first thing I wrote and did it wrong
	global $conn;
	$query = sprintf("SELECT `url` FROM `categories` WHERE `cid` = %s", mysqli_real_escape_string($conn,preg_replace("/[^0-9]/","",$cid)));
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	return ($row['url']);
}

function StyleNameFromSID($sid) {
	// simple turn styleID into name
	global $conn;
	$query = sprintf(
		"SELECT `name` FROM `styles` WHERE `sid` = %s",
		mysqli_real_escape_string($conn,preg_replace("/[^0-9]/","",$sid))
	);
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	return($row['name']);
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
	if (isEmpty($_REQUEST['form_category']) || isEmpty($_REQUEST['form_description'])) {
		echo "<div class='AdminError'>Please fill in Category Name, Description and the Category Graphic</div>";
	} else {
		$category = htmlspecialchars(ucwords(trim($_REQUEST['form_category'])));
		$url = preg_replace("/ /","_",strtolower(strip_tags(trim($_REQUEST['form_url']))) );
		if (isEmpty($url)) {
			$url = MakeURL(strtolower($category));
		}

		if (CheckForFiles()) {
			list ($fileid, $filename) = SaveFile("category")[0]; // for Categories, only one image uploaded.
			$newfileid = ResizeImage($fileid,"category"); // 728x90
		}

		// do highlighted carousel image
		$highlighted_fileid = NULL;
		$highlighted_filename = NULL;
		$highlighted = FALSE;
		if (strlen($_REQUEST['highlighted'])) {
			list ($highlighted_fileid, $highlighted_filename) = SaveCategoryHighlight(); // for Highghlighted Carousel image, only one file uploaded.
			$highlighted = TRUE;
		}

		if (strlen($_REQUEST['published'])) {
			$published = TRUE;
		} else {
			$published = FALSE;
		}
		$query = sprintf("INSERT INTO `categories` (`url`,`category`,`description`,`force_display_names`,`published`,`is_highlighted`,`carousel_id`,`carousel_filename`,`image_filename`,`image_id`, `last_updated`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
			mysqli_real_escape_string($conn,$url),
			mysqli_real_escape_string($conn,$category),
			mysqli_real_escape_string($conn,htmlspecialchars(ucwords(trim($_REQUEST['form_description'])))),
			mysqli_real_escape_string($conn,preg_replace("/[^YNI]/","",$_REQUEST['force_display_names'])),
			mysqli_real_escape_string($conn,$published),
			mysqli_real_escape_string($conn,$highlighted),
			mysqli_real_escape_string($conn,$highlighted_fileid),
			mysqli_real_escape_string($conn,$highlighted_filename),
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
	if ($origimage) {
		if (preg_match("/category/",$purpose)) {
			$width = 728;
			$height = 90;
		}
		if (preg_match("/artist/",$purpose)) {
			$height = 450;
			$width = abs(round( (imagesX($origimage) / imagesY($origimage)) * $height ));
		}
		if (preg_match("/carousel/",$purpose)) {
			$height = 400;
			$width = 266;
		}
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

function SaveCategoryHighlight() {
	// save categorty highlight image as a unique ID, returning back the id and "file name"
	// I'm repeating code because I suck sometimes
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
	$gotafile = FALSE;
	//make the filename safe
	$filename = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $_FILES['carousel_image']['name']);
	$errorIndex = $_FILES['carousel_image']['error'];
	if ($errorIndex > 0) {
		if ( ($errorIndex != 4) && (!$gotafile) ) {
			// listen, we got at least one file, so I no longer care about "no file uploaded" errors.
			$error_message = $error_types[$_FILES['carousel_image']['error']];
			echo "<div class='AdminError'>File Upload Error: $error_message.</div>";
 			$happyuploads[] = array(NULL,NULL);
		}
	} else {
		$fileid = uniqid();
		// XXX: I am a race condition, where my unconfirmed file name is exposed on the webs
		move_uploaded_file($_FILES['carousel_image']['tmp_name'], $dirlocation . "/i/category/original-" . $fileid );
		if (filesize($dirlocation . "/i/category/original-" . $fileid) < 1024) {
			// if the file is smaller than 1kb, I don't trust it.
			unlink($dirlocation . "/i/category/original-" . $fileid);
			echo "<div class='AdminError'>File Upload Error: File is invalid due to small size.</div>";
			$happyuploads[] = array(NULL,NULL);
		} else {
			// Yay, its a file!  Lets totally blow off the given file name and replace with my own.
			$finfo = finfo_open(FILEINFO_MIME);
			$type = finfo_file($finfo, $dirlocation . "/i/category/original-" . $fileid);
			if (preg_match("/jpeg/i",$type)) {
				$newfileid = "$fileid.jpg";	
				rename (
					$dirlocation . "/i/category/original-". $fileid,
					$dirlocation . "/i/category/original-". $newfileid
				);
			} elseif (preg_match("/png/i",$type)) {
				$newfileid = "$fileid.png";
				rename (
					$dirlocation . "/i/category/original-". $fileid,
					$dirlocation . "/i/category/original-". $newfileid
				);
			} 
			$happyuploads[] = array($newfileid,$filename);
			$gotafile = TRUE;
		}
	}
	// XXX: Yes, this whole routine is retarded. I just want it over with.
	// get the damn category highlight carousel image, do the thing, move the hell on.
	list ($fileid, $filename) = $happyuploads[0]; // for Highghlighted Carousel image, only one file uploaded.
	if (preg_match("/\.jpg/",$fileid)) {
		$origimage = imagecreatefromjpeg("$dirlocation/i/category/original-$fileid");
	} elseif (preg_match("/\.png/",$fileid)) {
		$origimage = imagecreatefrompng("$dirlocation/i/category/original-$fileid");
		imagealphablending($origimage, true);
		imagesavealpha($origimage, true);
	}
	// XXX: hardcoded carousel highlighted size!
	if ($origimage) {
		$height = 266;
		$width = 400;
		$newimage = imagecreatetruecolor($width,$height);
		imagesavealpha($newimage, true);
		$color = imagecolorallocatealpha($newimage,0x00,0x00,0x00,127);
		imagefill($newimage, 0, 0, $color);
		// dest , src , x dest, y dest , x src , y src , dest w, dest h, src w, src h
		if (!imagecopyresampled($newimage,$origimage,0, 0, 0, 0, $width, $height, imagesX($origimage), imagesY($origimage))) {
			echo "<div class='AdminError'>Highlight Image No Web Resize/Compress WTF $fileid</div>";
		}
		// if its a category, only do a transparent png.  Artist, whatever came in.
		if (preg_match("/\.jpg/",$fileid)) {
			$newfilename = substr($fileid,0,-4) . ".jpg";
			imagejpeg($newimage, "$dirlocation/i/category/$newfilename", 80); // http://www.ebrueggeman.com/blog/php_image_optimization
		} else if (preg_match("/\.png/",$fileid)) {
			$newfilename = substr($fileid,0,-4) . ".png";
			imagepng($newimage, "$dirlocation/i/category/$newfilename",9);
		}
		imagedestroy($origimage);
		imagedestroy($newimage);
	}
	$alldone = array($fileid,$filename);
	return($alldone);
}

function ShowPhotoArray($mediadata) {
	// show a bunch of photos
	// argument is just $artistinfo['media']
	global $conn;
	$photosorder = array();
	$videos = array();
	if (is_array($mediadata)) {
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
			} else {
				$videos[] = $mid;
			}
		}
	} else {
		if ($_REQUEST['page'] === 'admin') {
			echo "<div class='AdminError'>No Photos Available for this Artist!</div>";
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
			$highlightstatus = "Highlighted";
		} else {
			$highlighted = "Highlight This";
			$highlightstatus = "Not Highlighted";
		}
		if ($mediadata['viewable'][$mid] == 1) {
			$viewable = "Make Hidden";
			$viewablestatus = "Visible";
		} else {
			$viewable = "Make Visible";
			$viewablestatus = "Hidden";
		}
		if (strlen($mediadata['name'][$mid]) > 18) {
			$filename = htmlspecialchars(substr($mediadata['name'][$mid],0,18) . "...");
		} else {
			$filename = htmlspecialchars($mediadata['name'][$mid]);
		}
		// attempt to provide video preview image options
		$videothumboptions = "";
		foreach ($videos as $vidmid) {
			$videothumboptions .= sprintf(
				"<option value='VidThumb%s'>Video %s</option>",
				$mid,
				htmlspecialchars(substr($mediadata['name'][$vidmid],0,16))
			);
		}
		$string = sprintf(
			// fields
			"<div class='CheckBoxImageContainer'>".
			"<a href='/i/artist/%s' target='_new' border='0'>".
			"<img class='%s' src='/i/artist/%s' data-width='%s' data-height='%s' alt='%s' title='%s'>".
			"</a>".
			"<div class='%s'></div>".
			"<select name='ImageFeatures[%s]' class='DropDownImage' %s>". // final %s colors list pink if not viewable
			"<option value='' disabled='disabled' selected='selected'>Image Features</option>".
			"<option value='ToggleHighlight'>%s</option>".
			"<option value='ToggleHidden'>%s</option>".
			"$videothumboptions". 
			"<option value='Remove'>Delete Image</option>".
			"<optgroup disabled='disabled' label='Image Info'>".
			"<option value='' disabled='disabled'>%s</option>".	// filename
			"<option value='' disabled='disabled'>%s</option>".	// highlightstatus
			"<option value='' disabled='disabled'>%s</option>".	// viewablestatus
			"<option value='' disabled='disabled'>Size: %sx%s</option>".
			"<option value='' disabled='disabled'>Uploaded: %s</option>".
			"</select></div>\n",
			// values
			"original-".$mediadata['filename'][$mid],
			$highlightclass,
			$mediadata['filename'][$mid],
			$mediadata['thumbwidth'][$mid],
			$mediadata['thumbheight'][$mid],
			$mediadata['name'][$mid],
			$mediadata['name'][$mid],
			$adminimageicon,
			$mediadata['mid'][$mid],
			((int)$mediadata['viewable'][$mid] === 0)? 'style="background-color: #FFBABA" ' : '',
			$highlighted,
			$viewable,
			$filename,
			$highlightstatus,
			$viewablestatus,
			$mediadata['width'][$mid],
			$mediadata['height'][$mid],
			date("M d, Y",$mediadata['published'][$mid])
		);
		echo "$string\n";
	}
}

function UploadProgress() {
	// ajax file upload progress percentage for URL /uploadprogress
	$key = ini_get("session.upload_progress.prefix") . "sbpform";
	if (!empty($_SESSION[$key])) {
		$current = $_SESSION[$key]["bytes_processed"];
		$total = $_SESSION[$key]["content_length"];
		echo $current < $total ? ceil($current / $total * 100) : 100;
	} else {
		echo "100";
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
	// XXX: Using underscore for spaces per LeviV
	// XXX: Back to using dashes, since Google SEO prefers it.
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

function isEmpty($var, $allow_false = false, $allow_ws = false) {
	// freaking sick of trim, strlen, empty, and isset weirdness
	// XXX: I put a @ infront of trim here cause I sent arrays thru this and trim() no like.
	if (!isset($var) || is_null($var) || ($allow_ws == false && @trim($var) == "" && !is_bool($var)) || ($allow_false === false && is_bool($var) && $var === false) || (is_array($var) && empty($var))) {   
		return true;
	} else {
		return false;
	}
}

//Converts a string to Title Case based on one set of title case rules
// put <no_parse></no_parse> around content that you don't want to be parsed by the title case rules
function makeCase($string) {
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
			$new = preg_replace("/(\s|\"|\'){1}(a\.){1}(\s|,|\.|\"|\'|:|!|\?|\*){1}/ie","'\\1A.\\3'",$new); // Caps for "A."
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

function ScriptTime($starttime) {
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $endtime = $mtime; $totaltime = ($endtime - $starttime) * 1000;
	$totaltime = sprintf("%.2f", $totaltime);
	echo "<!-- This page was created in ".$totaltime." milliseconds -->"; 
}

function CurPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function CurServerURL() {
	$serverURL = "http";
	if ($_SERVER["HTTPS"] == "on") {
		$serverURL .= "s";
	}
	$serverURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$serverURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	} else {
		$serverURL .= $_SERVER["SERVER_NAME"];
	}
	$serverURL .= "/";
	return $serverURL;
}

function ClosestWord($input,$possibles) {
	// returns closest or matching word to $input, as selected from the $possibles array
	// no shortest distance found, yet
	$shortest = -1;
	// loop through words to find the closest
	foreach ($possibles as $word) {
		// calculate the distance between the input word,
		// and the current word
		$lev = levenshtein($input, $word);
		// check for an exact match
		if ($lev == 0) {
			// closest word is this one (exact match)
			$closest = $word;
			$shortest = 0;
			// break out of the loop; we've found an exact match
			break;
		}
		// if this distance is less than the next found shortest
		// distance, OR if a next shortest word has not yet been found
		if ($lev <= $shortest || $shortest < 0) {
			// set the closest match, and shortest distance
			$closest  = $word;
			$shortest = $lev;
		}
	}
	//if ($shortest == 0)
	return ($closest);
}
