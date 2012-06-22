<?
/* admin html templates */

function AdminHead($curfunction,$adminfunctions) {
	$tempadminfunctionsflipped = array_flip ($adminfunctions);
	?>
		<html>
			<head>
				<title>SBP Admin - <?= $tempadminfunctionsflipped[$curfunction]; ?></title>
	<?			
	readfile("templates/admin.css");
	?>
			</head>
			<body>
				<div class='adminTitle'>SBP Website Administration - <?= $tempadminfunctionsflipped[$curfunction]; ?></div>
	<?
}

function AdminNav($curfunction,$adminfunctions) {
	echo "<div class='adminNavHolder'>";
	foreach ($adminfunctions as $nicename => $url) { 
		echo "<div class='adminNavItem'> [<a href='/admin/$url'>"; 
		if ($curfunction == $url) { 
			echo "<B>$nicename</B></a>]";
		} else { 
			echo "$nicename</a>]"; 
		}
		echo '</div>';
	}
	echo "</div>";
}
