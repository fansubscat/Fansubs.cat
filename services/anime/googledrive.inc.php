<?php
require_once(__DIR__."/libs/google-api-php-client-2.4.1/vendor/autoload.php");

//Taken from the official Google Drive API sample for PHP... and heavily modified
function get_google_drive_client($account_id) {
	$client = new Google_Client();
	$client->setApplicationName('Google Drive API PHP Quickstart');
	$client->setScopes(Google_Service_Drive::DRIVE);
	$client->setAuthConfig('/srv/services/anime.fansubs.cat/googledrive/credentials_'.$account_id.'.json');
	$client->setAccessType('offline');
	$client->setPrompt('select_account consent');

	// Load previously authorized token from a file, if it exists.
	// The file token.json stores the user's access and refresh tokens, and is
	// created automatically when the authorization flow completes for the first
	// time.
	$tokenPath = '/srv/services/anime.fansubs.cat/googledrive/token_'.$account_id.'.json';
	if (file_exists($tokenPath)) {
		$accessToken = json_decode(file_get_contents($tokenPath), true);
		$client->setAccessToken($accessToken);
	}

	// If there is no previous token or it's expired.
	if ($client->isAccessTokenExpired()) {
		// Refresh the token if possible, else fetch a new one.
		if ($client->getRefreshToken()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			// Save the token to a file.
			if (!file_exists(dirname($tokenPath))) {
				mkdir(dirname($tokenPath), 0700, true);
			}
			file_put_contents($tokenPath, json_encode($client->getAccessToken()));
		} else {
			return NULL;
			//This code will not be run automatically... If we reach this, just error out and wait for someone to fix it manually
			/*
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			printf("Open the following link in your browser:\n%s\n", $authUrl);
			print 'Enter verification code: ';
			$authCode = trim(fgets(STDIN));

			// Exchange authorization code for an access token.
			$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
			$client->setAccessToken($accessToken);

			// Check to see if there was an error.
			if (array_key_exists('error', $accessToken)) {
				throw new Exception(join(', ', $accessToken));
			}
			// Save the token to a file.
			if (!file_exists(dirname($tokenPath))) {
				mkdir(dirname($tokenPath), 0700, true);
			}
			file_put_contents($tokenPath, json_encode($client->getAccessToken()));
			*/
		}
	}
	return $client;
}

function get_google_drive_files($account_id, $drive_id, $folder_id) {
	// Get the API client and construct the service object.
	$client = get_google_drive_client($account_id);

	if ($client===NULL){
		return array('status' => 'ko', 'code' => 1);
	}

	$service = new Google_Service_Drive($client);

	// Print the names and IDs for up to 10 files.
	$optParams = array(
		'q' => "mimeType != 'application/vnd.google-apps.folder' and '$folder_id' in parents",
		'corpora' => 'drive',
		'supportsAllDrives' => true,
		'includeItemsFromAllDrives' => true,
		'driveId' => $drive_id,
		'pageSize' => 1000,
		'orderBy' => 'name',
		'fields' => 'nextPageToken, files(id, name)'
	);

	try {
		$results = $service->files->listFiles($optParams);
	} catch (Exception $e) {
			return array('status' => 'ko', 'code' => 2);
		}

	$lines = array();
	foreach ($results->getFiles() as $file) {
		$permission = new Google_Service_Drive_Permission();
		$permission->setType('anyone');
		$permission->setRole('reader');
		$optParams = array(
			'supportsAllDrives' => true
		);
		try {
			$service->permissions->create($file->getId(), $permission, $optParams);
		} catch (Exception $e) {
			return array('status' => 'ko', 'code' => 3);
		}
		array_push($lines, $file->getName().':::https://drive.google.com/file/d/'.$file->getId().'/view?usp=sharing');
	}
	return array('status' => 'ok', 'files' => $lines);
}
?>
