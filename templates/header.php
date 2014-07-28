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
		<meta property="fb:app_id" content="596042810479776" />
		<meta property="fb:admins" content="529514765" />
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
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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
					<li><a href="/special/" title="Planning and Preparation of Weddings &amp; Special Events">Weddings/Events</a></li>
					<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
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
							<li><a href="/special/" title="Planning and Preparation of Weddings &amp; Special Events">Weddings/Events</a></li>
							<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
							<li><a href="/about/" title="Contact, Biographical, and Support Information">About Us</a></li>
						</ul>
					</li>
				</ul>
			</nav>
	<?
}

function htmlDropDownNavigationFull($navdata) {
	// Use this dropdown if including subcategories and artists
	?>
		<!-- Standard Navigation -->
		<nav class="nav col12">
			<ul>
				<li><a href="/" title="Home Page">Home</a></li>
				<li><a href="/talent/" title="Entertainment and Talent Categories List">Talent</a>
					<ul class="sub1">
						<?
							foreach (array_keys($navdata) as $categorykey) {
								foreach (array_keys($navdata[$categorykey]) as $subcatkey) {
									if ($subcatkey == 0) { 
										$line = sprintf("
											<li><a href=\"%s\" title=\"%s\">%s</a><span class=\"arrow\">&#x25b6;</span>
											<ul class=\"sub2\">",
											$navdata[$categorykey][0]['url'],
											$navdata[$categorykey][0]['description'],
											$navdata[$categorykey][0]['name']
										);
										echo $line;
									} else {
										$line = sprintf("
												<li><a href=\"%s\" title=\"%s\">%s</a></li>",
											$navdata[$categorykey][$subcatkey]['url'],
											$navdata[$categorykey][$subcatkey]['description'],
											$navdata[$categorykey][$subcatkey]['name']
										);
										echo $line;
									}
								}
								echo "</ul>\n";
							}
						?>
					</ul>
				</li>
				<li><a href="/production/" title="Production Management, Rigging, Lighting, Sound, Video, Rentals, and Equipment">Production</a></li>
				<li><a href="/special/" title="Planning and Preparation of Weddings &amp; Special Events">Weddings/Events</a></li>
				<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
				<li><a href="/about/" title="Contact, Biographical, and Support Information">About Us</a></li>
			</ul>
		</nav>
		<!-- end Standard Navigation -->
		<!-- Mobile (compressed menu) Navigation -->
		<nav class="menu nav col12">
			<ul>
				<li><img src="/templates/sbp/more.png">&nbsp;Menu
					<ul class="sub1">
						<li><a href="/" title="Home Page">Home</a></li>
						<li><a href="/talent/" title="Entertainment and Talent Categories List">Talent</a><span class="arrow">&#x25b6;</span>
							<!-- Sub-level 2 -->
							<ul class="sub2">
								<?
									foreach (array_keys($navdata) as $categorykey) {
										foreach (array_keys($navdata[$categorykey]) as $subcatkey) {
											if ($subcatkey == 0) { 
												$line = sprintf("
													<li><a href=\"%s\" title=\"%s\">%s</a><span class=\"arrow\">&#x25b6;</span><ul class=\"sub3\">",
													$navdata[$categorykey][0]['url'],
													$navdata[$categorykey][0]['description'],
													$navdata[$categorykey][0]['name']
												);
												echo $line;
											} else {
												$line = sprintf("
													<li><a href=\"%s\" title=\"%s\">%s</a></li>",
													$navdata[$categorykey][$subcatkey]['url'],
													$navdata[$categorykey][$subcatkey]['description'],
													$navdata[$categorykey][$subcatkey]['name']
												);
												echo $line;
											}
										}
										echo "</ul>\n";
									}
								?>
							</ul>
						</li>
						<li><a href="/production/" title="Production Management, Rigging, Lighting, Sound, Video, Rentals, and Equipment">Production</a></li>
						<li><a href="/special/" title="Planning and Preparation of Weddings &amp; Special Events">Weddings/Events</a></li>
						<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
						<li><a href="/about/" title="Contact, Biographical, and Support Information">About Us</a></li>
					</ul>
				</li>
			</ul>
		</nav>
		<!-- end Mobile (compressed menu) Navigation -->
	<?
}

function htmlDropDownNavigationSingle($navdata) {
	// use this dropdown if only want primary categories
	?>
		<!-- Standard Navigation -->
		<nav class="nav col12">
			<ul>
				<li><a href="/" title="Home Page">Home</a></li>
				<li><a href="/talent/" title="Entertainment and Talent Categories List">Talent</a>
					<ul class="sub1">
						<?
							foreach (array_keys($navdata) as $categorykey) {
								foreach (array_keys($navdata[$categorykey]) as $subcatkey) {
									if ($subcatkey == 0) { 
										$line = sprintf("
											<li><a href=\"%s\" title=\"%s\">%s</a>",
											$navdata[$categorykey][0]['url'],
											$navdata[$categorykey][0]['description'],
											$navdata[$categorykey][0]['name']
										);
										echo $line;
									}
								}
							}
						?>
					</ul>
				</li>
				<li><a href="/production/" title="Production Management, Rigging, Lighting, Sound, Video, Rentals, and Equipment">Production</a></li>
				<li><a href="/special/" title="Planning and Preparation of Weddings &amp; Special Events">Weddings/Events</a></li>
				<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
				<li><a href="/about/" title="Contact, Biographical, and Support Information">About Us</a></li>
			</ul>
		</nav>
		<!-- end Standard Navigation -->
		<!-- Mobile (compressed menu) Navigation -->
		<nav class="menu nav col12">
			<ul>
				<li><img src="/templates/sbp/more.png">&nbsp;Menu
					<ul class="sub1">
						<li><a href="/" title="Home Page">Home</a></li>
						<li><a href="/talent/" title="Entertainment and Talent Categories List">Talent</a><span class="arrow">&#x25b6;</span>
							<!-- Sub-level 2 -->
							<ul class="sub2">
								<?
									foreach (array_keys($navdata) as $categorykey) {
										foreach (array_keys($navdata[$categorykey]) as $subcatkey) {
											if ($subcatkey == 0) { 
												$line = sprintf("
													<li><a href=\"%s\" title=\"%s\">%s</a></li>",
													$navdata[$categorykey][0]['url'],
													$navdata[$categorykey][0]['description'],
													$navdata[$categorykey][0]['name']
												);
												echo $line;
											}
										}
									}
								?>
							</ul>
						</li>
						<li><a href="/production/" title="Production Management, Rigging, Lighting, Sound, Video, Rentals, and Equipment">Production</a></li>
						<li><a href="/special/" title="Planning and Preparation of Weddings &amp; Special Events">Weddings/Events</a></li>
						<li><a href="/decor/" title="Furnishings and Accents">Decor</a></li>
						<li><a href="/about/" title="Contact, Biographical, and Support Information">About Us</a></li>
					</ul>
				</li>
			</ul>
		</nav>
		<!-- end Mobile (compressed menu) Navigation -->
	<?
}

function htmlWavesStart() {
	?>
	<!-- dark waves -->
	<div class="dark carousel col12">
	<?
}

function htmlWavesStartShort() {
	?>
	<!-- dark waves -->
	<div class="dark carouselFull col12">
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
