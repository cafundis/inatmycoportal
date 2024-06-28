<?php
// This script is dual licensed under the MIT License and the CC0 License.
include 'conf.php';

$useragent = 'iNatMyCoPortal/1.0';
$inatapi = 'https://api.inaturalist.org/v1/';
$errors = [];
$logging = false;

function logMessage( $message ) {
	if ( is_writable( 'log.txt' ) ) {
		file_put_contents( 'log.txt', $message . "\n", FILE_APPEND );
	}
}

function resetLog() {
	global $errors;
	$fp = fopen( 'log.txt', 'w' );
	if ( $fp ) {
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		fwrite( $fp, $date);
		fwrite( $fp, PHP_EOL);
		fclose( $fp );
	} else {
		$errors[] = 'Log file is not writable. Please check permissions.';
	}
}

function make_curl_request( $url = null, $token = null, $postData = null ) {
	global $useragent, $errors;
	$curl = curl_init();
	if ( $curl && $url ) {
		if ( $postData ) {
			$curlheaders = array(
				'Cache-Control: no-cache',
				'Content-Type: application/json',
				'Content-Length: ' . strlen( $postData ),
				'Accept: application/json'
			);
			if ( $token ) {
				$curlheaders[] = "Authorization: Bearer " . $token;
			}
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $curlheaders );
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $postData );
		}
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_USERAGENT, $useragent );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		$out = curl_exec( $curl );
		if ( $out ) {
			$object = json_decode( $out );
			if ( $object ) {
				return json_decode( json_encode( $object ), true );
			} else {
				$errors[] = 'API request failed. ' . curl_error( $curl );
				return null;
			}
		} else {
			$errors[] = 'API request failed. ' . curl_error( $curl );
			return null;
		}
	} else {
		$errors[] = 'Curl initialization failed. ' . curl_error( $curl );
		return null;
	}
}

function iNat_auth_request( $app_id, $app_secret, $username, $password, $url = 'https://www.inaturalist.org/oauth/token' ) {
	global $useragent, $errors;
	$curl = curl_init();
	$payload = array( 'client_id' => $app_id, 'client_secret' => $app_secret, 'grant_type' => "password", 'username' => $username, 'password' => $password );
	if ( $curl ) {
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_USERAGENT, $useragent );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $payload );
		$out = curl_exec( $curl );
		if ( $out ) {
			$object = json_decode( $out );
			if ( $object ) {
				return json_decode( json_encode( $object ), true );
			} else {
				$errors[] = 'API request failed. ' . curl_error( $curl );
				return null;
			}
		} else {
			$errors[] = 'API request failed. ' . curl_error( $curl );
			return null;
		}
	} else {
		$errors[] = 'Curl initialization failed. ' . curl_error( $curl );
		return null;
	}
}

// Post MyCoPortal link to iNaturalist
function post_mycoportal_link( $observationid, $link, $token ) {
	global $inatapi, $errors;
	$postData['observation_field_value'] = [];
	$postData['observation_field_value']['observation_id'] = intval( $observationid );
	$postData['observation_field_value']['value'] = $link;
	$postData['observation_field_value']['observation_field_id'] = 9543;
	$postData = json_encode( $postData );
	$url = $inatapi . 'observation_field_values';
	$response = make_curl_request( $url, $token, $postData );
	sleep( 1 );
	if ( $response ) {
		if ( isset( $response['error'] ) ) {
			$errors[] = 'MyCoPortal link could not be added for observation ' . $observationid . '. The owner may have this permission restricted.';
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

print("------------------ SCRIPT STARTED ------------------\n");
$start_time = microtime( true );
$myFile = "occurrences.csv";
if ( isset( $argv[1] ) ) {
	$recordlimit = $argv[1];
} else {
	$recordlimit = 1;
}

// Get authentication token
$response = iNat_auth_request( $app_id, $app_secret, $username, $password );
if ( $response && isset( $response['access_token'] ) ) {
	$token = $response['access_token'];	

	if (($fh = fopen($myFile, "r")) !== FALSE) {
		$data = fgetcsv($fh, 0, ","); // Get headers
		while (($data = fgetcsv($fh, 0, ",")) !== FALSE && $x < $recordlimit) {
			$updateResult = false;
			$errors = [];
			$institionCode = (isset($data[1]) && $data[1]) ? $data[1] : null;
			$catalogNumber = (isset($data[7]) && $data[7]) ? $data[7] : null;
			$references = (isset($data[91]) && $data[91]) ? $data[91] : null;
			// Get the iNaturalist observation ID
			$observationid = null;
			$url = $inatapi . 'observations?field%3AAccession+Number=' . $catalogNumber;
			$inatdata = make_curl_request( $url );
			sleep( 1 );
			if ( $inatdata
				&& isset( $inatdata['results'] )
				&& isset( $inatdata['results'][0] )
				&& isset( $inatdata['results'][0]['id'] )
			) {
				$observationid = $inatdata['results'][0]['id'];
			} else {
				$url = $inatapi . 'observations?field%3AFUNDIS+Tag+Number=' . $catalogNumber;
				$inatdata = make_curl_request( $url );
				sleep( 1 );
				if ( $inatdata
					&& isset( $inatdata['results'] )
					&& isset( $inatdata['results'][0] )
					&& isset( $inatdata['results'][0]['id'] )
				) {
					$observationid = $inatdata['results'][0]['id'];
				}
			}
			// If we successfully got the iNaturalist observation ID ...
			if ( $observationid ) {
				// ... and the MyCoPortal link is valid ...
				if ( filter_var( $references, FILTER_VALIDATE_URL ) ) {
					// ... post the MyCoPortal link to the iNaturalist observation
					$updateResult = post_mycoportal_link( $observationid, $references, $token );
				} else {
					$errors[] = 'MyCoPortal link is not a valid URL for ' . $catalogNumber . '.';
				}
			} else {
				$errors[] = 'No observation found for ' . $catalogNumber . '.';
			}
			if ( $updateResult ) {
				print( $catalogNumber . " successfully updated: " . $observationid . ".\n" );
			} else {
				print( $catalogNumber . " not updated.\n" );
			}
			if ( $errors ) {
				if ( count($errors) === 1 ) {
					print( "Errors: " . $errors[0] . "\n" );
				} else {
					print( "Errors:\n" );
					foreach ( $errors as $error ) {
						print( '   ' . $error . "\n" );
					}
				}
			}
			print( "\n" );
			$x++;
		}
		fclose($fh);
	}

} else {
	print( "Errors:\n" );
	foreach ( $errors as $error ) {
		print( '   ' . $error . "\n" );
	}
}
$end_time = microtime( true );
$execution_time = ( $end_time - $start_time );
print( "Execution time: " . $execution_time . " seconds.\n" );
print("------------------ SCRIPT TERMINATED ------------------\n");
