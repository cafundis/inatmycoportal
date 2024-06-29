<?php
// This script is dual licensed under the MIT License and the CC0 License.
include 'conf.php';

$useragent = 'iNatMyCoPortal/1.0';
$inatapi = 'https://api.inaturalist.org/v1/';
$errors = [];
$logging = false;

$fungariums = [
"PH"=>"Academy of Natural Sciences of Drexel University (PH)",
"ACAD"=>"Acadia University, E. C. Smith Herbarium (ACAD)",
"ETH"=>"Addis Ababa University (ETH)",
"CHSC"=>"Ahart Herbarium, CSU Chico - Mycological Collection (CHSC)",
"BMSC"=>"Bamfield Marine Science Centre (BMSC)",
"BISH"=>"Bishop Museum, Herbarium Pacificum (BISH)",
"BRIT"=>"Botanical Research Institute of Texas (BRIT)",
"BDWR"=>"Bridgewater College Herbarium (BDWR)",
"BRU"=>"Brown University Herbarium (BRU)",
"HSC-F"=>"Cal Poly Humboldt Fungarium (HSC-F)",
"CDA-Fungi"=>"California Department of Food and Agriculture - Fungi (CDA-Fungi)",
"HAY"=>"California State University East Bay Fungarium (HAY)",
"AAFC-DAOM"=>"Canadian National Mycological Herbarium (AAFC-DAOM)",
"WSP"=>"Charles Gardner Shaw Mycological Herbarium, Washington State University (WSP)",
"CHRB"=>"Chrysler Herbarium - Mycological Collection (CHRB)",
"CLEMS"=>"Clemson University Herbarium (CLEMS)",
"HCOA"=>"College of the Atlantic, Acadia National Park Herbarium (HCOA)",
"CUP"=>"Cornell University Plant Pathology Herbarium (CUP)",
"CBBG"=>"Crested Butte Botanic Gardens (CBBG)",
"DEWV"=>"Davis & Elkins College Herbarium (DEWV)",
"DBG-DBG"=>"Denver Botanic Gardens, Sam Mitchel Herbarium of Fungi (DBG-DBG)",
"DUKE"=>"Duke University Herbarium Fungal Collection (DUKE)",
"EIU"=>"Eastern Illinois University (EIU)",
"QCAM"=>"Ecuador Fungi data from FungiWebEcuador (QCAM)",
"TAM"=>"Estonian Museum of Natural History (TAM)",
"BAFC-H"=>"Facultad de Ciencias Exactas y Naturales (BAFC-H)",
"F"=>"Field Museum of Natural History (F)",
"FNL"=>"Foray Newfoundland and Labrador Fungarium (FNL)",
"FLD"=>"Fort Lewis College Herbarium (FLD)",
"GLM"=>"Fungal Collection at the Senckenberg Museum für Naturkunde Görlitz (GLM)",
"M"=>"Fungal Collections at the Botanische Staatssammlung München (M)",
"KR"=>"Fungus Collections at Staatliches Museum für Naturkunde Karlsruhe (KR)",
"FH"=>"Harvard University, Farlow Herbarium (FH)",
"FR"=>"Herbarium Senckenbergianum (FR)",
"IND"=>"Indiana University (IND)",
"INEP-F"=>"Institute of the Industrial Ecology Problems of the North of Kola Science Center of the Russian Academy of Sciences. (INEP-F)",
"PACA"=>"Instituto Anchietano de Pesquisas/UNISINOS (PACA)",
"USU-UTC"=>"Intermountain Herbarium (fungi, not lichens), Utah State University (USU-UTC)",
"ICMP"=>"International Collection of Microorganisms from Plants (ICMP)",
"ISC"=>"Iowa State University, Ada Hayden Herbarium (ISC)",
"SUCO"=>"Jewell and Arline Moss Settle Herbarium at SUNY Oneonta (SUCO)",
"KEAN"=>"Kean University (KEAN)",
"LSUM-Fungi"=>"Louisiana State University, Bernard Lowy Mycological Herbarium (LSUM-Fungi)",
"MUHW"=>"Marshall University Herbarium - Fungi (MUHW)",
"BR"=>"Meise Botanic Garden Herbarium (BR)",
"MU"=>"Miami University, Willard Sherman Turrell Herbarium (MU)",
"MSC"=>"Michigan State University Herbarium non-lichenized fungi (MSC)",
"MOR"=>"Morton Arboretum (MOR)",
"CORD"=>"Museo Botánico Córdoba Fungarium (CORD)",
"CR"=>"Museo Nacional de Costa Rica (CR)",
"PC"=>"Muséum National d'Histoire Naturelle (PC)",
"MNA"=>"Museum of Northern Arizona (MNA)",
"IBUNAM-MEXU:FU"=>"National Herbarium of Mexico Fungal Collection (Hongos del Herbario Nacional de México) (IBUNAM-MEXU:FU)",
"TNS-F"=>"National Museum of Nature and Science - Japan (TNS-F)",
"NMC-FUNGI"=>"National Mushroom Centre (NMC-FUNGI)",
"UT-M"=>"Natural History Museum of Utah Fungarium (UT-M)",
"L"=>"Naturalis Biodiversity Center (L)",
"NBM"=>"New Brunswick Museum (NBM)",
"NY"=>"New York Botanical Garden (NY)",
"NYS"=>"New York State Museum Mycology Collection (NYS)",
"PDD"=>"New Zealand Fungarium (PDD)",
"NCSLG"=>"North Carolina State University, Larry F. Grand Mycological Herbarium (NCSLG)",
"ODU-Fungi"=>"Old Dominion University Natural History Collection - Fungi (ODU-Fungi)",
"OSC-Lichens"=>"Oregon State University Herbarium - Lichens (OSC-Lichens)",
"OSC"=>"Oregon State University Herbarium (OSC)",
"PIORIN"=>"Państwowa Inspekcja Ochrony Roślin i Nasiennictwa - Fungi (PIORIN)",
"USFWS-PRR"=>"Patuxent Research Refuge - Maryland (USFWS-PRR)",
"PUR"=>"Purdue University, Arthur Fungarium (PUR)",
"PUL"=>"Purdue University, Kriebel Herbarium (PUL)",
"QFB"=>"René Pomerleau Herbarium (QFB)",
"E"=>"Royal Botanic Garden Edinburgh (E)",
"TRTC"=>"Royal Ontario Museum Fungarium (TRTC)",
"TAES"=>"S.M. Tracy Herbarium Texas A&M University (TAES)",
"SFSU"=>"San Francisco State University, Harry D. Thiers Herbarium (SFSU)",
"SBBG"=>"Santa Barbara Botanic Garden (SBBG)",
"LJF"=>"Slovenian Fungal Database (Mikoteka in herbarij Gozdarskega inštituta Slovenije) (LJF)",
"CORT"=>"State University of New York College at Cortland (CORT)",
"SYRF"=>"State University of New York, SUNY College of Environmental Science and Forestry Herbarium (SYRF)",
"SWAT"=>"Swat University Fungarium (SWAT)",
"S"=>"Swedish Museum of Natural History (S)",
"TALL"=>"Tallinn Botanic Garden (TALL)",
"IBUG"=>"Universidad de Guadalajara (IBUG)",
"CMMF"=>"Université de Montréal, Cercle des Mycologues de Montréal Fungarium (CMMF)",
"UACCC"=>"University of Alabama Chytrid Culture Collection (UACCC)",
"ARIZ"=>"University of Arizona, Gilbertson Mycological Herbarium (ARIZ)",
"UARK"=>"University of Arkansas Fungarium (UARK)",
"UBC"=>"University of British Columbia Herbarium (UBC)",
"UC"=>"University of California Berkeley, University Herbarium (UC)",
"UCSC"=>"University of California Santa Cruz Fungal Herbarium (UCSC)",
"IRVC"=>"University of California, Irvine Fungarium (IRVC)",
"LA"=>"University of California, Los Angeles (LA)",
"FTU"=>"University of Central Florida (FTU)",
"CSU"=>"University of Central Oklahoma Herbarium (CSU)",
"CINC"=>"University of Cincinnati, Margaret H. Fulford Herbarium - Fungi (CINC)",
"C"=>"University of Copenhagen (C)",
"FLAS"=>"University of Florida Herbarium (FLAS)",
"GAM"=>"University of Georgia, Julian H. Miller Mycological Herbarium (GAM)",
"GB"=>"University of Gothenburg (GB)",
"HAW-F"=>"University of Hawaii, Joseph F. Rock Herbarium (HAW-F)",
"ILL"=>"University of Illinois Herbarium (ILL)",
"ILLS"=>"University of Illinois, Illinois Natural History Survey Fungarium (ILLS)",
"KANU-KU-F"=>"University of Kansas, R. L. McGregor Herbarium (KANU-KU-F)",
"MAINE"=>"University of Maine, Richard Homola Mycological Herbarium (MAINE)",
"WIN"=>"University of Manitoba (WIN)",
"MICH"=>"University of Michigan Herbarium (MICH)",
"MIN"=>"University of Minnesota, Bell Museum of Natural History Herbarium Fungal Collection (MIN)",
"MISS"=>"University of Mississippi (MISS)",
"MONTU"=>"University of Montana Herbarium (MONTU)",
"NEB"=>"University of Nebraska State Museum, C.E. Bessey Herbarium - Fungi (NEB)",
"UNM-Fungi"=>"University of New Mexico Herbarium Mycological Collection (UNM-Fungi)",
"UNCA-UNCA"=>"University of North Carolina Asheville (UNCA-UNCA)",
"NCU-Fungi"=>"University of North Carolina at Chapel Hill Herbarium: Fungi (NCU-Fungi)",
"O"=>"University of Oslo, Natural History Museum Fungarium (O)",
"URV"=>"University of Richmond (URV)",
"USAM"=>"University of South Alabama Herbarium (USAM)",
"USCH-Fungi"=>"University of South Carolina, A. C. Moore Herbarium Fungal Collection (USCH-Fungi)",
"USF"=>"University of South Florida Herbarium - Fungi including lichens (USF)",
"TU"=>"University of Tartu Natural History Museum (TU)",
"TENN-F"=>"University of Tennessee Fungal Herbarium (TENN-F)",
"UCHT-F"=>"University of Tennessee, Chattanooga (UCHT-F)",
"TEX"=>"University of Texas Herbarium (TEX)",
"VT"=>"University of Vermont, Pringle Herbarium, Macrofungi (VT)",
"WTU"=>"University of Washington Herbarium (WTU)",
"UWAL"=>"University of West Alabama Fungarium (UWAL)",
"WIS"=>"University of Wisconsin-Madison Herbarium (WIS)",
"UWSP"=>"University of Wisconsin-Stevens Point Herbarium (UWSP)",
"RMS"=>"University of Wyoming, Wilhelm G. Solheim Mycological Herbarium (RMS)",
"UPS-BOT"=>"Uppsala University, Museum of Evolution (UPS-BOT)",
"USAC-USCG Hongos"=>"Usac, Cecon, Herbario USCG Hongos (USAC-USCG Hongos)",
"CFMR"=>"USDA Forest Service, Center for Forest Mycology Research (CFMR)",
"FPF"=>"USDA Forest Service, Rocky Mountain Research Station (FPF)",
"BPI"=>"USDA United States National Fungus Collections (BPI)",
"VSC"=>"Valdosta State University Herbarium (VSC)",
"VPI"=>"Virginia Tech University, Massey Herbarium - Fungi (VPI)",
"YSU-F"=>"Yugra State University Fungarium (YSU-F)"
];

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

// Post Fungarium Accession Location to iNaturalist
function post_fungarium( $observationid, $code, $token ) {
	global $inatapi, $errors, $fungariums;
	if ( isset( $fungariums[$code] ) ) {
		$postData['observation_field_value'] = [];
		$postData['observation_field_value']['observation_id'] = intval( $observationid );
		$postData['observation_field_value']['value'] = $fungariums[$code];
		$postData['observation_field_value']['observation_field_id'] = 18006;
		$postData = json_encode( $postData );
		$url = $inatapi . 'observation_field_values';
		$response = make_curl_request( $url, $token, $postData );
		sleep( 1 );
		if ( $response ) {
			if ( isset( $response['error'] ) ) {
				$errors[] = 'Fungarium location could not be added for observation ' . $observationid . '. The owner may have this permission restricted.';
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	} else {
		$errors[] = 'No fungarium found for code ' . $code . '.';
		return false;
	}
}

function get_mycoportal_link( $observationid ) {
	global $inatapi, $errors;
	$url = $inatapi . 'observations/' . $observationid;
	$inatdata = make_curl_request( $url );
	sleep( 1 );
	if ( $inatdata && $inatdata['results'] && $inatdata['results'][0] ) {
		if ( isset( $inatdata['results'][0]['ofvs'] ) ) {
			foreach ( $inatdata['results'][0]['ofvs'] as $observation_field ) {
				if ( $observation_field['name'] === 'MyCoPortal Link' ) {
					return $observation_field['value'];
				}
			}
		}
	}
	return null;
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
			$updateResult1 = false;
			$updateResult2 = false;
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
			}
			// If we successfully got the iNaturalist observation ID ...
			if ( $observationid ) {
				// ... and the MyCoPortal link is not already set ...
				if ( !get_mycoportal_link( $observationid ) ) {
					// ... and the new MyCoPortal link is valid ...
					if ( filter_var( $references, FILTER_VALIDATE_URL ) ) {
						// ... post the MyCoPortal link to the iNaturalist observation
						$updateResult1 = post_mycoportal_link( $observationid, $references, $token );
					} else {
						$errors[] = 'MyCoPortal link is not a valid URL for ' . $catalogNumber . '.';
					}
					// Post the fungarium location to the iNaturalist observation
					$updateResult2 = post_fungarium( $observationid, $institionCode, $token );
				} else {
					$errors[] = 'MyCoPortal link already set for ' . $catalogNumber . '.';
				}
			} else {
				$errors[] = 'No observation found for ' . $catalogNumber . '.';
			}
			if ( $updateResult1 && $updateResult2 ) {
				print( $catalogNumber . " successfully updated: " . $observationid . ".\n" );
			} else {
				print( $catalogNumber . " not successfully updated.\n" );
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
