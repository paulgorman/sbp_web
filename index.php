<?
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $starttime = $mtime;
	require_once("shared.php");
	Init();
	switch($_REQUEST['page']) {
		case "admin":
			ShowAdminPage();
			ScriptTime($starttime);
			break;
		case "categories":
			RecordHit();
			CategoriesList();
			ScriptTime($starttime);
			break;
		case "artist":
			RecordHit();
			ArtistPage();
			Scripttime($starttime);
			break;
		case "production":
			RecordHit();
			ProductionPage();
			break;
		case "event":
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
		default:
			RecordHit();
			HomePage();
			ScriptTime($starttime);
	}
?>
