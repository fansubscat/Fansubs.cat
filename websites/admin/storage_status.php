<?php
$header_title="Estadístiques - Servidors d’emmagatzematge";
$page="analytics";
include("header.inc.php");

function get_image_type_url($array, $type) {
	foreach ($array as $element) {
		if ($element->type==$type) {
			return $element->url;
		}
	}
	return NULL;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Servidors d’emmagatzematge</h4>
					<hr>
					<p class="text-center">Aquest és l’estat actual dels servidors d’emmagatzematge. Els diagrames només mostren el darrer dia.</p>
				</article>
			</div>
		</div>
<?php
	foreach (ADMIN_STORAGES as $storage) {
		//Get images from server
		$auth = base64_encode($storage['api_username'].':'.$storage['api_password']);
		$context = stream_context_create([
			"http" => [
				"header" => "Authorization: Basic $auth"
			]
		]);
		$json = file_get_contents($storage['api_url'], FALSE, $context);
		$response = json_decode($json);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body text-center">
					<h4 class="card-title mb-4 mt-1">Servidor "<?php echo $storage['hostname']; ?>"</h4>
					<hr>
					<h5 class="mb-3">Operacions d’entrada/sortida per segon (màx. 150)</h5>
					<img src="<?php echo get_image_type_url($response, 'iops_daily'); ?>" alt="" />
					<h5 class="mb-3 mt-3">Ús de la xarxa</h5>
					<img src="<?php echo get_image_type_url($response, 'traffic_daily'); ?>" alt="" />
					<h5 class="mb-3 mt-3">Ús de la CPU</h5>
					<img src="<?php echo get_image_type_url($response, 'cpu_daily'); ?>" alt="" />
					<h5 class="mb-3 mt-3">Ús de la memòria</h5>
					<img src="<?php echo get_image_type_url($response, 'memory_daily'); ?>" alt="" />
					<h5 class="mb-3 mt-3">Ús de l’emmagatzematge</h5>
					<img src="<?php echo get_image_type_url($response, 'storage_daily'); ?>" alt="" />
				</article>
			</div>
		</div>
<?php
	}
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
