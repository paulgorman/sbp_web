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
				foreach ($categoryList as $key => $value) {
					?>
						<li>highlighted: <?= $categoryList[$key]['category']; ?></li>
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
				foreach ($categoryList as $count => $blah) {
					?>
						<li class="CategoryItem"><a href="/category/<?= $categoryList[$count]['url']; ?>"><img src="/i/category/<?= $categoryList[$count]['image_id']; ?>" border="0" title="<?= $categoryList[$count]['category']; ?>" alt="<?= $categoryList[$count]['description']; ?>"></a></li>
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
	echo $category;
	echo "<pre>";
	print_r ($artists);
	echo "</pre><br>";
}
