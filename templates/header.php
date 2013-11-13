<?

function htmlHeader($dataArray) {
	// $dataArray has:
	//  $title // 70 chars
	//  $description // 155 chars meta description call to action
	//  $keywords
	//  $image // full URL
	//  $url
	//  $jsinclude array
	//	$cssinclude array
	?>
<!DOCTYPE html>
	<html lang="en">
    <head>

			<meta charset="utf-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta http-equiv="Cache-Control" content="private, max-age=5400, pre-check=5400" />
			<meta http-equiv="Expires" content="<?= date(DATE_RFC822,strtotime("1 day")); ?>" />
			<title><?= $dataArray['title']; ?></title>
			<meta name="robots" content="all" /> 
			<meta name="description" content="<?= $dataArray['description']; ?>" />
			<meta name="keywords" content="<?= $dataArray['keywords']; ?>" />
			<meta name="copyright" content="&copy; <?= date("Y"); ?> Steve Beyer Productions" />
			<meta name="viewport" content="width=device-width" />
			<meta property="og:title" content="<?= $dataArray['title']; ?>" />
			<meta property="og:type" content="article" />
			<meta property="og:image" content="<?= $dataArray['image']; ?>" />
			<meta property="og:url" content="<?= $dataArray['url']; ?>" />
			<meta property="og:description" content="<?= $dataArray['description']; ?>" />
			<meta name="twitter:card" content="summary" />
			<meta name="twitter:title" content="<?= $dataArray['title']; ?>" />
			<meta name="twitter:description" content="<?= $dataArray['description']; ?>" />
			<meta name="twitter:image" content="<?= $dataArray['image']; ?>" />
			<link rel="author" href="humans.txt">
			<link rel="canonical" href="<?= $dataArray['url']; ?>" />
			<link rel="stylesheet" href="/templates/css/normalize.css" />
			<link rel="stylesheet" href="/templates/css/sbp.css" />
			<? foreach ($dataArray['css'] as $css) { ?> 
				<link rel="stylesheet" href="/templates/css/<?= $css; ?>" />
			<? } ?>
			<? foreach ($dataArray['js'] as $js) { ?> 
				<script type="text/javascript" src="/templates/js/<?= $js; ?>"></script>
			<? } ?>

		</head>
		<body>
	<?
}
