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
<div data-role="page" id="settings" data-theme="a">
	<div data-role="header">	
		<h1><img src="images/bitcoin2.png" height="16" width="16" alt=""/>&nbsp;phpBitAdmin</h1>
		<a href="scan.php" class="ui-btn ui-btn-right ui-corner-all ui-shadow ui-btn-inline ui-icon-camera ui-btn-icon-left ui-btn-a" data-theme="a">Scan</a>	
		<div data-role="navbar" data-iconpos="top">
			<ul>
				<li><a href="home.php" data-icon="home">Home</a>
				<li><a href="pay.php"  data-icon="arrow-u">Pay</a>
				<li><a href="getpaid.php" data-icon="arrow-d">Get Paid</a> 
			</ul>
		</div> 
	</div>

	<div data-role="content">
		<div class="ui-field-contain">
			<label class="primary" for="inputIP">Bitcoind IP Address:</label>
			<input data-mini="true" id="inputIP" value="<?php print $scheme.'://'.$server_ip ; ?>" type="text">
		</div>
		
		<div class="ui-field-contain">
			<label class="primary" for="inputPort">Bitcoind Port:</label>
			<input data-mini="true" id="inputPort" value="<?php print $server_port ?>" type="text">
		</div>
		
		<div class="ui-field-contain">
			<label class="primary" for="inputRpcUser">RPC User:</label>
			<input data-mini="true" id="inputRpcUser" value="<?php print $rpc_user; ?>" type="text">
		</div>
		
		<div class="ui-field-contain">
			<label class="primary" for="passwordRPC">RPC Password:</label>
			<input data-mini="true" id="passwordRPC" autocomplete="off" type="password" value="<?php print $rpc_pass; ?>">
		</div>
	</div>
	
	<form>
    <input data-icon="action" value="Update" type="button">
    </form>

	<div data-role="footer" data-id="main" data-position="fixed" data-tap-toggle="false">
		<div data-role="navbar">
			<ul>
				<li><a href="transactions.php" data-icon="bullets">Transactions</a></li>
				<li><a href="settings.php" class="ui-btn-active ui-state-persist" data-icon="gear">Settings</a></li>
				<li><a href="index.php" data-icon="delete">Log Out</a></li>
			</ul>
		</div>
	</div>

</div><!-- /page -->
</body>
</html>
<?php ob_flush(); ?>
