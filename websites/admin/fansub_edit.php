<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.fansub_edit.header');
$page="fansub";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && ($_SESSION['admin_level']>=3 || ($_SESSION['admin_level']==2 && !empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id']) && ($_SESSION['fansub_id']==$_GET['id'] || $_SESSION['fansub_id']==$_POST['id'])))) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.id_missing'));
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash(lang('admin.error.name_missing'));
		}
		if (!empty($_POST['slug'])) {
			$data['slug']=escape($_POST['slug']);
		} else {
			crash(lang('admin.error.slug_missing'));
		}
		if (!empty($_POST['type'])) {
			$data['type']=escape($_POST['type']);
		} else {
			crash(lang('admin.error.type_missing'));
		}
		if (!empty($_POST['url'])) {
			$data['url']="'".escape($_POST['url'])."'";
		} else {
			$data['url']="NULL";
		}
		if (!empty($_POST['mastodon_url'])) {
			$data['mastodon_url']="'".escape($_POST['mastodon_url'])."'";
		} else {
			$data['mastodon_url']="NULL";
		}
		if (!empty($_POST['mastodon_handle'])) {
			$data['mastodon_handle']=escape($_POST['mastodon_handle']);
		} else {
			crash(lang('admin.error.mastodon_handle_missing'));
		}
		if (!empty($_POST['twitter_url'])) {
			$data['twitter_url']="'".escape($_POST['twitter_url'])."'";
		} else {
			$data['twitter_url']="NULL";
		}
		if (!empty($_POST['twitter_handle'])) {
			$data['twitter_handle']=escape($_POST['twitter_handle']);
		} else {
			crash(lang('admin.error.twitter_handle_missing'));
		}
		if (!empty($_POST['bluesky_url'])) {
			$data['bluesky_url']="'".escape($_POST['bluesky_url'])."'";
		} else {
			$data['bluesky_url']="NULL";
		}
		if (!empty($_POST['bluesky_handle'])) {
			$data['bluesky_handle']=escape($_POST['bluesky_handle']);
		} else {
			crash(lang('admin.error.bluesky_handle_missing'));
		}
		if (!empty($_POST['discord_url'])) {
			$data['discord_url']="'".escape($_POST['discord_url'])."'";
		} else {
			$data['discord_url']="NULL";
		}
		if (!empty($_POST['facebook_url'])) {
			$data['facebook_url']="'".escape($_POST['facebook_url'])."'";
		} else {
			$data['facebook_url']="NULL";
		}
		if (!empty($_POST['instagram_url'])) {
			$data['instagram_url']="'".escape($_POST['instagram_url'])."'";
		} else {
			$data['instagram_url']="NULL";
		}
		if (!empty($_POST['linktree_url'])) {
			$data['linktree_url']="'".escape($_POST['linktree_url'])."'";
		} else {
			$data['linktree_url']="NULL";
		}
		if (!empty($_POST['telegram_url'])) {
			$data['telegram_url']="'".escape($_POST['telegram_url'])."'";
		} else {
			$data['telegram_url']="NULL";
		}
		if (!empty($_POST['threads_url'])) {
			$data['threads_url']="'".escape($_POST['threads_url'])."'";
		} else {
			$data['threads_url']="NULL";
		}
		if (!empty($_POST['youtube_url'])) {
			$data['youtube_url']="'".escape($_POST['youtube_url'])."'";
		} else {
			$data['youtube_url']="NULL";
		}
		if (!empty($_POST['ping_token'])) {
			$data['ping_token']="'".escape($_POST['ping_token'])."'";
		} else {
			$data['ping_token']="NULL";
		}
		if (!empty($_POST['status']) && $_POST['status']==1) {
			$data['status']=1;
		} else {
			$data['status']=0;
		}
		if (!empty($_POST['is_historical']) && $_POST['is_historical']==1) {
			$data['is_historical']=1;
		} else {
			$data['is_historical']=0;
		}
		if (!empty($_POST['archive_url'])) {
			$data['archive_url']="'".escape($_POST['archive_url'])."'";
		} else {
			$data['archive_url']="NULL";
		}
		if (!empty($_POST['hentai_category']) && ($_POST['hentai_category']==1 || $_POST['hentai_category']==2)) {
			$data['hentai_category']=$_POST['hentai_category'];
		} else {
			$data['hentai_category']=0;
		}
		if (!empty($_POST['has_site_access']) && $_POST['has_site_access']==1) {
			$data['has_site_access']=1;
		} else {
			$data['has_site_access']=0;
		}
		if (!empty($_POST['old_username'])) {
			$data['old_username']=escape($_POST['old_username']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.old_username_missing'));
		}
		if (!empty($_POST['user_password'])) {
			$data['user_password']=escape($_POST['user_password']);
		} else if ($_POST['action']=='edit') {
			$data['user_password']='';
		} else {
			crash(lang('admin.error.user_password_missing'));
		}
		if (!empty($_POST['email'])) {
			$data['email']=escape($_POST['email']);
		} else {
			crash(lang('admin.error.email_missing'));
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM fansub WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash(lang('admin.error.fansub_edit_concurrency_error'));
			}
			$user_email_result = query("SELECT * FROM user WHERE email='".$data['email']."' AND fansub_id<>".$data['id']);
			if (mysqli_num_rows($user_email_result)>0) {
				crash(lang('admin.error.fansub_edit_email_error'));
			}
			
			log_action("update-fansub", "Fansub «".$_POST['name']."» (fansub id: ".$data['id'].") updated");
			query("UPDATE fansub SET name='".$data['name']."',slug='".$data['slug']."',type='".$data['type']."',url=".$data['url'].",email='".$data['email']."',twitter_url=".$data['twitter_url'].",twitter_handle='".$data['twitter_handle']."',mastodon_url=".$data['mastodon_url'].",mastodon_handle='".$data['mastodon_handle']."',bluesky_url=".$data['bluesky_url'].",bluesky_handle='".$data['bluesky_handle']."',discord_url=".$data['discord_url'].",facebook_url=".$data['facebook_url'].",instagram_url=".$data['instagram_url'].",linktree_url=".$data['linktree_url'].",telegram_url=".$data['telegram_url'].",threads_url=".$data['threads_url'].",youtube_url=".$data['youtube_url'].",status=".$data['status'].",ping_token=".$data['ping_token'].",is_historical=".$data['is_historical'].",archive_url=".$data['archive_url'].",hentai_category=".$data['hentai_category'].",has_site_access=".$data['has_site_access'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);

			if (!empty($_FILES['icon'])) {
				move_uploaded_file($_FILES['icon']["tmp_name"], STATIC_DIRECTORY.'/images/icons/'.$data['id'].'.png');
			}
			
			edit_fansub_user($data['id'], $data['old_username'], $data['user_password']);
		}
		else {
			$user_email_result = query("SELECT * FROM user WHERE email='".$data['email']."'");
			if (mysqli_num_rows($user_email_result)>0) {
				crash(lang('admin.error.fansub_edit_email_error'));
			}
			
			log_action("create-fansub", "Fansub «".$_POST['name']."» created");
			query("INSERT INTO fansub (name,slug,type,url,email,twitter_url,twitter_handle,mastodon_url,mastodon_handle,bluesky_handle,bluesky_url,discord_url,facebook_url,instagram_url,linktree_url,telegram_url,threads_url,youtube_url,status,ping_token,is_historical,archive_url,hentai_category,has_site_access,created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['slug']."','".$data['type']."',".$data['url'].",'".$data['email']."',".$data['twitter_url'].",'".$data['twitter_handle']."',".$data['mastodon_url'].",'".$data['mastodon_handle']."','".$data['bluesky_handle']."',".$data['bluesky_url'].",".$data['discord_url'].",".$data['facebook_url'].",".$data['instagram_url'].",".$data['linktree_url'].",".$data['telegram_url'].",".$data['threads_url'].",".$data['youtube_url'].",".$data['status'].",".$data['ping_token'].",".$data['is_historical'].",".$data['archive_url'].",".$data['hentai_category'].",".$data['has_site_access'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			
			$fansub_id=mysqli_insert_id($db_connection);

			if (!empty($_FILES['icon'])) {
				move_uploaded_file($_FILES['icon']["tmp_name"], STATIC_DIRECTORY.'/images/icons/'.$fansub_id.'.png');
			}
			
			add_fansub_user($fansub_id, $data['user_password']);
		}

		$_SESSION['message']=lang('admin.generic.data_saved');

		header("Location: fansub_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT f.*, (SELECT u.username FROM user u WHERE u.fansub_id=f.id) old_username FROM fansub f WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.fansub_not_found'));
		mysqli_free_result($result);
	} else {
		$row = array();
		$row['id'] = '';
		$row['name'] = '';
		$row['updated'] = '';
		$row['old_username'] = '';
		$row['slug'] = '';
		$row['type'] = '';
		$row['url'] = '';
		$row['email'] = '';
		$row['bluesky_url'] = '';
		$row['bluesky_handle'] = '';
		$row['mastodon_url'] = '';
		$row['mastodon_handle'] = '';
		$row['twitter_url'] = '';
		$row['twitter_handle'] = '';
		$row['discord_url'] = '';
		$row['facebook_url'] = '';
		$row['instagram_url'] = '';
		$row['linktree_url'] = '';
		$row['telegram_url'] = '';
		$row['threads_url'] = '';
		$row['youtube_url'] = '';
		$row['status'] = 1;
		$row['is_historical'] = 0;
		$row['hentai_category'] = '';
		$row['has_site_access'] = 0;
		$row['archive_url'] = '';
		$row['ping_token'] = '';
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? lang('admin.fansub_edit.edit_title') : lang('admin.fansub_edit.create_title'); ?></h4>
				<hr>
				<form method="post" action="fansub_edit.php" enctype="multipart/form-data" onsubmit="return checkFansub()">
					<div class="mb-3">
						<label for="form-name-with-autocomplete" class="mandatory"><?php echo lang('admin.fansub_edit.name'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.name'), lang('admin.fansub_edit.name.help')); ?>
						<input class="form-control" name="name" id="form-name-with-autocomplete" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" id="form-id" name="id" value="<?php echo $row['id']; ?>">
						<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
						<input type="hidden" name="old_username" value="<?php echo $row['old_username']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-slug"><?php echo lang('admin.fansub_edit.slug'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.fansub_edit.slug'), lang('admin.fansub_edit.slug.help')); ?></label>
						<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-type"><?php echo lang('admin.fansub_edit.type'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.type'), lang('admin.fansub_edit.type.help')); ?>
						<select class="form-select" name="type" id="form-type" required>
							<option value=""><?php echo lang('admin.fansub_edit.type.select'); ?></option>
							<option value="fansub"<?php echo $row['type']=='fansub' ? " selected" : ""; ?>><?php echo lang('admin.generic.fansub_type.fansub'); ?></option>
							<option value="fandub"<?php echo $row['type']=='fandub' ? " selected" : ""; ?>><?php echo lang('admin.generic.fansub_type.fandub'); ?></option>
						</select>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label><?php echo lang('admin.fansub_edit.icon'); ?><?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?> <?php print_helper_box(lang('admin.fansub_edit.icon'), lang('admin.fansub_edit.icon.help')); ?> <small class="text-muted"><?php echo lang('admin.fansub_edit.icon.requirements'); ?></small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/icons/'.$row['id'].'.png');
?>
								<label for="form-icon" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? lang('admin.common.change_image') : lang('admin.common.upload_image') ; ?></label>
								<input class="form-control d-none" name="icon" type="file" accept="image/png" id="form-icon" onchange="checkImageUpload(this, -1, 'image/png', 192, 192, 192, 192, 'form-icon-preview', 'form-icon-preview-link');">
							</div>
						</div>
						<div class="col-sm-2" style="align-self: center;">
							<div class="mb-3">
								<a id="form-icon-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/icons/'.$row['id'].'.png" data-original="'.STATIC_URL.'/images/icons/'.$row['id'].'.png"' : ''; ?> target="_blank">
									<img id="form-icon-preview" style="width: 60px; height: 60px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/icons/'.$row['id'].'.png" data-original="'.STATIC_URL.'/images/icons/'.$row['id'].'.png"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="form-url"><?php echo lang('admin.fansub_edit.url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.url'), lang('admin.fansub_edit.url.help')); ?>
						<input class="form-control" type="url" name="url" id="form-url" maxlength="200" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-email"><?php echo lang('admin.fansub_edit.email'); ?><span class="mandatory"></label> <?php print_helper_box(lang('admin.fansub_edit.email'), lang('admin.fansub_edit.email.help')); ?>
						<input class="form-control" type="email" name="email" id="form-email" maxlength="200" value="<?php echo htmlspecialchars($row['email']); ?>" required>
					</div>
					<div class="mb-3">
						<label for="form-user_password"><?php echo !empty($row['id']) ? lang('admin.fansub_edit.user_password_edit') : lang('admin.fansub_edit.user_password').'<span class="mandatory">'; ?></label> <?php print_helper_box(lang('admin.fansub_edit.user_password'), lang('admin.fansub_edit.user_password.help')); ?>
						<input class="form-control" type="password" name="user_password" id="form-user_password" maxlength="200" minlength="6"autocomplete="new-password" value=""<?php echo !empty($row['id']) ? '' : ' required'; ?>>
					</div>
					<div class="mb-3">
						<label for="form-bluesky_handle"><?php echo lang('admin.fansub_edit.bluesky_handle'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.fansub_edit.bluesky_handle'), lang('admin.fansub_edit.bluesky_handle.help')); ?></label>
						<input class="form-control" name="bluesky_handle" id="form-bluesky_handle" required maxlength="200" value="<?php echo htmlspecialchars($row['bluesky_handle']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-mastodon_handle"><?php echo lang('admin.fansub_edit.mastodon_handle'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.fansub_edit.mastodon_handle'), lang('admin.fansub_edit.mastodon_handle.help')); ?></label>
						<input class="form-control" name="mastodon_handle" id="form-mastodon_handle" required maxlength="200" value="<?php echo htmlspecialchars($row['mastodon_handle']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-twitter_handle"><?php echo lang('admin.fansub_edit.twitter_handle'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.fansub_edit.twitter_handle'), lang('admin.fansub_edit.twitter_handle.help')); ?></label>
						<input class="form-control" name="twitter_handle" id="form-twitter_handle" required maxlength="200" value="<?php echo htmlspecialchars($row['twitter_handle']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-bluesky_url"><?php echo lang('admin.fansub_edit.bluesky_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.bluesky_url'), lang('admin.fansub_edit.bluesky_url.help')); ?>
						<input class="form-control" type="url" name="bluesky_url" id="form-bluesky_url" maxlength="200" value="<?php echo htmlspecialchars($row['bluesky_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-discord_url"><?php echo lang('admin.fansub_edit.discord_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.discord_url'), lang('admin.fansub_edit.discord_url.help')); ?>
						<input class="form-control" type="url" name="discord_url" id="form-discord_url" maxlength="200" value="<?php echo htmlspecialchars($row['discord_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-facebook_url"><?php echo lang('admin.fansub_edit.facebook_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.facebook_url'), lang('admin.fansub_edit.facebook_url.help')); ?>
						<input class="form-control" type="url" name="facebook_url" id="form-facebook_url" maxlength="200" value="<?php echo htmlspecialchars($row['facebook_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-instagram_url"><?php echo lang('admin.fansub_edit.instagram_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.instagram_url'), lang('admin.fansub_edit.instagram_url.help')); ?>
						<input class="form-control" type="url" name="instagram_url" id="form-instagram_url" maxlength="200" value="<?php echo htmlspecialchars($row['instagram_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-linktree_url"><?php echo lang('admin.fansub_edit.linktree_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.linktree_url'), lang('admin.fansub_edit.linktree_url.help')); ?>
						<input class="form-control" type="url" name="linktree_url" id="form-linktree_url" maxlength="200" value="<?php echo htmlspecialchars($row['linktree_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-mastodon_url"><?php echo lang('admin.fansub_edit.mastodon_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.mastodon_url'), lang('admin.fansub_edit.mastodon_url.help')); ?>
						<input class="form-control" type="url" name="mastodon_url" id="form-mastodon_url" maxlength="200" value="<?php echo htmlspecialchars($row['mastodon_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-telegram_url"><?php echo lang('admin.fansub_edit.telegram_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.telegram_url'), lang('admin.fansub_edit.telegram_url.help')); ?>
						<input class="form-control" type="url" name="telegram_url" id="form-telegram_url" maxlength="200" value="<?php echo htmlspecialchars($row['telegram_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-threads_url"><?php echo lang('admin.fansub_edit.threads_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.threads_url'), lang('admin.fansub_edit.threads_url.help')); ?>
						<input class="form-control" type="url" name="threads_url" id="form-threads_url" maxlength="200" value="<?php echo htmlspecialchars($row['threads_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-twitter_url"><?php echo lang('admin.fansub_edit.twitter_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.twitter_url'), lang('admin.fansub_edit.twitter_url.help')); ?>
						<input class="form-control" type="url" name="twitter_url" id="form-twitter_url" maxlength="200" value="<?php echo htmlspecialchars($row['twitter_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-youtube_url"><?php echo lang('admin.fansub_edit.youtube_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.youtube_url'), lang('admin.fansub_edit.youtube_url.help')); ?>
						<input class="form-control" type="url" name="youtube_url" id="form-youtube_url" maxlength="200" value="<?php echo htmlspecialchars($row['youtube_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-status"><?php echo lang('admin.fansub_edit.status'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.status'), lang('admin.fansub_edit.status.help')); ?>
						<div id="form-status">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="status" id="form-active" value="1"<?php echo $row['status']==1? " checked" : ""; ?>>
								<label class="form-check-label" for="form-active"><?php echo lang('admin.fansub_edit.status.active'); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="has_site_access" id="form-has_site_access" value="1"<?php echo $row['has_site_access']==1? " checked" : ""; ?>>
								<label class="form-check-label" for="form-has_site_access"><?php echo lang('admin.fansub_edit.status.has_site_access'); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="is_historical" id="form-is_historical" value="1"<?php echo $row['is_historical']==1? " checked" : ""; ?> onchange="$('#form-archive_url').prop('disabled',!$(this).prop('checked'));">
								<label class="form-check-label" for="form-is_historical"><?php echo lang('admin.fansub_edit.status.historical'); ?></label>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="form-hentai_category"><?php echo lang('admin.fansub_edit.hentai_category'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.hentai_category'), sprintf(lang('admin.fansub_edit.hentai_category.help'), MAIN_SITE_NAME, HENTAI_SITE_NAME)); ?>
						<select class="form-select" name="hentai_category" id="form-hentai_category" required>
							<option value="0"<?php echo $row['hentai_category']==0 ? " selected" : ""; ?>><?php echo lang('admin.fansub_edit.hentai_category.never'); ?></option>
							<option value="1"<?php echo $row['hentai_category']==1 ? " selected" : ""; ?>><?php echo lang('admin.fansub_edit.hentai_category.sometimes'); ?></option>
							<option value="2"<?php echo $row['hentai_category']==2 ? " selected" : ""; ?>><?php echo lang('admin.fansub_edit.hentai_category.always'); ?></option>
						</select>
					</div>
					<div class="mb-3">
						<label for="form-archive_url"><?php echo lang('admin.fansub_edit.archive_url'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.archive_url'), lang('admin.fansub_edit.archive_url.help')); ?>
						<input class="form-control" type="url" name="archive_url" id="form-archive_url" maxlength="200"<?php echo $row['is_historical']==0? " disabled" : ""; ?> value="<?php echo htmlspecialchars($row['archive_url']); ?>" required>
					</div>
					<div class="mb-3">
						<label for="form-ping_token"><?php echo lang('admin.fansub_edit.ping_token'); ?></label> <?php print_helper_box(lang('admin.fansub_edit.ping_token'), sprintf(lang('admin.fansub_edit.ping_token.help'), MAIN_SITE_NAME, API_URL)); ?>
						<input class="form-control" name="ping_token" id="form-ping_token" maxlength="200" value="<?php echo htmlspecialchars($row['ping_token']); ?>">
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? lang('admin.generic.save_changes') : lang('admin.fansub_edit.create_button'); ?></button>
					</div>
				</form>
			</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
