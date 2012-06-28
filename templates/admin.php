<?
/* admin html templates */

function AdminHead($curfunction,$adminfunctions) {
	$tempadminfunctionsflipped = array_flip ($adminfunctions);
	?><!DOCTYPE html>
		<html lang="en">
			<head>
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta charset="utf-8">
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
		echo "<div class='adminNavItem'> <a href='/admin/$url'>"; 
		if ($curfunction == $url) { 
			echo "<B>" . strtoupper($nicename) . "</B></a>";
		} else { 
			echo strtoupper($nicename) . "</a>"; 
		}
		echo '</div>';
	}
	echo "</div>";
}

function AdminShowCategories ($categorieslist) {
	?>
		<div class="AdminCategoryListContainer">
			<div class="AdminCategoryListHeader">
				<div class="AdminCategoryListItemURL">URL</div>
				<div class="AdminCategoryListItemCategory">Category Name</div>
				<div class="AdminCategoryListItemDescription">Description</div>
			</div>
			<div class="clear"></div>
			<? foreach ($categorieslist as $catkey => $catvalues) { ?>
				<div class="AdminCategoryListRow">
					<form method="POST" action="/admin/categories_list" name="edit<?= $catvalues['url']; ?>">
						<div class="AdminCategoryListItemURL" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;"><?=$catvalues['url'] ?></div>
						<div class="AdminCategoryListItemCategory" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;"><?=$catvalues['category'] ?></div>
						<div class="AdminCategoryListItemDescription" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;"><?=$catvalues['description'] ?></div>
						<div class="AdminCategoryListItemIcon" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;">
							<input type="hidden" name="categoryurl" value="<?= $catvalues['url']; ?>">
							<input type="hidden" name="function" value="edit_category">
							<a href="javascript:;" onclick="document.forms['edit<?= $catvalues['url']; ?>'].submit(); return false;" class="AdminCategoryEditIcon" title="Edit '<?= $catvalues['category'] ?>' Category"></a>
						</div> <!-- class="AdminCategoryListItem" -->
					</form>
					<div class="AdminCategoryListItemIcon">
						<form method="POST" action="/admin/categories_list" name="del<?= $catvalues['url']; ?>">
							<input type="hidden" name="categoryurl" value="<?= $catvalues['url']; ?>">
							<input type="hidden" name="function" value="del_category">
							<a href="javascript:;" onclick="document.forms['del<?= $catvalues['url']; ?>'].submit(); return false;" class="AdminCategoryRemoveIcon" title="Remove '<?= $catvalues['category']; ?>' Category"></a>
						</form>
					</div>
					<div class="AdminCategoryListItemIcon">
						<form method="POST" action="/admin/categories_list" name="search<?= $catvalues['url']; ?>">
							<input type="hidden" name="categoryurl" value="<?= $catvalues['url']; ?>">
							<input type="hidden" name="function" value="search_category">
							<a href="javascript:;" onclick="document.forms['search<?= $catvalues['url']; ?>'].submit(); return false;" class="AdminCategorySearchIcon" title="Show all artists in the '<?= $catvalues['category']; ?>' Category"></a>
						</form>
					</div>
				</div>  <!-- class="AdminCategoryListRow" -->
			<? } ?>
		</div> <!-- AdminCategoryListContainer -->
		<div class="clear"></div>
		<form method="POST" action="/admin/categories_list">
			<input type="hidden" name="function" value="add_category">
			<div class="AdminCategoryListingAddContainer">
				<div class="AdminCategoryListingAddHeader">Add New Category</div>
				<div class="AdminCategoryListingAddItem">URL</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="form_url" length="15" style="text-transform: lowercase"></div>
				<div class="AdminCategoryListingAddItem">Category Name</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="form_category" length="20"></div>
				<div class="AdminCategoryListingAddItem">Description</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="form_description" length="30"></div>
				<div class="AdminCategoryListingAddSubmit"><input type="submit" value="Add Category"></div>
			</div> <!-- class="AdminCategoryListingAddContainer" -->
		</form>
	<?
}

function AdminShowDeleteConfirmation($targetcategoryurl,$url,$nextfunction) {
	?>
		<div class="AdminWarning">
			<form method="POST" action="/admin/<?= $url ?>">
				<input type="hidden" name="targetcategoryurl" value="<?= $targetcategoryurl ?>">
				<input type="hidden" name="function" value="<?= $nextfunction ?>">
				Are you sure you want to delete <B><?= $targetcategoryurl ?></B>?<br>
				Proceding will have this feature removed from any artists possessing the feature.<br>
				<input type="submit" value="Delete"><input type="button" name="Cancel" value="Cancel" onclick="window.location='/admin/<?= $url ?>'">
			</form>
		</div>
	<?
}
