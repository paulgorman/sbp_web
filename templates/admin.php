<?
/* admin html templates */

function AdminHead($curfunction,$adminfunctions) {
	$tempadminfunctionsflipped = array_flip ($adminfunctions);
	?>
		<html>
			<head>
				<title>SBP Admin - <?= $tempadminfunctionsflipped[$curfunction]; ?></title>
				<script type="text/javascript" src="/templates/tableselect.js"></script>
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
	?>
		<div class="AdminCategoryRemoveIcon">x</div>
		<div class="AdminCategoryEditIcon">x</div>
		<div class="AdminCategoryListContainer">
			<div class="AdminCategoryListHeader">
				<div class="AdminCategoryListHeaderItem">URL</div>
				<div class="AdminCategoryListHeaderItem">Category</div>
				<div class="AdminCategoryListHeaderItem">Description</div>
			</div>
			<? foreach ($categorieslist as $catkey => $catvalues) { ?>
				<form method="POST" action="/admin/categories_list" name="edit<?= $catvalues['url']; ?>">
					<div class="AdminCategoryListItem" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;" style="cursor: pointer;"><?=$catvalues['url'] ?></div>
					<div class="AdminCategoryListItem" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;" style="cursor: pointer;"><?=$catvalues['category'] ?></div>
					<div class="AdminCategoryListItem" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;" style="cursor: pointer;"><?=$catvalues['description'] ?></div>
					<div class="AdminCategoryListItem" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;">
						<input type="hidden" name="categoryurl" value="<?= $catvalues['url']; ?>">
						<input type="hidden" name="function" value="edit_category">
						<a href="javascript:;" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;" class="AdminCategoryEditIcon" title="Edit '<?= $catvalues['category'] ?>' Category"></a>
					</div> <!-- class="AdminCategoryListItem" -->
				</form>
				<form method="POST" action="/admin/categories_list" name="del<?= $catvalues['url']; ?>">
					<div class="AdminCategoryListItem">
						<a href="javascript:;" onclick="document.forms['del<?= $catvalues['url']; ?>'].submit(); return false;" class="AdminCategoryRemoveIcon" title="Remove '<?= $catvalues['category']; ?>' Category"></a>
					</div>
				</form>
			<? } ?>
		</div> <!-- AdminCategoryListContainer -->

				<form method="POST" action="/admin/categories_list">
				<input type="hidden" name="function" value="add_category">
				<table class="fixHeader">
					<tr><td colspan="2">Add New Category</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td class="shortColumnHead">URL: </td><td class="shortColumnData"><input type="text" name="form_url"></td></tr>
					<tr><td colspan="2" class="mainColumnHead">Category Name: </td></tr>
					<tr><td colspan="2" class="mainColumnData"><input type="text" name="form_category"></td></tr>
					<tr><td colspan="2" class="extraColumnHead">Category Description: </td></tr>
					<tr><td colspan="2" class="extraColumnData"><input type="text" name="form_description"></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td colspan="2"><input type="submit" value="Add New Category"></td></tr>
				</table>
			</form>
	<?
}
