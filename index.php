<?
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $starttime = $mtime;
	require_once("shared.php");
	Init();
	//RecordHit();
	echo "You wanted to look at: ";
	echo $_REQUEST['page'];
?>

<br>

<?
	$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $endtime = $mtime; $totaltime = ($endtime - $starttime);
	echo "<!-- This page was created in ".$totaltime." seconds -->"; 
?>
