=== RePack - Reuse Packaging for WooCommerce ===
Contributors: werepack, philippmuenchen
Donate link: https://www.paypal.me/ouun
Tags: woocommerce, shipping, sustainability
Requires at least: 4.0.0
Tested up to: 5.4.1
Stable tag: 1.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ask for customer permission to ship reused shipping packaging. Show your support for a more sustainable ecommerce, reduce waste, save money and be part of the WeRePack.org initiative.

== Description ==

This plugin is part of the WeRePack.org initiative to avoid packaging waste and helps shop owners and their customers save resources.
To do this, the plugin adds a field in the WooCommerce payment process where customers can give their consent to receive reused shipping packaging. So simple, so effective. And with lots of customization options via filters, let us know if there's anything else you need.

**Current features**
* Checkbox in payment process with subtle animation
* Reward with a voucher: Shop owners can create a voucher to encourage customers and share the savings. To do this, simply create an ordinary WooCommerce voucher called 'WeRePack'.
* Registered users can set their default value in the WooCommerce Dashboard.
* The shortcode `[repack]` shows the total number of shipping packages saved.

**Coming soon**
* Gutenberg Block, which visually represents the savings to date.
* Be listed as a supporter on the initiative's website.

**Missing something?**
Write us what else is needed to make your shop more sustainable. Whatever it is, we will do our best to get as many shops as possible to join the initiative.

== Installation ==

Install the plugin either via WordPress or as a composer package via `composer require ouun/wc-repack`

== Frequently Asked Questions ==

= Am I forced to give a discount? =

No! You are not! But you can show your good will and that you are not taking part to maximize profit, but to support the initiative and environment.

= How to set up and customize a coupon code =

The plugin gives you maximum flexibility and accepts all WooCommerce coupons that can be applied to the shopping cart. Just create a new coupon named 'WeRePack', it will be added or removed automatically when you select the RePack checkbox. If you want to name coupon differently, add the filter `add_filter( 'repack_coupon_name', 'MyCouponName' )` to your `functions.php`.

= How to use the shortcode =

Use the shortcode wherever you want with `[repack]`. This will output the amount of packaging the shop saved in total. You can prepend and append text to that number with attributes: `[repack prepend="Wow! We already saved " append=" packaging so far"]`.
if you additionally set the attribute `user_id` the amount of packaging a single user has saved will be displayed. A neat way to further motivate recurring users: `[repack user_id="123" prepend="Thank you! Together we already saved " append=" packaging."]`

= I like the initiative and want to support you =

Yes, please! We need every heart, hand and mouth. Talk about us, help us improve the code [on GitHub](https://github.com/ouun/wc-repack "RePack on GitHub"), translate the plugin. We really appreciate every support.

== Screenshots ==

1. Checkbox animation, adapts your Theme Design
2. Shipping notice in WooCommerce Order Overview

== Changelog ==

= 1.0 =
* Hello World!
* Contains all features as explained in Readme

== About WeRePack.org Initiative ==

Learn more about the initiative on [WeRePack.org](https://werepack.org/ "The WeRePack Initiative") and join us.
