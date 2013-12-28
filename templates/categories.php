<?
	/* Category displays */

function ErrorDisplay($error) {
	echo "<div class='SiteError'>$error</div>";
}

function ListCategoryCarousel ($categoryList) {
	// $url, $category, description, is_highlighted
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
					foreach ($categoryList as $key => $blah) {
						?>
						<ul>
							<li data-type="link" data-url="/categories/<?= $categoryList[$key]['url']; ?>" data-target="_self"></li>
							<li data-thumbnail-path="/i/category/<?= $categoryList[$key]['carousel_id']; ?>"></li>
							<li data-thumbnail-text="<?= $categoryList[$key]['category']; ?>" data-thumbnail-text-title-offset="35" data-thumbnail-text-offset-top="10" data-thumbnail-text-offset-bottom="7">
								<p class="largeLabel"><?= $categoryList[$key]['category']; ?></p>
								<p class="smallLabel"><?= $categoryList[$key]['description']; ?></p>
							</li>
							<li data-info="">
								<p class="mediaDescriptionHeader"><?= $categoryList[$key]['catagory']; ?></p>
								<p class="mediaDescriptionText"><?= $categoryList[$key]['description']; ?></p>
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

function ListAllCategories($categoryList) {
	// $url, $category, description, is_highlighted
	echo "<div class='content'>";
		foreach ($categoryList as $key => $blah) {
			$i++;
			($i % 2 == 0 )? $float="fr" : $float="fl";
			?>
				<div class="box catButton blue <?= $float; ?>"><a href="/category/<?= $categoryList[$key]['url']; ?>"><img src="/i/category/<?= $categoryList[$key]['image_id']; ?>" width="420" height="62" title="<?= $categoryList[$key]['category']; ?> - <?= $categoryList[$key]['description']; ?>" alt="<?= $categoryList[$key]['category']; ?>"></a></div>
			<?
		}
	echo "</div>";
}

function htmlCategoryImage($categoryImage,$category) {
	?>
		<div class='catHeader'><img src="/i/category/<?= $categoryImage; ?>" width="485" height="60" title="<?= $category; ?>" alt="<?= $category; ?>"></div>
	<?
}

function ListArtistsForCategory($category,$artists)	{
	// aid, name, url, slug, is_highlighted
	//asdf
	?>
		<div class="artistArray"><!-- start of artistArray -->
		<div id="goGrid" style="width:100%;"></div>
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
				foreach ($artists as $key => $blah) {
					?>
				<ul>
					<li data-type="link" data-url="/artist/<?= $artists[$key]['url']; ?>" data-target="_self"></li>
					<li data-thumbnail-path="/i/artist/<?= $artists[$key]['filename']; ?>"></li>
					<li data-thumbnail-text>
						<p class="largeLabel"><?= $artists[$key]['name']; ?></p>
						<p class="smallLabel"><?= $artists[$key]['slug']; ?></p>
					</li>
					<li data-info="">
						<p class="mediaDescriptionHeader">CUSTOM PRESS THUMBNAIL ACTION.</p>
						<p class="mediaDescriptionText">When a thumbnail is pressed you can choose either to display an original media lightbox which we have coded, or to open a new webpage, the url and target of this webpage can be specified. The lightbox can display images, or can display videos loaded from YouTube or Vimeo. Also you can set up one or more thumbnails so that a new browser page will be opened when they are pressed. The URL and target for this page can be customized.</p>
					</li>
				</ul>
					<?
				}
				?>
			</ul>
		</ul>
	</div><!-- /end of artistArray -->
	<?
}

function ListArtistCarousel($category,$artists) {
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
			</ul>
			<!-- category  -->
			<ul data-cat="Category one">
			<?
				foreach ($artists as $key => $blah) {
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
		</ul><!-- /category -->
	</ul><!-- /carouselPlaylist -->
	<?
}
