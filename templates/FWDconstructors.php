<?

function fwdConsStart() {
	?>
		<script type="text/javascript">
			var carousel;
			var grid;
			FWDUtils.onReady(function(){
	<?
}

function fwdConsStop() {
	?>
			})
		</script>
	<?
}

function fwdConsCarousel() {
	?>
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
	<?
}






function fwdConsGrid() {
	?>	
				grid = new FWDGrid({
					//main settings
					gridHolderId:"ArtistsGrid",
					gridPlayListAndSkinId:"playlist",
					showContextMenu:"no",
					//grid settings
					thumbnailOverlayType:"text",
					addMargins:"no",
					loadMoreThumbsButtonOffest:4,
					thumbnailBaseWidth:278,
					thumbnailBaseHeight:188,
					nrOfThumbsToShowOnSet:27,
					horizontalSpaceBetweenThumbnails:8,
					verticalSpaceBetweenThumbnails:8,
					thumbnailBorderSize:4,
					thumbnailBorderRadius:4,
					thumbnailOverlayOpacity:.85,
					//backgroundColor:"#000000",
					thumbnailOverlayColor:"#000000",
					thumbnailBackgroundColor:"#333333",
					thumbnailBorderNormalColor:"#FFFFFF",
					thumbnailBorderSelectedColor:"#DDDDDD",
					//combobox settings
					startAtCategory:1,
					selectLabel:"SELECT CATEGORIES",
					allCategoriesLabel:"All Categories",
					showAllCategories:"no",
					comboBoxPosition:"topleft",
					selctorBackgroundNormalColor:"#FFFFFF",
					selctorBackgroundSelectedColor:"#000000",
					selctorTextNormalColor:"#000000",
					selctorTextSelectedColor:"#FFFFFF",
					buttonBackgroundNormalColor:"#FFFFFF",
					buttonBackgroundSelectedColor:"#000000",
					buttonTextNormalColor:"#000000",
					buttonTextSelectedColor:"#FFFFFF",
					comboBoxShadowColor:"#000000",
					comboBoxHorizontalMargins:12,
					comboBoxVerticalMargins:12,
					comboBoxCornerRadius:4,
					//ligtbox settings
					addLightBoxKeyboardSupport:"yes",
					showLightBoxNextAndPrevButtons:"yes",
					showLightBoxZoomButton:"yes",
					showLightBoxInfoButton:"yes",
					showLighBoxSlideShowButton:"yes",
					showLightBoxInfoWindowByDefault:"no",
					slideShowAutoPlay:"no",
					lightBoxVideoAutoPlay:"no",
					lighBoxBackgroundColor:"#000000",
					lightBoxInfoWindowBackgroundColor:"#FFFFFF",
					lightBoxItemBorderColor:"#FFFFFF",
					lightBoxItemBackgroundColor:"#222222",
					lightBoxMainBackgroundOpacity:.8,
					lightBoxInfoWindowBackgroundOpacity:.9,
					lightBoxBorderSize:4,
					lightBoxBorderRadius:6,
					lightBoxSlideShowDelay:4
				});
	<?
}
