<?
function htmlHomePageTop () {
}

function htmlHomePageCategories($categories) {
	echo "<div class=\"artistTop\">";
	echo "<br><hr><br>";
	foreach (array_keys($categories) as $key) {
		$link = sprintf(
			"<div class=\"col2 \"><a href=\"/category/%s\" title=\"%s\">%s</a></div>\n",
			$categories[$key]['url'],
			$categories[$key]['category'] . " - " . $categories[$key]['description'],
			$categories[$key]['category']
		);
		echo $link;
	}
	echo "</div>";
	echo "<div class=\"clearfix\"></div>";
	echo "<br><hr><br>";
}

function homePageCarousel($artists) {
	?>
		<div id="goCarousel"></div>
			<ul id="carouselPlaylist" style="display: none;">
				<!-- skin -->
				<ul data-skin="">
					<li data-preloader-path="/templates/skin_modern_silver/preloader.png"></li>
					<li data-thumbnail-gradient-left-path="/templates/skin_modern_silver/gradientLeft.png"></li>
					<li data-thumbnail-gradient-right-path="/templates/skin_modern_silver/gradientRight.png"></li>
					<li data-thumbnail-title-gradient-path="/templates/skin_modern_silver/textGradient.png"></li>
					<li data-next-button-normal-path="/templates/skin_modern_silver/nextButtonNormalState.png"></li>
					<li data-next-button-selected-path="/templates/skin_modern_silver/nextButtonSelectedState.png"></li>
					<li data-prev-button-normal-path="/templates/skin_modern_silver/prevButtonNormalState.png"></li>
					<li data-prev-button-selected-path="/templates/skin_modern_silver/prevButtonSelectedState.png"></li>
					<li data-play-button-normal-path="/templates/skin_modern_silver/playButtonNormalState.png"></li>
					<li data-play-button-selected-path="/templates/skin_modern_silver/playButtonSelectedState.png"></li>
					<li data-pause-button-path="/templates/skin_modern_silver/pauseButtonSelectedState.png"></li>
					<li data-handler-left-normal-path="/templates/skin_modern_silver/handlerLeftNormal.png"></li>
					<li data-handler-left-selected-path="/templates/skin_modern_silver/handlerLeftSelected.png"></li>
					<li data-handler-center-normal-path="/templates/skin_modern_silver/handlerCenterNormal.png"></li>
					<li data-handler-center-selected-path="/templates/skin_modern_silver/handlerCenterSelected.png"></li>
					<li data-handler-right-normal-path="/templates/skin_modern_silver/handlerRightNormal.png"></li>
					<li data-handler-right-selected-path="/templates/skin_modern_silver/handlerRightSelected.png"></li>
					<li data-track-left-path="/templates/skin_modern_silver/trackLeft.png"></li>
					<li data-track-center-path="/templates/skin_modern_silver/trackCenter.png"></li>
					<li data-track-right-path="/templates/skin_modern_silver/trackRight.png"></li>
					<li data-slideshow-timer-path="/templates/skin_modern_silver/slideshowTimer.png"></li>
					<li data-lightbox-slideshow-preloader-path="/templates/skin_modern_silver/slideShowPreloader.png"></li>
					<li data-lightbox-close-button-normal-path="/templates/skin_modern_silver/closeButtonNormalState.png"></li>
					<li data-lightbox-close-button-selected-path="/templates/skin_modern_silver/closeButtonSelectedState.png"></li>
					<li data-lightbox-next-button-normal-path="/templates/skin_modern_silver/lightboxNextButtonNormalState.png"></li>
					<li data-lightbox-next-button-selected-path="/templates/skin_modern_silver/lightboxNextButtonSelectedState.png"></li>
					<li data-lightbox-prev-button-normal-path="/templates/skin_modern_silver/lightboxPrevButtonNormalState.png"></li>
					<li data-lightbox-prev-button-selected-path="/templates/skin_modern_silver/lightboxPrevButtonSelectedState.png"></li>
					<li data-lightbox-play-button-normal-path="/templates/skin_modern_silver/lightboxPlayButtonNormalState.png"></li>
					<li data-lightbox-play-button-selected-path="/templates/skin_modern_silver/lightboxPlayButtonSelectedState.png"></li>
					<li data-lightbox-pause-button-normal-path="/templates/skin_modern_silver/lightboxPauseButtonNormalState.png"></li>
					<li data-lightbox-pause-button-selected-path="/templates/skin_modern_silver/lightboxPauseButtonSelectedState.png"></li>
					<li data-lightbox-maximize-button-normal-path="/templates/skin_modern_silver/maximizeButtonNormalState.png"></li>
					<li data-lightbox-maximize-button-selected-path="/templates/skin_modern_silver/maximizeButtonSelectedState.png"></li>
					<li data-lightbox-minimize-button-normal-path="/templates/skin_modern_silver/minimizeButtonNormalState.png"></li>
					<li data-lightbox-minimize-button-selected-path="/templates/skin_modern_silver/minimizeButtonSelectedState.png"></li>
					<li data-lightbox-info-button-open-normal-path="/templates/skin_modern_silver/infoButtonOpenNormalState.png"></li>
					<li data-lightbox-info-button-open-selected-path="/templates/skin_modern_silver/infoButtonOpenSelectedState.png"></li>
					<li data-lightbox-info-button-close-normal-path="/templates/skin_modern_silver/infoButtonCloseNormalPath.png"></li>
					<li data-lightbox-info-button-close-selected-path="/templates/skin_modern_silver/infoButtonCloseSelectedPath.png"></li>
					<li data-combobox-arrow-icon-normal-path="/templates/skin_modern_silver/comboboxArrowNormal.png"></li>
					<li data-combobox-arrow-icon-selected-path="/templates/skin_modern_silver/comboboxArrowSelected.png"></li>
				</ul><!-- /data-skin -->
				<!-- /skin -->
				<!-- category  -->
				<ul data-cat="Category one">
				<?
					foreach (array_keys($artists) as $key) {
						?>
						<ul>
							<li data-type="link" data-url="/artist/<?= $artists[$key]['url']; ?>" data-target="_self"></li>
							<li data-thumbnail-path="/i/artist/<?= $artists[$key]['filename']; ?>"></li>
							<li data-thumbnail-text="<?= $artists[$key]['name']; ?>" data-thumbnail-text-title-offset="35" data-thumbnail-text-offset-top="10" data-thumbnail-text-offset-bottom="7">
								<p class="largeLabel"><?= $artists[$key]['name']; ?></p>
								<p class="smallLabel"><?= $artists[$key]['slug']; ?></p>
							</li>
							<li data-info="">
								<p class="mediaDescriptionHeader"><?= $artists[$key]['name']; ?></p>
								<p class="mediaDescriptionText"><?= $artists[$key]['slug']; ?></p>
							</li>
						</ul>
						<?
					}
				?>
				</ul><!-- /category one -->
			<!-- /category -->
		</ul><!-- /CarouselPlaylist -->
	<?
}
