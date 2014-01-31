<?
function htmlArtistPageTop($artistinfo) {
	$artist = $artistinfo[key($artistinfo)];
	$hlighted_filename = $artist['media']['filename'][key($artist['media']['mid'])];
	$hlighted_width = $artist['media']['thumbwidth'][key($artist['media']['mid'])];
	$hlighted_height = $artist['media']['thumbheight'][key($artist['media']['mid'])];
	$hlighted_alt = $artist['name'];
	$artist_bio = Parsedown::instance()->parse(htmlspecialchars_decode($artist['bio']));
	?>
		<div class="artistTop">
			<div class="col6 fl artistHeadImageContainer"><!-- Artist Highlighted Photo -->
			<?
				if (count($artist['media']['mid']) === 1) {
					// If there's only one photo for the artist, just link this here image to high-res, since there's no Gridfolio
					?>
						<a href="/i/artist/original-<?= $hlighted_filename; ?>" title="Click for High-Resolution Image"><img class="artistHeadImage" src="/i/artist/<?= $hlighted_filename; ?>" width="<?= $hlighted_width; ?>" height="<?= $hlighted_height; ?>" alt="<?= $hlighted_alt; ?>" title="<?= $hlighted_alt; ?>"></a>
					<?
				} else {
					?>
						<img class="artistHeadImage" src="/i/artist/<?= $hlighted_filename; ?>" width="<?= $hlighted_width; ?>" height="<?= $hlighted_height; ?>" alt="<?= $hlighted_alt; ?>" title="<?= $hlighted_alt; ?>">
					<?
				}
			?>
			</div>
			<div class="col6 fr artistTitle">
				<h1><?= $artist['name']; ?></h1>
				<h3><?= $artist['slug']; ?></h3>
				<div class="artistBio">
					<?= $artist_bio; ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="artistVideo">
			<table border="0" width="100%">
			 <tr align="center">
			  <td align="center">
				<? PrepareVideoPlayer($artist); ?>
				</td>
			</tr>
			</table>
			</div>
			<div class="clearfix"></div>
		</div>
	<?
}


function htmlArtistPageBottom($artistinfo) {
	$artist = $artistinfo[key($artistinfo)];
	?>
		<div class="artistTop">
			<div class="artistGrid box">
				<div id="goGrid" style="width:100%;"></div>
			</div>
		</div>
		<!-- grid data list -->
		<ul id="gridPlaylist" style="display: none;">
			<!-- skin -->
			<ul data-skin="">
				<li data-preloader-path="/templates/skin_minimal_dark_round/rotite-30-29.png"></li>
				<li data-show-more-thumbnails-button-normal-path="/templates/skin_minimal_dark_round/showMoreThumbsNormalState.png"></li>
				<li data-show-more-thumbnails-button-selectsed-path="/templates/skin_minimal_dark_round/showMoreThumbsSelectedState.png"></li>
				<li data-image-icon-path="/templates/skin_minimal_dark_round/photoIcon.png"></li>
				<li data-video-icon-path="/templates/skin_minimal_dark_round/videoIcon.png"></li>
				<li data-link-icon-path="/templates/skin_minimal_dark_round/linkIcon.png"></li>
				<li data-iframe-icon-path="/templates/skin_minimal_dark_round/iframeIcon.png"></li>
				<li data-hand-move-icon-path="/templates/skin_minimal_dark_round/handnmove.cur"></li>
				<li data-hand-drag-icon-path="/templates/skin_minimal_dark_round/handgrab.cur"></li>
				<li data-combobox-down-arrow-icon-normal-path="/templates/skin_minimal_dark_round/combobox-down-arrow.png"></li>
				<li data-combobox-down-arrow-icon-selected-path="/templates/skin_minimal_dark_round/combobox-down-arrow-rollover.png"></li>
				<li data-lightbox-slideshow-preloader-path="/templates/skin_minimal_dark_round/slideShowPreloader.png"></li>
				<li data-lightbox-close-button-normal-path="/templates/skin_minimal_dark_round/galleryCloseButtonNormalState.png"></li>
				<li data-lightbox-close-button-selected-path="/templates/skin_minimal_dark_round/galleryCloseButtonSelectedState.png"></li>
				<li data-lightbox-next-button-normal-path="/templates/skin_minimal_dark_round/nextIconNormalState.png"></li>
				<li data-lightbox-next-button-selected-path="/templates/skin_minimal_dark_round/nextIconSelectedState.png"></li>
				<li data-lightbox-prev-button-normal-path="/templates/skin_minimal_dark_round/prevIconNormalState.png"></li>
				<li data-lightbox-prev-button-selected-path="/templates/skin_minimal_dark_round/prevIconSelectedState.png"></li>
				<li data-lightbox-play-button-normal-path="/templates/skin_minimal_dark_round/playButtonNormalState.png"></li>
				<li data-lightbox-play-button-selected-path="/templates/skin_minimal_dark_round/playButtonSelectedState.png"></li>
				<li data-lightbox-pause-button-normal-path="/templates/skin_minimal_dark_round/pauseButtonNormalState.png"></li>
				<li data-lightbox-pause-button-selected-path="/templates/skin_minimal_dark_round/pauseButtonSelectedState.png"></li>
				<li data-lightbox-maximize-button-normal-path="/templates/skin_minimal_dark_round/maximizeButtonNormalState.png"></li>
				<li data-lightbox-maximize-button-selected-path="/templates/skin_minimal_dark_round/maximizeButtonSelectedState.png"></li>
				<li data-lightbox-minimize-button-normal-path="/templates/skin_minimal_dark_round/minimizeButtonNormalState.png"></li>
				<li data-lightbox-minimize-button-selected-path="/templates/skin_minimal_dark_round/minimizeButtonSelectedState.png"></li>
				<li data-lightbox-info-button-open-normal-path="/templates/skin_minimal_dark_round/infoButtonOpenNormalState.png"></li>
				<li data-lightbox-info-button-open-selected-path="/templates/skin_minimal_dark_round/infoButtonOpenSelectedState.png"></li>
				<li data-lightbox-info-button-close-normal-path="/templates/skin_minimal_dark_round/infoButtonCloseNormalPath.png"></li>
				<li data-lightbox-info-button-close-selected-path="/templates/skin_minimal_dark_round/infoButtonCloseSelectedPath.png"></li>
			</ul> 
			<ul data-cat="Category one">
				<?
					if (count($artist['media']['mid']) > 1) {
						// if there's only the one photo, don't duplicate it down here...
						foreach (array_keys($artist['media']['mid']) as $key) {
							if ($artist['media']['vidlength'][$key] > 0) { continue; }
							?>
				<ul>
					<li data-type="media" data-url="/i/artist/original-<?= $artist['media']['filename'][$key]; ?>" data-target="_self"></li>
					<li data-thumbnail-path="/i/artist/<?= $artist['media']['filename'][$key]; ?>"></li>
					<li data-thumbnail-text>
						<p class="largeLabel"><?= $artist['name']; ?></p>
						<p class="smallLabel"><?= $artist['slug']; ?></p>
					</li>
					<li data-info="">
						<p class="mediaDescriptionHeader"><?= $artist['name']; ?> High Resolution Image Download</p>
						<p class="mediaDescriptionText"><a href="/i/artist/original-<?= $artist['media']['filename'][$key]; ?>"><?= $artist['media']['filename'][$key]; ?></a> (<?= $artist['media']['width'][$key]; ?> x <?= $artist['media']['height'][$key]; ?>) Published: <?= nicetime(date("r",$artist['media']['published'][$key])); ?></p>
					</li>
				</ul>
							<?
						}
					}
				?>
			</ul>
		</ul>
	<?

}

function htmlStylesTags($artists) {
	$colors = array("green","blue","turkese","orange","");
	$i = 0;
	?>
		<div class="artistStyles">
	<?
	foreach (array_keys($artists) as $aid) {
		foreach (array_keys($artists[$aid]['styles']) as $key) {
			if ($i > 8) { continue; }
			$color = $colors[$i];
			$i++;
			if ($i == count($colors)) {
				$i = 0;
			}
			?>
				<p class="btn big <?= $color; ?>"><?= $artists[$aid]['styles'][$key]; ?></p>
			<?
		}
	}
	?>
		</div>
	<?
}
