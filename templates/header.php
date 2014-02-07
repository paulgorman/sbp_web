<?

function htmlHeader($dataArray) {
	// $dataArray has:
	//  $title // 70 chars
	//  $description // 155 chars meta description call to action
	//  $keywords
	//  $image // full URL
	//  $url
	//  $jsinclude array of filenames
	//	$cssinclude array of filenames
	//  $bc array of breadcrumbs 'name' / 'url'
	?>
<!DOCTYPE html>
<html lang="en">
   <head>

		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta http-equiv="Content-Language" content="en" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Cache-Control" content="private, max-age=5400, pre-check=5400" />
		<meta http-equiv="Expires" content="<?= date(DATE_RFC822,strtotime("1 day")); ?>" />
		<title><?= $dataArray['title']; ?></title>
		<meta name="robots" content="all" /> 
		<meta name="description" content="<?= $dataArray['description']; ?>" />
		<meta name="keywords" content="<?= $dataArray['keywords']; ?>" />
		<meta name="copyright" content="&copy; <?= date("Y"); ?> Steve Beyer Productions" />
		<meta name="viewport" content="width=device-width; initial-scale=1.0" />
		<meta property="og:title" content="<?= $dataArray['title']; ?>" />
		<meta property="og:type" content="article" />
		<meta property="og:image" content="<?= $dataArray['image']; ?>" />
		<meta property="og:url" content="<?= $dataArray['url']; ?>" />
		<meta property="og:description" content="<?= $dataArray['description']; ?>" />
		<meta property="og:site_name" content="Steve Beyer Productions" />
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:title" content="<?= $dataArray['title']; ?>" />
		<meta name="twitter:description" content="<?= $dataArray['description']; ?>" />
		<meta name="twitter:image" content="<?= $dataArray['image']; ?>" />
		<link rel="author" href="/humans.txt">
		<link rel="canonical" href="<?= $dataArray['url']; ?>" />
		<link rel="stylesheet" type="text/css" href="/templates/css/responsiveboilerplate.css">
		<link rel="stylesheet" type="text/css" href="/templates/css/sbp.css">
		<? if (isset($dataArray['css'])) { foreach ($dataArray['css'] as $css) { ?> 
			<link rel="stylesheet" href="/templates/css/<?= $css; ?>" />
		<? } } ?>
		<? if (isset($dataArray['js'])) { foreach ($dataArray['js'] as $js) { ?> 
			<script type="text/javascript" src="/templates/js/<?= $js; ?>"></script>
		<? } } ?>

	</head>
	<body>
		<div class="content">
	<?
}

function htmlMasthead($meta) {
	?>
		<header class="col12">
			<img class="logo" src="/templates/sbp/sbp-logo.png" title="Steve Beyer Productions" alt="SBP" />
			<div class="swoosh"></div>
		</header>
	<?
}

function htmlNavigation($meta) {
	?>
			<nav class="nav col12">
				<ul>
					<li><a href="/" title="Home Page">Home</a></li>
					<li><a href="/talent/" title="Entertainment and Talent Categories List">Talent</a></li>
					<li><a href="/production/" title="Production Management, Rigging, Lighting, Sound, Video, Rentals, and Equipment">Production</a></li>
					<li><a href="/special/" title="Planning and Preparation of Special Events">Event Planning</a></li>
					<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
					<li><a href="/wedding/" title="Special Requirements and Wishes of Beautiful Weddings">Weddings</a></li>
					<li><a href="/about/" title="Contact, Biographical, and Support Information">About Us</a></li>
				</ul>
			</nav>
			<nav class="menu col12">
				<ul>
					<li><img src="/templates/sbp/more.png" />&nbsp;Menu
						<ul class="subMenu">
							<li><a href="/" title="Home Page">Home</a></li>
							<li><a href="/talent/" title="Entertainment and Talent Categories List">Talent</a></li>
							<li><a href="/production/" title="Production Management, Rigging, Lighting, Sound, Video, Rentals, and Equipment">Production</a></li>
							<li><a href="/event/" title="Planning and Preparation of Special Events">Event Planning</a></li>
							<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
							<li><a href="/wedding/" title="Special Requirements and Wishes of Beautiful Weddings">Weddings</a></li>
							<li><a href="/about/" title="Contact, Biographical, and Support Information">About Us</a></li>
						</ul>
					</li>
				</ul>
			</nav>
	<?
}

function htmlWavesStart() {
	?>
	<!-- dark waves -->
	<div class="dark carousel col12">
	<?
}

function htmlWavesFullStart() {
	?>
	<!-- dark waves artist page -->
	<div class="dark carouselFull col12">
	<?
}

function htmlWavesShortStart() {
	?>
	<!-- dark waves -->
	<div class="dark carousel carouselShort col12">
	<?
}

function htmlBodyStart() {
	?>
	</div> <!-- / dark waves -->
	<!-- purple body -->
	<div class="purple col12"> 
	<?
}

function htmlBreadcrumb($meta) {
	echo "\t<div class=\"breadcrumb\">\n";
	echo "\t\t\t" . '<div class="breadcrumbitem"><a href="' . curServerURL() . '" title="Entertainment Home">Home</a></div>';
	foreach ($meta['breadcrumb'] as $bc) { 
		echo "\n\t\t\t". '<div class="breadcrumbitem"><a href="' . $bc['url'] . '" title="' . $bc['name'] . '">' . $bc['name'] . '</a></div>';
	}
	echo "\n\t\t</div><!-- /breadcrumb -->\n\n";
}


function htmlFooter($meta) {
	?>
			</div><!-- /purple body -->
			<footer class="col12">
				<p>
					Steve Beyer Productions, Inc - The Entertainment & Production Company - (702) 568-9000
				</p>
			</footer>
		</div>
	</body>
</html>
	<?
}

function htmlContent($content) {
	?>
		<div class="content" style="max-width: 960px; margin: 0px auto 0px auto;">
			<div class="col12 homeBody">
				<?= $content; ?>
			</div>
		</div>
		<br>
	<?
}
