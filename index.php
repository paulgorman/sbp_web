<?
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $starttime = $mtime;
	require_once("shared.php");
	Init();
	switch($_REQUEST['page']) {
		case "admin":
			if (isAdmin()) {
				ShowAdminPage();
			} elseif ($_REQUEST['url'] == "login") {
				ConfirmLogin();
			} else {
				AskForAdmin();
			}
			ScriptTime($starttime);
			break;
		case "categories":
			RecordHit();
			CategoriesList();
			ScriptTime($starttime);
			break;
		case "artist":
		case "artists":
		case "event":
		case "events":
		case "images":
			RecordHit();
			ArtistPage();
			Scripttime($starttime);
			break;
		case "production":
			RecordHit();
			ProductionPage();
			break;
		case "special":
			RecordHit();
			EventPage();
			break;
		case "decor":
			RecordHit();
			DecorPage();
			break;
		case "wedding":
			RecordHit();
			WeddingPage();
			break;
		case "about":
			RecordHit();
			AboutPage();
			break;
		case "uploadprogress":
			UploadProgress();
			break;
		case "videoplay":
			RecordHit();
			break;
		case "liked":
			RecordHit();
			break;
		default:
			RecordHit();
			HomePage();
			ScriptTime($starttime);
	}
?>
