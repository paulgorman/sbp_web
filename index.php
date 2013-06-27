<?
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $starttime = $mtime;
	require_once("shared.php");
	Init();
	switch($_REQUEST['page']) {
		case "admin":
			ShowAdminPage();
			break;
		default:
			RecordHit();
			DebugShow();
	}
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $endtime = $mtime; $totaltime = ($endtime - $starttime) * 1000;
	$totaltime = sprintf("%.2f", $totaltime);
	echo "<!-- This page was created in ".$totaltime." milliseconds -->"; 
?>
