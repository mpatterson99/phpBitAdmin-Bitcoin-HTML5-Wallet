<?php
class PhpBitAdmin_Wallet {
	
	public function aasort (&$array, $key) {
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		natcasesort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		return $ret;
	}
	
	public function bitcoinConverter($currancy) {
		$url = "https://bitpay.com/api/rates";
		$json = file_get_contents($url);
		$data = json_decode($json, TRUE);
		$rate = $data[$currancy]["rate"];
		$usd_price = 1;
		$current_price = round( $usd_price / $rate , 6 );
		return $rate;
	}
	
	public function ping($scheme,$ip,$port){
	
		$url = $scheme.'://'.$ip;	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_PORT , $port);
		curl_setopt ($ch, CURLOPT_TIMEOUT , 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	public function rpc($scheme,$ip,$port,$rpcuser,$rpcpass,$command,$params=null){

		$url = $scheme.'://'.$ip.':'.$port.'/';
		$request = '{"jsonrpc": "1.0", "id":"phpBitAdmin", "method": "'.$command.'", "params": ['.$params.'] }';		
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
		curl_setopt($ch, CURLOPT_USERPWD, "$rpcuser:$rpcpass");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/plain'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		$response = curl_exec($ch);
		$response = json_decode($response,true);
		$result = $response['result'];
		$error  = $response['error'];
		curl_close($ch);

		switch($command){

			case "validateaddress":				
				return ($result['isvalid']);
				break;
			case "walletpassphrase":				
				if( empty($result) && empty($error) ) {
					return true;
				} else {
					return '<span class="ui-state-error" style="padding-left:2px;">'.nl2br(htmlentities($response['error']['message'])).'</span>';
				}
				break;				
			default:
				if (!is_null($error) ) {
					return $response['error']['message'];
				} else {
					return $result;
				}
		}
		
	} // /rpc
	
	public function setSession($scheme,$ip,$port,$user,$pass,$btc_addr,$p_phrase){
		$_SESSION['PHPBITADMIN']['BITCOIND_SCHEME'] 	= $scheme;
		$_SESSION['PHPBITADMIN']['BITCOIND_IP'] 		= $ip;
		$_SESSION['PHPBITADMIN']['BITCOIND_PORT'] 		= $port;
		$_SESSION['PHPBITADMIN']['BITCOIND_RPC_USER'] 	= $user;
		$_SESSION['PHPBITADMIN']['BITCOIND_RPC_PASS'] 	= $pass;
		$_SESSION['PHPBITADMIN']['BITCOIND_BTC_ADDRESS']= $btc_addr;
		$_SESSION['PHPBITADMIN']['BITCOIND_PASSPHRASE'] = $p_phrase;
		$_SESSION['PHPBITADMIN']['LOGIN'] = true;
		$_SESSION['PHPBITADMIN']['LOGIN_TIME'] = time();
		$_SESSION['PHPBITADMIN']['EXPIRE_TIME'] = $_SESSION['PHPBITADMIN']['LOGIN_TIME'] + (20 * 60); // set expiration for 20 minutes.
		return true;
	}	
}