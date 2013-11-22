<?
	/* Category displays */

function ErrorDisplay($error) {
	echo "<div class='SiteError'>$error</div>";
}

function ListCategoryCarousel ($categoryList) {
	// $url, $category, description, is_highlighted
	?>
		<div class='CategoryCarousel'>
			<ul>
			<?
				foreach ($categoryList as $key => $blah) {
					?>
						<li><img src="/i/category/<?= $categoryList[$key]['carousel_id']; ?>" width="400" height="266" title="<?= $categoryList[$key]['category']; ?> - <?= $categoryList[$key]['description']; ?>" alt="<?= $categoryList[$key]['category']; ?>"></li>
					<?
				}
			?>
			</ul>
		</div>
	<?
}

function ListAllCategories($categoryList) {
	// $url, $category, description, is_highlighted
	?>
		<div class='CategoryList'>
			<ul>
			<?
				foreach ($categoryList as $key => $blah) {
					?>
						<li class="CategoryItem"><a href="/category/<?= $categoryList[$key]['url']; ?>"><img src="/i/category/<?= $categoryList[$key]['image_id']; ?>" width="728" height="90" title="<?= $categoryList[$key]['category']; ?> - <?= $categoryList[$key]['description']; ?>" alt="<?= $categoryList[$key]['category']; ?>"></a></li>
					<?
				}
			?>
			</ul>
		</div>
	<?
}

function ListArtistsForCategory($category,$artists)	{
	// aid, name, url, slug, is_highlighted
	echo "<pre>";
	print_r ($artists);
	echo "</pre><br>";
}

function ListArtistCarousel($category,$artists) {
	echo "<pre>";
	print_r ($artists);
	echo "</pre><br>";
}
