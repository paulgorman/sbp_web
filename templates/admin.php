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
	echo "<nav class='adminNavHolder'>";
	foreach ($adminfunctions as $nicename => $url) { 
		echo "<div class='adminNavItem'><a href='/admin/$url'>"; 
		if ($curfunction == $url) { 
			echo "<B>" . strtoupper($nicename) . "</B></a>";
		} else { 
			echo strtoupper($nicename) . "</a>"; 
		}
		echo '</div>';
	}
	echo "</nav>";
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
		<form method="POST" action="/admin/categories_list" enctype="multipart/form-data">
			<input type="hidden" name="function" value="add_category">
			<div class="AdminCategoryListingAddContainer">
				<div class="AdminCategoryListingAddHeader">ADD NEW CATEGORY</div>
				<div class="AdminCategoryListingAddItem">URL</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="form_url" size="15" style="text-transform: lowercase"></div>
				<div class="AdminCategoryListingAddItem">Category Name</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="form_category" size="20"></div>
				<div class="AdminCategoryListingAddItem">Description</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="form_description" size="40"></div>
				<div class="clear"></div>
				<div class="AdminCategoryListingAddItem">Category Logo</div>
				<div class="AdminCategoryListingAddValue"><input name="filesToUpload[]" class="filesToUpload" size="40" id="1" type="file" multiple=""></div>
				<div class="AdminCategoryListingAddSubmit"><input type="submit" value="Add Category"></div>
				<div class="clear"></div>
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

function AdminEditCategory($dataarray) {
	// cid, category, url, description, image_filename, image_id, last_updated
	?>
		<form method="POST" action="/admin/categories_list" enctype="multipart/form-data">
			<input method="hidden" name="function" value="save_category" style="display:none">
			<input method="hidden" name="form_cid" value="<?= $dataarray['cid'] ?>" style="display:none">
			<div class="AdminCategoryListContainer">
				<div class="AdminCategoryListingEditHeader">
					Edit Category Properties for "<?= $dataarray['category'] ?>" <font size="-2">Last edited <?= nicetime($dataarray['last_updated']) ?> (<?= $dataarray['last_updated'] ?>)</font>
				</div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">Short Name:</div>
				<div class="AdminCategoryListingAddValue">
					<input type="text" name="form_category" size="20" value="<?= $dataarray['category']?>">
				</div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">Nice Description:</div>
				<div class="AdminCategoryListingAddValue">
					<input type="text" name="form_description" size="50" value="<?= $dataarray['description'] ?>">
				</div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">URL:</div>
				<div class="AdminCategoryListingAddValue">
					<input type="text" name="form_url" size="20" value="<?= $dataarray['url'] ?>">
				</div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">Stored Image:</div>
				<div class="AdminCategoryListingAddValue"><?= $dataarray['image_id'] ?></div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">Image Name:</div>
				<div class="AdminCategoryListingAddValue"><?= $dataarray['image_filename'] ?></div>
				<div class="clear"></div>
				<div class="AdminCategoryListingAddItem"></div>
				<div class="clear"></div>
			</div> <!-- AdminCategoryListContainer -->
		</form>
	<?
}