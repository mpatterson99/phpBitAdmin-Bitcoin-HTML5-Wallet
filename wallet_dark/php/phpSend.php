<?php
require_once('../config.inc.php'); 
require('phpbitadmin.class.php');
$wallet = new PhpBitAdmin_Wallet();

$params 	= $_POST["params"];
$pieces 	= explode("~", $params);
$address	= $pieces[0];
$amount    	= $pieces[1];
$message   	= $pieces[2];
$status		= '';

// validate incoming address.
$valid_address = $wallet->rpc($scheme,$server_ip,$server_port,$rpc_user,$rpc_pass,'validateaddress',$address);
if(!$valid_address){
	die($address . ' = Invalid Bitcoin Address');
} else {
	$status = 'valid';
}

// check if wallet is encrypted or not.
$check_login =  $wallet->rpc($scheme,$server_ip,$server_port,$rpc_user,$rpc_pass,'getinfo') ;
if ( !is_array($check_login) ) {
	die (' There was an error with your Log In parameters. Is your RPC Username and Password correct?');
} else {
	if (array_key_exists('unlocked_until',$check_login)) {
		$encrypted = 'True';
	} else {
		$encrypted = 'False';
	}	
}

// we need to unlock an encrypted wallet to send bitcoins.
if($encrypted === 'True'){
	$pp_params = '"'.$passphrase.'",10'; 
	$wp = $wallet->rpc($scheme,$server_ip,$server_port,$rpc_user,$rpc_pass,'walletpassphrase',$pp_params); // unlock wallet
	if ($wp===true) {
		$status = 'valid';
	} else {
		die($passphrase . ' = Invalid Passphrase');
	}
}

// okay, send bitcoins.
if( $status == 'valid') {
	if( $message=='') {
		$send_params = $address . ', ' . $amount;
	} else {
		$send_params = $address . ', ' . $amount . ', ' . $message;
	}
	$query = $wallet->rpc($scheme,$server_ip,$server_port,$rpc_user,$rpc_pass,'sendtoaddress',$send_params);
	print 'SENT TO: '.$address.'<br>AMOUNT: '.$amount.'<br>MESSAGE: '.$message;
} 
ob_flush();
?>