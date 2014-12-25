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
		die (' The bitcoind server located at '. $scheme.'://'.$host.' on Port:['.$server_port.'] appears to be unresponsive.');
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
<title>phpBitAdmin - Bitcoin Wallet</title>
<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css" />
<link rel="stylesheet" href="css/m_phpbitadmin.css" />
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery.mobile-1.4.5.min.js"></script>
<script type="text/javascript">
$(document).on("pagecreate", function () {
    $.mobile.ajaxEnabled = false;
});
</script>
<style>
#inputPin .ui-input-text { width: 60px !important }
</style>
</head>
<body>
<div data-role="page" id="login" data-theme="b">
	<div data-role="header">	
		<h2><img src="images/bitcoin2.png" height="16" width="16" alt=""/>&nbsp;phpBitAdmin</h2>
		<button class="ui-btn-right ui-btn ui-btn-b ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-camera ui-disabled">Scan
</button>		
		<div data-role="navbar" data-iconpos="top">
			<ul>
				<li><a href="home.php" data-icon="home" class="ui-disabled">Home</a>
				<li><a href="pay.php" data-icon="arrow-u" class="ui-disabled">Send</a>
				<li><a href="getpaid.php" data-icon="arrow-d" class="ui-disabled">Get Paid</a> 
			</ul>
		</div>		
	</div><!-- /header -->
	
	<div data-role="content" class="content">		
		
		<a href="#popupLogin" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-action ui-btn-icon-left ui-btn-b">Sign In</a>
		<div data-role="popup" id="popupLogin" data-overlay-theme="a" data-theme="a" data-dismissible="false" style="max-width:400px;">
		    <div data-role="header" data-theme="b" style="text-align:center;padding-right:10px;padding-left:10px;">
		    	<img id="image_WalletImage" src="images/BitCoin_L.png" height="50" width="50" alt="Bitcoind" />
				<span id="span_WalletHeaderText">Bitcoind Wallet</span>				
		    </div>
		    <div data-role="main" class="ui-content" style="text-align:center;">
		        <form>		    	
		    	<label for="inputPin" style="font-weight:bold;text-align:center;">PIN Number:</label>
	     		<input name="inputPin" id="inputPin" value="1234" type="password" pattern="[0-9]*" data-role="none" style="width:62px;background:#b2b2b2b;border:1px solid #666;text-align:center"> 	     				
		    	</form><p>
		    	<hr>		    	
		        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Cancel</a>
		        <a href="home.php" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-transition="flow">Submit</a>
		    </div>
		</div>
		            
	</div><!-- /content one -->
	
	<div data-role="footer" data-id="main" data-position="fixed" data-tap-toggle="false">
		<div data-role="navbar">
			<ul>
				<li><a href="transactions.php" data-icon="bullets" class="ui-disabled">Transactions</a></li>
				<li><a href="settings.php" data-icon="gear" class="ui-disabled">Settings</a></li>
				<li><a href="index.php" data-icon="delete" class="ui-disabled">Log Out</a></li>
			</ul>
		</div>
	</div><!-- /footer one -->
</div><!-- /page one -->
<script type="text/javascript">
    //$('#popupLogin').popup();
    //$('#popupLogin').popup('open');

</script>
</body>
</html>
