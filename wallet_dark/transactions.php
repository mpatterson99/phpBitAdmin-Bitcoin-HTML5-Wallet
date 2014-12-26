<?php
require_once 'config.inc.php'; 
require('php/phpbitadmin.class.php');
$wallet = new PhpBitAdmin_Wallet();
if ( (empty($_SESSION['PHPBITADMIN'])) || ($_SESSION['PHPBITADMIN'] === null) ) { // check if $_SESSION is set.
	$session = $wallet->setSession($scheme, $server_ip, $server_port, $rpc_user, $rpc_pass, $btc_addr, $p_phrase);
} else {
	$session = true;
}
if( $session ) {
	$check_server = $wallet->ping($scheme, $server_ip, $server_port);
	if ( $check_server == '' || empty($check_server) ) {
		die (' The bitcoind server located at '. $scheme.'://'.$server_port.' on Port:['.$server_port.'] appears to be unresponsive.');
	}
	$check_login =  $wallet->rpc($scheme,$server_ip,$server_port,$rpc_user,$rpc_pass,'getinfo') ;
	if ( !is_array($check_login) ) {
		die (' There was an error with your Log In parameters. Is your RPC Username and Password correct?');
	}
}

$params = '"",100,0';
$query = $wallet->rpc($scheme,$server_ip,$server_port,$rpc_user,$rpc_pass,'listsinceblock');
$trans = $query["transactions"];
$trans_sorted = $wallet->aasort($trans,"time");
$transactions = array_reverse($trans_sorted); ?>
<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>phpBitAdmin - Bitcoin Wallet</title>
<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css" />
<link rel="stylesheet" href="css/m_phpbitadmin.css" />
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery.mobile-1.4.5.min.js"></script>
<script type="text/javascript">
$(document).bind("pagecreate", function () {
    $.mobile.ajaxEnabled = false;
});
</script>
</head>
<body>
<div data-role="page" id="transactions" data-theme="b">
	<div data-role="header">	
		<h1><img src="images/bitcoin2.png" height="16" width="16" alt=""/>&nbsp;phpBitAdmin</h1>
		<a href="scan.php" class="ui-btn ui-btn-right ui-corner-all ui-shadow ui-btn-inline ui-icon-camera ui-btn-icon-left ui-btn-b" data-theme="b">Scan</a>				
		<div data-role="navbar" data-iconpos="top">
			<ul>
				<li><a href="home.php" data-icon="home">Home</a>
				<li><a href="pay.php"  data-icon="arrow-u">Pay</a>
				<li><a href="getpaid.php" data-icon="arrow-d">Get Paid</a> 
			</ul>
		</div>
</div>

	<div data-role="content" class="content">
		<table data-role="table" id="movie-table-custom" data-mode="reflow" class="movie-list ui-responsive">
		  <thead>
		    <tr style="font-size:.8em;">
		      <th data-priority="1">Date</th>
		      <th data-priority="2">Confirmed</th>
		      <th data-priority="3">Type</th>
		      <th data-priority="4">Address</th>
		      <th data-priority="5">Amount</th>
		      
		    </tr>
		  </thead>
		  <tbody>
		  <?php 
		  	$i=0;
		  	foreach ($transactions as $value) {
				foreach ($value as $key => $val) {
					switch ($key) {
						case "account":
							$account = $val;
							break;
						case "address":
							$address = $val;
							break;
						case "amount":
							if ((int)$val < 0) {$negative = true;}
							$amount = number_format($val, 4, '.', ' ') ;
							break;
						case "category":
							$category = $val;
							break;
						case "comment":
							$comment = $val;
							break;
						case "confirmations":
							$confirmations = $val;
							break;
						case "time":
							$time = date('m-d-y H:i', $val);
							break;
						case "fee":
							$fee = $val;
					}
				}
			
			if ($category == 'send') {
				$amount = $amount + $fee;
				$category = 'Sent';
			}
			if ($category == 'receive') {$category = 'Received';}
			if ($amount < 0) {$negative = true;}else{$negative=false;}
			if ($confirmations >= 6) {
				$conf_img = 'images/check-green.png';
			} else {
				$conf_img = 'images/check-red.png';
			}
			print '<tr style="border-bottom:1px solid #808080;">';
			print '<th class="ui-priority-secondary">'.$time.'</th>';
			print '<td><img src="'.$conf_img.'" alt=""/>&nbsp;['.$confirmations.']</td>';			
			print '<td>'.$category.'</td>';
			print '<td style="font-size:.7em;">'.$address.'</td>';
			if($negative){
				print '<td><span style="color:#ff0000;">'.$amount.'</span></td>';
			} else {
				print '<td><span style="color:#00b200;">'.$amount.'</span></td>';
			}
			print '</tr>';
			
			$i++;
			$account = $address = $amount = $category = $comment = $confirmations = $fee = $negative = $time = '';
			}
		  ?>
		  </tbody>
		</table>
		    
			
	</div>

	<div data-role="footer" data-id="main" data-position="fixed" data-tap-toggle="false">
	<div data-role="navbar">
		<ul>
			<li><a href="transactions.php" data-icon="bullets" class="ui-btn-active ui-state-persist">Transactions</a></li>
			<li><a href="settings.php" data-icon="gear">Settings</a></li>
			<li><a href="index.php?" data-icon="delete">Log Out</a></li>
		</ul>
	</div>
	
</div><!-- /page transactions -->

</div>
</body>
</html>
<?php ob_flush(); ?>
