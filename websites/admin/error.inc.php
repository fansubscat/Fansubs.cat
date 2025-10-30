<!doctype html>
<html lang="<?php echo SITE_LANGUAGE; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php echo lang('error.page_title').' - '.sprintf(lang('admin.generic.header'), MAIN_SITE_NAME); ?></title>
	</head>
	<body style="display: flex; flex-direction: column; align-items: center;">
		<div style="text-align: center; font-family: sans-serif; color: #303030; width: 50%;">
			<h2><?php echo lang('admin.error.error_occurred'); ?></h2>
			<h3 style="color: brown;"><?php echo defined('ERROR_DESCRIPTION') ? ERROR_DESCRIPTION : '???'; ?></h3>
			<img src="https://i.imgur.com/XdPq82s.gif" alt="">			
			<h3><?php echo lang('admin.error.contact_admin'); ?></h3>
			<h3><a href="/"><?php echo lang('admin.error.go_home'); ?></a></h3>
		</div>
	</body>
</html>
