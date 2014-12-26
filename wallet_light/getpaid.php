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
$btcRate = $wallet->bitcoinConverter(0);
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
<script src="js/jquery.qrcode.min.js"></script>
<script type="text/javascript">

$(document).on("pagecreate", function () {

	$.mobile.ajaxEnabled = false;
		
	var btc = <?php print $btcRate; ?>; // current dollar rate.
	 var btcValue = 1/btc; // convert into bitcoin value.
	 $('#spanBTC').text(parseFloat(btcValue.toFixed(6))); // write bitcoin value to span.
	 var sVal = $('#sliderPay').val(); // get current slider value.
	 $('#sliderPay').bind('change', function () { 
		 if (sVal !== $(this).val()) {
			 $('#spanBTC').text( parseFloat ( $(this).val()/btc).toFixed(6) );
		}
	});

	 $( "#popupQR" ).bind({
		   popupbeforeposition: function(event, ui) {
			   $('#qrcode').html("");
			   	var defaultAddress = '<?php print $btc_address ; ?>';
			   	var label = $('#inputLabel').val();
			   	var message = $('#inputMessage').val();
			   	var amt = $('#spanBTC').text() + ' BTC';
				var qrText = "bitcoin:"+defaultAddress+"?amount="+ escape( $('#spanBTC').text() ) +"&amp;label="+ escape(label)+"&amp;message="+escape(message);
				$('#qrcode').qrcode({width:200,height:200,text:qrText});
			   	$('#spanAmount').html(amt);
		 }
	});
                                                                     
});
</script>
<style>
.ui-slider-input {width:55px !important;}
</style>
</head>
<body>
<div data-role="page" id="pay" data-theme="a">
	<div data-role="header">	
		<h1><img src="images/bitcoin2.png" height="16" width="16" alt=""/>&nbsp;phpBitAdmin</h1>
		<a href="scan.php" class="ui-btn ui-btn-right ui-corner-all ui-shadow ui-btn-inline ui-icon-camera ui-btn-icon-left ui-btn-a" data-theme="a">Scan</a>			
		<div data-role="navbar" data-iconpos="top">
			<ul>
				<li><a href="home.php" data-ajax="false" data-icon="home">Home</a>
				<li><a href="pay.php" data-ajax="false" data-icon="arrow-u">Pay</a>
				<li><a href="getpaid.php" data-ajax="false" class="ui-btn-active ui-state-persist" data-icon="arrow-d">Get Paid</a> 
			</ul>
		</div>		
	</div><!-- /header -->
	
	<div data-role="content" class="content">
	
		<div style="padding-left:10px;padding-right:10px;padding-top:6px;">
			<label class="primary" for="inputLabel" id="labelLabel">Label:</label>
			<input data-mini="true" type="text" id="inputLabel" spellcheck="false" tabindex=1  />			
		</div>
		
		<div style="padding-left:10px;padding-right:10px;">
	        <label class="primary" for="sliderPay">Amount (USD):</label>
	        <input id="sliderPay" min="0.01" max="2.00" step="0.01" value="1.00" type="range" data-highlight="true" style="padding-right:5px;">
		</div>
		
		<div style="padding-left:10px;padding-right:10px;">
			<label class="primary" for="inputMessage" id="labelMessage">Message:</label>
			<input data-mini="true" type="text" id="inputMessage" spellcheck="true" tabindex=2  />
		</div>
		
		<div style="display:table-cell;padding-left:10px;padding-right:10px;text-align:center;width:100px;">		
			<a href="#popupQR" data-rel="popup" data-position-to="window" class="ui-btn  ui-corner-all ui-shadow ui-btn-inline ui-icon-check ui-btn-icon-left" data-transition="pop">Get Paid</a>
		</div>
		<div style="display:table-cell;width:35px;">
			<span id="spanBTC" class="secondary_light"></span>
			
		</div>
		<div style="display:table-cell;">
		<span style="font-weight:bold;">&nbsp;BTC</span>
		</div>
		
		

		<div data-role="popup" id="popupQR" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:400px;">
			<div data-role="header" data-theme="b"><h1>Payment Request</h1>
				<div data-role="main" class="ui-content" data-theme="a" style="margin-top:5px;;padding:0;background:#fff;">		
					<br><div id="qrcode" style="height:200px;width:200px;margin:0 auto;"></div><br />
					<hr style="height:0; border:0; border-top: 1px solid #333;"> 		
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b i-btn-icon-left ui-icon-check" data-theme="b" data-rel="back">Close</a>
        			<span id="spanAmount" style="font-weight:bold;color:#333;"></span>
   				 </div>
			</div>
		</div><!-- /popup -->
		
	</div><!-- /content -->
	
	<div data-role="footer" data-id="main" data-position="fixed" data-tap-toggle="false">
		<div data-role="navbar">
			<ul>
				<li><a href="transactions.php" data-ajax="false" data-icon="bullets">Transactions</a></li>
				<li><a href="settings.php" data-ajax="false" data-icon="gear">Settings</a></li>
				<li><a href="index.php" data-ajax="false" data-icon="delete">Log Out</a></li>
			</ul>
		</div>
	</div><!-- /footer  -->
	
</div><!-- /page pay -->

</body>
</html>
<?php ob_flush(); ?>
