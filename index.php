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
			CategoriesList();
			break;
		case "uploadprogress":
			UploadProgress();
			break;
		default:
			RecordHit();
			DebugShow();
			ScriptTime($starttime);
	}
?>
