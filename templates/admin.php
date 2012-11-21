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
		if ($curfunction == $url) { 
			?>
					<div class='adminNavItemSelected'>
						<a href='/admin/<?= $url ?>'><B><?= strtoupper($nicename) ?></B></a>
					</div>
			<?
		} else { 
			?>
					<div class='adminNavItem'>
						<a href='/admin/<?= $url ?>'><?= strtoupper($nicename) ?></a>
					</div>
			<?
		}
	}
	echo "</nav>";
}

function AdminArtistsButtonBar() {
	?>
		<div  class="adminNavSubHolder">
			<div class="adminNavSubItem"><a href='/admin/artists/add_new'>Add New Artist</a></div>
			<div class="adminNavSubItem"><a href='/admin/artists/list_new'>List Newest Artists</a></div>
			<div class="adminNavSubItem"><a href='/admin/artists/list_feat'>List Featured Artists</a></div>
			<div class="adminNavSubItem"><a href='/admin/artists/list_secret'>List Hidden Artists</a></div>
			<div class="adminNavSubItem"><a href='/admin/artists/list_all'>List All Artists</a></div>
			<div class="adminNavSubItem"><form method="POST" action="/admin/artists/" id="search"><input type="hidden" name="function" value="search"><input type="text" name="q" placeholder="Search..."></form></div>
		</div>
	<?
}

function AdminArtistListPage($artists,$page) {

}

function AdminShowLocations($locations) {
	?>
		<div class="AdminCategoryListContainer">
			<div class="AdminCategoryListHeader">
				<div class="AdminCategoryListItemURL">Locations</div>
			</div>
			<div class="clear"></div>
			<? foreach ($locations as $key => $values) { ?>
				<div class="AdminCategoryListRow">
					<form method="POST" action="/admin/locations_list" name="edit<?= $values['lid']; ?>">
						<div class="AdminCategoryListItemCategory" onclick="document.forms['edit<?= $values['lid']; ?>'].submit(); return false;"><?=$values['city'] ?></div>
						<div class="AdminCategoryListItemURL" onclick="document.forms['edit<?= $values['lid']; ?>'].submit(); return false;"><?=$values['state'] ?></div>
						<div class="AdminCategoryListItemDescription" onclick="document.forms['edit<?= $values['lid']; ?>'].submit(); return false;">&nbsp;</div>
						<div class="AdminCategoryListItemIcon" onclick="document.forms['edit<?= $values['lid']; ?>'].submit(); return false;">
							<input type="hidden" name="lid" value="<?= $values['lid']; ?>">
							<input type="hidden" name="function" value="edit_location">
							<a href="javascript:;" onclick="document.forms['edit<?= $values['lid']; ?>'].submit(); return false;" class="AdminCategoryEditIcon" title="Rename <?= $values['city'] ?>, <?= $values['state'] ?>."></a>
						</div> <!-- class="AdminCategoryListItem" -->
					</form>
					<div class="AdminCategoryListItemIcon">
						<form method="POST" action="/admin/locations_list" name="del<?= $values['lid']; ?>">
							<input type="hidden" name="lid" value="<?= $values['lid']; ?>">
							<input type="hidden" name="function" value="del_location">
							<a href="javascript:;" onclick="document.forms['del<?= $values['lid']; ?>'].submit(); return false;" class="AdminCategoryRemoveIcon" title="Remove <?= $values['city']; ?>, <?= $values['state'] ?>."></a>
						</form>
					</div>
					<div class="AdminCategoryListItemIcon">
						<form method="POST" action="/admin/locations_list" name="search<?= $values['lid']; ?>">
							<input type="hidden" name="lid" value="<?= $values['lid']; ?>">
							<input type="hidden" name="function" value="search_location">
							<a href="javascript:;" onclick="document.forms['search<?= $values['lid']; ?>'].submit(); return false;" class="AdminCategorySearchIcon" title="Show all artists in '<?= $values['city'] ?>, <?= $values['state'] ?>."></a>
						</form>
					</div>
				</div>  <!-- class="AdminCategoryListRow" -->
			<? } ?>
		</div> <!-- AdminCategoryListContainer -->
		<div class="clear"></div>
		<form method="POST" action="/admin/locations_list" enctype="multipart/form-data">
			<input type="hidden" name="function" value="add_location">
			<div class="AdminCategoryListingAddContainer">
				<div class="AdminCategoryListingAddHeader">ADD NEW LOCATION</div>
				<div class="AdminCategoryListingAddItem">City</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="city" size="30"></div>
				<div class="AdminCategoryListingAddItem">State</div>
				<div class="AdminCategoryListingAddValue"><select name="state"><option value="">Select a State</option><?= OptionsDropDown("none"); ?></select></div>
				<div class="AdminCategoryListingAddSubmit"><input type="submit" value="Add Location"></div>
				<div class="clear"></div>
			</div> <!-- class="AdminCategoryListingAddContainer" -->
		</form>
	<?
}

function AdminShowStyles($styles,$quantity) {
	?>
		<div class="AdminCategoryListContainer">
			<div class="AdminCategoryListHeader">
				<div class="AdminCategoryListItemURL">Styles (<?= $quantity ?>)</div>
			</div>
			<div class="clear"></div>
			<? foreach ($styles as $key => $values) { ?>
				<div class="AdminCategoryListRow">
					<form method="POST" action="/admin/styles_list" name="edit<?= $values['sid']; ?>">
						<div class="AdminCategoryListItemDescription" onclick="document.forms['edit<?= $values['sid']; ?>'].submit(); return false;"><?=$values['name'] ?></div>
						<div class="AdminCategoryListItemURL" onclick="document.forms['edit<?= $values['sid']; ?>'].submit(); return false;">&nbsp;</div>
						<div class="AdminCategoryListItemCategory" onclick="document.forms['edit<?= $values['sid']; ?>'].submit(); return false;">&nbsp;</div>
						<div class="AdminCategoryListItemIcon" onclick="document.forms['edit<?= $values['sid']; ?>'].submit(); return false;">
							<input type="hidden" name="sid" value="<?= $values['sid']; ?>">
							<input type="hidden" name="function" value="edit_style">
							<a href="javascript:;" onclick="document.forms['edit<?= $values['sid']; ?>'].submit(); return false;" class="AdminCategoryEditIcon" title="Edit '<?= $values['name'] ?>' Style"></a>
						</div> <!-- class="AdminCategoryListItem" -->
					</form>
					<div class="AdminCategoryListItemIcon">
						<form method="POST" action="/admin/styles_list" name="del<?= $values['sid']; ?>">
							<input type="hidden" name="sid" value="<?= $values['sid']; ?>">
							<input type="hidden" name="function" value="del_style">
							<a href="javascript:;" onclick="document.forms['del<?= $values['sid']; ?>'].submit(); return false;" class="AdminCategoryRemoveIcon" title="Remove '<?= $values['name']; ?>' Style"></a>
						</form>
					</div>
					<div class="AdminCategoryListItemIcon">
						<form method="POST" action="/admin/styles_list" name="search<?= $values['sid']; ?>">
							<input type="hidden" name="sid" value="<?= $values['sid']; ?>">
							<input type="hidden" name="function" value="search_style">
							<a href="javascript:;" onclick="document.forms['search<?= $values['sid']; ?>'].submit(); return false;" class="AdminCategorySearchIcon" title="Show all artists in the '<?= $values['name']; ?>' style"></a>
						</form>
					</div>
				</div>  <!-- class="AdminCategoryListRow" -->
			<? } ?>
		</div> <!-- AdminCategoryListContainer -->
		<div class="clear"></div>
		<form method="POST" action="/admin/styles_list" enctype="multipart/form-data">
			<input type="hidden" name="function" value="add_style">
			<div class="AdminCategoryListingAddContainer">
				<div class="AdminCategoryListingAddHeader">ADD NEW STYLE</div>
				<div class="AdminCategoryListingAddItem">Name</div>
				<div class="AdminCategoryListingAddValue"><input type="text" name="name" size="40"></div>
				<div class="AdminCategoryListingAddSubmit"><input type="submit" value="Add Style"></div>
				<div class="clear"></div>
			</div> <!-- class="AdminCategoryListingAddContainer" -->
		</form>
	<?
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
						<?
							if ($catvalues['published']) {
								echo "<div class='AdminCategoryPublishedIcon' title='Public Listed Category'></div>";
							} else {
								echo "<div class='AdminCategoryHiddenIcon' title='Not Listed'></div>";
							}
						?>
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
    		<div class="AdminCategoryListingAddItem">Public</div>
    		<div class="AdminCategoryListingCheckBox">
					<input type="checkbox" name="published" id="published" class="regular-checkbox big-checkbox" CHECKED /><label title="Publicly Displayed in Categories Listing" for="published"></label>
				</div>
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
					<input type="text" name="form_url" size="20" value="<?= $dataarray['url'] ?>" style="text-transform: lowercase">
				</div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">Stored Image:</div>
				<div class="AdminCategoryListingShowImage"><img src="/images/category/<?= $dataarray['image_id'] ?>"></div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">Image Name:</div>
				<div class="AdminCategoryListingAddValue"><?= $dataarray['image_filename'] ?></div>
				<div class="AdminCategoryListingAddValue"><input name="filesToUpload[]" class="filesToUpload" size="40" id="1" type="file" multiple=""></div>
				<div class="clear"></div>
				<div class="AdminCategoryListingAddItem"></div>
				<div class="clear"></div>

    		<div class="AdminCategoryListingEditItem">Public</div>
    		<div class="AdminCategoryListingCheckBox">
					<input type="checkbox" id="published" name="published" class="regular-checkbox big-checkbox" <?= ($dataarray['published']?'CHECKED ':'') ?>/><label title="Publicly Displayed in Categories Listing" for="published"></label>
				</div>

				<div class="AdminCategoryListingAddSubmit">
					<input type="submit" value="Update Category">
					<input type="button" name="Cancel" value="Cancel" onclick="window.location='/admin/categories_list'">
				</div>
				<div class="clear"></div>
			</div> <!-- AdminCategoryListContainer -->
		</form>
	<?
}

function AdminEditLocation($dataarray) {
	?>
		<form method="POST" action="/admin/locations_list" enctype="multipart/form-data">
			<input method="hidden" name="function" value="save_location" style="display:none">
			<input method="hidden" name="lid" value="<?= $dataarray['lid'] ?>" style="display:none">
			<div class="AdminCategoryListContainer">
				<div class="AdminCategoryListingEditHeader">
					Rename Location "<?= $dataarray['city'] ?>, <?= StateCodeToName($dataarray['state']) ?>"
				</div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">City:</div>
				<div class="AdminCategoryListingAddValue">
					<input type="text" name="city" size="40" value="<?= $dataarray['city'] ?>">
				</div>
				<div class="AdminCategoryListingAddValue"><select name="state"><?= OptionsDropDown($dataarray['state']) ?></select></div>
				<div class="AdminCategoryListingAddSubmit">
					<input type="submit" value="Update Location">
					<input type="button" name="Cancel" value="Cancel" onclick="window.location='/admin/locations_list'">
				</div>
				<div class="clear"></div>
			</div> <!-- AdminCategoryListContainer -->
		</form>
	<?
}

function AdminEditStyle($dataarray) {
	?>
		<form method="POST" action="/admin/styles_list" enctype="multipart/form-data">
			<input method="hidden" name="function" value="save_style" style="display:none">
			<input method="hidden" name="sid" value="<?= $dataarray['sid'] ?>" style="display:none">
			<div class="AdminCategoryListContainer">
				<div class="AdminCategoryListingEditHeader">
					Edit Style Name "<?= $dataarray['name'] ?>"
				</div>
				<div class="clear"></div>
				<div class="AdminCategoryListingEditItem">Style Name:</div>
				<div class="AdminCategoryListingAddValue">
					<input type="text" name="name" size="40" value="<?= $dataarray['name'] ?>">
				</div>
				<div class="AdminCategoryListingAddSubmit">
					<input type="submit" value="Update Style">
					<input type="button" name="Cancel" value="Cancel" onclick="window.location='/admin/styles_list'">
				</div>
				<div class="clear"></div>
			</div> <!-- AdminCategoryListContainer -->
		</form>
	<?
}
