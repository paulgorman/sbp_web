<?
	/* Category displays */

function ErrorDisplay($error) {
	echo "<div class='SiteError'>$error</div>";
}

function ListCategoryCarousel ($categoryList) {
	// $url, $category, description, is_highlighted
	?>
		<script type="text/javascript">
			var carousel;
			
			FWDUtils.onReady(function(){
				carousel = new FWD3DCarousel({
				
					//required settings
					carouselHolderDivId:"goCarousel",
					carouselDataListDivId:"playList1",
					displayType:"fluidwidth",
					autoScale:"yes",
					carouselWidth:940,
					carouselHeight:338,
												
					//main settings
					/* Presence's hack to hide scroll bar and controls
					backgroundColor:"#000000",
					backgroundImagePath:"templates/skin_modern_silver/background.jpg",
					thumbnailsBackgroundImagePath:"templates/skin_modern_silver/thumbnailsBackground.jpg",
					scrollbarBackgroundImagePath:"templates/skin_modern_silver/scrollbarBackground.jpg",
					*/
					backgroundRepeat:"repeat-x",
					showDisplay2DAlways:"no",
					carouselStartPosition:"center",
					numberOfThumbnailsToDisplayLeftAndRight:4,
					slideshowDelay:2000,
					autoplay:"yes",
					showPrevButton:"yes",
					showNextButton:"yes",
					showSlideshowButton:"yes",
					disableNextAndPrevButtonsOnMobile:"no",
					controlsMaxWidth:"x", //  Presence's hack to hide controls bar.
					slideshowTimerColor:"#777777",
					showContextMenu:"no",
												
					//thumbnail settings
					thumbnailWidth:400,
					thumbnailHeight:266,
					thumbnailSpaceOffset3D:-19,
					thumbnailSpaceOffset2D:-19,
					thumbnailBorderSize:4,
					thumbnailBackgroundColor:"#666666",
					thumbnailBorderColor1:"#fcfdfd",
					thumbnailBorderColor2:"#e4e4e4",
					maxNumberOfThumbnailsOnMobile:13,
					showThumbnailsHtmlContent:"no",
					enableHtmlContent:"no",
					textBackgroundColor:"#333333",
					textBackgroundOpacity:.7,
					showText:"yes",
					showTextBackgroundImage:"yes",
					showThumbnailBoxShadow:"yes",
					thumbnailBoxShadowCss:"0px 2px 2px #555555",
												
					//scrollbar settings
					showScrollbar:"yes",
					disableScrollbarOnMobile:"yes",
					enableMouseWheelScroll:"yes",
					scrollbarMaxWidth:940,
					scrollbarHandlerWidth:300,
					scrollbarTextColorNormal:"#777777",
					scrollbarTextColorSelected:"#000000",
												
					//combobox settings
					startAtCategory:1,
					selectLabel:"SELECT CATEGORIES",
					allCategoriesLabel:"All Categories",
					showAllCategories:"no",
					comboBoxPosition:"topright",
					selectorBackgroundNormalColor1:"#fcfdfd",
					selectorBackgroundNormalColor2:"#e4e4e4",
					selectorBackgroundSelectedColor1:"#a7a7a7",
					selectorBackgroundSelectedColor2:"#8e8e8e",
					selectorTextNormalColor:"#8b8b8b",
					selectorTextSelectedColor:"#FFFFFF",
					buttonBackgroundNormalColor1:"#e7e7e7",
					buttonBackgroundNormalColor2:"#e7e7e7",
					buttonBackgroundSelectedColor1:"#a7a7a7",
					buttonBackgroundSelectedColor2:"#8e8e8e",
					buttonTextNormalColor:"#000000",
					buttonTextSelectedColor:"#FFFFFF",
					comboBoxShadowColor:"#000000",
					comboBoxHorizontalMargins:12,
					comboBoxVerticalMargins:12,
					comboBoxCornerRadius:0,
												
					//lightbox settings
					addLightBoxKeyboardSupport:"yes",
					showLightBoxNextAndPrevButtons:"yes",
					showLightBoxZoomButton:"yes",
					showLightBoxInfoButton:"yes",
					showLighBoxSlideShowButton:"yes",
					showLightBoxInfoWindowByDefault:"no",
					slideShowAutoPlay:"no",
					lightBoxVideoAutoPlay:"no",
					lightBoxBackgroundColor:"#000000",
					lightBoxInfoWindowBackgroundColor:"#FFFFFF",
					lightBoxItemBorderColor1:"#fcfdfd",
					lightBoxItemBorderColor2:"#e4FFe4",
					lightBoxItemBackgroundColor:"#333333",
					lightBoxMainBackgroundOpacity:.8,
					lightBoxInfoWindowBackgroundOpacity:.9,
					lightBoxBorderSize:5,
					lightBoxBorderRadius:0,
					lightBoxSlideShowDelay:4
				});
			})
		</script>
		<div id="goCarousel"></div>
			<ul id="playList1" style="display: none;">
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
		</ul>
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
