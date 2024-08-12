<?php
require_once('db.inc.php');

log_action('cron-accounts-updater-started', "S’ha iniciat la comprovació de comptes");

$resulta = query("SELECT a.* FROM remote_account a ORDER BY name ASC");

$lock_pointer = fopen(MEGA_LOCK_FILE, "w+");

//We acquire a file lock to prevent two invocations at the same time.
//This could happen if a user is asking for manual file sync while this cron runs.
if (flock($lock_pointer, LOCK_EX)) {
	while ($account = mysqli_fetch_assoc($resulta)) {
		echo "Checking remote account ".$account['name']."\n";

		$output = array();
		$result = 0;
		exec("./mega_get_account_status.sh ".$account['token'], $output, $result);

		if ($result!=0){
			log_action("cron-error","S’ha produït l’error $result en processar el compte remot «".$account['name']."»");
		} else {
			$line = $output[0];
			$used_storage = explode(":::",$line, 2)[0];
			$total_storage = explode(":::",$line, 2)[1];
			query("UPDATE remote_account SET used_storage=".escape($used_storage).",total_storage=$total_storage WHERE id=".$account['id']);
		}
	}
	flock($lock_pointer, LOCK_UN);
} else {
	log_action("cron-error","No s’ha pogut blocar el fitxer de blocatge de MEGA");
}

log_action('cron-accounts-updater-finished', "S’ha completat la comprovació de comptes");
?>
