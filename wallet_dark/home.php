<?php 
require_once('config.inc.php'); 
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
?>
<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>phpBitAdmin - Bitcoin Mobile Wallet</title>
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
<div data-role="page" id="home" data-theme="b">

	<div data-role="header" data-add-back-btn="true" data-icon="arrow-l">	
		<h1><img src="images/bitcoin2.png" height="16" width="16" alt=""/>&nbsp;phpBitAdmin</h1>
		<a href="scan.php" class="ui-btn ui-btn-right ui-corner-all ui-shadow ui-btn-inline ui-icon-camera ui-btn-icon-left ui-btn-b" data-theme="b">Scan</a>		
		<div data-role="navbar" data-iconpos="top">
			<ul>
				<li><a href="#" class="ui-btn-active ui-state-persist" data-ajax="false"  data-transition="fade" data-icon="home">Home</a>
				<li><a href="pay.php" data-icon="arrow-u" data-ajax="false"  data-transition="fade">Pay</a>
				<li><a href="getpaid.php" data-icon="arrow-d" data-ajax="false"  data-transition="fade">Get Paid</a> 
			</ul>
		</div>
	</div><!-- /header -->
	
	<div data-role="content" class="content">
		<div style="margin-top:15px;margin-left:5px;">
			<img id="image_WalletImage" src="images/BitCoin_L.png" height="50" width="50" alt="Bitcoind" />
			<span id="span_WalletHeaderText">Bitcoin <span style="font-style: italic;font-size:.7em;">m</span>Wallet</span>
			<hr class="hr_wallet">
		</div>
		
		<div class="div_WalletOverview">
			<span class="primary">Total Balance:</span>
			<span class="secondary"><?php print $check_login['balance']; ?>&nbsp;BTC</span>
		</div>
		
		<div class="div_WalletOverview">
			<span class="primary">Fee per transaction:</span>
			<span class="secondary"><?php print (string)$check_login['paytxfee']; ?></span>
		</div>
		
		<div class="div_WalletOverview">
			<span class="primary">Wallet Version:</span>
			<span class="secondary"><?php print (string)$check_login['walletversion']; ?></span>
		</div>
		
		<div class="div_WalletOverview">
			<span class="primary">Network:</span>
			<span class="secondary"><?php if ($check_login['testnet']) { print 'Testnet'; } else { print 'Main'; }?></span>
		</div>	
	
		<div class="div_WalletOverview">
			<span class="primary">Bitcoind Protocol:</span>
			<span class="secondary"><?php print $check_login['protocolversion']; ?></span>
		</div>
	
		<div class="div_WalletOverview">
			<span class="primary">Current Connections:</span>
			<span class="secondary"><?php print $check_login['connections']; ?></span>
		</div>
	</div><!-- /content  -->
		
	<div data-role="footer" data-id="main" data-position="fixed" data-tap-toggle="false">
		<div data-role="navbar">
			<ul>
				<li><a href="transactions.php" data-ajax="false" data-icon="bullets">Transactions</a></li>
				<li><a href="settings.php" data-ajax="false" data-icon="gear">Settings</a></li>
				<li><a href="index.php" data-ajax="false" data-icon="delete">Log Out</a></li>
			</ul>
		</div>
	</div><!-- /footer -->
		
</div><!-- /page home -->
</body>
</html>
<?php ob_flush(); ?>
