Welcome to phpBitAdmin - Bitcoind HTML5 Wallet

This web application will run on any PHP enabled webserver. Only cURL
must be enabled in PHP. Otherwise, it's good to go.

Drop this web app anywhere in your webserver's directory. Open config.inc.php
and enter the required parameters needed to access any particular bitcoind
instance.

On the target Bitcoin Server add a 'rpcallowip" directive to bitcoin.conf
that points to the network ip address of the webserver running 
phpBitAdmin - Bitcoin Wallet.

#[ex.]
rpcallowip=10.0.0.15

Where 10.0.0.15 is the address of the webserver. You may enter as many 
rpcallowip directives as necessary. Other important bitcoin.conf 
directives are as follows. Uncomment as desired.

# server=1 tells Bitcoin to accept JSON-RPC commands.
server=1

# Listen for RPC connection on this TCP port:
# Standard port:
#rpcport=1832 
# Testnet port:
#rpcport=18332

# Run on the test network instead of the real bitcoin network.
#testnet=3

That's about all that is required in bitcoin.conf. You may want to check
your firewall and open port 8332 || 18332 depending on which network your
running the application against.

Page 'pay.php' runs a qr code scanner directly inside a web page. It makes use
of getUserMedia (WebRTC) so use of the latest browser is recommended. Safari
an IE do not yet support it but it is coming soon on those browsers. This page 
also requires a web cam or native camera to function.

NOTE: This web app contains downloaded versions of jQuery & jQuery Mobile. I made
a conscience decision not to link to various repositories containing the libraries
because issues are created when one accesses non-ssl from an ssl page. This app
is not yet production-ready, but when it is this will be one less issue to resolve.
