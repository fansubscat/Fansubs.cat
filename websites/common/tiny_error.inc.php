<!doctype html>
<html lang="<?php echo SITE_LANGUAGE; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php echo lang('error.page_title'); ?></title>
	</head>
	<body style="display: flex; flex-direction: column; align-items: center;">
		<div style="text-align: center; font-family: sans-serif; color: #303030; width: 50%;">
			<h2><?php echo lang('error.page_title'); ?></h2>
			<h3 style="color: brown;"><?php echo defined('ERROR_DESCRIPTION') ? ERROR_DESCRIPTION : '???'; ?></h3>
			<img src="https://i.imgur.com/XdPq82s.gif" alt="">
		</div>
	</body>
</html>
