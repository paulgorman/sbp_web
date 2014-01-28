<?
function htmlArtistPageTop($artistinfo) {
	$artist = $artistinfo[key($artistinfo)];
	$hlighted_filename = $artist['media']['filename'][key($artist['media']['mid'])];
	$hlighted_width = $artist['media']['thumbwidth'][key($artist['media']['mid'])];
	$hlighted_height = $artist['media']['thumbheight'][key($artist['media']['mid'])];
	$hlighted_alt = $artist['name'];
	$artist_bio = Parsedown::instance()->parse(htmlspecialchars_decode($artist['bio']));
	?>
		<!-- <?= print_r ($artist); ?> -->
		<div class="artistTop">
			<div class="col6 fl artistHeadImageContainer"><!-- Artist Highlighted Photo -->
				<img class="artistHeadImage" src="/i/artist/<?= $hlighted_filename; ?>" width="<?= $hlighted_width; ?>" height="<?= $hlighted_height; ?>" alt="<?= $hlighted_alt; ?>" title="<?= $hlighted_alt; ?>">
			</div>
			<div class="col6 fr artistTitle">
				<h1><?= $artist['name']; ?></h1>
				<h3><?= $artist['slug']; ?></h3>
				<div class="artistBio">
					<?= $artist_bio; ?>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
			<div class="artistVideo">
				<? PrepareVideoPlayer($artist); ?>
			</div>
	<?
}


function htmlArtistPageBottom($artistinfo) {
	echo "<div style='height: 10em; margin-top: 1em;'>lower body</div>";
}
