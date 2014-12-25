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
<script src="js/html5-qrcode.min.js"></script>
<script type="text/javascript">

$(document).on("pagecreate", function () {

    $.mobile.ajaxEnabled = false;

    // check that browser is getUserMedia enabled.
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    
    if (navigator.getUserMedia){        

	    $('#reader').html5_qrcode(function(data){ //start scanning for qr code.
	
	    	$('#read').html(data); // populate #read span with qr code text.	    	
	    	var qr_result = $('#read').text(); // less errors reading from span than data.
	    	var qr = decodeURI(qr_result); // url decode.
	    	qr = qr_result.replace('bitcoin:','address='); // for our purposes, change bitcoin: to address=.
	    	qr = qr.replace('?','&'); // turn entire qr code text into querystring.
	    	data=''; // clear data stream.
	    	//var beep = window.Audio("audio/beep.mp3");
	    	//function playBeep() { beep.play() }; // beep to notify user of successful scan [future version].
			window.location.href ='pay.php?'+qr; // redirect to pay.php.		
			},
			
			function(error){
				$('#read').html('');
				$('#read_error').html('Please scan QR Code.');
				
			}, function(videoError){
				$('#vid_error').html(videoError);
			}
		);
		
    } else {
        
    	$('#read').html("Sorry, your browser does not support getUserMedia (WebRTC)");
    }
});
</script>
</head>
<body>
<div data-role="page" id="scan" data-theme="b">

	<div data-role="header">	
		<h2><img src="images/bitcoin2.png" height="16" width="16" alt=""/>&nbsp;phpBitAdmin</h2>
		<a href="scan.php" class="ui-btn ui-btn-active ui-btn-right ui-corner-all ui-shadow ui-btn-inline ui-btn-b i-btn-icon-left ui-icon-camera" data-theme="b">Scan</a>		
		<div data-role="navbar" data-iconpos="top">
			<ul>
				<li><a href="home.php" data-ajax="false" data-icon="home">Home</a>
				<li><a href="pay.php" data-icon="arrow-u" data-ajax="false">Pay</a>
				<li><a href="getpaid.php" data-icon="arrow-d" data-ajax="false">Get Paid</a> 
			</ul>
		</div>
	</div><!-- /header -->
	
	<div data-role="content" class="content" style="text-align:center;">
		<div id="scanWrapperDiv" style="margin-left:auto;margin-right:auto;">
			<div id="reader" style="width:300px;height:250px;padding:0px;margin-left:auto;margin-right:auto;"></div><br>
			<span id="read" style="margin-left:auto;margin-right:auto;font-size:.8em;">WebRTC requires you to "Allow" camera access.</span>
			<span id="read_error" style="text-align:center;font-size:.8em;"></span>
			<span id="vid_error" style="text-align:center;font-size:.8em;"></span>
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
	</div><!-- /footer -->
	
</div><!-- /page scan -->

</body>
</html>
