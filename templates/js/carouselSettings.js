
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
					thumbnailWidth:420,
					thumbnailHeight:286,
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
