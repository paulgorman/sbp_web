<?
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $starttime = $mtime;
	require_once("shared.php");
	Init();
	RecordHit();
	switch($_REQUEST['page']) {
		case "admin":
			ShowAdminPage();
			break;
		default:
			DebugShow();
	}
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $endtime = $mtime; $totaltime = ($endtime - $starttime);
	echo "<!-- This page was created in ".$totaltime." seconds -->"; 
?>
