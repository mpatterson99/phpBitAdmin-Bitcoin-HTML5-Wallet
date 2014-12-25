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
		die (' The bitcoind server located at '. $scheme.'://'.$host.' on Port:['.$server_port.'] appears to be unresponsive.');
	}
	$check_login =  $wallet->rpc($scheme,$server_ip,$server_port,$rpc_user,$rpc_pass,'getinfo') ;
	if ( !is_array($check_login) ) {
		die (' There was an error with your Log In parameters. Is your RPC Username and Password correct?');
	}
}

$btcRate = $wallet->bitcoinConverter(0);

$url = urldecode($_SERVER['REQUEST_URI']);
$qs  = parse_url($url);
$query = $qs['query'];
$query = urldecode($query);

if( isset($query) ) {

	$add_len = strlen($_REQUEST['address']);
	$address = $_REQUEST['address'];
	$amount  = $_REQUEST['amount'];
	$message = urlencode($_REQUEST['message']);
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
	
	var address_len = '<?php print $add_len?>'; // bitcoin address length.
	var txFee = <?php print number_format($check_login['paytxfee'], 6, '.', ' '); ?>; // wallet current transaction fee.
	var btcRate = <?php print $btcRate; ?>; // current dollar rate.

	if (address_len !== '34') { // pay page accessed directly ( NOT via qr code ).		
		
		var btcValue = 1/btcRate; // 1 dollar to bitcoin conversion.
		$('#spanBTC').text(parseFloat(btcValue.toFixed(6))); // write bitcoin value to span.
		var sVal = $('#sliderPay').val(); // get current slider value.		
		
		txFee = txFee.toFixed(6); // transaction fee.
		$('#spanFee').text(txFee); // write fee to span.
		
		var tx    = parseFloat( $('#spanFee').text() ); // transaction fee.
		var total = btcValue + tx; // sum ( converted btc value + transaction fee ) .
		$('#spanTotal').text( total.toFixed(6) ); // write total to span.
		
		$('#sliderPay').bind('change', function () { // 

			if (sVal !== $(this).val()) {
				
				var sBTC = ( $(this).val() / btcRate ); 
				$('#spanBTC').text( parseFloat ( sBTC.toFixed(6) ) );
	
				total = sBTC + tx;
				$('#spanTotal').text( parseFloat ( total.toFixed(6) ) );
			}
		});

	} else { // pay page accessed via qr code.

		var address = '<?php print $address ; ?>';
		$('#inputAddress').val(address); // write address to input box.
		
		var amount  = '<?php print $amount ; ?>';
		$('#spanBTC').text(amount); // write bitcoin value to span.
		
		txFee = txFee.toFixed(6); // transaction fee.
		$('#spanFee').text(txFee); // write fee to span.

		var amt   = parseFloat ( $('#spanBTC').text() ); // btc amount
		var tx    = parseFloat ( $('#spanFee').text() ); // transaction fee.
		var total = amt + tx; // sum ( converted btc amount value + transaction fee ) .
		
		$('#spanTotal').text( total.toFixed(6) ); // write total to span.
		
		var message = '<?php print str_replace("+", " ", $message); ?>';		
		$('#inputMessage').val(decodeURIComponent(message)); // write message to input.

		var dollarVal = ( amt * btcRate ) ; // bitcoin to dollar conversion.
		sVal = dollarVal.toFixed(6);
		$('#sliderPay').val(sVal).slider("refresh");
	}
	
	$('#send_BTC').click(function () { // pay button clicked.		
		
		var send_to_address = $('#inputAddress').val();
		var send_amount 	= $('#spanBTC').text();	// transaction fee will be automatically added to this value.	
		var send_comment 	= $('#inputMessage').val();

		if (isEmpty(send_to_address)) {
			alert('Please enter a valid Bitcoin address.');
			return false;
		}

		if( send_to_address.length != 34 ) {
			alert(send_to_address + ' is not a valid Bitcoin address, it\'s length is ' + send_to_address.length + ' when it should be 34.') ;
			return false;
		}

		if( send_to_address.length == 34 ) {
			if( send_amount == 0) {
				alert( 'Please enter a Bitcoin amount to send.');
				return false;
			}
		}

		if( (send_to_address.length == 34) && ( send_amount > 0 ) ) {

			var send_params = '"' + send_to_address + '"';
			send_params += '~' + send_amount + '';
			if( !isEmpty( send_comment ) ) {
				send_params += '~"' + send_comment + '"';
			}
						
			$.ajax({
				url: 'php/phpSend.php',
				type: 'post',
				data: {
					'params': send_params
				},
				success: function (response) {
					$('#popupPay').popup("open");
					var res_amount = response.search("AMOUNT");
					
					if( res_amount == -1 ) { // if 'AMOUNT' is not returned then there's an error.
						$('#popupPayHeader').text('Payment NOT Sent!');
					} else {
						$('#popupPayHeader').text('Payment Sent!');
					}
					$('#payResponse').html(response);
				}
			});
			 
		};
	});
});

function isEmpty(obj){
	'use strict';
	var key = '';
	for (key in obj) {
		if (obj.hasOwnProperty(key)) {
			return false;
		}
	}
	return true;
}
</script>
<style>
.ui-header .ui-title {margin-right: 10%;margin-left: 10%;}
.ui-slider-input {width:55px !important;}
</style>
</head>
<body>
<div data-role="page" id="pay" data-theme="a">
	
	<div data-role="header">	
		<h1><img src="images/bitcoin2.png" height="16" width="16" alt=""/>&nbsp;phpBitAdmin</h1>
		<a href="scan.php" class="ui-btn ui-btn-right ui-corner-all ui-shadow ui-btn-inline ui-icon-camera ui-btn-icon-left ui-btn-a">Scan</a>				
		<div data-role="navbar" data-iconpos="top">
			<ul>
				<li><a href="home.php" data-ajax="false" data-icon="home">Home</a>
				<li><a href="pay.php" data-ajax="false" class="ui-btn-active ui-state-persist" data-icon="arrow-u">Pay</a>
				<li><a href="getpaid.php" data-ajax="false" data-icon="arrow-d">Get Paid</a> 
			</ul>
		</div>		
	</div><!-- /header -->
	
	<div data-role="content" class="content">
	
		<div style="padding-left:10px;padding-right:10px;padding-top:6px;">
			<label class="primary" for="inputAddress" id="label_Address">Pay To:</label>
			<input data-mini="true" type="text" id="inputAddress" spellcheck="false" placeholder="&nbsp;&lt;bitcoin address&gt;" tabindex=1 value="" />			
		</div>
		
		<div style="padding-left:10px;padding-right:10px;">
	        <label class="primary" for="sliderPay">Amount (USD):</label>
	        <input id="sliderPay" min="0.01" max="2.00" step="0.01" value="1.00" type="range" data-highlight="true" style="padding-right:5px;">
		</div>
		
		<div style="padding-left:10px;padding-right:10px;">
			<label class="primary" for="input_Message" id="label_Message">Message:</label>
			<input data-mini="true" type="text" id="inputMessage" spellcheck="true" tabindex=2  />
		</div>
		
		<div style="display:table-cell;padding-left:50px;padding-right:15px;text-align:center;width:100px;padding-top:6px;">
			<input id="send_BTC" data-icon="action" value="Pay" type="button">
		</div>
		
		<div style="display:table-cell;width:40px;vertical-align:top;text-align:right;">
			<span id="spanBTC" class="primary"></span><br>
			<span id="spanFee" class="primary"></span><br>
			<span id="spanTotal"></span><br>			
		</div>
		
		<div style="display:table-cell;vertical-align:top;">
			<span style="font-weight:bold;font-size:.8em;">&nbsp;-BTC</span><br>
			<span style="font-weight:bold;font-size:.8em;">&nbsp;-Fee</span><br>
			<span style="font-weight:bold;font-size:.8em;">&nbsp;&nbsp;TOTAL</span>
		</div>			
		
		<div data-role="popup" id="popupPay" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:400px;">
    		<div data-role="header" data-theme="a">
    		<h1 id="popupPayHeader">Payment Sent!</h1>
    		</div>
    		<div data-role="main" class="ui-content">
        		<div id="payResponse" style="margin-left: auto;margin-right:auto;font-size:.7em;font-weight:bold;"></div>
        		<hr style="width:370px;height:0; border:0; border-top: 1px solid #4c4c4c;"> 	
               <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back" data-transition="flow">Close</a>
    		</div>
		</div>		
		
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