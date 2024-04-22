<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("../common.fansubs.cat/common.inc.php");
require_once("queries.inc.php");

validate_hentai();

if (empty($user)) {
	header("Location: /configuracio");
	die();
}

define('PAGE_TITLE', 'El meu perfil');
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once("../common.fansubs.cat/header.inc.php");

$result = query_user_seen_data_by_user_id($user['id']);
$seen_data = mysqli_fetch_assoc($result);

$total_manga_seen = $seen_data['total_manga_seen'];
$total_liveaction_seen = $seen_data['total_liveaction_seen'];
$total_comments_left = $seen_data['total_comments_left'];
$total_ratings_left = $seen_data['total_ratings_left'];

if ($seen_data['total_anime_seen']>=3600) {
	$total_anime_seen = intval($seen_data['total_anime_seen']/3600);
	if ($total_anime_seen==1) {
		$total_anime_seen.=' hora';
	} else {
		$total_anime_seen.=' hores';
	}
} else {
	$total_anime_seen = intval($seen_data['total_anime_seen']/60);
	if ($total_anime_seen==1) {
		$total_anime_seen.=' minut';
	} else {
		$total_anime_seen.=' minuts';
	}
}

if ($seen_data['total_liveaction_seen']>=3600) {
	$total_liveaction_seen = intval($seen_data['total_liveaction_seen']/3600);
	if ($total_liveaction_seen==1) {
		$total_liveaction_seen.=' hora';
	} else {
		$total_liveaction_seen.=' hores';
	}
} else {
	$total_liveaction_seen = intval($seen_data['total_liveaction_seen']/60);
	if ($total_liveaction_seen==1) {
		$total_liveaction_seen.=' minut';
	} else {
		$total_liveaction_seen.=' minuts';
	}
}

if ($total_manga_seen==1) {
	$total_manga_seen.=' pàgina';
} else {
	$total_manga_seen.=' pàgines';
}

mysqli_free_result($result);
?>
<div class="section">
	<h2 class="section-title-main"><i class="fa fa-fw fa-user"></i> El meu perfil</h2>
	<div class="profile-layout">
		<div class="profile-section">
			<div class="profile-avatar-name">
				<div class="profile-avatar">
					<img alt="Avatar de l’usuari" class="profile-avatar-image" src="<?php echo !empty($user['avatar_filename']) ? STATIC_URL.'/images/avatars/'.$user['avatar_filename'] : STATIC_URL.'/images/site/default_avatar.jpg'; ?>">
				</div>
				<div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
			</div>
			<div class="profile-links">
				<a class="profile-button" href="/edita-el-perfil"><i class="fa fa-fw fa-pen-to-square"></i> Edita el perfil</a>
				<a class="profile-button" href="/canvia-la-contrasenya"><i class="fa fa-fw fa-key"></i> Canvia la contrasenya</a>
			</div>
		</div>
		<div class="content-layout profile-page">
			<div class="profile-basic-info profile-details-section">
				<div class="profile-section-header">Informació bàsica</div>
				<div class="profile-section-content">
					<div class="profile-section-data">
						<div class="profile-section-data-header">Adreça electrònica</div>
						<div class="profile-section-data-info"><?php echo $user['email']; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Data de naixement</div>
						<div class="profile-section-data-info"><?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'd/m/Y'); ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Data de registre a <?php echo CURRENT_SITE_NAME; ?></div>
						<div class="profile-section-data-info"><?php echo date_format(date_create_from_format('Y-m-d H:i:s', $user['created']), 'd/m/Y'); ?></div>
					</div>
				</div>
			</div>
			<div class="profile-statistics profile-details-section">
				<div class="profile-section-header">Estadístiques</div>
				<div class="profile-section-content">
					<div class="profile-section-data">
						<div class="profile-section-data-header">Total d’anime vist</div>
						<div class="profile-section-data-info"><?php echo $total_anime_seen; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Total de manga llegit</div>
						<div class="profile-section-data-info"><?php echo $total_manga_seen; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Total d’imatge real vist</div>
						<div class="profile-section-data-info"><?php echo $total_liveaction_seen; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Comentaris escrits</div>
						<div class="profile-section-data-info"><?php echo $total_comments_left; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Valoracions atorgades</div>
						<div class="profile-section-data-info"><?php echo $total_ratings_left; ?></div>
					</div>
				</div>
			</div>
			<a class="profile-button remove-profile" href="/elimina-el-perfil">Elimina el perfil</a>
		</div>
	</div>
</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>
