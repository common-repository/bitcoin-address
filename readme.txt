=== Plugin Name ===
Contributors: Abdussamad
Donate link: https://bitcoinspakistan.com/bitcoin-address/
Tags: bitcoin,bitcoin address,electrum
Requires at least: 3.9.2
Tested up to: 4.6.1
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin generates and displays a unique bitcoin address each time someone wants to send you bitcoin. 

== Description ==

**Note**: This plugin requires PHP 5.3 or newer.

Reusing bitcoin addresses is bad for your privacy. With this plugin you can enter a shortcode - [bitcoin_address] - in your blog posts or pages that displays a button which users can use to get a fresh bitcoin address. Address requests are logged and you can browse that log from the WordPress admin area.

The plugin works by generating deterministic addresses using an Electrum Master Public Key. You need to create a new Electrum wallet and then paste its Master Public Key into the plugin's settings page. It will then generate addresses from that wallet sequentially.

= Creating Electrum Wallet =

You will need an [Electrum](https://electrum.org) Master Public Key which is also known as an extended public key (xpub). The plugin only works with Electrum MPKs but does support both Electrum version 1.x and 2.x MPKs. It only supports standard wallets i.e. it does not support multisig wallets or 2FA wallets.

*  Run Electrum. It will open your default wallet.
*  To create a new wallet select File menu > New/Restore. 
*  Enter a name for your new wallet file.
*  Follow the on-screen prompts to create a new wallet.
*  To see the Master Public Key of your new wallet go to Wallet menu > Master Public Keys.
*  It is recommended that you increase the gap limit of your wallet from the default 20. The gap limit is the number of unused addresses Electrum keeps track of. To increase the gap limit open your wallet and go to the console tab. Type in `wallet.change_gap_limit(50)`. Close Electrum and reopen your wallet to see new addresses listed on the receive tab.
* **TIP**: To open your new wallet in future run Electrum and select File menu > Open. Then navigate to your wallet file and select it to open it.
* **TIP**: You can create a shortcut to your new wallet using the -w switch i.e. `electrum -w c:\path\to\wallet_file`. To learn the location of your wallet file see [this FAQ](http://docs.electrum.org/en/latest/faq.html#where-is-my-wallet-file-located).

= Configuring plugin =

To access the plugin configuration page you need to login to the WordPress admin page. Then click on Settings > Bitcoin Address from the left sidebar. There you can paste the Electrum Master Public Key.

= Shortcode =

The plugin shortcode is [bitcoin_address]. The following options exist:

*   Display type: You can specify a type of "field" or "link". Field type will display a text field containing the address. Link will display a bitcoin address link. For example: [bitcoin address type='field'] will display a text field. The default type is field.
*   QR Code: You can enable or disable the display of a QR Code containing the address. Ex: [bitcoin_address type='link' qr_code='enabled'] will display an address as a link and above that link will be the address QR code. By default QR code display is disabled.

= Address Log =

An address log is maintained that lists the time and date an address was handed out, the ip address of the user who requested it, and the address itself. You can see the address log in WordPress admin > Settings > Bitcoin Address Log

== Installation ==

1. Unzip the package, and upload `bitcoin-address` to the `/wp-content/plugins/`
directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit Settings > Bitcoin Address and enter your Electrum Master Public Key there.

== Screenshots ==
1. Address Display
2. Plugin settings

== Credits ==

The plugin makes use of the following opensource scripts:

* Code borrowed from the [bitcoin payments for woocommerce plugin](https://wordpress.org/plugins/bitcoin-payments-for-woocommerce/) by [BitcoinWay](http://www.bitcoinway.com) including parts of [PHP-ECC](https://github.com/phpecc/phpecc) by [Matyas Danter](http://www.matyasdanter.com/).

* [PHP QR Code encoder](http://phpqrcode.sourceforge.net/)

== Changelog ==

= 0.8.0 =
* Now supports Electrum version 2.x MPKs

= 0.7.8 =
* readme.txt updated with instructions on how to raise the gap limit.

= 0.7.7 =
* First publicly released version

== Upgrade Notice ==

= 0.8.0 =
Added support for Electrum version 2.x MPKs
