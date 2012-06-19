<?
/****************************************
**  Steve Beyer Productions
**  Website and Talent Database
**
**  Concept: Steve Beyer
**  Code: Presence
**
**  Last Edit: 20120618
** hey, in php.ini, do:  disable_functions = phpinfo
** and in http.conf do: <VirtualHost domain><Location /urldir/>\n php_value mysql.default_user example\n php_value mysql.default_password secretpassword\n </Location></VirtualHost>
****************************************/

function Init() {
  global $conn;
	error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
  $host  = "localhost";
  $db    = "sbpweb";
	require_once("db.php");
  //$conn = mysql_pconnect($host, $user, $pass) or die(mysql_error());
  //mysql_select_db($db);
}

function RecordHit() {
	global $conn;
	$query = sprintf("INSERT INTO sitehits (hit_datetime, hit_ip, hit_url, user_agent, referrer) values ('%s','%s','%s','%s','%s');",
		mysql_real_escape_string(DatePHPtoSQL(time())),
		mysql_real_escape_string($_SERVER['REMOTE_ADDR']),
		mysql_real_escape_string($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']),
		mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']),
		mysql_real_escape_string($_SERVER['HTTP_REFERRER'])
	);
	$result = mysql_query($query,$conn);
}

function DateSQLtoPHP($mysqldate) {
	// mysql's datetime to PHP seconds-since-epoch date format
	return (strtotime($mysqldate));
}

function DatePHPtoSQL($phpdate) {
	// php's seconds-since-epoch timestamp to MySQL
	return (date('Y-m-d H:i:s', $phpdate));
}


