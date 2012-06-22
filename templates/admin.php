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

function AdminShowCategories ($categorieslist) {
	echo '<script type="text/javascript" src="/templates/tableselect.js"></script>';
	?>
		<table><tr><td>
			<table class="fixHeader">
				<tr>
					<td class="shortColumnHead">URL</td>
					<td class="mainColumnHead">Category</td>
					<td class="extraColumnHead">Description</td>
				</tr>
			</table>
			<div class="scrollableContentOuter">
				<table id="available_contacts" class="scrollableContentInner" onkeydown="changeHighLightTR(event, this);">
					<? foreach ($categorieslist as $catkey => $catvalues) { ?>
						<tr id="<?=$catvalues['cid']; ?>" onclick="highLightTR(event, this, '#4A9481', '#FFFFFF', '#316AC5', '#FFFFFF');">
							<td class="shortColumnData"><?=$catvalues['url'] ?></td>
							<td class="mainColumnData"><?=$catvalues['category'] ?></td>
							<td class="extraColumnData"><?=$catvalues['description'] ?></td>
						</tr>
					<? } ?>
				</table>
			</div>
		</td>
		<td align="middle">
			<br><input type="button" onclick="transfer('selected_contacts','available_contacts');" value="Remove">
			<br><input type="button" onclick="_submitEdit(1,'del_category');" value="Save">
		</td>
		<td valign="top">
			<form method="POST" action="/admin/categories_list">
				<input type="hidden" name="function" value="add_category">
				<table class="fixHeader">
					<tr><td colspan="2">Add New Category</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td class="shortColumnHead">URL: </td><td class="shortColumnData"><input type="text" name="url"></td></tr>
					<tr><td colspan="2" class="mainColumnHead">Category Name: </td></tr>
					<tr><td colspan="2" class="mainColumnData"><input type="text" name="category"></td></tr>
					<tr><td colspan="2" class="extraColumnHead">Category Description: </td></tr>
					<tr><td colspan="2" class="extraColumnData"><input type="text" name="description"></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td colspan="2"><input type="submit" value="Add New Category"></td></tr>
				</table>
			</form>
		</td></tr></table>
	<?
}
