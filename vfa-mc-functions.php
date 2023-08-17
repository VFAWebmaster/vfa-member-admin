<?php

/* -------------------------------------------------------------------------
Functions to support working with MailChimp lists
Drawn from https://github.com/stevenkellow/MailChimp-API-v3.0-PHP-cURL-example/blob/master/mc-API-connector.php
Drawn from http://blog.chapagain.com.np/mailchimp-api-v3-0-manage-subscriber-using-php-curl/
Created 180718 by DAV 
---------------------------------------------------------------------------*/

include_once("functions.php");

$action = $_POST["action"];
$email = $_POST["email"];
$fname = $_POST["fname"];
$lname = $_POST["lname"];
$interest = $_POST["interest"];
$debug = isset($_POST["debug"])?$_POST["debug"]:0;
// $apikey = "6cf21a268ce7550556dbbc559e8b9fbc-us4"; // Violette Family Association
// $listid = "dd83a71808"; // VFA Exec Comm (used for testing)
// // $listid = "5a9b003581"; // VFA eNewsletters list
$server = "us4.";

if ($debug) {
	// echo "*Robot voice* : Bleep bleep. Debugging is on master.<br /><br />";
}

if (!isset($email)) {
	echo "*Robot voice*: No email master, I don't know what to do now.<br /><br />";
}

switch($action) {
	case "subscribe":
    mc_subscribe($email, $fname, $lname, $debug, $apikey, $listid, $server);
    break;
	case "unsubscribe":
    mc_unsubscribe($email, $fname, $lname, $debug, $apikey, $listid, $server);
    break;
	case "addinterest":
    mc_addinterest($email, $interest, $debug, $apikey, $listid, $server);
    break;
	case "reminterest":
    mc_reminterest($email, $interest, $debug, $apikey, $listid, $server);
    break;
	case "changename":
    mc_changename($fname, $lname, $email, $debug, $apikey, $listid, $server);
    break;
	case "checklist":
    mc_checklist($email, $debug, $apikey, $listid, $server);
    break;
	default:
    echo "*Robot voice* : Your action is not valid master.<br /><br />";
    break;
}

function mc_subscribe($email, $fname, $lname, $debug, $apikey, $listid, $server) {
	$auth = base64_encode( 'user:'.$apikey );
	$EUID = md5( strtolower( $email ) );
	$data = array(
		'apikey'        => $apikey,
		'email_address' => $email,
		'status'        => 'subscribed',
		'merge_fields'  => array(
			'FNAME' => $fname,
			'LNAME' => $lname
			)
		);
	$json_data = json_encode($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://'.$server.'api.mailchimp.com/3.0/lists/'.$listid.'/members/'.$EUID);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.$auth));
	curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	$result = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	switch ($httpCode) {
		case 200:
			$msg = "Success: ";
			break;
		case 214:
			$msg = "Already subscribed: ";
			break;
		default:
			$msg = "Please try again. [msg_code=".$httpCode."]: ";
			break;
	}
	$result_obj = json_decode($result); // true=objects converted into associative arrays

	if ($debug) {
		print_r($result);
		print_r($result-obj);
	} else {
		echo $msg.$result_obj->email_address." ".$result_obj->status."<br/>";
	}
	curl_close($ch);
}

function mc_unsubscribe($email, $fname, $lname, $debug, $apikey, $listid, $server) {
	$auth = base64_encode( 'user:'.$apikey );
	$EUID = md5( strtolower( $email ) );

	$data = array(
		'apikey'        => $apikey,
		'email_address' => $email,
		'status'        => 'unsubscribed',
		'merge_fields'  => array(
			'FNAME' => $fname,
			'LNAME' => $lname
			)
		);
	$json_data = json_encode($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://'.$server.'api.mailchimp.com/3.0/lists/'.$listid.'/members/'.$EUID);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.$auth));
	curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	$result = curl_exec($ch);
	$result_obj = json_decode($result);

	if ($debug) {
		var_dump($result_obj);
	} else {
		echo $result_obj->email_address." ".$result_obj->status."<br/>";
		// echo $result_obj[1]['merge_fields'][1]['FNAME']."<br/>";
		// echo $result_obj->email_address." ".$result_obj->status."<br/>";
	}
	curl_close($ch);
}

function mc_changename($fname, $lname, $email, $debug, $apikey, $listid, $server) {
	$EUID = md5( strtolower( $email ) );
	$auth = base64_encode( 'user:'. $apikey );
	$data = array(
		'apikey'        => $apikey,
		'email_address' => $email,
		'merge_fields'  => array(
			'FNAME' => $fname,
			'LNAME' => $lname
			)
		);
	$json_data = json_encode($data);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://'.$server.'api.mailchimp.com/3.0/lists/'.$listid.'/members/'.$EUID);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '. $auth));
	curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	$result = curl_exec($ch);
	$result_obj = json_decode($result);

	if ($debug) {
		var_dump($result_obj);
	} else {
		echo $result_obj->email_address." ".$result_obj->status."<br/>";
	}
	curl_close($ch);
}

function mc_addinterest($email, $interest, $debug, $apikey, $listid, $server) {
	$EUID = md5( strtolower( $email ) );
	$auth = base64_encode( 'user:'. $apikey );
	$data = array(
		'apikey'        => $apikey,
		'email_address' => $email,
		'interests' => array(
			$interest => true
			)
		);
	$json_data = json_encode($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://'.$server.'api.mailchimp.com/3.0/lists/'.$listid.'/members/'.$EUID);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '. $auth));
	curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	$result = curl_exec($ch);
	$result_obj = json_decode($result);

	if ($debug) {
		var_dump($result_obj);
	} else {
		echo $result_obj->status;
	}
	curl_close($ch);
}

function mc_reminterest($email, $interest, $debug, $apikey, $listid, $server) {
	$EUID = md5( strtolower( $email ) );
	$auth = base64_encode( 'user:'. $apikey );

	$data = array(
		'apikey'        => $apikey,
		'email_address' => $email,
		'interests' => array(
			$interest => false
			)
		);
	$json_data = json_encode($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://'.$server.'api.mailchimp.com/3.0/lists/'.$listid.'/members/'.$EUID);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',	'Authorization: Basic '. $auth));
	curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	$result = curl_exec($ch);
	$result_obj = json_decode($result);

	if ($debug) {
		var_dump($result_obj);
	} else {
		echo $result_obj->status;
	}
	curl_close($ch);
}

function mc_checklist($email, $debug, $apikey, $listid, $server) {
	$EUID = md5( strtolower( $email ) );
	$auth = base64_encode( 'user:'. $apikey );

	$data = array(
		'apikey'        => $apikey,
		'email_address' => $email
		);
	$json_data = json_encode($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://'.$server.'api.mailchimp.com/3.0/lists/'.$listid.'/members/'.$EUID);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',	'Authorization: Basic '. $auth));
	curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	$result = curl_exec($ch);
	$result_obj = json_decode($result);

	if ($debug) {
		var_dump($result_obj);
	} else {
		echo $result_obj->status;
	}
	curl_close($ch);
}

?>